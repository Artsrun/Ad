<?php
defined('ABSPATH') or die('No script kiddies please!');

///== Add settings page in plugin menu ==
add_action('admin_menu', 'apsa_child_social_forms_page');

function apsa_child_social_forms_page() {

    global $apsa_admin_labels;
    add_submenu_page('apsa-manage-campaigns', $apsa_admin_labels["custom_ads"], $apsa_admin_labels["custom_ads"], 'manage_options', 'apsa-custom-ads', 'apsa_custom_ads_page');
}

/*
 * ======= Create settings page ============= 
 */

function apsa_custom_ads_page() {

    /*
     * ========= Calling fuctions for outpution single and general form  pages html ========
     */

    if (isset($_GET['form'])) {

        apsa_single_custom_ad_page();
    } else {
        apsa_general_custom_ad_page();
    }
}

/*
 * ======== Single Form Page  html and other functionality =============
 */

function apsa_single_custom_ad_page() {

    global $apsa_admin_labels;
    global $apsa_file_path;
    global $apsa_templates;
    $is_new = (isset($_GET['form']) && $_GET['form'] == 'edit') ? $_GET['id'] : 'true';
    $page_title = ($_GET['form'] == 'edit') ? $apsa_admin_labels["edit_ad"] : $apsa_admin_labels["add_new_ad"];


    $all_template_data = apsa_get_all_templates_data();
    $form_slug = $all_template_data[0]['slug'];
    $form_title = '';
    /*
     * ======== Checking whether editing or creating new social form =========
     */
    if ($is_new == 'true') {
        $publish = $apsa_admin_labels["publish"];
        $add_new_button = '';
        $button_title = $apsa_admin_labels["save"];
    } else {
        $publish = $apsa_admin_labels["published_on"] . "  :";
        $add_new_button = '<a href="' . menu_page_url("apsa-custom-ads", false) . '&form=new" class="apsa-page-title-action">' . $apsa_admin_labels["add_new"] . '</a>';
        $button_title = $apsa_admin_labels["update"];
    }

    /*
     * ===== After submitting page  ========
     */
    if (isset($_POST['apsa-save-settings'])) {
        $is_new = sanitize_text_field($_POST['apsa-save-settings']);
        $form_title = sanitize_text_field($_POST['apsa-new-form-title']);
    }


    /*
     * ================Outputing Saved message=============================
     */



    $apsa_saved_message = isset($_GET['saved']) ? $apsa_admin_labels['ad_saved'] : $apsa_admin_labels['ad_updated'];
    ?>
    <!-- Messages -->
    <div class="apsa-popup apsa-transit-450" id="apsa-message-popup" data-apsa-open="false">
        <p></p>
        <span class="apsa-close-popup"></span>
    </div>
    <div class="apsa-social-form-content" >
        <div class="apsa-wrap">
            <input type='hidden' class="apsa-submittable-input" name='apsa-save-settings' value='<?php echo $is_new ?>'>
            <input type='hidden' class="apsa-submittable-input" id='apsa-module-data' name='apsa-module-data' value=''>
            <h1 class='apsa-page-title'>
                <?php echo $page_title ?>
                <?php echo $add_new_button ?>
            </h1>
            <?php
            if (isset($_POST['apsa-save-settings']) || isset($_GET['saved'])) {
                echo '<div style="position:relative;" id="message" class="apsa-updated updated fade"><p><strong>' . $apsa_saved_message . '</strong></p> <span class="apsa-dismissible"></span></div>';
            }
            ?>
            <div class='apsa-form-body'>
                <div class='apsa-module-wrap'>
                    <div class="apsa-form-content">
                        <div class='apsa-form-title'>
                            <div class="apsa-title-wrap">
                                <input class='apsa-single-form-title apsa-submittable-input' placeholder="<?php echo $apsa_admin_labels["ad_page_placeholder"] ?>" type="text" name="apsa-new-form-title" size="30" value="<?php echo $form_title ?>" id="apsa-single-form-title" spellcheck="true" autocomplete="off">
                            </div>
                        </div>
                        <div class='apsa-slide-cont'>
                            <h3 class="apsa-slide-opener apsa-builder-slide" data-apsa-open-slide="true">
                                <span><?php echo $apsa_admin_labels['build_your_ad'] ?></span>
                                <span class="apsa-slide-open-pointer"></span>
                            </h3>
                            <div class="apsa-sliding-block" data-apsa-open="true">
                                <div class='apsa-module-body'></div>
                            </div>
                        </div>
                        <div class='apsa-slide-cont'>

                            <h3 class="apsa-slide-opener apsa-preview-slide" data-apsa-open-slide="true">
                                <span><?php echo $apsa_admin_labels['preview'] ?></span>
                                <span class="apsa-slide-open-pointer"></span>
                            </h3>

                            <div class="apsa-sliding-block" data-apsa-open="true">
                                <div id="apsa-preview-container">
                                    <div class="apsa-preview-head">
                                        <span  data-btn="desktop" class="apsa-prv-desktop apsa-prev-btn apsa-active-btn"><span></span></span>
                                        <span   data-btn="tablet" class="apsa-prv-tablet apsa-prev-btn"><span></span></span>
                                        <span  data-btn="mobile" class="apsa-prv-mobile apsa-prev-btn"><span></span></span>
                                    </div>
                                    <div class="apsa-preview-body apsa-prv-desktop-body">
                                        <div>
                                            <iframe scrolling="no"  name="apsa_preview_iframe_name" id="apsa-post-to-iframe" ></iframe>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class='apsa-right-sidebbar'>

                    <div class="apsa-slide-cont apsa-side-general-block">
                        <h3 class="apsa-slide-opener" data-apsa-open-slide="true">
                            <span><?php echo $apsa_admin_labels["general_opt"] ?></span>
                            <span class="apsa-slide-open-pointer"></span>
                        </h3>
                        <div class="apsa-sliding-block" data-apsa-open="true">
                            <span class="apsa-timestamp">
                                <?php echo $publish ?>
                                <b></b>
                            </span>
                        </div>

                        <div class="apsa-save-form-cont">
                            <?php if ($is_new != 'true') { ?>
                                <div class = 'apsa-move-trash-btn-wrap'>
                                    <a class='apsa-move-trash-btn' data-id="<?php echo $is_new; ?>" href="<?php echo menu_page_url('apsa-custom-ads', false) . '&trash=true&id=' . $is_new ?>" ><?php echo $apsa_admin_labels["move_to_trash"] ?></a>
                                </div>
                            <?php } ?>
                            <div class="apsa-update-btn-wrapper">
                                <input type="button" class="button button-primary apsa-save-form-options" id='apsa-save-form-options' value="<?php echo $button_title ?>">
                            </div>
                        </div>
                    </div>

                    <div class="apsa-slide-cont">
                        <h3 class="apsa-slide-opener" data-apsa-open-slide="true">
                            <span><?php echo $apsa_admin_labels["ad_template"] ?></span>
                            <span class="apsa-slide-open-pointer"></span>
                        </h3>
                        <div class="apsa-sliding-block" data-apsa-open="true">
                            <div class='apsa-social-template-select'>
                                <div class="apsa-template-screenshot">
                                    <?php if (!empty($apsa_templates)): ?>
                                        <img class='apsa-template-screenshot-img' src="<?php echo plugin_dir_url($apsa_file_path) . 'templates/form/' . $form_slug . '/screenshot.png'; ?>" />
                                    <?php endif; ?>
                                    <div class='apsa-label'>
                                        <span class="apsa-select-template-button"><?php echo $apsa_admin_labels["choose_template"] ?></span>
                                    </div>
                                </div>

                                <div class='apsa-admin-popup-overlay'></div>
                                <div class='apsa-popup-container'>
                                    <div class="apsa-popup-header" >
                                        <span class="apsa-popup-title"><?php echo $apsa_admin_labels["choose_template"] ?></span>
                                        <span class="apsa-close-popup"></span>
                                    </div>
                                    <div class='apsa-admin-popup-content'>

                                        <?php
                                        $selected_name = '';
                                        if (!empty($apsa_templates)) {
                                            ?>
                                            <?php
                                            foreach ($apsa_templates as $key => $value) {

                                                $selected_class = '';
                                                $bg_path = plugin_dir_url($apsa_file_path) . 'templates/form/' . $value['slug'] . '/screenshot.png'; //template background image path
                                                if ($value['slug'] == $form_slug) {
                                                    $selected_name = $value['name'];
                                                    $selected_class = 'apsa-block-selected';
                                                }
                                                $demo_path = plugin_dir_path($apsa_file_path) . 'templates/form/' . $value['slug'] . '/demo/demo.json';
                                                $demo_data = file_exists($demo_path) ? file_get_contents($demo_path) : '[]';
                                                ?>
                                                <div class='apsa-template-container'>
                                                    <h4 class='apsa-choose-template-name'><?php echo $value['name'] ?></h4>
                                                    <div data-img ='<?php echo $bg_path ?>' data-name='<?php echo $value['name']; ?>' data-slug='<?php echo $value['slug']; ?>' class='apsa-template-block <?php echo $selected_class ?>' style=' background-image: url(<?php echo $bg_path ?>)' ><div></div>
                                                        <input type="hidden"  value="<?php echo htmlspecialchars($demo_data) ?>" class="apsa-hidden-demo">
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                        }
                                        ?>

                                    </div>
                                    <div class="apsa-popup-footer">
                                        <button class="button-primary" id="apsa-template-choose-button"><?php echo $apsa_admin_labels['ok'] ?></button>
                                    </div>
                                </div>
                                <input value="<?php echo $form_slug; ?>" data-slug="<?php echo $form_slug; ?>" class="apsa-submittable-input" data-name="<?php echo $selected_name; ?>" type="hidden" id='apsa-template-select' name='apsa-template-select'>
                            </div>

                        </div>

                    </div>




                </div>
            </div>

        </div>

    </div>



    <?php
}

/**
 * *===== General forms page html =========== 
 * 
 * @global array $apsa_admin_labels
 */
function apsa_general_custom_ad_page() {
    global $apsa_admin_labels;
    global $apsa_templates;

    //get names  and slugs of templates for outputing 
    if (!empty($apsa_templates)) {
        $templates_labels = array();
        foreach ($apsa_templates as $index => $template_data) {

            $templates_labels[$template_data['slug']] = array(
                'name' => $template_data['name'],
            );
        }
    }

    $counts_and_dates = apsa_get_counts_and_dates();
    $show_at_most = 20;
    $trash_count = isset($counts_and_dates['trash']) ? $counts_and_dates['trash']['count'] : 0;
    $available_count = isset($counts_and_dates['available']) ? $counts_and_dates['available']['count'] : 0;

    $trash_dates = isset($counts_and_dates['trash']) ? $counts_and_dates['trash']['date'] : array();
    $available_dates = isset($counts_and_dates['available']) ? $counts_and_dates['available']['date'] : array();

    $status = (isset($_GET['status']) && $_GET['status'] == 'trash') ? 'trash' : 'available';
    $paged = ( isset($_GET['paged']) ) ? intval($_GET['paged']) : 1;

    $sorting_order = (isset($_GET['order']) && ($_GET['order'] == 'asc')) ? 'asc' : 'desc';
  
    $order_by = ( isset($_GET['orderby']) && $_GET['orderby'] == 'title' ) ? 'title' : 'id';
    $order_by = ( isset($_GET['orderby']) && $_GET['orderby'] == 'date' ) ? 'creation_date' : $order_by;

    $active_count = ($status == "available") ? $available_count : $trash_count;
    $date_array = ($status == "available") ? $available_dates : $trash_dates;

    $filter_by_date = (isset($_GET['m']) && strtotime($_GET['m'])) ? sanitize_text_field($_GET['m']) : false;

    //===================== Get search Results ====================
    $search = false;
    if (isset($_GET['s']) && $_GET['s']) {

        $search = ($_GET['s']);
        $search_results = apsa_get_search_results($paged, $show_at_most, $search, $sorting_order, $order_by, $status, $filter_by_date);
        $active_count = $search_results['count'];
        $item_numbers = $active_count ? $active_count . " " . $apsa_admin_labels["items"] : '';
        $item_numbers = $active_count == 1 ? $active_count . " " . $apsa_admin_labels["item"] : $item_numbers;
        $all_forms = $search_results['forms'];
        $status = ($status == 'trash') ? $status : 'search';
    } else {
        $all_forms = apsa_get_custom_ads_with_limit($paged, $show_at_most, $status, $sorting_order, $order_by, $filter_by_date);
        $active_count = isset($all_forms['filtered_count']) ? $all_forms['filtered_count'] : $active_count;
        $all_forms = isset($all_forms['filtered_data']) ? $all_forms['filtered_data'] : $all_forms;
        $item_numbers = $active_count ? $active_count . " " . $apsa_admin_labels["items"] : '';
        $item_numbers = $active_count == 1 ? $active_count . " " . $apsa_admin_labels["item"] : $item_numbers;
        $status = ($filter_by_date && $status != 'trash' || (isset($_GET['order']) && $status != 'trash')) ? 'search' : $status;
    }

    $status = ($status == 'available' && $active_count == 0 && !isset($_GET['clicked'])) ? 'search' : $status;
    $sorting_order = ((isset($_GET['order']) && ($_GET['order'] != 'asc' && $_GET['order'] != 'desc')) || !isset($_GET['order'])) ? 'initial' : $sorting_order;

    $max_num_pages = intval(ceil($active_count / $show_at_most));
    $big = 999999999;

    $pagination = paginate_links(array(
        'base' => str_replace($big, '%#%', html_entity_decode(get_pagenum_link($big))),
        'format' => '?paged=%#%',
        'current' => max(1, $paged),
        'total' => $max_num_pages,
        'prev_text' => '‹',
        'next_text' => '›',
        'type' => 'plain',
        'mid_size' => 0,
    ));
    ?>

    <div class="apsa-social-form-content" >
        <div id="apsa-social-forms-page-wrap" class="apsa-wrap">
            <h1 class='apsa-page-title'>
                <?php echo $apsa_admin_labels["custom_ads"] ?><a href="<?php echo menu_page_url('apsa-custom-ads', false) . '&form=new' ?>"class="apsa-page-title-action"><?php echo $apsa_admin_labels["add_new"] ?></a>
                <?php if ($search !== false) { ?>
                    <span class="apsa-subtitle"><?php echo $apsa_admin_labels["search_res_for"] . " &ldquo;$search&rdquo; " ?></span>
                <?php } ?>
            </h1>
            <?php
            if (isset($_POST['single_trash']) || isset($_POST['trash']) || isset($_POST['restore']) || isset($_POST['single_restore']) || isset($_POST['delete']) || isset($_POST['single_delete'])) {
                if (isset($_POST['single_trash']) || isset($_POST['trash'])) {
                    $ids = isset($_POST['trash']) ? $_GET['ids'] : $_POST['single_trash'];
                    $count = count(explode(',', urldecode($_GET['ids'])));
                    $apsa_saved_message = (isset($_POST['single_trash']) || $count == 1) ? '1 ' . $apsa_admin_labels['ad_moved_trash'] : $count . " " . $apsa_admin_labels['ads_moved_trash'];
                    $undo = '<a class="apsa-undo" data-ids="' . $ids . '" href="#">' . $apsa_admin_labels['undo'] . '</a>';
                } elseif (isset($_POST['restore']) || isset($_POST['single_restore'])) {
                    $ids = isset($_POST['restore']) ? $_GET['ids'] : $_POST['single_restore'];
                    $count = count(explode(',', urldecode($_GET['ids'])));
                    $apsa_saved_message = (isset($_POST['single_restore']) || $count == 1) ? '1 ' . $apsa_admin_labels['ad_restored_trash'] : $count . " " . $apsa_admin_labels['ads_restored_trash'];
                    $undo = '';
                } elseif (isset($_POST['delete']) || isset($_POST['single_delete'])) {
                    $ids = isset($_POST['delete']) ? $_GET['ids'] : $_POST['single_delete'];
                    $count = count(explode(',', urldecode($_GET['ids'])));
                    $apsa_saved_message = (isset($_POST['single_restore']) || $count == 1) ? '1 ' . $apsa_admin_labels['ad_permanently_deleted'] : $count . " " . $apsa_admin_labels['ads_permanently_deleted'];
                    $undo = '';
                }
                echo '<div style="position:relative;" id="message" class="apsa-updated updated fade"><p>' . $apsa_saved_message . '' . $undo . '</p> <span class="apsa-dismissible"></span></div>';
            }
            ?>
            <div>
                <ul class="apsa-templates-navigation">
                    <li>
                        <span class="apsa-status-name "><a class=" <?php echo ($status == "available") ? "apsa-selected-status" : "" ?>" href="<?php echo menu_page_url('apsa-custom-ads', FALSE) . '&status=available&clicked=true' ?>"><?php echo $apsa_admin_labels["available"]; ?></a></span>
                        <span class="apsa-status-count"><?php echo $available_count; ?></span>
                    </li>
                    <?php if ($trash_count) { ?>
                        <li>
                            <span class="apsa-status-name"><a class= "<?php echo ($status == "trash") ? "apsa-selected-status" : '' ?>"  href="<?php echo menu_page_url('apsa-custom-ads', FALSE) . '&status=trash' ?>"><?php echo $apsa_admin_labels["trash"] ?></a></span>
                            <span class="apsa-status-count"><?php echo $trash_count; ?></span>
                        </li>
                    <?php } ?>
                </ul><!-- ======= added search box also css    =========== -->
                <p class="apsa-search-box">
                    <input type="search" id="apsa-form-search-input" name="s" value="<?php echo ($search !== false) ? $search : ''; ?>">
                    <input type="button" id="apsa-search-submit" class="apsa-button" value="<?php echo $apsa_admin_labels["search_ads"]; ?>"></p>
            </div><!-- ===================  -->
            <div class="apsa-tablenav apsa-top">
                <div class="apsa-bulkactions">
                    <?php
                    //=========== added filter by date
                    if (!empty($all_forms)) {
                        ?>
                        <select name="action" id="">
                            <option value="-1"><?php echo $apsa_admin_labels["bulk_action"] ?></option>
                            <?php if ($status == "available" || $status == "search") { ?>
                                <option value="trash"><?php echo $apsa_admin_labels["move_to_trash"] ?></option>
                            <?php } else { ?>
                                <option value="delete"><?php echo $apsa_admin_labels["delete_permanently"] ?></option>
                                <option value="restore"><?php echo $apsa_admin_labels["restore"] ?></option>
                            <?php } ?>
                        </select>
                        <button id="" class="apsa-button apsa-bulk-button" ><?php echo $apsa_admin_labels["apply"] ?></button>
                        <?php
                    }  //===============  
                    array_unshift($date_array, $apsa_admin_labels["all_dates"]);
                    ?>
                    <div class="apsa-date-filter-wrap" <?php echo (empty($all_forms)) ? 'style="margin-left:0"' : '' ?> > 
                        <select name="m" id="apsa-filter-by-date">
                            <?php foreach ($date_array as $key => $value) { ?>
                                <option  <?php echo ($key == $filter_by_date) ? 'selected' : '' ?> value="<?php echo $key ?>"><?php echo $value ?></option>
                            <?php } ?>
                        </select>
                        <input type="button" name="filter_action" id="apsa-date-filter" class="apsa-button" value="<?php echo $apsa_admin_labels["filter"]; ?>">
                    </div>

                    <div class='apsa-pagination'>
                        <span class="apsa-displaying-num"><?php echo $item_numbers ?></span>
                        <?php
                        echo $pagination;
                        ?>
                    </div>
                    <br class="apsa-clear">
                </div>
            </div>
            <table class="<?php echo 'apsa-order-' . $sorting_order . ' ' . 'apsa-orderby-' . $order_by; ?> apsa-forms-list-table  <?php echo (empty($all_forms)) ? 'apsa-no-forms-found' : ''; ?>  ">
                <thead>
                    <tr>
                        <td id="" class="apsa-check-column"><input class='apsa-bulk-check' id="" type="checkbox"></td>
                        <th scope="col"  class="apsa-form-table-title apsa-manage-column apsa-column-title">
                <div>
                    <span><?php echo $apsa_admin_labels["title"] ?></span>
                    <span class="apsa-sorting-pointer"></span>
                </div>
                </th>

                <th scope="col" id="" class="apsa-manage-column  apsa-column-template">
                    <span>
                        <span><?php echo $apsa_admin_labels["ad_template"] ?></span>
                    </span>
                </th>
               
                <th scope="col" id="" class="apsa-manage-column  apsa-column-date">
                <div>
                    <span><?php echo $apsa_admin_labels["date"] ?></span>
                    <span class="apsa-sorting-pointer"></span>
                </div>
                </th>
                </tr>
                </thead>

                <tbody class='' id=""> 
                    <?php if (empty($all_forms)) { ?>
                        <tr class="apsa-no-items">
                            <td class="apsa-colspanchange" colspan="6"><?php echo ($status == 'trash') ? $apsa_admin_labels["no_ads_trash"] : $apsa_admin_labels["no_ads"] ?></td>
                        </tr>
                        <?php
                    } else {
                        foreach ($all_forms as $key => $value) {
                            ?>
                            <tr id="" class="apsa-row apsa-slide-cont">
                                <th scope="row" class="apsa-check-column">
                                    <input  id="" class='apsa-form-checkbox' data-id='<?php echo $value['id'] ?>' type="checkbox" name="" value="">
                                </th>
                                <td class="apsa-column-title apsa-row-title" data-colname="<?php echo $apsa_admin_labels["title"] ?>">
                                    <strong>
                                        <?php if ($status == 'trash') { ?>
                                            <span><?php echo ($value['title']=="" ? "(" .$apsa_admin_labels["no_title"].")":$value['title']) ?></span>   
                                        <?php } else { ?>
                                            <a class="" href="<?php echo menu_page_url('apsa-custom-ads', false) . '&form=edit&id=' . $value['id'] ?>" aria-label=""><?php echo ($value['title']=="" ? "(" .$apsa_admin_labels["no_title"].")":$value['title']) ?></a>           
                                        <?php } ?>
                                    </strong>
                                    <div class="apsa-row-actions">
                                        <?php
                                        $edit_action = ($status == 'trash') ? 'restore' : 'edit';
                                        $restore_action = ($status == 'trash') ? 'restore=true&status=trash' : 'form=edit';
                                        $delete_action = ($status == 'trash') ? 'delete' : 'trash';
                                        $btn_class = ($status == 'trash') ? 'apsa-restore-btn' : 'apsa-edit-btn';
                                        ?>
                                        <span class="apsa-edit"><a href="<?php echo menu_page_url('apsa-custom-ads', false) . '&' . $restore_action . '&id=' . $value['id'] ?>" class="<?php echo $btn_class ?>"  data-id="<?php echo $value['id'] ?>" data-action="<?php echo $edit_action; ?>"><?php echo ($status == 'trash') ? $apsa_admin_labels["restore"] : $apsa_admin_labels["edit"] ?></a> | </span>
                                        <span class="apsa-trash"><a href="<?php echo menu_page_url('apsa-custom-ads', false) . '&paged=' . $paged . '&' . $delete_action . '=true&id=' . $value['id'] ?>" class="apsa-submitdelete" data-id="<?php echo $value['id'] ?>" data-action="<?php echo $delete_action; ?>"><?php echo ($status == 'trash') ? $apsa_admin_labels["delete_permanently"] : $apsa_admin_labels["trash"] ?></a></span>
                                    </div>
                                    <span class="apsa-toggle-row" data-apsa-open-slide="false"><span class="apsa-slide-open-pointer"></span></span>
                                </td>
                                <td class="apsa-template apsa-column-template apsa-sliding-block"  data-apsa-open="false" data-colname="<?php echo $apsa_admin_labels["ad_template"] ?>" ><?php echo $templates_labels[$value['slug']]['name'] ?></td>
                                <td class="apsa-date apsa-column-date apsa-sliding-block"  data-apsa-open="false" data-colname="<?php echo $apsa_admin_labels["date"] ?>" ><?php echo $apsa_admin_labels['published'] ?><br><abbr title=""><?php echo date_i18n('Y/m/d', strtotime($value['creation_date'])) ?></abbr></td>	
                            </tr>
                            <?php
                        }
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td id="" class="apsa-check-column"><input class='apsa-bulk-check' id="" type="checkbox"></td>
                        <th scope="col"  class="apsa-form-table-title apsa-manage-column apsa-column-title">
                <div><span><?php echo $apsa_admin_labels["title"] ?></span>
                    <span class="apsa-sorting-pointer"></span>
                </div>
                </th>

                <th scope="col" id="" class="apsa-manage-column  apsa-column-template">
                    <span>
                        <span><?php echo $apsa_admin_labels["ad_template"] ?></span>
                    </span>
                </th>
               

                <th scope="col" id="" class="apsa-manage-column  apsa-column-date">
                <div>
                    <span><?php echo $apsa_admin_labels["date"] ?></span>
                    <span class="apsa-sorting-pointer"></span>
                </div>
                </th>
                </tr>
                </tfoot>
            </table>
            <?php if (!empty($all_forms)) { ?>
                <div class="apsa-tablenav apsa-bottom">
                    <div class="apsa-bulkactions">
                        <select name="action" id="">
                            <option value="-1"><?php echo $apsa_admin_labels["bulk_action"] ?></option>
                            <?php if ($status == "available" || $status == "search") { ?>
                                <option value="trash"><?php echo $apsa_admin_labels["move_to_trash"] ?></option>
                            <?php } else { ?>
                                <option value="delete"><?php echo $apsa_admin_labels["delete_permanently"] ?></option>
                                <option value="restore"><?php echo $apsa_admin_labels["restore"] ?></option>
                            <?php } ?>
                        </select>
                        <button id="" class="apsa-button apsa-bulk-button" ><?php echo $apsa_admin_labels["apply"] ?></button>
                        <div class='apsa-pagination'>
                            <span class="apsa-displaying-num"><?php echo $item_numbers ?></span>
                            <?php
                            echo $pagination;
                            ?>
                        </div>
                    </div>
                    <br class="apsa-clear">
                </div>
            <?php } ?>
            <div class="apsa-clear"></div>
        </div>

    </div>

    <?php
}
