<?php

defined('ABSPATH') or die('No script kiddies please!');

/**
 * Adds new row in wp_apsa_campaigns table
 * 
 * @global object $wpdb
 * @param str $name
 * @param str $type
 * @param str $status
 * @param array $defaults
 * @return int
 */
function apsa_add_campaign($type, $name = '', $status = "suspended", $defaults = array()) {
    global $wpdb;
    global $apsa_admin_labels;

    if (empty($name)) {
        switch ($type) {
            case 'background':
                $name = $apsa_admin_labels["camp_name_background"];
                break;
            case 'popup':
                $name = $apsa_admin_labels["camp_name_popup"];
                break;
            case 'embed':
                $name = $apsa_admin_labels["camp_name_embed"];
                break;
            case 'sticky':
                $name = $apsa_admin_labels["camp_name_sticky"];
                break;
            default:
                $name = '';
                break;
        }

        $name = $name . " - (" . date_i18n(get_option('date_format'), current_time('timestamp')) . ")";
    }

    $wpdb->insert(
            $wpdb->prefix . 'apsa_campaigns', array(
        'type' => $type,
        'name' => $name,
        'status' => $status,
        'creation_date' => date('Y-m-d H:i:s', current_time('timestamp')),
            )
    );

    $campaign_id = $wpdb->insert_id;

    if (!empty($defaults)) {
        $camp_defaults = apsa_update_campaign_options($campaign_id, $defaults);
    }

    if (!empty($defaults) && $camp_defaults === FALSE) {
        return FALSE;
    } else {
        $new_campaign = array();

        $new_campaign['campaign_id'] = $campaign_id;
        $new_campaign['creation_date'] = date_i18n(get_option('date_format'), current_time('timestamp'));

        return $new_campaign;
    }
}

/**
 * Get campaigns from database
 * 
 * @param array|int $campaign_ids
 * @return array
 */
function apsa_get_campaigns($campaign_ids = 'all', $type = 'all') {

    /** determin request type and retur result */
    if (is_array($campaign_ids)) {
        foreach ($campaign_ids as $key => $value) {
            if ($key == 0) {
                $where_clause = " WHERE id = " . $value;
            } else {
                $where_clause .= " OR id = " . $value;
            }
        }
    } elseif (is_int($campaign_ids)) {
        $where_clause = " WHERE id = " . $campaign_ids;
    } elseif ($campaign_ids == 'all') {
        $where_clause = '';
    } else {
        trigger_error('undefined variable', E_USER_NOTICE);
        return false;
    }
    if ($type != 'all') {
        if ($where_clause == '')
            $where_clause = ' WHERE type = "' . $type . '"';
        else
            $where_clause .= ' AND type = "' . $type . '"';
    }

    /** Get from db wp_apsa_campaigns table */
    global $wpdb;

    $campaign_table = $wpdb->prefix . 'apsa_campaigns';

    $campaigns = $wpdb->get_results('SELECT * FROM ' . $campaign_table . $where_clause . ' ORDER BY id DESC', ARRAY_A);

    return $campaigns;
}

/**
 * Update campaign options
 * 
 * @global object $wpdb
 * @param int $campaign_id
 * @param array $options
 * @return none
 */
function apsa_update_campaign_options($campaign_id, $options) {

    /** Check if wrong argument return error message */
    if (!is_array($options)) {
        trigger_error('undefined variable', E_USER_NOTICE);
        return false;
    }

    global $wpdb;

    $campaign_options_table = $wpdb->prefix . 'apsa_campaign_options';

    $insert_values = '';
    $counter = 0;
    foreach ($options as $key => $option) {
        $counter ++;

        $insert_values .= '("' . $campaign_id . '", "' . $key . '", "' . $option . '")';
        if ($counter != count($options)) {

            $insert_values .= ', ';
        }
    }

    $query = $wpdb->query('INSERT INTO ' . $campaign_options_table . ' (campaign_id, option_name, option_value) VALUES ' . $insert_values . ' ON DUPLICATE KEY UPDATE option_value = VALUES(option_value)');

    return $query;
}

/**
 * Get campaign option value or all options 
 * 
 * @global object $wpdb
 * @param int/array $campaign_ids
 * @param str $option_name Option name or leave to get all options
 * @return array
 */
function apsa_get_campaign_options($campaign_ids, $option_name = 'all') {

    /** Get options from wp_apsa_campaign_options table */
    global $wpdb;

    $campaign_options_table = $wpdb->prefix . 'apsa_campaign_options';

    $campaign_ids = (is_array($campaign_ids) ? $campaign_ids : array($campaign_ids));

    if ($option_name == "all") {
        $name_condition = '';
    } else {
        $name_condition = ' AND option_name = "' . $option_name . '"';
    }

    $campaign_options = $wpdb->get_results('SELECT campaign_id, option_name, option_value FROM ' . $campaign_options_table . ' WHERE campaign_id IN (' . implode(',', $campaign_ids) . ') ' . $name_condition, ARRAY_A);
    
    if ($option_name == "all") {
        return $campaign_options;
    } elseif (empty($campaign_options)) {
        return '';
    } else {
        return $campaign_options[0]['option_value'];
    }
}

/*
 * delete campaign option
 */

function apsa_delete_campaign_option($campaign_id, $option_name) {
    global $wpdb;
    $campaign_options_table = $wpdb->prefix . 'apsa_campaign_options';
    $delete_option = $wpdb->delete($campaign_options_table, array('campaign_id' => $campaign_id, 'option_name' => $option_name));
}

