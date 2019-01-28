<?php
defined('ABSPATH') or die('No script kiddies please!');


global $apsa_plugin_data;
global $apsa_file_path;
// Include configurations
include_once 'includes/apsa-framework-config.php';

/**
 * Adding custom links into plugis page 
 * @param array $links
 * @return array
 */
function apsa_add_action_links($links) {
    global $apsa_admin_labels;

    $custom_links = array(
        // Link to dashboard
        '<a href="' . menu_page_url('apsa-manage-campaigns', FALSE) . '">' . $apsa_admin_labels["plugin_settings"] . '</a>',
    );
    return array_merge($links, $custom_links);
}

global $apsa_plugin_basename;
add_filter('plugin_action_links_' . $apsa_plugin_basename, 'apsa_add_action_links');

/**
 * Setup when installing 
 */
function apsa_aparg_install($networkwide) {
    global $wpdb;
    global $apsa_framework_version;
    global $apsa_framework_db_version;
    global $apsa_db_version;
    global $apsa_plugin_version;

    apsa_merge_config();
    if (function_exists('is_multisite') && is_multisite()) {
        //check if it is network activation if so run the activation function for each id
        if ($networkwide) {
            $old_blog = $wpdb->blogid;
            //Get all blog ids
            $blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
            foreach ($blogids as $blog_id) {
                switch_to_blog($blog_id);
                //Create database table if not exists
                apsa_activation();
                if (function_exists('apsa_child_activation'))
                    apsa_child_activation();
                // Include migrate file
                $apsa_changed = FALSE;
                if ((get_option('apsa_framework_version') !== FALSE && get_option('apsa_framework_version') < $apsa_framework_version) || (get_option('apsa_framework_db_version') !== FALSE && get_option('apsa_framework_db_version') < $apsa_framework_db_version)) {
                    $apsa_changed = true;
                    include_once 'includes/apsa-framework-migrate.php';
                    update_option('apsa_framework_version', $apsa_framework_version);
                    update_option('apsa_framework_db_version', $apsa_framework_db_version);
                }
                if ((get_option('apsa_plugin_version') !== FALSE && get_option('apsa_plugin_version') < $apsa_plugin_version) || (get_option('apsa_db_version') !== FALSE && get_option('apsa_db_version') < $apsa_db_version)) {
                    $apsa_changed = true;
                    if (function_exists('apsa_migrate_child')) {
                        apsa_migrate_child();
                        update_option('apsa_plugin_version', $apsa_plugin_version);
                        update_option('apsa_db_version', $apsa_db_version);
                    }
                }
                // when change plugin or framework version apsa_view_count set 0
                if ($apsa_changed) {
                    $apsa_extra_option = get_option('apsa_extra_options');
                    $apsa_extra_option['apsa_view_count'] = 0;
                    update_option('apsa_extra_options', $apsa_extra_option);
                }
            }
            switch_to_blog($old_blog);
            return;
        }
    }
    //Create database table if not exists

    apsa_activation();
    if (function_exists('apsa_child_activation'))
        apsa_child_activation();

    // Include migrate file
    $apsa_changed = FALSE;
    if ((get_option('apsa_framework_version') !== FALSE && get_option('apsa_framework_version') < $apsa_framework_version) || (get_option('apsa_framework_db_version') !== FALSE && get_option('apsa_framework_db_version') < $apsa_framework_db_version)) {
        $apsa_changed = true;
        include_once 'includes/apsa-framework-migrate.php';
        update_option('apsa_framework_version', $apsa_framework_version);
        update_option('apsa_framework_db_version', $apsa_framework_db_version);
    }
    if ((get_option('apsa_plugin_version') !== FALSE && get_option('apsa_plugin_version') < $apsa_plugin_version) || (get_option('apsa_db_version') !== FALSE && get_option('apsa_db_version') < $apsa_db_version)) {
        $apsa_changed = true;
        if (function_exists('apsa_migrate_child')) {
            apsa_migrate_child();
            update_option('apsa_plugin_version', $apsa_plugin_version);
            update_option('apsa_db_version', $apsa_db_version);
        }
    }

    // when change plugin or framework version apsa_view_count set 0
    if ($apsa_changed) {
        $apsa_extra_option = get_option('apsa_extra_options');
        $apsa_extra_option['apsa_view_count'] = 0;
        update_option('apsa_extra_options', $apsa_extra_option);
    }
}

register_activation_hook($apsa_file_path, 'apsa_aparg_install');

/**
 * Function for create database tables for storage plugin data
 */
