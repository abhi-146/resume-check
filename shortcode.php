<?php
/*
* Shortcode to display new post form
*/

// Exit if accessed directly
if ( ! defined('ABSPATH') ) {
   exit;
}

add_shortcode('rsai_checker', 'rsai_checker_callback');
if (!function_exists('rsai_checker_callback')) {
	function rsai_checker_callback($atts){
		if( !is_admin() ){

			wp_enqueue_style('rsai-style');
			wp_enqueue_script('rsai-script');

			$html = '<form id="rsai_resume_form" method="post" enctype="multipart/form-data">
					<input type="file" id="pdffile" name="pdfFile" accept=".pdf" required />
					<input type="url" id="url" name="sourceUrl" placeholder="Enter URL" required />
					<input type="submit" value="Submit" />
					<div id="resultDiv"></div>
				</form>
			';

			return $html;

		}
	}
}
