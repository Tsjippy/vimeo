<?php
namespace SIM\VIMEO;
use SIM;

// Update vimeo when attachment has changed
add_action('sim_after_post_save',__NAMESPACE__.'\afterPostSave' );
function afterPostSave($post){
    if($post->post_type == 'attachment' && is_numeric($post->ID)){

        $vimeoApi		= new VimeoApi();
        $vimeoId        = $vimeoApi->getVimeoId($post->ID);
        if(!is_numeric($vimeoId)){
            return;
        }

        $data			= [];

        $newTitle       = sanitize_text_field($_POST['post-title']);

        // Only update when needed
        if(!empty($newTitle) && $newTitle != $post->post_title){
            $data['name']	= $newTitle;
        }
        
        if(!empty($_POST['post-content']) && $_POST['post-content'] != $post->post_content){
            $data['description']	= $_POST['post-content'];
        }

        if(!empty($data)){
            $vimeoApi->updateMeta($post->ID, $data);
        }
    }
}

add_filter('sim_attachment_preview', __NAMESPACE__.'\attachmentPreview', 10, 2);
function attachmentPreview($image, $postId){

    $vimeoApi   = new VimeoApi();
    $vimeoId    = $vimeoApi->getVimeoId($postId);

    if($vimeoId){
        return showVimeoVideo($vimeoId);
    }

    return  $image;
}