function apsa_activation() {
    include_once ABSPATH . 'wp-admin/includes/upgrade.php';

    global $wpdb;
    $wpdb->show_errors();
    /**
     * Create database tables for storage plugin data
     */
    $charset_collate = $wpdb->get_charset_collate();

    /** Create apsa_campaigns table */
    $table_name = $wpdb->prefix . "apsa_campaigns";

    $sql = "CREATE TABLE $table_name (
            id int(11) NOT NULL AUTO_INCREMENT,
            name varchar(200) NOT NULL,
            type varchar(20) NOT NULL,
            status varchar(20) NOT NULL,
            creation_date datetime NOT NULL,
            KEY type (type),
            KEY status (status),
            UNIQUE KEY id (id)
          ) $charset_collate;";

    dbDelta($sql);

    /** Create apsa_campaign_options table */
    $table_name = $wpdb->prefix . "apsa_campaign_options";

    $sql = "CREATE TABLE $table_name (
            id int(11) NOT NULL AUTO_INCREMENT,
            campaign_id int(11) NOT NULL,
            option_name varchar(50) NOT NULL,
            option_value varchar(255) NOT NULL,
            KEY campaign_id (campaign_id),
            KEY option_name (option_name),
            UNIQUE KEY id (id),
            UNIQUE KEY unique_camp_option (campaign_id,option_name)
          ) $charset_collate;";

    dbDelta($sql);

    /** Create statistics table */
    $table_name = $wpdb->prefix . "apsa_element_statistics";

    $sql = "CREATE TABLE $table_name (
            element_id int(11) NOT NULL,
            type varchar(20) NOT NULL,
            count int(11) NOT NULL,
            date date NOT NULL,
            UNIQUE KEY unique_stat (element_id,type,date)
          ) $charset_collate;";

    dbDelta($sql);

    /** Create apsa_elements table */
    $table_name = $wpdb->prefix . "apsa_elements";

    $sql = "CREATE TABLE $table_name (
            id int(11) NOT NULL AUTO_INCREMENT,
            campaign_id int(11) NOT NULL,
            title varchar(255) NOT NULL,
            type varchar(20) NOT NULL,
            priority smallint(5) NOT NULL,
            creation_date datetime NOT NULL,
            status varchar(20) NOT NULL,
            KEY campaign_id (campaign_id),
            KEY priority (priority),
            UNIQUE KEY id (id)
          ) $charset_collate;";

    dbDelta($sql);

    /** Create apsa_campaign_options table */
    $table_name = $wpdb->prefix . "apsa_element_options";

    $sql = "CREATE TABLE $table_name (
            id int(11) NOT NULL AUTO_INCREMENT,
            element_id int(11) NOT NULL,
            option_name varchar(50) NOT NULL,
            option_value longtext NOT NULL,
            KEY element_id (element_id),
            KEY option_name (option_name),
            UNIQUE KEY id (id),
            UNIQUE KEY unique_element_option (element_id,option_name)
          ) $charset_collate;";

    dbDelta($sql);

    $apsa_old_extra_option = get_option('apsa_extra_options');
    if ($apsa_old_extra_option === FALSE) {
        $apsa_extra_options = array();
        global $apsa_plugin_data;
        $apsa_extra_options['apsa_custom_css'] = '';
        if ($apsa_plugin_data['anticache'] == 'true')
            $apsa_extra_options['apsa_cache_enabled'] = 'false';
        $apsa_extra_options['apsa_view_count'] = 0;
        update_option('apsa_extra_options', $apsa_extra_options);
    }
    global $apsa_framework_version;
    $apsa_old_framework_version = get_option('apsa_framework_version');
    if ($apsa_old_framework_version === FALSE) {
        update_option('apsa_framework_version', $apsa_framework_version);
    }
    global $apsa_framework_db_version;
    $apsa_old_framework_db_version = get_option('apsa_framework_db_version');
    if ($apsa_old_framework_db_version === FALSE) {
        update_option('apsa_framework_db_version', $apsa_framework_db_version);
    }
}

/**
 * Create subscribe table for new multisite created
 */
function apsa_activation_mu($blog_id) {
    global $apsa_plugin_basename;
    if (is_plugin_active_for_network($apsa_plugin_basename)) {
        switch_to_blog($blog_id);
        apsa_activation();
        if (function_exists('apsa_child_activation'))
            apsa_child_activation();
        restore_current_blog();
    }
}

add_action('wpmu_new_blog', 'apsa_activation_mu');

/**
 * Delete subscribe table when multisite blog is deleted
 */
function apsa_delete_tables_mu($tables) {
    apsa_merge_config();
    global $wpdb;
    global $apsa_uninstall;
    $apsa_tables = $apsa_uninstall['apsa_tables'];
    foreach ($apsa_tables as $apsa_table) {
        $tables[] = $wpdb->prefix . $apsa_table;
    }
    return $tables;
}

add_filter('wpmu_drop_tables', 'apsa_delete_tables_mu');

// merge framework and plugin configs
function apsa_merge_config() {
    global $apsa_uninstall;
    global $apsa_child_uninstall;
    if (!empty($apsa_child_uninstall)) {
        $apsa_uninstall = array_merge_recursive($apsa_uninstall, $apsa_child_uninstall);
    }
}

/**
 * Include scripts and styles in admin
 */
