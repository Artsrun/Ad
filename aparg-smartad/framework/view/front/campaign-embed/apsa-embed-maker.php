<?php

defined('ABSPATH') or die('No script kiddies please!');
/**
 * Create shortcode for displaying plugin embed campaign
 */

/** [shortcode id="campaign-id"] */
function apsa_embed_campaign_func($atts, $content = NULL, $name, $apsa_page_info = NULL, $apsa_anticache = FALSE) {

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

    $temp = shortcode_atts(array('id' => '0', 'widget' => 0, 'align' => 'none', 'vc_class' => ''), $atts);

    $shortcode_name = strtolower($apsa_plugin_data['plugin_data']['name']);
    $css = '';
    extract(shortcode_atts(array('css' => ''), $atts));
    $apsa_vs_css_class = (apsa_vc_available()) ? apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($css, ' '), $shortcode_name, $atts) : false;
    $apsa_vs_css_class = $apsa_vs_css_class ? $apsa_vs_css_class : $temp['vc_class'];

    $campaign_id = intval($temp['id']);

    $apsa_alignment = $temp['align'];

    $post_id = $apsa_page_info['apsa_get_the_ID'];

    $campaign = apsa_get_campaigns($campaign_id);

    if (empty($campaign)) {
        if (isset($content)) {
            return $content;
        } else {
            return;
        }
    }

    if ($campaign[0]['type'] != 'embed') {
        if (isset($content)) {
            return $content;
        } else {
            return;
        }
    }

    $apsa_extra_options = get_option('apsa_extra_options');

    //checking if plugin require anticache
    $cache = isset($apsa_extra_options['apsa_cache_enabled']) ? $apsa_extra_options['apsa_cache_enabled'] : 'false';

    $apsa_empty_embed = '';
    if ($cache == 'true') {
        if ($apsa_anticache) {
            $apsa_empty_embed = '<div class="apsa-embed-cont apsa-reset-start apsa-not-loaded" data-apsa-campaign-id="' . $campaign_id . '" style="display:none;" data-apsa-alignment="' . $apsa_alignment . '" data-apsa-vc-class="' . $apsa_vs_css_class . '"></div>';
            $apsa_empty_embed = isset($content) ? $apsa_empty_embed . $content : $apsa_empty_embed;
        } else {
            $apsa_empty_embed = '<div class="apsa-embed-cont apsa-not-loaded" data-apsa-campaign-id="' . $campaign_id . '"' . (!isset($content) ? ' style="display:none;" ' : ' ') . 'data-apsa-alignment="' . $apsa_alignment . '" data-apsa-vc-class="' . $apsa_vs_css_class . '">' . (isset($content) ? '<div class="apsa-content-holder">' . $content . '</div>' : '') . '</div>';
        }
    } else {
        $apsa_empty_embed = '<div class="apsa-embed-cont apsa-reset-start apsa-not-loaded" data-apsa-campaign-id="' . $campaign_id . '" style="display:none;" data-apsa-alignment="' . $apsa_alignment . '" data-apsa-vc-class="' . $apsa_vs_css_class . '"></div>';
        $apsa_empty_embed = isset($content) ? $apsa_empty_embed . $content : $apsa_empty_embed;
    }

    $campaign = $campaign[0];

    /** Check if campaign not exists or not have status active */
    if ($campaign["status"] !== "active") {
        return $apsa_empty_embed;
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
        return $apsa_empty_embed;
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

    if (empty($camp_options["width"])) {
        $camp_options["width"] = "100%";
    }

    if (empty($camp_options["height"])) {
        $camp_options["height"] = "100px";
    } else {
        $camp_options["height"] = $camp_options["height"] . 'px';
    }

    if (empty($camp_options['embed_direction'])) {
        $camp_options['embed_direction'] = "none";
    }

    /**
     * call smart filter
     */
    $campaign_elements = apsa_smart_filter($campaign, $camp_options, $apsa_page_info, $campaign_elements, $elements_options, $elements_stat, $content);

    // Check whether campaign has enable elements
    if (empty($campaign_elements)) {
        return $apsa_empty_embed;
    }

    // Get cookie for this campaign elements
    $apsa_embeds_info = isset($_COOKIE['apsa_embeds_info']) ? $_COOKIE['apsa_embeds_info'] : '';

    if (!empty($apsa_embeds_info)) {
        $apsa_embeds_info = json_decode(stripslashes($apsa_embeds_info), TRUE);
        $apsa_emb_info = isset($apsa_embeds_info[$campaign_id]) ? $apsa_embeds_info[$campaign_id] : FALSE;
    }

    // Check if cookie is set for this campaign elements
    if (!empty($apsa_emb_info)) {
        $lasts_priority = $apsa_emb_info['last'];
        $last_id = $apsa_emb_info['last_id'];

        $last_element = isset($campaign_elements[$last_id]) ? $campaign_elements[$last_id] : FALSE;

        if (!empty($_COOKIE['apsa_emb_change_interval_' . $campaign_id]) && $camp_options["change_interval"] !== -1 && !empty($last_element) && in_array($last_element, $campaign_elements)) {
            $element = $last_element;

            $camp_options["change_interval"] = "dontset";
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

                $element = $elements_by_priority[$lasts_priority];
                if (!empty($element) && in_array($element, $campaign_elements)) {
                    $element_choosen = TRUE;
                }
            }
        }
    } else {
        // Get first element(enable) by priority and display
        $elements_arr_vals = array_values($campaign_elements);
        $element = $elements_arr_vals[0];
    }

    // Check if element exists
    if (empty($element)) {
        return $apsa_empty_embed;
    }

    /** Determine embed content type and make html content for display */
    $emb_element_html = "";
    $embed_html = '';

    $apsa_get_child_embed_content = '';
    if (function_exists('apsa_get_child_embed_content'))
        $apsa_get_child_embed_content = apsa_get_child_embed_content($element, $elements_options[$element['id']], $camp_options, $content);

    if (!empty($apsa_get_child_embed_content)) {
        $emb_element_html = $apsa_get_child_embed_content['html'];
    }

    global $apsa_effects;
    $apsa_embed_effects = $apsa_effects['embed'];

    if ($camp_options['embed_direction'] == 'none') {
        $apsa_animation_name = 'animation-none';
        $apsa_embed_cont_visible = 'apsa-embed-cont-visible';
    } else {
        $apsa_animation_name = $apsa_embed_effects[$camp_options['embed_direction']];
        $apsa_embed_cont_visible = '';
    }

    if ($css != '') {
        $embed_html .= '<style type="text/css">' . $css . '</style>';
    }

    $embed_form = '<form class="apsa-embed-info">'
            . '<input type="hidden" name="campaign_id" value="' . $campaign_id . '" />'
            . '<input type="hidden" name="last" value="' . $element["priority"] . '" />'
            . '<input type="hidden" name="last_id" value="' . $element["id"] . '" />'
            . '<input type="hidden" name="change_interval" value="' . $camp_options["change_interval"] . '" />'
            . '</form>';



    //checking if plugin require anticache
    if ($cache == 'true') {
        if ($apsa_anticache) {
            $embed_html .= '<div class="apsa-embed-cont apsa-reset-start apsa-alignment-' . $apsa_alignment . ' apsa-embed-' . $element['type'] . ' ' . $apsa_embed_cont_visible . $apsa_vs_css_class . ' " style="width: ' . $camp_options["width"] . '; height: ' . $camp_options["height"] . ';" data-apsa-animation-name="' . $apsa_animation_name . '" data-apsa-alignment="' . $apsa_alignment . '" data-apsa-vc-class="' . $apsa_vs_css_class . '" data-apsa-element-id="' . $element['id'] . '" data-apsa-campaign-id="' . $element['campaign_id'] . '">';
            $embed_html = $embed_html . $embed_form . $emb_element_html . '</div>';
            // update element statistics
            apsa_update_element_statistics($element["id"], "view");

            if (function_exists('apsa_child_filter_embed')) {
                $embed_html = apsa_child_filter_embed($embed_html, $content, $campaign, $camp_options, $element, $elements_options[$element['id']]);
            } else if (isset($content)) {
                $embed_html = $embed_html . $content;
            }
        } else {
            $embed_html .= '<div class="apsa-embed-cont' . (!isset($content) ? ' apsa-reset-start ' : ' ') . 'apsa-not-loaded apsa-alignment-' . $apsa_alignment . ' apsa-embed-' . $element['type'] . ' ' . $apsa_embed_cont_visible . ' " style="min-width: ' . $camp_options["width"] . '; min-height: ' . $camp_options["height"] . '; padding-top: ' . $camp_options["height"] . '" data-apsa-animation-name="' . $apsa_animation_name . '" data-apsa-alignment="' . $apsa_alignment . '" data-apsa-vc-class="' . $apsa_vs_css_class . '" data-apsa-element-id="' . $element['id'] . '" data-apsa-campaign-id="' . $element['campaign_id'] . '">';
            if (isset($content)) {
                $embed_html .= '<div class="apsa-content-holder">' . $content . '</div>';
            }

            $embed_html = $embed_html . $embed_form . '</div>';
        }
    } else {
        $embed_html .= '<div class="apsa-embed-cont apsa-reset-start apsa-alignment-' . $apsa_alignment . ' apsa-embed-' . $element['type'] . ' ' . $apsa_embed_cont_visible . $apsa_vs_css_class . ' " style="width: ' . $camp_options["width"] . '; height: ' . $camp_options["height"] . ';" data-apsa-animation-name="' . $apsa_animation_name . '" data-apsa-alignment="' . $apsa_alignment . '" data-apsa-vc-class="' . $apsa_vs_css_class . '" data-apsa-element-id="' . $element['id'] . '" data-apsa-campaign-id="' . $element['campaign_id'] . '">';
        $embed_html = $embed_html . $embed_form . $emb_element_html . '</div>';

        // update element statistics
        apsa_update_element_statistics($element["id"], "view");

        if (function_exists('apsa_child_filter_embed')) {
            $embed_html = apsa_child_filter_embed($embed_html, $content, $campaign, $camp_options, $element, $elements_options[$element['id']]);
        } else if (isset($content)) {
            $embed_html = $embed_html . $content;
        }
    }

    return $embed_html;
}

