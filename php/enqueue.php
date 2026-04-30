<?php
namespace TSJIPPY\VIMEO;
use TSJIPPY;

// admin js
add_action( 'admin_enqueue_scripts', __NAMESPACE__.'\loadAssets');
function loadAssets(){
	wp_register_script('tsjippy_vimeo_admin_script', TSJIPPY\pathToUrl(PLUGINPATH.'js/admin.min.js'), ['tsjippy_formsubmit_script', 'tsjippy_script'], PLUGINVERSION);
	wp_localize_script( 'tsjippy_vimeo_admin_script',
		'sim',
		array(
			'baseUrl' 		=> get_home_url(),
			'restNonce'		=> wp_create_nonce('wp_rest'),
			'restApiPrefix'	=> '/'.RESTAPIPREFIX
		)
	);
}


add_action( 'wp_enqueue_scripts', __NAMESPACE__.'\enqueueVimeoScripts');
add_action( 'admin_enqueue_scripts', __NAMESPACE__.'\enqueueVimeoScripts');
function enqueueVimeoScripts(){
    // Load css
    wp_register_style( 'vimeo_style', TSJIPPY\pathToUrl(PLUGINPATH.'css/style.min.css'), array(), PLUGINVERSION);

	wp_register_script('tsjippy_vimeo_player', 'https://player.vimeo.com/api/player.js', [], false, true);

	wp_register_script('tsjippy_vimeo_library_script', TSJIPPY\pathToUrl(PLUGINPATH.'js/vimeo_library.min.js'), ['tsjippy_vimeo_player','media-audiovideo', 'sweetalert', 'tsjippy_script'], PLUGINVERSION, true);

	wp_register_script('tsjippy_vimeo_uploader_script', TSJIPPY\pathToUrl(PLUGINPATH.'js/vimeo_upload.min.js'), ['tsjippy_script', 'tsjippy_formsubmit_script'], PLUGINVERSION, true);

	if($_SERVER['PHP_SELF'] == "/simnigeria/wp-admin/upload.php"){
		wp_enqueue_script('tsjippy_vimeo_library_script');
	}
}

//auto upload via js if enabled
if(SETTINGS['upload'] ?? false){
	//load js script to change media screen
	add_action( 'wp_enqueue_media', __NAMESPACE__.'\loadMediaAssets');
}

function loadMediaAssets(){
	wp_enqueue_script('tsjippy_vimeo_library_script');
}