function apsa_load_style_script_admin($admin_page) {

    global $apsa_admin_labels;
    if (strpos($admin_page, "apsa-") !== FALSE) {
        // set view count in plugin pages
        $apsa_extra_options = get_option('apsa_extra_options');
        if ($apsa_extra_options !== FALSE) {
            if (isset($apsa_extra_options['apsa_view_count']) && intval($apsa_extra_options['apsa_view_count']) > 30) {
                ?>
                <script type="text/javascript">
                    apsa_new = false;
                </script>
                <?php
            } else {
                // set anticache notice in plugin pages
                $active_plugins = get_option('active_plugins');
                $has_cache_plugin = apsa_cache_installed();

                if (isset($apsa_extra_options['apsa_cache_enabled']) && $apsa_extra_options['apsa_cache_enabled'] != 'true' && empty($_COOKIE['apsa_anticache_notice']) && $has_cache_plugin) {

                    function apsa_notice() {
                        global $apsa_admin_labels;
                        ?>
                        <div class="update-nag notice apsa-anticache-notice">
                            <p><?php echo str_replace('%gen_settings%', '<a class="apsa-href-general" href="' . menu_page_url('apsa-manage-general-settings', false) . '">' . $apsa_admin_labels["general_settings"] . '</a>', $apsa_admin_labels["anticache_notice"]); ?></p>
                            <span class="apsa-dismissible"></span>
                        </div>
                        <?php
                    }

                    add_action('admin_notices', 'apsa_notice');
                }

                $apsa_extra_options['apsa_view_count'] = isset($apsa_extra_options['apsa_view_count']) ? intval($apsa_extra_options['apsa_view_count']) + 1 : 0;
                update_option('apsa_extra_options', $apsa_extra_options);
            }
        }

        // adding styles to all pages
        wp_register_style('apsa-general-styles', plugin_dir_url(__FILE__) . 'view/admin/apsa-general-styles.css');
        wp_enqueue_style('apsa-general-styles');

        // adding general scripts to all pages
        wp_register_script('apsa-general-scripts', plugin_dir_url(__FILE__) . 'view/admin/apsa-general-scripts.js', array('wp-color-picker'), false, true);
        wp_enqueue_script('apsa-general-scripts');

        wp_enqueue_script('jquery-ui-datepicker');

        /** Localize month names and day names */
        $dyn_month_names = array();
        $month_names = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
        foreach ($month_names as $month_name) {
            $dyn_month_names[] = date_i18n('F', strtotime('01-' . $month_name));
        }

        $dyn_day_names = array();
        $day_names = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
        foreach ($day_names as $day_name) {
            $dyn_day_names[] = date_i18n('l', strtotime($day_name));
        }

        $dyn_month_short_names = array();
        $month_names = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
        foreach ($month_names as $month_name) {
            $dyn_month_short_names[] = date_i18n('M', strtotime('01-' . $month_name));
        }

        $dyn_day_short_names = array();
        $day_names = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
        foreach ($day_names as $day_name) {
            $dyn_day_short_names[] = date_i18n('D', strtotime($day_name));
        }

        wp_localize_script('apsa-general-scripts', 'apsa_month_names', $dyn_month_names);
        wp_localize_script('apsa-general-scripts', 'apsa_day_names', $dyn_day_names);
        wp_localize_script('apsa-general-scripts', 'apsa_month_short_names', $dyn_month_short_names);
        wp_localize_script('apsa-general-scripts', 'apsa_day_short_names', $dyn_day_short_names);

        /** Localize start of week */
        $start_of_week = get_option('start_of_week');

        wp_localize_script('apsa-general-scripts', 'apsa_start_of_week', $start_of_week);

        //convert wp date format to jquery
        $date_format_options = array(
            // Day
            'd' => 'dd',
            'D' => 'D',
            'j' => 'd',
            'l' => 'DD',
            'N' => '',
            'S' => '',
            'w' => '',
            'z' => 'o',
            // Week
            'W' => '',
            // Month
            'F' => 'MM',
            'm' => 'mm',
            'M' => 'M',
            'n' => 'm',
            't' => '',
            // Year
            'L' => '',
            'o' => '',
            'Y' => 'yy',
            'y' => 'y',
            // Time
            'a' => '',
            'A' => '',
            'B' => '',
            'g' => '',
            'G' => '',
            'h' => '',
            'H' => '',
            'i' => '',
            's' => '',
            'u' => ''
        );

        /** Localize date format */
        wp_localize_script('apsa-general-scripts', 'apsa_date_format', str_replace(array_keys($date_format_options), array_values($date_format_options), get_option('date_format')));


        wp_register_style('apsa-admin-styles', plugin_dir_url(__FILE__) . 'view/admin/apsa-admin-styles.css');
        wp_enqueue_style('apsa-admin-styles');

        wp_register_style('apsa-admin-mobile-styles', plugin_dir_url(__FILE__) . 'view/admin/apsa-admin-mobile.css');
        wp_enqueue_style('apsa-admin-mobile-styles');
    }

    if (strpos($admin_page, "apsa-manage-campaigns") === FALSE && strpos($admin_page, "apsa-manage-general-settings") === FALSE) {
        return;
    }


    wp_register_style('jquery-ui-styles', plugin_dir_url(__FILE__) . 'view/admin/jquery-ui/jquery-ui.min.css');
    wp_enqueue_style('jquery-ui-styles');

    wp_enqueue_script('jquery');

    wp_enqueue_script('jquery-ui-sortable');

    wp_enqueue_media();

    if (strpos($admin_page, "apsa-manage-general-settings") !== FALSE) {
        wp_register_script('apsa-admin-settings-scripts', plugin_dir_url(__FILE__) . 'view/admin/apsa-admin-settings-scripts.js');
        wp_enqueue_script('apsa-admin-settings-scripts');
        /** localize admin labels in apsa-admin-settings-scripts */
        wp_localize_script('apsa-admin-settings-scripts', 'apsa_admin_labels', $apsa_admin_labels);
    } else {
        wp_register_script('apsa-admin-scripts', plugin_dir_url(__FILE__) . 'view/admin/apsa-admin-scripts.js', array('wp-color-picker'), false, true);
        wp_enqueue_script('apsa-admin-scripts');
        /** localize admin labels in apsa-admin-scripts */
        wp_localize_script('apsa-admin-scripts', 'apsa_admin_labels', $apsa_admin_labels);
    }
    // localize ajax url in apsa-admin-scripts
    // $lang added only for qtranslate(NOT X) as it doesn't detect language in ajax (WPML and qtranslateX do it well)
    $lang = (apsa_qtranslate_old_available() != false) ? '?lang=' . apsa_get_language() : '';
    wp_localize_script('apsa-admin-scripts', 'apsa_ajax_url', admin_url('admin-ajax.php' . $lang));
    wp_localize_script('apsa-admin-settings-scripts', 'apsa_ajax_url', admin_url('admin-ajax.php'));

    //localize extra options in apsa-admin-scripts and apsa-admin-settings-scripts
    $apsa_extra_options = get_option('apsa_extra_options');
    $apsa_extra_options['apsa_custom_css'] = stripslashes($apsa_extra_options['apsa_custom_css']);
    wp_localize_script('apsa-admin-scripts', 'apsa_extra_options', $apsa_extra_options);
    wp_localize_script('apsa-admin-settings-scripts', 'apsa_extra_options', $apsa_extra_options);

    // Localize animation effects
    global $apsa_effects;
    wp_localize_script('apsa-admin-scripts', 'apsa_effects', $apsa_effects);

    // Localize plugin data
    global $apsa_plugin_data;
    wp_localize_script('apsa-admin-scripts', 'apsa_plugin_data', $apsa_plugin_data);

    // Localize framework event name
    global $apsa_fr_event_name;
    wp_localize_script('apsa-admin-scripts', 'apsa_fr_event_name', $apsa_fr_event_name);

    wp_register_script('highcharts-scripts', plugin_dir_url(__FILE__) . 'view/admin/highcharts/highcharts.min.js');
    wp_enqueue_script('highcharts-scripts');

    wp_register_script('highcharts-export', plugin_dir_url(__FILE__) . 'view/admin/highcharts/exporting.min.js');
    wp_enqueue_script('highcharts-export');

    wp_register_script('highcharts-export', plugin_dir_url(__FILE__) . 'view/admin/highcharts/offline-exporting.min.js');
    wp_enqueue_script('highcharts-export');

    wp_register_script('jspdf', plugin_dir_url(__FILE__) . 'view/admin/highcharts/jspdf.min.js');
    wp_enqueue_script('jspdf');

    wp_register_script('rgbcolor', plugin_dir_url(__FILE__) . 'view/admin/highcharts/rgbcolor.min.js');
    wp_enqueue_script('rgbcolor');

    wp_register_script('canvg', plugin_dir_url(__FILE__) . 'view/admin/highcharts/canvg.js');
    wp_enqueue_script('canvg');

    /** Include Codemirror */
    wp_register_script('codemirror-scripts', plugin_dir_url(__FILE__) . 'view/admin/codemirror/lib/codemirror.js');
    wp_enqueue_script('codemirror-scripts');

    wp_register_style('codemirror-styles', plugin_dir_url(__FILE__) . 'view/admin/codemirror/lib/codemirror.css');
    wp_enqueue_style('codemirror-styles');

    wp_register_script('codemirror-mode-htmlmixed', plugin_dir_url(__FILE__) . 'view/admin/codemirror/mode/htmlmixed/htmlmixed.js');
    wp_enqueue_script('codemirror-mode-htmlmixed');

    wp_register_script('codemirror-mode-javascript', plugin_dir_url(__FILE__) . 'view/admin/codemirror/mode/javascript/javascript.js');
    wp_enqueue_script('codemirror-mode-javascript');

    wp_register_script('codemirror-mode-css', plugin_dir_url(__FILE__) . 'view/admin/codemirror/mode/css/css.js');
    wp_enqueue_script('codemirror-mode-css');

    wp_register_script('codemirror-mode-xml', plugin_dir_url(__FILE__) . 'view/admin/codemirror/mode/xml/xml.js');
    wp_enqueue_script('codemirror-mode-xml');

    /** Include Tagsinput */
    wp_register_style('bootstrap-css', plugin_dir_url(__FILE__) . 'view/admin/bootstrap-tagsinput/bootstrap-styles.css');
    wp_enqueue_style('bootstrap-css');

    wp_register_style('bootstrap-tagsinput-css', plugin_dir_url(__FILE__) . 'view/admin/bootstrap-tagsinput/dist/bootstrap-tagsinput.css');
    wp_enqueue_style('bootstrap-tagsinput-css');

    wp_register_style('bootstrap-app-css', plugin_dir_url(__FILE__) . 'view/admin/bootstrap-tagsinput/tagsinput-app.css');
    wp_enqueue_style('bootstrap-app-css');

    wp_register_script('bootstrap-js', plugin_dir_url(__FILE__) . 'view/admin/bootstrap-tagsinput/bootstrap-scripts.js');
    wp_enqueue_script('bootstrap-js');

    wp_register_script('typeahead-js', plugin_dir_url(__FILE__) . 'view/admin/bootstrap-tagsinput/typeahead-scripts.js');
    wp_enqueue_script('typeahead-js');

    wp_register_script('bootstrap-tagsinput-js', plugin_dir_url(__FILE__) . 'view/admin/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js');
    wp_enqueue_script('bootstrap-tagsinput-js');

    /** Include colorpicker */
    // Add the color picker css file       
    wp_enqueue_style('wp-color-picker');
}