/**
 * Update campaign data
 * 
 * @global object $wpdb
 * @param int $campaign_id
 * @param str $name
 * @param str $type
 * @param str $status
 * @return str
 */
function apsa_update_campaign($campaign_id, $name = '', $type = '', $status = '') {

    if ($name == '' && $type == '' && $status == '') {
        trigger_error('undefined variable', E_USER_NOTICE);
        return false;
    }

    global $wpdb;

    $campaigns_table = $wpdb->prefix . 'apsa_campaigns';

    $data_array = array();
    $data_array['name'] = $name;
    $data_array['type'] = $type;
    $data_array['status'] = $status;

    $set_query = '';
    $first = TRUE;
    foreach ($data_array as $key => $value) {
        if (!empty($value)) {
            if ($first == TRUE) {
                $set_query .= 'SET ' . $key . '="' . $value . '"';
                $first = FALSE;
            } else {
                $set_query .= ', ' . $key . '="' . $value . '"';
            }
        }
    }

    $update = $wpdb->query('UPDATE ' . $campaigns_table . ' ' . $set_query . ' WHERE id = "' . $campaign_id . '"');

    return $update;
}

/**
 * Get campaigns count with any status or all
 * 
 * @global object $wpdb
 * @param str $status
 * @return int
 */
function apsa_get_campaigns_count($status = 'all') {

    global $wpdb;

    $campaigns_table = $wpdb->prefix . 'apsa_campaigns';

    /** Set where clause */
    $where_clause = "";

    if ($status !== 'all') {
        $where_clause = " WHERE status = '" . $status . "'";
    }

    // Get count
    $campaigns_count = $wpdb->get_var("SELECT COUNT(*) FROM " . $campaigns_table . $where_clause);

    return $campaigns_count;
}

/**
 * Delete campaign and its options, elements and elements options
 * 
 * @global object $wpdb
 * @param int $campaign_id
 */
function apsa_delete_campaign($campaign_id) {

    global $wpdb;

    $campaigns_table = $wpdb->prefix . 'apsa_campaigns';
    $campaign_options_table = $wpdb->prefix . 'apsa_campaign_options';
    $elements_table = $wpdb->prefix . 'apsa_elements';

    // Delete campaign from campaigns table
    $delete_camp = $wpdb->delete($campaigns_table, array('id' => $campaign_id));

    // Delete campaign options
    $delete_options = $wpdb->delete($campaign_options_table, array('campaign_id' => $campaign_id));

    // Get deleted campaign elements
    $campaign_elements = $wpdb->get_results('SELECT id FROM ' . $elements_table . ' WHERE campaign_id = "' . $campaign_id . '"', ARRAY_A);

    $delete_elements = TRUE;
    if (!empty($campaign_elements)) {
        foreach ($campaign_elements as $element) {
            if (apsa_delete_element($element["id"]) === FALSE) {
                $delete_elements = FALSE;
            }
        }
    }

    if ($delete_camp === FALSE || $delete_options === FALSE || $delete_elements === FALSE) {
        return FALSE;
    } else {
        return TRUE;
    }
}

/**
 * Return campaigns by type, with additional clause: active or not
 * 
 * @global object $wpdb
 * @param str $campaign_type
 * @return int | array
 */
function apsa_get_active_campaigns($campaign_type) {

    global $wpdb;

    $campaigns_table = $wpdb->prefix . 'apsa_campaigns';

    $campaign = $wpdb->get_results('SELECT * FROM ' . $campaigns_table . ' WHERE status = "active" AND type = "' . $campaign_type . '"', ARRAY_A);

    if (!empty($campaign)) {

        if (count($campaign) == 1) {
            $campaign = $campaign[0];
        }

        return $campaign;
    }
}

/**
 * Return campaign data
 * 
 * @global object $wpdb
 * @param int $campaign_id
 * @param str $data_name
 * @return array | string
 */
function apsa_get_campaign_data($campaign_id, $data_name = FALSE) {

    global $wpdb;

    $campaigns_table = $wpdb->prefix . 'apsa_campaigns';

    if (empty($data_name)) {
        $data = $wpdb->get_results('SELECT * FROM ' . $campaigns_table . ' WHERE id = "' . $campaign_id . '"', ARRAY_A);
        $data = $data[0];
    } else {
        $data = $wpdb->get_results('SELECT ' . $data_name . ' FROM ' . $campaigns_table . ' WHERE id = "' . $campaign_id . '"', ARRAY_A);
        $data = $data[0][$data_name];
    }

    return $data;
}

/**
 * Inserts new event on element statistics table
 * 
 * @global object $wpdb
 * @param int $element_id
 * @param str $type
 * @return int
 */
function apsa_update_element_statistics($element_id, $type) {

    global $wpdb;

    $statistics_table = $wpdb->prefix . 'apsa_element_statistics';

    $update_stat = $wpdb->query('INSERT INTO `' . $statistics_table . '` (element_id, type, date, count) VALUES ("' . $element_id . '", "' . $type . '", "' . date('Y-m-d', current_time('timestamp')) . '", 1) ON DUPLICATE KEY UPDATE count=count+1');

    return $update_stat;
}

/**
 * Get campaign total statistics
 * 
 * @param int $camp_id
 * @return array
 */
