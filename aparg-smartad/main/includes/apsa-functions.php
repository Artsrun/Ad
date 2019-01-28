<?php
defined('ABSPATH') or die('No script kiddies please!');

// get elements options structure
function apsa_get_element_options_struct($element_options, $element_type, $campaign_type, $event_count) {
    global $apsa_admin_labels;
    global $apsa_all_forms; //===========Custom==========
    $restrict_visits = isset($element_options['restrict_visits']) ? $element_options['restrict_visits'] : "";
    $bg_type = isset($element_options['background_type']) ? $element_options['background_type'] : "";
    $auto_play_video = isset($element_options['auto_play_video']) ? $element_options['auto_play_video'] : "";

    if (trim($restrict_visits) != "" && $event_count >= $restrict_visits) {
        $visit_text = $apsa_admin_labels['visits_warning_message'];
        $warning_visit_class = 'apsa-warning-message';
    } else {
        $visit_text = $apsa_admin_labels['default'] . ' ' . $apsa_admin_labels['max_visits_def'];
        $warning_visit_class = '';
    }

    $htm = '<li class="apsa-form-item apsa-element-usual-input apsa-child-option">' .
            '<span>' . $apsa_admin_labels["max_clicks"] . '</span>' .
            '<span class="apsa-with-question" title="' . ucfirst($apsa_admin_labels["detailed_description"]) . '" data-apsa-message="' . $apsa_admin_labels["max_clicks_desc"] . '"></span>' .
            '<div class="apsa-input-block">' .
            '<input type="text" class="apsa-positive-int ' . (trim($restrict_visits) != "" && $event_count >= $restrict_visits ? ' apsa-overdue-element ' : '') . '" name="restrict_visits" value="' . $restrict_visits . '" />' .
            '</div>' .
            '<span class="apsa-input-message  ' . $warning_visit_class . '" data-apsa-default-message="' . $apsa_admin_labels['default'] . ' ' . $apsa_admin_labels['max_visits_def'] . '">' . $visit_text . '</span>' .
            '</li>' .
            '<li class="apsa-form-item apsa-element-extra-large-input apsa-child-option">' .
            '<span>' . $apsa_admin_labels["link_to"] . '</span>' .
            '<span class="apsa-with-question" title="' . ucfirst($apsa_admin_labels["detailed_description"]) . '" data-apsa-message="' . $apsa_admin_labels["link_to_desc"] . '"></span>' .
            '<div class="apsa-input-block">' .
            '<input type="text" class="apsa-link apsa-broken-link" name="link_to" value="' . (isset($element_options['link_to']) ? $element_options['link_to'] : "") . '" />' .
            '</div>' .
            '<span class="apsa-input-message"></span>' .
            '</li>' .
            '<li class="apsa-form-item apsa-element-large-input apsa-child-option">';
    if ($element_type == 'image'):
        $htm .= '<span>' . $apsa_admin_labels["image_file"] . '</span>' .
                '<span class="apsa-with-question" title="' . ucfirst($apsa_admin_labels["detailed_description"]) . '" data-apsa-message="' . $apsa_admin_labels["image_file_desc"] . '"></span>' .
                '<div class="apsa-input-block">' .
                '<div class="apsa-upload-wrap">' .
                '<button type="button" class="apsa-upload-file button" data-apsa-file-type="image">' . $apsa_admin_labels["choose_img_file"] . '</button>' .
                '</div>' .
                '<div class="apsa-upload-input-wrap">' .
                '<input type="text" class="apsa-hold-element-content" name="element_content" value="' . (isset($element_options['element_content']) ? $element_options['element_content'] : "") . '" />' .
                '</div>' .
                '</div>' .
                '<span class="apsa-input-message"></span>';

    elseif ($element_type == 'flash'):
        $htm .= '<span>' . $apsa_admin_labels["swf_file"] . '</span>' .
                '<span class="apsa-with-question" title="' . ucfirst($apsa_admin_labels["detailed_description"]) . '" data-apsa-message="' . $apsa_admin_labels["swf_file_desc"] . '"></span>' .
                '<div class="apsa-input-block">' .
                '<div class="apsa-upload-wrap">' .
                '<button type="button" class="apsa-upload-file button" data-apsa-file-type="application">' . $apsa_admin_labels["choose_swf_file"] . '</button>' .
                '</div>' .
                '<div class="apsa-upload-input-wrap">' .
                '<input type="text" class="apsa-hold-element-content" name="element_content" value="' . ( isset($element_options['element_content']) ? $element_options['element_content'] : "" ) . '" />' .
                '</div>' .
                '</div>' .
                '<span class="apsa-input-message"></span>';
    elseif ($element_type == 'video'):
        $url_data = isset($element_options['element_content']) ? json_decode($element_options['element_content'], TRUE) : "";
        $video_url = (!empty($url_data) ? $url_data['origin_url'] : '');
        $htm .= '<span>' . $apsa_admin_labels["video_url"] . '</span>' .
                '<span class="apsa-with-question" title="' . ucfirst($apsa_admin_labels["detailed_description"]) . '" data-apsa-message="' . $apsa_admin_labels["video_url_desc"] . '"></span>' .
                '<div class="apsa-input-block">' .
                '<input type="text" class="apsa-hold-element-content apsa-video-url" name="element_content" value="' . $video_url . '" />' .
                '</div>' .
                '<span class="apsa-input-message"></span>';
    elseif ($element_type == 'iframe'):
        $htm .= '<span>' . $apsa_admin_labels["source_url"] . '</span>' .
                '<span class="apsa-with-question" title="' . ucfirst($apsa_admin_labels["detailed_description"]) . '" data-apsa-message="' . $apsa_admin_labels["source_url_desc"] . '"></span>' .
                '<div class="apsa-input-block">' .
                '<input type="text" class="apsa-link apsa-broken-link apsa-hold-element-content" name="element_content" value="' . (isset($element_options['element_content']) ? $element_options['element_content'] : "" ) . '"" />' .
                '</div>' .
                '<span class="apsa-input-message"></span>';
    elseif ($element_type == 'code'):
        $htm .= '<span>' . $apsa_admin_labels["html_code"] . '</span>' .
                '<span class="apsa-with-question" title="' . ucfirst($apsa_admin_labels["detailed_description"]) . '" data-apsa-message="' . $apsa_admin_labels["html_code_desc"] . '"></span>' .
                '<div class="apsa-input-block apsa-code-button">' .
                '<input type="button" class="button apsa-type-code" value="' . $apsa_admin_labels["type_code"] . '" />' .
                '<textarea class="apsa-hold-element-content apsa-hold-code-content apsa-hidden-textarea" name="element_content">' . (isset($element_options['element_content']) ? $element_options['element_content'] : "" ) . '</textarea>' .
                '</div>' .
                '<span class="apsa-input-message"></span>';
    elseif ($element_type == 'custom')://===========Custom=====
        $forms = $apsa_all_forms;
        $current_form = isset($element_options['element_content']) ? $element_options['element_content'] : '';
        $htm .= '<span>' . $apsa_admin_labels["custom_ad"] . '</span>'
                . '<span class="apsa-with-question" title="' . ucfirst($apsa_admin_labels["detailed_description"]) . '" data-apsa-message="' . $apsa_admin_labels["choose_custom_ads_desc"] . '"></span>'
                . '<div class="apsa-input-block"><select class="apsa-hold-element-content" name="element_content">'
                . '<option value="">none</option>';
        if (!empty($forms)) {
            foreach ($forms as $key => $value) {

                $htm.= '<option value="' . $value['id'] . '" ';
                if ($value['id'] == $current_form) {
                    $htm.= ' selected';
                }
                $htm.='>' . ($value['title'] == "" ? "(" . $apsa_admin_labels["no_title"] . ")" : $value['title']) . '</option>';
            }
        }
        $htm.= '</select> </div> </li>';
    endif;
    $htm .= '</li>';
    if ($campaign_type == 'background') {
        if ($element_type == 'image') {
            $htm .= '<li class="apsa-form-item apsa-element-option-select apsa-child-option">'
                    . '<span>' . $apsa_admin_labels["bg_img_type"] . '</span>'
                    . '<span class="apsa-with-question" title="' . ucfirst($apsa_admin_labels["detailed_description"]) . '" data-apsa-message="' . $apsa_admin_labels["bg_img_type_desc"] . '"></span>'
                    . '<div>'
                    . '<select name="background_type">'
                    . '<option value="cover_bg"' . ($bg_type == 'cover_bg' ? ' selected="selected"' : "" ) . '>' . $apsa_admin_labels["cover"] . '</option>'
                    . '<option value="repeat_bg"' . ($bg_type == 'repeat_bg' ? ' selected="selected"' : "" ) . '>' . $apsa_admin_labels["repeat"] . '</option>'
                    . '<option value="cover_bg_parallax"' . ($bg_type == 'cover_bg_parallax' ? ' selected="selected"' : "" ) . '>' . $apsa_admin_labels["cover"] . ' (Parallax)' . '</option>'
                    . '<option value="repeat_bg_parallax"' . ($bg_type == 'repeat_bg_parallax' ? ' selected="selected"' : "" ) . '>' . $apsa_admin_labels["repeat"] . ' (Parallax)' . '</option>'
                    . '</select>'
                    . '</div>'
                    . '</li>';
        }
    }
    if ($campaign_type == 'popup') {
        if ($element_type == 'image') {
            $htm .= '<li class="apsa-form-item apsa-element-option-select apsa-child-option">'
                    . '<span>' . $apsa_admin_labels["bg_img_type"] . '</span>'
                    . '<span class="apsa-with-question" title="' . ucfirst($apsa_admin_labels["detailed_description"]) . '" data-apsa-message="' . $apsa_admin_labels["bg_img_type_desc"] . '"></span>'
                    . '<div>'
                    . '<select name="background_type">'
                    . '<option value="contain"' . ($bg_type == 'contain' ? ' selected="selected"' : "" ) . '>' . $apsa_admin_labels["contain"] . '</option>'
                    . '<option value="cover"' . ($bg_type == 'cover' ? ' selected="selected"' : "" ) . '>' . $apsa_admin_labels["cover"] . '</option>'
                    . '</select>'
                    . '</div>'
                    . '</li>';
        }
        if ($element_type == 'video') {
            $htm .= '<li class="apsa-form-item apsa-element-option-select apsa-child-option">'
                    . '<span>' . $apsa_admin_labels["auto_play_video"] . '</span>'
                    . '<span class="apsa-with-question" title=" ' . ucfirst($apsa_admin_labels["detailed_description"]) . '" data-apsa-message="' . $apsa_admin_labels["auto_play_video_desc"] . '"></span>'
                    . '<div>'
                    . '<input type="checkbox" ' . (!empty($auto_play_video) ? ' checked' : "") . '/>'
                    . '<input type="hidden" class="apsa-hold-checkbox" name="auto_play_video"' . (!empty($auto_play_video) ? ' value="on"' : "") . '>'
                    . '</div>'
                    . '</li>';
        }
    }
    if ($campaign_type == 'embed') {
        if ($element_type == 'image') {
            $htm .= '<li class="apsa-form-item apsa-element-option-select apsa-child-option">'
                    . '<span>' . $apsa_admin_labels["bg_img_type"] . '</span>'
                    . '<span class="apsa-with-question" title="' . ucfirst($apsa_admin_labels["detailed_description"]) . '" data-apsa-message="' . $apsa_admin_labels["bg_img_type_desc"] . '"></span>'
                    . '<div>'
                    . '<select name="background_type">'
                    . '<option value="contain"' . ($bg_type == 'contain' ? ' selected="selected"' : "" ) . '>' . $apsa_admin_labels["contain"] . '</option>'
                    . '<option value="cover"' . ($bg_type == 'cover' ? ' selected="selected"' : "" ) . '>' . $apsa_admin_labels["cover"] . '</option>'
                    . '</select>'
                    . '</div>'
                    . '</li>';
        }
        if ($element_type == 'video') {
            $htm .= '<li class="apsa-form-item apsa-element-option-select apsa-child-option">'
                    . '<span>' . $apsa_admin_labels["auto_play_video"] . '</span>'
                    . '<span class="apsa-with-question" title=" ' . ucfirst($apsa_admin_labels["detailed_description"]) . '" data-apsa-message="' . $apsa_admin_labels["auto_play_video_desc"] . '"></span>'
                    . '<div>'
                    . '<input type="checkbox" ' . (!empty($auto_play_video) ? ' checked' : "") . '/>'
                    . '<input type="hidden" class="apsa-hold-checkbox" name="auto_play_video"' . (!empty($auto_play_video) ? ' value="on"' : "") . '>'
                    . '</div>'
                    . '</li>';
        }
    }
    if ($campaign_type == 'sticky') {
        if ($element_type == 'image') {
            $htm .= '<li class="apsa-form-item apsa-element-option-select apsa-child-option">'
                    . '<span>' . $apsa_admin_labels["bg_img_type"] . '</span>'
                    . '<span class="apsa-with-question" title="' . ucfirst($apsa_admin_labels["detailed_description"]) . '" data-apsa-message="' . $apsa_admin_labels["bg_img_type_desc"] . '"></span>'
                    . '<div>'
                    . '<select name="background_type">'
                    . '<option value="contain"' . ($bg_type == 'contain' ? ' selected="selected"' : "" ) . '>' . $apsa_admin_labels["contain"] . '</option>'
                    . '<option value="cover"' . ($bg_type == 'cover' ? ' selected="selected"' : "" ) . '>' . $apsa_admin_labels["cover"] . '</option>'
                    . '</select>'
                    . '</div>'
                    . '</li>';
        }
        if ($element_type == 'video') {
            $htm .= '<li class="apsa-form-item apsa-element-option-select apsa-child-option">'
                    . '<span>' . $apsa_admin_labels["auto_play_video"] . '</span>'
                    . '<span class="apsa-with-question" title=" ' . ucfirst($apsa_admin_labels["detailed_description"]) . '" data-apsa-message="' . $apsa_admin_labels["auto_play_video_desc"] . '"></span>'
                    . '<div>'
                    . '<input type="checkbox" ' . (!empty($auto_play_video) ? ' checked' : "") . '/>'
                    . '<input type="hidden" class="apsa-hold-checkbox" name="auto_play_video"' . (!empty($auto_play_video) ? ' value="on"' : "") . '>'
                    . '</div>'
                    . '</li>';
        }
    }
    return $htm;
}