add_action('admin_enqueue_scripts', 'apsa_load_style_script_admin');

/**
 * Include scripts and styles in view
 */
function apsa_load_style_script_view() {

    wp_enqueue_script('jquery');

    // Add css 
    wp_register_style('apsa-front-styles', plugin_dir_url(__FILE__) . 'view/front/apsa-front-styles.css');
    wp_enqueue_style('apsa-front-styles');

    // Add animate styles
    wp_register_style('apsa-animate', plugin_dir_url(__FILE__) . 'view/front/apsa-animate.min.css');
    wp_enqueue_style('apsa-animate');

    // Add scripts
    wp_register_script('apsa-front-scripts', plugin_dir_url(__FILE__) . 'view/front/apsa-front-scripts.js');
    wp_enqueue_script('apsa-front-scripts');

    // localize ajax url in apsa-front-scripts
    // $lang added only for qtranslate(NOT X) as it doesn't detect language in ajax (WPML and qtranslateX do it well)
    $lang = (apsa_qtranslate_old_available() != false) ? '?lang=' . apsa_get_language() : '';
    wp_localize_script('apsa-front-scripts', 'apsa_ajax_url', admin_url('admin-ajax.php' . $lang));

    // localize extra options
    $apsa_extra_options = get_option('apsa_extra_options');
    wp_localize_script('apsa-front-scripts', 'apsa_extra_options', $apsa_extra_options);

    //localize is_page, is_single, is_archive for apsa-front-scripts
    $apsa_page_info['apsa_is_page'] = is_page();
    $apsa_page_info['apsa_is_single'] = is_single();
    $apsa_page_info['apsa_is_archive'] = is_archive();
    $apsa_page_info['apsa_get_queried_object'] = get_queried_object();
    $apsa_page_info['apsa_get_the_ID'] = get_the_ID();
    $apsa_page_info['apsa_get_taxonomies'] = get_taxonomies();
    wp_localize_script('apsa-front-scripts', 'apsa_page_info', $apsa_page_info);

    //localize plugin_dir in admin-scripts
    wp_localize_script('apsa-front-scripts', 'apsa_plugin_dir', plugin_dir_url(__FILE__));

    // Localize plugin campaign types
    global $apsa_plugin_data;
    wp_localize_script('apsa-front-scripts', 'apsa_plugin_data', $apsa_plugin_data);
}

add_action('wp_enqueue_scripts', 'apsa_load_style_script_view');

/**
 * added custom css
 */
