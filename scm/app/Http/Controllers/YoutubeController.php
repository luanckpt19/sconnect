<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use Illuminate\Http\Request;
use Exception;
use App\Utils;
use App\NetworkUtils;

class YoutubeController extends BaseController {
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    private const YT_V3_KEY = 'AIzaSyChPS6h3XzZvPzGmcUf9hGhm02xTzEWPdY';
    private const YOUTUBE_V3_URL = "https://www.googleapis.com/youtube/v3/";
    private const YOUTUBE_CHANNEL_NAME_URL = "https://www.youtube.com/c/";
    private const YOUTUBE_USER_NAME_URL = "https://www.youtube.com/user/";
    //private const FIELD_CHANNEL_SUBSCRIBER_COUNT = "channels?part=statistics&key=" . YT_V3_KEY;

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index() {
        return '';
    }
    
    public function apiV3GetChannelInfo(Request $request) {
        /*
         * Ex:
         * Channel name: KplusSportsOfficial
         * User name: klauskkpm
         * https://www.googleapis.com/youtube/v3/search?key={your_key_here}&channelId={channel_id_here}&part=snippet,id&order=date&maxResults=20
         * */
        $data = array();
        $data['platform'] = 'Youtube';
        $data['body'] = '[]';
        
        try {
            $channel_id = $this->get_channel_id($request->input('channel_url'));
            if (!empty($channel_id)) {
                //https://youtube.googleapis.com/youtube/v3/channels?key=AIzaSyChPS6h3XzZvPzGmcUf9hGhm02xTzEWPdY&part=snippet,contentDetails,statistics&id=UCmgGhZ_OMFGRn5cbfd4Svrw
                $api_url = "https://youtube.googleapis.com/youtube/v3/channels?key=" . $this::YT_V3_KEY . "&id=$channel_id&part=snippet,statistics";
                $data['api_url'] = $api_url;
                $json_text = json_decode(NetworkUtils::fetch_content_from_url($api_url));
                $data['body'] = $json_text;
            }
        } catch (Exception $e) {
        }
        return response()->json($data);
    }
    
    public function apiV3GetVideo(Request $request) {
        /*
         * Ex:
         * Channel name: KplusSportsOfficial
         * User name: klauskkpm
         * https://www.googleapis.com/youtube/v3/search?key=AIzaSyChPS6h3XzZvPzGmcUf9hGhm02xTzEWPdY&channelId=UCue2DK0ccHtBrP9V__gYl6A&part=snippet,id&order=date&maxResults=50
         * */
        $data = array();
        $data['platform'] = 'Youtube';
        $data['body'] = '[]';
        
        try {
            
            $channel_url = $request->input('channel_url');
            $published_after = $request->input('publishedAfter'); // 2022-04-20T14:25:32Z
            $next_page_token = $request->input('nextPageToken');
            
            $channel_id = $this->get_channel_id($channel_url);
            if (!empty($channel_id)) {
                $api_url = "https://youtube.googleapis.com/youtube/v3/search?key=" . $this::YT_V3_KEY
                . "&channelId=$channel_id&part=snippet,id&type=video&order=date&maxResults=50";
                // Nếu có tham số chỉ lấy video published sau thời gian publishedAfter
                if (!empty($published_after)) {
                    $api_url .= '&publishedAfter=' . $published_after;
                }
                /*
                 * Nếu có tham số lấy video của trang có token nextPageToken.
                 * Tham số này phục vụ khi lấy video của kênh có nhiều hơn 50 video sẽ được phân trang.
                 * */
                if (!empty($next_page_token)) {
                    $api_url .= '&pageToken=' . $next_page_token;
                }
                
                $data['api_url'] = $api_url;
                $json_text = json_decode(NetworkUtils::fetch_content_from_url($api_url));
                $data['body'] = $json_text;
            }
        } catch (Exception $e) {
        }
        return response()->json($data);
    }
    
