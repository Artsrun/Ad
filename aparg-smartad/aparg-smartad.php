<?php

/**
  Plugin Name: Aparg SmartAd
  Description: The only one of a kind WordPress plugin for managing ads with smart controlling.
  Version:     1.7
  Author:      Aparg
  Author URI:  https://aparg.com/
  Text Domain: apsa-aparg
  Domain Path: /languages/
  License:     GPL2

  This plugin is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as active by
  the Free Software Foundation, either version 2 of the License, or
  any later version.

  This plugin is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this plugin. If not, see https://wordpress.org/about/gpl/.
 */
defined('ABSPATH') or die('No script kiddies please!');

// include files
global $apsa_file_path;
global $apsa_all_forms; //===========Custom===============
$apsa_file_path = __FILE__;
global $apsa_plugin_basename;
$apsa_plugin_basename = plugin_basename(__FILE__);
include_once 'main/includes/apsa-config.php';
include_once 'framework/apsa-framework.php';
include_once 'main/includes/apsa-functions.php';
include_once 'main/includes/apsa-general.php'; //===========Custom===============
include_once 'main/view/admin/apsa-manage-settings.php';
include_once 'main/view/admin/apsa-custom-ads.php'; //===========Custom===============
// create plugin tables and options 

function apsa_child_activation() {
    //===========Custom===============
    include_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    global $wpdb;
    $wpdb->show_errors();
    /**
     * Create database tables for storage plugin data
     */
    $charset_collate = $wpdb->get_charset_collate();

    /** Create apsa_custom_ads table */
    $table_name = $wpdb->prefix . "apsa_custom_ads";

    $sql = "CREATE TABLE $table_name (
            id int(11) NOT NULL AUTO_INCREMENT,
            title varchar(255) NOT NULL,
            status varchar(255) NOT NULL,
            slug varchar(255) NOT NULL,
            data longtext NOT NULL,
            creation_date datetime NOT NULL,
            KEY status (status),
            UNIQUE KEY id (id)
          ) $charset_collate;";

    dbDelta($sql);
//===========Custom===============
    // set options
    $apsa_old_option = get_option('apsa_options');
    if ($apsa_old_option === FALSE) {
        $apsa_options = array();
        $apsa_options['apsa_warning_text'] = '';
        $apsa_options['apsa_warning_text_enabled'] = 'false';
        update_option('apsa_options', $apsa_options);
    }

    /*
     * check plugin db version
     * for old versions (option name is sa_db_version) set apsa_db_version to 1 in order to migrate
     */
    global $apsa_db_version;
    global $apsa_plugin_version;
    if (get_option('sa_db_version') !== FALSE) {
        update_option('apsa_db_version', '1');
    } else {
        if (get_option('apsa_db_version') === FALSE) {
            update_option('apsa_db_version', $apsa_db_version);
            update_option('apsa_plugin_version', $apsa_plugin_version);
        }
    }
}

// migrate plugin
function apsa_migrate_child() {
    include_once 'main/includes/apsa-migrate.php';
}