function apsa_add_custom_css() {
    $apsa_extra_options = get_option('apsa_extra_options');
    $custom_css = stripslashes($apsa_extra_options['apsa_custom_css']);
    $apsa_have_anticache = isset($apsa_extra_options['apsa_cache_enabled']) ? $apsa_extra_options['apsa_cache_enabled'] : 'false';
    if ($apsa_have_anticache == 'false' && $custom_css != '') {
        echo '<style type="text/css">' . $custom_css . '</style>';
    }
}

add_action('wp_head', 'apsa_add_custom_css');

/**
 * added inline css
 */
function apsa_add_inline_css_admin() {
    // check current page
    $apsa_current = get_current_screen();
    $apsa_current_page = $apsa_current->id;
    if (strpos($apsa_current_page, 'apsa-manage-campaigns')) {
        // inline styles for elements type image
        global $apsa_plugin_data;
        $apsa_element_types = $apsa_plugin_data['element_data'];
        $inline_style = '';
        foreach ($apsa_element_types as $apsa_element_type) {
            $inline_style .= '[data-apsa-element-type="' . $apsa_element_type["name"] . '"] {background-image: url(' . $apsa_element_type["icon_path"] . ');}';
        }
        if ($inline_style != '') {
            echo '<style type="text/css">' . $inline_style . '</style>';
        }
    }
}

add_action('admin_head', 'apsa_add_inline_css_admin');

/**
 * Load text domain
 */
function apsa_aparg_text_domain() {
    global $apsa_plugin_basename;
    load_plugin_textdomain('apsa-aparg', false, plugin_dir_path($apsa_plugin_basename) . 'languages');
    apsa_load_labels();
}

add_action('plugins_loaded', 'apsa_aparg_text_domain');

// Include functions for creating campaigns managing menu page in admin section
include_once 'view/admin/apsa-manage-campaigns.php';

// Include actions file
include_once 'includes/apsa-framework-functions.php';

if ($apsa_plugin_data['campaign_types']['embed'] == 'true') {
    // Include embed widget creation file
    include_once 'view/front/campaign-embed/apsa-embed-extra.php';

    // Include embed shortcode creation file
    include_once 'view/front/campaign-embed/apsa-embed-maker.php';

    // Get active embed campaigns
    add_action('init', 'apsa_retrieve_active_embeds');
}
if ($apsa_plugin_data['campaign_types']['popup'] == 'true') {
    // Include popup maker
    include_once 'view/front/campaign-popup/apsa-popup-maker.php';
}
if ($apsa_plugin_data['campaign_types']['background'] == 'true') {
    // Include bg maker
    include_once 'view/front/campaign-bg/apsa-bg-maker.php';
}
if ($apsa_plugin_data['campaign_types']['sticky'] == 'true') {
    // Include sticky maker
    include_once 'view/front/campaign-sticky/apsa-sticky-maker.php';
}

/**
 * Get all active embeds and hold on global variable
 */
function apsa_retrieve_active_embeds() {
    global $apsa_active_campaigns;
    $apsa_active_campaigns = apsa_get_active_campaigns('embed');
}

/**
 * Ajax handler for add new campaign
 */
function apsa_ajax_new_campaign() {
    if (!current_user_can('manage_options')) {
        die();
    }

    $success = 1;

    $type = $_POST['type'];

    $defaults = array();
    if ($type == 'popup' || $type == 'background') {
        $apsa_camps = apsa_get_campaigns('all', $type);
        if (!empty($apsa_camps)) {
            $success = 0;
            $response = array('success' => $success);
            echo json_encode($response);
            wp_die();
        }
    }
    $new_campaign = apsa_add_campaign($type, "", "suspended", $defaults);

    if (empty($new_campaign['campaign_id'])) {
        $success = 0;
    }

    $response = array('success' => $success, 'campaign_id' => $new_campaign['campaign_id'], 'creation_date' => $new_campaign['creation_date']);
    echo json_encode($response);

    wp_die();
}

add_action('wp_ajax_apsa_ajax_new_campaign', 'apsa_ajax_new_campaign');

/**
 * Ajax handler for update campaign options
 */
function apsa_ajax_update_campaign_options() {
    if (!current_user_can('manage_options')) {
        die();
    }

    global $apsa_admin_labels;
    $success = 1;

    $campaign_name = $_POST['campaign_name'];
    $campaign_id = $_POST['campaign_id'];
    $campaign_options = $_POST['campaign_options'];
    $options = array();

    foreach ($campaign_options as $key => $campaign_option) {

        $options[$campaign_option['name']] = $campaign_option['value'];
    }

    /** Update campaign basic data */
    // Check if compaign name set, else set default name
    if (empty($campaign_name)) {
        $campaign_type = apsa_get_campaign_data($campaign_id, "type");

        switch ($campaign_type) {
            case 'background':
                $campaign_name = $apsa_admin_labels["camp_name_background"];
                break;
            case 'popup':
                $campaign_name = $apsa_admin_labels["camp_name_popup"];
                break;
            case 'embed':
                $campaign_name = $apsa_admin_labels["camp_name_embed"];
                break;
            case 'sticky':
                $campaign_name = $apsa_admin_labels["camp_name_sticky"];
                break;
            default:
                $campaign_name = '';
                break;
        }

        $campaign_date = apsa_get_campaign_data($campaign_id, "creation_date");
        $campaign_date = date_i18n(get_option('date_format'), strtotime($campaign_date));
        $campaign_name .= " - (" . $campaign_date . ")";
    }

    $update_camp = apsa_update_campaign($campaign_id, $campaign_name);
    $update_camp_opt = apsa_update_campaign_options($campaign_id, $options);

    if ($update_camp === FALSE || $update_camp_opt === FALSE) {
        $success = 0;
    } else {
        if (isset($_POST['campaign_elements'])) {

            $elements = $_POST['campaign_elements'];

            $apsa_child_update = apsa_update_child_element_options($elements);
            if (!$apsa_child_update)
                $success = 0;
        }
    }

    $response = array('success' => $success);
    echo json_encode($response);

    wp_die();
}

add_action('wp_ajax_apsa_ajax_update_campaign_options', 'apsa_ajax_update_campaign_options');

/**
 * Ajax handler for add new element
 */