/**
 * Get campaign elements from databse with customm order
 * 
 * @global {object} $wpdb
 * @param {int} $campaign_id
 * @param {str} $order_by
 * @param {str} $order
 * @param {boolean} $non_empty
 * @return {array}
 */
function apsa_get_campaign_elements($campaign_id, $order_by = 'priority', $order = 'ASC', $non_empty = FALSE) {

    /** Get elements from wp_apsa_elements  table */
    global $wpdb;

    $elements_table = $wpdb->prefix . 'apsa_elements';
    $element_options_table = $wpdb->prefix . 'apsa_element_options';

    $order_clause = '';

    if (!empty($order_by) && !empty($order)) {
        $order_clause = ' ORDER BY ' . (($non_empty) ? $elements_table . '.' : '') . $order_by . ' ' . $order;
    }

    if ($non_empty) {
        $campaign_elements = $wpdb->get_results('SELECT ' . $elements_table . '.* FROM ' . $elements_table . ' INNER JOIN ' . $element_options_table . ' ON ' . $elements_table . '.id = ' . $element_options_table . '.element_id WHERE ' . $elements_table . '.campaign_id = ' . $campaign_id . ' GROUP BY ' . $elements_table . '.id' . $order_clause, ARRAY_A);
    } else {
        $campaign_elements = $wpdb->get_results('SELECT * FROM ' . $elements_table . ' WHERE campaign_id = ' . $campaign_id . $order_clause, ARRAY_A);
    }

    return $campaign_elements;
}

/**
 * Get element option value or all options 
 * 
 * @global object $wpdb
 * @param int $element_id
 * @param str $option_name
 * @return array
 */
function apsa_get_element_options($element_id, $option_name = 'all') {

    /** Get options from wp_apsa_element_options table */
    global $wpdb;

    $element_options_table = $wpdb->prefix . 'apsa_element_options';

    if ($option_name == "all") {
        $name_condition = '';
    } else {
        $name_condition = ' AND option_name = "' . $option_name . '"';
    }

    $element_options = $wpdb->get_results('SELECT * FROM ' . $element_options_table . ' WHERE element_id = "' . $element_id . '"' . $name_condition, ARRAY_A);

    if ($option_name == "all") {
        return $element_options;
    } elseif (empty($element_options)) {
        return '';
    } else {
        return $element_options[0]['option_value'];
    }
}

/**
 * Update add options
 * 
 * @global object $wpdb
 * @param int $element_id
 * @param array $options
 * @return string
 */
function apsa_update_element_options($element_id, $options) {


    /** Check if wrong argument return error message */
    if (!is_array($options)) {
        trigger_error('undefined variable', E_USER_NOTICE);
        return false;
    }

    global $wpdb;

    $element_options_table = $wpdb->prefix . 'apsa_element_options';

    $insert_values = '';
    $counter = 0;

    foreach ($options as $key => $option) {
        $counter ++;

        $insert_values .= "('" . $element_id . "', '" . $key . "', '" . $option . "')";
        if ($counter != count($options)) {

            $insert_values .= ', ';
        }
    }

    $query = $wpdb->query('INSERT INTO ' . $element_options_table . ' (element_id, option_name, option_value) VALUES ' . $insert_values . ' ON DUPLICATE KEY UPDATE option_value = VALUES(option_value)');

    return $query;
}

/**
 * Insert new element
 * 
 * @global object $wpdb
 * @param str $title
 * @param str $type
 * @return int
 */
function apsa_insert_element($campaign_id, $title = '', $type = '', $status = "active", $priority = 0) {

    global $wpdb;
    global $apsa_admin_labels;

    $elements_table = $wpdb->prefix . 'apsa_elements';

    if (empty($title)) {

        switch ($type) {
            case 'image':
                $title = $apsa_admin_labels["element_name_image"];
                break;
            case 'video':
                $title = $apsa_admin_labels["element_name_video"];
                break;
            case 'flash':
                $title = $apsa_admin_labels["element_name_flash"];
                break;
            case 'code':
                $title = $apsa_admin_labels["element_name_code"];
                break;
            case 'iframe':
                $title = $apsa_admin_labels["element_name_iframe"];
                break;
            case 'custom'://===========Custom=====
                $title = $apsa_admin_labels["element_name_custom"];
                break;
            default:
                $title = '';
                break;
        }

        $title = $title . " - (" . date_i18n(get_option('date_format'), current_time('timestamp')) . ")";
    }

    /** Increment elements priority */
    $wpdb->query('UPDATE ' . $elements_table . ' SET priority = priority+1 WHERE priority >= "' . $priority . '" AND campaign_id = "' . $campaign_id . '"');

    $wpdb->insert(
            $elements_table, array(
        'campaign_id' => $campaign_id,
        'title' => $title,
        'type' => $type,
        'status' => $status,
        'priority' => $priority,
        'creation_date' => date('Y-m-d H:i:s', current_time('timestamp')),
            )
    );

    $new_element = array();
    $new_element['element_id'] = $wpdb->insert_id;
    $new_element['creation_date'] = date_i18n(get_option('date_format'), current_time('timestamp'));

    return $new_element;
}