function apsa_get_camp_statistics($camp_id, $from = FALSE, $to = FALSE) {

    global $apsa_plugin_data;
    global $apsa_fr_event_name;
    $from = empty($from) ? date("0000-00-00") : $from;
    $to = empty($to) ? date("Y-m-d") : $to;

    $camp_elements = apsa_get_campaign_elements($camp_id, "title");
    $statistics["elements"] = array();
    $statistics[$apsa_fr_event_name] = array();
    $statistics[$apsa_plugin_data['event_name']] = array();
    $element_ids = array();


    if (!empty($camp_elements)) {

        foreach ($camp_elements as $camp_element) {
            $element_ids[] = $camp_element["id"];
        }

        $elements_stat_counts = apsa_get_element_stat_counts($element_ids, $from, $to);
        $stat_by_id = array();
        foreach ($elements_stat_counts as $stat) {
            $stat_by_id[$stat['element_id']][$apsa_plugin_data['event_name']] = (isset($stat_by_id[$stat['element_id']][$apsa_plugin_data['event_name']]) && $stat_by_id[$stat['element_id']][$apsa_plugin_data['event_name']] != 0) ? $stat_by_id[$stat['element_id']][$apsa_plugin_data['event_name']] : (($stat['type'] == $apsa_plugin_data['event_name']) ? $stat['total_count'] : 0);
            $stat_by_id[$stat['element_id']][$apsa_fr_event_name] = (isset($stat_by_id[$stat['element_id']][$apsa_fr_event_name]) && $stat_by_id[$stat['element_id']][$apsa_fr_event_name] != 0) ? $stat_by_id[$stat['element_id']][$apsa_fr_event_name] : (($stat['type'] == $apsa_fr_event_name) ? $stat['total_count'] : 0);
        }

        foreach ($camp_elements as $camp_element) {

            $statistics['elements'][] = $camp_element["title"];
            $statistics[$apsa_fr_event_name][] = intval(isset($stat_by_id[$camp_element['id']]) ? $stat_by_id[$camp_element['id']][$apsa_fr_event_name] : 0);
            $statistics[$apsa_plugin_data['event_name']][] = intval(isset($stat_by_id[$camp_element['id']]) ? $stat_by_id[$camp_element['id']][$apsa_plugin_data['event_name']] : 0);
        }
    }

    return $statistics;
}

/**
 * Get element statistics
 * 
 * @global object $wpdb
 * @param int $element_id
 * @param date $from
 * @param date $to
 * @return array
 */
function apsa_get_element_statistics($element_id, $from = FALSE, $to = FALSE) {

    global $wpdb;

    global $apsa_plugin_data;
    global $apsa_fr_event_name;
    $statistics_table = $wpdb->prefix . 'apsa_element_statistics';

    $from = empty($from) ? date("0000-00-00") : $from;
    $to = empty($to) ? date("Y-m-d") : $to;

    $statistics_array = $wpdb->get_results("SELECT * FROM `$statistics_table` WHERE element_id = $element_id AND (`date`>='$from' and `date`<='$to') ORDER BY date ASC", ARRAY_A);
    $statistics["days"] = array();
    $statistics[$apsa_fr_event_name] = array();
    $statistics[$apsa_plugin_data['event_name']] = array();

    if (!empty($statistics_array)) {

        $stat_by_day = array();
        foreach ($statistics_array as $data) {
            $stat_by_day[$data['date']][$apsa_plugin_data['event_name']] = (isset($stat_by_day[$data['date']][$apsa_plugin_data['event_name']]) && $stat_by_day[$data['date']][$apsa_plugin_data['event_name']] != 0) ? $stat_by_day[$data['date']][$apsa_plugin_data['event_name']] : (($data['type'] == $apsa_plugin_data['event_name']) ? $data['count'] : 0);
            $stat_by_day[$data['date']][$apsa_fr_event_name] = (isset($stat_by_day[$data['date']][$apsa_fr_event_name]) && $stat_by_day[$data['date']][$apsa_fr_event_name] != 0) ? $stat_by_day[$data['date']][$apsa_fr_event_name] : (($data['type'] == $apsa_fr_event_name) ? $data['count'] : 0);
        }

        $first_day = strtotime($statistics_array[0]['date']);
        $last_day_key = count($statistics_array) - 1;
        $last_day = strtotime($statistics_array[$last_day_key]['date']);
        $stat_by_day_full = array();
        while ($first_day <= $last_day) {
            $day = date('Y-m-d', $first_day);
            $stat_by_day_full[$day] = isset($stat_by_day[$day]) ? $stat_by_day[$day] : array($apsa_plugin_data['event_name'] => 0, $apsa_fr_event_name => 0);
            $first_day = $first_day + 86400;
        }

        foreach ($stat_by_day_full as $day => $value) {
            $statistics["days"][] = date_i18n(get_option('date_format'), strtotime($day));
            $statistics[$apsa_fr_event_name][] = intval($value[$apsa_fr_event_name]);
            $statistics[$apsa_plugin_data['event_name']][] = intval($value[$apsa_plugin_data['event_name']]);
        }
    }

    return $statistics;
}

/**
 * Get  all campaigns all options
 */
function apsa_get_all_campaign_options() {
    global $wpdb;

    $campaign_options_table = $wpdb->prefix . 'apsa_campaign_options';

    $get_campaign_options = $wpdb->get_results('SELECT * FROM `' . $campaign_options_table . '`', ARRAY_A);

    return $get_campaign_options;
}

/**
 * Get all elements statistics count
 */