function apsa_ajax_add_element() {
    if (!current_user_can('manage_options')) {
        die();
    }

    $success = 1;

    $campaign_id = $_POST['campaign_id'];
    $type = $_POST['type'];

    $new_element = apsa_insert_element($campaign_id, '', $type);

    if ($new_element['element_id'] === FALSE) {
        $success = 0;
    }

    $response = array('success' => $success, 'element_id' => $new_element['element_id'], 'creation_date' => $new_element['creation_date']);
    echo json_encode($response);

    wp_die();
}

add_action('wp_ajax_apsa_ajax_add_element', 'apsa_ajax_add_element');

/**
 * Ajax handler for update campaign status or delete
 */
function apsa_ajax_update_campaign_status() {
    if (!current_user_can('manage_options')) {
        die();
    }

    $success = 1;

    $campaign_id = $_POST['campaign_id'];
    $campaign_action = $_POST['campaign_action'];
    $campaign_type = $_POST['campaign_type'];

    if ($campaign_action == "delete") {
        $action = apsa_delete_campaign($campaign_id);
    } else {
        $action = apsa_update_campaign($campaign_id, '', $campaign_type, $campaign_action);
    }

    if ($action === FALSE) {
        $success = 0;
    }

    $response = array('success' => $success);
    echo json_encode($response);

    wp_die();
}

add_action('wp_ajax_apsa_ajax_update_campaign_status', 'apsa_ajax_update_campaign_status');

/**
 * Ajax handler for delete element
 */
function apsa_ajax_delete_element() {
    if (!current_user_can('manage_options')) {
        die();
    }

    $success = 1;

    $element_id = $_POST['element_id'];

    if (apsa_delete_element($element_id) === FALSE) {
        $success = 0;
    }

    $response = array('success' => $success);
    echo json_encode($response);

    wp_die();
}

add_action('wp_ajax_apsa_ajax_delete_element', 'apsa_ajax_delete_element');

/**
 * Ajax handler for update element statisitcs
 */
function apsa_ajax_update_element_statistics() {
    $success = 1;

    $element_id = $_POST["element_id"];
    $type = $_POST["type"];

    if (apsa_update_element_statistics($element_id, $type) === FALSE) {
        $success = 0;
    }

    $response = array('success' => $success);
    echo json_encode($response);

    wp_die();
}

add_action('wp_ajax_apsa_ajax_update_element_statistics', 'apsa_ajax_update_element_statistics');
add_action('wp_ajax_nopriv_apsa_ajax_update_element_statistics', 'apsa_ajax_update_element_statistics');

/**
 * Ajax handler for update element statisitcs
 */
function apsa_ajax_get_element_statistics() {

    $from = $_POST["from"];
    if (empty($from)) {
        $from = FALSE;
    }

    $to = $_POST["to"];
    if (empty($to)) {
        $to = FALSE;
    }

    $element_id = $_POST["element_id"];

    $statistics = apsa_get_element_statistics($element_id, $from, $to);

    echo json_encode($statistics);

    wp_die();
}

add_action('wp_ajax_apsa_ajax_get_element_statistics', 'apsa_ajax_get_element_statistics');

/**
 * Ajax handler for get campaign statisitcs
 */
function apsa_ajax_get_camp_statistics() {

    $from = $_POST["from"];
    if (empty($from)) {
        $from = FALSE;
    }

    $to = $_POST["to"];
    if (empty($to)) {
        $to = FALSE;
    }

    $camp_id = $_POST["camp_id"];

    $statistics = apsa_get_camp_statistics($camp_id, $from, $to);

    echo json_encode($statistics);

    wp_die();
}

add_action('wp_ajax_apsa_ajax_get_camp_statistics', 'apsa_ajax_get_camp_statistics');

/**
 * Ajax handler for update element status
 */
function apsa_ajax_update_element_status() {
    if (!current_user_can('manage_options')) {
        die();
    }

    $success = 1;

    $element_id = $_POST['element_id'];
    $status = $_POST['status'];

    if (apsa_update_element($element_id, "", "", "", $status) === FALSE) {

        $success = 0;
    }

    $response = array('success' => $success);
    echo json_encode($response);

    wp_die();
}

add_action('wp_ajax_apsa_ajax_update_element_status', 'apsa_ajax_update_element_status');

/**
 * Ajax handler for autocomplete
 */