/**
 * Update element
 * 
 * @global object $wpdb
 * @param int $element_id
 * @param str $title
 * @param str $type
 */
function apsa_update_element($element_id, $title = '', $type = '', $priority = '', $status = '') {


    if ($title == '' && $type == '' && $priority == '' && $status == '') {
        trigger_error('undefined variable', E_USER_NOTICE);
        return false;
    }

    global $wpdb;

    $elements_table = $wpdb->prefix . 'apsa_elements';

    $data_array = array();
    $data_array['title'] = $title;
    $data_array['type'] = $type;
    $data_array['priority'] = $priority;
    $data_array['status'] = $status;

    $set_query = '';
    $not_first = FALSE;
    foreach ($data_array as $key => $value) {
        if (!empty($value) || $value == "0") {
            if ($not_first == FALSE) {
                $set_query .= 'SET ' . $key . '="' . $value . '"';
                $not_first = TRUE;
            } else {
                $set_query .= ', ' . $key . '="' . $value . '"';
            }
        }
    }

    $query = $wpdb->query('UPDATE ' . $elements_table . ' ' . $set_query . ' WHERE id = "' . $element_id . '"');

    return $query;
}

/**
 * Delete element and its options 
 * 
 * @global object $wpdb
 * @param int $element_id
 */
function apsa_delete_element($element_id) {

    global $wpdb;

    $elements_table = $wpdb->prefix . 'apsa_elements';
    $element_options_table = $wpdb->prefix . 'apsa_element_options';
    $statistics_table = $wpdb->prefix . 'apsa_element_statistics';

    /** Get element priority and campaign id */
    $priority = apsa_get_element_data($element_id, "priority");
    $campaign_id = apsa_get_element_data($element_id, "campaign_id");

    // Delete element from elements table
    $delete_element = $wpdb->delete($elements_table, array('id' => $element_id));

    /** Decrement elements priority */
    $update_priority = $wpdb->query('UPDATE ' . $elements_table . ' SET priority = priority-1 WHERE priority > "' . $priority . '" AND campaign_id = "' . $campaign_id . '"');

    // Delete element options
    $delete_options = $wpdb->delete($element_options_table, array('element_id' => $element_id));

    // Delete element statistics
    $delete_stat = $wpdb->delete($statistics_table, array('element_id' => $element_id));

    if ($delete_element === FALSE || $update_priority === FALSE || $delete_options === FALSE || $delete_stat === FALSE) {
        return FALSE;
    } else {
        return TRUE;
    }
}

/*
 * delete element option
 */

function apsa_delete_element_option($element_id, $option_name) {
    global $wpdb;
    $element_options_table = $wpdb->prefix . 'apsa_element_options';
    $delete_option = $wpdb->delete($element_options_table, array('element_id' => $element_id, 'option_name' => $option_name));
}

/**
 * Check if element enable or disable
 * 
 * @param int $element_id
 * @return boolean
 */
function apsa_is_enable_element($element_id) {
    if (apsa_get_element_options($element_id, "disable") == "on") {
        return FALSE;
    } else {
        return TRUE;
    }
}

/**
 * Returns element or specific element data
 * 
 * @global object $wpdb
 * @param int $element id
 * @param str $data_name
 * @return array | str
 */
function apsa_get_element_data($element_id, $data_name = FALSE) {

    global $wpdb;

    $elements_table = $wpdb->prefix . 'apsa_elements';

    if (empty($data_name)) {
        $data = $wpdb->get_results('SELECT * FROM ' . $elements_table . ' WHERE id = "' . $element_id . '"', ARRAY_A);
        $data = isset($data[0]) ? $data[0] : "";
    } else {
        $data = $wpdb->get_results('SELECT ' . $data_name . ' FROM ' . $elements_table . ' WHERE id = "' . $element_id . '"', ARRAY_A);
        $data = isset($data[0][$data_name]) ? $data[0][$data_name] : "";
    }

    return $data;
}

/**
 * Returne campaign element by priority
 * 
 * @global object $wpdb
 * @param int $campaign_id
 * @param int $priority
 * If not set return element by priority 0
 * @return array
 */
function apsa_get_element_by_priority($campaign_id, $priority = 0) {

    global $wpdb;

    $elements_table = $wpdb->prefix . 'apsa_elements';

    $element = $wpdb->get_results('SELECT * FROM ' . $elements_table . ' WHERE campaign_id = "' . $campaign_id . '" AND priority = "' . $priority . '"', ARRAY_A);

    return $element[0];
}

/**
 * Get campaign elements count
 * 
 * @global object $wpdb
 * @param int $campaign_id
 * @return int
 */
function apsa_get_campaign_elements_count($campaign_id, $element_status = NULL) {

    global $wpdb;

    $elements_table = $wpdb->prefix . 'apsa_elements';

    if (isset($element_status)) {
        $elements_count = $wpdb->get_var("SELECT COUNT(*) FROM " . $elements_table . " WHERE campaign_id = '" . $campaign_id . "' AND status = '" . $element_status . "'");
    } else {
        $elements_count = $wpdb->get_var("SELECT COUNT(*) FROM " . $elements_table . " WHERE campaign_id = '" . $campaign_id . "'");
    }

    return $elements_count;
}

/**
 * Get elements by ids
 */
function apsa_get_elements($element_ids = 'all') {
    /** determin request type and return result */
    if (is_array($element_ids)) {
        foreach ($element_ids as $key => $value) {
            if ($key == 0) {
                $where_clause = " WHERE id = " . $value;
            } else {
                $where_clause .= " OR id = " . $value;
            }
        }
    } elseif (is_int($element_ids)) {
        $where_clause = " WHERE id = " . $element_ids;
    } elseif ($element_ids == 'all') {
        $where_clause = '';
    } else {
        trigger_error('undefined variable', E_USER_NOTICE);
        return false;
    }

    /** Get from db wp_apsa_elements table */
    global $wpdb;

    $elements_table = $wpdb->prefix . 'apsa_elements';

    $get_elements = $wpdb->get_results('SELECT * FROM `' . $elements_table . '`' . $where_clause . ' ORDER BY priority ASC', ARRAY_A);

    return $get_elements;
}

/**
 * Get all elements all options
 */
function apsa_get_all_element_options($element_ids = NULL) {
    global $wpdb;

    $element_options_table = $wpdb->prefix . 'apsa_element_options';

    if (!isset($element_ids)) {
        $get_element_options = $wpdb->get_results('SELECT * FROM `' . $element_options_table . '`', ARRAY_A);
    } elseif (is_array($element_ids)) {
        $element_ids_str = implode(',', $element_ids);

        $get_element_options = $wpdb->get_results('SELECT * FROM `' . $element_options_table . '` WHERE element_id IN(' . $element_ids_str . ')', ARRAY_A);
    } else {
        $get_element_options = FALSE;
    }

    return $get_element_options;
}

// updating child options
function apsa_update_child_element_options($elements) {
    global $apsa_admin_labels;
    foreach ($elements as $element) {
        // Check if element name set, else set default name
        if (empty($element['element_info']['title'])) {
            $element_type = apsa_get_element_data($element['element_info']['element_id'], "type");

            switch ($element_type) {
                case 'image':
                    $title = $apsa_admin_labels["element_name_image"];
                    break;
                case 'video':
                    $title = $apsa_admin_labels["element_name_video"];
                    break;
                case 'flash':
                    $title = $apsa_admin_labels["element_name_flash"];
                    break;
                case 'code':
                    $title = $apsa_admin_labels["element_name_code"];
                    break;
                case 'iframe':
                    $title = $apsa_admin_labels["element_name_iframe"];
                    break;
                case 'custom'://===========Custom=====
                    $title = $apsa_admin_labels["element_name_custom"];
                    break;
                default:
                    $title = '';
                    break;
            }

            $element_date = apsa_get_element_data($element['element_info']['element_id'], "creation_date");
            $element_date = date_i18n(get_option('date_format'), strtotime($element_date));

            $title .= " - (" . $element_date . ")";
        }

        /** Check which action must call, and call */
        $update_element = apsa_update_element($element['element_info']['element_id'], $element['element_info']['title'], $element['element_info']['type'], $element['element_info']['priority']);
        $update_element_opt = apsa_update_element_options($element['element_info']['element_id'], $element['element_options']);

        if ($update_element === FALSE || $update_element_opt === FALSE) {
            return false;
        }
    }
    return true;
}

