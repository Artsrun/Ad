/**
 * Show a prompt before the user leaves the current page
 */

var apsa_url_object = apsa_parse_query_string();
if (apsa_url_object.page == 'apsa-manage-settings') {//========Custom==========
    jQuery(window).load(function () {
        jQuery(document).on('change', 'input, textarea, select', function () {
            apsa_leave_page(false);
        });
    });

// desable apsa-new class 
    if (typeof apsa_new == 'undefined') {
        jQuery('.apsa-new').each(function () {
            jQuery(this).addClass('apsa-new-show');
        });
    }

    jQuery(document).ready(function (jQuery) {
        /*
         * set CodeMirror textareas values
         */
        var code_areas = document.getElementsByClassName("apsa-code-area");
        var myCodeMirrors = {};
        myCodeMirrors.warningText = CodeMirror.fromTextArea(code_areas[0], {
            lineNumbers: true,
            mode: "htmlmixed",
        });

        myCodeMirrors.warningText.setValue(jQuery('#apsa-warning-text-value').val());

        myCodeMirrors.warningText.on('change', function () {
            apsa_leave_page(false);
        });



        /*
         *  save extra options 
         */
        jQuery(document).on("click", "#apsa-update-child-settings", function () {
            apsa_leave_page(true);
        });

        /**
         * Close popup
         */
        jQuery(document).on('click', '#apsa-managing-overlay, .apsa-close-popup', function () {
            jQuery('.apsa-popup').attr('data-apsa-open', "false");
            jQuery('body').removeClass('modal-open');
            jQuery('#apsa-managing-overlay').fadeOut(150);
        });

        /**
         * Close sticky
         */
        jQuery(document).on('click', '.apsa-close-sticky', function () {
            jQuery('.apsa-sticky').attr('data-apsa-open', "false");
            jQuery('body').removeClass('modal-open');
            jQuery('#apsa-managing-overlay').fadeOut(150);
        });

        /**
         * close anticache notice
         */
        jQuery(document).on("click", ".apsa-dismissible", function () {
            jQuery(this).parent('div').hide();
            apsa_set_cookie('apsa_anticache_notice', 'no');
        });
    });
}
jQuery(document).ready(function (jQuery) {
//===========Custom===============
    if (apsa_url_object.page == 'apsa-custom-ads' && apsa_url_object.form) {


        /*
         *====== Set title  to Custom Ad page ========== 
         * 
         */
        var selected = jQuery('.apsa-template-block[data-slug="' + apsa_inital_temp_data.temp_slug + '"]')

        jQuery('#apsa-single-form-title').val(apsa_inital_temp_data.form_title);
        jQuery('.apsa-timestamp b').html(apsa_inital_temp_data.form_publised);
        jQuery('#apsa-template-select').attr('data-slug', apsa_inital_temp_data.temp_slug).val(apsa_inital_temp_data.temp_slug);

        jQuery('.apsa-popup-container').find('.apsa-block-selected').removeClass('apsa-block-selected');
        selected.addClass('apsa-block-selected');


        jQuery('#apsa-template-select').attr('data-name', selected.attr('data-name'));
        jQuery('.apsa-template-screenshot-img').attr('src', selected.attr('data-img'));
        jQuery('.apsa-template-screenshot-img').css('visibility', 'visible');

        /*
         *=======  Get template builder required data and calling it ============
         */
        var saved_data = [];
        if (apsa_inital_temp_data.form_data != "") {
            saved_data = JSON.parse(apsa_inital_temp_data.form_data);
        }
        var template_info = apsa_get_template_info(apsa_inital_temp_data.temp_slug)





        var builder = jQuery('.apsa-module-body').template_builder({
            on_change: function (that) {

                if (that != 'init') {
                    var temp_data = builder.save_data();
                    apsa_show_template_preview(temp_data);
                    apsa_leave_page(false);
                    //adding * to update button
                    apsa_add_asterisk_to_button(jQuery('#apsa-save-form-options'));

                }





            },
            on_save: function (data) {

                jQuery('#apsa-module-data').val((JSON.stringify(data)))

            },
            on_complete: function () {

                var temp_data = builder.save_data();
                apsa_show_template_preview(temp_data); //  preview  for social buttons  



            }
        }
        );



        /*======== Implimenting demo content for template ==========*/

        if (saved_data.length == 0) {
            var demo_content = jQuery('.apsa-block-selected').find('.apsa-hidden-demo').val();
            builder.init_builder(template_info, JSON.parse(demo_content));

        } else {
            builder.init_builder(template_info, saved_data);
        }


        /*
         * =========== Single Custom ad page save button  ==============
         */
        jQuery(document).on("click", ".apsa-save-form-options", function () {

            apsa_leave_page(true);

            if (apsa_validation(jQuery('.apsa-template-wrapper')) == true) {
                /** Open items with error */
                jQuery('.apsa-template-wrapper .apsa-error-valid').each(function () {
                    jQuery(this).parentsUntil('.apsa-template-wrapper', '.apsa-slide-cont').each(function () {
                        jQuery(this).children('.apsa-sliding-block').attr('data-apsa-open', "true");
                        jQuery(this).children('.apsa-slide-opener').attr("data-apsa-open-slide", "true");
                    });
                });
                apsa_action_message("error", apsa_admin_labels["check_form_req_msg"]);
                return false;
            }
            builder.save_data();
            var new_form = jQuery('<form>', {
                'action': '',
                'method': 'POST',
            })
            jQuery('.apsa-submittable-input').each(function () {
                new_form.append(jQuery('<input>', {
                    'name': jQuery(this).attr('name'),
                    'value': jQuery(this).val(),
                    'type': 'hidden'
                }))
            })
            jQuery(this).attr('disabled', 'disabled');
            jQuery('body').append(new_form)
            new_form.trigger('submit');


        });

        /*
         *================= Template chooser popup functionality ==================
         */
        jQuery(document).on("click", '.apsa-template-block', function () {
            jQuery(this).parents('.apsa-admin-popup-content').find('.apsa-template-block').removeClass('apsa-block-selected');
            jQuery(this).addClass('apsa-block-selected');
        })
        /*================= Template chosser popup ok button ==================*/
        jQuery(document).on("click", '#apsa-template-choose-button', function (e) {
            e.preventDefault();
            jQuery(this).parent('.apsa-popup-footer').addClass('apsa-waiting-button');

            jQuery('body').removeClass('apsa-overflow-hidden');
            var selected = jQuery(this).parents('.apsa-popup-container').find('.apsa-block-selected');
            if (selected.length) {
                var template_slug = selected.attr('data-slug');
                var template_name = selected.attr('data-name');
                var template_screenshot = selected.attr('data-img');
                jQuery('.apsa-template-screenshot-img').attr('src', template_screenshot);
                jQuery('#apsa-template-select').val(template_slug);
                jQuery('#apsa-template-select').attr('data-slug', template_slug);
                jQuery('#apsa-template-select').attr('data-name', template_name);
                jQuery('.apsa-selected-template-name').html(template_name);
                jQuery('.apsa-admin-popup-overlay,.apsa-popup-container').hide(); //hiding popup
                jQuery('#apsa-social-type-select').val('apsa-share')

                var template_info = apsa_get_template_info(template_slug);

                var demo_content = selected.find('.apsa-hidden-demo').val();// by default share buttons demo
                setTimeout(function () {
                    builder.init_builder(template_info, JSON.parse(demo_content));
                    builder.change_template();


                }, 0)



            }


        });
        /*
         * ========== Template chooser button and popup close functionality ========= 
         */
        jQuery(document).on("click", ".apsa-select-template-button,.apsa-template-screenshot-img", function (e) {
            e.preventDefault();
            jQuery(this).parents('.apsa-sliding-block').find('.apsa-popup-footer').removeClass('apsa-waiting-button');
            jQuery('body').addClass('apsa-overflow-hidden');
            jQuery('.apsa-admin-popup-overlay,.apsa-popup-container').show();

        });

        jQuery(document).on("click", ".apsa-close-popup,.apsa-admin-popup-overlay", function (e) {
            jQuery('body').removeClass('apsa-overflow-hidden');
            jQuery('.apsa-admin-popup-overlay,.apsa-popup-container').hide();

        });

        /* ===== Preventing popup opening on page title input enter ===========*/

        jQuery('.apsa-single-form-title').keydown(function (e) {
            if (e.which == 13) {
                e.preventDefault();
            }
            apsa_add_asterisk_to_button(jQuery('#apsa-save-form-options'));
        })
        /*======================  Device Buttons Preview Functionality ===============================*/

        jQuery(document).on('click', '.apsa-prev-btn', function () {

            jQuery('.apsa-prev-btn').removeClass('apsa-active-btn');
            var prev_class = jQuery(this).attr('data-btn');
            prev_class = 'apsa-prv-' + prev_class + '-body';
            jQuery('.apsa-preview-body').attr('class', '').addClass('apsa-preview-body ' + prev_class + '');
            jQuery('.apsa-preview-body').attr('style', '');
            jQuery('#apsa-post-to-iframe').attr('style', '');
            jQuery(this).addClass('apsa-active-btn');


        })

        /*
         * ==============  Move to trash button ===============
         */
        jQuery(document).on('click', '.apsa-move-trash-btn', function (e) {
            e.preventDefault()
            var ids = jQuery(this).attr('data-id');
            var action = 'trash';
            action = 'single_' + action;
            var target_components = {ids: ids, };
            target_components['form'] = '';
            target_components['id'] = '';
            var url_components = apsa_change_url_components(target_components);
            var url = window.location.origin + window.location.pathname + "?" + url_components;
            var submit_obj = {};
            submit_obj[action] = ids;
            apsa_submit_on_fly(submit_obj, 'POST', url);

        });



        /*======== On window  resize change preview mode   =============*/


        var time_out;
        jQuery(window).resize(function () {
            /** Calculate Choose template popup container height */
            clearTimeout(time_out);
            time_out = setTimeout(function () {
                var device = jQuery('.apsa-active-btn').attr('data-btn')

                if (jQuery(window).width() < 1050) {
                    jQuery('.apsa-prv-' + device).removeClass('apsa-active-btn');
                    jQuery('.apsa-prv-mobile').addClass('apsa-active-btn');
                    jQuery('.apsa-preview-body').attr('style', '');
                    jQuery('#apsa-post-to-iframe').attr('style', '');
                    jQuery('.apsa-preview-body').attr('class', '').addClass('apsa-preview-body apsa-prv-mobile-body apsa-prev-resize')
                    jQuery('.apsa-preview-head').css('display', 'none');

                } else if (jQuery(window).width() > 1050) {

                    if (jQuery('.apsa-preview-body').hasClass('apsa-prev-resize')) {

                        jQuery('.apsa-prv-' + device).removeClass('apsa-active-btn');
                        jQuery('.apsa-prv-desktop').addClass('apsa-active-btn');
                        jQuery('.apsa-preview-body').removeClass('apsa-prv-mobile-body').addClass('apsa-prv-desktop-body');
                        jQuery('.apsa-preview-head').css('display', 'block')

                    }
                }

                var temp_data = builder.save_data();
                apsa_show_template_preview(temp_data);

                jQuery('#apsa-preview-container').css('visibility', 'visible')

                /*============ Adjusting height of template chooser ==================*/
                if (jQuery('.apsa-popup-container').css('display') == 'block' && jQuery(window).width() > 782) {
                    var window_height = jQuery(window).height();
                    var pop_top = parseInt(jQuery(".apsa-popup-container").css('top'));


                    var popup_cont_height = window_height - 122 - 2 * pop_top;

                    jQuery(".apsa-admin-popup-content").height(popup_cont_height);
                } else {
                    jQuery(".apsa-admin-popup-content").css('height', 'auto');
                }
            }, '300');

        })


        jQuery(window).trigger('resize');





    } else if (apsa_url_object.page == 'apsa-custom-ads' && !apsa_url_object.form) {




        /*
         * ============= Bulck check all forms in general form page =======
         */
        jQuery(document).on('change', '.apsa-bulk-check', function () {
            if (jQuery(this).is(":checked")) {
                jQuery(".apsa-check-column input[type='checkbox']").prop("checked", true);
            } else {
                jQuery(".apsa-check-column input[type='checkbox']").prop("checked", false);
            }

        })

        /*
         *============ Bulck action on forms page (for bulk trash) ============
         */
        jQuery(document).on('click', '.apsa-bulk-button', function () {

            var select_val = jQuery(this).parent('.apsa-bulkactions').find('select').val();
            if (select_val == -1)
                return false;

            var trash_ids = [];
            jQuery('input.apsa-form-checkbox:checked').each(function () {
                trash_ids.push(jQuery(this).attr('data-id'));
            })
            if (trash_ids.length == 0)
                return false

            var trash_ids_str = JSON.stringify(trash_ids);
            trash_ids = trash_ids.join(',');
            var target_components = {ids: trash_ids};
            //var query_obj = apsa_parse_query_string();
            if (apsa_url_object['paged'] && apsa_url_object['paged'] != 1) {
                if (jQuery('.apsa-forms-list-table tbody tr').length == 1 || jQuery('.apsa-form-checkbox:not(:checked)').length == 0)
                    target_components['paged'] = apsa_url_object['paged'] - 1;
            }
            target_components['clicked'] = '';
            var url_components = apsa_change_url_components(target_components);

            var url = window.location.origin + window.location.pathname + "?" + url_components
            var submit_obj = {};
            submit_obj[select_val] = trash_ids_str;

            apsa_submit_on_fly(submit_obj, 'POST', url);
        });
        /*
         * ==============  Single trash | restore  action on forms page ===============
         */
        jQuery(document).on('click', '.apsa-submitdelete,.apsa-restore-btn', function (e) {
            e.preventDefault()

            var ids = jQuery(this).attr('data-id');
            var action = jQuery(this).attr('data-action');
            action = 'single_' + action;
            var target_components = {ids: ids, };
            //var query_obj = apsa_parse_query_string();
            if (apsa_url_object['paged'] && apsa_url_object['paged'] != 1) {
                if (jQuery('.apsa-forms-list-table tbody tr').length == 1)
                    target_components['paged'] = apsa_url_object['paged'] - 1;
            }
            target_components['clicked'] = '';
            var url_components = apsa_change_url_components(target_components);
            var url = window.location.origin + window.location.pathname + "?" + url_components;
            var submit_obj = {};
            submit_obj[action] = ids;
            apsa_submit_on_fly(submit_obj, 'POST', url);

        });
        /**
         * ========= Undo button ============ 
         */

        jQuery(document).on('click', '.apsa-undo', function (e) {
            e.preventDefault()

            var ids = jQuery(this).attr('data-ids');
            var action = 'restore';
            action = (ids.split(',').length == 1) ? 'single_' + action : action;
            ids = (ids.split(',').length == 1) ? ids : JSON.stringify(ids.split(','));
            var url_components = apsa_change_url_components({ids: ids, clicked: '', status: 'search'});
            var url = window.location.origin + window.location.pathname + "?" + url_components;
            var submit_obj = {};
            submit_obj[action] = ids;
            apsa_submit_on_fly(submit_obj, 'POST', url)
        });


        /**
         *======================== Search input functionality ===============
         */
        jQuery('#apsa-form-search-input').keypress(function (e) {
            var code = e.keyCode || e.which;
            if (code === 13) {
                e.preventDefault();
                jQuery("#apsa-search-submit").trigger('click');
            }
        });

        jQuery(document).on('click', '#apsa-search-submit', function (e) {

            e.preventDefault()
            var select = jQuery('#apsa-filter-by-date').val();
            if (select != '0') {
                apsa_url_object['m'] = select;
            }

            var search_value = jQuery('#apsa-form-search-input').val();
            var search_name = jQuery('#apsa-form-search-input').attr('name');

            apsa_url_object[search_name] = search_value;
            apsa_url_object['paged'] = 1;
            delete apsa_url_object['clicked'];
            apsa_submit_on_fly(apsa_url_object, 'GET');

        })


        /**
         * ================= Table title sorting order ==============
         */

        jQuery(document).on('click', '.apsa-form-table-title', function (e) {
            var order = jQuery(this).parents('table.apsa-forms-list-table').hasClass('apsa-order-asc') ? 'desc' : 'asc';
            var search_value = jQuery('#apsa-form-search-input').val();
            var select = jQuery('#apsa-filter-by-date').val();
            if (select != '0') {
                apsa_url_object['m'] = select;
            }
            if (search_value !== '') {
                apsa_url_object['s'] = search_value;
            }
            apsa_url_object['orderby'] = 'title';
            apsa_url_object['order'] = order;

            delete apsa_url_object['clicked'];
            apsa_submit_on_fly(apsa_url_object, 'GET');
        });
        /**
         * ================= Table date sorting order ==============
         */

        jQuery(document).on('click', '.apsa-column-date', function (e) {
            var order = jQuery(this).parents('table.apsa-forms-list-table').hasClass('apsa-order-asc') ? 'desc' : 'asc';
            var search_value = jQuery('#apsa-form-search-input').val();
            var select = jQuery('#apsa-filter-by-date').val();
            if (select != '0') {
                apsa_url_object['m'] = select;
            }
            if (search_value != '') {
                apsa_url_object['s'] = search_value;
            }
            apsa_url_object['orderby'] = 'date';
            apsa_url_object['order'] = order;
            delete apsa_url_object['clicked'];
            apsa_submit_on_fly(apsa_url_object, 'GET');
        });
        /**
         * =================  Filter by date  ===================
         */

        jQuery(document).on('click', '#apsa-date-filter', function (e) {
            var select = jQuery('#apsa-filter-by-date').val();
            var search_value = jQuery('#apsa-form-search-input').val();
            if (search_value !== '') {
                apsa_url_object['s'] = search_value;
            }
            apsa_url_object['m'] = select;
            delete apsa_url_object['clicked'];
            apsa_submit_on_fly(apsa_url_object, 'GET');

        });
        /**
         * =================  Set current page number data attribute for hold page number ===================
         */

        jQuery('.page-numbers.current').each(function () {
            var page_number = jQuery(this).text();
            jQuery(this).append('<input type="text" class="apsa-find-page" value="' + page_number + '">');
        });

        /**
         * =================  Paging by input page number ===================
         */
        jQuery('.page-numbers.current .apsa-find-page').keypress(function (e) {
            if (e.which == 13) {
                e.preventDefault();

                var page_num = parseInt(jQuery(this).val());

                page_num = (isNaN(page_num)) ? '1' : Math.abs(page_num);

                var max_num = jQuery(this).closest('.apsa-pagination').find('.page-numbers').not('.next').last().text();
                max_num = parseInt(max_num);

                page_num = ((page_num > max_num)) ? max_num : page_num;
                apsa_url_object['paged'] = page_num;


                apsa_submit_on_fly(apsa_url_object, 'GET');

            }
        });


        /**
         * ============ Slide block open and close =============
         */
        jQuery(document).on('click', '.apsa-slide-opener', function () {
            if (jQuery(this).siblings('.apsa-sliding-block').attr('data-apsa-open') == 'false') {
                jQuery(this).siblings('.apsa-sliding-block').attr('data-apsa-open', "true");
            } else {
                jQuery(this).siblings('.apsa-sliding-block').attr('data-apsa-open', "false");
            }
            jQuery(this).attr("data-apsa-open-slide", jQuery(this).attr("data-apsa-open-slide") == "false" ? "true" : "false");
        });
        jQuery(document).on('click', '.apsa-toggle-row', function () {

            if (jQuery(this).parents('.apsa-slide-cont').find('.apsa-sliding-block').attr('data-apsa-open') == 'false') {
                jQuery(this).parents('.apsa-slide-cont').find('.apsa-sliding-block').attr('data-apsa-open', "true");
            } else {
                jQuery(this).parents('.apsa-slide-cont').find('.apsa-sliding-block').attr('data-apsa-open', "false");
            }
            jQuery(this).attr("data-apsa-open-slide", jQuery(this).attr("data-apsa-open-slide") == "false" ? "true" : "false");

        });
    }
});
/** 
 * * ======== Geting object of url location query parameters ========
 * 
 * @returns obj
 */