function apsa_get_element_stat_counts($element_ids = NULL, $from = FALSE, $to = FALSE) {

    $from = empty($from) ? date("0000-00-00") : $from;
    $to = empty($to) ? date("Y-m-d") : $to;

    global $wpdb;

    $element_stats_table = $wpdb->prefix . 'apsa_element_statistics';

    if (isset($element_ids)) {
        $element_ids_str = implode(',', $element_ids);

        $get_elements_stat = $wpdb->get_results("SELECT element_id, type, SUM(count) AS total_count FROM `$element_stats_table` WHERE element_id IN($element_ids_str) AND (`date`>='$from' and `date`<='$to') group by type,element_id", ARRAY_A);
    } else {
        $get_elements_stat = $wpdb->get_results("SELECT element_id, type, SUM(count) AS total_count FROM `$element_stats_table` WHERE (`date`>='$from' and `date`<='$to') group by type,element_id", ARRAY_A);
    }

    return $get_elements_stat;
}

/**
 * Get elements statistics
 */
function apsa_get_elements_statistics($camp_id = NULL, $from = FALSE, $to = FALSE) {

    $camps_elements_stats = array();

    if (!empty($camp_id)) {
        $camp_elements = apsa_get_campaign_elements($camp_id);

        $camp_stat = apsa_get_camp_statistics($camp_id, $from, $to);

        $camps_elements_stats[$camp_id]['camp_stat'] = $camp_stat;

        if (!empty($camp_elements)) {
            foreach ($camp_elements as $camp_element) {
                $camps_elements_stats[$camp_id]['elements_stats'][$camp_element['id']] = apsa_get_element_statistics($camp_element['id'], $from, $to);
            }
        }
    } else {
        $camps = apsa_get_campaigns();
        if (!empty($camps)) {
            foreach ($camps as $camp) {
                $camp_elements = apsa_get_campaign_elements($camp['id']);

                $camp_stat = apsa_get_camp_statistics($camp['id'], $from, $to);

                $camps_elements_stats[$camp['id']]['camp_stat'] = $camp_stat;

                if (!empty($camp_elements)) {
                    foreach ($camp_elements as $camp_element) {
                        $camps_elements_stats[$camp['id']]['elements_stats'][$camp_element['id']] = apsa_get_element_statistics($camp_element['id'], $from, $to);
                    }
                }
            }
        }
    }

    return $camps_elements_stats;
}

/*
 * Smart filtering
 */