// returning embed campaings child elements html 
function apsa_get_child_embed_content($element, $element_options, $camp_options) {

    if (empty($camp_options['link_color'])) {
        $camp_options['link_color'] = "#ffffff";
    }

    if (empty($camp_options['link_type'])) {
        $camp_options['link_type'] = "_blank";
    }

    $emb_element_html = '';
    if ($element["type"] == "image") {

        if (empty($element_options["background_type"])) {
            $element_options["background_type"] = "contain";
        }

        if (!empty($element_options["link_to"])) {
            $emb_element_html = '<div data-apsa-link-target="' . $camp_options["link_type"] . '" data-apsa-link="' . $element_options["link_to"] . '" style="background-size: ' . $element_options["background_type"] . '; background-image: url(' . $element_options["element_content"] . ')" class="apsa-element-link apsa-child-content"></div>';
        } else {
            $emb_element_html = '<div style="background-size: ' . $element_options["background_type"] . '; background-image: url(' . $element_options["element_content"] . ')" class="apsa-child-content"></div>';
        }
    } elseif ($element["type"] == "video") {

        if (isset($element_options["auto_play_video"]) && $element_options["auto_play_video"] == "on") {
            $element_options["auto_play_video"] = 1;
        } else {
            $element_options["auto_play_video"] = 0;
        }

        /** Convert youtube or vimeo url to embed */
        $url_data = json_decode($element_options["element_content"], true);
        $parsed_url = $url_data['parsed'];

        /** Convert youtube or vimeo url to embed */
        if ($parsed_url['provider'] == "youtube") {

            $start_time = (isset($parsed_url['params']['start']) ? '&start=' . $parsed_url['params']['start'] : "");
            $emb_element_html = '<iframe class="apsa-child-content" style="width:100%;" src="https://www.youtube.com/embed/' . $parsed_url['id'] . '?rel=0&autoplay=' . $element_options['auto_play_video'] . $start_time . '" frameborder="0" allowfullscreen></iframe>';
        } else if ($parsed_url['provider'] == "vimeo") {

            $start_time = (isset($parsed_url['params']['start']) ? '#t=' . $parsed_url['params']['start'] . 's' : "");
            $emb_element_html = '<iframe class="apsa-child-content" src="https://player.vimeo.com/video/' . $parsed_url['id'] . '?autoplay=' . $element_options['auto_play_video'] . $start_time . '" style="width:100%;" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
        }
    } elseif ($element["type"] == "flash") {
        $emb_element_html = '<object class="apsa-child-content" type="application/x-shockwave-flash" data="' . $element_options["element_content"] . '" style="width:100%;"></object>';
    } elseif ($element["type"] == "code") {
        $emb_element_html = '<div class="apsa-child-content">' . $element_options["element_content"] . '</div>';
        $emb_element_html = do_shortcode($emb_element_html);
    } elseif ($element["type"] == "iframe") {
        $emb_element_html = '<iframe class="apsa-child-content" src="' . $element_options["element_content"] . '" style="width:100%;"></iframe>';
    } elseif ($element["type"] == "custom") {//===========Custom=====
        $form_id = $element_options["element_content"];
        $custom_ad = apsa_get_forms($form_id);
        $camp_options['type'] = 'embed';
        $required_options = array(
            'saved_data' => $custom_ad['data'],
            'slug' => $custom_ad['slug'],
            'height' => $camp_options['height'],
            'camp_options' => $camp_options,
            'element_options' => $element_options
        );

        $emb_element_html = apsa_draw_custom_ad($required_options);
        $emb_element_html = do_shortcode($emb_element_html);
    }

    $element_link = '';

    if (!empty($element_options["link_to"]) && !empty($camp_options["show_link"])) {
        $element_link = '<style type="text/css">.apsa-embed-cont[data-apsa-campaign-id="' . $element['campaign_id'] . '"]{margin-bottom: 15px;}</style>';
        $element_link .= '<span data-apsa-link-target="' . $camp_options["link_type"] . '" style="color: ' . $camp_options["link_color"] . '" class="apsa-element-link" data-apsa-link="' . $element_options["link_to"] . '">' . $element_options["link_to"] . '</span>';
    }

    $response = array(
        'html' => $emb_element_html . $element_link,
    );
    return $response;
}

// checking child element visibility for front 
function apsa_check_child_visibility($campaign, $camp_options, $campaign_element, $element_options, $element_stat) {

    global $apsa_plugin_data;

    // Get restrict parameters
    $element_restrict_visits = isset($element_options["restrict_visits"]) ? $element_options["restrict_visits"] : "";
    $element_content = isset($element_options["element_content"]) ? $element_options["element_content"] : "";
    $element_visits = isset($element_stat[$apsa_plugin_data['event_name']]) ? $element_stat[$apsa_plugin_data['event_name']] : 0;

    $response = true;
    if (empty($element_content) || $campaign_element['status'] == "suspended" || (!empty($element_restrict_visits) && $element_visits >= $element_restrict_visits)) {
        $response = false;
    }
    return $response;
}

// creating element content and returning with other options
function apsa_get_child_popup_options($element_option, $pop_element, $camp_options) {

    if (empty($camp_options['link_color'])) {
        $camp_options['link_color'] = "#ffffff";
    }

    if (empty($camp_options['link_type'])) {
        $camp_options['link_type'] = "_blank";
    }

    $pop_element_html = '';
    $response = array();
    if ($pop_element["type"] == "image") {
        $response["pop_backgroud_type"] = $element_option['background_type'];
        if (!empty($element_option["link_to"])) {
            $pop_element_html = '<div data-apsa-link-target="' . $camp_options["link_type"] . '" data-apsa-link="' . $element_option["link_to"] . '" class="apsa-element-link apsa-child-content" style="background-size: ' . $element_option['background_type'] . '; background-image: url(' . $element_option["element_content"] . ')"></div>';
        } else {
            $pop_element_html = '<div style="background-size: ' . $element_option['background_type'] . '; background-image: url(' . $element_option["element_content"] . ')" class="apsa-child-content"></div>';
        }
    } elseif ($pop_element["type"] == "video") {

        if (isset($element_option['auto_play_video']) && $element_option['auto_play_video'] == "on") {
            $element_option['auto_play_video'] = 1;
        } else {
            $element_option['auto_play_video'] = 0;
        }
        $response["pop_auto_play_video"] = $element_option['auto_play_video'];

        $url_data = json_decode($element_option["element_content"], true);
        $parsed_url = $url_data['parsed'];

        /** Convert youtube or vimeo url to embed */
        if ($parsed_url['provider'] == "youtube") {

            $start_time = (isset($parsed_url['params']['start']) ? '&start=' . $parsed_url['params']['start'] : "");
            $pop_element_html = '<iframe class="apsa-child-content" style="width:100%; height:100%;" src="https://www.youtube.com/embed/' . $parsed_url['id'] . '?rel=0&autoplay=' . $element_option['auto_play_video'] . $start_time . '" frameborder="0" allowfullscreen></iframe>';
        } else if ($parsed_url['provider'] == "vimeo") {

            $start_time = (isset($parsed_url['params']['start']) ? '#t=' . $parsed_url['params']['start'] . 's' : "");
            $pop_element_html = '<iframe class="apsa-child-content" src="https://player.vimeo.com/video/' . $parsed_url['id'] . '?autoplay=' . $element_option['auto_play_video'] . $start_time . '" style="width:100%; height:100%;" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
        }
    } elseif ($pop_element["type"] == "flash") {
        $pop_element_html = '<object class="apsa-child-content" type="application/x-shockwave-flash" data="' . $element_option["element_content"] . '" style="width:100%; height:100%;"></object>';
    } elseif ($pop_element["type"] == "code") {
        $pop_element_html = '<div class="apsa-child-content">' . $element_option["element_content"] . '</div>';
        $pop_element_html = do_shortcode($pop_element_html);
    } elseif ($pop_element["type"] == "iframe") {
        $pop_element_html = '<iframe class="apsa-child-content" src="' . $element_option["element_content"] . '" style="width:100%; height:100%;"></iframe>';
    } elseif ($pop_element["type"] == "custom") {//===========Custom=====
        $form_id = $element_option["element_content"];
        $custom_ad = apsa_get_forms($form_id);



        $camp_options['type'] = 'popup';
        $required_options = array(
            'saved_data' => $custom_ad['data'],
            'slug' => $custom_ad['slug'],
            'height' => '100%',
            'camp_options' => $camp_options,
            'element_options' => $element_option
        );



        $pop_element_html = apsa_draw_custom_ad($required_options);
        $pop_element_html = do_shortcode($pop_element_html);
    }
    if ($camp_options['show_link'] != '' && $element_option["link_to"] != '') {
        $link = '<span style="color: ' . $camp_options["link_color"] . '" data-apsa-link="' . $element_option["link_to"] . '" data-apsa-link-target="' . $camp_options["link_type"] . '" class="apsa-element-link">' . $element_option["link_to"] . '</span>';
        $pop_element_html = $link . $pop_element_html;
    }
    $response["pop_link_to"] = $element_option["link_to"];
    $response["pop_element_content"] = $element_option["element_content"];
    $response["pop_element_html"] = $pop_element_html;
    $response["pop_element_type"] = $pop_element["type"];
    $response["pop_link_color"] = $camp_options['link_color'];
    $response["pop_show_link"] = $camp_options['show_link'];
    $response["pop_link_type"] = $camp_options['link_type'];

    return $response;
}