function apsa_parse_query_string() {

    var str = window.location.search;
    var objURL = {};

    str.replace(
            new RegExp("([^?=&]+)(=([^&]*))?", "g"),
            function ($0, $1, $2, $3) {
                objURL[ $1 ] = $3;
            }
    );
    return objURL;
}


/**
 *======== Add asterisk to save button============
 * 
 * @param {obj} button
 * @returns {undefined}
 */
function apsa_add_asterisk_to_button(button) {

    var button_text = button.val();
    if (button_text.indexOf('*') == -1) {
        button.val(button_text + " *")
    }
}

/**
 * *======= Change Url Components =========
 * 
 * @param {str} target_key
 * @param {str} target_value
 * @returns {String}
 */
function apsa_change_url_components(target_componets) {
    var query_arr = [];

    var query_obj = apsa_parse_query_string();



    jQuery.each(target_componets, function (key, value) {

        if (value == '') {
            delete query_obj[key];
            return true;
        }

        query_obj[key] = value;
    });



    jQuery.each(query_obj, function (key, value) {

        query_arr.push(encodeURIComponent(key) + "=" + encodeURIComponent(value))
    })
    var query_str = query_arr.join("&")
    return query_str;
}
/*===============================  Functions for Template Preview  =================================== */
/**
 * * ========  Preview  Template ========
 * 
 * @returns {undefined}
 */
