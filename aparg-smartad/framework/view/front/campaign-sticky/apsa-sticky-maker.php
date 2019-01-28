<?php
defined('ABSPATH') or die('No script kiddies please!');

function apsa_sticky_before_page_template_load($cache = null, $apsa_page_info = null) {
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
    $camps = apsa_get_active_campaigns("sticky");
    $campaigns = empty($camps[0])?array( 0 => $camps):$camps;
    global $apsa_stickys_data;
    $apsa_stickys_data = array();
    foreach ($campaigns as $campaign) {
        if (empty($campaign)) {
            continue;
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
            continue;
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
        if (empty($camp_options['position'])) {
            $camp_options['position'] = 'bottom-right';
        }

        if (empty($camp_options['change_interval'])) {
            $camp_options['change_interval'] = -1;
        }

        if (empty($camp_options['view_interval'])) {
            $camp_options['view_interval'] = -1;
        }

        if (empty($camp_options['width'])) {
            $camp_options['width'] = "300px";
        }

        if (empty($camp_options['height'])) {
            $camp_options['height'] = "200px";
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

        /**
         * call smart filter
         */
        $campaign_elements = apsa_smart_filter($campaign, $camp_options, $apsa_page_info, $campaign_elements, $elements_options, $elements_stat);
        // Check whether campaign has enable elements
        if (empty($campaign_elements)) {
            continue;
        }
        // Get cookie for this campaign elements
        $apsa_stickys_info = isset($_COOKIE['apsa_stickys_info']) ? json_decode(stripslashes($_COOKIE['apsa_stickys_info']), TRUE) : '';
        $apsa_sticky_info = isset($apsa_stickys_info[$campaign['id']]) ? $apsa_stickys_info[$campaign['id']] : array();
        // Check if cookie is set for this campaign elements
        if (!empty($apsa_sticky_info) && $apsa_sticky_info["campaign_id"] == $campaign['id']) {
            $lasts_priority = $apsa_sticky_info["last"];
            $last_id = isset($apsa_sticky_info["last_id"]) ? $apsa_sticky_info["last_id"] : FALSE;

            $last_element = isset($campaign_elements[$last_id]) ? $campaign_elements[$last_id] : FALSE;

            if (isset($_COOKIE['apsa_sticky_view_interval_'.$campaign['id']]) && $camp_options['view_interval'] !== -1 && !empty($last_element) && in_array($last_element, $campaign_elements)) {
                $sticky_element = FALSE;
            } else if (isset($_COOKIE['apsa_sticky_change_interval_'.$campaign['id']]) && $camp_options['change_interval'] !== -1 && !empty($last_element) && in_array($last_element, $campaign_elements)) {
                $sticky_element = $last_element;

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

                    $sticky_element = $elements_by_priority[$lasts_priority];
                    if (!empty($sticky_element) && in_array($sticky_element, $campaign_elements)) {
                        $element_choosen = TRUE;
                    }
                }
            }
        } else {
            // Get first element(enable) by priority and display
            $elements_arr_vals = array_values($campaign_elements);

            $sticky_element = $elements_arr_vals[0];
        }

        // Check if element exists
        if (empty($sticky_element)) {
            continue;
        }
        /** Determine sticky content type and make html content for display into sticky */
        $sticky_element_html = "";

        // get child element options
        $apsa_get_child_sticky_options = array();
        if (function_exists('apsa_get_child_sticky_options')) {
            $apsa_get_child_sticky_options = apsa_get_child_sticky_options($elements_options[$sticky_element['id']], $sticky_element, $camp_options);
        }


        /** Set options array for js */
        global $apsa_effects;
        $apsa_sticky_effects = $apsa_effects['sticky'];

        $apsa_sticky_options["sticky_change_interval"] = $camp_options['change_interval'];
        $apsa_sticky_options["sticky_view_interval"] = $camp_options['view_interval'];
        $apsa_sticky_options["sticky_sticky_direction"] = $camp_options['position'];
        $apsa_sticky_options["sticky_sticky_direction_in"] = $apsa_sticky_effects[$camp_options['sticky_animation']][0];
        $apsa_sticky_options["sticky_sticky_direction_out"] = $apsa_sticky_effects[$camp_options['sticky_animation']][1];
        $apsa_sticky_options["sticky_sticky_correction_out"] = isset($apsa_sticky_effects[$camp_options['sticky_animation']][2]) ? $apsa_sticky_effects[$camp_options['sticky_animation']][2] : 0;
        $apsa_sticky_options["sticky_view_after"] = $camp_options['view_after'];
        $apsa_sticky_options["sticky_show_close_after"] = $camp_options['show_close_after'];
        $apsa_sticky_options["sticky_hide_element_after"] = $camp_options['hide_element_after'];
        $apsa_sticky_options["sticky_put_in_frame"] = $camp_options['put_in_frame'];
        $apsa_sticky_options["sticky_frame_color"] = $camp_options['frame_color'];
        $apsa_sticky_options["sticky_width"] = $camp_options['width'];
        $apsa_sticky_options["sticky_height"] = $camp_options['height'];
        $apsa_sticky_options["sticky_element_id"] = $sticky_element["id"];

        $apsa_sticky_options = array_merge($apsa_sticky_options, $apsa_get_child_sticky_options);

        $apsa_sticky_info["campaign_id"] = $sticky_element["campaign_id"];
        $apsa_sticky_info["last"] = $sticky_element["priority"];
        $apsa_sticky_info["last_id"] = $sticky_element["id"];

        global $apsa_sticky_data;
        $apsa_sticky_data = array();

        $apsa_sticky_data['apsa_sticky_options'] = $apsa_sticky_options;
        $apsa_sticky_data['apsa_sticky_info'] = $apsa_sticky_info;
        
        array_push($apsa_stickys_data, $apsa_sticky_data);
    }
        if ($cache == 'true') {
            return $apsa_stickys_data;
        }
        ?>        
        <script type="text/javascript">
            apsa_stickys_data = <?php echo json_encode($apsa_stickys_data); ?>;
        </script>
        <?php
}

$apsa_extra_options = get_option('apsa_extra_options');

/**
 * when anti-cache enabled ajax handler for get sticky campain options
 */
if (isset($apsa_extra_options['apsa_cache_enabled']) && $apsa_extra_options['apsa_cache_enabled'] == 'true') {
    add_action('wp_ajax_apsa_call_sticky_before_page_template_load', 'apsa_call_sticky_before_page_template_load');
    add_action('wp_ajax_nopriv_apsa_call_sticky_before_page_template_load', 'apsa_call_sticky_before_page_template_load');
} else {
    add_action('wp_head', 'apsa_sticky_before_page_template_load');
    add_action('wp_footer', 'apsa_sticky_load_sticky_in_footer');
}

function apsa_sticky_load_sticky_in_footer() {
    global $apsa_stickys_data;
    foreach ($apsa_stickys_data as $apsa_sticky_data) {
    $apsa_sticky_options = $apsa_sticky_data['apsa_sticky_options'];
    $apsa_sticky_info = $apsa_sticky_data['apsa_sticky_info'];
    ?>
    <div id="apsa-sticky-cont-<?php echo $apsa_sticky_info['campaign_id'];?>" style="width: <?php $apsa_sticky_options['sticky_width'] ?>; height: <?php $apsa_sticky_options['sticky_height'] ?>"
         class="apsa-sticky-cont <?php echo (($apsa_sticky_options["sticky_put_in_frame"] == "on") ? ' apsa-sticky-border ' : ""); ?> <?php echo "apsa-".str_replace('_', '-', strtolower($apsa_sticky_options['sticky_sticky_direction']));?>
         apsa-suspense-hidden apsa-reset-start <?php echo "apsa-sticky-" . $apsa_sticky_options['sticky_element_type'] ?>
         <?php echo $apsa_sticky_options['sticky_sticky_direction_in'] ?>"
         data-apsa-in='<?php echo $apsa_sticky_options['sticky_sticky_direction_in'] ?>'
         data-apsa-out='<?php echo $apsa_sticky_options['sticky_sticky_direction_out'] ?>'
         data-apsa-correction-out='<?php echo $apsa_sticky_options['sticky_sticky_correction_out'] ?>'
         data-apsa-element-id='<?php echo $apsa_sticky_options['sticky_element_id'] ?>'
         data-apsa-campaign-id='<?php echo $apsa_sticky_info['campaign_id'] ?>'>
        <div id="apsa-sticky-header-<?php echo $apsa_sticky_info['campaign_id'];?>" class="apsa-sticky-header">
            <span class="apsa-close-sticky apsa-sticky-hidden-close" style="color: <?php echo $apsa_sticky_options["sticky_frame_color"] ?>"></span>
            <div id="apsa-hide-text-<?php echo $apsa_sticky_info['campaign_id'];?>" class="apsa-hide-text apsa-hide-text-hidden"><span class="apsa-sticky-hide-settlement" style="color: <?php echo $apsa_sticky_options["sticky_frame_color"] ?>;"><?php echo (($apsa_sticky_options["sticky_hide_element_after"] !== "-1") ? $apsa_sticky_options["sticky_hide_element_after"] : "") ?></span></div>
            <div id="apsa-close-text-<?php echo $apsa_sticky_info['campaign_id'];?>" class="apsa-close-text"><span class="apsa-sticky-close-settlement" style="color: <?php echo $apsa_sticky_options["sticky_frame_color"] ?>;"><?php echo (($apsa_sticky_options["sticky_show_close_after"] !== "0") ? $apsa_sticky_options["sticky_show_close_after"] : "") ?></span></div>
        </div>
        <div id="apsa-sticky-element-<?php echo $apsa_sticky_info['campaign_id'];?>" class="apsa-sticky-element" <?php echo (($apsa_sticky_options["sticky_put_in_frame"] == "on") ? ' style="background-color: ' . $apsa_sticky_options["sticky_frame_color"] . '; border-color: ' . $apsa_sticky_options["sticky_frame_color"] . '"' : "") ?>><?php echo $apsa_sticky_options['sticky_element_html'] ?></div>
    </div>
    <?php
    }
}

function apsa_call_sticky_before_page_template_load() {
    $apsa_page_info = $_POST['apsa_page_info'];

    $response = apsa_sticky_before_page_template_load('true', $apsa_page_info);
    echo json_encode($response);
    die();
}
