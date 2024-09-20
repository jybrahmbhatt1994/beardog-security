<?php
/**
 * Plugin Name: Beardog Security
 * Plugin URI: https://beardog.digital
 * Description: Just another security plugin.
 * Version: 1.0.0
 * Author: Jainish Brahmbhatt
 * License: GPL2
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

define('PASSCODE','loginbdtech');
function beardog_login_url(){
  // redirect to login page when passcode is verified
  if( !is_user_logged_in() && parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY) == PASSCODE ){
    wp_safe_redirect( home_url('wp-login.php?'. PASSCODE .'&redirect=false') );
    exit();
  }
  // redirect to dashboard if user has already logged in
  if( is_user_logged_in() && parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY) == PASSCODE ){
    wp_safe_redirect( home_url("wp-admin") );
    exit();
  } }
add_action( 'init', 'beardog_login_url');

function beardog_login_redirects(){
  if( isset($_POST['passcode']) && $_POST['passcode'] == PASSCODE) return false;

  // redirects to dashboard when /wp-admin is accessed and user is logged in
  if ( (is_user_logged_in()) && (strpos($_SERVER['REQUEST_URI'], 'wp-admin') !== false)) {
    wp_safe_redirect( home_url("wp-admin"), 302 );
    exit();
  }
  // redirects to homepage when /wp-admin or /wp-login is accessed and user is not logged in
  if ( (!is_user_logged_in()) && ((strpos($_SERVER['REQUEST_URI'], 'wp-admin') !== false) || (strpos($_SERVER['REQUEST_URI'], 'wp-login') !== false)) && ( strpos($_SERVER['REQUEST_URI'], PASSCODE) === false ) ) {
    wp_safe_redirect( home_url(), 302 );
    exit();
  }
  // redirect to homepage after logout
  if( strpos($_SERVER['REQUEST_URI'], 'action=logout') !== false ){
    check_admin_referer( 'log-out' );
    wp_logout();
    wp_safe_redirect( home_url('?logged-out'), 302 );
    exit();
  } 
}
add_action( 'login_init', 'beardog_login_redirects', 1);

// Add a passcode hidden field to login form
function custom_login_hidden_field(){
  echo '<input type="hidden" name="passcode" value="'. PASSCODE .'" />';
}
add_action('login_form', 'custom_login_hidden_field');

// Disable theme and plugin editors
function disable_theme_and_plugin_editors() {
    if ( !current_user_can('manage_options') ) {
        // Disable Theme Editor
        define('DISALLOW_FILE_EDIT', true);
        
        // Disable Plugin Editor
        define('DISALLOW_FILE_MODS', true);
    }
}
add_action('init', 'disable_theme_and_plugin_editors');

function restrict_editor_access() {
    if (current_user_can('editor')) {
        global $menu;
        global $submenu;

        // Remove menu items from admin dashboard
        remove_menu_page('index.php');
        remove_menu_page('edit.php?post_type=page');
        remove_menu_page('upload.php');
        remove_menu_page('edit-comments.php');
        remove_menu_page('themes.php');
        remove_menu_page('plugins.php');
        remove_menu_page('users.php');
        remove_menu_page('tools.php');
        remove_menu_page('options-general.php');
        remove_menu_page('edit.php?post_type=your_custom_post_type');
        remove_menu_page('wpcf7');
        remove_menu_page('theme-general-settings');
        remove_menu_page('edit.php?post_type=agr_google_review');
        remove_menu_page('profile.php');
        remove_menu_page('wpseo_workouts');
        remove_menu_page('wpseo_redirects');
        remove_menu_page('edit.php?post_type=city-service');
        remove_menu_page('edit.php?post_type=attorney');
        remove_menu_page('edit.php?post_type=practicearea');
        remove_menu_page('edit.php?post_type=practice-area');
        remove_menu_page('edit.php?post_type=practice-areas');
        remove_menu_page('awesome-google-review');
        remove_menu_page('edit.php?post_type=acf-field-group');
        remove_menu_page('beardog-seo-enhancer');
        remove_menu_page('cfdb7-list.php');
        remove_menu_page('export-personal-data.php');
        remove_menu_page('tws-activate-contact-forms-anti-spam');
        
        
        // Optionally remove submenus
        // Uncomment any lines to remove submenu items
        // remove_submenu_page('edit.php', 'edit-tags.php?taxonomy=category'); // Categories
        // remove_submenu_page('edit.php', 'edit-tags.php?taxonomy=post_tag'); // Tags
    }
}
add_action('admin_menu', 'restrict_editor_access', 999);

