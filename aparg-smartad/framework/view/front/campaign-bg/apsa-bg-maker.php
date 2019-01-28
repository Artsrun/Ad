<?php
defined('ABSPATH') or die('No script kiddies please!');

function apsa_bg_before_page_template_load($cache = null, $apsa_page_info = null) {    
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
    $campaign = apsa_get_active_campaigns("background");

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
    if (empty($camp_options["change_interval"])) {
        $camp_options["change_interval"] = -1;
    }

    if (empty($camp_options["background_selector"])) {
        $camp_options["background_selector"] = "body";
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
    $apsa_bg_info = isset($_COOKIE['apsa_bg_info']) ? $_COOKIE['apsa_bg_info'] : '';
    $apsa_bg_info = json_decode(stripslashes($apsa_bg_info), TRUE);

    // Check if cookie is set for this campaign elements
    if (!empty($apsa_bg_info) && $apsa_bg_info["campaign_id"] == $campaign['id']) {

        $lasts_priority = $apsa_bg_info["last"];
        $last_id = isset($apsa_bg_info["last_id"]) ? $apsa_bg_info["last_id"] : FALSE;

        /** check if interval is valid display last else second by priority */
        $last_element = isset($campaign_elements[$last_id]) ? $campaign_elements[$last_id] : FALSE;

        if (isset($_COOKIE['apsa_bg_change_interval']) && $camp_options['change_interval'] !== -1 && !empty($last_element) && in_array($last_element, $campaign_elements)) {
            $bg_element = $last_element;

            $camp_options["change_interval"] = "during";
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

                $bg_element = $elements_by_priority[$lasts_priority];

                if (!empty($bg_element) && in_array($bg_element, $campaign_elements)) {
                    $element_choosen = TRUE;
                }
            }
        }
    } else {
        // Get first element(enable) by priority and display        
        $elements_arr_vals = array_values($campaign_elements);

        $bg_element = $elements_arr_vals[0];
    }

    // Check if element exists
    if (empty($bg_element)) {
        return;
    }

    /** Set options array for js */
    $apsa_bg_options["bg_selector"] = $camp_options["background_selector"];
    $apsa_bg_options["bg_change_interval"] = $camp_options["change_interval"];
    $apsa_bg_options["bg_element_id"] = $bg_element["id"];
    $apsa_bg_options["bg_element_type"] = $bg_element["type"];

    // get child options
    $apsa_get_child_bg_options = array();
    if (function_exists('apsa_get_child_bg_options'))
        $apsa_get_child_bg_options = apsa_get_child_bg_options($elements_options[$bg_element['id']], $bg_element, $camp_options);

    $apsa_bg_options = array_merge($apsa_bg_options, $apsa_get_child_bg_options);
    $apsa_bg_options['bg_element_style'] = $apsa_bg_options["bg_selector"] . $apsa_bg_options['bg_element_style'];

    $apsa_bg_info["campaign_id"] = $bg_element["campaign_id"];
    $apsa_bg_info["last"] = $bg_element["priority"];
    $apsa_bg_info["last_id"] = $bg_element["id"];

    if ($cache == 'true') {
        $res['apsa_bg_options'] = $apsa_bg_options;
        $res['apsa_bg_info'] = $apsa_bg_info;
        return $res;
    }

    $apsa_bg_inline_style = '<style type="text/css">' . $apsa_bg_options['bg_element_style'] . '</style>';
    echo $apsa_bg_inline_style;
    ?>
    <script type="text/javascript">
        apsa_bg_options = <?php echo json_encode($apsa_bg_options); ?>;
        apsa_bg_info = <?php echo json_encode($apsa_bg_info); ?>;
    </script>
    <?php
}

$apsa_extra_options = get_option('apsa_extra_options');

/**
 * when anti-cache enabled ajax handler for get background campain options
 */
if (isset($apsa_extra_options['apsa_cache_enabled']) && $apsa_extra_options['apsa_cache_enabled'] == 'true') {
    add_action('wp_ajax_apsa_call_bg_before_page_template_load', 'apsa_call_bg_before_page_template_load');
    add_action('wp_ajax_nopriv_apsa_call_bg_before_page_template_load', 'apsa_call_bg_before_page_template_load');
} else {
    add_action('wp_head', 'apsa_bg_before_page_template_load');
}

function apsa_call_bg_before_page_template_load() {
    $apsa_page_info = $_POST['apsa_page_info'];

    $response = apsa_bg_before_page_template_load('true', $apsa_page_info);
    echo json_encode($response);
    die();
}
