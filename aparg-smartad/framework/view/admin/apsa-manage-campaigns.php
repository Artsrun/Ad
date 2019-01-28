<?php
defined('ABSPATH') or die('No script kiddies please!');
/**
 * Add framework managing page in admin
 */
//Add menu item for plugin

add_action('admin_menu', 'apsa_compains_managing');

function apsa_compains_managing() {
    global $apsa_plugin_data;
    add_menu_page('Manage campaigns', $apsa_plugin_data['plugin_data']['name'], 'manage_options', 'apsa-manage-campaigns', '', $apsa_plugin_data['plugin_data']['icon_path']);
}

//Add menu item for settings and campaigns managing in plugin
add_action('admin_menu', 'apsa_settings_page_managing');

function apsa_settings_page_managing() {
    global $apsa_admin_labels;
    add_submenu_page('apsa-manage-campaigns', $apsa_admin_labels["campaigns_page"], $apsa_admin_labels["campaigns"], 'manage_options', 'apsa-manage-campaigns', 'apsa_campaigns_manage_page');
    add_submenu_page('apsa-manage-campaigns', $apsa_admin_labels["general_settings_page"], $apsa_admin_labels["general_settings"], 'manage_options', 'apsa-manage-general-settings', 'apsa_general_settings_page');
}

/**
 * create general settings page 
 */
function apsa_general_settings_page() {
    global $apsa_admin_labels;
    global $apsa_plugin_data;

    // save settings
    if (isset($_POST['apsa-update-settings'])) {
        $apsa_old_extra_options = get_option('apsa_extra_options');
        if (isset($_POST['apsa-cache-enable'])) {
            $apsa_old_extra_options['apsa_cache_enabled'] = $_POST['apsa-cache-enable'];
        } else {
            $apsa_old_extra_options['apsa_cache_enabled'] = 'false';
        }
        if (isset($_POST['apsa-custom-css'])) {
            $apsa_old_extra_options['apsa_custom_css'] = $_POST['apsa-custom-css'];
        }
        update_option('apsa_extra_options', $apsa_old_extra_options);
        echo '<div class="updated fade apsa-success-update"><p><strong>' . $apsa_admin_labels["success_update_msg"] . '</strong></p></div>';
    }

    $wp_version = get_bloginfo('version');
    $apsa_extra_options = get_option('apsa_extra_options');
    ?>
    <!-- Messages -->
    <div class="apsa-popup apsa-transit-450" id="apsa-message-popup" data-apsa-open="false">
        <p></p>
        <span class="apsa-close-popup"></span>
    </div>
    <form method="POST" action="" enctype="multipart/form-data">
        <div id="apsa-managing-wrap" <?php echo (($wp_version >= 3.8) ? 'class="apsa-mobile-admin"' : ""); ?>>
            <!-- Header -->
            <h2 id="apsa-element-campaign-header">
                <span><?php echo $apsa_admin_labels["general_settings"]; ?></span>
                <div id="apsa-save-camps-cont">
                    <div class="apsa-waiting-wrapper">
                        <button class="button button-primary" id="apsa-update-settings" name="apsa-update-settings"><?php echo $apsa_admin_labels["update_all"]; ?></button>
                    </div>
                </div>
            </h2>
            <div><span class="apsa-by-aparg"><?php echo $apsa_admin_labels["developed_by"]; ?> <a href="<?php echo APSA_APARG_LINK ?>" target="blank">Aparg.</a></span></div>
            <?php if ($apsa_plugin_data['anticache'] == 'true'): ?>        
                <div id="apsa-anticache">
                    <label for="apsa-cache-enable"><?php echo $apsa_admin_labels["anticache"]; ?></label> 
                    <input type="checkbox" value="true" name="apsa-cache-enable" id="apsa-cache-enable" <?php checked('true', $apsa_extra_options['apsa_cache_enabled'], true); ?> />
                    <span class="apsa-with-question" title="<?php echo ucfirst($apsa_admin_labels["detailed_description"]); ?>" data-apsa-message="<?php echo $apsa_admin_labels['anticache_desc']; ?>"></span>
                </div>
            <?php endif; ?>
            <!-- Custom Css -->
            <div>
                <div id="apsa-custom-css-new" class="apsa-code-type">
                    <label><?php echo $apsa_admin_labels["custom_css"]; ?></label>
                    <div class="apsa-code-type-textarea">
                        <textarea id="apsa-code-area" class="apsa-code-area" name="apsa-custom-css"></textarea>
                        <textarea class="apsa-hold-code-content apsa-hidden-textarea" id="apsa-custom-css-value"><?php echo stripslashes($apsa_extra_options['apsa_custom_css']); ?></textarea>
                    </div>
                </div>
            </div>    
        </div>
    </form>
    <?php
}

/**
 * Create compains manage page forms
 */
