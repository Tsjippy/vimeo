<?php

namespace TSJIPPY\VIMEO;

use TSJIPPY;

// Update vimeo when attachment has changed
add_action('tsjippy-frontend-content-after-post-save', __NAMESPACE__ . '\afterPostSave');
function afterPostSave($post)
{
    if ($post->post_type == 'attachment' && is_numeric($post->ID)) {

        $vimeoApi        = new VimeoApi();
        $vimeoId        = $vimeoApi->getVimeoId($post->ID);
        if (!is_numeric($vimeoId)) {
            return;
        }

        $data            = [];

        $newTitle       = TSJIPPY\sanitize($_POST['post-title']);

        // Only update when needed
        if (!empty($newTitle) && $newTitle != $post->post_title) {
            $data['name']    = $newTitle;
        }

        if (($_POST['post-content'] ?? '') != $post->post_content) {
            $data['description']    = TSJIPPY\sanitize($_POST['post-content'], 'textarea_field');
        }

        if (!empty($data)) {
            $vimeoApi->updateMeta($post->ID, $data);
        }
    }
}

add_filter('tsjippy-frontend-content-attachment-preview', __NAMESPACE__ . '\attachmentPreview', 10, 2);
function attachmentPreview($image, $postId)
{

    $vimeoApi   = new VimeoApi();
    $vimeoId    = $vimeoApi->getVimeoId($postId);

    if ($vimeoId) {
        return showVimeoVideo($vimeoId);
    }

    return  $image;
}
