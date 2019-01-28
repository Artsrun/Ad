<?php
defined('ABSPATH') or die('No script kiddies please!');

function apsa_pop_before_page_template_load($cache = null, $apsa_page_info = null) {

    global $apsa_plugin_data;

    if (empty($apsa_page_info)) {
        $apsa_page_info['apsa_is_page'] = is_page();
        $apsa_page_info['apsa_is_single'] = is_single();
        $apsa_page_info['apsa_is_archive'] = is_archive();
        $get_queried_object = get_queried_object();
        if (!empty($get_queried_object))
            $apsa_page_info['apsa_get_queried_object'] = get_object_vars($get_queried_object);
        else
            $apsa_page_info['apsa_get_queried_object'] = array();
        $apsa_page_info['apsa_get_the_ID'] = get_the_ID();
        $apsa_page_info['apsa_get_taxonomies'] = get_taxonomies();
    }

    // Get active campaign
    $campaign = apsa_get_active_campaigns("popup");

    if (empty($campaign)) {
        return;
    }

    // Get campaign elements    
    $get_campaign_elements = apsa_get_campaign_elements($campaign['id'], "priority", "ASC", TRUE);

    $campaign_elements = array();
    $elements_by_priority = array();

    if (!empty($get_campaign_elements)) {
        foreach ($get_campaign_elements as $element_data) {
            $campaign_elements[$element_data['id']] = $element_data;
            $elements_by_priority[$element_data['priority']] = $element_data;
        }
    }

    $element_ids = array_keys($campaign_elements);

    // Check whether campaign exists
    if (empty($campaign) || empty($campaign_elements)) {
        return;
    }

    // Get campaign options
    $get_camp_options = apsa_get_campaign_options($campaign['id']);
    $camp_options = array();

    if (!empty($get_camp_options)) {
        foreach ($get_camp_options as $camp_option) {
            $camp_options[$camp_option['option_name']] = $camp_option['option_value'];
        }
    }

    // Get campaign elements options
    $get_elements_options = apsa_get_all_element_options($element_ids);
    $elements_options = array();

    if (!empty($get_elements_options)) {
        foreach ($get_elements_options as $element_option) {
            $elements_options[$element_option['element_id']][$element_option['option_name']] = $element_option['option_value'];
        }
    }

    // Get campaign elements statistics 
    $get_elements_stat = apsa_get_element_stat_counts($element_ids);
    $elements_stat = array();
    if (!empty($get_elements_stat)) {
        foreach ($get_elements_stat as $get_element_stat) {
            $elements_stat[$get_element_stat['element_id']][$get_element_stat['type']] = $get_element_stat['total_count'];
        }
    }

    /** Check some options which must have a value */
    if (empty($camp_options['change_interval'])) {
        $camp_options['change_interval'] = -1;
    }

    if (empty($camp_options['view_interval'])) {
        $camp_options['view_interval'] = -1;
    }

    if (empty($camp_options['width'])) {
        $camp_options['width'] = "600px";
    }

    if (empty($camp_options['height'])) {
        $camp_options['height'] = "500px";
    }

    if (empty($camp_options['view_after'])) {
        $camp_options['view_after'] = "0";
    }

    if (empty($camp_options['show_close_after'])) {
        $camp_options['show_close_after'] = "0";
    }

    if (empty($camp_options['hide_element_after'])) {
        $camp_options['hide_element_after'] = "-1";
    }

    if (empty($camp_options['frame_color'])) {
        $camp_options['frame_color'] = "#ffffff";
    }

    if (empty($camp_options['overlay_pattern'])) {
        $camp_options['overlay_pattern'] = "none";
    }

    /**
     * call smart filter
     */
    $campaign_elements = apsa_smart_filter($campaign, $camp_options, $apsa_page_info, $campaign_elements, $elements_options, $elements_stat);

    // Check whether campaign has enable elements
    if (empty($campaign_elements)) {
        return;
    }

    // Get cookie for this campaign elements
    $apsa_popup_info = isset($_COOKIE['apsa_popup_info']) ? $_COOKIE['apsa_popup_info'] : '';
    $apsa_popup_info = json_decode(stripslashes($apsa_popup_info), TRUE);

    // Check if cookie is set for this campaign elements
    if (!empty($apsa_popup_info) && $apsa_popup_info["campaign_id"] == $campaign['id']) {

        $lasts_priority = $apsa_popup_info["last"];
        $last_id = isset($apsa_popup_info["last_id"]) ? $apsa_popup_info["last_id"] : FALSE;

        $last_element = isset($campaign_elements[$last_id]) ? $campaign_elements[$last_id] : FALSE;

        if (isset($_COOKIE['apsa_pop_view_interval']) && $camp_options['view_interval'] !== -1 && !empty($last_element) && in_array($last_element, $campaign_elements)) {
            $pop_element = FALSE;
        } else if (isset($_COOKIE['apsa_pop_change_interval']) && $camp_options['change_interval'] !== -1 && !empty($last_element) && in_array($last_element, $campaign_elements)) {
            $pop_element = $last_element;

            $camp_options['change_interval'] = "during";
        } else {
            $elements_arr_vals = array_values($campaign_elements);

            $first_priority = $elements_arr_vals[0]["priority"];
            $last_priority = $elements_arr_vals[count($campaign_elements) - 1]["priority"];

            $element_choosen = FALSE;
            while (empty($element_choosen)) {
                $lasts_priority++;
                if ($lasts_priority > $last_priority) {
                    $lasts_priority = $first_priority;
                }

                $pop_element = $elements_by_priority[$lasts_priority];
                if (!empty($pop_element) && in_array($pop_element, $campaign_elements)) {
                    $element_choosen = TRUE;
                }
            }
        }
    } else {
        // Get first element(enable) by priority and display
        $elements_arr_vals = array_values($campaign_elements);

        $pop_element = $elements_arr_vals[0];
    }

    // Check if element exists
    if (empty($pop_element)) {
        return;
    }

    /** Determine popup content type and make html content for display into popup */
    $pop_element_html = "";

    // get child element options
    $apsa_get_child_popup_options = array();
    if (function_exists('apsa_get_child_popup_options')) {
        $apsa_get_child_popup_options = apsa_get_child_popup_options($elements_options[$pop_element['id']], $pop_element, $camp_options);
    }


    /** Set options array for js */
    global $apsa_effects;
    $apsa_popup_effects = $apsa_effects['popup'];
    $apsa_overlay_patterns = $apsa_effects['patterns'];

    $apsa_pop_options["pop_change_interval"] = $camp_options['change_interval'];
    $apsa_pop_options["pop_view_interval"] = $camp_options['view_interval'];
    if ($camp_options['overlay_pattern'] == 'gray' || $camp_options['overlay_pattern'] == 'none') {
        $apsa_pop_options["pop_overlay_pattern"] = $camp_options['overlay_pattern'];
    } else {
        $apsa_pop_options["pop_overlay_pattern"] = $apsa_overlay_patterns[$camp_options['overlay_pattern']];
    }
    $apsa_pop_options["pop_popup_direction"] = 'center';
    $apsa_pop_options["pop_popup_direction_in"] = $apsa_popup_effects[$camp_options['popup_animation']][0];
    $apsa_pop_options["pop_popup_direction_out"] = $apsa_popup_effects[$camp_options['popup_animation']][1];
    $apsa_pop_options["pop_popup_correction_out"] = isset($apsa_popup_effects[$camp_options['popup_animation']][2]) ? $apsa_popup_effects[$camp_options['popup_animation']][2] : 0;
    $apsa_pop_options["pop_view_after"] = $camp_options['view_after'];
    $apsa_pop_options["pop_show_close_after"] = $camp_options['show_close_after'];
    $apsa_pop_options["pop_hide_element_after"] = $camp_options['hide_element_after'];
    $apsa_pop_options["pop_put_in_frame"] = $camp_options['put_in_frame'];
    $apsa_pop_options["pop_frame_color"] = $camp_options['frame_color'];
    $apsa_pop_options["pop_width"] = $camp_options['width'];
    $apsa_pop_options["pop_height"] = $camp_options['height'];
    $apsa_pop_options["pop_element_id"] = $pop_element["id"];

    $apsa_pop_options = array_merge($apsa_pop_options, $apsa_get_child_popup_options);

    $apsa_popup_info["campaign_id"] = $pop_element["campaign_id"];
    $apsa_popup_info["last"] = $pop_element["priority"];
    $apsa_popup_info["last_id"] = $pop_element["id"];

    global $apsa_popup_data;
    $apsa_popup_data = array();

    $apsa_popup_data['apsa_pop_options'] = $apsa_pop_options;
    $apsa_popup_data['apsa_popup_info'] = $apsa_popup_info;

    if ($cache == 'true') {
        return $apsa_popup_data;
    }
    ?>        
    <script type="text/javascript">
        apsa_pop_options = <?php echo json_encode($apsa_pop_options); ?>;
        apsa_popup_info = <?php echo json_encode($apsa_popup_info); ?>;
    </script>
    <?php
}