function apsa_ajax_autocomplete() {
    global $apsa_plugin_data;
    global $apsa_admin_labels;

    if (isset($_GET["query"])) {
        $search_query = $_GET["query"];

        $all_taxonomies = get_taxonomies();
        unset($all_taxonomies["link_taxonomy"]);
        unset($all_taxonomies["post_format"]);

        $all_terms = get_terms($all_taxonomies, array(
            "hide_empty" => FALSE,
            "name__like" => $search_query,
            "offset" => 100
        ));

        $responce_array = array();

        // added all posts and all pages
        if (mb_strpos(mb_strtolower($apsa_admin_labels["apsa_all_pages"]['loc']), mb_strtolower($search_query)) !== false) {
            array_push($responce_array, array(
                "value" => "page%apsa_all_pages%" . $apsa_admin_labels["apsa_all_pages"]['not_loc'] . "%" . strtolower($apsa_plugin_data['plugin_data']['name']) . "%",
                "text" => $apsa_admin_labels["apsa_all_pages"]['loc'],
                "type" => 'page'
            ));
        }
        if (mb_strpos(mb_strtolower($apsa_admin_labels["apsa_all_posts"]['loc']), mb_strtolower($search_query)) !== false) {
            array_push($responce_array, array(
                "value" => "post%apsa_all_posts%" . $apsa_admin_labels["apsa_all_posts"]['not_loc'] . "%" . strtolower($apsa_plugin_data['plugin_data']['name']) . "%",
                "text" => $apsa_admin_labels["apsa_all_posts"]['loc'],
                "type" => 'post'
            ));
        }

        foreach ($all_terms as $key => $term) {
            if ($term->taxonomy == "post_tag") {
                array_push($responce_array, array(
                    "value" => "post_tag%" . $term->term_id . "%" . $term->name . "%" . strtolower($apsa_plugin_data['plugin_data']['name']) . "%",
                    "text" => html_entity_decode($term->name),
                    "type" => 'post_tag'
                ));
            } else {
                array_push($responce_array, array(
                    "value" => "category%" . $term->term_id . "%" . $term->name . "%" . strtolower($apsa_plugin_data['plugin_data']['name']) . "%",
                    "text" => html_entity_decode($term->name),
                    "type" => 'category'
                ));
            }
        }

        // The Query
        add_filter('posts_where', 'apsa_title_like_posts_where', 10, 2);

        function apsa_title_like_posts_where($where, &$wp_query) {
            global $wpdb;
            if ($post_title_like = $wp_query->get('post_title_like')) {
                $wp_version = get_bloginfo('version');

                if ($wp_version < 4) {
                    $title_like = like_escape($post_title_like);
                    if ($wp_version < 3.6) {
                        $where .= " AND " . $wpdb->posts . ".post_title LIKE '%" . $wpdb->escape($title_like) . "%'";
                    } else {
                        $where .= " AND " . $wpdb->posts . ".post_title LIKE '%" . esc_sql($title_like) . "%'";
                    }
                } else {
                    $title_like = $wpdb->esc_like($post_title_like);
                    $where .= " AND " . $wpdb->posts . ".post_title LIKE '%" . esc_sql($title_like) . "%'";
                }
            }
            return $where;
        }

        $apsa_posts_query = new WP_Query(array(
            'post_type' => 'any',
            'post_title_like' => $search_query,
            'posts_per_page' => 100
        ));

        // The Loop
        if ($apsa_posts_query->have_posts()) {

            while ($apsa_posts_query->have_posts()) {
                $apsa_posts_query->the_post();
                $title = apsa_qtranslateX_available()?qtranxf_use(qtranxf_getLanguage(), get_the_title()):get_the_title();
                if (get_post_type(get_the_ID()) == "page") {
                    array_push($responce_array, array(
                        "value" => "page%" . get_the_ID() . "%" . $title . "%" . strtolower($apsa_plugin_data['plugin_data']['name']) . "%",
                        "text" => html_entity_decode($title),
                        "type" => 'page'
                    ));
                } else {
                    array_push($responce_array, array(
                        "value" => "post%" . get_the_ID() . "%" . $title . "%" . strtolower($apsa_plugin_data['plugin_data']['name']) . "%",
                        "text" => html_entity_decode($title),
                        "type" => 'post'
                    ));
                }
            }
        }

        // language block
        $enabled_languages = apsa_get_enabled_languages();
        if ($enabled_languages !== false) {
            foreach ($enabled_languages as $code => $name) {
                $pos = mb_strpos(mb_strtolower($name . $apsa_admin_labels["tagsinput_language"]), mb_strtolower($search_query));
                if ($pos !== FALSE) {
                    array_push($responce_array, array(
                        "value" => "language%" . $code . "%" . $name . "%" . strtolower($apsa_plugin_data['plugin_data']['name']) . "%",
                        "text" => html_entity_decode($name),
                        "type" => 'language'
                    ));
                }
            }
        }
        // end language block
        // device block
        $enabled_devices = apsa_get_enabled_devices();
        if ($enabled_devices !== false) {
            foreach ($enabled_devices as $device) {
                $pos = mb_strpos(mb_strtolower($device['name'] . $apsa_admin_labels["tagsinput_device"]), mb_strtolower($search_query));
                if ($pos !== FALSE) {
                    array_push($responce_array, array(
                        "value" => "device%" . $device['code'] . "%" . $device['name'] . "%" . strtolower($apsa_plugin_data['plugin_data']['name']) . "%",
                        "text" => html_entity_decode($device['name']),
                        "type" => 'device'
                    ));
                }
            }
        }
        // end device block

        /* Restore original Post Data */
        wp_reset_postdata();

        echo json_encode($responce_array);
    }

    wp_die();
}

add_action('wp_ajax_apsa_ajax_autocomplete', 'apsa_ajax_autocomplete');

/**
 * Ajax handler for get elements stats
 */
function apsa_ajax_get_elements_stat() {

    $from = $_POST["from"];
    if (empty($from)) {
        $from = FALSE;
    }

    $to = $_POST["to"];
    if (empty($to)) {
        $to = FALSE;
    }

    $camp_id = $_POST["camp_id"];

    $statistics = apsa_get_elements_statistics($camp_id, $from, $to);

    echo json_encode($statistics);

    wp_die();
}

add_action('wp_ajax_apsa_ajax_get_elements_stat', 'apsa_ajax_get_elements_stat');

/**
 * Ajax handler for update all campaigns
 */
function apsa_ajax_update_all_camps() {
    if (!current_user_can('manage_options')) {
        die();
    }

    global $apsa_admin_labels;
    $all_camps = $_POST["all_camps"];

    $success = 1;

    foreach ($all_camps as $campaign_id => $camp) {

        /** Update campaign */
        $campaign_options = $camp['campaign_options'];
        $options = array();

        foreach ($campaign_options as $key => $campaign_option) {
            if ($campaign_option['name'] == "campaign_name") {
                $campaign_name = $campaign_option['value'];
                continue;
            }
            $options[$campaign_option['name']] = $campaign_option['value'];
        }

        /** Update campaign basic data */
        // Check if compaign name set, else set default name
        if (empty($campaign_name)) {
            $campaign_type = apsa_get_campaign_data($campaign_id, "type");

            switch ($campaign_type) {
                case 'background':
                    $campaign_name = $apsa_admin_labels["camp_name_background"];
                    break;
                case 'popup':
                    $campaign_name = $apsa_admin_labels["camp_name_popup"];
                    break;
                case 'embed':
                    $campaign_name = $apsa_admin_labels["camp_name_embed"];
                    break;
                case 'sticky':
                    $campaign_name = $apsa_admin_labels["camp_name_sticky"];
                    break;
                default:
                    $campaign_name = '';
                    break;
            }

            $campaign_date = apsa_get_campaign_data($campaign_id, "creation_date");
            $campaign_date = date_i18n(get_option('date_format'), strtotime($campaign_date));
            $campaign_name .= " - (" . $campaign_date . ")";
        }

        $update_camp = apsa_update_campaign($campaign_id, $campaign_name);
        $update_camp_opt = apsa_update_campaign_options($campaign_id, $options);

        if ($update_camp === FALSE || $update_camp_opt === FALSE) {
            $success = 0;
            break;
        }

        /** Update campaign elements */
        if (isset($camp['campaign_elements'])) {
            $elements = $camp['campaign_elements'];
            $apsa_child_update = apsa_update_child_element_options($elements);
            if (!$apsa_child_update) {
                $success = 0;
                break;
            }
        }
    }

    $response = array('success' => $success);
    echo json_encode($response);

    wp_die();
}

