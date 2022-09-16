<?php

// Facebook //
function getFacebookLike($fbid, $token)
{
    $json_string = @file_get_contents('https://graph.facebook.com/v5.0/' . $fbid . '/?fields=name&access_token=' . $token);
    $json = json_decode($json_string, true);
    $like_count = isset($json['name']) ? $json['name'] : 0;
    return $like_count;
}