global $apsa_plugin_data;
$shortcode_name = strtolower($apsa_plugin_data['plugin_data']['name']);
add_shortcode($shortcode_name, 'apsa_embed_campaign_func');

/**
 * when anti-cache enabled ajax handler for get embed campain content
 */
function apsa_call_embed_campaign_func() {
    $apsa_page_info = $_POST['apsa_page_info'];
    $apsa_widget_id = $_POST['apsa_widget_id'];
    $content = isset($_POST['apsa_content']) ? stripslashes(htmlspecialchars_decode($_POST['apsa_content'])) : NULL;

    if ($apsa_widget_id != 0) {
        $apsa_all_widgets = get_option('widget_apsa_campaign');
        $apsa_widget = $apsa_all_widgets[$apsa_widget_id];
        $apsa_shortcode_id = $apsa_widget['campaign_id'];
        $apsa_embed_align = isset($apsa_widget['embed_align']) ? $apsa_widget['embed_align'] : 'none';
    } else {
        $apsa_shortcode_id = $_POST['shortcode_id'];
        $apsa_embed_align = $_POST['apsa_alignment'];
    }

    $atts['id'] = $apsa_shortcode_id;
    $atts['align'] = $apsa_embed_align;
    $atts['vc_class'] = $_POST['apsa_vc_class'];
    $apsa_anticache = true;
    $name = '';
    $result[$_POST['shortcode_id']] = apsa_embed_campaign_func($atts, $content, $name, $apsa_page_info, $apsa_anticache);
    echo json_encode($result);
    die();
}

add_action('wp_ajax_nopriv_apsa_call_embed_campaign_func', 'apsa_call_embed_campaign_func');
add_action('wp_ajax_apsa_call_embed_campaign_func', 'apsa_call_embed_campaign_func');
