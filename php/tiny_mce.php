<?php
namespace SIM\VIMEO;
use SIM;


//change hyperlink to shortcode for vimeo videos
add_filter( 'media_send_to_editor', __NAMESPACE__.'\sendToEditor', 10, 9 );
function sendToEditor($html, $id, $attachment) {
	if(str_contains($attachment['url'], 'vimeo.com')){
		$vimeoId	= get_post_meta($id, 'vimeo_id', true);

		$html		= "[vimeo_video id=$vimeoId]";
	}
	
	return $html;
}