function apsa_show_template_preview(temp_data) {
    var temp_changed = jQuery('.apsa-template-builder').attr('data-apsa-changed');
    temp_changed++;
    jQuery('.apsa-template-builder').attr('data-apsa-changed', temp_changed);

    var temp_slug = jQuery('#apsa-template-select').val();
    var iframe_height = 522; //jQuery('#apsa-post-to-iframe').height();
    var apsa_form_data = {
        iframe_height: iframe_height,
        temp_data: JSON.stringify(temp_data),
        temp_slug: temp_slug,
    };

    apsa_post_to_preview_iframe(apsa_form_data)

}
/**
 * *==== Submitting form on fly on the  iframe ======
 * 
 * @param {type} data
 * @returns {undefined}
 */
function apsa_post_to_preview_iframe(data) {

    jQuery('body').append('<form action="' + apsa_admin_url + 'admin.php?page=apsa-ad-preview" method="post" target="apsa_preview_iframe_name" id="apsa_post_to_frame"></form>');
    jQuery.each(data, function (name, value) {

        jQuery('#apsa_post_to_frame').append(jQuery('<input>', {
            'name': name,
            'value': value,
            'type': 'hidden'
        }));
    });

    jQuery('#apsa_post_to_frame').submit().remove();

}

/**
 * *========== Getting template info with it's slug =============== 
 * 
 * @param {str} slug
 * @returns {Array}
 */
function apsa_get_template_info(slug) {
    var elements = {};
    jQuery.each(apsa_all_templates_data, function (index, value) {
        if (value.slug == slug) {
            jQuery.each(value['elements'], function (ind, val) {
                delete apsa_all_templates_data[index]['elements'][ind]['html']
            })

            elements = value['elements'];
            return;
        }

    });
    return elements;
}

/**
 **======= Submiting form on fly ===========
 * 
 * @param {obj} obj
 * @param {str} method
 * @param {str} url
 * @returns {undefined}
 */
function apsa_submit_on_fly(obj, method, url) {

    url = url || "";
    var new_form = jQuery('<form>', {
        'action': url,
        'method': method,
    })

    jQuery.each(obj, function (key, value) {
        new_form.append(jQuery('<input>', {
            'name': key,
            'value': value,
            'type': 'hidden'
        }));
    });

    jQuery('body').append(new_form)
    new_form.trigger('submit');
}    