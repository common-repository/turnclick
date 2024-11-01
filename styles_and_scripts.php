<?php

/********************************************************************************/
add_action('admin_init', 'Turnclick_admin_style');
function Turnclick_admin_style(){
  $url = plugins_url()."/turnclick/css/admin.css";

  if(is_admin()){
    wp_register_style( 'turnclick-admin-style', $url );

    wp_enqueue_style( 'turnclick-admin-style');
  }
}
/********************************************************************************/


/********************************************************************************/
add_action('wp_print_scripts', 'Turnclick_js');
function Turnclick_js(){
  $url = plugins_url()."/turnclick/js/turnclick.admin.js";

  if(is_admin()){
    wp_enqueue_script('turnclick-admin', $url, array('jquery'));
  }
}
/********************************************************************************/