function apsa_smart_filter($campaign, $camp_options, $apsa_page_info, $campaign_elements, $elements_options, $elements_stat, $content = NULL) {
    
    global $apsa_plugin_data;
    global $apsa_fr_event_name;
    /**
     * Check specifing campaign
     */
    $camp_include_tags = isset($camp_options["camp_include_tags"]) ? $camp_options["camp_include_tags"] : "";
    $camp_exclude_tags = isset($camp_options["camp_exclude_tags"]) ? $camp_options["camp_exclude_tags"] : "";
    $lang = apsa_get_language();
    $device = apsa_get_device();
    
    $specify_ids['include']['category'] = array();
    $specify_ids['include']['post_tag'] = array();
    $specify_ids['include']['post'] = array();
    $specify_ids['include']['page'] = array();
    $specify_ids['include']['language'] = array();
    $specify_ids['include']['device'] = array();
    $specify_ids['exclude']['category'] = array();
    $specify_ids['exclude']['post_tag'] = array();
    $specify_ids['exclude']['post'] = array();
    $specify_ids['exclude']['page'] = array();
    $specify_ids['exclude']['language'] = array();
    $specify_ids['exclude']['device'] = array();

    if (!empty($camp_include_tags) || !empty($camp_exclude_tags)) {

        if (!empty($camp_include_tags)) {
            $camp_include_tags = explode("%" . strtolower($apsa_plugin_data['plugin_data']['name']) . "%,", rtrim($camp_include_tags, "%" . strtolower($apsa_plugin_data['plugin_data']['name']) . "%"));
            foreach ($camp_include_tags as $key => $camp_include_tag) {
                $camp_include_tag = explode("%", $camp_include_tag);
                if ($camp_include_tag[0] == "category") {
                    array_push($specify_ids['include']['category'], $camp_include_tag[1]);
                } elseif ($camp_include_tag[0] == "post_tag") {
                    array_push($specify_ids['include']['post_tag'], $camp_include_tag[1]);
                } elseif ($camp_include_tag[0] == "post") {
                    array_push($specify_ids['include']['post'], $camp_include_tag[1]);
                } elseif ($camp_include_tag[0] == "page") {
                    array_push($specify_ids['include']['page'], $camp_include_tag[1]);
                } elseif ($camp_include_tag[0] == "language") {
                    array_push($specify_ids['include']['language'], $camp_include_tag[1]);
                } elseif ($camp_include_tag[0] == "device") {
                    array_push($specify_ids['include']['device'], $camp_include_tag[1]);
                }
            }
        }
        if (!empty($camp_exclude_tags)) {
            $camp_exclude_tags = explode("%" . strtolower($apsa_plugin_data['plugin_data']['name']) . "%,", rtrim($camp_exclude_tags, "%" . strtolower($apsa_plugin_data['plugin_data']['name']) . "%"));
            foreach ($camp_exclude_tags as $key => $camp_exclude_tag) {
                $camp_exclude_tag = explode("%", $camp_exclude_tag);
                if ($camp_exclude_tag[0] == "category") {
                    array_push($specify_ids['exclude']['category'], $camp_exclude_tag[1]);
                } elseif ($camp_exclude_tag[0] == "post_tag") {
                    array_push($specify_ids['exclude']['post_tag'], $camp_exclude_tag[1]);
                } elseif ($camp_exclude_tag[0] == "post") {
                    array_push($specify_ids['exclude']['post'], $camp_exclude_tag[1]);
                } elseif ($camp_exclude_tag[0] == "page") {
                    array_push($specify_ids['exclude']['page'], $camp_exclude_tag[1]);
                } elseif ($camp_exclude_tag[0] == "language") {
                    array_push($specify_ids['exclude']['language'], $camp_exclude_tag[1]);
                } elseif ($camp_exclude_tag[0] == "device") {
                    array_push($specify_ids['exclude']['device'], $camp_exclude_tag[1]);
                }
            }
        }

        $camp_allow = TRUE;

        /** Check if current page included or excloded */
        if ($apsa_page_info['apsa_is_archive']) {
            $term = $apsa_page_info['apsa_get_queried_object'];

            if (!empty($term)) {
                $term_id = $term['term_id'];

                if (in_array($term_id, $specify_ids['include']['category']) || in_array($term_id, $specify_ids['include']['post_tag'])) {
                    $camp_allow = TRUE;
                } elseif (!empty($camp_include_tags)) {
                    $camp_allow = FALSE;
                }

                // for include language
                if ($lang != false) {
                    if (in_array($lang, $specify_ids['include']['language'])) {
                        $camp_allow = TRUE;
                    }
                }
                // for include device
                if(in_array($device, $specify_ids['include']['device'])){
                    $camp_allow = TRUE;
                }
                
                if (in_array($term_id, $specify_ids['exclude']['category']) || in_array($term_id, $specify_ids['exclude']['post_tag'])) {
                    $camp_allow = FALSE;
                }

                // for exclude language
                if ($lang != false) {
                    if (in_array($lang, $specify_ids['exclude']['language'])) {
                        $camp_allow = FALSE;
                    }
                }
                
                // for exclude device
                if(in_array($device, $specify_ids['exclude']['device'])){
                    $camp_allow = FALSE;
                }
                    
            } elseif (!empty($camp_include_tags)) {
                $camp_allow = FALSE;
            }
        } elseif ($apsa_page_info['apsa_is_page'] || $apsa_page_info['apsa_is_single']) {
            $post_terms_ids = array();
            $post_tag_ids = array();
            $post_id = $apsa_page_info['apsa_get_the_ID'];

            $all_taxonomies = $apsa_page_info['apsa_get_taxonomies'];
            unset($all_taxonomies["link_taxonomy"]);
            unset($all_taxonomies["post_format"]);
            unset($all_taxonomies["post_tag"]);

            $post_terms = wp_get_post_terms($apsa_page_info['apsa_get_the_ID'], $all_taxonomies, array('fields' => 'ids'));
            if (!empty($post_terms)) {
                $post_terms_ids = array_merge($post_terms_ids, $post_terms);
            }

            $post_tags = wp_get_post_terms($apsa_page_info['apsa_get_the_ID'], "post_tag", array('fields' => 'ids'));
            if (!empty($post_tags)) {
                $post_tag_ids = array_merge($post_tag_ids, $post_tags);
            }

            $inter_terms = array_intersect($specify_ids['include']['category'], $post_terms_ids);
            $inter_tags = array_intersect($specify_ids['include']['post_tag'], $post_tag_ids);
            if (in_array($post_id, $specify_ids['include']['post']) || in_array($post_id, $specify_ids['include']['page']) || !empty($inter_terms) || !empty($inter_tags)) {
                $camp_allow = TRUE;
            } elseif (!empty($camp_include_tags)) {
                $camp_allow = FALSE;
            }

            // for all pages and posts
            if ($apsa_page_info['apsa_is_page'] && in_array('apsa_all_pages', $specify_ids['include']['page'])) {
                $camp_allow = TRUE;
            }
            if ($apsa_page_info['apsa_is_single'] && in_array('apsa_all_posts', $specify_ids['include']['post'])) {
                $camp_allow = TRUE;
            }
            // for include language
            if ($lang != false) {
                if (in_array($lang, $specify_ids['include']['language'])) {
                    $camp_allow = TRUE;
                }
            }
            // for include device
            if(in_array($device, $specify_ids['include']['device'])){
                $camp_allow = TRUE;
            }

            $inter_terms = array_intersect($specify_ids['exclude']['category'], $post_terms_ids);
            $inter_tags = array_intersect($specify_ids['exclude']['post_tag'], $post_tag_ids);
            if (in_array($post_id, $specify_ids['exclude']['post']) || in_array($post_id, $specify_ids['exclude']['page']) || !empty($inter_terms) || !empty($inter_tags)) {
                $camp_allow = FALSE;
            }

            // for  all pages and posts
            if ($apsa_page_info['apsa_is_page'] && in_array('apsa_all_pages', $specify_ids['exclude']['page'])) {
                $camp_allow = FALSE;
            }
            if ($apsa_page_info['apsa_is_single'] && in_array('apsa_all_posts', $specify_ids['exclude']['post'])) {
                $camp_allow = FALSE;
            }

            // for exclude language
            if ($lang != false) {
                if (in_array($lang, $specify_ids['exclude']['language'])) {
                    $camp_allow = FALSE;
                }
            }
            // for exclude device
            if(in_array($device, $specify_ids['exclude']['device'])){
                $camp_allow = FALSE;
            }
        } else {
            if (!empty($camp_include_tags)) {
                $camp_allow = FALSE;
            }
            
            // for include language
            if ($lang != false) {
                if (in_array($lang, $specify_ids['include']['language'])) {
                    $camp_allow = TRUE;
                }
            }
            // for include device
            if(in_array($device, $specify_ids['include']['device'])){
                $camp_allow = TRUE;
            }
            
            if ($lang != false) {
                if (in_array($lang, $specify_ids['exclude']['language'])) {
                    $camp_allow = FALSE;
                }
            }

            // for exclude device
            if(in_array($device, $specify_ids['exclude']['device'])){
                $camp_allow = FALSE;
            }
        }
    } else {
        $camp_allow = TRUE;
    }
    if ($camp_allow == FALSE) {
        return FALSE;
    }

    /** Separate enabled elements */
    foreach ($campaign_elements as $element_id => $campaign_element) {

        /** Check whether element not expired or disabled */
        if (!empty($elements_options[$element_id]["deadline"])) {
            $elements_options[$element_id]["deadline"] = strtotime($elements_options[$element_id]["deadline"]);
        }

        /** Check whether element not expired or disabled */
        if (!empty($elements_options[$element_id]["schedule"])) {
            $elements_options[$element_id]["schedule"] = strtotime($elements_options[$element_id]["schedule"]);
        }

        /**
         * Check specifing element
         */
        $element_include_tags = isset($elements_options[$element_id]["element_include_tags"]) ? $elements_options[$element_id]["element_include_tags"] : "";
        $element_exclude_tags = isset($elements_options[$element_id]["element_exclude_tags"]) ? $elements_options[$element_id]["element_exclude_tags"] : "";

        $specify_ids['include']['category'] = array();
        $specify_ids['include']['post_tag'] = array();
        $specify_ids['include']['post'] = array();
        $specify_ids['include']['page'] = array();
        $specify_ids['include']['language'] = array();
        $specify_ids['include']['device'] = array();
        $specify_ids['exclude']['category'] = array();
        $specify_ids['exclude']['post_tag'] = array();
        $specify_ids['exclude']['post'] = array();
        $specify_ids['exclude']['page'] = array();
        $specify_ids['exclude']['language'] = array();
        $specify_ids['exclude']['device'] = array();

        if (!empty($element_include_tags) || !empty($element_exclude_tags)) {

            if (!empty($element_include_tags)) {
                $element_include_tags = explode("%" . strtolower($apsa_plugin_data['plugin_data']['name']) . "%,", rtrim($element_include_tags, "%" . strtolower($apsa_plugin_data['plugin_data']['name']) . "%"));
                foreach ($element_include_tags as $key => $element_include_tag) {
                    $element_include_tag = explode("%", $element_include_tag);
                    if ($element_include_tag[0] == "category") {
                        array_push($specify_ids['include']['category'], $element_include_tag[1]);
                    } elseif ($element_include_tag[0] == "post_tag") {
                        array_push($specify_ids['include']['post_tag'], $element_include_tag[1]);
                    } elseif ($element_include_tag[0] == "post") {
                        array_push($specify_ids['include']['post'], $element_include_tag[1]);
                    } elseif ($element_include_tag[0] == "page") {
                        array_push($specify_ids['include']['page'], $element_include_tag[1]);
                    } elseif ($element_include_tag[0] == "language") {
                        array_push($specify_ids['include']['language'], $element_include_tag[1]);
                    } elseif ($element_include_tag[0] == "device") {
                        array_push($specify_ids['include']['device'], $element_include_tag[1]);
                    }
                }
            }

            if (!empty($element_exclude_tags)) {
                $element_exclude_tags = explode("%" . strtolower($apsa_plugin_data['plugin_data']['name']) . "%,", rtrim($element_exclude_tags, "%" . strtolower($apsa_plugin_data['plugin_data']['name']) . "%"));
                foreach ($element_exclude_tags as $key => $element_exclude_tag) {
                    $element_exclude_tag = explode("%", $element_exclude_tag);
                    if ($element_exclude_tag[0] == "category") {
                        array_push($specify_ids['exclude']['category'], $element_exclude_tag[1]);
                    } elseif ($element_exclude_tag[0] == "post_tag") {
                        array_push($specify_ids['exclude']['post_tag'], $element_exclude_tag[1]);
                    } elseif ($element_exclude_tag[0] == "post") {
                        array_push($specify_ids['exclude']['post'], $element_exclude_tag[1]);
                    } elseif ($element_exclude_tag[0] == "page") {
                        array_push($specify_ids['exclude']['page'], $element_exclude_tag[1]);
                    } elseif ($element_exclude_tag[0] == "language") {
                        array_push($specify_ids['exclude']['language'], $element_exclude_tag[1]);
                    } elseif ($element_exclude_tag[0] == "device") {
                        array_push($specify_ids['exclude']['device'], $element_exclude_tag[1]);
                    }
                }
            }
            $element_allow = TRUE;

            /** Check if current page included or excloded */
            if ($apsa_page_info['apsa_is_archive']) {
                $term = $apsa_page_info['apsa_get_queried_object'];

                if (!empty($term)) {
                    $term_id = $term['term_id'];

                    if (in_array($term_id, $specify_ids['include']['category']) || in_array($term_id, $specify_ids['include']['post_tag'])) {
                        $element_allow = TRUE;
                    } elseif (!empty($element_include_tags)) {
                        $element_allow = FALSE;
                    }

                    // for include language
                    if ($lang != false) {
                        if (in_array($lang, $specify_ids['include']['language'])) {
                            $element_allow = TRUE;
                        }
                    }
                    // for include device
                    if(in_array($device, $specify_ids['include']['device'])){
                        $element_allow = TRUE;
                    }

                    if (in_array($term_id, $specify_ids['exclude']['category']) || in_array($term_id, $specify_ids['exclude']['post_tag'])) {
                        $element_allow = FALSE;
                    }

                    // for exclude language
                    if ($lang != false) {
                        if (in_array($lang, $specify_ids['exclude']['language'])) {
                            $element_allow = FALSE;
                        }
                    }

                    // for exclude device
                    if(in_array($device, $specify_ids['exclude']['device'])){
                        $element_allow = FALSE;
                    }
                } elseif (!empty($element_include_tags)) {
                    $element_allow = FALSE;
                }
            } elseif ($apsa_page_info['apsa_is_page'] || $apsa_page_info['apsa_is_single']) {

                $post_terms_ids = array();
                $post_tag_ids = array();
                $post_id = $apsa_page_info['apsa_get_the_ID'];

                $all_taxonomies = $apsa_page_info['apsa_get_taxonomies'];
                unset($all_taxonomies["link_taxonomy"]);
                unset($all_taxonomies["post_format"]);
                unset($all_taxonomies["post_tag"]);

                $post_terms = wp_get_post_terms($apsa_page_info['apsa_get_the_ID'], $all_taxonomies, array('fields' => 'ids'));
                if (!empty($post_terms)) {
                    $post_terms_ids = array_merge($post_terms_ids, $post_terms);
                }

                $post_tags = wp_get_post_terms($apsa_page_info['apsa_get_the_ID'], "post_tag", array('fields' => 'ids'));
                if (!empty($post_tags)) {
                    $post_tag_ids = array_merge($post_tag_ids, $post_tags);
                }


                $inter_terms = array_intersect($specify_ids['include']['category'], $post_terms_ids);
                $inter_tags = array_intersect($specify_ids['include']['post_tag'], $post_tag_ids);
                if (in_array($post_id, $specify_ids['include']['post']) || in_array($post_id, $specify_ids['include']['page']) || !empty($inter_terms) || !empty($inter_tags)) {
                    $element_allow = TRUE;
                } elseif (!empty($element_include_tags)) {
                    $element_allow = FALSE;
                }

                // for  all pages and posts
                if ($apsa_page_info['apsa_is_page'] && in_array('apsa_all_pages', $specify_ids['include']['page'])) {
                    $element_allow = TRUE;
                }
                if ($apsa_page_info['apsa_is_single'] && in_array('apsa_all_posts', $specify_ids['include']['post'])) {
                    $element_allow = TRUE;
                }

                // for include language
                if ($lang != false) {
                    if (in_array($lang, $specify_ids['include']['language'])) {
                        $element_allow = TRUE;
                    }
                }
                // for include device
                if(in_array($device, $specify_ids['include']['device'])){
                    $element_allow = TRUE;
                }

                $inter_terms = array_intersect($specify_ids['exclude']['category'], $post_terms_ids);
                $inter_tags = array_intersect($specify_ids['exclude']['post_tag'], $post_tag_ids);
                if (in_array($post_id, $specify_ids['exclude']['post']) || in_array($post_id, $specify_ids['exclude']['page']) || !empty($inter_terms) || !empty($inter_tags)) {
                    $element_allow = FALSE;
                }

                // for  all pages and posts
                if ($apsa_page_info['apsa_is_page'] && in_array('apsa_all_pages', $specify_ids['exclude']['page'])) {
                    $element_allow = FALSE;
                }
                if ($apsa_page_info['apsa_is_single'] && in_array('apsa_all_posts', $specify_ids['exclude']['post'])) {
                    $element_allow = FALSE;
                }

                // for exclude language
                if ($lang != false) {
                    if (in_array($lang, $specify_ids['exclude']['language'])) {
                        $element_allow = FALSE;
                    }
                }

                // for exclude device
                if(in_array($device, $specify_ids['exclude']['device'])){
                    $element_allow = FALSE;
                }
            } else {
                if (!empty($element_include_tags)) {
                    $element_allow = FALSE;
                }
                // for include language
                if ($lang != false) {
                    if (in_array($lang, $specify_ids['include']['language'])) {
                        $element_allow = TRUE;
                    }
                }
                // for include device
                if(in_array($device, $specify_ids['include']['device'])){
                    $element_allow = TRUE;
                }
                
                if ($lang != false) {
                    if (in_array($lang, $specify_ids['exclude']['language'])) {
                        $element_allow = FALSE;
                    }
                }

                // for exclude device
                if(in_array($device, $specify_ids['exclude']['device'])){
                    $element_allow = FALSE;
                }
            }
        } else {
            $element_allow = TRUE;
        }

        $current_date = date('Y-m-d', current_time('timestamp'));
        $element_deadline = isset($elements_options[$element_id]["deadline"]) ? $elements_options[$element_id]["deadline"] : "";
        $element_schedule = isset($elements_options[$element_id]["schedule"]) ? $elements_options[$element_id]["schedule"] : "";
        $element_restrict_fr_event = isset($elements_options[$element_id]["restrict_views"]) ? $elements_options[$element_id]["restrict_views"] : "";
        $element_fr_event = isset($elements_stat[$element_id][$apsa_fr_event_name]) ? $elements_stat[$element_id][$apsa_fr_event_name] : 0;
        if ((!empty($element_deadline) && strtotime($current_date) > $element_deadline) || (!empty($element_schedule) && strtotime($current_date) < $element_schedule) || (!empty($element_restrict_fr_event) && $element_fr_event >= $element_restrict_fr_event)) {
            $element_allow = false;
        }

        // checking child element visibility
        $apsa_check_child_visibility = TRUE;
        if (function_exists('apsa_check_child_visibility')) {
            $apsa_stat = isset($elements_stat[$element_id]) ? $elements_stat[$element_id] : '';
            $apsa_el_options = isset($elements_options[$element_id]) ? $elements_options[$element_id] : '';
            $apsa_check_child_visibility = apsa_check_child_visibility($campaign, $camp_options, $campaign_element, $apsa_el_options, $apsa_stat, $content);
        }

        if ($element_allow == FALSE || $apsa_check_child_visibility == FALSE) {
            unset($campaign_elements[$element_id]);
        }
    }

    if (empty($campaign_elements)) {
        return false;
    } else {
        return $campaign_elements;
    }
}

