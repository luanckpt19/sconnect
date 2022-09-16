<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Jobs\GetChannelInfo;
use App\Models\Channel;

class ChannelCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'channel:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        \Log::info("Channel Cron is working fine!");
        $this->collectChannel();
    }

    private function collectChannel() {
        \Log::info("Schedule collectChannel called!");
        try {
            $channels = Channel::all();
            if (!empty($channels) && count($channels) > 0) {
                \Log::info("Channel: " . count($channels) . " records!");
                $channels->each(function($channel) {
                    $this->collectChannelInfo($channel);
                });
            } else {
                \Log::info("No Channel Record!");
            }
        } catch (\Exception $e) {
            \Log::info("collectChannel error: " . $e->getMessage());
        }
        
    }    

    private function collectChannelInfo($channel) {
        try {
            if (!empty($channel)) {
                //$this->dispatch(new GetChannelInfo($channel->id, $channel->url));
                GetChannelInfo::dispatch($channel->id, $channel->url);
            }
        } catch (\Exception $e) {
            \Log::info("collectChannelInfo error: " . $e->getMessage());
        }
    }
    
}
