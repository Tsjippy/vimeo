<?php

namespace TSJIPPY\VIMEO;

use TSJIPPY;

add_filter('tsjippy-media-gallery-item-html', __NAMESPACE__ . '\mediaItem', 10, 3);
function mediaItem($mediaHtml, $type, $postId)
{
    if ($type != 'video') {
        return $mediaHtml;
    }

    $vimeoApi      = new VimeoApi();

    $vimeoId    = $vimeoApi->getVimeoId($postId);

    // Vimeo video
    if (is_numeric($vimeoId)) {
        return showVimeoVideo($vimeoId);
    }

    return $mediaHtml;
}

add_filter('tsjippy-media-gallery-download-url', __NAMESPACE__ . '\downloadUrl', 10, 2);
function downloadUrl($url, $postId)
{
    $vimeoApi   = new VimeoApi();
    $path       = $vimeoApi->getVideoPath($postId);

    if (file_exists($path)) {
        return TSJIPPY\pathToUrl($path);
    }

    return $url;
}

add_filter('tsjippy-media-gallery-download-filename', __NAMESPACE__ . '\downloadFileName', 10, 3);
function downloadFileName($fileName, $type, $postId)
{
    if ($type != 'video') {
        return $fileName;
    }

    $vimeoApi   = new VimeoApi();
    $path       = $vimeoApi->getVideoPath($postId);

    if (file_exists($path)) {
        $fileName   = basename($path);
        $vimeoId    = $vimeoApi->getVimeoId($postId);

        $fileName   = str_replace($vimeoId . '_', '', $fileName);

        return $fileName;
    }

    return $fileName;
}