// returning background campaign element options
function apsa_get_child_bg_options($element_options, $element, $camp_options) {

    global $apsa_effects;
    $apsa_background_patterns = $apsa_effects['patterns'];

    if (empty($element_options["background_type"])) {
        $element_options["background_type"] = "cover";
    }

    if (empty($camp_options["link_type"])) {
        $camp_options["link_type"] = "_blank";
    }

    if (empty($camp_options['background_pattern'])) {
        $camp_options['background_pattern'] = "none";
    }

    $response = array();

    $response["bg_link_type"] = $camp_options["link_type"];
    if ($camp_options["background_pattern"] == 'none') {
        $response["background_pattern"] = $camp_options["background_pattern"];
    } else {
        $response["background_pattern"] = $apsa_background_patterns[$camp_options["background_pattern"]];
    }

    $response["bg_background_type"] = $element_options["background_type"];
    $response["bg_link_to"] = $element_options["link_to"];
//    $response["bg_restrict_views"] = $element_options["restrict_views"];
//    $response["bg_restrict_visits"] = $element_options["restrict_visits"];
    $response["bg_element_content"] = $element_options["element_content"];

    // set background type
    $webkit_background_size = '';
    $background_size = '';
    $background_repeat = '';
    $background_position = '';
    $background_attachment = '';
    $height = '';
    $background_image = 'url("' . $element_options["element_content"] . '")';

    if ($element_options["background_type"] == "cover_bg") {
        $webkit_background_size = 'cover !important';
        $background_size = 'cover !important';
        $background_repeat = 'no-repeat !important';
        $background_position = 'center !important';
        $background_attachment = '';
        $height = '';
    } else if ($element_options["background_type"] == "repeat_bg") {
        $webkit_background_size = '';
        $background_size = '';
        $background_repeat = 'repeat !important';
        $background_position = '';
        $background_attachment = '';
        $height = '';
    } else if ($element_options["background_type"] == "cover_bg_parallax") {
        $webkit_background_size = 'cover !important';
        $background_size = 'cover !important';
        $background_repeat = 'no-repeat !important';
        $background_position = 'center !important';
        $background_attachment = 'fixed !important';
        $height = '100% !important';
    } else if ($element_options["background_type"] == "repeat_bg_parallax") {
        $webkit_background_size = '';
        $background_size = '';
        $background_repeat = 'repeat !important';
        $background_position = '';
        $background_attachment = 'fixed !important';
        $height = '100% !important';
    }

    // set background pattern
    if ($response["background_pattern"] != "none") {
        $webkit_background_size = 'auto' . ($webkit_background_size != '' ? ', ' . $webkit_background_size : '');
        $background_size = 'auto' . ($background_size != '' ? ', ' . $background_size : '');
        $background_repeat = 'repeat' . ($background_repeat != '' ? ', ' . $background_repeat : '');
        $background_position = 'center' . ($background_position != '' ? ', ' . $background_position : '');
        $background_attachment = $background_attachment;
        $height = $height;
        $background_image = 'url("' . plugin_dir_url(__FILE__) . '../../framework/view/front/images/patterns/' . $response["background_pattern"] . '"), ' . $background_image;
    }

    if ($webkit_background_size != '')
        $webkit_background_size = ' -webkit-background-size: ' . $webkit_background_size . ';';
    if ($background_size != '')
        $background_size = ' background-size: ' . $background_size . ';';
    if ($background_repeat != '')
        $background_repeat = ' background-repeat: ' . $background_repeat . ';';
    if ($background_position != '')
        $background_position = ' background-position: ' . $background_position . ';';
    if ($background_attachment != '')
        $background_attachment = ' background-attachment: ' . $background_attachment . ';';
    if ($height != '')
        $height = 'height: ' . $height . ';';
    if ($background_image != '')
        $background_image = ' background-image: ' . $background_image . ' !important;';

    $style = '{' . $webkit_background_size . $background_size . $background_repeat . $background_position . $background_attachment . $height . $background_image . '}';

    $response["bg_element_style"] = $style;
    return $response;
}

// creating element content and returning with other options
function apsa_get_child_sticky_options($element_option, $sticky_element, $camp_options) {

    if (empty($camp_options['link_color'])) {
        $camp_options['link_color'] = "#ffffff";
    }

    if (empty($camp_options['link_type'])) {
        $camp_options['link_type'] = "_blank";
    }

    $sticky_element_html = '';
    $response = array();
    if ($sticky_element["type"] == "image") {
        $response["sticky_backgroud_type"] = $element_option['background_type'];
        if (!empty($element_option["link_to"])) {
            $sticky_element_html = '<div data-apsa-link-target="' . $camp_options["link_type"] . '" data-apsa-link="' . $element_option["link_to"] . '" class="apsa-element-link apsa-child-content" style="background-size: ' . $element_option['background_type'] . '; background-image: url(' . $element_option["element_content"] . ')"></div>';
        } else {
            $sticky_element_html = '<div style="background-size: ' . $element_option['background_type'] . '; background-image: url(' . $element_option["element_content"] . ')" class="apsa-child-content"></div>';
        }
    } elseif ($sticky_element["type"] == "video") {

        if (isset($element_option['auto_play_video']) && $element_option['auto_play_video'] == "on") {
            $element_option['auto_play_video'] = 1;
        } else {
            $element_option['auto_play_video'] = 0;
        }
        $response["sticky_auto_play_video"] = $element_option['auto_play_video'];

        $url_data = json_decode($element_option["element_content"], true);
        $parsed_url = $url_data['parsed'];

        /** Convert youtube or vimeo url to embed */
        if ($parsed_url['provider'] == "youtube") {

            $start_time = (isset($parsed_url['params']['start']) ? '&start=' . $parsed_url['params']['start'] : "");
            $sticky_element_html = '<iframe class="apsa-child-content" style="width:100%; height:100%;" src="https://www.youtube.com/embed/' . $parsed_url['id'] . '?rel=0&autoplay=' . $element_option['auto_play_video'] . $start_time . '" frameborder="0" allowfullscreen></iframe>';
        } else if ($parsed_url['provider'] == "vimeo") {

            $start_time = (isset($parsed_url['params']['start']) ? '#t=' . $parsed_url['params']['start'] . 's' : "");
            $sticky_element_html = '<iframe class="apsa-child-content" src="http://player.vimeo.com/video/' . $parsed_url['id'] . '?autoplay=' . $element_option['auto_play_video'] . $start_time . '" style="width:100%; height:100%;" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
        }
    } elseif ($sticky_element["type"] == "flash") {
        $sticky_element_html = '<object class="apsa-child-content" type="application/x-shockwave-flash" data="' . $element_option["element_content"] . '" style="width:100%; height:100%;"></object>';
    } elseif ($sticky_element["type"] == "code") {
        $sticky_element_html = '<div class="apsa-child-content">' . $element_option["element_content"] . '</div>';
        $sticky_element_html = do_shortcode($sticky_element_html);
    } elseif ($sticky_element["type"] == "iframe") {
        $sticky_element_html = '<iframe class="apsa-child-content" src="' . $element_option["element_content"] . '" style="width:100%; height:100%;"></iframe>';
    } elseif ($sticky_element["type"] == "custom") {//==========Custom============
        $form_id = $element_option["element_content"];
        $custom_ad = apsa_get_forms($form_id);

        $camp_options['type'] = 'sticky';
        $required_options = array(
            'saved_data' => $custom_ad['data'],
            'slug' => $custom_ad['slug'],
            'height' => '100%',
            'camp_options' => $camp_options,
            'element_options' => $element_option
        );

        $sticky_element_html = apsa_draw_custom_ad($required_options);
        $sticky_element_html = do_shortcode($sticky_element_html);
    }//==========Custom============
    if ($camp_options['show_link'] != '' && $element_option["link_to"] != '') {
        $link = '<span style="color: ' . $camp_options["link_color"] . '" data-apsa-link="' . $element_option["link_to"] . '" data-apsa-link-target="' . $camp_options["link_type"] . '" class="apsa-element-link">' . $element_option["link_to"] . '</span>';
        $sticky_element_html = $link . $sticky_element_html;
    }
    $response["sticky_link_to"] = $element_option["link_to"];
    $response["sticky_element_content"] = $element_option["element_content"];
    $response["sticky_element_html"] = $sticky_element_html;
    $response["sticky_element_type"] = $sticky_element["type"];
    $response["sticky_link_color"] = $camp_options['link_color'];
    $response["sticky_show_link"] = $camp_options['show_link'];
    $response["sticky_link_type"] = $camp_options['link_type'];

    return $response;
}

