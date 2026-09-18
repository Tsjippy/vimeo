<?php

namespace TSJIPPY\VIMEO;

use TSJIPPY;

// admin js
add_action('admin_enqueue_scripts', __NAMESPACE__ . '\loadAssets');
function loadAssets()
{
    wp_register_script_module('@tsjippy/vimeo_admin_script', TSJIPPY\pathToUrl(PLUGINPATH . 'js/admin.min.js'), ['@tsjippy/formsubmit_script', '@tsjippy/main'], PLUGINVERSION);

    add_filter( 'script_module_data_@tsjippy/vimeo_admin_script', function($data){
        $data['baseUrl']       = get_home_url();
        $data['restApiPrefix'] = '/' . TSJIPPY\RESTAPIPREFIX;
        $data['restNonce']     = wp_create_nonce('wp_rest');

        return $data; 
    } );
}


add_action('wp_enqueue_scripts', __NAMESPACE__ . '\enqueueVimeoScripts');
add_action('admin_enqueue_scripts', __NAMESPACE__ . '\enqueueVimeoScripts');
function enqueueVimeoScripts()
{
    // Load css
    wp_register_style('vimeo_style', TSJIPPY\pathToUrl(PLUGINPATH . 'css/style.min.css'), array(), PLUGINVERSION);

    wp_register_script_module('@tsjippy/vimeo_player', 'https://player.vimeo.com/api/player.js', [], false);

    wp_register_script_module('@tsjippy/vimeo_library_script', TSJIPPY\pathToUrl(PLUGINPATH . 'js/vimeo_library.min.js'), ['tsjippy_vimeo_player', 'media-audiovideo', '@tsjippy/main'], PLUGINVERSION);

    wp_register_script_module('@tsjippy/vimeo_uploader_script', TSJIPPY\pathToUrl(PLUGINPATH . 'js/vimeo_upload.min.js'), ['@tsjippy/main', '@tsjippy/formsubmit_script'], PLUGINVERSION);

    if (str_contains($_SERVER['PHP_SELF'],  "wp-admin/upload.php")) {
        wp_enqueue_script_module('@tsjippy/vimeo_library_script');
    }
}

//auto upload via js if enabled
if (SETTINGS['upload'] ?? false) {
    //load js script to change media screen
    add_action('wp_enqueue_media', __NAMESPACE__ . '\loadMediaAssets');
}

function loadMediaAssets()
{
    wp_enqueue_script_module('@tsjippy/vimeo_library_script');
}
