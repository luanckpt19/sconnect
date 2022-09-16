<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\NetworkUtils;
use Carbon\Carbon;
use App\Models\Channel;
use App\Utils;
use App\Models\ChannelReport;

class GetChannelInfo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $channel_id;
    protected $url;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($channel_id, $url) {
        $this->channel_id = $channel_id;
        $this->url = $url;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        
        Log::info('GetChannelInfo: '.$this->url);
        
        if(Utils::is_youtube_link($this->url)) {
            $this->get_youtube_channel_info();
        }
               
        
    }
    
    private function get_youtube_channel_info() {
        try {
            /*
             * get youtube content of channel
             * */
            //dd('/youtube/v3-get-channel-info?channel_url=' . $url);
            // https://www.youtube.com/channel/UConnM5zwOP9vG_LPTYbRsAg
            $api_url = config('app.api_base_url').'/youtube/v3-get-channel-info?channel_url=' . $this->url;
            Log::info('GetChannelInfo: api_url='.$api_url);
            
            $json_text = json_decode(NetworkUtils::fetch_content_from_url($api_url));

            $json_body = $json_text->body->items[0];
            
            $title = $json_body->snippet->title;
            $description = $json_body->snippet->description;
            $publishedAt = $json_body->snippet->publishedAt;
            
            if (!empty($publishedAt)) {
                $publishedAt = str_replace('Z', '', str_replace('T', ' ', $publishedAt));
            } else {
                $publishedAt = Carbon::now();
            }
            
            $thumbnail = $json_body->snippet->thumbnails->medium->url;
            $viewCount = $json_body->statistics->viewCount;
            $videoCount = $json_body->statistics->videoCount;
            $subscriber = -1;
            if (!empty($json_body->statistics->subscriberCount)) {
                $subscriber = $json_body->statistics->subscriberCount;
            }
            
            $channel = Channel::find($this->channel_id);
            if (!empty($channel)) {
                $channel->name = $title;
                $channel->description = $description;
                $channel->joined_date = $publishedAt;
                $channel->thumbnail = $thumbnail;
                $channel->views = $viewCount;
                $channel->video_count = $videoCount;
                if ($subscriber >= 0) $channel->subcriber = $subscriber;
                $channel->save();
                
                /*
                 * Update or add new info of this channel into channel report table by day.
                 * */
                $today = Carbon::now()->format('Y-m-d');;    // yyyy-mm-dd
                $report = ChannelReport::where(['channel_id'=>$this->channel_id, 'date'=>$today])->first();
                if (empty($report)) {
                    $report = new ChannelReport();
                    $report->channel_id = $this->channel_id;
                    $report->date = $today;
                }
                $report->video_count = $videoCount;
                $report->view_count = $viewCount;
                $report->subcriber_count = $channel->subcriber;
                $report->updated_at = now();
                $report->save();
                
                /*
                 * Put job get video list into queue.
                 * */
                Log::info("dispatch channelId=$channel->id, url=$channel->url");
                dispatch(new GetVideosOfChannel($channel->id, $channel->url));
            }
            Log::info('GetChannelInfo done!');
        } catch (\Exception $e) {
            Log::error('GetChannelInfo: ' . $e->getMessage());
        } 
    }
    
}
