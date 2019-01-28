<?php

defined('ABSPATH') or die('No script kiddies please!');

/**
 * Db tabels updates write here
 */
if (get_option('sa_db_version') !== FALSE && get_option('sa_db_version') < 1.1) {
    // change tables name
    global $wpdb;
    $wpdb->show_errors();
    $table_name1 = $wpdb->prefix . "sa_campaigns";
    $table_new_name1 = $wpdb->prefix . "apsa_campaigns";
    $table_name2 = $wpdb->prefix . "sa_campaign_options";
    $table_new_name2 = $wpdb->prefix . "apsa_campaign_options";
    $table_name3 = $wpdb->prefix . "sa_ad_statistics";
    $table_new_name3 = $wpdb->prefix . "apsa_element_statistics";
    $table_name4 = $wpdb->prefix . "sa_ads";
    $table_new_name4 = $wpdb->prefix . "apsa_elements";
    $table_name5 = $wpdb->prefix . "sa_ad_options";
    $table_new_name5 = $wpdb->prefix . "apsa_element_options";
    $sql = "DROP TABLE $table_new_name1, $table_new_name2, $table_new_name3, $table_new_name4, $table_new_name5";
    $wpdb->query($sql);
    $sql = "rename table $table_name1 to $table_new_name1, $table_name2 to $table_new_name2, $table_name3 to $table_new_name3, $table_name4 to $table_new_name4, $table_name5 to $table_new_name5;";
    $wpdb->query($sql);
    $sql = "ALTER TABLE " . $wpdb->prefix . "apsa_element_options CHANGE ad_id element_id INT(11);";
    $wpdb->query($sql);
    $sql = "ALTER TABLE " . $wpdb->prefix . "apsa_element_statistics CHANGE ad_id element_id INT(11);";
    $wpdb->query($sql);
    delete_option('sa_db_version');
    update_option('apsa_db_version', 1.1);
}

/* version 1.0 -> version 1.1 -> version 1.2 */
if (get_option('sa_plugin_version') === FALSE && get_option('apsa_plugin_version') === FALSE) {
// migrate custom css
	// for update > 1.2
   if(get_option('sa_extra_options') === FALSE){
	   $temp_options = array(
	   'sa_custom_css' => '',
	   'sa_warning_text' => '',
	   'sa_warning_text_enabled' => 'false',
	   'sa_cache_enabled' => 'false'
	   );
	   update_option('sa_extra_options', $temp_options);
   }
    $sa_custom_css = get_option('sa_custom_css');
    $sa_extra_options = get_option('sa_extra_options');
    if ($sa_custom_css !== FALSE && $sa_extra_options !== FALSE) {
        $sa_extra_options['sa_custom_css'] = stripslashes($sa_custom_css);
        update_option('sa_extra_options', $sa_extra_options);
        delete_option('sa_custom_css');
    }
// migrate embad campaigns height option, set int value
    $apsa_campaigns = apsa_get_campaigns();
    if (!empty($apsa_campaigns)) {
        foreach ($apsa_campaigns as $apsa_campaign) {
            if ($apsa_campaign['type'] == 'embed') {
                $apsa_height_option_name = 'height';
                $height = apsa_get_campaign_options($apsa_campaign['id'], $apsa_height_option_name);
                if ($height != '') {
                    $find = array("px", "%");
                    $replace = array("");
                    $height = str_replace($find, $replace, $height);
                    $apsa_height_option = array($apsa_height_option_name => $height);
                    apsa_update_campaign_options($apsa_campaign['id'], $apsa_height_option);
                }
            }
            /* version 1.1 -> version 1.2 */
            // migrate popup campaigns popup-direction option and gray-background option
            if ($apsa_campaign['type'] == 'popup') {
                $apsa_option_name = 'popup_direction';
                $apsa_popup_direction = apsa_get_campaign_options($apsa_campaign['id'], $apsa_option_name);
                if ($apsa_popup_direction != '') {
                    if ($apsa_popup_direction == 'center')
                        $apsa_popup_direction = 'fade';
                    if ($apsa_popup_direction == 'left')
                        $apsa_popup_direction = 'fadeLeftBig';
                    if ($apsa_popup_direction == 'bottom')
                        $apsa_popup_direction = 'fadeUpBig';
                    if ($apsa_popup_direction == 'top')
                        $apsa_popup_direction = 'fadeDownBig';
                    if ($apsa_popup_direction == 'right')
                        $apsa_popup_direction = 'fadeRightBig';
                    $apsa_option_new_name = 'popup_animation';
                    $apsa_popup_direction_option = array($apsa_option_new_name => $apsa_popup_direction);
                    apsa_update_campaign_options($apsa_campaign['id'], $apsa_popup_direction_option);
                    apsa_delete_campaign_option($apsa_campaign['id'], $apsa_option_name);
                }
                $apsa_option_name = 'gray_background';
                $apsa_popup_gray_background = apsa_get_campaign_options($apsa_campaign['id'], $apsa_option_name);
                if ($apsa_popup_gray_background == 'on')
                    $apsa_popup_gray_background = 'gray';
                else
                    $apsa_popup_gray_background = 'none';
                $apsa_option_new_name = 'overlay_pattern';
                $apsa_popup_overlay_pattern_option = array($apsa_option_new_name => $apsa_popup_gray_background);
                apsa_update_campaign_options($apsa_campaign['id'], $apsa_popup_overlay_pattern_option);
                apsa_delete_campaign_option($apsa_campaign['id'], $apsa_option_name);
            }
            /* version 1.1 -> version 1.2 */
        }
    }
    update_option('sa_plugin_version', '1.2');
}