// load styles and scripts for admin page
function apsa_load_child_style_script_admin($admin_page) {
    global $apsa_admin_labels;
    global $apsa_file_path;

    if (strpos($admin_page, "apsa-manage-campaigns") !== FALSE) {
        global $apsa_all_forms; //===========Custom===============
        $apsa_all_forms = apsa_get_forms();
        $apsa_all_forms = $apsa_all_forms ? $apsa_all_forms : array();
        wp_register_script('apsa-child-functions', plugin_dir_url(__FILE__) . 'main/view/admin/apsa-child-functions.js');
        wp_enqueue_script('apsa-child-functions');
        wp_localize_script('apsa-child-functions', 'apsa_all_forms', $apsa_all_forms);
        /** Include url parser */
        wp_register_script('url-parser-js', plugin_dir_url(__FILE__) . 'main/view/admin/video-url-parser/js-video-url-parser.min.js');
        wp_enqueue_script('url-parser-js');
    }
    if (strpos($admin_page, "apsa-manage-settings") !== FALSE || strpos($admin_page, "apsa-custom-ads") !== FALSE) {
        wp_register_script('apsa-child-admin-settings', plugin_dir_url(__FILE__) . 'main/view/admin/apsa-child-admin-settings.js');
        wp_enqueue_script('apsa-child-admin-settings');
        //localize child admin labels
        wp_localize_script('apsa-child-admin-settings', 'apsa_admin_labels', $apsa_admin_labels);
        // localize ajax url in apsa-admin-scripts
        $admin_url = admin_url('admin-ajax.php');
        wp_localize_script('apsa-child-admin-settings', 'apsa_ajax_url', $admin_url);
    }
    /** Include Codemirror */
    if (strpos($admin_page, "apsa-manage-settings") !== FALSE) {


        wp_register_script('codemirror-scripts', plugin_dir_url(__FILE__) . 'framework/view/admin/codemirror/lib/codemirror.js');
        wp_enqueue_script('codemirror-scripts');

        wp_register_style('codemirror-styles', plugin_dir_url(__FILE__) . 'framework/view/admin/codemirror/lib/codemirror.css');
        wp_enqueue_style('codemirror-styles');

        wp_register_script('codemirror-mode-htmlmixed', plugin_dir_url(__FILE__) . 'framework/view/admin/codemirror/mode/htmlmixed/htmlmixed.js');
        wp_enqueue_script('codemirror-mode-htmlmixed');

        wp_register_script('codemirror-mode-javascript', plugin_dir_url(__FILE__) . 'framework/view/admin/codemirror/mode/javascript/javascript.js');
        wp_enqueue_script('codemirror-mode-javascript');

        wp_register_script('codemirror-mode-css', plugin_dir_url(__FILE__) . 'framework/view/admin/codemirror/mode/css/css.js');
        wp_enqueue_script('codemirror-mode-css');

        wp_register_script('codemirror-mode-xml', plugin_dir_url(__FILE__) . 'framework/view/admin/codemirror/mode/xml/xml.js');
        wp_enqueue_script('codemirror-mode-xml');

        //localize options in apsa-child-admin-settings
        $apsa_options = get_option('apsa_options');
        $apsa_options['apsa_warning_text'] = stripslashes($apsa_options['apsa_warning_text']);
        wp_localize_script('apsa-child-admin-settings', 'apsa_options', $apsa_options);
    }





    //===========Custom===============

    if (strpos($admin_page, "apsa-custom-ads") !== FALSE) {
        /* -------------Localizing Default values for template builder------------------------- */
        $apsa_admin_url = get_admin_url();
        $apsa_plugin_dir_url = plugin_dir_url($apsa_file_path);
        wp_localize_script('apsa-child-admin-settings', 'apsa_admin_url', $apsa_admin_url);
        wp_localize_script('apsa-child-admin-settings', 'apsa_plugin_dir_url', $apsa_plugin_dir_url);

        wp_register_style('apsa-child-admin-styles', plugin_dir_url(__FILE__) . 'main/view/admin/apsa-child-admin-settings.css');
        wp_enqueue_style('apsa-child-admin-styles');

        wp_enqueue_script('jquery');

        wp_enqueue_script('jquery-ui-sortable');

        wp_register_style('apsa-form-styles', plugin_dir_url(__FILE__) . 'main/view/admin/template-builder/apsa-builder-styles.css');
        wp_enqueue_style('apsa-form-styles');
        wp_register_script('apsa-form-builder', plugin_dir_url(__FILE__) . 'main/view/admin/template-builder/apsa-builder-scripts.js', array('jquery', 'apsa-tinymce-scripts'));
        wp_enqueue_script('apsa-form-builder');
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_media();
        wp_register_script('apsa-tinymce-scripts', includes_url('js/tinymce/') . 'wp-tinymce.php', array('jquery'), false, true);
        wp_enqueue_script('apsa-tinymce-scripts');
        /*
         * localizing initial data for form builder module
         */
        $inital_template_data = apsa_get_template_inital_data();

        wp_localize_script('apsa-child-admin-settings', 'apsa_inital_temp_data', $inital_template_data);

        /*
         * localizing all templates conig files array 
         */
        $all_templates = apsa_get_all_templates_data();
        wp_localize_script('apsa-child-admin-settings', 'apsa_all_templates_data', $all_templates);
    }
}

add_action('admin_enqueue_scripts', 'apsa_load_child_style_script_admin');

// load styles and scripts for front page
function apsa_load_child_style_script_view() {
    // add child css
    wp_register_style('apsa-child-front-styles', plugin_dir_url(__FILE__) . 'main/view/front/apsa-child-front-styles.css');
    wp_enqueue_style('apsa-child-front-styles');

    // add child js
    wp_register_script('apsa-child-front', plugin_dir_url(__FILE__) . 'main/view/front/apsa-child-front.js');
    wp_enqueue_script('apsa-child-front');

    // _localize_ options in apsa-front-scripts
    $apsa_options = get_option('apsa_options');
    $apsa_options['apsa_warning_text'] = stripslashes($apsa_options['apsa_warning_text']);
    wp_localize_script('apsa-child-front', 'apsa_options', $apsa_options);

    // localize adblocker warning default text in apsa-child-front
    global $apsa_admin_labels;
    wp_localize_script('apsa-child-front', 'apsa_warning_default', $apsa_admin_labels["adblock_warning_default"]);
}

add_action('wp_enqueue_scripts', 'apsa_load_child_style_script_view');

/**
 * load admin labels
 */
function apsa_load_labels() {
    include_once 'main/includes/apsa-labels.php';
}

/**
 *  allow upload flash
 */
function pixert_allow_flash($mimes) {

    $mimes['swf'] = 'application/x-shockwave-flash';

    return $mimes;
}

add_filter('upload_mimes', 'pixert_allow_flash');

// ajax handler for get apsa_options
function apsa_ajax_get_options() {
    $apsa_options = get_option('apsa_options');
    $apsa_options['apsa_warning_text'] = stripslashes($apsa_options['apsa_warning_text']);

    echo json_encode($apsa_options);
    wp_die();
}

add_action('wp_ajax_apsa_ajax_get_options', 'apsa_ajax_get_options');
add_action('wp_ajax_nopriv_apsa_ajax_get_options', 'apsa_ajax_get_options');