/**
 * Get language code if plugin installed(qTranslate, qTranslate X or WPLML)
 * 
 * @return bool|string False if plugin not available or language code
 */
function apsa_get_language() {

    static $lang = null;
    if ($lang !== null) {
        return $lang;
    }

    $lang = false;
    if(function_exists('pll_current_language')){
        $lang = mb_strtolower(pll_current_language());
    }else if (defined('ICL_LANGUAGE_CODE')) {
        $lang = mb_strtolower(ICL_LANGUAGE_CODE);
    }else if (isset($GLOBALS['q_config'])) {
        $lang = mb_strtolower($GLOBALS['q_config']['language']);
    } 
    return $lang;
}

/**
* Get device code
* 
* @return string device code
*/

function apsa_get_device() {
    
    static $device = null;
    if ($device !== null) {
        return $device;
    }
    include_once 'mobile-detect/apsa-mobile-detect.php';
    $detect = new APSA_Mobile_Detect;
    if ($detect->isTablet()) {
        $device = 'tab';
    } else if ($detect->isMobile()) {
        $device = 'mob';
    } else {
        $device = 'des';
    }
    return $device;
}

/**
 * Check if cach plugin is instaled
 * 
 * @return bool
 */
function apsa_cache_installed() {

    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }

    $active_plugins = get_option('active_plugins');
    $plugins_data = get_plugins();
    $cache = false;
    global $apsa_cache_plugins;
    foreach ($active_plugins as $active_plugin) {
        foreach ($apsa_cache_plugins as $apsa_cache_plugin) {
            if ($plugins_data[$active_plugin]["Name"] == $apsa_cache_plugin) {
                $cache = true;
                break;
            }
        }
    }
    return $cache;
}

