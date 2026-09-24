<?php

namespace TSJIPPY\VIMEO;

use TSJIPPY;

add_action('wp_enqueue_scripts', __NAMESPACE__ . '\enqueueVimeoScripts');
add_action('admin_enqueue_scripts', __NAMESPACE__ . '\enqueueVimeoScripts');
/**
 * Registeres the CSS and JS
 */
function enqueueVimeoScripts()
{
    /** 
     * CSS
     */
    wp_register_style('vimeo_style', TSJIPPY\pathToUrl(PLUGINPATH . 'css/style.min.css'), array(), PLUGINVERSION);

    /**
     * Libraries
     */
    wp_register_script_module('@tsjippy/vimeo_player', 'https://player.vimeo.com/api/player.js', [], false);
    wp_register_script_module('@tsjippy/vimeo_tus', TSJIPPY\pathToUrl(PLUGINPATH . 'js/node_modules/tus-js-client.js/dist/tus.js'), [], false);

    /**
     * Modules
     */
    wp_register_script_module('@tsjippy/max_file_size', TSJIPPY\pathToUrl(PLUGINPATH . 'js/modules/max_file_size.js'), [], PLUGINVERSION);

    $deps   = SCRIPT_DEBUG ? [  
        '@tsjippy/vimeo_upload', 
        "@tsjippy/show_loader",
    ] :
    [];
    wp_register_script_module('@tsjippy/upload_video', TSJIPPY\pathToUrl(PLUGINPATH . 'js/modules/upload_video.js'), $deps, PLUGINVERSION);

    $deps   = SCRIPT_DEBUG ? [  
        "@tsjippy/show_loader"
    ] :
    [];
    wp_register_script_module('@tsjippy/vimeo_view', TSJIPPY\pathToUrl(PLUGINPATH . 'js/modules/vimeo_view.js'), $deps, PLUGINVERSION);

    /**
     * Scripts
     */

    // Admin Script
    $deps   = SCRIPT_DEBUG ? [  
        '@tsjippy/form_submit_functions', 
        "@tsjippy/show_loader", 
        "@tsjippy/display_message"
    ] :
    [];

    $deps[] = "@tsjippy/nonce_script";
    wp_register_script_module('@tsjippy/vimeo_admin_script', TSJIPPY\pathToUrl(PLUGINPATH . 'js/admin' . TSJIPPY\JSEXTENSION), $deps, PLUGINVERSION);

    // Vimeo Library
    $deps   = SCRIPT_DEBUG ? [  
        '@tsjippy/max_file_size', 
        "@tsjippy/upload_video", 
        "@tsjippy/vimeo_view"
    ] :
    [];

    $deps[] = '@tsjippy/vimeo_player';
    wp_register_script_module('@tsjippy/vimeo_library_script', TSJIPPY\pathToUrl(PLUGINPATH . 'js/vimeo_library' . TSJIPPY\JSEXTENSION), $deps, PLUGINVERSION);
    
    if (str_contains($_SERVER['PHP_SELF'],  "wp-admin/upload.php")) {
        wp_enqueue_script_module('@tsjippy/vimeo_library_script');
    }

    // Vimeo Uploader
    $deps   = SCRIPT_DEBUG ? [  
        '@tsjippy/form_submit_functions', 
        '@tsjippy/vimeo_tus', 
        "@tsjippy/display_message"
    ] :
    [];

    $deps[] = "@tsjippy/nonce_script";
    wp_register_script_module('@tsjippy/vimeo_uploader_script', TSJIPPY\pathToUrl(PLUGINPATH . 'js/vimeo_upload' . TSJIPPY\JSEXTENSION), $deps, PLUGINVERSION);
}

//auto upload via js if enabled
if (SETTINGS['upload'] ?? false) {
    //load js script to change media screen
    add_action('wp_enqueue_media', __NAMESPACE__ . '\loadMediaAssets');
}

/**
 * Registeres the CSS and JS
 */
function loadMediaAssets()
{
    wp_enqueue_script_module('@tsjippy/vimeo_library_script');
}