add_action('wp_ajax_apsa_ajax_update_all_camps', 'apsa_ajax_update_all_camps');

/*
 * ajax handler for get extra options
 */

function apsa_get_extra_options() {
    $apsa_extra_options = get_option('apsa_extra_options');
    $apsa_extra_options['apsa_custom_css'] = stripslashes($apsa_extra_options['apsa_custom_css']);

    echo json_encode($apsa_extra_options);
    wp_die();
}

add_action('wp_ajax_apsa_get_extra_options', 'apsa_get_extra_options');
add_action('wp_ajax_nopriv_apsa_get_extra_options', 'apsa_get_extra_options');

// ajax handler for check broken link
function apsa_ajax_check_broken_link() {

    $link = $_POST['link'];
    $version['version'] = '0.0.0';
    if (function_exists('curl_version')) {
        $version = curl_version();
    }

    if (version_compare($version['version'], '7.16.0', '<=')) {
        $httpCode = 200; //Disable broken link detector if curl is old or disabled
    } else {
        $user_agent = (isset($_SERVER['HTTP_USER_AGENT']) && !empty($_SERVER['HTTP_USER_AGENT'])) ? $_SERVER['HTTP_USER_AGENT'] : '';
        $args = array(
            'timeout' => 10,
            'redirection' => 10,
            'httpversion' => '1.0',
            'sslverify' => false,
            'user-agent' => $user_agent
        );
        $response = wp_remote_get(esc_url_raw($link), $args);
        $httpCode = wp_remote_retrieve_response_code($response);
    }

    $res = array(
        'status' => $httpCode
    );
    echo json_encode($res);
    wp_die();
}

add_action('wp_ajax_apsa_ajax_check_broken_link', 'apsa_ajax_check_broken_link');


if ($apsa_plugin_data['visual_composer']['enable'] == 'true') {
    /*
     * add plugin in visual composer
     */
    add_action('vc_before_init', 'apsa_visual_composer');

    function apsa_visual_composer() {
        global $apsa_plugin_data;
        global $apsa_admin_labels;
        vc_map(array(
            "name" => $apsa_plugin_data['plugin_data']['name'],
            "icon" => $apsa_plugin_data['visual_composer']['icon_path'],
            "base" => strtolower($apsa_plugin_data['plugin_data']['name']),
            "description" => $apsa_admin_labels["camp_name_embed"],
            "category" => __('Content', 'js_composer'),
            "as_parent" => array('except' => 'nothing_or_something'),
            "params" => array(
                array(
                    "type" => "dropdown",
                    "holder" => "div",
                    "heading" => $apsa_admin_labels["camp_name_embed"],
                    "param_name" => "id",
                    "value" => apsa_get_embed_compaigns(),
                    "description" => $apsa_admin_labels["select_embed_campaign"]
                ),
                array(
                    "type" => "dropdown",
                    "heading" => $apsa_admin_labels["embed_alignment"],
                    "param_name" => "align",
                    "value" => array(
                        $apsa_admin_labels["alignment_none"] => 'none',
                        $apsa_admin_labels["alignment_left"] => 'left',
                        $apsa_admin_labels["alignment_center"] => 'center',
                        $apsa_admin_labels["alignment_right"] => 'right'
                    ),
                    "description" => $apsa_admin_labels["select_embed_alignment"]
                ),
                array(
                    'type' => 'css_editor',
                    'heading' => __('Css', 'js_composer'),
                    'param_name' => 'css',
                    'group' => __('Design Options', 'js_composer'),
                )
            )
        ));
    }

    function apsa_get_embed_compaigns() {
        $res = array();
        $campaigns = apsa_get_campaigns();
        if (!empty($campaigns)) {
            foreach ($campaigns as $campaign) {
                if ($campaign['type'] == 'embed') {
                    $res[$campaign['name'] . ' (' . intval($campaign['id']) . ')'] = intval($campaign['id']);
                }
            }
        }
        return $res;
    }

}

// unintsall plugin
function apsa_uninstall() {

    function apsa_delete_table($apsa_table_name) {
        global $wpdb;
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}$apsa_table_name");
    }

    /**
     * Delete custom table and options created by plugin
     */
    apsa_merge_config();
    global $apsa_uninstall;

    $apsa_tables = $apsa_uninstall['apsa_tables'];
    $apsa_options = $apsa_uninstall['apsa_options'];
    if (function_exists('is_multisite') && is_multisite()) {
        global $wpdb;
        $old_blog = $wpdb->blogid;
        //Get all blog ids
        $blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
        foreach ($blogids as $blog_id) {
            switch_to_blog($blog_id);
            foreach ($apsa_tables as $apsa_table) {
                apsa_delete_table($apsa_table);
            }
            foreach ($apsa_options as $apsa_option) {
                delete_option($apsa_option);
            }
        }
        switch_to_blog($old_blog);
    } else {
        foreach ($apsa_tables as $apsa_table) {
            apsa_delete_table($apsa_table);
        }
        foreach ($apsa_options as $apsa_option) {
            delete_option($apsa_option);
        }
    }
}

if (is_admin()) {
    register_uninstall_hook($apsa_file_path, 'apsa_uninstall');
}