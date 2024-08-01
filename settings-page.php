<?php
/**
 * Plugin Name: Manage Site Settings
 * Description: Creates a Site Settings page menu
 * Version: 1.0
 * Author: Taras Yurchysnyn
 * Author URI: https://www.linkedin.com/in/yurchyshyn/
 * License: GPL2
 */


add_action('admin_notices', 'check_acf_installed');
function check_acf_installed() {
    if ( ! class_exists('ACF') ) {
        echo '<div class="notice notice-error"><p>Wrong: Advanced Custom Fields (ACF) is not installed or activated.</p></div>';
    }

    deactivate_plugins(plugin_basename(__FILE__));
}


register_activation_hook(__FILE__, 'manage_site_settings_page_create');
register_deactivation_hook(__FILE__, 'settings_page_remove');


function manage_site_settings_page_create() {
    $page_title = 'Site Settings';
    $page_slug = 'site-settings';
    $page = get_page_by_path($page_slug);

    if (!$page) {
        $page_id = wp_insert_post(array(
            'post_title'   => $page_title,
            'post_name'    => $page_slug,
            'post_content' => '',
            'post_status'  => 'publish',
            'post_type'    => 'page',
        ));

        update_option('site_settings_page_id', $page_id);
    } else {
        update_option('site_settings_page_id', $page->ID);
    }
}


function settings_page_remove() {
    $settings_page = get_page_by_path('site-settings', OBJECT, 'page');

    if ($settings_page) {
        wp_delete_post($settings_page->ID, true);
        error_log("Page with ID {$settings_page->ID} has been deleted.");
    } else {
        error_log("Page with slug 'site-settings' not found.");
    }
}


add_filter('use_block_editor_for_post', 'disable_gutenberg_on_settings_page', 5, 2);
function disable_gutenberg_on_settings_page($can, $post){
    if($post && $post->post_name === "site-settings"){
        remove_post_type_support('page', 'editor'); 
        remove_post_type_support('page', 'thumbnail'); 
        remove_submenu_page('edit.php?post_type=page', 'post-new.php?post_type=page');
        echo '<style>
            #titlediv, #edit-slug-box, #pageparentdiv, .page-title-action, #delete-action, #minor-publishing, .postbox-header { display: none !important; }
            #major-publishing-actions{background: #fff !important;}
            .postbox {border: none !important;}
            #side-sortables{margin-top: 20px !important;}
        </style>';
        return false;
    }
    return $can;
}


add_action('pre_get_posts', 'hide_settings_page');
function hide_settings_page($query) {
    if ( !is_admin() && !is_main_query() ) {
        return;
    }    
    global $typenow;
    if ($typenow === "page") {
        $settings_page = get_page_by_path("site-settings", NULL, "page")->ID;
        $query->set('post__not_in', array($settings_page));    
    }
}


add_action('edit_form_after_title', 'add_custom_text_below_title');
function add_custom_text_below_title() {
    if (get_post_type() === 'page') {
        echo '<div class="custom-text-below-title" style="margin-top: 20px; font-size: 18px; color: #555; background-color: #cecece; padding:16px">
        1. Add all needed fields via ACF plugin.
        <br><br>
        2. Use these fields:
        <p>$allOptions = get_fields('.get_option('site_settings_page_id').'); </p>
        <p>$specificOption = get_field("option_field_name", '.get_option('site_settings_page_id').')</p>
        <p><em>'.get_option('site_settings_page_id').' - is the ID of the current options page</em></p></div>';
    }
}


add_action('admin_menu', 'add_site_settings_to_menu');
function add_site_settings_to_menu(){
    add_menu_page('Site Settings', 'Site Settings', 'manage_options', 'post.php?post='.get_page_by_path("site-settings", NULL, "page")->ID.'&action=edit', '', 'dashicons-admin-generic', 120);
}


add_filter('parent_file', 'highlight_custom_settings_page');
function highlight_custom_settings_page($file) {
    global $parent_file;
    global $pagenow;
    global $typenow, $self;

    $settings_page = get_page_by_path("site-settings", NULL, "page")->ID;

    $post = (int)$_GET["post"];
    if ($pagenow === "post.php" && $post === $settings_page) {
        $file = "post.php?post=$settings_page&action=edit";
    }
    return $file;
}

add_action('admin_title', 'edit_site_settings_title');
function edit_site_settings_title($title) {
    global $post, $action, $current_screen;
    if (isset($current_screen->post_type) && $current_screen->post_type === 'page' && $action == 'edit' && $post->post_name === "site-settings") {
        $title = $post->post_title.' - '.get_bloginfo('name');           
    }
    return $title;  
}
?>