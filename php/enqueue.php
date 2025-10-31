<?php
namespace SIM\VIMEO;
use SIM;

// admin js
add_action( 'admin_enqueue_scripts', __NAMESPACE__.'\loadAssets');
function loadAssets(){
	wp_register_script('sim_vimeo_admin_script', SIM\pathToUrl(MODULE_PATH.'js/admin.min.js'), ['sim_formsubmit_script', 'sim_script'], MODULE_VERSION);
	wp_localize_script( 'sim_vimeo_admin_script',
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
    wp_register_style( 'vimeo_style', SIM\pathToUrl(MODULE_PATH.'css/style.min.css'), array(), MODULE_VERSION);

	wp_register_script('sim_vimeo_player', 'https://player.vimeo.com/api/player.js', [], false, true);

	wp_register_script('sim_vimeo_library_script', SIM\pathToUrl(MODULE_PATH.'js/vimeo_library.min.js'), ['sim_vimeo_player','media-audiovideo', 'sweetalert', 'sim_script'], MODULE_VERSION, true);

	wp_register_script('sim_vimeo_uploader_script', SIM\pathToUrl(MODULE_PATH.'js/vimeo_upload.min.js'), ['sim_script', 'sim_formsubmit_script'], MODULE_VERSION, true);

	if($_SERVER['PHP_SELF'] == "/simnigeria/wp-admin/upload.php"){
		wp_enqueue_script('sim_vimeo_library_script');
	}
}

//auto upload via js if enabled
if(SIM\getModuleOption(MODULE_SLUG, 'upload')){
	//load js script to change media screen
	add_action( 'wp_enqueue_media', __NAMESPACE__.'\loadMediaAssets');
}

function loadMediaAssets(){
	wp_enqueue_script('sim_vimeo_library_script');
}