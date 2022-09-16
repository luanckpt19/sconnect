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
use App\Models\Video;
use App\Utils;
use App\Models\VideoReport;

class GetVideosOfChannel implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $channel_id;
    protected $url;
    
    private $base_video_url = 'https://www.youtube.com/watch?v=';

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
        Log::info('GetVideosOfChannel: '.$this->url);
        if(Utils::is_youtube_link($this->url)) {            
            $this->fetch_video_youtube_from_api();
        }
        Log::info('GetVideosOfChannel done!');
        
    }
    
    /*
     * Hàm lấy thông tin cơ bản của video từ url kênh youtube
     * */
    private function fetch_video_youtube_from_api($nextPageToken = null) {
        try {
            /*
             * get youtube content of channel
             * */
            //dd('/youtube/v3-get-channel-info?channel_url=' . $url);
            // https://www.youtube.com/channel/UConnM5zwOP9vG_LPTYbRsAg
            // https://www.youtube.com/watch?v=aWN18fov3tw
            $api_url = config('app.api_base_url').'/youtube/v3-get-video?channel_url=' . $this->url;
            if (!empty($nextPageToken)) {
                $api_url .= '&nextPageToken=' . $nextPageToken;
            }
            
            Log::info('GetVideosOfChannel: api_url='.$api_url);
            
            $json_text = json_decode(NetworkUtils::fetch_content_from_url($api_url));
            
            $json_video_list = $json_text->body->items;
            $count = count($json_video_list);
            
            $arr_video_ids = array();
            
            if (!empty($json_video_list) && $count > 0) {                
                for($idx = $count-1; $idx >= 0; $idx--) {
                    $video_id = $this->saveVideo( $json_video_list[$idx] );
                    if (!empty($video_id)) $arr_video_ids[] = $video_id;
                }
            }
            
            $str_video_ids = implode(',', $arr_video_ids);
            $this->fetch_yt_video_info_from_api($str_video_ids);
            
            /*
             * Kiểm tra xem có trang tiếp theo hay không? Nếu có thì thực hiện đệ quy để lấy video của trang tiếp theo.
             * */
            $nextPageToken = $json_text->body->nextPageToken;
            if (!empty($nextPageToken)) {
                $this->fetch_video_youtube_from_api($nextPageToken);
            }
            
        } catch (\Exception $e) {
            Log::error('GetVideosOfChannel: ' . $e->getMessage());
        } 
    }
    
    /*
     * Hàm lấy thông tin số liệu thống kê view, like, ... của video youtube
     * */
    private function fetch_yt_video_info_from_api($str_video_ids) {
        try {
            /*
             * get youtube content of channel
             * */
            //dd('/youtube/v3-get-channel-info?channel_url=' . $url);
            // https://www.youtube.com/channel/UConnM5zwOP9vG_LPTYbRsAg
            // https://www.youtube.com/watch?v=aWN18fov3tw
            $api_url = config('app.api_base_url').'/youtube/v3-get-video-info?video_ids=' . $str_video_ids;
            
            Log::info('fetch_yt_video_info_from_api: api_url='.$api_url);
            
            $json_text = json_decode(NetworkUtils::fetch_content_from_url($api_url));
            
            $json_video_list = $json_text->body->items;
            $count = count($json_video_list);
            
            if (!empty($json_video_list) && $count > 0) {
                $today = Carbon::now()->format('Y-m-d');;    // yyyy-mm-dd
                Log::info('Fetching ' . $count . ' videos.');
                $count_fetched = 0;
                foreach ($json_video_list as $json_video) {                    
                    $video_url      = $this->base_video_url . $json_video->id;
                    $viewCount      = $json_video->statistics->viewCount;
                    $likeCount      = $json_video->statistics->likeCount;
                    
                    // update videos table
                    $video = Video::where('url', $video_url)->first();
                    $video->view_count = $viewCount;
                    $video->like_count = $likeCount;
                    $video->save();
                    
                    // update video_reports table
                    $where = ['video_id'=>$video->id, 'date'=>$today];
                    VideoReport::updateOrCreate($where, ['view_count'=>$viewCount, 'like_count'=>$likeCount]);
                    $count_fetched++;
                }
                Log::info('Fetched ' . $count_fetched . ' videos.');
            }
            
            
        } catch (\Exception $e) {
            Log::error('fetch_yt_video_info_from_api: ' . $e->getMessage());
        } 
    }
    
    private function saveVideo($json_video) {
        try {
            $video_id       = $json_video->id->videoId;
            $video_url      = $this->base_video_url . $video_id;
            $title          = $json_video->snippet->title;
            $description    = $json_video->snippet->description;
            $publishedAt    = $json_video->snippet->publishedAt;
            
            if (!empty($publishedAt)) {
                $publishedAt = str_replace('Z', '', str_replace('T', ' ', $publishedAt));
            } else {
                $publishedAt = Carbon::now();
            }
            $thumbnail = $json_video->snippet->thumbnails->medium->url;
            
            $video = Video::where('url', $video_url)->first();
            if (empty($video)) {
                $video = new Video();                
                $video->url = $video_url;
                $video->channel_id = $this->channel_id;
                $video->joined_date = $publishedAt;
            }
            $video->name = $title;
            $video->description = $description;
            $video->thumbnail = $thumbnail;
            $video->save();
            
            return $video_id;
                        
        } catch (\Exception $e) {
            Log::error('saveVideo: ' . $e->getMessage());
        } 
        return '';
    }

}