/**
 * Check if visual composer methods are available
 * 
 * @return bool
 */
function apsa_vc_available() {

    if (function_exists('vc_shortcode_custom_css_class') && defined('VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG')) {
        return true;
    } else {
        return false;
    }
}
/**
* Check if qTranslateX methods are available
*
* @return bool
*/
function apsa_qtranslateX_available() {
   if (function_exists('qtranxf_getLanguage')) {
       return true;
   } else {
       return false;
   }
}
/**
 * Check if qTranslate(not X) methods are available
 * 
 * @return bool
 */
function apsa_qtranslate_old_available() {

    if (function_exists('qtrans_getLanguage') && !function_exists('qtranxf_getLanguage')) {
        return true;
    } else {
        return false;
    }
}

/**
 * Get enabled languages
 * 
 * @return bool|array False if plugin not available or languages array
 */
function apsa_get_enabled_languages() {

    static $languages = null;
    if ($languages !== null) {
        return $languages;
    }

    $languages = false;
    if(function_exists('pll_languages_list')){
        $pll_languages = pll_languages_list(array('fields' => array()));
        foreach ($pll_languages as $pll_language) {
            $code = mb_strtolower($pll_language->slug);
            $languages[$code] = $pll_language->name;
        }
    }else if (function_exists('icl_get_languages')) {
        $wpml_languages = icl_get_languages();
        $languages = array();
        foreach ($wpml_languages as $wpml_language) {
            $code = mb_strtolower($wpml_language['code']);
            $languages[$code] = $wpml_language['native_name'];
        }
    }
    if (isset($GLOBALS['q_config'])) {
        $q_language_names = $GLOBALS['q_config']['language_name'];
        $q_languages = $GLOBALS['q_config']['enabled_languages'];
        foreach ($q_languages as $key => $value) {
            $code = mb_strtolower($value);
            $languages[$code] = $q_language_names[$value];
        }
    }
    return $languages;
}

/**
 * Get enabled devices
 * 
 * @return bool|array False if devices not available or devices array
 */
function apsa_get_enabled_devices(){
    global $apsa_admin_labels;
    $devices = array(array('code' => 'des', 'name' => $apsa_admin_labels["tagsinput_desktop"]), array('code' => 'tab', 'name' => $apsa_admin_labels["tagsinput_tablet"]), array('code' => 'mob', 'name' => $apsa_admin_labels["tagsinput_mobile"]));
    return $devices;
}