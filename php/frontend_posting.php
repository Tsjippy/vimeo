<?php

namespace TSJIPPY\VIMEO;

use TSJIPPY;

// Update vimeo when attachment has changed
/**
 * Allow comments
 * 
 * @param   \WP_Post    $post       The new or updated post
 * @param   object      $object     FrontEndContent Instance
 * @param   array       $request    The sanitized request data
 */
add_action('tsjippy-frontend-content-after-post-save', __NAMESPACE__ . '\afterPostSave', 10, 3);
function afterPostSave($post, $object, $request)
{
    if ($post->post_type == 'attachment' && is_numeric($post->ID)) {

        $vimeoApi        = new VimeoApi();
        $vimeoId        = $vimeoApi->getVimeoId($post->ID);
        if (!is_numeric($vimeoId)) {
            return;
        }

        $data            = [];

        $newTitle       = $request['post-title'];

        // Only update when needed
        if (!empty($newTitle) && $newTitle != $post->post_title) {
            $data['name']    = $newTitle;
        }

        if (($request['post-content'] ?? '') != $post->post_content) {
            $data['description']    = $request['post-content'];
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