// return child campaign options html
function apsa_child_get_campaign_options($camp_options, $camp_type) {
    global $apsa_effects;
    $apsa_popup_effects = $apsa_effects['popup'];
    $apsa_embed_effects = $apsa_effects['embed'];
    $apsa_sticky_effects = $apsa_effects['sticky'];
    $apsa_overlay_patterns = $apsa_effects['patterns'];
    global $apsa_admin_labels;

    if ($camp_type == 'background') {
        ?>
        <li class="apsa-form-item">
            <span><?php echo $apsa_admin_labels["background_pattern"]; ?></span>
            <span class="apsa-with-question" title="<?php echo ucfirst($apsa_admin_labels["detailed_description"]); ?>" data-apsa-message="<?php echo $apsa_admin_labels["background_pattern_desc"]; ?>"></span>
            <?php $background_pattern = isset($camp_options['background_pattern']) ? $camp_options['background_pattern'] : "" ?>
            <div class="apsa-input-block">
                <select name="background_pattern">
                    <option value="none"<?php if ('none' == $background_pattern): ?> selected="selected"<?php endif; ?>>none</option>
                    <?php foreach ($apsa_overlay_patterns as $key => $value): ?>                                                
                        <option value="<?php echo $key; ?>"<?php if ($key == $background_pattern): ?> selected="selected"<?php endif; ?>><?php echo $key; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </li>
        <li class="apsa-form-item">
            <span><?php echo $apsa_admin_labels["open_link_type"]; ?></span>
            <span class="apsa-with-question" title="<?php echo ucfirst($apsa_admin_labels["detailed_description"]); ?>" data-apsa-message="<?php echo $apsa_admin_labels["open_link_type_desc"]; ?>"></span>
            <div class="apsa-input-block">
                <?php $link_type = isset($camp_options['link_type']) ? $camp_options['link_type'] : ""; ?>
                <select name="link_type">                                               
                    <option value="_blank"<?php if ($link_type == '_blank'): ?> selected="selected"<?php endif; ?>><?php echo $apsa_admin_labels["open_link_type_blank"]; ?></option>
                    <option value="_self"<?php if ($link_type == '_self'): ?> selected="selected"<?php endif; ?>><?php echo $apsa_admin_labels["open_link_type_self"]; ?></option>
                    <option value="_window"<?php if ($link_type == '_window'): ?> selected="selected"<?php endif; ?>><?php echo $apsa_admin_labels["open_link_type_window"]; ?></option>
                </select>
            </div>
        </li>
        <?PHP
    }
    if ($camp_type == 'popup') {
        ?>
        <li class="apsa-form-item">
            <span><?php echo $apsa_admin_labels["open_link_type"]; ?></span>
            <span class="apsa-with-question" title="<?php echo ucfirst($apsa_admin_labels["detailed_description"]); ?>" data-apsa-message="<?php echo $apsa_admin_labels["open_link_type_desc"]; ?>"></span>
            <div class="apsa-input-block">
                <?php $link_type = isset($camp_options['link_type']) ? $camp_options['link_type'] : ""; ?>
                <select name="link_type">                                               
                    <option value="_blank"<?php if ($link_type == '_blank'): ?> selected="selected"<?php endif; ?>><?php echo $apsa_admin_labels["open_link_type_blank"]; ?></option>
                    <option value="_self"<?php if ($link_type == '_self'): ?> selected="selected"<?php endif; ?>><?php echo $apsa_admin_labels["open_link_type_self"]; ?></option>
                    <option value="_window"<?php if ($link_type == '_window'): ?> selected="selected"<?php endif; ?>><?php echo $apsa_admin_labels["open_link_type_window"]; ?></option>
                </select>
            </div>
        </li>
        <li class="apsa-form-item">
            <?php $show_link = isset($camp_options['show_link']) ? $camp_options['show_link'] : "on"; ?>
            <?php $link_color = isset($camp_options['link_color']) ? $camp_options['link_color'] : "#ffffff"; ?>
            <div class="apsa-input-block">
                <span><?php echo $apsa_admin_labels["show_link"]; ?></span>
                <input type="checkbox" class="apsa-show-link" <?php if (!empty($show_link)): ?> checked<?php endif; ?>/>
                <input type="hidden" class="apsa-hold-checkbox" name="show_link"<?php if (!empty($show_link)): ?> value="on"<?php endif; ?>>
                <span class="apsa-with-question" title="<?php echo ucfirst($apsa_admin_labels["detailed_description"]); ?>" data-apsa-message="<?php echo $apsa_admin_labels["show_link_desc"]; ?>"></span>
            </div>
        </li>
        <li class="apsa-form-item">
            <?php $show_link = isset($camp_options['show_link']) ? $camp_options['show_link'] : "on"; ?>
            <?php $link_color = isset($camp_options['link_color']) ? $camp_options['link_color'] : "#ffffff"; ?>
            <span><?php echo $apsa_admin_labels["link_color"]; ?></span>
            <span class="apsa-with-question" title="<?php echo ucfirst($apsa_admin_labels["detailed_description"]); ?>" data-apsa-message="<?php echo $apsa_admin_labels["link_color_desc"]; ?>"></span>
            <div class="apsa-input-block">
                <input type="text" name="link_color" class="apsa-extra-small-input apsa-colorpicker" data-default-color="#ffffff" value="<?php echo $link_color; ?>">
            </div>
            <span class="apsa-input-message"><?php echo $apsa_admin_labels['default']; ?> #ffffff</span>
        </li>
        <?PHP
    }
    if ($camp_type == 'embed') {
        ?>
        <li class="apsa-form-item">
            <span><?php echo $apsa_admin_labels["open_link_type"]; ?></span>
            <span class="apsa-with-question" title="<?php echo ucfirst($apsa_admin_labels["detailed_description"]); ?>" data-apsa-message="<?php echo $apsa_admin_labels["open_link_type_desc"]; ?>"></span>
            <div class="apsa-input-block">
                <?php $link_type = isset($camp_options['link_type']) ? $camp_options['link_type'] : ""; ?>
                <select name="link_type">                                               
                    <option value="_blank"<?php if ($link_type == '_blank'): ?> selected="selected"<?php endif; ?>><?php echo $apsa_admin_labels["open_link_type_blank"]; ?></option>
                    <option value="_self"<?php if ($link_type == '_self'): ?> selected="selected"<?php endif; ?>><?php echo $apsa_admin_labels["open_link_type_self"]; ?></option>
                    <option value="_window"<?php if ($link_type == '_window'): ?> selected="selected"<?php endif; ?>><?php echo $apsa_admin_labels["open_link_type_window"]; ?></option>
                </select>
            </div>
        </li>
        <li class="apsa-form-item">
            <?php $show_link = isset($camp_options['show_link']) ? $camp_options['show_link'] : "on"; ?>
            <?php $link_color = isset($camp_options['link_color']) ? $camp_options['link_color'] : "#808080"; ?>
            <div class="apsa-input-block">
                <span><?php echo $apsa_admin_labels["show_link"]; ?></span>
                <input type="checkbox" class="apsa-show-link" <?php if (!empty($show_link)): ?> checked<?php endif; ?>/>
                <input type="hidden" class="apsa-hold-checkbox" name="show_link"<?php if (!empty($show_link)): ?> value="on"<?php endif; ?>>
                <span class="apsa-with-question" title="<?php echo ucfirst($apsa_admin_labels["detailed_description"]); ?>" data-apsa-message="<?php echo $apsa_admin_labels["show_link_desc"]; ?>"></span>
            </div>
        </li>
        <li class="apsa-form-item">
            <?php $show_link = isset($camp_options['show_link']) ? $camp_options['show_link'] : "on"; ?>
            <?php $link_color = isset($camp_options['link_color']) ? $camp_options['link_color'] : "#808080"; ?>
            <span><?php echo $apsa_admin_labels["link_color"]; ?></span>
            <span class="apsa-with-question" title="<?php echo ucfirst($apsa_admin_labels["detailed_description"]); ?>" data-apsa-message="<?php echo $apsa_admin_labels["link_color_desc"]; ?>"></span>
            <div class="apsa-input-block">
                <input type="text" name="link_color" class="apsa-extra-small-input apsa-colorpicker" data-default-color="#808080" value="<?php echo $link_color; ?>">
            </div>
            <span class="apsa-input-message"><?php echo $apsa_admin_labels['default']; ?> #808080</span>
        </li>
        <?PHP
    }
    if ($camp_type == 'sticky') {
        ?>
        <li class="apsa-form-item">
            <span><?php echo $apsa_admin_labels["open_link_type"]; ?></span>
            <span class="apsa-with-question" title="<?php echo ucfirst($apsa_admin_labels["detailed_description"]); ?>" data-apsa-message="<?php echo $apsa_admin_labels["open_link_type_desc"]; ?>"></span>
            <div class="apsa-input-block">
                <?php $link_type = isset($camp_options['link_type']) ? $camp_options['link_type'] : ""; ?>
                <select name="link_type">                                               
                    <option value="_blank"<?php if ($link_type == '_blank'): ?> selected="selected"<?php endif; ?>><?php echo $apsa_admin_labels["open_link_type_blank"]; ?></option>
                    <option value="_self"<?php if ($link_type == '_self'): ?> selected="selected"<?php endif; ?>><?php echo $apsa_admin_labels["open_link_type_self"]; ?></option>
                    <option value="_window"<?php if ($link_type == '_window'): ?> selected="selected"<?php endif; ?>><?php echo $apsa_admin_labels["open_link_type_window"]; ?></option>
                </select>
            </div>
        </li>
        <li class="apsa-form-item">
            <?php $show_link = isset($camp_options['show_link']) ? $camp_options['show_link'] : "on"; ?>
            <?php $link_color = isset($camp_options['link_color']) ? $camp_options['link_color'] : "#ffffff"; ?>
            <div class="apsa-input-block">
                <span><?php echo $apsa_admin_labels["show_link"]; ?></span>
                <input type="checkbox" class="apsa-show-link" <?php if (!empty($show_link)): ?> checked<?php endif; ?>/>
                <input type="hidden" class="apsa-hold-checkbox" name="show_link"<?php if (!empty($show_link)): ?> value="on"<?php endif; ?>>
                <span class="apsa-with-question" title="<?php echo ucfirst($apsa_admin_labels["detailed_description"]); ?>" data-apsa-message="<?php echo $apsa_admin_labels["show_link_desc"]; ?>"></span>
            </div>
        </li>
        <li class="apsa-form-item">
            <?php $show_link = isset($camp_options['show_link']) ? $camp_options['show_link'] : "on"; ?>
            <?php $link_color = isset($camp_options['link_color']) ? $camp_options['link_color'] : "#ffffff"; ?>
            <span><?php echo $apsa_admin_labels["link_color"]; ?></span>
            <span class="apsa-with-question" title="<?php echo ucfirst($apsa_admin_labels["detailed_description"]); ?>" data-apsa-message="<?php echo $apsa_admin_labels["link_color_desc"]; ?>"></span>
            <div class="apsa-input-block">
                <input type="text" name="link_color" class="apsa-extra-small-input apsa-colorpicker" data-default-color="#ffffff" value="<?php echo $link_color; ?>">
            </div>
            <span class="apsa-input-message"><?php echo $apsa_admin_labels['default']; ?> #ffffff</span>
        </li>
        <?PHP
    }
}

//===========Custom===============

/**
 * *===== Inserting custom ad ========
 * 
 * @global object $wpdb
 * @param str $title
 * @param str $template_slug
 * @param str $data
 * @return boolean | array
 */
function apsa_insert_custom_ad($title = '', $template_slug = '', $data = '', $status = 'available') {

    if ($title == '' && empty($data)) {
        trigger_error('undefined variable', E_USER_NOTICE);
        return false;
    }
    global $wpdb;

    $custom_ads_table = $wpdb->prefix . 'apsa_custom_ads';



    $wpdb->insert(
            $custom_ads_table, array(
        'title' => $title,
        'slug' => $template_slug,
        'data' => stripcslashes($data),
        'status' => $status,
        'creation_date' => date('Y-m-d H:i:s', current_time('timestamp')),
            )
    );

    $new_element = array();
    $new_element['id'] = $wpdb->insert_id;
    $new_element['creation_date'] = date_i18n(get_option('date_format'), current_time('timestamp'));
    return $new_element;
}

