<?php
namespace TSJIPPY\VIMEO;
use TSJIPPY;


/**
 * Plugin Name:  		Tsjippy Vimeo
 * Description:  		This plugin will upload all video's to Vimeo. It supports resumable uploads, meaning that if the page gets reloaded or internet connection is lost the video upload can be restarted and will continue where it was left. - A video title or description update is also synced to Vimeo. - You can enable the option to sync your media library with Vimeo, so that any videos added to Vimeo will also be added to your websites library - You can enable the option to delete a video from Vimeo if you delete the video from your media library.
 * Version:      		10.0.3
 * Author:       		Ewald Harmsen
 * AuthorURI:			harmseninnigeria.nl
 * Requires at least:	6.3
 * Requires PHP: 		8.3
 * Tested up to: 		6.9
 * Plugin URI:			https://github.com/Tsjippy/vimeo
 * Tested:				6.9
 * TextDomain:			tsjippy
 * Requires Plugins:	tsjippy-shared-functionality
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * @author Ewald Harmsen
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$pluginData = get_plugin_data(__FILE__, false, false);

// Define constants
define(__NAMESPACE__ .'\PLUGIN', plugin_basename(__FILE__));
define(__NAMESPACE__ .'\PLUGINPATH', __DIR__.'/');
define(__NAMESPACE__ .'\PLUGINVERSION', $pluginData['Version']);
define(__NAMESPACE__ .'\PLUGINSLUG', str_replace('tsjippy-', '', basename(__FILE__, '.php')));
define(__NAMESPACE__ .'\SETTINGS', get_option('tsjippy_'.PLUGINSLUG.'_settings', []));