    public function apiV3GetVideoDetail(Request $request) {
        /*
         * Ex:
         * Channel name: KplusSportsOfficial
         * User name: klauskkpm
         * https://www.googleapis.com/youtube/v3/search?key=AIzaSyChPS6h3XzZvPzGmcUf9hGhm02xTzEWPdY&channelId=UCue2DK0ccHtBrP9V__gYl6A&part=snippet,id&order=date&maxResults=50
         * */
        $data = array();
        $data['platform'] = 'Youtube';
        $data['body'] = '[]';
        
        try {
            
            $video_ids = $request->input('video_ids');
            
            if (!empty($video_ids)) {
                $api_url = "https://youtube.googleapis.com/youtube/v3/videos?part=statistics&key=" . $this::YT_V3_KEY . "&id=$video_ids";                
                $data['api_url'] = $api_url;
                $json_text = json_decode(NetworkUtils::fetch_content_from_url($api_url));
                $data['body'] = $json_text;
            }
        } catch (Exception $e) {
        }
        return response()->json($data);
    }
    
    /*
     * get html content from url
     * * /
    private function fetch_content_from_url($url) {
        try {            
            $curlSession = curl_init();
            curl_setopt($curlSession, CURLOPT_URL, $url);
            curl_setopt($curlSession, CURLOPT_BINARYTRANSFER, true);
            curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);
            $html = curl_exec($curlSession);
            curl_close($curlSession);
            
            return $html;
        } catch (Exception $e) {
        }
        return '';
    }
    
    /*
     * fetch channelId from html
     * */
    private function fetch_channel_id($html) {
        try {
            if (empty($html)) return '';
            
            $rel_canonical_pos = strpos($html, 'rel="canonical"');
            if ($rel_canonical_pos < 0) return '';
            
            $canonical_length = strlen('rel="canonical"');
            $href_length = strlen(' href="');
            $start_pos = $rel_canonical_pos + $canonical_length + $href_length;        
            if ($start_pos < 0) return '';
            
            $end_channel_link_post = strpos($html, '"', $start_pos + 5);
            if ($end_channel_link_post < 0) return '';
            
            $channel_url = substr($html, $start_pos, $end_channel_link_post - $start_pos);
            $channel_id = substr($channel_url, strrpos($channel_url, '/') + 1);        
            return $channel_id;
        } catch (Exception $e) {
        }
        return '';
    }
    
    /*
     * Lấy channelId từ tên kênh
     * Ví dụ: https://www.youtube.com/c/KplusSportsOfficial => channelId=UCBAai6Tz209ukZrjqlrUalw
     * 
     * */
    private function get_channelId_from_channel_name($channel_name) {
        try {
            $url = $this::YOUTUBE_CHANNEL_NAME_URL . $channel_name;            
            $html = NetworkUtils::fetch_content_from_url($url);            
            $channel_id = $this->fetch_channel_id($html);            
            return $channel_id;
        } catch (Exception $e) {  
        }
        return '';
    }
    
    /*
     * Lấy channelId từ tên user
     * Ví dụ: https://www.youtube.com/user/klauskkpm => channelId=UCfjTOrCPnAblTngWAzpnlMA
     *
     * */
    private function get_channelId_from_user_name($user_name) {
        try {
            $url = $this::YOUTUBE_USER_NAME_URL . $user_name;            
            $html = NetworkUtils::fetch_content_from_url($url);
            $channel_id = $this->fetch_channel_id($html);            
            return $channel_id;
        } catch (Exception $e) {
        }
        return '';
    }
    
    /*
     * 
     * */
    private function get_channel_id($channel_url) {
        try {
            
            $channel_var = substr($channel_url, strrpos($channel_url, '/') + 1);
            $query_pos = strpos($channel_var, '?');
            if ($query_pos > 0) {
                $channel_var = substr($channel_var, 0, $query_pos);
            }
            
            if (Utils::startsWith($channel_var, 'UC')) return $channel_var;
            
            $channel_id = $this->get_channelId_from_channel_name($channel_var);
            if (Utils::startsWith($channel_id, 'UC')) return $channel_id;
            
            $channel_id = $this->get_channelId_from_user_name($channel_var);
            if (Utils::startsWith($channel_id, 'UC')) return $channel_id;
            
            return $channel_id;
        } catch (Exception $e) {
        }
        return '';
    }
}