/**
 * *====== Updating Custom ad =========
 * 
 * @global object $wpdb
 * @param int $element_id
 * @param str $title
 * @param str $slug
 * @param str $data
 * @return boolean
 */
function apsa_update_custom_ad($element_id, $title = '', $slug = '', $data = '', $status = 'available') {


    if ($title == '' && empty($data)) {
        trigger_error('undefined variable', E_USER_NOTICE);
        return false;
    }
    global $wpdb;

    $custom_ads_table = $wpdb->prefix . 'apsa_custom_ads';

    $data_array = array();
    $data_array['title'] = $title;
    $data_array['data'] = $data;
    $data_array['slug'] = $slug;
    $data_array['status'] = $status;


    $set_query = '';
    $not_first = FALSE;
    foreach ($data_array as $key => $value) {
        if ($not_first == FALSE) {
            $set_query .= "SET " . $key . "='" . $value . "'";
            $not_first = TRUE;
        } else {
            $set_query .= ", " . $key . "='" . $value . "'";
        }
    }
    $query = $wpdb->query('UPDATE ' . $custom_ads_table . ' ' . $set_query . ' WHERE id = "' . $element_id . '"');
    return $query;
}

/**
 * *===== Updating sttus of forms ======
 * 
 * @global object $wpdb
 * @param array | int $element_id
 * @param str $status
 * @return type
 */
function apsa_update_custom_ad_status($form_id = '', $status = 'available') {
    if ($form_id == '') {
        trigger_error('undefined variable', E_USER_NOTICE);
        return false;
    }

    if (is_array($form_id)) {
        $ids = '';
        foreach ($form_id as $key => $value) {
            if ($ids == "")
                $ids.= $value;
            else
                $ids.= ',' . $value;
        }
    } else {
        $ids = $form_id;
    }
    $set_query = "SET status='" . $status . "'";
    global $wpdb;
    $custom_ads_table = $wpdb->prefix . 'apsa_custom_ads';
    $query = $wpdb->query('UPDATE ' . $custom_ads_table . ' ' . $set_query . ' WHERE id IN (' . $ids . ')');
    return $query;
}

/**
 * *===== Deleting custom ad from all tables =======
 * 
 * @global object $wpdb
 * @param int $form_id
 * @return boolean
 */
function apsa_delete_custom_ad($form_id = '') {

    if ($form_id == '') {
        trigger_error('undefined variable', E_USER_NOTICE);
        return false;
    }
    global $wpdb;
    $element_options_table = $wpdb->prefix . 'apsa_element_options';
    $custom_ads_table = $wpdb->prefix . 'apsa_custom_ads';


    if (is_array($form_id)) {
        $ids = '';
        foreach ($form_id as $key => $value) {
            if ($ids == "")
                $ids.= $value;
            else
                $ids.= ',' . $value;
        }
    } else {
        $ids = $form_id;
    }

//deleting from elemets options table

    $update_options_query = $wpdb->query("UPDATE  $element_options_table  SET option_value='' WHERE `option_name` = 'custom_ads' and option_value IN (" . $ids . ")");
    $query = "DELETE FROM $custom_ads_table WHERE id IN (" . $ids . ")";
    $delete_form = $wpdb->query($query);

    if ($delete_form === FALSE || $update_options_query === FALSE) {
        return FALSE;
    } else {
        return TRUE;
    }
}

/**
 * *===== Checking whether form id exist =======
 * 
 * @global object $wpdb
 * @param str | int  $id
 * @return boolean
 */
function apsa_is_id_exist($id) {
    $id = intval($id);
    global $wpdb;
    $custom_ads_table = $wpdb->prefix . 'apsa_custom_ads';
    $is_is_exist = $wpdb->get_var('SELECT status FROM  ' . $custom_ads_table . ' WHERE id = "' . $id . '"');
    return $is_is_exist;
}

/**
 * *==== Getting all templates config files data ====
 * 
 * @global string $apsa_file_path
 * @global array $apsa_templates
 * @return  array
 */
function apsa_get_all_templates_data() {

    global $apsa_file_path;
    global $apsa_templates;
    $apsa_templates = array();
    $scan_dir = scandir(plugin_dir_path($apsa_file_path) . '/templates/form');
    if (!empty($scan_dir)) {
        foreach ($scan_dir as $key => $value) {
            if (!in_array($value, array('.', '..'))) {
                $apsa_templates[] = require plugin_dir_path($apsa_file_path) . '/templates/form/' . $value . '/config.php';
            }
        }
    }
    return $apsa_templates;
}

/**
 * *====== Getting template config from  template dir =======
 * 
 * @global str $apsa_file_path
 * @param str $template_name
 * @param str $config_type
 * @return string
 */
function apsa_get_template_info($template_name, $config_type = '') {
    global $apsa_file_path;
    if (file_exists(plugin_dir_path($apsa_file_path) . 'templates/form/' . $template_name . '/config.php')) {
        $config = require plugin_dir_path($apsa_file_path) . 'templates/form/' . $template_name . '/config.php';
    } else {
        trigger_error("directory does not exist", E_USER_NOTICE);
        return '';
    }

    if ($config_type == '') {
        foreach ($config['elements'] as $key => $value) {
            unset($config['elements'][$key]['html']);
        }
        return ($config['elements']);
    } else if ($config_type == 'html') {
        $output_html = array();
        foreach ($config['elements'] as $key => $value) {
            $children = isset($value['childrens']) ? $value['childrens'] : array();
            $children = is_array($children) ? $children : array($children);
            $extra_data = isset($value['extra_data']) ? $value['extra_data'] : false;

            $output_html[$key] = array('html' => $value['html'], 'childrens' => $children);
            if ($extra_data != false) {
                $output_html[$key]['extra_data'] = $extra_data;
            }
        }
        return $output_html;
    }
}

/**
 * *===== Get forms count and dates =====
 * 
 * @global object $wpdb
 * @return array
 */
function apsa_get_counts_and_dates() {
    global $wpdb;
    $custom_ads_table = $wpdb->prefix . 'apsa_custom_ads';

    $items_all_count = $wpdb->get_results('SELECT DISTINCT status, group_concat(creation_date ORDER BY creation_date DESC) AS date, COUNT(*) AS count FROM ' . $custom_ads_table . ' GROUP BY status', ARRAY_A);
    $return_array = array();

    if (!empty($items_all_count)) {
        foreach ($items_all_count as $count_status) {
            $count_status['date'] = explode(',', $count_status['date']);
            $creation_months = array();
            foreach (array_unique(array_reverse($count_status['date']))as $key => $val) {
                $creation_timestamp = strtotime($val);
                $creation_months[date('Y-m', $creation_timestamp)] = date_i18n('M Y', $creation_timestamp);
            }
            $count_status['date'] = $creation_months;
            $return_array[$count_status['status']] = $count_status;
        }
    }

    return $return_array;
}

/**
 * *======== Get forms elements with count for pagination =======
 * 
 * @global object $wpdb
 * @param int $paged
 * @param int $limit
 * @return array
 */
function apsa_get_custom_ads_with_limit($paged = 1, $limit = 1000, $status = 'available', $order = 'DESC', $order_by = 'id', $date_filter = false) {

    global $wpdb;
    $from = ($paged * $limit) - $limit;
    $custom_ads_table = $wpdb->prefix . 'apsa_custom_ads';
    if ($date_filter === false) {
        $form = $wpdb->get_results('SELECT  * FROM  ' . $custom_ads_table . '   WHERE status="' . $status . '"  Order by ' . $order_by . ' ' . $order . ' LIMIT ' . $from . ',' . $limit . '  ', ARRAY_A);
    } else {

        $date_value = '%' . $date_filter . '%';
        $form = $wpdb->get_results('SELECT  SQL_CALC_FOUND_ROWS * FROM  ' . $custom_ads_table . '   WHERE `creation_date` like "' . $date_value . '" and  status="' . $status . '"  Order by ' . $order_by . ' ' . $order . ' LIMIT ' . $from . ',' . $limit . '  ', ARRAY_A);
        $count = $wpdb->get_results('SELECT FOUND_ROWS() as count', ARRAY_A);
        $form = array(
            'filtered_data' => $form,
            'filtered_count' => intval($count[0]['count'])
        );
    }
    return $form;
}

/**
 * *===== Getting template builder module initial data ========
 * 
 * @global array $apsa_admin_labels
 * @return array
 */
function apsa_get_template_inital_data() {
    global $apsa_admin_labels;
    $is_new = (isset($_GET['form']) && $_GET['form'] == 'edit') ? $_GET['id'] : 'true';
    if ($is_new == 'true') {
        $form_data = '[]';
        $all_template_data = apsa_get_all_templates_data();
        $form_slug = $all_template_data[0]['slug'];
        $form_title = $apsa_admin_labels["custom_ad"];
        $form_publised = $apsa_admin_labels['immediately'];
        $form_title = $form_title . " - (" . date_i18n(get_option('date_format'), current_time('timestamp')) . ")";
    } else {
        $all_forms = apsa_get_forms($is_new); // geting form data from DB
        $form_data = $all_forms['data'];
        $form_slug = $all_forms['slug'];
        $form_title = $all_forms['title'];
        $form_publised = date_i18n('M d, Y  h:i', strtotime($all_forms['creation_date']));
    }

    $inital_template_data = array(
        'temp_slug' => $form_slug,
        'form_data' => $form_data,
        'form_title' => $form_title,
        'form_publised' => $form_publised,
    );
    return $inital_template_data;
}

/**
 * *==== Getting custom ad ====== 
 * 
 * @global object $wpdb
 * @param int $form_id
 * @return array | boolean
 */
