<?php
namespace SIM\VIMEO;
use SIM;
use Vimeo\Vimeo;

//shortcode to display vimeo video's
add_shortcode("vimeo_video", function($atts){
	return showVimeoVideo($atts['id']);
});

function showVimeoVideo($vimeoId){

	// Load css
	wp_enqueue_style( 'vimeo_style');

	$vimeoApi	= new VimeoApi();
	$html		= $vimeoApi->getEmbedHtml($vimeoId);

	ob_start();
	?>
	<div class="vimeo-wrapper">
		<div class="loader-image-trigger" data-size="100" data-text="Loading video..."></div>
		<div class='vimeo-embed-container'>
			<?php echo $html;?>
		</div>
	</div>
	<?php
	
	return ob_get_clean();
}

add_filter( 'wp_video_shortcode', __NAMESPACE__.'\videoShortcode', 10, 4 );
function videoShortcode($output, $atts, $video, $postId){
	$vimeoId	= get_post_meta($postId, 'vimeo_id', true);

	if(!is_numeric($vimeoId)){
		return $output;
	}

	return showVimeoVideo($vimeoId);
}