function apsa_campaigns_manage_page() {
    global $apsa_plugin_data;
    global $apsa_admin_labels;
    global $apsa_fr_event_name;
    /** Get All campaigns, also determine active and suspended campaigns count */
    $get_campaigns = apsa_get_campaigns();
    $campaigns = array();
    $active_count = 0;
    $suspended_count = 0;
    if (!empty($get_campaigns)) {
        foreach ($get_campaigns as $camp_data) {
            $campaigns[$camp_data['id']] = $camp_data;

            /** Initialize additional fields */
            $campaigns[$camp_data['id']]['total_fr_event'] = 0;
            $campaigns[$camp_data['id']]['total_child_event'] = 0;
            $campaigns[$camp_data['id']]['elements_count'] = 0;
            $campaigns[$camp_data['id']]['active_elements_count'] = 0;
            $campaigns[$camp_data['id']]['suspend_elements_count'] = 0;

            if ($camp_data['status'] == 'active') {
                $active_count++;
                $campaigns[$camp_data['id']]['status_name'] = $apsa_admin_labels["status_active"];
            } elseif ($camp_data['status'] == 'suspended') {
                $suspended_count++;
                $campaigns[$camp_data['id']]['status_name'] = $apsa_admin_labels["status_suspended"];
            }
        }
    }

    /** Get all campaign options */
    $get_campaign_options = apsa_get_all_campaign_options();
    $camp_options = array();

    if (!empty($get_campaign_options)) {
        foreach ($get_campaign_options as $campaign_option) {
            $camp_options[$campaign_option['campaign_id']][$campaign_option['option_name']] = $campaign_option['option_value'];
        }
    }

    /** Get all elements statistics */
    $get_elements_stat = apsa_get_element_stat_counts();
    $elements_stat = array();
    if (!empty($get_elements_stat)) {
        foreach ($get_elements_stat as $get_element_stat) {
            $elements_stat[$get_element_stat['element_id']][$get_element_stat['type']] = $get_element_stat['total_count'];
        }
    }

    /** Get all elements, also calculate campaigns elements total events count, active and suspended elements count */
    $get_elements = apsa_get_elements();


    $elements = array();
    if (!empty($get_elements)) {
        foreach ($get_elements as $element_data) {
            $elements[$element_data['campaign_id']][$element_data['id']] = $element_data;

            /** Add events counts to parent campaign totals */
            $element_fr_events = (isset($elements_stat[$element_data['id']][$apsa_fr_event_name])) ? $elements_stat[$element_data['id']][$apsa_fr_event_name] : 0;
            $element_child_events = (isset($elements_stat[$element_data['id']][$apsa_plugin_data['event_name']])) ? $elements_stat[$element_data['id']][$apsa_plugin_data['event_name']] : 0;

            $campaigns[$element_data['campaign_id']]['total_fr_event'] += $element_fr_events;
            $campaigns[$element_data['campaign_id']]['total_child_event'] += $element_child_events;

            /** Check if element active or suspend, and increment respective counter of parent campaign, also elements */
            if ($element_data['status'] == 'active') {
                $campaigns[$element_data['campaign_id']]['active_elements_count'] ++;
                $elements[$element_data['campaign_id']][$element_data['id']]['status_name'] = $apsa_admin_labels["status_active"];
            } elseif ($element_data['status'] == 'suspended') {
                $campaigns[$element_data['campaign_id']]['suspend_elements_count'] ++;
                $elements[$element_data['campaign_id']][$element_data['id']]['status_name'] = $apsa_admin_labels["status_suspended"];
            }
            $campaigns[$element_data['campaign_id']]['elements_count'] ++;
        }
    }

    /** Get all element options */
    $get_element_options = apsa_get_all_element_options();
    $element_options = array();
    if (!empty($get_element_options)) {
        foreach ($get_element_options as $element_option) {
            $element_options[$element_option['element_id']][$element_option['option_name']] = $element_option['option_value'];
        }
    }
    $apsa_extra_options = get_option('apsa_extra_options');
    /** Get or set required variables */
    // Popup, embed animations and patterns
    global $apsa_effects;
    $apsa_popup_effects = $apsa_effects['popup'];
    $apsa_embed_effects = $apsa_effects['embed'];
    $apsa_sticky_effects = $apsa_effects['sticky'];
    $apsa_sticky_positions = $apsa_effects['sticky_positions'];
    $apsa_overlay_patterns = $apsa_effects['patterns'];
    ?>
    <div id="apsa-managing-overlay"></div>
    <?php $wp_version = get_bloginfo('version'); ?>
    <div id="apsa-managing-wrap" <?php echo (($wp_version >= 3.8) ? 'class="apsa-mobile-admin"' : ""); ?>>
        <!-- Popup for adding new campaign -->
        <div class="apsa-popup apsa-wp-popup apsa-transit-150" id="apsa-add-campaign-popup" data-apsa-open="false">
            <div class="apsa-popup-header">
                <span class="apsa-popup-title"><?php echo $apsa_admin_labels["choose_campaign_type"]; ?></span>
                <span class="apsa-close-popup"></span>
            </div>
            <div class="apsa-popup-cont">
                <div id="apsa-campaign-types"></div>
            </div>
            <div class="apsa-popup-footer">
                <button class="button-primary" id="apsa-campaign-save"><?php echo $apsa_admin_labels["add"]; ?></button>
            </div>
        </div>
        <!-- Popup for choose new element type -->
        <div class="apsa-popup apsa-wp-popup apsa-transit-150" id="apsa-add-element-popup" data-apsa-open="false">
            <div class="apsa-popup-header">
                <span class="apsa-popup-title"><?php echo $apsa_admin_labels['choose_element_type']; ?></span>
                <span class="apsa-close-popup"></span>
            </div>
            <div class="apsa-popup-cont">
                <div id="apsa-element-types"></div>
            </div>
            <div class="apsa-popup-footer">
                <button class="button-primary" id="apsa-element-save"><?php echo $apsa_admin_labels["add"]; ?></button>
            </div>
        </div>
        <?php
        $apsa_check_code_type = false;
        if (!empty($apsa_plugin_data['element_data'])) {
            foreach ($apsa_plugin_data['element_data'] as $apsa_element_type) {
                if (in_array('code', $apsa_element_type))
                    $apsa_check_code_type = true;
            }
        }
        if ($apsa_check_code_type):
            ?>
            <!-- Popup for type code -->
            <div class="apsa-popup apsa-wp-popup apsa-transit-150" id="apsa-code-popup" data-apsa-open="false">
                <div class="apsa-popup-header">
                    <span class="apsa-popup-title"><?php echo $apsa_admin_labels["enter_code"]; ?></span>
                    <span class="apsa-close-popup"></span>
                </div>
                <textarea id="apsa-code-area"></textarea>
                <div class="apsa-popup-footer">
                    <button id="apsa-save-code" class="button button-primary"><?php echo $apsa_admin_labels["ok"]; ?></button>
                </div>
            </div>
        <?php endif; ?>
        <!-- Confirmation messages -->
        <div class="apsa-popup apsa-wp-popup apsa-transit-150" id="apsa-confirm-message" data-apsa-open="false">
            <div class="apsa-popup-header">
                <span class="apsa-popup-title"></span>
                <span class="apsa-close-popup"></span>
            </div>
            <div class="apsa-popup-cont">
                <p id="apsa-confirm-text"></p>
            </div>
            <div class="apsa-popup-footer">
                <ul id="apsa-confirm-options"></ul> 
            </div>
        </div>
        <!-- Export ranged date-->
        <div class="apsa-popup apsa-wp-popup apsa-transit-150" id="apsa-export-range" data-apsa-open="false">
            <div class="apsa-popup-header">
                <span class="apsa-popup-title"><?php echo $apsa_admin_labels["choose_period"]; ?></span>
                <span class="apsa-close-popup"></span>
            </div>
            <div class="apsa-popup-cont">
                <div class="apsa-stat-filter">
                    <input type="text" class="apsa-stat-from" placeholder="<?php echo $apsa_admin_labels["stat_from_place"]; ?>" />
                    <input type="text" class="apsa-stat-to" placeholder="<?php echo $apsa_admin_labels["stat_to_place"]; ?>" />
                </div>
            </div>
            <div class="apsa-popup-footer">
                <div class="apsa-waiting-wrapper">
                    <button id="apsa-export-period-stats" class="button button-primary"><?php echo $apsa_admin_labels["export"]; ?></button>
                </div>
            </div>
        </div>
        <!-- Messages -->
        <div class="apsa-popup apsa-transit-450" id="apsa-message-popup" data-apsa-open="false">
            <p></p>
            <span class="apsa-close-popup"></span>
        </div>
        <!-- Button for add new campaign -->
        <h2 id="apsa-element-campaign-header">
            <span><?php echo $apsa_admin_labels["campaigns"]; ?></span>
            <button type="button" class="button apsa-add-campaign apsa-new" id="apsa-add-campaign"><?php echo $apsa_admin_labels["add_new_campaign"]; ?></button>
            <button type="button" class="button apsa-export-all apsa-export-range <?php if (empty($campaigns)): ?> apsa-all-action-hidden <?php endif; ?>" id="apsa-export-all"><?php echo $apsa_admin_labels["export_full_stats"]; ?></button>
            <div id="apsa-save-camps-cont">
                <div class="apsa-waiting-wrapper">
                    <button type="button" class="button button-primary <?php if (empty($campaigns)): ?> apsa-all-action-hidden <?php endif; ?>" id="apsa-update-all"><?php echo $apsa_admin_labels["update_all"]; ?></button>
                </div>
            </div>
        </h2>
        <!-- Filter campaigns by status -->
        <?php
        /** Get campaigns count */
        $all_count = count($campaigns);
        ?>
        <ul class="apsa-campaigns-filter">
            <li data-apsa-filter="all">
                <span class="apsa-status-name apsa-selected-status"><?php echo $apsa_admin_labels["filter_campaign_all"]; ?></span>
                <span class="apsa-status-count"><?php echo $all_count ?></span>
            </li>
            <li class="apsa-inside-li" data-apsa-filter="active">
                <span class="apsa-status-name"><?php echo $apsa_admin_labels["filter_campaign_active"]; ?></span>
                <span class="apsa-status-count"><?php echo $active_count ?></span>
            </li>
            <li data-apsa-filter="suspended">
                <span class="apsa-status-name"><?php echo $apsa_admin_labels["filter_campaign_suspended"]; ?></span>
                <span class="apsa-status-count"><?php echo $suspended_count ?></span>
            </li>
        </ul>
        <span id="apsa-by-aparg" class="apsa-by-aparg"><?php echo $apsa_admin_labels["developed_by"]; ?> <a href="<?php echo APSA_APARG_LINK ?>" target="blank">Aparg.</a></span>
        <ul id="apsa-campaigns-block">
            <?php
            /**
             * Show all campaigns 
             */
            /** Check if campaigns exists display else say it is not */
            if (!empty($campaigns)) {
                foreach ($campaigns as $camp_id => $campaign) {

                    // Get human creation date
                    $camp_creation_date = date_i18n(get_option('date_format'), strtotime($campaign["creation_date"]));
                    
                    /** Get statistics */
                    $elements_count = isset($campaign['elements_count']) ? $campaign['elements_count'] : 0;
                    $fr_event_count = isset($campaign['total_fr_event']) ? $campaign['total_fr_event'] : 0;
                    $child_event_count = isset($campaign['total_child_event']) ? $campaign['total_child_event'] : 0;

                    $elements_kilo = $elements_count;
                    if ($elements_count >= 1000) {
                        $elements_kilo = round($elements_count / 1000, 1);
                        $elements_kilo .= "K";
                    }

                    $fr_event_kilo = $fr_event_count;
                    if ($fr_event_count >= 1000) {
                        $fr_event_kilo = round($fr_event_count / 1000, 1);
                        $fr_event_kilo .= "K";
                    }

                    $child_event_kilo = $child_event_count;
                    if ($child_event_count >= 1000) {
                        $child_event_kilo = round($child_event_count / 1000, 1);
                        $child_event_kilo .= "K";
                    }
                    ?>
                    <li class="apsa-campaign-block apsa-slide-cont" data-apsa-camp-type="<?php echo $campaign['type']; ?>" data-apsa-status="<?php echo $campaign['status']; ?>" data-apsa-campaign-id="<?php echo $campaign['id']; ?>" id="apsa-campaign-block-<?php echo $campaign['id']; ?>">
                        <div class="apsa-campaign-header apsa-slide-opener" data-apsa-open-slide="false">
                            <div class="apsa-campaign-type-logo" data-apsa-campaign-type="<?php echo $campaign['type']; ?>"></div>
                            <div class="apsa-camp-info">
                                <input type="text" class="apsa-suspended-input apsa-campaign-name" value="<?php echo $campaign['name']; ?>" title="<?php echo $campaign['name']; ?>" />
                                <p class="apsa-dash-pub-date"><span><?php echo $apsa_admin_labels["creation_date"]; ?></span>&nbsp;<?php echo $camp_creation_date; ?></p>
                            </div>
                            <span class="apsa-campaign-status <?php echo "apsa-camp-status-" . $campaign['status'] ?>"><?php echo $campaign['status_name']; ?></span>
                            <ul class="apsa-camp-data">
                                <li class="apsa-fr-event-count">
                                    <div><?php echo $fr_event_kilo; ?></div>
                                    <div><?php echo $apsa_admin_labels["camp_event_count"]; ?></div>
                                </li>
                                <li class="apsa-child-event-count apsa-inside-data">
                                    <div><?php echo $child_event_kilo; ?></div>
                                    <div><?php echo $apsa_admin_labels["camp_event_count_child"]; ?></div>
                                </li>
                                <li class="apsa-elements-count">
                                    <div><?php echo $elements_kilo; ?></div>
                                    <div><?php echo $apsa_admin_labels['element_plural_name'] ?></div>
                                </li>
                            </ul>
                            <span class="apsa-slide-open-pointer"></span>                                   
                        </div>
                        <div class="apsa-campaign-content apsa-sliding-block" data-apsa-open="false">                            
                            <!-- Display campaign elements -->
                            <div class="apsa-campaign-elements">
                                <div class="apsa-elements-header">
                                    <h3><?php echo $apsa_admin_labels['element_plural_name'] ?></h3>
                                    <ul class="apsa-elements-filter">
                                        <li data-apsa-element-filter="all">
                                            <span class="apsa-element-status-name apsa-selected-status"><?php echo $apsa_admin_labels["filter_element_all"]; ?></span>
                                            <span class="apsa-status-count"><?php echo $campaign['elements_count'] ?></span>
                                        </li>
                                        <li class="apsa-inside-li" data-apsa-element-filter="active">
                                            <span class="apsa-element-status-name"><?php echo $apsa_admin_labels["filter_element_active"]; ?></span>
                                            <span class="apsa-status-count"><?php echo $campaign['active_elements_count'] ?></span>
                                        </li>
                                        <li data-apsa-element-filter="suspended">
                                            <span class="apsa-element-status-name"><?php echo $apsa_admin_labels["filter_element_suspended"]; ?></span>
                                            <span class="apsa-status-count"><?php echo $campaign['suspend_elements_count'] ?></span>
                                        </li>
                                    </ul>
                                    <div class="apsa-waiting-wrapper apsa-add-element-wrap">
                                        <button class="button button-primary apsa-add-element apsa-new" data-apsa-campaign-id="<?php echo $campaign['id']; ?>" data-apsa-campaign-type="<?php echo $campaign['type']; ?>"><?php echo $apsa_admin_labels["add_new_element"]; ?></button>
                                    </div>
                                </div>
                                <?php ?>
                                <ul class="apsa-elements-list apsa-sortable">                                
                                    <?php
                                    $campaign_elements = (!empty($elements[$camp_id])) ? $elements[$camp_id] : FALSE;
                                    if (!empty($campaign_elements)) {
                                        foreach ($campaign_elements as $element_id => $campaign_element) {
                                            $element_creation_date = date_i18n(get_option('date_format'), strtotime($campaign_element["creation_date"]));
                                            $element_status = $campaign_element["status"];
                                            $element_status_name = $campaign_element["status_name"];
                                            $element_type = $campaign_element["type"];
                                            $current_date = strtotime(date('Y-m-d', current_time('timestamp')));
                                            $element_deadline = isset($element_options[$element_id]['deadline']) ? $element_options[$element_id]['deadline'] : "";
                                            $deadline_date = '';
                                            if (!empty($element_deadline)) {
                                                $deadline_date = strtotime($element_deadline);
                                            }
                                            $element_schedule = isset($element_options[$element_id]['schedule']) ? $element_options[$element_id]['schedule'] : "";
                                            $schedule_date = '';
                                            if (!empty($element_schedule)) {
                                                $schedule_date = strtotime($element_schedule);
                                            }
                                            /** Get statistics */
                                            $fr_event_count = isset($elements_stat[$element_id][$apsa_fr_event_name]) ? $elements_stat[$element_id][$apsa_fr_event_name] : 0;
                                            $child_event_count = isset($elements_stat[$element_id][$apsa_plugin_data['event_name']]) ? $elements_stat[$element_id][$apsa_plugin_data['event_name']] : 0;

                                            if (empty($fr_event_count)) {
                                                $crt = 0;
                                            } else {
                                                $crt = round($child_event_count / $fr_event_count * 100);
                                            }

                                            $fr_event_kilo = $fr_event_count;
                                            if ($fr_event_count >= 1000) {
                                                $fr_event_kilo = round($fr_event_count / 1000, 1);
                                                $fr_event_kilo .= "K";
                                            }

                                            $child_event_kilo = $child_event_count;
                                            if ($child_event_count >= 1000) {
                                                $child_event_kilo = round($child_event_count / 1000, 1);
                                                $child_event_kilo .= "K";
                                            }

                                            if (!empty($schedule_date) && $current_date < $schedule_date) {
                                                $schedule_text = $apsa_admin_labels['schedule_warning_message'];
                                                $warning_schedule_class = 'apsa-warning-message';
                                            } else {
                                                $schedule_text = $apsa_admin_labels['default'] . ' ' . $apsa_admin_labels['schedule_def'];
                                                $warning_schedule_class = '';
                                            }

                                            if (!empty($deadline_date) && $current_date > $deadline_date) {
                                                $deadline_text = $apsa_admin_labels['deadline_warning_message'];
                                                $warning_deadline_class = 'apsa-warning-message';
                                            } else {
                                                $deadline_text = $apsa_admin_labels['default'] . ' ' . $apsa_admin_labels['deadline_def'];
                                                $warning_deadline_class = '';
                                            }

                                            $restrict_fr_event = isset($element_options[$element_id]['restrict_views']) ? $element_options[$element_id]['restrict_views'] : "";
                                            if (trim($restrict_fr_event) != "" && $fr_event_count >= $restrict_fr_event) {
                                                $fr_event_text = $apsa_admin_labels['views_warning_message'];
                                                $warning_fr_event_class = 'apsa-warning-message';
                                            } else {
                                                $fr_event_text = $apsa_admin_labels['default'] . ' ' . $apsa_admin_labels['max_views_def'];
                                                $warning_fr_event_class = '';
                                            }
                                            ?>
                                            <li id="apsa-element-block-<?php echo $element_id; ?>" class="apsa-element-block" data-apsa-status="<?php echo $element_status; ?>" data-apsa-element-id="<?php echo $element_id; ?>">
                                                <form class="apsa-element-form">
                                                    <input type="hidden" name="action" class="apsa-element-action apsa-fr-option" value="update"/>
                                                    <input type="hidden" name="campaign_id" class="apsa-fr-option" value="<?php echo $campaign['id']; ?>"/>
                                                    <input type="hidden" name="priority" class="apsa-element-priority  apsa-fr-option" value="<?php echo $campaign_element['priority']; ?>"/>
                                                    <input type="hidden" name="element_id" class="apsa-fr-option" value="<?php echo $element_id; ?>"/>
                                                    <div class="apsa-element-controls">
                                                        <div class="apsa-sort-mover" title="<?php echo ucfirst($apsa_admin_labels["sort_mover_tile"]) ?>"></div>
                                                        <ul class="apsa-actions apsa-element-actions">
                                                            <?php if ($element_status == "suspended"): ?>
                                                                <li data-apsa-action="activate" title="<?php echo ucfirst($apsa_admin_labels["activate_element"]) ?>">
                                                                    <span class="apsa-action-name apsa-dash-activate"></span>
                                                                </li>
                                                            <?php else : ?>
                                                                <li data-apsa-action="suspend" title="<?php echo ucfirst($apsa_admin_labels["suspend_element"]) ?>">
                                                                    <span class="apsa-action-name apsa-dash-suspend"></span>
                                                                </li>
                                                            <?php endif; ?>
                                                            <li data-apsa-action="delete" title="<?php echo ucfirst($apsa_admin_labels["delete_element"]) ?>">
                                                                <span class="apsa-action-name apsa-dash-delete"></span>
                                                            </li>
                                                        </ul>
                                                        <div class="apsa-element-header">
                                                            <div class="apsa-element-type-logo" data-apsa-element-type="<?php echo $campaign_element['type']; ?>">
                                                                <input type="hidden" name="type" class="apsa-fr-option" value="<?php echo $campaign_element['type']; ?>"/>
                                                            </div>
                                                            <div class="apsa-element-info">
                                                                <input type="text" class="apsa-suspended-input apsa-element-name apsa-fr-option" name="title" value="<?php echo $campaign_element['title']; ?>" title="<?php echo $campaign_element['title']; ?>" />
                                                                <p class="apsa-dash-pub-date"><span><?php echo $apsa_admin_labels["creation_date"]; ?></span>&nbsp;<?php echo $element_creation_date; ?></p>
                                                            </div>                                                       
                                                            <span class="apsa-element-status apsa-element-status-<?php echo $element_status; ?>"><?php echo $element_status_name; ?></span>
                                                            <ul class="apsa-element-data">
                                                                <li class="apsa-fr-event-count">
                                                                    <div><?php echo $fr_event_kilo; ?></div>
                                                                    <div><?php echo $apsa_admin_labels["element_event_count"]; ?></div>
                                                                </li>
                                                                <li class="apsa-child-event-count apsa-inside-data">
                                                                    <div><?php echo $child_event_kilo; ?></div>
                                                                    <div><?php echo $apsa_admin_labels["element_event_count_child"]; ?></div>
                                                                </li>
                                                                <li class="apsa-crt">
                                                                    <div><?php echo $crt; ?></div>
                                                                    <div><?php echo $apsa_admin_labels["element_rate"]; ?>(%)</div>
                                                                </li>
                                                            </ul>                                                        
                                                        </div>
                                                        <div class="apsa-tags-inputs">
                                                            <span class="apsa-tags-label"><?php echo $apsa_admin_labels["include"]; ?></span>
                                                            <span class="apsa-with-question" title="<?php echo ucfirst($apsa_admin_labels["detailed_description"]) ?>" data-apsa-message='<?php echo $apsa_admin_labels["include_element_desc"]; ?>
                                                                  <br><br><strong><?php echo $apsa_admin_labels["specify_tag"]; ?> - <span style="color: #F0AD4E"><?php echo $apsa_admin_labels["yellow"]; ?></span></strong>
                                                                  <br><strong><?php echo $apsa_admin_labels["specify_category"]; ?> - <span style="color: #337AB7"><?php echo $apsa_admin_labels["blue"]; ?></span></strong>
                                                                  <br><strong><?php echo $apsa_admin_labels["specify_post"]; ?> - <span style="color: #D9534F"><?php echo $apsa_admin_labels["red"]; ?></span></strong>
                                                                  <br><strong><?php echo $apsa_admin_labels["specify_page"]; ?> - <span style="color: #5CB85C"><?php echo $apsa_admin_labels["green"]; ?></span></strong>
                                                                  <br><strong><?php echo $apsa_admin_labels["specify_language"]; ?> - <span style="color: #5bc0de"><?php echo $apsa_admin_labels["light_blue"]; ?></span></strong>
                                                                  <br><strong><?php echo $apsa_admin_labels["specify_device"]; ?> - <span style="color: #aaaaaa"><?php echo $apsa_admin_labels["grey"]; ?></span></strong>'></span>
                                                            <div class="apsa-tags">
                                                                <input type="hidden" class="apsa-tags-values" data-apsa-tags-values="<?php echo isset($element_options[$element_id]["element_include_tags"]) ? $element_options[$element_id]["element_include_tags"] : ""; ?>" />
                                                                <div class="apsa_tag_include">
                                                                    <input type="text" class="typeahead-auto"/>
                                                                </div>
                                                            </div>
                                                            <div class="apsa-slide-cont">
                                                                <?php $element_exclude_tags = isset($element_options[$element_id]["element_exclude_tags"]) ? $element_options[$element_id]["element_exclude_tags"] : "" ?>
                                                                <span class="apsa-tags-label apsa-action-name apsa-slide-opener" data-apsa-open-slide="<?php if (empty($element_exclude_tags)): ?>false<?php else: ?>true<?php endif; ?>"><?php echo $apsa_admin_labels["exclude"]; ?></span>
                                                                <span class="apsa-with-question" title="<?php echo ucfirst($apsa_admin_labels["detailed_description"]) ?>" data-apsa-message='<?php echo $apsa_admin_labels["exclude_element_desc"]; ?>
                                                                      <br><br><strong><?php echo $apsa_admin_labels["specify_tag"]; ?> - <span style="color: #F0AD4E"><?php echo $apsa_admin_labels["yellow"]; ?></span></strong>
                                                                      <br><strong><?php echo $apsa_admin_labels["specify_category"]; ?> - <span style="color: #337AB7"><?php echo $apsa_admin_labels["blue"]; ?></span></strong>
                                                                      <br><strong><?php echo $apsa_admin_labels["specify_post"]; ?> - <span style="color: #D9534F"><?php echo $apsa_admin_labels["red"]; ?></span></strong>
                                                                      <br><strong><?php echo $apsa_admin_labels["specify_page"]; ?> - <span style="color: #5CB85C"><?php echo $apsa_admin_labels["green"]; ?></span></strong>
                                                                      <br><strong><?php echo $apsa_admin_labels["specify_language"]; ?> - <span style="color: #5bc0de"><?php echo $apsa_admin_labels["light_blue"]; ?></span></strong>
                                                                      <br><strong><?php echo $apsa_admin_labels["specify_device"]; ?> - <span style="color: #aaaaaa"><?php echo $apsa_admin_labels["grey"]; ?></span></strong>'></span>
                                                                <div class="apsa-tags apsa-sliding-block" data-apsa-open="<?php if (empty($element_exclude_tags)): ?>false<?php else: ?>true<?php endif; ?>">
                                                                    <input type="hidden" class="apsa-tags-values" data-apsa-tags-values="<?php echo $element_exclude_tags; ?>" />
                                                                    <div class="apsa_tag_exclude">
                                                                        <input type="text" class="typeahead-auto"/>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- Element options -->
                                                    <div class="apsa-slide-cont apsa-element-options-cont">
                                                        <h3 class="apsa-slide-opener" data-apsa-open-slide="false">
                                                            <span><?php echo $apsa_admin_labels["options"]; ?></span>
                                                            <span class="apsa-slide-open-pointer"></span>
                                                        </h3>
                                                        <div class="apsa-sliding-block" data-apsa-open="false" >
                                                            <ul class="apsa-element-options">
                                                                <!-- campaign part -->
                                                                <li class="apsa-form-item apsa-element-usual-input apsa-fr-option">
                                                                    <span><?php echo $apsa_admin_labels["schedule"]; ?></span>
                                                                    <span class="apsa-with-question" title="<?php echo ucfirst($apsa_admin_labels["detailed_description"]); ?>" data-apsa-message="<?php echo $apsa_admin_labels["schedule_desc"]; ?>"></span>
                                                                    <div class="apsa-input-block">
                                                                        <input type="text" class="apsa-schedule-date <?php echo (!empty($schedule_date) && $current_date < $schedule_date ? ' apsa-overdue-element ' : '') ?>" value="<?php echo (!empty($element_schedule) ? date_i18n(get_option('date_format'), strtotime($element_schedule)) : '') ?>" />
                                                                        <input type="hidden" name="schedule" value="<?php echo $element_schedule ?>" />
                                                                    </div>
                                                                    <span class="apsa-input-message <?php echo $warning_schedule_class; ?>" data-apsa-default-message="<?php echo $apsa_admin_labels['default'] . ' ' . $apsa_admin_labels['schedule_def']; ?>"><?php echo $schedule_text; ?></span>
                                                                </li>
                                                                <li class="apsa-form-item apsa-element-usual-input apsa-fr-option">
                                                                    <span><?php echo $apsa_admin_labels["deadline"]; ?></span>
                                                                    <span class="apsa-with-question" title="<?php echo ucfirst($apsa_admin_labels["detailed_description"]) ?>" data-apsa-message="<?php echo $apsa_admin_labels["deadline_desc"]; ?>"></span>
                                                                    <div class="apsa-input-block">
                                                                        <input type="text" class="apsa-deadline-date <?php echo (!empty($deadline_date) && $current_date > $deadline_date ? ' apsa-overdue-element ' : '') ?>" value="<?php echo (!empty($element_deadline) ? date_i18n(get_option('date_format'), strtotime($element_deadline)) : '') ?>" />
                                                                        <input type="hidden" name="deadline" value="<?php echo $element_deadline ?>" />
                                                                    </div>
                                                                    <span class="apsa-input-message <?php echo $warning_deadline_class; ?>" data-apsa-default-message="<?php echo $apsa_admin_labels['default'] . ' ' . $apsa_admin_labels['deadline_def']; ?>"><?php echo $deadline_text; ?></span>
                                                                </li>
                                                                <li class="apsa-form-item apsa-element-usual-input apsa-fr-option">
                                                                    <span><?php echo $apsa_admin_labels["max_views"]; ?></span>
                                                                    <span class="apsa-with-question" title="<?php echo ucfirst($apsa_admin_labels["detailed_description"]) ?>" data-apsa-message="<?php echo $apsa_admin_labels["max_views_desc"]; ?>"></span>
                                                                    <div class="apsa-input-block">
                                                                        <input type="text" class="apsa-positive-int <?php echo (trim($restrict_fr_event) != "" && $fr_event_count >= $restrict_fr_event ? ' apsa-overdue-element ' : '') ?>" name="restrict_views" value="<?php echo $restrict_fr_event ?>" />
                                                                    </div>
                                                                    <span class="apsa-input-message <?php echo $warning_fr_event_class; ?>" data-apsa-default-message="<?php echo $apsa_admin_labels['default'] . ' ' . $apsa_admin_labels['max_views_def']; ?>"><?php echo $fr_event_text; ?></span>
                                                                </li>
                                                                <!-- child part -->
                                                                <?php
                                                                $apsa_op = isset($element_options[$element_id]) ? $element_options[$element_id] : '';
                                                                echo apsa_get_element_options_struct($apsa_op, $element_type, $campaign['type'], $child_event_count);
                                                                ?>
                                                            </ul>
                                                        </div>    
                                                    </div>
                                                </form>
                                                <!-- Element statistics by chart and table -->
                                                <div class="apsa-slide-cont apsa-element-stat-cont apsa-stat-cont">
                                                    <h3 class="apsa-slide-opener apsa-element-stat-opener" data-apsa-open-slide="false">
                                                        <span><?php echo $apsa_admin_labels["Statistics_element"]; ?></span>
                                                        <span class="apsa-slide-open-pointer"></span>
                                                    </h3>
                                                    <div class="apsa-sliding-block apsa-range-stat" data-apsa-open="false" >
                                                        <div class="apsa-stat-filter">
                                                            <input type="text" class="apsa-stat-from" placeholder="<?php echo $apsa_admin_labels["stat_from_place"]; ?>" />
                                                            <span>-</span>
                                                            <input type="text" class="apsa-stat-to" placeholder="<?php echo $apsa_admin_labels["stat_to_place"]; ?>" />
                                                            <input type="button" class="button apsa-get-statistics" value="<?php echo $apsa_admin_labels["stat_show"]; ?>" />
                                                            <div class="apsa-export-single-stat apsa-export-element-stat">
                                                                <span class="apsa-action-name apsa-dash-export" title="<?php echo ucfirst($apsa_admin_labels["export_stats"]) ?>"></span>
                                                            </div>
                                                        </div>
                                                        <div class="apsa-stat-container apsa-stat-block" data-apsa-element-id="<?php echo $element_id; ?>" data-apsa-element-title="<?php echo $campaign_element['title']; ?>"></div>
                                                    </div>    
                                                </div>
                                            </li>
                                            <?php
                                        }
                                    }
                                    ?>
                                </ul>
                            </div>
                            <div class="apsa-campaign-settings">
                                <div class="apsa-slide-cont apsa-campaign-fate apsa-item-part">
                                    <h3 class="apsa-slide-opener" data-apsa-open-slide="true">
                                        <span><?php echo $apsa_admin_labels["general"]; ?></span>
                                        <span class="apsa-slide-open-pointer"></span>
                                    </h3>
                                    <div class="apsa-sliding-block" data-apsa-open="true">
                                        <ul class="apsa-main-settings">
                                            <?php if ($campaign['type'] == "embed"): ?>
                                                <li><span class="apsa-dash-shortcode"><span><?php echo $apsa_admin_labels["shortcode"] . ': '; ?></span><span class="apsa-click-select"><?php echo '[' . strtolower($apsa_plugin_data['plugin_data']['name']) . ' id="' . $campaign['id'] . '"]'; ?></span></span>
                                                    <span class="apsa-with-question" title="<?php echo ucfirst($apsa_admin_labels["detailed_description"]); ?>" data-apsa-message="<?php echo $apsa_admin_labels["embed_shortcode_desc"]; ?>"></span>
                                                </li>
                                                <li>
                                                    <div class="apsa-embed-alignment apsa-embeding-alignment" data-apsa-camp-id="<?php echo $campaign['id']; ?>" data-apsa-plugin-name="<?php echo $apsa_admin_labels['plugin_name']; ?>">
                                                        <label><?php echo $apsa_admin_labels["alignment"] . ': '; ?></label>
                                                        <ul>
                                                            <li class="apsa-embed-alignment-left" data-apsa-embed-alignment="left" title="<?php echo $apsa_admin_labels["alignment_left"]; ?>"></li>
                                                            <li class="apsa-embed-alignment-center" data-apsa-embed-alignment="center" title="<?php echo $apsa_admin_labels["alignment_center"]; ?>"></li>
                                                            <li class="apsa-embed-alignment-right" data-apsa-embed-alignment="right" title="<?php echo $apsa_admin_labels["alignment_right"]; ?>"></li>
                                                            <li class="apsa-embed-alignment-none" data-apsa-embed-alignment="none" title="<?php echo $apsa_admin_labels["alignment_none"]; ?>"></li>
                                                        </ul>
                                                    </div>
                                                </li>
                                                <li><span class="apsa-dash-code"><span><?php echo $apsa_admin_labels["auto_placement"]; ?></span></span>
                                                    <span class="apsa-with-question" title="<?php echo ucfirst($apsa_admin_labels["detailed_description"]); ?>" data-apsa-message="<?php echo $apsa_admin_labels["auto_placement_desc"]; ?>"></span>
                                                </li>
                                                <li class="apsa-form-item apsa-auto-placement">
                                                    <span><?php echo $apsa_admin_labels["embed_placement_before"]; ?></span>
                                                    <?php $before_align = isset($camp_options[$camp_id]['before_align']) ? $camp_options[$camp_id]['before_align'] : "" ?>
                                                    <div class="apsa-input-block">
                                                        <div class="apsa-placement-area">
                                                            <input type="checkbox" class="apsa-auto-placement apsa-placement-before" <?php if ((isset($camp_options[$camp_id]['auto_placement']) && ('before' == $camp_options[$camp_id]['auto_placement'])) || (isset($camp_options[$camp_id]['auto_placement']) && 'both' == $camp_options[$camp_id]['auto_placement'])): ?> checked="checked" <?php endif; ?>/>
                                                        </div><!--
                                                        --><select name="before_align" class="apsa-placement-align apsa-before-align">
                                                            <option value="none"<?php if ('none' == $before_align): ?> selected="selected"<?php endif; ?>><?php echo $apsa_admin_labels["alignment_none"]; ?></option>
                                                            <option value="left"<?php if ('left' == $before_align): ?> selected="selected"<?php endif; ?>><?php echo $apsa_admin_labels["alignment_left"]; ?></option>
                                                            <option value="center"<?php if ('center' == $before_align): ?> selected="selected"<?php endif; ?>><?php echo $apsa_admin_labels["alignment_center"]; ?></option>
                                                            <option value="right"<?php if ('right' == $before_align): ?> selected="selected"<?php endif; ?>><?php echo $apsa_admin_labels["alignment_right"]; ?></option>
                                                        </select>
                                                    </div>
                                                </li>
                                                <li class="apsa-form-item apsa-auto-placement">
                                                    <span><?php echo $apsa_admin_labels["embed_placement_after"]; ?></span>
                                                    <?php $after_align = isset($camp_options[$camp_id]['after_align']) ? $camp_options[$camp_id]['after_align'] : "" ?>
                                                    <div class="apsa-input-block">
                                                        <div class="apsa-placement-area">
                                                            <input type="checkbox" class="apsa-auto-placement apsa-placement-after" <?php if ((isset($camp_options[$camp_id]['auto_placement']) && ('after' == $camp_options[$camp_id]['auto_placement'])) || (isset($camp_options[$camp_id]['auto_placement']) && 'both' == $camp_options[$camp_id]['auto_placement'])): ?> checked="checked" <?php endif; ?>/>
                                                        </div><!--
                                                        --><select name="after_align" class="apsa-placement-align apsa-after-align">
                                                            <option value="none"<?php if ('none' == $after_align): ?> selected="selected"<?php endif; ?>><?php echo $apsa_admin_labels["alignment_none"]; ?></option>
                                                            <option value="left"<?php if ('left' == $after_align): ?> selected="selected"<?php endif; ?>><?php echo $apsa_admin_labels["alignment_left"]; ?></option>
                                                            <option value="center"<?php if ('center' == $after_align): ?> selected="selected"<?php endif; ?>><?php echo $apsa_admin_labels["alignment_center"]; ?></option>
                                                            <option value="right"<?php if ('right' == $after_align): ?> selected="selected"<?php endif; ?>><?php echo $apsa_admin_labels["alignment_right"]; ?></option>
                                                        </select>
                                                    </div>
                                                </li>
                                            <?php endif; ?>
                                            <ul class="apsa-actions apsa-campaign-actions">
                                                <?php if ($campaign['status'] == "active"): ?>
                                                    <li data-apsa-action="suspended">
                                                        <span class="apsa-action-name apsa-dash-suspend"><?php echo $apsa_admin_labels["suspend_camp"] ?></span>
                                                    </li>
                                                <?php elseif ($campaign['status'] == "suspended"): ?>
                                                    <li data-apsa-action="active">
                                                        <span class="apsa-action-name apsa-dash-activate"><?php echo $apsa_admin_labels["activate_camp"]; ?></span>
                                                    </li>
                                                <?php endif; ?>
                                                <li data-apsa-action="export" class="apsa-export-camp-elements apsa-export-range">
                                                    <span class="apsa-action-name apsa-dash-export"><?php echo $apsa_admin_labels["export_campaign_Stats"]; ?></span>
                                                </li>
                                                <li data-apsa-action="delete">
                                                    <span class="apsa-action-name apsa-dash-delete"><?php echo $apsa_admin_labels["delete_camp"]; ?></span>
                                                </li>
                                            </ul>
                                        </ul> 
                                        <div class="apsa-tags-inputs">
                                            <span class="apsa-tags-label"><?php echo $apsa_admin_labels["include"]; ?></span>
                                            <span class="apsa-with-question" title="<?php echo ucfirst($apsa_admin_labels["detailed_description"]) ?>" data-apsa-message='<?php echo $apsa_admin_labels["include_camp_desc"]; ?>
                                                  <br><br><strong><?php echo $apsa_admin_labels["specify_tag"]; ?> - <span style="color: #F0AD4E"><?php echo $apsa_admin_labels["yellow"]; ?></span></strong>
                                                  <br><strong><?php echo $apsa_admin_labels["specify_category"]; ?> - <span style="color: #337AB7"><?php echo $apsa_admin_labels["blue"]; ?></span></strong>
                                                  <br><strong><?php echo $apsa_admin_labels["specify_post"]; ?> - <span style="color: #D9534F"><?php echo $apsa_admin_labels["red"]; ?></span></strong>
                                                  <br><strong><?php echo $apsa_admin_labels["specify_page"]; ?> - <span style="color: #5CB85C"><?php echo $apsa_admin_labels["green"]; ?></span></strong>
                                                  <br><strong><?php echo $apsa_admin_labels["specify_language"]; ?> - <span style="color: #5bc0de"><?php echo $apsa_admin_labels["light_blue"]; ?></span></strong>
                                                  <br><strong><?php echo $apsa_admin_labels["specify_device"]; ?> - <span style="color: #aaaaaa"><?php echo $apsa_admin_labels["grey"]; ?></span></strong>'></span>
                                            <div class="apsa-tags">
                                                <input type="hidden" class="apsa-tags-values" data-apsa-tags-values="<?php echo isset($camp_options[$camp_id]['camp_include_tags']) ? $camp_options[$camp_id]['camp_include_tags'] : ""; ?>" />
                                                <div class="apsa_tag_include">
                                                    <input type="text" class="typeahead-auto"/>
                                                </div>
                                            </div>
                                            <div class="apsa-slide-cont">
                                                <?php $camp_exclude_tags = isset($camp_options[$camp_id]["camp_exclude_tags"]) ? $camp_options[$camp_id]["camp_exclude_tags"] : "" ?>
                                                <span class="apsa-tags-label apsa-action-name apsa-slide-opener" data-apsa-open-slide="<?php if (empty($camp_exclude_tags)): ?>false<?php else: ?>true<?php endif; ?>"><?php echo $apsa_admin_labels["exclude"]; ?></span>
                                                <span class="apsa-with-question" title="<?php echo ucfirst($apsa_admin_labels["detailed_description"]) ?>" data-apsa-message='<?php echo $apsa_admin_labels["exclude_camp_desc"]; ?>
                                                      <br><br><strong><?php echo $apsa_admin_labels["specify_tag"]; ?> - <span style="color: #F0AD4E"><?php echo $apsa_admin_labels["yellow"]; ?></span></strong>
                                                      <br><strong><?php echo $apsa_admin_labels["specify_category"]; ?> - <span style="color: #337AB7"><?php echo $apsa_admin_labels["blue"]; ?></span></strong>
                                                      <br><strong><?php echo $apsa_admin_labels["specify_post"]; ?> - <span style="color: #D9534F"><?php echo $apsa_admin_labels["red"]; ?></span></strong>
                                                      <br><strong><?php echo $apsa_admin_labels["specify_page"]; ?> - <span style="color: #5CB85C"><?php echo $apsa_admin_labels["green"]; ?></span></strong>
                                                      <br><strong><?php echo $apsa_admin_labels["specify_language"]; ?> - <span style="color: #5bc0de"><?php echo $apsa_admin_labels["light_blue"]; ?></span></strong>
                                                      <br><strong><?php echo $apsa_admin_labels["specify_device"]; ?> - <span style="color: #aaaaaa"><?php echo $apsa_admin_labels["grey"]; ?></span></strong>'></span>
                                                <div class="apsa-tags apsa-sliding-block" data-apsa-open="<?php if (empty($camp_exclude_tags)): ?>false<?php else: ?>true<?php endif; ?>">
                                                    <input type="hidden" class="apsa-tags-values" data-apsa-tags-values="<?php echo $camp_exclude_tags; ?>" />
                                                    <div class="apsa_tag_exclude">
                                                        <input type="text" class="typeahead-auto"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="apsa-save-camp-cont">
                                    <div class="apsa-waiting-wrapper">
                                        <input type="button" class="button button-primary apsa-save-campaign-options" apsa-data-campaign-id="<?php echo $campaign['id']; ?>" value="<?php echo $apsa_admin_labels["update_camp"]; ?>" />
                                    </div>
                                </div>
                                <div class="apsa-slide-cont apsa-item-part">
                                    <h3 class="apsa-slide-opener" data-apsa-open-slide="false">
                                        <span><?php echo $apsa_admin_labels["options"]; ?></span>
                                        <span class="apsa-slide-open-pointer"></span>
                                    </h3>
                                    <div class="apsa-sliding-block" data-apsa-open="false">
                                        <form class="apsa-campaign-options-form"> 
                                            <ul class="apsa-campaign-options">
                                                <?php if ($campaign['type'] == "background"): ?>                                        
                                                    <li class="apsa-form-item">
                                                        <span><?php echo $apsa_admin_labels["change_interval"]; ?> </span>&nbsp;<span class="apsa-no-cap">(<?php echo $apsa_admin_labels["second"]; ?>)</span>
                                                        <span class="apsa-with-question" title="<?php echo ucfirst($apsa_admin_labels["detailed_description"]); ?>" data-apsa-message="<?php echo $apsa_admin_labels["change_interval_desc"]; ?>"></span>
                                                        <div class="apsa-input-block">
                                                            <input type="text" name="change_interval" class="apsa-positive-int" value="<?php echo isset($camp_options[$camp_id]['change_interval']) ? $camp_options[$camp_id]['change_interval'] : ""; ?>" />
                                                        </div>
                                                        <span class="apsa-input-message" data-apsa-default-message="<?php echo $apsa_admin_labels["default"] . ' ' . $apsa_admin_labels["change_interval_def"]; ?>"><?php echo $apsa_admin_labels["default"] . ' ' . $apsa_admin_labels["change_interval_def"]; ?></span>
                                                    </li>
                                                    <li class="apsa-form-item">
                                                        <span><?php echo $apsa_admin_labels["bg_selector"]; ?></span>
                                                        <span class="apsa-with-question" title="<?php echo ucfirst($apsa_admin_labels["detailed_description"]); ?>" data-apsa-message="<?php echo $apsa_admin_labels["bg_selector_desc"]; ?>"></span>
                                                        <div class="apsa-input-block">
                                                            <input type="text" name="background_selector" value="<?php echo isset($camp_options[$camp_id]['change_interval']) ? $camp_options[$camp_id]['background_selector'] : ""; ?>" />
                                                        </div>
                                                        <span class="apsa-input-message"><?php echo $apsa_admin_labels["default"]; ?> body</span>
                                                    </li>
                                                <?php elseif ($campaign['type'] == "popup"): ?>                                        
                                                    <li class="apsa-form-item">
                                                        <span><?php echo $apsa_admin_labels["change_interval"]; ?></span>&nbsp;<span class="apsa-no-cap">(<?php echo $apsa_admin_labels["second"]; ?>)</span>
                                                        <span class="apsa-with-question" title="<?php echo ucfirst($apsa_admin_labels["detailed_description"]); ?>" data-apsa-message="<?php echo $apsa_admin_labels["change_interval_desc"]; ?>"></span>
                                                        <div class="apsa-input-block">
                                                            <input type="text" name="change_interval" class="apsa-positive-int" value="<?php echo isset($camp_options[$camp_id]['change_interval']) ? $camp_options[$camp_id]['change_interval'] : ""; ?>" />
                                                        </div>
                                                        <span class="apsa-input-message" data-apsa-default-message="<?php echo $apsa_admin_labels["default"] . ' ' . $apsa_admin_labels["change_interval_def"]; ?>"><?php echo $apsa_admin_labels["default"] . ' ' . $apsa_admin_labels["change_interval_def"]; ?></span>
                                                    </li>
                                                    <li class="apsa-form-item">
                                                        <span><?php echo $apsa_admin_labels["show_interval"]; ?></span>&nbsp;<span class="apsa-no-cap">(<?php echo $apsa_admin_labels["second"]; ?>)</span>
                                                        <span class="apsa-with-question" title="<?php echo ucfirst($apsa_admin_labels["detailed_description"]) ?>" data-apsa-message="<?php echo $apsa_admin_labels["show_interval_desc"]; ?>"></span>
                                                        <div class="apsa-input-block">
                                                            <input type="text" name="view_interval" class="apsa-positive-int" value="<?php echo isset($camp_options[$camp_id]['view_interval']) ? $camp_options[$camp_id]['view_interval'] : ""; ?>" />
                                                        </div>
                                                        <span class="apsa-input-message" data-apsa-default-message="<?php echo $apsa_admin_labels["default"] . ' ' . $apsa_admin_labels["show_interval_def"]; ?>"><?php echo $apsa_admin_labels["default"] . ' ' . $apsa_admin_labels["show_interval_def"]; ?></span>
                                                    </li>
                                                    <li class="apsa-form-item">
                                                        <span><?php echo $apsa_admin_labels["popup_direction"]; ?></span>
                                                        <span class="apsa-with-question" title="<?php echo ucfirst($apsa_admin_labels["detailed_description"]); ?>" data-apsa-message="<?php echo $apsa_admin_labels["popup_direction_desc"]; ?>"></span>
                                                        <?php $pop_direction = isset($camp_options[$camp_id]['popup_animation']) ? $camp_options[$camp_id]['popup_animation'] : "" ?>
                                                        <div class="apsa-input-block">
                                                            <select name="popup_animation">
                                                                <?php foreach ($apsa_popup_effects as $key => $value): ?>                                                
                                                                    <option value="<?php echo $key; ?>"<?php if ($key == $pop_direction): ?> selected="selected"<?php endif; ?>><?php echo $key; ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                    </li>
                                                    <li class="apsa-form-item">
                                                        <span><?php echo $apsa_admin_labels["width"]; ?> </span>&nbsp;<span class="apsa-no-cap">(px/%)</span></span>
                                                        <span class="apsa-with-question" title="<?php echo ucfirst($apsa_admin_labels["detailed_description"]); ?>" data-apsa-message="<?php echo $apsa_admin_labels["width_desc"]; ?>"></span>
                                                        <div class="apsa-input-block">
                                                            <input type="text" name="width" class="apsa-size" value="<?php echo isset($camp_options[$camp_id]['width']) ? $camp_options[$camp_id]['width'] : ""; ?>" />
                                                        </div>
                                                        <span class="apsa-input-message" data-apsa-default-message="<?php echo $apsa_admin_labels["default"]; ?> 600px"><?php echo $apsa_admin_labels["default"]; ?> 600px</span>
                                                    </li>
                                                    <li class="apsa-form-item">
                                                        <span><?php echo $apsa_admin_labels["height"]; ?> </span>&nbsp;<span class="apsa-no-cap">(px/%)</span>
                                                        <span class="apsa-with-question" title="<?php echo ucfirst($apsa_admin_labels["detailed_description"]); ?>" data-apsa-message="<?php echo $apsa_admin_labels["height_desc"]; ?>"></span>
                                                        <div class="apsa-input-block">
                                                            <input type="text" name="height" class="apsa-size" value="<?php echo isset($camp_options[$camp_id]['height']) ? $camp_options[$camp_id]['height'] : ""; ?>" />
                                                        </div>
                                                        <span class="apsa-input-message" data-apsa-default-message="<?php echo $apsa_admin_labels["default"]; ?> 500px"><?php echo $apsa_admin_labels["default"]; ?> 500px</span>
                                                    </li>
                                                    <li class="apsa-form-item">
                                                        <span><?php echo $apsa_admin_labels["popup_show_delay"]; ?> </span>&nbsp;<span class="apsa-no-cap">(<?php echo $apsa_admin_labels["second"]; ?>)</span>
                                                        <span class="apsa-with-question" title="<?php echo ucfirst($apsa_admin_labels["detailed_description"]); ?>" data-apsa-message="<?php echo $apsa_admin_labels["popup_show_delay_desc"]; ?>"></span>
                                                        <div class="apsa-input-block">
                                                            <input type="text" class="apsa-positive-int" name="view_after" value="<?php echo isset($camp_options[$camp_id]['view_after']) ? $camp_options[$camp_id]['view_after'] : ""; ?>" />
                                                        </div>
                                                        <span class="apsa-input-message" data-apsa-default-message="<?php echo $apsa_admin_labels["default"]; ?> 0"><?php echo $apsa_admin_labels["default"]; ?> 0</span>
                                                    </li>
                                                    <li class="apsa-form-item">
                                                        <span><?php echo $apsa_admin_labels["close_button_delay"]; ?> </span>&nbsp;<span class="apsa-no-cap">(<?php echo $apsa_admin_labels["second"]; ?>)</span>
                                                        <span class="apsa-with-question" title="<?php echo ucfirst($apsa_admin_labels["detailed_description"]); ?>" data-apsa-message="<?php echo $apsa_admin_labels["close_button_delay_desc"]; ?>"></span>
                                                        <div class="apsa-input-block">
                                                            <input type="text" class="apsa-positive-int" name="show_close_after" value="<?php echo isset($camp_options[$camp_id]['show_close_after']) ? $camp_options[$camp_id]['show_close_after'] : ""; ?>" />
                                                        </div>
                                                        <span class="apsa-input-message" data-apsa-default-message="<?php echo $apsa_admin_labels["default"]; ?> 0"><?php echo $apsa_admin_labels["default"]; ?> 0</span>
                                                    </li>
                                                    <li class="apsa-form-item">
                                                        <span><?php echo $apsa_admin_labels["popup_autoclose"]; ?> </span>&nbsp;<span class="apsa-no-cap">(<?php echo $apsa_admin_labels["second"]; ?>)</span>
                                                        <span class="apsa-with-question" title="<?php echo ucfirst($apsa_admin_labels["detailed_description"]); ?>" data-apsa-message="<?php echo $apsa_admin_labels["popup_autoclose_desc"]; ?>"></span>
                                                        <div class="apsa-input-block">
                                                            <input type="text" class="apsa-positive-int" name="hide_element_after" value="<?php echo isset($camp_options[$camp_id]['hide_element_after']) ? $camp_options[$camp_id]['hide_element_after'] : ""; ?>" />
                                                        </div>
                                                        <span class="apsa-input-message" data-apsa-default-message="<?php echo $apsa_admin_labels["default"] . ' ' . $apsa_admin_labels["popup_autoclose_def"]; ?>"><?php echo $apsa_admin_labels["default"] . ' ' . $apsa_admin_labels["popup_autoclose_def"]; ?></span>
                                                    </li>
                                                    <li class="apsa-form-item">
                                                        <span><?php echo $apsa_admin_labels["overlay_pattern"]; ?></span>
                                                        <span class="apsa-with-question" title="<?php echo ucfirst($apsa_admin_labels["detailed_description"]); ?>" data-apsa-message="<?php echo $apsa_admin_labels["overlay_pattern_desc"]; ?>"></span>
                                                        <?php $overlay_pattern = isset($camp_options[$camp_id]['overlay_pattern']) ? $camp_options[$camp_id]['overlay_pattern'] : "" ?>
                                                        <div class="apsa-input-block">
                                                            <select name="overlay_pattern">
                                                                <option value="none"<?php if ('none' == $overlay_pattern): ?> selected="selected"<?php endif; ?>>none</option>
                                                                <option value="gray"<?php if ('gray' == $overlay_pattern || '' == $overlay_pattern): ?> selected="selected"<?php endif; ?>>gray</option>
                                                                <?php foreach ($apsa_overlay_patterns as $key => $value): ?>                                                
                                                                    <option value="<?php echo $key; ?>"<?php if ($key == $overlay_pattern): ?> selected="selected"<?php endif; ?>><?php echo $key; ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                    </li>
                                                    <li class="apsa-form-item">
                                                        <?php $put_in_frame = isset($camp_options[$camp_id]['put_in_frame']) ? $camp_options[$camp_id]['put_in_frame'] : "on"; ?>
                                                        <?php $frame_color = isset($camp_options[$camp_id]['frame_color']) ? $camp_options[$camp_id]['frame_color'] : "#ffffff"; ?>
                                                        <div class="apsa-input-block">
                                                            <span><?php echo $apsa_admin_labels["put_in_frame"]; ?></span>
                                                            <input type="checkbox" <?php if (!empty($put_in_frame)): ?> checked<?php endif; ?>/>
                                                            <input type="hidden" class="apsa-hold-checkbox" name="put_in_frame"<?php if (!empty($put_in_frame)): ?> value="on"<?php endif; ?>>
                                                            <span class="apsa-with-question" title="<?php echo ucfirst($apsa_admin_labels["detailed_description"]); ?>" data-apsa-message="<?php echo $apsa_admin_labels["put_in_frame_desc"]; ?>"></span>
                                                        </div>
                                                    </li>
                                                    <li class="apsa-form-item">
                                                        <?php $put_in_frame = isset($camp_options[$camp_id]['put_in_frame']) ? $camp_options[$camp_id]['put_in_frame'] : "on"; ?>
                                                        <?php $frame_color = isset($camp_options[$camp_id]['frame_color']) ? $camp_options[$camp_id]['frame_color'] : "#ffffff"; ?>
                                                        <span><?php echo $apsa_admin_labels["popup_color"]; ?></span>
                                                        <span class="apsa-with-question" title="<?php echo ucfirst($apsa_admin_labels["detailed_description"]); ?>" data-apsa-message="<?php echo $apsa_admin_labels["popup_color_desc"]; ?>"></span>
                                                        <div class="apsa-input-block">
                                                            <input type="text" name="frame_color" class="apsa-extra-small-input apsa-colorpicker" data-default-color="#ffffff" value="<?php echo $frame_color; ?>">
                                                        </div>
                                                        <span class="apsa-input-message"><?php echo $apsa_admin_labels["default"]; ?> #ffffff</span>
                                                    </li>
                                                <?php elseif ($campaign['type'] == "embed"): ?>                                        
                                                    <li class="apsa-form-item">
                                                        <span><?php echo $apsa_admin_labels["change_interval"]; ?> </span>&nbsp;<span class="apsa-no-cap">(<?php echo $apsa_admin_labels["second"]; ?>)</span>
                                                        <span class="apsa-with-question" title="<?php echo ucfirst($apsa_admin_labels["detailed_description"]); ?>" data-apsa-message="<?php echo $apsa_admin_labels["change_interval_desc"]; ?>"></span>
                                                        <div class="apsa-input-block">
                                                            <input type="text" name="change_interval" class="apsa-positive-int" value="<?php echo isset($camp_options[$camp_id]['change_interval']) ? $camp_options[$camp_id]['change_interval'] : ""; ?>" />
                                                        </div>
                                                        <span class="apsa-input-message" data-apsa-default-message="<?php echo $apsa_admin_labels["default"] . ' ' . $apsa_admin_labels["change_interval_def"]; ?>"><?php echo $apsa_admin_labels["default"] . ' ' . $apsa_admin_labels["change_interval_def"]; ?></span>
                                                    </li>
                                                    <li class="apsa-form-item">
                                                        <span><?php echo $apsa_admin_labels["embed_direction"]; ?></span>
                                                        <span class="apsa-with-question" title="<?php echo ucfirst($apsa_admin_labels["detailed_description"]); ?>" data-apsa-message="<?php echo $apsa_admin_labels["embed_direction_desc"]; ?>"></span>
                                                        <?php $emb_direction = isset($camp_options[$camp_id]['embed_direction']) ? $camp_options[$camp_id]['embed_direction'] : "" ?>
                                                        <div class="apsa-input-block">
                                                            <select name="embed_direction">
                                                                <option value="none"<?php if ('none' == $emb_direction): ?> selected="selected"<?php endif; ?>>none</option>
                                                                <?php foreach ($apsa_embed_effects as $key => $value): ?>                                                
                                                                    <option value="<?php echo $key; ?>"<?php if ($key == $emb_direction): ?> selected="selected"<?php endif; ?>><?php echo $key; ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                    </li>
                                                    <li class="apsa-form-item">
                                                        <span><?php echo $apsa_admin_labels["width"]; ?> </span>&nbsp;<span class="apsa-no-cap">(px/%)</span>
                                                        <span class="apsa-with-question" title="<?php echo ucfirst($apsa_admin_labels["detailed_description"]); ?>" data-apsa-message="<?php echo $apsa_admin_labels["width_desc"]; ?>"></span>
                                                        <div class="apsa-input-block">
                                                            <input type="text" name="width" class="apsa-size" value="<?php echo isset($camp_options[$camp_id]['width']) ? $camp_options[$camp_id]['width'] : ""; ?>" />
                                                        </div>
                                                        <span class="apsa-input-message" data-apsa-default-message="<?php echo $apsa_admin_labels["default"]; ?> 100%"><?php echo $apsa_admin_labels["default"]; ?> 100%</span>
                                                    </li>
                                                    <li class="apsa-form-item">
                                                        <span><?php echo $apsa_admin_labels["height"]; ?></span>&nbsp;<span class="apsa-no-cap">(px)</span>
                                                        <span class="apsa-with-question" title="<?php echo ucfirst($apsa_admin_labels["detailed_description"]); ?>" data-apsa-message="<?php echo $apsa_admin_labels["height_desc_px"]; ?>"></span>
                                                        <div class="apsa-input-block">
                                                            <input type="text" name="height" class="apsa-positive-int" value="<?php echo isset($camp_options[$camp_id]['height']) ? $camp_options[$camp_id]['height'] : ""; ?>" />
                                                        </div>
                                                        <span class="apsa-input-message" data-apsa-default-message="<?php echo $apsa_admin_labels["default"]; ?> 100"><?php echo $apsa_admin_labels["default"]; ?> 100</span>
                                                    </li>
                                                <?php elseif ($campaign['type'] == "sticky"): ?>
                                                    <li class="apsa-form-item">
                                                        <span><?php echo $apsa_admin_labels["sticky_position"]; ?></span>
                                                        <span class="apsa-with-question" title="<?php echo ucfirst($apsa_admin_labels["detailed_description"]); ?>" data-apsa-message="<?php echo $apsa_admin_labels["sticky_position_desc"]; ?>"></span>
                                                        <div class="apsa-select-block">
                                                            <select name="position">
                                                                <?php foreach ($apsa_sticky_positions as $key => $value): ?>                                                
                                                                <option value="<?php echo $key; ?>"<?php if (empty($camp_options[$camp_id]['position']) || $key == $camp_options[$camp_id]['position']): ?> selected="selected"<?php endif; ?>><?php echo $apsa_admin_labels['sticky_'.$key]; ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                        <span class="apsa-input-message" data-apsa-default-message="<?php echo $apsa_admin_labels["default"] . ' ' . $apsa_admin_labels["sticky_position_def"]; ?>"><?php echo $apsa_admin_labels["default"] . ' ' . $apsa_admin_labels["sticky_position_def"]; ?></span>
                                                    </li>
                                                    <li class="apsa-form-item">
                                                        <span><?php echo $apsa_admin_labels["change_interval"]; ?></span>&nbsp;<span class="apsa-no-cap">(<?php echo $apsa_admin_labels["second"]; ?>)</span>
                                                        <span class="apsa-with-question" title="<?php echo ucfirst($apsa_admin_labels["detailed_description"]); ?>" data-apsa-message="<?php echo $apsa_admin_labels["change_interval_desc"]; ?>"></span>
                                                        <div class="apsa-input-block">
                                                            <input type="text" name="change_interval" class="apsa-positive-int" value="<?php echo isset($camp_options[$camp_id]['change_interval']) ? $camp_options[$camp_id]['change_interval'] : ""; ?>" />
                                                        </div>
                                                        <span class="apsa-input-message" data-apsa-default-message="<?php echo $apsa_admin_labels["default"] . ' ' . $apsa_admin_labels["change_interval_def"]; ?>"><?php echo $apsa_admin_labels["default"] . ' ' . $apsa_admin_labels["change_interval_def"]; ?></span>
                                                    </li>
                                                    <li class="apsa-form-item">
                                                        <span><?php echo $apsa_admin_labels["show_interval"]; ?></span>&nbsp;<span class="apsa-no-cap">(<?php echo $apsa_admin_labels["second"]; ?>)</span>
                                                        <span class="apsa-with-question" title="<?php echo ucfirst($apsa_admin_labels["detailed_description"]) ?>" data-apsa-message="<?php echo $apsa_admin_labels["show_interval_desc"]; ?>"></span>
                                                        <div class="apsa-input-block">
                                                            <input type="text" name="view_interval" class="apsa-positive-int" value="<?php echo isset($camp_options[$camp_id]['view_interval']) ? $camp_options[$camp_id]['view_interval'] : ""; ?>" />
                                                        </div>
                                                        <span class="apsa-input-message" data-apsa-default-message="<?php echo $apsa_admin_labels["default"] . ' ' . $apsa_admin_labels["show_interval_def"]; ?>"><?php echo $apsa_admin_labels["default"] . ' ' . $apsa_admin_labels["show_interval_def"]; ?></span>
                                                    </li>
                                                    <li class="apsa-form-item">
                                                        <span><?php echo $apsa_admin_labels["sticky_direction"]; ?></span>
                                                        <span class="apsa-with-question" title="<?php echo ucfirst($apsa_admin_labels["detailed_description"]); ?>" data-apsa-message="<?php echo $apsa_admin_labels["sticky_direction_desc"]; ?>"></span>
                                                        <?php $sticky_direction = isset($camp_options[$camp_id]['sticky_animation']) ? $camp_options[$camp_id]['sticky_animation'] : "" ?>
                                                        <div class="apsa-input-block">
                                                            <select name="sticky_animation">
                                                                <?php foreach ($apsa_sticky_effects as $key => $value): ?>                                                
                                                                    <option value="<?php echo $key; ?>"<?php if ($key == $sticky_direction): ?> selected="selected"<?php endif; ?>><?php echo $key; ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                    </li>
                                                    <li class="apsa-form-item">
                                                        <span><?php echo $apsa_admin_labels["width"]; ?> </span>&nbsp;<span class="apsa-no-cap">(px/%)</span></span>
                                                        <span class="apsa-with-question" title="<?php echo ucfirst($apsa_admin_labels["detailed_description"]); ?>" data-apsa-message="<?php echo $apsa_admin_labels["width_desc"]; ?>"></span>
                                                        <div class="apsa-input-block">
                                                            <input type="text" name="width" class="apsa-size" value="<?php echo isset($camp_options[$camp_id]['width']) ? $camp_options[$camp_id]['width'] : ""; ?>" />
                                                        </div>
                                                        <span class="apsa-input-message" data-apsa-default-message="<?php echo $apsa_admin_labels["default"]; ?> 300px"><?php echo $apsa_admin_labels["default"]; ?> 300px</span>
                                                    </li>
                                                    <li class="apsa-form-item">
                                                        <span><?php echo $apsa_admin_labels["height"]; ?> </span>&nbsp;<span class="apsa-no-cap">(px/%)</span>
                                                        <span class="apsa-with-question" title="<?php echo ucfirst($apsa_admin_labels["detailed_description"]); ?>" data-apsa-message="<?php echo $apsa_admin_labels["height_desc"]; ?>"></span>
                                                        <div class="apsa-input-block">
                                                            <input type="text" name="height" class="apsa-size" value="<?php echo isset($camp_options[$camp_id]['height']) ? $camp_options[$camp_id]['height'] : ""; ?>" />
                                                        </div>
                                                        <span class="apsa-input-message" data-apsa-default-message="<?php echo $apsa_admin_labels["default"]; ?> 200px"><?php echo $apsa_admin_labels["default"]; ?> 200px</span>
                                                    </li>
                                                    <li class="apsa-form-item">
                                                        <span><?php echo $apsa_admin_labels["sticky_show_delay"]; ?> </span>&nbsp;<span class="apsa-no-cap">(<?php echo $apsa_admin_labels["second"]; ?>)</span>
                                                        <span class="apsa-with-question" title="<?php echo ucfirst($apsa_admin_labels["detailed_description"]); ?>" data-apsa-message="<?php echo $apsa_admin_labels["sticky_show_delay_desc"]; ?>"></span>
                                                        <div class="apsa-input-block">
                                                            <input type="text" class="apsa-positive-int" name="view_after" value="<?php echo isset($camp_options[$camp_id]['view_after']) ? $camp_options[$camp_id]['view_after'] : ""; ?>" />
                                                        </div>
                                                        <span class="apsa-input-message" data-apsa-default-message="<?php echo $apsa_admin_labels["default"]; ?> 0"><?php echo $apsa_admin_labels["default"]; ?> 0</span>
                                                    </li>
                                                    <li class="apsa-form-item">
                                                        <span><?php echo $apsa_admin_labels["close_button_delay"]; ?> </span>&nbsp;<span class="apsa-no-cap">(<?php echo $apsa_admin_labels["second"]; ?>)</span>
                                                        <span class="apsa-with-question" title="<?php echo ucfirst($apsa_admin_labels["detailed_description"]); ?>" data-apsa-message="<?php echo $apsa_admin_labels["close_button_delay_desc"]; ?>"></span>
                                                        <div class="apsa-input-block">
                                                            <input type="text" class="apsa-positive-int" name="show_close_after" value="<?php echo isset($camp_options[$camp_id]['show_close_after']) ? $camp_options[$camp_id]['show_close_after'] : ""; ?>" />
                                                        </div>
                                                        <span class="apsa-input-message" data-apsa-default-message="<?php echo $apsa_admin_labels["default"]; ?> 0"><?php echo $apsa_admin_labels["default"]; ?> 0</span>
                                                    </li>
                                                    <li class="apsa-form-item">
                                                        <span><?php echo $apsa_admin_labels["sticky_autoclose"]; ?> </span>&nbsp;<span class="apsa-no-cap">(<?php echo $apsa_admin_labels["second"]; ?>)</span>
                                                        <span class="apsa-with-question" title="<?php echo ucfirst($apsa_admin_labels["detailed_description"]); ?>" data-apsa-message="<?php echo $apsa_admin_labels["sticky_autoclose_desc"]; ?>"></span>
                                                        <div class="apsa-input-block">
                                                            <input type="text" class="apsa-positive-int" name="hide_element_after" value="<?php echo isset($camp_options[$camp_id]['hide_element_after']) ? $camp_options[$camp_id]['hide_element_after'] : ""; ?>" />
                                                        </div>
                                                        <span class="apsa-input-message" data-apsa-default-message="<?php echo $apsa_admin_labels["default"] . ' ' . $apsa_admin_labels["sticky_autoclose_def"]; ?>"><?php echo $apsa_admin_labels["default"] . ' ' . $apsa_admin_labels["sticky_autoclose_def"]; ?></span>
                                                    </li>
                                                    <li class="apsa-form-item">
                                                        <?php $put_in_frame = isset($camp_options[$camp_id]['put_in_frame']) ? $camp_options[$camp_id]['put_in_frame'] : "on"; ?>
                                                        <?php $frame_color = isset($camp_options[$camp_id]['frame_color']) ? $camp_options[$camp_id]['frame_color'] : "#ffffff"; ?>
                                                        <div class="apsa-input-block">
                                                            <span><?php echo $apsa_admin_labels["put_in_frame"]; ?></span>
                                                            <input type="checkbox" <?php if (!empty($put_in_frame)): ?> checked<?php endif; ?>/>
                                                            <input type="hidden" class="apsa-hold-checkbox" name="put_in_frame"<?php if (!empty($put_in_frame)): ?> value="on"<?php endif; ?>>
                                                            <span class="apsa-with-question" title="<?php echo ucfirst($apsa_admin_labels["detailed_description"]); ?>" data-apsa-message="<?php echo $apsa_admin_labels["put_in_frame_desc"]; ?>"></span>
                                                        </div>
                                                    </li>
                                                    <li class="apsa-form-item">
                                                        <?php $put_in_frame = isset($camp_options[$camp_id]['put_in_frame']) ? $camp_options[$camp_id]['put_in_frame'] : "on"; ?>
                                                        <?php $frame_color = isset($camp_options[$camp_id]['frame_color']) ? $camp_options[$camp_id]['frame_color'] : "#ffffff"; ?>
                                                        <span><?php echo $apsa_admin_labels["sticky_color"]; ?></span>
                                                        <span class="apsa-with-question" title="<?php echo ucfirst($apsa_admin_labels["detailed_description"]); ?>" data-apsa-message="<?php echo $apsa_admin_labels["sticky_color_desc"]; ?>"></span>
                                                        <div class="apsa-input-block">
                                                            <input type="text" name="frame_color" class="apsa-extra-small-input apsa-colorpicker" data-default-color="#ffffff" value="<?php echo $frame_color; ?>">
                                                        </div>
                                                        <span class="apsa-input-message"><?php echo $apsa_admin_labels["default"]; ?> #ffffff</span>
                                                    </li>
                                                <?php endif; ?>
                                            </ul>
                                        </form>
                                        <form class="apsa-child-campaign-options-form">
                                            <ul class="apsa-campaign-options">
                                                <?php
                                                if (function_exists('apsa_child_get_campaign_options')) {
                                                    $camp_op = isset($camp_options[$camp_id]) ? $camp_options[$camp_id] : '';
                                                    echo apsa_child_get_campaign_options($camp_op, $campaign['type']);
                                                }
                                                ?>
                                            </ul>
                                        </form>
                                    </div>
                                </div>
                                <div class="apsa-campaign-stat-cont apsa-stat-cont apsa-slide-cont apsa-item-part">
                                    <h3 class="apsa-slide-opener apsa-camp-stat-opener" data-apsa-open-slide="false">
                                        <span><?php echo $apsa_admin_labels["statistics_camp"]; ?></span>
                                        <span class="apsa-slide-open-pointer"></span>
                                    </h3>
                                    <div class="apsa-sliding-block" data-apsa-open="false">
                                        <div class="apsa-stat-filter">
                                            <input type="text" class="apsa-stat-from" placeholder="<?php echo $apsa_admin_labels["stat_from_place"]; ?>" />
                                            <span>-</span>
                                            <input type="text" class="apsa-stat-to" placeholder="<?php echo $apsa_admin_labels["stat_to_place"]; ?>" />
                                            <input type="button" class="button apsa-get-statistics" value="<?php echo $apsa_admin_labels["stat_show"]; ?>" />                                    
                                            <div class="apsa-export-single-stat apsa-export-camp-stat">
                                                <span class="apsa-action-name apsa-dash-export" title="<?php echo $apsa_admin_labels["export_stats"]; ?>"></span>
                                            </div>
                                        </div>
                                        <div class="apsa-campaign-stat apsa-stat-block" data-apsa-camp-id="<?php echo $campaign["id"]; ?>" data-apsa-camp-title="<?php echo $campaign['name']; ?>"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    <?php
                }
            }
            ?>
            <li class="apsa-add-block apsa-add-campaign">
                <span></span>
                <h3 class="apsa-new"><?php echo $apsa_admin_labels["add_new_campaign_full"]; ?></h3>
            </li>
        </ul>
    </div>
    <?php
}