function apsa_get_forms($form_id = '', $status = 'available') {

    global $wpdb;

    $elements_table = $wpdb->prefix . 'apsa_custom_ads';

    if ($form_id) {
        $form_id = intval($form_id);
        $form = $wpdb->get_results('SELECT * FROM ' . $elements_table . ' WHERE id = ' . $form_id . ' and `status` =\'' . $status . '\' ', ARRAY_A);
        return $form ? $form[0] : false;
    } else {
        $form = $wpdb->get_results('SELECT * FROM ' . $elements_table . ' WHERE `status` =\'' . $status . '\'  ORDER BY id DESC', ARRAY_A);
        return $form ? $form : false;
    }
}

/**
 * *======== Get Forms With Search ============
 * 
 * @global object $wpdb
 * @param str $query_str
 */
function apsa_get_search_results($paged = 1, $limit = 1000, $query_str = '', $order = 'DESC', $order_by = 'id', $status = 'available', $date_filter = false) {
    global $wpdb;
    $from = ($paged * $limit) - $limit;
    $query_str = sanitize_text_field($query_str);
    $query_str = '%' . $query_str . '%';
    $custom_ads_table = $wpdb->prefix . 'apsa_custom_ads';
    if ($date_filter === false || $date_filter === '0') {
        $form = $wpdb->get_results('SELECT SQL_CALC_FOUND_ROWS * FROM ' . $custom_ads_table . ' WHERE `status`="' . $status . '" and `title` like "' . $query_str . '" Order by ' . $order_by . ' ' . $order . '  LIMIT ' . $from . ',' . $limit . '  ', ARRAY_A);
        $count = $wpdb->get_results('SELECT FOUND_ROWS() as count', ARRAY_A);
    } else {
        $date_value = '%' . $date_filter . '%';
        $form = $wpdb->get_results('SELECT SQL_CALC_FOUND_ROWS * FROM ' . $custom_ads_table . ' WHERE `creation_date` like "' . $date_value . '" and  `status`="' . $status . '" and `title` like "' . $query_str . '" Order by ' . $order_by . ' ' . $order . '  LIMIT ' . $from . ',' . $limit . '  ', ARRAY_A);
        $count = $wpdb->get_results('SELECT FOUND_ROWS() as count', ARRAY_A);
    }
    return array('forms' => $form, 'count' => intval($count[0]['count']));
}

/**
 * *===== Returning html for drawing in front page =====
 * 
 * @global str $apsa_file_path
 * @param arr $required_options
 * @return string
 */
function apsa_draw_custom_ad($required_options) {
    $element_html = '';
    $saved_data = $required_options['saved_data'];
    $slug = $required_options['slug'];
    $height = $required_options['height'];
    $campaign_type = $required_options['camp_options']['type'];
    $element_options = isset($required_options['element_options']) ? $required_options['element_options'] : array();



    if (!isset($slug)) {
        return $element_html;
    }
    global $apsa_file_path;
    $css_path = plugin_dir_path($apsa_file_path) . 'templates/form/' . $slug . '/style.css';
    $css_url = plugin_dir_url($apsa_file_path) . 'templates/form/' . $slug . '/style.css';
    $js_url = plugin_dir_url($apsa_file_path) . 'templates/form/' . $slug . '/script.js';
    $config_data = apsa_get_template_info($slug, 'html'); //getting html and other required data from config.php

    $all_required_data = gettype($saved_data) == 'string' ? json_decode($saved_data, true) : $saved_data; // checking for demo 
    if (!is_array($all_required_data))
        return '';
    $unreplaced_html = '';
    $changed_html = '';

    /* =============collecting all childs in template================ */
    $template_all_child_elements = array();
    foreach ($config_data as $key => $val) {
        if (!empty($val['childrens'])) {
            foreach ($val['childrens'] as $child_key => $child_value) {
                $template_all_child_elements[] = $child_value;
            }
        }
    }

    $template_all_child_elements = array_unique($template_all_child_elements);
    foreach ($all_required_data as $key => $value) { // iterating over form data
        $el_pref_index = (strpos($key, 'apsa_') == false) ? 0 : strpos($key, 'apsa_') + 5;
        $element_slug = substr($key, $el_pref_index);
        if (in_array($element_slug, $template_all_child_elements)) {
            continue;
        }


        $replace_from_array = array();
        $replace_to_array = array();

        if (isset($config_data[$element_slug])) {
            $unreplaced_html = $config_data[$element_slug]['html'];
        }

        /* ============= cheking unset vars ================ */
        if (isset($config_data[$element_slug]['extra_data']) && isset($config_data[$element_slug]['extra_data']['unset_vars'])) {

            foreach ($config_data[$element_slug]['extra_data']['unset_vars'] as $unset_var => $unset_array) {
                if ($soc_type && in_array($soc_type, $unset_array)) {
                    $replace_from_array[] = '%' . $unset_var . '%';
                    $replace_to_array[] = '';
                }
            }
        }

        if (isset($value['var_groups'])) {
            foreach ($value['var_groups'] as $var_key => $var_value) { // iterating over elements vars_groups
                foreach ($var_value['vars'] as $single_key => $single_value) {   // iterating over elements vars  elements
                    $replace_from_array[] = '%' . $single_key . '%';
                    $replace_to_array[] = $single_value['value'];
                }
            }
        }


        /* =============  cheking if element has chosen childs  ================ */
        $child_changed_html = '';

        if (isset($value["choosen_childs"])) {

            foreach ($value["choosen_childs"] as $choose_child_key => $choose_child_slug) { //iterating over chossen childs array
                $pf_index = strpos($choose_child_slug, 'apsa_');
                $child_element_slug = substr($choose_child_slug, $pf_index + 5);

                $child_replace_from_array = array();
                $child_replace_to_array = array();
                $child_unreplaced_html = $config_data[$child_element_slug]['html']; //take child html from configs
                $choosen_child_groups_social = isset($all_required_data[$choose_child_slug]['choosen_groups']['social']) ? $all_required_data[$choose_child_slug]['choosen_groups']['social'] : ''; //take choosen groups of child

                $child_choosen_slug = '';

                if (isset($all_required_data[$choose_child_slug]['var_groups'])) {

                    foreach ($all_required_data[$choose_child_slug]['var_groups'] as $child_var_slug => $child_var_value) { // iterating over child elements vars_groups
                        foreach ($child_var_value['vars'] as $child_single_key => $child_single_value) {   // iterating over child elements vars  elements
                            $child_replace_from_array[] = '%' . $child_single_key . '%';
                            $child_replace_to_array[] = isset($child_single_value['value']) ? $child_single_value['value'] : ''; // `chekbox` type  in template builder does't has inital value 
                        }
                    }
                }

                $child_element_changed_html = str_replace($child_replace_from_array, $child_replace_to_array, $child_unreplaced_html);
                $child_changed_html.= $child_element_changed_html;
            }
        }

        $element_changed_html = str_replace('%children%', $child_changed_html, $unreplaced_html);

        $element_changed_html = str_replace($replace_from_array, $replace_to_array, $element_changed_html); //canging vars of parent
        $changed_html.= $element_changed_html;
    }
//============== Replacing remaining vars ==============
    $changed_html = str_replace('%apsa_theme_path%', plugin_dir_url($apsa_file_path) . 'templates/form/' . $slug, $changed_html);

    $css_content = file_get_contents($css_path);
    $unique_id = 'apsa_' . apsa_return_random_string();
    $css = str_replace(array("\r", "\n"), '', $css_content);
    $css = str_replace('%apsa_theme_path%', plugin_dir_url($apsa_file_path) . 'templates/form/' . $slug, $css);
    $element_class = 'apsa-custom-' . $campaign_type . '-wrap';
    $template_class = 'apsa-' . $slug;
    $link_options = (!empty($element_options["link_to"]) ? 'data-apsa-link-target="' . $required_options['camp_options']["link_type"] . '" data-apsa-link="' . $element_options["link_to"] . '"' : "");
    $link_class = (!empty($element_options["link_to"]) ? 'apsa-element-link' : '');
    $element_html = '<script type="text/javascript">if(!document.querySelector("style[data-src=\'' . $css_url . '\']")){'
            . 'var style=document.createElement("style");'
            . 'style.setAttribute("data-src","' . $css_url . '");'
            . 'var inner_css = document.createTextNode("' . $css . '");'
            . 'style.appendChild(inner_css);'
            . 'var head = document.head || document.getElementsByTagName("head")[0];'
            . 'head.appendChild(style);}'
            . '</script><div ' . $link_options . ' id="' . $unique_id . '"  data-slug="' . $slug . '" style="height:' . $height . ';" class="' . $element_class . '  ' . $template_class . ' ' . $link_class . ' ">' . $changed_html . '</div>'
            . '<script  type="text/javascript">'
            . 'if(!document.querySelector("script[src=\'' . $js_url . '\']")){'
            . 'var script=document.createElement("script");'
            . 'script.onload=function(){var element =jQuery("#' . $unique_id . '");  if (typeof window["apsa_"+\'' . $slug . '\'+ "_resize"] == "function") {window["apsa_" +\'' . $slug . '\'+ "_resize"](element); }};'
            . 'script.setAttribute("src","' . $js_url . '");'
            . 'document.body.insertBefore(script,null);'
            . '} '
            . '</script>';

    return $element_html;
}

/**
 * *==== Returning random string (for unique id) ===== 
 * 
 * @return string
 */
function apsa_return_random_string() {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $characters_length = strlen($characters);
    $random_string = '';
    for ($i = 0; $i < 6; $i++) {
        $random_string .= $characters[rand(0, $characters_length - 1)];
    }
    return $random_string;
}
