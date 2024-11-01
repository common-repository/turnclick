<?php
/**
 * Plugin Name: TurnClick Embed Code for WordPress
 * Plugin URI: https://www.turnclick.com/plugins/wordpress/turnclick
 * Description: TurnClick's WordPress Plugin allows TurnClick users to automatically install the embed code on their WordPress websites.
 * Version: 1.0
 * Author: TurnClick
 * Author URI: https://www.turnclick.com
 * License: TurnClick Software License
 */

include('classes/class.turnclick.php');
include('styles_and_scripts.php');


/********************************************************************************/
//the actual theme hook to add our js to their page
add_action('wp_head','turnclick_js_include');
/********************************************************************************/
function turnclick_js_include(){
	$turnclick = new turnclickApp\turnclick;

	if(!empty($turnclick->turnclick_customer_id)){
		print '<script src="'.esc_url('//cdn.turnclick.com/client/'.$turnclick->turnclick_customer_id.'.js').'"></script>';
	}
}
/********************************************************************************/



/********************************************************************************/
//display custom message directing them to the settings area
/********************************************************************************/
function turnclick_activate() {
  add_option('Turnclick_Activated_Plugin', 'Turnclick-Plugin-Slug');
}
register_activation_hook(basename(dirname(__FILE__)).'/'.basename(__FILE__), 'turnclick_activate');

function load_turnclick_plugin() {
    if (is_admin() && get_option('Turnclick_Activated_Plugin') == 'Turnclick-Plugin-Slug'){
        delete_option( 'Turnclick_Activated_Plugin' );
		print '<div class="updated notice is-dismissible" style="border-color: #f51490;">';
		print '	<p>Almost done! <a href="/wp-admin/options-general.php?page=turnclick">Enter your TurnClick username</a> and you\'ll be all set.</p>';
        print '</div>';
    }
}
add_action('admin_init', 'load_turnclick_plugin');
/********************************************************************************/


/********************************************************************************/
//add our submenu option to the settings menu
/********************************************************************************/
add_action('admin_menu', 'add_turnclick_location_manager');
function add_turnclick_location_manager(){
	add_options_page( 'TurnClick Settings', 'TurnClick Settings', 'manage_options', 'turnclick', 'turnclick');
}
/********************************************************************************/


/********************************************************************************/
//gets called when a user clicks our menu option
/********************************************************************************/
function turnclick() {
	if (!current_user_can('manage_options')){
		wp_die( __('You do not have sufficient permissions to access this page.'));
	}
	
	$turnclick = new turnclickApp\turnclick;

	if(isset($_POST['turnclick_action'])){
		$turnclick->process_form();
	}

	$turnclick->build_form();
}
/********************************************************************************/
