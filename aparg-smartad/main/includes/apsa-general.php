<?php

defined('ABSPATH') or die('No script kiddies please!');

/*
 * Add hidden submenu for preview 
 */
add_action('admin_menu', 'apsa_ad_preview_submenu');

function apsa_ad_preview_submenu() {

    add_submenu_page(null, 'preview', 'preview', 'manage_options', 'apsa-ad-preview', 'apsa_ad_preview_page');
}

/**
 * *========= Hooking into preview page head ==============
 * 
 * @global type $apsa_file_path
 */
function apsa_preview_admin_head() {
    if (isset($_POST['temp_slug'])) {
        global $apsa_file_path;
        $temp_data = $_POST['temp_data'];
        $temp_slug = $_POST['temp_slug'];
        $iframe_height = $_POST['iframe_height'];

        $apsa_plugin_dir_url = plugin_dir_url($apsa_file_path);

        $camp_options = array(
            'type' => 'popup',
        );
        $required_options = array(
            'saved_data' => stripslashes($temp_data),
            'slug' => $temp_slug,
            'height' => $iframe_height . 'px',
            'camp_options' => $camp_options,
        );


        $temp_html = apsa_draw_custom_ad($required_options);
        echo '<link rel="stylesheet"  href="' . $apsa_plugin_dir_url . 'framework/view/front/apsa-front-styles.css" type="text/css" media="all" />';
        echo '<style type="text/css"> body{'
        . 'background-image:url("' . $apsa_plugin_dir_url . 'main/view/admin/images/preview/demo-bg.jpg");'
        . 'background-size: cover;'
        . 'background-repeat: no-repeat;'
        . 'background-position: 0 8%;}'
        . 'html{padding:0 !important;}'
        . '.apsa-preview-cont-wrap:before{'
        . 'z-index:100;'
        . 'cursor:pointer;'
        . 'content: "\f158";'
        . 'position:absolute;'
        . 'top:-35px;'
        . 'color:white;'
        . 'right:-5px;'
        . ' font: 400 33px/1 wp-dashicons;'
        . 'speak: none; '
        . 'vertical-align: middle;'
        . '}'
        . '.apsa-preview-cont-overlay{    background: rgba(54,54,54,0.7);position: fixed;  '
        . 'top: 0;left: 0;right: 0;bottom: 0;'
        . ' z-index: 100;}'
        . '#apsa-popup-cont.apsa-preview-wrap{'
        . 'transform: scale(0.75) !important;'
        . '-o-transform: scale(0.75) !important;'
        . '-moz-transform: scale(0.75) !important;'
        . '-webkit-transform: scale(0.75) !important;'
        . 'position: absolute;'
        . 'height:100%;'
        . 'width:67%;'
        . 'margin:auto;'
        . 'top:0;'
        . 'left:0;box-sizing:border-box;'
        . 'right:0;'
        . 'bottom:0;}'
        . '#apsa-popup-cont .apsa-preview-cont-wrap{'
        . 'border: solid 6px white;'
        . 'border-radius: 3px;'
        . 'background-color: white;'
        . '}'
        . '@media only screen and (max-width: 810px){'
        . ' #apsa-popup-cont .apsa-custom-popup-wrap,'
        . '  #apsa-popup-cont .apsa-preview-cont-wrap {'
        . ' height: 100% !important;}}'
        . '@media only screen and (max-width: 500px){'
        . '#apsa-popup-cont.apsa-preview-wrap{'
        . 'transform: scale(0.75) !important;'
        . '-o-transform: scale(0.75) !important;'
        . '-moz-transform: scale(0.75) !important;'
        . '-webkit-transform: scale(0.75) !important;'
        . 'transform-origin: 0 0 !important;'
        . '-webkit-transform-origin: 0 0 !important;'
        . '-ms-transform-origin: 0 0 !important;'
        . '-moz-transform-origin: 0 0 !important;'
        . '-o-transform-origin: 0 0 !important;'
        . 'position: absolute;'
        . 'margin:0;'
        . 'height: 134% !important;'
        . 'width:134% !important;'
        . 'top:0;'
        . 'left:0;}'
        . '#apsa-popup-cont .apsa-preview-cont-wrap{'
        . 'border:none;'
        . '}'
        . '#apsa-popup-cont .apsa-preview-cont-wrap:before{'
        . 'top:0;'
        . 'right:0;}'
        . '}'
        . '</style>';
        echo '<script  type="text/javascript" src="' . $apsa_plugin_dir_url . 'main/view/front/apsa-child-front.js"></script>';
        echo '<script  type="text/javascript" src="' . $apsa_plugin_dir_url . 'framework/view/admin/apsa-general-scripts.js"></script>';
        echo'</head><body class="apsa-preview-body">';
        echo '<div class="apsa-preview-cont-overlay"></div><div id="apsa-popup-cont" style="" class="apsa-reset-start  apsa-preview-wrap"><div class="apsa-preview-cont-wrap">';
        echo $temp_html;
        echo '</div></div>';
        echo '<script  type="text/javascript" >
            setTimeout(function () { 
              if (typeof window["apsa_' . $temp_slug . '_resize"] == "function") {
                  window["apsa_' . $temp_slug . '_resize"](jQuery("#apsa-popup-cont .apsa-custom-popup-wrap"),true);
                    }
                  }, 200);
        apsa_reset_style();
           jQuery(document).off();
                    jQuery(".apsa-preview-wrap *").off();
                   jQuery(document).on("click",".apsa-preview-wrap *",function(e){
                      e.preventDefault(); return false;
                    });
        </script>
            </body>
        </html>';
        exit();
    } else {
        echo'<style>body, html {height:auto}</style></head>';
        wp_die(__('Sorry, you are not allowed to access this page.'), 403);
    }
}

