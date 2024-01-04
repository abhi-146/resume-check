<?php
/**
* Plugin Name: Resume.Ai
* Description: This plugin test your resume against the job description
* Version: 0.0.1
* Author: Abhinav jain
* Author URI: https://zainmatrix.com/
* License: GPL+2
* Text Domain: Resume-ai
* Domain Path: /languages
*/

// Exit if accessed directly
if ( ! defined('ABSPATH') ) {
	exit;
}

add_action( 'init', 'rsai_init_plugin', 1 );
if (!function_exists('rsai_init_plugin')) {
	function rsai_init_plugin(){
		define ( 'rsai_PLUGIN_DIR', plugin_dir_path(__FILE__ ) );
		global $rsai_plugin_url, $rsai_text_domain;
		$rsai_plugin_url = plugin_dir_url( __FILE__ );
		$rsai_text_domain = 'Resume-ai';
		add_action( 'wp_enqueue_scripts', 'rsai_styles_scripts' );
		add_action( 'admin_enqueue_scripts', 'rsai_admin_script' );

		// Other files
		include(plugin_dir_path(__FILE__ ) . 'admin/settings.php');
		
		include(plugin_dir_path(__FILE__ ) . 'shortcode.php');
		include(plugin_dir_path(__FILE__ ) . 'inc/ajax-functions.php');
		
		load_plugin_textdomain( 'Resume-ai', false, 'Resume-ai' );
	}
}

// Back-end assets
if (!function_exists('rsai_admin_script')) {
	function rsai_admin_script(){
		wp_enqueue_style(
			'rsai-admin-style',
			plugin_dir_url( __FILE__ ) . 'admin/settings.css'
		);
		wp_enqueue_script(
			'rsai-admin-script',
			plugins_url('admin/settings.js',__FILE__ ),
			array('jquery')
		);
	}
}

if (!function_exists('rsai_styles_scripts')) {
	function rsai_styles_scripts(){
		
		wp_register_style(
			'rsai-style',
			plugin_dir_url( __FILE__ ) . 'css/style.css'
		);
		
		wp_register_script(
			'rsai-script',
			plugins_url('js/main.js',__FILE__ ),
			array('jquery')
		);
		
		
		wp_localize_script(
			'rsai-script',
			'ajax_rsai_obj',
			array( 'ajaxurl' 			=> admin_url( 'admin-ajax.php' ), 
					'nonce'				=> wp_create_nonce('rsai_ajax_nonce')
				 )
		);
	}
}


