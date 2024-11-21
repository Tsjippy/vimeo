<?php
namespace SIM\VIMEO;
use SIM;

add_filter('sim_media_gallery_item_html', __NAMESPACE__.'\mediaItem', 10, 3);
function mediaItem($mediaHtml, $type, $postId){
    if($type != 'video'){
        return $mediaHtml;
    }

    $vimeoApi      = new VimeoApi();

    $vimeoId    = $vimeoApi->getVimeoId($postId);

    // Vimeo video
    if(is_numeric($vimeoId)){
        return showVimeoVideo($vimeoId);
    }

    return $mediaHtml;
}

add_filter('sim_media_gallery_download_url', __NAMESPACE__.'\downloadUrl', 10, 2);
function downloadUrl($url, $postId){
    $vimeoApi   = new VimeoApi();
    $path       = $vimeoApi->getVideoPath($postId);

    if($path){
        return SIM\pathToUrl($path);
    }

    return $url;
}

add_filter('sim_media_gallery_download_filename', __NAMESPACE__.'\downloadFileName', 10, 3);
function downloadFileName($fileName, $type, $postId){
    if($type != 'video'){
        return $fileName;
    }

    $vimeoApi   = new VimeoApi();
    $path       = $vimeoApi->getVideoPath($postId);

    if($path){
        $fileName   = basename($path);
        $vimeoId    = $vimeoApi->getVimeoId($postId);

        $fileName   = str_replace($vimeoId.'_', '', $fileName);

        return $fileName;
    }

    return $fileName;
}