add_action('admin_head-admin_page_apsa-ad-preview', 'apsa_preview_admin_head');

/**
 * *================== Preview page calback ==========
 */
function apsa_ad_preview_page() {
    //silence is gold  
}

/*
 * =========== Custom ad page redirection and saving form data into DB ==========
 */

add_action('admin_init', 'apsa_custom_ad_page_redirect');

function apsa_custom_ad_page_redirect() {
    if (isset($_GET['page']) && $_GET['page'] === "apsa-custom-ads") {
        //cheking id of editing form page
        if (isset($_GET['form'])) {

            $id_check = (isset($_GET['id'])) ? apsa_is_id_exist($_GET['id']) : false;

            if ((($_GET['form'] != 'edit' ) && $_GET['form'] != 'new')) {
                wp_redirect(menu_page_url("apsa-custom-ads", false));
                exit;
            } else if (($_GET['form'] == 'edit')) {
               if (!$id_check) {
                    $message = __("You attempted to edit an item that doesn't exist. Perhaps it was deleted?");
                    wp_die("<style>body, html {height:auto !important;}</style>" . $message, 403);
                } elseif ($id_check == 'trash') {
                    $message = __("You can't edit this item because it is in the Trash. Please restore it and try again.");
                    wp_die("<style>body, html {height:auto !important;}</style>" . $message, 403);
                }
            }
        }


        $is_trashed = false;
        $is_deleted_restored = false;
        /*
         * =================== Sending to trash page ================================
         */
// bulk trash

        if (isset($_POST['trash'])) {
            $ids = array_map('intval', json_decode(stripslashes($_POST['trash'])));
            $is_trashed = apsa_update_custom_ad_status($ids, 'trash');
        }
        //single trash
        if (isset($_POST['single_trash'])) {
            $trashed_ids = array_map('intval', array($_POST['single_trash']));
            $is_trashed = apsa_update_custom_ad_status($trashed_ids, 'trash');
        }

        /*
         * =================== Restoring from trash page ================================
         */
        //single restoring from trash
        if (isset($_POST['single_restore'])) {
            $is_deleted_restored = apsa_update_custom_ad_status(intval($_POST['single_restore']));
        }
        //bulk restoring from trash
        if (isset($_POST['restore'])) {
            $ids = array_map('intval', json_decode(stripslashes($_POST['restore'])));
            $is_deleted_restored = apsa_update_custom_ad_status($ids, 'available');
        }

        /*
         * =================== Deleting permanently  ================================
         */
        //bulk delete 
        if (isset($_POST['delete'])) {
            $ids = array_map('intval', json_decode(stripslashes($_POST['delete'])));
            $is_deleted_restored = apsa_delete_custom_ad($ids);
        }
        //single delete
        if (isset($_POST['single_delete'])) {
            $is_deleted_restored = apsa_delete_custom_ad(intval($_POST['single_delete']));
        }


        /*
         * single form page saving data into database and redirection part
         */

        if (isset($_POST['apsa-save-settings'])) {
            $is_new = sanitize_text_field($_POST['apsa-save-settings']); // ID of page or true if page is new 

            $title = ($_POST['apsa-new-form-title']);

            $data = ($_POST['apsa-module-data']);

            $slug = ($_POST['apsa-template-select']);
             if ($is_new == 'true') {
                $form_all = apsa_insert_custom_ad($title, $slug, $data);


                $id = $form_all['id'];
                wp_redirect(menu_page_url("apsa-custom-ads", false) . "&saved=true&form=edit&id=" . $id);
                exit;
            } else {
                $id = sanitize_text_field($_GET['id']);
                apsa_update_custom_ad($id, $title, $slug, $data);
            }
        }
    }
}