// Get current plugin version

$sa_plugin_version = get_option('sa_plugin_version');
$current_plugin_version = $sa_plugin_version ? $sa_plugin_version : get_option('apsa_plugin_version');

if ($current_plugin_version < 1.4) {
    /* version 1.2 -> version 1.4 */
    $apsa_sa_extra_options = get_option('sa_extra_options');
    $apsa_extra_options = get_option('apsa_extra_options');
    $apsa_option = get_option('apsa_options');
    if ($apsa_sa_extra_options !== FALSE && $apsa_extra_options !== FALSE && $apsa_option !== FALSE) {
        $apsa_extra_options['apsa_custom_css'] = stripslashes($apsa_sa_extra_options['sa_custom_css']);
        $apsa_extra_options['apsa_cache_enabled'] = $apsa_sa_extra_options['sa_cache_enabled'];
        $apsa_option['apsa_warning_text'] = stripslashes($apsa_sa_extra_options['sa_warning_text']);
        $apsa_option['apsa_warning_text_enabled'] = $apsa_sa_extra_options['sa_warning_text_enabled'];
        update_option('apsa_extra_options', $apsa_extra_options);
        update_option('apsa_options', $apsa_option);
        delete_option('sa_extra_options');
    }
    update_option('apsa_plugin_version', 1.4);
    delete_option('sa_plugin_version');

    /* changed widget name */
    $sidebar_widget_option = get_option('sidebars_widgets');
    if (!empty($sidebar_widget_option)) {
        foreach ($sidebar_widget_option as $sidebar => $options) {
            if (!empty($options) && is_array($options)) {
                foreach ($options as $key => $value) {
                    if (strpos($value, 'sa_campaign') !== false) {
                        $sidebar_widget_option[$sidebar][$key] = str_replace('sa_campaign', 'apsa_campaign', $sidebar_widget_option[$sidebar][$key]);
                    }
                }
            }
        }
        update_option('sidebars_widgets', $sidebar_widget_option);
    }
    $apsa_widget_option = get_option('widget_sa_campaign');
    if ($apsa_widget_option !== FALSE) {
        update_option('widget_apsa_campaign', $apsa_widget_option);
        delete_option('widget_sa_campaign');
    }

    /* campaign options part */
    $apsa_campaigns = apsa_get_campaigns();
    if (!empty($apsa_campaigns)) {
        foreach ($apsa_campaigns as $apsa_campaign) {
            $apsa_camp_elements = apsa_get_campaign_elements($apsa_campaign['id']);
            if ($apsa_campaign['type'] == 'popup') {
                $apsa_hide_after = apsa_get_campaign_options($apsa_campaign['id'], 'hide_ad_after');
                $apsa_hide_after_new = array('hide_element_after' => $apsa_hide_after);
                apsa_update_campaign_options($apsa_campaign['id'], $apsa_hide_after_new);
                $apsa_bg_img_type = apsa_get_campaign_options($apsa_campaign['id'], 'background_type');
                $apsa_autoplay_video = apsa_get_campaign_options($apsa_campaign['id'], 'auto_play_video');
                $apsa_options = array();
                if (!empty($apsa_camp_elements)) {
                    foreach ($apsa_camp_elements as $apsa_camp_element) {
                        $apsa_element_content = apsa_get_element_options($apsa_camp_element['id'], 'ad_content');
                        $apsa_element_include = apsa_get_element_options($apsa_camp_element['id'], 'ad_include_tags');
                        $apsa_element_exclude = apsa_get_element_options($apsa_camp_element['id'], 'ad_exclude_tags');
                        $apsa_options['element_content'] = stripslashes($apsa_element_content);
                        $apsa_options['element_include_tags'] = $apsa_element_include;
                        $apsa_options['element_exclude_tags'] = $apsa_element_exclude;
                        if ($apsa_camp_element['type'] == 'image') {
                            $apsa_options['background_type'] = $apsa_bg_img_type;
                        }
                        if ($apsa_camp_element['type'] == 'video') {
                            $apsa_options['auto_play_video'] = $apsa_autoplay_video;
                        }
                        if ($apsa_camp_element['type'] == 'html') {
                            apsa_update_element($apsa_camp_element['id'], $apsa_camp_element['title'], 'code', $apsa_camp_element['priority'], $apsa_camp_element['status']);
                        }
                        apsa_update_element_options($apsa_camp_element['id'], $apsa_options);
                        apsa_delete_element_option($apsa_camp_element['id'], 'ad_content');
                        apsa_delete_element_option($apsa_camp_element['id'], 'ad_include_tags');
                        apsa_delete_element_option($apsa_camp_element['id'], 'ad_exclude_tags');
                    }
                }
                apsa_delete_campaign_option($apsa_campaign['id'], 'background_type');
                apsa_delete_campaign_option($apsa_campaign['id'], 'auto_play_video');
                apsa_delete_campaign_option($apsa_campaign['id'], 'hide_ad_after');
            }
            if ($apsa_campaign['type'] == 'embed') {
                $apsa_bg_img_type = apsa_get_campaign_options($apsa_campaign['id'], 'background_type');
                $apsa_autoplay_video = apsa_get_campaign_options($apsa_campaign['id'], 'auto_play_video');
                $apsa_options = array();
                if (!empty($apsa_camp_elements)) {
                    foreach ($apsa_camp_elements as $apsa_camp_element) {
                        $apsa_element_content = apsa_get_element_options($apsa_camp_element['id'], 'ad_content');
                        $apsa_element_include = apsa_get_element_options($apsa_camp_element['id'], 'ad_include_tags');
                        $apsa_element_exclude = apsa_get_element_options($apsa_camp_element['id'], 'ad_exclude_tags');
                        $apsa_options['element_content'] = stripslashes($apsa_element_content);
                        $apsa_options['element_include_tags'] = $apsa_element_include;
                        $apsa_options['element_exclude_tags'] = $apsa_element_exclude;
                        if ($apsa_camp_element['type'] == 'image') {
                            $apsa_options['background_type'] = $apsa_bg_img_type;
                        }
                        if ($apsa_camp_element['type'] == 'video') {
                            $apsa_options['auto_play_video'] = $apsa_autoplay_video;
                        }
                        if ($apsa_camp_element['type'] == 'html') {
                            apsa_update_element($apsa_camp_element['id'], $apsa_camp_element['title'], 'code', $apsa_camp_element['priority'], $apsa_camp_element['status']);
                        }
                        apsa_update_element_options($apsa_camp_element['id'], $apsa_options);
                        apsa_delete_element_option($apsa_camp_element['id'], 'ad_content');
                        apsa_delete_element_option($apsa_camp_element['id'], 'ad_include_tags');
                        apsa_delete_element_option($apsa_camp_element['id'], 'ad_exclude_tags');
                    }
                }
                apsa_delete_campaign_option($apsa_campaign['id'], 'background_type');
                apsa_delete_campaign_option($apsa_campaign['id'], 'auto_play_video');
            }
            if ($apsa_campaign['type'] == 'background') {
                $apsa_bg_img_type = apsa_get_campaign_options($apsa_campaign['id'], 'background_type');
                $apsa_options = array();
                $apsa_options['background_type'] = $apsa_bg_img_type;
                if (!empty($apsa_camp_elements)) {
                    foreach ($apsa_camp_elements as $apsa_camp_element) {
                        $apsa_element_content = apsa_get_element_options($apsa_camp_element['id'], 'ad_content');
                        $apsa_element_include = apsa_get_element_options($apsa_camp_element['id'], 'ad_include_tags');
                        $apsa_element_exclude = apsa_get_element_options($apsa_camp_element['id'], 'ad_exclude_tags');
                        $apsa_options['element_content'] = stripslashes($apsa_element_content);
                        $apsa_options['element_include_tags'] = $apsa_element_include;
                        $apsa_options['element_exclude_tags'] = $apsa_element_exclude;
                        apsa_update_element_options($apsa_camp_element['id'], $apsa_options);
                        apsa_delete_element_option($apsa_camp_element['id'], 'ad_content');
                        apsa_delete_element_option($apsa_camp_element['id'], 'ad_include_tags');
                        apsa_delete_element_option($apsa_camp_element['id'], 'ad_exclude_tags');
                    }
                }
                apsa_delete_campaign_option($apsa_campaign['id'], 'background_type');
            }
        }
    }
    update_option('apsa_plugin_version', '1.4');
}



if (get_option('apsa_db_version') < 1.2) {
    
}