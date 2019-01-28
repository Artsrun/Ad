<?php
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Create widget "plugin campaign"
 */
class APSA_Campaign_Embed extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    function __construct() {
        global $apsa_plugin_data;
        global $apsa_admin_labels;
        parent::__construct(
                'apsa_campaign', // Base ID
                $apsa_plugin_data['plugin_data']['name'], // Name
                array('description' => $apsa_admin_labels['widget_desc'] // Description
                ) // Args 
        );
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget($args, $instance) {
        global $apsa_plugin_data;
        echo $args['before_widget'];
        if (!empty($instance['campaign_id'])) {
            $embed_align = isset($instance["embed_align"]) ? $instance["embed_align"] : 'none';
            echo do_shortcode("[" . strtolower($apsa_plugin_data['plugin_data']['name']) . " id='{$instance["campaign_id"]}' widget='1' align='{$embed_align}']");
        }
        echo $args['after_widget'];
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form($instance) {
        global $apsa_admin_labels;
        // Get campaigns
        $campaigns = apsa_get_campaigns();

        /** Separate campaigns type of embed */
        if (!empty($campaigns)) {
            foreach ($campaigns as $key => $campaign) {
                if ($campaign['type'] !== 'embed') {
                    unset($campaigns[$key]);
                }
            }
        }

        $campaign_id = !empty($instance['campaign_id']) ? $instance['campaign_id'] : 0;
        $embed_align = !empty($instance['embed_align']) ? $instance['embed_align'] : 0;
        ?>
        <?php if (!empty($campaigns)): ?>
            <p>
                <label for="<?php echo $this->get_field_id('campaign_id'); ?>"><?php echo $apsa_admin_labels["camp_name_embed"]; ?>:</label>
                <select class="widefat" id="<?php echo $this->get_field_id('campaign_id'); ?>" name="<?php echo $this->get_field_name('campaign_id'); ?>">
                    <option value="0"><?php echo $apsa_admin_labels["select_campaign"]; ?></option>
                    <?php
                    foreach ($campaigns as $key => $campaign) {
                        ?>
                        <option value="<?php echo $campaign['id']; ?>"<?php if ($campaign['id'] == esc_attr($campaign_id)): ?> selected="selected"<?php endif; ?>><?php echo!empty($campaign['name']) ? $campaign['name'] : "No tiltle (id is " . $campaign['id'] . ")"; ?></option>
                        <?php
                    }
                    ?>
                </select>
                <span><?php echo $apsa_admin_labels["select_embed_campaign"]; ?></span>
            </p>
            <p>    
                <label for="<?php echo $this->get_field_id('embed_align'); ?>"><?php echo $apsa_admin_labels["embed_alignment"]; ?>:</label>
                <select class="widefat" id="<?php echo $this->get_field_id('embed_align'); ?>" name="<?php echo $this->get_field_name('embed_align'); ?>"> 
                    <option value="none" <?php if ('none' == esc_attr($embed_align)): ?> selected="selected"<?php endif; ?>><?php echo $apsa_admin_labels["alignment_none"]; ?></option>
                    <option value="left" <?php if ('left' == esc_attr($embed_align)): ?> selected="selected"<?php endif; ?>><?php echo $apsa_admin_labels["alignment_left"]; ?></option>
                    <option value="center" <?php if ('center' == esc_attr($embed_align)): ?> selected="selected"<?php endif; ?>><?php echo $apsa_admin_labels["alignment_center"]; ?></option>
                    <option value="right" <?php if ('right' == esc_attr($embed_align)): ?> selected="selected"<?php endif; ?>><?php echo $apsa_admin_labels["alignment_right"]; ?></option>
                </select>
                <span><?php echo $apsa_admin_labels["select_embed_alignment"]; ?></span>
            </p>
        <?php else: ?>
            <p><?php echo $apsa_admin_labels["no_campaign_to_select"]; ?></p> 
        <?php endif; ?>
        <?php
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update($new_instance, $old_instance) {

        $instance = array();
        $instance['campaign_id'] = (!empty($new_instance['campaign_id']) ) ? strip_tags($new_instance['campaign_id']) : '';
        $instance['embed_align'] = (!empty($new_instance['embed_align']) ) ? strip_tags($new_instance['embed_align']) : '';
        return $instance;
    }

}

// register Foo_Widget widget
function register_apsa_campaign_embed() {
    register_widget('APSA_Campaign_Embed');
}

add_action('widgets_init', 'register_apsa_campaign_embed');

/**
 * Embed Auto Placement
 * Add embed element on post content
 * @param $content
 * @return changed content
 */
function apsa_embed_on_post($content) {

    // Check if post or custom post, page or attachment single page
    if (!is_singular() || !in_the_loop() || !is_main_query()) {
        return $content;
    }

    remove_filter('the_content', 'apsa_embed_on_post');

    global $apsa_plugin_data;
    global $apsa_active_campaigns;

    $apsa_extra_options = get_option('apsa_extra_options');

    // Check if plugin require anticache
    $anticache = isset($apsa_extra_options['apsa_cache_enabled']) ? $apsa_extra_options['apsa_cache_enabled'] : 'false';
    $clearfix = "<div class='apsa-clearfix'></div>";

    if ($anticache == 'true') {
        $content = '<div class="apsa-placement-holder apsa-before-holder"></div>' . $content . '<div class="apsa-placement-holder apsa-after-holder"></div>';
    } else if (isset($apsa_active_campaigns) && !empty($apsa_active_campaigns)) {

        if (count($apsa_active_campaigns) == count($apsa_active_campaigns, COUNT_RECURSIVE)) {
            $apsa_active_campaigns = array($apsa_active_campaigns);
        }

        // Get campaigns ids
        $campaign_ids = wp_list_pluck($apsa_active_campaigns, 'id');

        // Get campaigns options
        $campaigns_options = apsa_get_campaign_options($campaign_ids);

        $camp_options_arr = array();
        foreach ($campaigns_options as $campaigns_option) {
            $camp_options_arr[$campaigns_option['campaign_id']][$campaigns_option['option_name']] = $campaigns_option['option_value'];
        }

        $before_content = "";
        $after_content = "";		
        foreach ($camp_options_arr as $camp_id => $camp_options) {
            if (!isset($camp_options['auto_placement']) || $camp_options['auto_placement'] == "off") {
                continue;
            }

            $before_align = ($camp_options['before_align'] != "none") ? " align='" . $camp_options['before_align'] . "'" : "";
            $after_align = ($camp_options['after_align'] != "none") ? " align='" . $camp_options['after_align'] . "'" : "";

            switch ($camp_options['auto_placement']) {
                case 'before':
                    $before_content = $before_content . do_shortcode("[" . strtolower($apsa_plugin_data['plugin_data']['name']) . " id='" . $camp_id . "'" . $before_align . "]");
                    break;
                case 'after':
                    $after_content = $after_content . do_shortcode("[" . strtolower($apsa_plugin_data['plugin_data']['name']) . " id='" . $camp_id . "'" . $after_align . "]");
                    break;
                case 'both':
                    $before_content = $before_content . do_shortcode("[" . strtolower($apsa_plugin_data['plugin_data']['name']) . " id='" . $camp_id . "'" . $before_align . "]");
                    $after_content = $after_content . do_shortcode("[" . strtolower($apsa_plugin_data['plugin_data']['name']) . " id='" . $camp_id . "'" . $after_align . "]");
                    break;
                default:
                    break;
            }
        }
        
	$before_content = empty($before_content) ? $before_content : $before_content . $clearfix; 
	$after_content = empty($after_content) ? $after_content : $after_content . $clearfix; 
        $content = $before_content . $content . $after_content;
    }

    return $content;
}

add_filter('the_content', 'apsa_embed_on_post', 100);

/**
 * Ajax handler for return autoplacements contents
 */
function apsa_ajax_get_auto_placements() {

    $response = array('success' => 1, 'exists' => 0);

    global $apsa_active_campaigns;

    if (!isset($apsa_active_campaigns) || empty($apsa_active_campaigns)) {
        echo json_encode($response);
        die();
    }

    if (count($apsa_active_campaigns) == count($apsa_active_campaigns, COUNT_RECURSIVE)) {
        $apsa_active_campaigns = array($apsa_active_campaigns);
    }

    $apsa_page_info = $_POST['apsa_page_info'];

    // Get campaigns ids
    $campaign_ids = wp_list_pluck($apsa_active_campaigns, 'id');

    // Get campaigns options
    $campaigns_options = apsa_get_campaign_options($campaign_ids);

    $camp_options_arr = array();
    foreach ($campaigns_options as $campaigns_option) {
        $camp_options_arr[$campaigns_option['campaign_id']][$campaigns_option['option_name']] = $campaigns_option['option_value'];
    }

    $before_content = "";
    $after_content = "";
	$clearfix = "<div class='apsa-clearfix'></div>";
	
    foreach ($camp_options_arr as $camp_id => $camp_options) {
        if (!isset($camp_options['auto_placement']) || $camp_options['auto_placement'] == "off") {
            continue;
        }

        $atts = array('id' => $camp_id, 'vc_class' => '');

        switch ($camp_options['auto_placement']) {
            case 'before':
                $atts['align'] = $camp_options['before_align'];
                $embed_cont = apsa_embed_campaign_func($atts, NULL, '', $apsa_page_info, TRUE);
                $before_content = $before_content . $embed_cont;
                break;
            case 'after':
                $atts['align'] = $camp_options['after_align'];
                $embed_cont = apsa_embed_campaign_func($atts, NULL, '', $apsa_page_info, TRUE);
                $after_content = $after_content . $embed_cont;
                break;
            case 'both':
                $atts['align'] = $camp_options['before_align'];
                $embed_cont = apsa_embed_campaign_func($atts, NULL, '', $apsa_page_info, TRUE);
                $before_content = $before_content . $embed_cont;

                $atts['align'] = $camp_options['after_align'];
                $embed_cont = apsa_embed_campaign_func($atts, NULL, '', $apsa_page_info, TRUE);
                $after_content = $after_content . $embed_cont;
                break;
            default:
                break;
        }
    }

    if (!empty($before_content) || !empty($after_content)) {
        $response['exists'] = 1;
    }
	
	$before_content = empty($before_content) ? $before_content : $before_content . $clearfix; 
	$after_content = empty($after_content) ? $after_content : $after_content . $clearfix; 
	
    $response['before'] = $before_content;
    $response['after'] = $after_content;

    echo json_encode($response);

    die();
}

add_action('wp_ajax_nopriv_apsa_ajax_get_auto_placements', 'apsa_ajax_get_auto_placements');
add_action('wp_ajax_apsa_ajax_get_auto_placements', 'apsa_ajax_get_auto_placements');
