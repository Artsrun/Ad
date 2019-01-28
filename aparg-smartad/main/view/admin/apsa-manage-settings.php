<?php
defined('ABSPATH') or die('No script kiddies please!');

//Add settings page in plugin menu
add_action('admin_menu', 'apsa_child_manage_settings_page');

function apsa_child_manage_settings_page() {
    global $apsa_admin_labels;
    add_submenu_page('apsa-manage-campaigns', $apsa_admin_labels["settings_page"], $apsa_admin_labels["settings"], 'manage_options', 'apsa-manage-settings', 'apsa_child_settings_page');
}

/*
 * create settings page 
 */

function apsa_child_settings_page() {
    global $apsa_admin_labels;
    // save settings
    if (isset($_POST['apsa-update-child-settings'])) {
        $apsa_new_options = array();
        if (isset($_POST['apsa-warning-text-enabled'])) {
            $apsa_new_options['apsa_warning_text_enabled'] = $_POST['apsa-warning-text-enabled'];
        } else {
            $apsa_new_options['apsa_warning_text_enabled'] = 'false';
        }
        if (isset($_POST['apsa-warning-text'])) {
            $apsa_new_options['apsa_warning_text'] = $_POST['apsa-warning-text'];
        }
        update_option('apsa_options', $apsa_new_options);
        echo '<div class="updated fade apsa-success-update"><p><strong>' . $apsa_admin_labels["success_update_msg"] . '</strong></p></div>';
    }

    $wp_version = get_bloginfo('version');
    $apsa_options = get_option('apsa_options');
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
                <span><?php echo $apsa_admin_labels["settings"]; ?></span>
                <div id="apsa-save-camps-cont">
                    <div class="apsa-waiting-wrapper">
                        <button class="button button-primary" id="apsa-update-child-settings" name="apsa-update-child-settings"><?php echo $apsa_admin_labels["update_all"]; ?></button>
                    </div>
                </div>
            </h2>
            <span class="apsa-by-aparg"><?php echo $apsa_admin_labels["developed_by"]; ?> <a href="<?php echo APSA_APARG_LINK ?>" target="blank">Aparg.</a></span>
            <div>
                <!-- Warning text -->
                <div id="apsa-warning-text-new" class="apsa-code-type">
                    <div>
                        <label><?php echo $apsa_admin_labels["adblock_warning"]; ?></label>
                        <div class="apsa-warning-text-enabled">
                            <input type="checkbox" id="apsa-warning-text-enabled" name="apsa-warning-text-enabled" value="true" <?php checked('true', $apsa_options['apsa_warning_text_enabled'], true); ?> />
                            <label for="apsa-warning-text-enabled"><?php echo $apsa_admin_labels["adblock_warning_activate"]; ?></label>
                            <span class="apsa-with-question" title="<?php echo ucfirst($apsa_admin_labels["detailed_description"]); ?>" data-apsa-message="<?php echo $apsa_admin_labels["adblock_warning_desc"]; ?>"></span>
                        </div>
                    </div>
                    <div class="apsa-code-type-textarea">
                        <textarea id="apsa-code-area" class="apsa-code-area" name="apsa-warning-text"></textarea>
                        <textarea class="apsa-hold-code-content apsa-hidden-textarea" id="apsa-warning-text-value"><?php echo stripslashes($apsa_options['apsa_warning_text']); ?></textarea>
                        <span class="apsa-input-message"><?php echo $apsa_admin_labels["default"]; ?> <?php echo $apsa_admin_labels["adblock_warning_default"]; ?> </span>
                    </div>
                </div>
            </div>    
        </div>
    </form>
    <?php
}
