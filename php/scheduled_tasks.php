<?php

namespace TSJIPPY\VIMEO;

use TSJIPPY;

add_action('init', __NAMESPACE__ . '\scheduleTasks');
/**
 * Schedule all tasks for this plugin
 */
function scheduleTasks()
{
    TSJIPPY\scheduleTask('tsjippy-vimeo-create-thumbnails', 'daily', __NAMESPACE__, 'createVimeoThumbnails');
    if (SETTINGS['sync'] ?? false) {
        TSJIPPY\scheduleTask('tsjippy-vimeo-sync', 'daily', __NAMESPACE__, 'vimeoSync');
    }
}

/**
 * create local thumbnails
 */
function createVimeoThumbnails()
{

    $args = array(
        'post_type'       => 'attachment',
        'numberposts'     => -1,
        'meta_query'      => array(
            array(
                'key'     => 'tsjippy_vimeo_id'
            ),
            array(
                'key'     => 'thumbnail',
                'compare' => 'NOT EXISTS'
            )
        )
    );
    $posts = get_posts($args);

    if (!empty($posts)) {
        $vimeoApi    = new VimeoApi();
        foreach ($posts as $post) {
            $vimeoApi->getThumbnail($post->ID);
        }
    }
}

/**
 * sync local db with vimeo.com
 */
function vimeoSync()
{

    $vimeoApi    = new VimeoApi();

    if ($vimeoApi->isConnected()) {
        $vimeoVideos    = $vimeoApi->getUploadedVideos();
        if (!$vimeoVideos) {
            return;
        }

        $indexedVideos  = [];

        // use the vimeo id as the index for easy finding
        foreach ($vimeoVideos as $vimeoVideo) {
            $vimeoId                    = str_replace('/videos/', '', $vimeoVideo['uri']);

            $indexedVideos[$vimeoId]    = $vimeoVideo;
        }

        // update the cache
        update_option('tsjippy-vimeo-videos', $indexedVideos);

        $args = array(
            'post_type'      => 'attachment',
            'numberposts'    => -1,
            'meta_query'    => array(
                array(
                    'key'   => 'tsjippy_vimeo_id'
                )
            )
        );
        $posts = get_posts($args);

        $localVideos    = [];
        $onlineVideos    = [];

        //Build the local videos array
        foreach ($posts as $post) {
            $vimeoId    = get_post_meta($post->ID, 'tsjippy_vimeo_id', true);
            if (is_numeric($vimeoId)) {
                $localVideos[$vimeoId]    = $post->ID;
            }
        }

        //Build online video's array
        foreach ($vimeoVideos as $vimeoVideo) {
            $vimeoId                = str_replace('/videos/', '', $vimeoVideo['uri']);
            $onlineVideos[$vimeoId]    = html_entity_decode($vimeoVideo['name']);
        }

        //remove any local video which does not exist on vimeo
        foreach (array_diff_key($localVideos, $onlineVideos) as $vimeoId => $postId) {
            $vimeoId        = get_post_meta($postId, 'tsjippy_vimeo_id', true);
            TSJIPPY\printArray("Deleting video with vimeo id $vimeoId");
            wp_delete_post($postId);
        }

        //add any video which does not exist locally
        foreach (array_diff_key($onlineVideos, $localVideos) as $vimeoId => $videoName) {
            $vimeoApi->createVimeoPost($videoName, 'video/mp4', $vimeoId);
        }

        // Backup any video who is not yet backed up
        $files      = glob($vimeoApi->backupDir . '*.mp4');
        $files      = apply_filters('tsjippy-vimeo-local-files', $files);
        foreach (array_keys($onlineVideos) as $vimeoId) {
            // If the video does not exist locally
            if (empty(preg_grep("~$vimeoId~", $files))) {
                $post   = $vimeoApi->getPost($vimeoId);
                $vimeoApi->downloadVideo($post->ID);
            }
        }
    }
}