$apsa_extra_options = get_option('apsa_extra_options');

/**
 * when anti-cache enabled ajax handler for get popup campain options
 */
if (isset($apsa_extra_options['apsa_cache_enabled']) && $apsa_extra_options['apsa_cache_enabled'] == 'true') {
    add_action('wp_ajax_apsa_call_pop_before_page_template_load', 'apsa_call_pop_before_page_template_load');
    add_action('wp_ajax_nopriv_apsa_call_pop_before_page_template_load', 'apsa_call_pop_before_page_template_load');
} else {
    add_action('wp_head', 'apsa_pop_before_page_template_load');
    add_action('wp_footer', 'apsa_pop_load_popup_in_footer');
}

function apsa_pop_load_popup_in_footer() {
    global $apsa_popup_data;

    $apsa_pop_options = $apsa_popup_data['apsa_pop_options'];
    $apsa_popup_info = $apsa_popup_data['apsa_popup_info'];
    ?>
    <div id="apsa-popup-cont" style="width: <?php $apsa_pop_options['pop_width'] ?>; height: <?php $apsa_pop_options['pop_height'] ?>"
         class="<?php echo (($apsa_pop_options["pop_put_in_frame"] == "on") ? ' apsa-popup-border ' : ""); ?> <?php echo "apsa-pop-" . $apsa_pop_options['pop_popup_direction'] ?>
         apsa-suspense-hidden apsa-reset-start <?php echo "apsa-popup-" . $apsa_pop_options['pop_element_type'] ?>
         <?php echo $apsa_pop_options['pop_popup_direction_in'] ?>"
         data-apsa-in='<?php echo $apsa_pop_options['pop_popup_direction_in'] ?>'
         data-apsa-out='<?php echo $apsa_pop_options['pop_popup_direction_out'] ?>'
         data-apsa-correction-out='<?php echo $apsa_pop_options['pop_popup_correction_out'] ?>'
         data-apsa-element-id='<?php echo $apsa_pop_options['pop_element_id'] ?>'
         data-apsa-campaign-id='<?php echo $apsa_popup_info['campaign_id'] ?>'>
        <div id="apsa-popup-header">
            <span class="apsa-close-popup apsa-pop-hidden-close" style="color: <?php echo $apsa_pop_options["pop_frame_color"] ?>"></span>
            <div id="apsa-hide-text" class="apsa-hide-text-hidden"><span class="apsa-hide-settlement" style="color: <?php echo $apsa_pop_options["pop_frame_color"] ?>;"><?php echo (($apsa_pop_options["pop_hide_element_after"] !== "-1") ? $apsa_pop_options["pop_hide_element_after"] : "") ?></span></div>
            <div id="apsa-close-text"><span class="apsa-close-settlement" style="color: <?php echo $apsa_pop_options["pop_frame_color"] ?>;"><?php echo (($apsa_pop_options["pop_show_close_after"] !== "0") ? $apsa_pop_options["pop_show_close_after"] : "") ?></span></div>
        </div>
        <div id="apsa-popup-element" <?php echo (($apsa_pop_options["pop_put_in_frame"] == "on") ? ' style="background-color: ' . $apsa_pop_options["pop_frame_color"] . '; border-color: ' . $apsa_pop_options["pop_frame_color"] . '"' : "") ?>><?php echo $apsa_pop_options['pop_element_html'] ?></div>
    </div>
    <?php
}

function apsa_call_pop_before_page_template_load() {
    $apsa_page_info = $_POST['apsa_page_info'];

    $response = apsa_pop_before_page_template_load('true', $apsa_page_info);
    echo json_encode($response);
    die();
}
