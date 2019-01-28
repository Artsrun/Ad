/**
 * broken link detector on keyup
 */
function apsa_broken_link_detector() {
    var apsa_timer;
    jQuery(document).on('keyup', '.apsa-broken-link', function () {
        clearTimeout(apsa_timer);
        var link = jQuery(this).val().trim();
        var that = jQuery(this);
        if (link != '') {
            apsa_timer = setTimeout(function () {
                jQuery.ajax({
                    type: "POST",
                    url: apsa_ajax_url,
                    dataType: "json",
                    cache: false,
                    data: {
                        action: "apsa_ajax_check_broken_link",
                        link: encodeURI(jQuery.trim(link))
                    },
                    success: function (res) {
                        if (link != that.val().trim())
                            return false;
                        if (res.status == 200) {
                            that.removeClass('apsa-error-input');
                            that.removeClass('apsa-overdue-element');
                            that.closest('.apsa-form-item').find('.apsa-input-message').text('').removeClass('apsa-warning-message');
                        } else {
                            that.addClass('apsa-overdue-element');
                            that.closest('.apsa-form-item').find('.apsa-input-message').text(apsa_admin_labels['broken_link_message']).addClass('apsa-warning-message');
                        }

                    },
                });
            }, 500);
        } else {
            that.removeClass('apsa-error-input');
            that.removeClass('apsa-overdue-element');
            that.closest('.apsa-form-item').find('.apsa-input-message').text('').removeClass('apsa-warning-message');
        }
    });
}

/**
 * Refresh elements priority numbers, for determine display order
 * 
 * @param object campaign_elements_block
 * @returns {undefined}
 */
function apsa_refresh_elements_priority(campaign_elements_block) {
    var counter = 0;
    campaign_elements_block.find(".apsa-element-priority").each(function () {

        jQuery(this).val(counter);
        counter++;
    });
}

/**
 * Makes elements lists sortable
 * 
 * @returns {undefined}
 */
function apsa_make_elements_sortable() {
    jQuery(".apsa-sortable").sortable({
        axis: "y",
        items: "> .apsa-element-block",
        handle: '.apsa-sort-mover',
        stop: function () {
            apsa_refresh_elements_priority(jQuery(this));
        }
    });
}

/**
 * Makes elements highlightable
 */
function apsa_make_elements_highlight() {
    jQuery(document).on('mouseup touchend', '.apsa-element-block', function () {
        jQuery('.apsa-element-block').removeClass('apsa-element-highlight');
        jQuery(this).addClass('apsa-element-highlight');
    });
    jQuery(document).on('mouseup touchend', function (e) {
        if (jQuery(e.target).parents('.apsa-element-block').length == 0) {
            jQuery('.apsa-element-block').removeClass('apsa-element-highlight');
        }
    });
    jQuery(document).on('keyup', function (e) {
        if (e.which == 9) { //TAB key
            var parentBlock = jQuery(':focus').parents('.apsa-element-block');
            if (!parentBlock.hasClass('apsa-element-highlight')) {
                jQuery('.apsa-element-block').removeClass('apsa-element-highlight');
                parentBlock.addClass('apsa-element-highlight');
            }
        }
    });
}

/**
 * Make element chart from statistics
 */
function apsa_make_element_chart(container, statistics, title) {
    /** Create statistics chart chart */
    jQuery(container).highcharts({
        exporting: {
            enabled: false
        },
        chart: {
            type: 'areaspline'
        },
        title: {
            text: apsa_admin_labels['element_stat_title']
        },
        subtitle: {
            text: title
        },
        legend: {
            layout: 'vertical',
            align: 'left',
            verticalAlign: 'top',
            x: 150,
            y: 100,
            floating: true,
            borderWidth: 1,
            backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
        },
        xAxis: {
            allowDecimals: false,
            categories: statistics["days"],
        },
        yAxis: {
            title: {
                text: apsa_admin_labels["stat_count"]
            }
        },
        tooltip: {
            shared: true,
            valueSuffix: ''
        },
        credits: {
            enabled: false
        },
        colors: ['#27ae60', '#2980b9'],
        plotOptions: {
            areaspline: {
                fillOpacity: 0.5
            }
        },
        series: [{
                name: apsa_first_to_upper_case(apsa_admin_labels['stat_event']),
                data: statistics[apsa_fr_event_name]
            }, {
                name: apsa_first_to_upper_case(apsa_admin_labels['stat_event_child']),
                data: statistics[apsa_plugin_data['event_name']]
            }]
    });
}

/**
 * Make campaign chart from statistics
 */
function apsa_make_camp_chart(container, statistics, title) {
    /** Create statistics chart */
    container.highcharts({
        exporting: {
            enabled: false
        },
        credits: {
            enabled: false
        },
        colors: ['#1abc9c', '#0073aa'],
        chart: {
            type: 'column'
        },
        title: {
            text: apsa_admin_labels['camp_stat_title']
        },
        subtitle: {
            text: title
        },
        xAxis: {
            categories: statistics["elements"],
            crosshair: true
        },
        yAxis: {
            allowDecimals: false,
            min: 0,
            title: {
                text: apsa_admin_labels["stat_count"]
            }
        },
        tooltip: {
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            }
        },
        series: [{
                name: apsa_first_to_upper_case(apsa_admin_labels['stat_event']),
                data: statistics[apsa_fr_event_name]

            }, {
                name: apsa_first_to_upper_case(apsa_admin_labels['stat_event_child']),
                data: statistics[apsa_plugin_data['event_name']]

            }]
    });
}

/**
 * Create statistics chart
 */
function apsa_create_element_chart(container, from, to, export_chart) {
    container.addClass("apsa-stat-loading");
    if (from === undefined) {
        from = 0;
    }

    if (to === undefined) {
        to = 0;
    }

    var element_id = container.attr("data-apsa-element-id");
    var element_title = container.attr("data-apsa-element-title");
    /** Ajax request for get element statistics */
    jQuery.ajax({
        type: "POST",
        url: apsa_ajax_url,
        dataType: "json",
        async: apsa_is_safari() ? false : true,
        data: {
            action: "apsa_ajax_get_element_statistics",
            element_id: element_id,
            from: from,
            to: to
        },
        success: function (statistics) {
            /** Create statistics chart chart */
            apsa_make_element_chart(container, statistics, element_title);
            if (export_chart === true) {
                var doc = new jsPDF("l", "mm", "a4");
                var range_date = "";
                var from_date = container.closest('.apsa-stat-cont').find(".apsa-stat-from").val();
                var to_date = container.closest('.apsa-stat-cont').find(".apsa-stat-to").val();
                if (from_date != '' && to_date != '') {
                    range_date = '<span style="font-weight:bold;font-size:12px;">' + apsa_admin_labels["stat_from_place"] + ' </span><span style="font-size:12px;">' + from_date + '</span><span style="font-weight:bold;font-size:12px;"> ' + apsa_admin_labels["stat_to_place"] + ' </span> <span style="font-size:12px;">' + to_date + '</span>';
                } else if (from_date != '') {
                    range_date = '<span style="font-weight:bold;font-size:12px;">' + apsa_admin_labels["stat_from_place"] + ' </span><span style="font-size:12px;">' + from_date + '</span>';
                } else if (to_date != '') {
                    range_date = '<span style="font-weight:bold;font-size:12px;">' + apsa_admin_labels["stat_to_place"] + ' </span><span style="font-size:12px;">' + to_date + '</span>';
                }

                //loop through each chart
                var imageData = container.highcharts().createCanvas(range_date);
                doc.addImage(imageData, 'JPEG', 15, 15, 257, 170);
                //save with name
                doc.save(apsa_plugin_data["plugin_data"]["name"].toLowerCase() + '-stats.pdf');
            }
        },
        error: function () {
            apsa_action_message("error", apsa_admin_labels["cant_get_stat_msg"]);
        }
    });
}

function apsa_create_camp_chart(container, from, to, export_chart) {
    container.addClass("apsa-stat-loading");
    if (from === undefined) {
        from = 0;
    }

    if (to === undefined) {
        to = 0;
    }

    var camp_id = container.attr("data-apsa-camp-id");
    var camp_title = container.attr("data-apsa-camp-title");
    /** Ajax request for get campaign statistics */
    jQuery.ajax({
        type: "POST",
        url: apsa_ajax_url,
        dataType: "json",
        async: apsa_is_safari() ? false : true,
        data: {
            action: "apsa_ajax_get_camp_statistics",
            camp_id: camp_id,
            from: from,
            to: to
        },
        success: function (statistics) {
            apsa_make_camp_chart(container, statistics, camp_title);
            if (export_chart === true) {
                var doc = new jsPDF("l", "mm", "a4");
                var range_date = "";
                var from_date = container.closest('.apsa-stat-cont').find(".apsa-stat-from").val();
                var to_date = container.closest('.apsa-stat-cont').find(".apsa-stat-to").val();
                if (from_date != '' && to_date != '') {
                    range_date = '<span style="font-weight:bold;font-size:12px;">' + apsa_admin_labels["stat_from_place"] + ' </span><span style="font-size:12px;">' + from_date + '</span><span style="font-weight:bold;font-size:12px;"> ' + apsa_admin_labels["stat_to_place"] + ' </span> <span style="font-size:12px;">' + to_date + '</span>';
                } else if (from_date != '') {
                    range_date = '<span style="font-weight:bold;font-size:12px;">' + apsa_admin_labels["stat_from_place"] + ' </span><span style="font-size:12px;">' + from_date + '</span>';
                } else if (to_date != '') {
                    range_date = '<span style="font-weight:bold;font-size:12px;">' + apsa_admin_labels["stat_to_place"] + ' </span><span style="font-size:12px;">' + to_date + '</span>';
                }

                //loop through each chart
                var imageData = container.highcharts().createCanvas(range_date);
                doc.addImage(imageData, 'JPEG', 15, 15, 257, 170);
                //save with name
                doc.save(apsa_plugin_data["plugin_data"]["name"].toLowerCase() + '-stats.pdf');
            }
        },
        error: function () {
            apsa_action_message("error", apsa_admin_labels["cant_get_stat_msg"]);
        }
    });
}

/**
 * Jquery ui element statistics range 
 * 
 * @returns {undefined}
 */
function apsa_charts_datepicker() {
    jQuery(".apsa-stat-filter").each(function () {
        var range_cont = jQuery(this);
        range_cont.find(".apsa-stat-from").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: apsa_date_format,
            monthNames: apsa_month_names,
            monthNamesShort: apsa_month_short_names,
            dayNamesShort: apsa_day_short_names,
            dayNamesMin: apsa_day_short_names,
            dayNames: apsa_day_names,
            firstDay: apsa_start_of_week,
            onClose: function (selected_date) {
                range_cont.find("apsa-stat-to").datepicker("option", "minDate", selected_date);
            },
            onSelect: function () {
                jQuery(this).closest(".apsa-campaign-block").attr("data-apsa-camp-changed", "1");
                jQuery(this).trigger('change');
            }
        });
        range_cont.find(".apsa-stat-to").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: apsa_date_format,
            monthNames: apsa_month_names,
            monthNamesShort: apsa_month_short_names,
            dayNamesShort: apsa_day_short_names,
            dayNamesMin: apsa_day_short_names,
            dayNames: apsa_day_names,
            firstDay: apsa_start_of_week,
            onClose: function (selected_date) {
                range_cont.find(".apsa-stat-from").datepicker("option", "maxDate", selected_date);
            },
            onSelect: function () {
                jQuery(this).closest(".apsa-campaign-block").attr("data-apsa-camp-changed", "1");
                jQuery(this).trigger('change');
            }
        });
    });
}

/**
 * Make tags input for campaign and element specifing
 */
function apsa_make_bloodhound() {
    var url = apsa_ajax_url + ((apsa_ajax_url.indexOf('?') != -1) ? '&' : '?') + 'action=apsa_ajax_autocomplete&query=%QUERY';
    var autocomplete = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('text'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: url,
            wildcard: '%QUERY'
        }
    });
    autocomplete.initialize();
    /**
     * Categorizing tags
     */
    elt = jQuery('.apsa-tags > > .typeahead-auto');
    elt.tagsinput({
        tagClass: function (item) {
            switch (item.type) {
                case 'category'   :
                    return 'label label-primary';
                case 'post_tag':
                    return 'label label-warning';
                case 'post':
                    return 'label label-danger';
                case 'page':
                    return 'label label-success';
                case 'language':
                    return 'label label-info';
                case 'device':
                    return 'label label-default';
            }
        },
        itemValue: 'value',
        itemText: 'text',
        typeaheadjs: [
            {
                hint: true,
                highlight: true,
                minLength: 2
            },
            {
                name: 'autocomplete',
                displayKey: 'text',
                limit: 200,
                source: autocomplete.ttAdapter(),
                templates: {
                    empty: [apsa_admin_labels["no_result"]],
                    suggestion: function (data) {
                        var apsa_value = data.value.split("%").slice(1)[0];
                        return '<p>' + ((apsa_value == 'apsa_all_pages' || apsa_value == 'apsa_all_posts') ? apsa_admin_labels[apsa_value]['loc'] : data.text) + ' - <strong>' + apsa_first_to_upper_case(apsa_admin_labels["tagsinput_" + data.type]) + '</strong></p>';
                    }
                }
            }
        ]
    });
// HACK: overrule hardcoded display inline-block of typeahead.js
    jQuery(".twitter-typeahead").css('display', 'inline');
}

/**
 * Callback after choosing element files
 * @param {json} attachments
 * @param {json} upload_buttton
 * @returns {undefined}
 */
function apsa_element_file_callback(attachments, upload_buttton) {
    var attachment = attachments[0];
    var file_url = attachment.url;
    jQuery(upload_buttton).closest('.apsa-form-item').find('.apsa-hold-element-content').val(file_url).trigger('change');
}

/** Document ready actions */
jQuery(document).ready(function ($) {

    camp_creation_process = false;
    el_creation_process = false;

    var ie_version = apsa_ie_version();
    (function (API) {
        API.myText = function (txt, options, x, y) {
            options = options || {};
            if (options.align == "center") {
                // Get current font size
                var fontSize = this.internal.getFontSize();
                // Get page width
                var pageWidth = this.internal.pageSize.width;
                // Get the actual text's width
                txtWidth = this.getStringUnitWidth(txt) * fontSize / this.internal.scaleFactor;
                // Calculate text's x coordinate
                x = (pageWidth - txtWidth) / 2;
            }

            // Draw text at x,y
            this.text(txt, x, y);
        }
    })(jsPDF.API);
    apsa_make_bloodhound();
    var code_area = document.getElementById("apsa-code-area");
    if (code_area) {
        var myCodeMirror = CodeMirror.fromTextArea(code_area, {
            lineNumbers: true,
            mode: "htmlmixed",
        });
    }

// Make sortable all element blocks
    apsa_make_elements_sortable();
    // Make elements highlightable
    apsa_make_elements_highlight();
    // make file uploaders for images
    apsa_make_file_uploader('[data-apsa-file-type="image"].apsa-upload-file', 'apsa_element_file_callback', false, 'image');
    // make file uploaders for swf
    apsa_make_file_uploader('[data-apsa-file-type="application"].apsa-upload-file', 'apsa_element_file_callback', false, 'application', 'x-shockwave-flash');
    /**
     * Open popup for add campaign
     */
    $(document).on('click', '.apsa-add-campaign', function () {

        // check if any campaign in creation process not open popup
        if (camp_creation_process == true) {
            return false;
        }

        var camp_types = {};
        if (apsa_plugin_data['campaign_types']['background'] == 'true')
            camp_types['background'] = apsa_admin_labels["camp_type_bg"];
        if (apsa_plugin_data['campaign_types']['popup'] == 'true')
            camp_types['popup'] = apsa_admin_labels["camp_type_popup"];
        if (apsa_plugin_data['campaign_types']['embed'] == 'true')
            camp_types['embed'] = apsa_admin_labels["camp_type_embed"];
        if (apsa_plugin_data['campaign_types']['sticky'] == 'true')
            camp_types['sticky'] = apsa_admin_labels["camp_type_sticky"];
        if (Object.keys(camp_types).length === 1) {
            apsa_add_new_campaign(Object.keys(camp_types)[0]);
            return true;
        }

        $("#apsa-campaign-types").empty();
        $.each(camp_types, function (camp_type, camp_name) {
            if ((camp_type == "popup" || camp_type == "background") && $('[data-apsa-camp-type="' + camp_type + '"]').length >= 1) {
                return true;
            }

            $("#apsa-campaign-types").append('<div class="apsa-campaign-type-cont">\n\
                        <h4>' + camp_name + '</h4>\n\
                        <div class="apsa-campaign-type apsa-selection-block" data-apsa-campaign-type="' + camp_type + '"><div></div></div>\n\
                    </div>');
        });
        $('#apsa-managing-overlay').fadeIn(150);
        $('#apsa-add-campaign-popup').attr('data-apsa-open', "true");
        $('body').addClass('modal-open');
    });
    /**
     * Select type
     */
    $(document).on("click", ".apsa-selection-block", function () {

        $(".apsa-selection-block").removeClass("apsa-block-selected");
        $(this).addClass("apsa-block-selected");
    });
    /**
     * Unselect type
     */
    $(document).on("click", ".apsa-block-selected", function () {
        $(this).removeClass("apsa-block-selected");
    });
    /**
     * Add campaign
     */
    function apsa_add_new_campaign(campaign_type) {
        $.ajax({
            type: "POST",
            url: apsa_ajax_url,
            dataType: "json",
            data: {
                action: "apsa_ajax_new_campaign",
                type: campaign_type
            },
            success: function (response) {
                if (response["success"] != 1) {
                    // Error message
                    apsa_action_message("error", apsa_admin_labels["cant_add_camp_msg"]);
                    return false;
                }

                /** Add new campaign to list of campaigns */
                var campaign_id = response['campaign_id'];
                var campaign_options = '';
                var overlay_patterns = '';
                var child_campaign_options = '';
                $.each(apsa_effects['patterns'], function (key, value) {
                    overlay_patterns += '<option value="' + key + '">' + key + '</option>';
                });
                if (typeof apsa_child_create_campaign_options == 'function') {
                    child_campaign_options = apsa_child_create_campaign_options(campaign_type, apsa_admin_labels, overlay_patterns);
                }
                /** Take appropriate options for choosen type */
                if (campaign_type == "background") {
                    campaign_options = '<li class="apsa-form-item">\n\
                                                <span>' + apsa_admin_labels["change_interval"] + '</span>&nbsp;<span class="apsa-no-cap">(' + apsa_admin_labels["second"] + ')</span>\n\
                                                <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_first_to_upper_case(apsa_admin_labels["detailed_description"])) + '" data-apsa-message="' + apsa_admin_labels["change_interval_desc"] + '"></span>\n\
                                                <div class="apsa-input-block">\n\
                                                <input type="text" name="change_interval" class="apsa-positive-int" value="" />\n\
                                                </div>\n\
                                                <span class="apsa-input-message" data-apsa-default-message="' + apsa_admin_labels["default"] + ' ' + apsa_admin_labels["change_interval_def"] + '">' + apsa_admin_labels["default"] + ' ' + apsa_admin_labels["change_interval_def"] + '</span>\n\
                                        </li>\n\
                                        <li class="apsa-form-item">\n\
                                            <span>' + apsa_admin_labels["bg_selector"] + '</span>\n\
                                            <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message="' + apsa_admin_labels["bg_selector_desc"] + '"></span>\n\
                                            <div class="apsa-input-block">\n\
                                            <input type="text" name="background_selector" value="" />\n\
                                            </div>\n\
                                            <span class="apsa-input-message">' + apsa_admin_labels["default"] + ' ' + 'body</span>\n\
                                        </li>';
                } else if (campaign_type == "popup") {
                    var popup_effects = '';
                    $.each(apsa_effects['popup'], function (key, value) {
                        popup_effects += '<option value="' + key + '">' + key + '</option>';
                    });
                    campaign_options = '<li class="apsa-form-item">\n\
                                                <span>' + apsa_admin_labels["change_interval"] + ' </span>&nbsp;<span class="apsa-no-cap">(' + apsa_admin_labels["second"] + ')</span>\n\
                                                <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message="' + apsa_admin_labels["change_interval_desc"] + '"></span>\n\
                                                <div class="apsa-input-block">\n\
                                                <input type="text" name="change_interval" class="apsa-positive-int" value="" />\n\
                                                </div>\n\
                                                <span class="apsa-input-message" data-apsa-default-message="' + apsa_admin_labels["default"] + ' ' + apsa_admin_labels["change_interval_def"] + '">' + apsa_admin_labels["default"] + ' ' + apsa_admin_labels["change_interval_def"] + '</span>\n\
                                        </li>\n\
                                        <li class="apsa-form-item">\n\
                                            <span>' + apsa_admin_labels["show_interval"] + '</span>&nbsp;<span class="apsa-no-cap">(' + apsa_admin_labels["second"] + ')</span>\n\
                                            <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message="' + apsa_admin_labels["show_interval_desc"] + '"></span>\n\
                                            <div class="apsa-input-block">\n\
                                            <input type="text" class="apsa-positive-int" name="view_interval" value="" />\n\
                                            </div>\n\
                                            <span class="apsa-input-message" data-apsa-default-message="' + apsa_admin_labels["default"] + ' ' + apsa_admin_labels["show_interval_def"] + '">' + apsa_admin_labels["default"] + ' ' + apsa_admin_labels["show_interval_def"] + '</span>\n\
                                        </li>\n\
                                        <li class="apsa-form-item">\n\
                                            <span>' + apsa_admin_labels["popup_direction"] + '</span>\n\
                                            <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message="' + apsa_admin_labels["popup_direction_desc"] + '"></span>\n\
                                            <div class="apsa-input-block">\n\
                                            <select name="popup_animation">' + popup_effects + '</select>\n\
                                            </div>\n\
                                            <span class="apsa-input-message"></span>\n\
                                        </li>\n\
                                        <li class="apsa-form-item">\n\
                                            <span>' + apsa_admin_labels["width"] + ' </span>&nbsp;<span class="apsa-no-cap">(px/%)</span>\n\
                                            <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message="' + apsa_admin_labels["width_desc"] + '"></span>\n\
                                            <div class="apsa-input-block">\n\
                                            <input type="text" name="width" class="apsa-size" />\n\
                                            </div>\n\
                                            <span class="apsa-input-message" data-apsa-default-message="' + apsa_admin_labels["default"] + ' 600px">' + apsa_admin_labels["default"] + ' 600px</span>\n\
                                        </li>\n\
                                        <li class="apsa-form-item">\n\
                                            <span>' + apsa_admin_labels["height"] + '</span>&nbsp;<span class="apsa-no-cap">(px/%)</span>\n\
                                            <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message="' + apsa_admin_labels["height_desc"] + '"></span>\n\
                                            <div class="apsa-input-block">\n\
                                            <input type="text" name="height" class="apsa-size" />\n\
                                            </div>\n\
                                            <span class="apsa-input-message" data-apsa-default-message="' + apsa_admin_labels["default"] + ' 500px">' + apsa_admin_labels["default"] + ' 500px</span>\n\
                                        </li>\n\
                                        <li class="apsa-form-item">\n\
                                            <span>' + apsa_admin_labels["popup_show_delay"] + ' </span>&nbsp;<span class="apsa-no-cap">(' + apsa_admin_labels["second"] + ')</span>\n\
                                            <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message="' + apsa_admin_labels["popup_show_delay_desc"] + '"></span>\n\
                                            <div class="apsa-input-block">\n\
                                            <input type="text" class="apsa-positive-int" name="view_after" />\n\
                                            </div>\n\
                                            <span class="apsa-input-message" data-apsa-default-message="' + apsa_admin_labels["default"] + ' 0">' + apsa_admin_labels["default"] + ' 0</span>\n\
                                        </li>\n\
                                        <li class="apsa-form-item">\n\
                                            <span>' + apsa_admin_labels["close_button_delay"] + ' </span>&nbsp;<span class="apsa-no-cap">(' + apsa_admin_labels["second"] + ')</span>\n\
                                            <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message="' + apsa_admin_labels["close_button_delay_desc"] + '"></span>\n\
                                            <div class="apsa-input-block">\n\
                                            <input type="text" class="apsa-positive-int" name="show_close_after" />\n\
                                            </div>\n\
                                            <span class="apsa-input-message" data-apsa-default-message="' + apsa_admin_labels["default"] + ' 0">' + apsa_admin_labels["default"] + ' 0</span>\n\
                                        </li>\n\
                                        <li class="apsa-form-item">\n\
                                            <span>' + apsa_admin_labels["popup_autoclose"] + ' </span>&nbsp;<span class="apsa-no-cap">(' + apsa_admin_labels["second"] + ')</span>\n\
                                            <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message="' + apsa_admin_labels["popup_autoclose_desc"] + '"></span>\n\
                                            <div class="apsa-input-block">\n\
                                            <input type="text" class="apsa-positive-int" name="hide_element_after" />\n\
                                            </div>\n\
                                            <span class="apsa-input-message" data-apsa-default-message="' + apsa_admin_labels["default"] + ' ' + apsa_admin_labels["popup_autoclose_def"] + '">' + apsa_admin_labels["default"] + ' ' + apsa_admin_labels["popup_autoclose_def"] + '</span>\n\
                                        </li>\n\
                                        <li class="apsa-form-item">\n\
                                            <span>' + apsa_admin_labels["overlay_pattern"] + '</span>\n\
                                            <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message="' + apsa_admin_labels["overlay_pattern_desc"] + '"></span>\n\
                                            <div class="apsa-input-block">\n\
                                            <select name="overlay_pattern"><option value="none">None</option><option value="gray" selected="selected">Gray</option>' + overlay_patterns + '</select>\n\
                                            </div>\n\
                                            <span class="apsa-input-message"></span>\n\
                                        </li>\n\
                                        <li class="apsa-form-item">\n\
                                            <div class="apsa-input-block">\n\
                                            <span>' + apsa_admin_labels["put_in_frame"] + '</span>\n\
                                            <input type="checkbox" checked="checked" />\n\
                                            <input type="hidden" class="apsa-hold-checkbox" name="put_in_frame" value="on" />\n\
                                            <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message="' + apsa_admin_labels["put_in_frame_desc"] + '"></span>\n\
                                            </div>\n\
                                        </li>\n\
                                        <li class="apsa-form-item">\n\
                                            <span>' + apsa_admin_labels["popup_color"] + '</span>\n\
                                            <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message="' + apsa_admin_labels["popup_color_desc"] + '"></span>\n\
                                            <div class="apsa-input-block">\n\
                                            <input type="text" name="frame_color" class="apsa-extra-small-input apsa-colorpicker" data-default-color="#ffffff" value="#ffffff">\n\
                                            </div>\n\
                                            <span class="apsa-input-message">' + apsa_admin_labels["default"] + ' #ffffff</span>\n\
                                        </li>';
                } else if (campaign_type == "embed") {
                    var embed_effects = '';
                    $.each(apsa_effects['embed'], function (key, value) {
                        embed_effects += '<option value="' + key + '">' + key + '</option>';
                    });
                    campaign_options = '<li class="apsa-form-item">\n\
                                                <span>' + apsa_admin_labels["change_interval"] + ' </span>&nbsp;<span class="apsa-no-cap">(' + apsa_admin_labels["second"] + ')</span>\n\
                                                <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message="' + apsa_admin_labels["change_interval_desc"] + '"></span>\n\
                                                <div class="apsa-input-block">\n\
                                                <input type="text" name="change_interval" class="apsa-positive-int" value="" />\n\
                                                </div>\n\
                                                <span class="apsa-input-message" data-apsa-default-message="' + apsa_admin_labels["default"] + ' ' + apsa_admin_labels["change_interval_def"] + '">' + apsa_admin_labels["default"] + ' ' + apsa_admin_labels["change_interval_def"] + '</span>\n\
                                        </li>\n\
                                        <li class="apsa-form-item">\n\
                                            <span>' + apsa_admin_labels["embed_direction"] + '</span>\n\
                                            <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message="' + apsa_admin_labels["embed_direction_desc"] + '"></span>\n\
                                            <div class="apsa-input-block">\n\
                                            <select name="embed_direction"><option value="none">None</option>' + embed_effects + '</select>\n\
                                            </div>\n\
                                            <span class="apsa-input-message"></span>\n\
                                        </li>\n\
                                        <li class="apsa-form-item">\n\
                                            <span>' + apsa_admin_labels["width"] + ' </span>&nbsp;<span class="apsa-no-cap">(px/%)</span>\n\
                                            <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message="' + apsa_admin_labels["width_desc"] + '"></span>\n\
                                            <div class="apsa-input-block">\n\
                                            <input type="text" name="width" class="apsa-size" />\n\
                                            </div>\n\
                                            <span class="apsa-input-message" data-apsa-default-message="' + apsa_admin_labels["default"] + ' 100%">' + apsa_admin_labels["default"] + ' 100%</span>\n\
                                        </li>\n\
                                        <li class="apsa-form-item">\n\
                                            <span>' + apsa_admin_labels["height"] + ' </span>&nbsp;<span class="apsa-no-cap">(px)</span>\n\
                                            <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message="' + apsa_admin_labels["height_desc_px"] + '"></span>\n\
                                            <div class="apsa-input-block">\n\
                                            <input type="text" name="height" class="apsa-positive-int" />\n\
                                            </div>\n\
                                            <span class="apsa-input-message" data-apsa-default-message="' + apsa_admin_labels["default"] + ' 100">' + apsa_admin_labels["default"] + ' 100</span>\n\
                                        </li>';
                } else if (campaign_type == "sticky") {
                    var sticky_effects = '';
                    var sticky_positions = '';
                    $.each(apsa_effects['sticky'], function (key, value) {
                        sticky_effects += '<option value="' + key + '">' + key + '</option>';
                    });
                    $.each(apsa_effects['sticky_positions'], function (key, value) {
                        sticky_positions += '<option value="' + key + '"'+(key=='bottom_right'?'selected="selected"':'')+'>' + apsa_admin_labels['sticky_'+key] + '</option>';
                    });
                    campaign_options = '<li class="apsa-form-item">\n\
                                                <span>' + apsa_admin_labels["sticky_position"] + ' </span>\n\
                                                <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message="' + apsa_admin_labels["sticky_position_desc"] + '"></span>\n\
                                                <div class="apsa-select-block">\n\
                                                <select name="position" value="">\n\
                                                    '+sticky_positions+'\n\
                                                </select>\n\
                                                </div>\n\
                                                <span class="apsa-input-message" data-apsa-default-message="' + apsa_admin_labels["default"] + ' ' + apsa_admin_labels["sticky_position_def"] + '">' + apsa_admin_labels["default"] + ' ' + apsa_admin_labels["sticky_position_def"] + '</span>\n\
                                        </li>\n\
                                        <li class="apsa-form-item">\n\
                                                <span>' + apsa_admin_labels["change_interval"] + ' </span>&nbsp;<span class="apsa-no-cap">(' + apsa_admin_labels["second"] + ')</span>\n\
                                                <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message="' + apsa_admin_labels["change_interval_desc"] + '"></span>\n\
                                                <div class="apsa-input-block">\n\
                                                <input type="text" name="change_interval" class="apsa-positive-int" value="" />\n\
                                                </div>\n\
                                                <span class="apsa-input-message" data-apsa-default-message="' + apsa_admin_labels["default"] + ' ' + apsa_admin_labels["change_interval_def"] + '">' + apsa_admin_labels["default"] + ' ' + apsa_admin_labels["change_interval_def"] + '</span>\n\
                                        </li>\n\
                                        <li class="apsa-form-item">\n\
                                            <span>' + apsa_admin_labels["show_interval"] + '</span>&nbsp;<span class="apsa-no-cap">(' + apsa_admin_labels["second"] + ')</span>\n\
                                            <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message="' + apsa_admin_labels["show_interval_desc"] + '"></span>\n\
                                            <div class="apsa-input-block">\n\
                                            <input type="text" class="apsa-positive-int" name="view_interval" value="" />\n\
                                            </div>\n\
                                            <span class="apsa-input-message" data-apsa-default-message="' + apsa_admin_labels["default"] + ' ' + apsa_admin_labels["show_interval_def"] + '">' + apsa_admin_labels["default"] + ' ' + apsa_admin_labels["show_interval_def"] + '</span>\n\
                                        </li>\n\
                                        <li class="apsa-form-item">\n\
                                            <span>' + apsa_admin_labels["sticky_direction"] + '</span>\n\
                                            <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message="' + apsa_admin_labels["sticky_direction_desc"] + '"></span>\n\
                                            <div class="apsa-input-block">\n\
                                            <select name="sticky_animation">' + sticky_effects + '</select>\n\
                                            </div>\n\
                                            <span class="apsa-input-message"></span>\n\
                                        </li>\n\
                                        <li class="apsa-form-item">\n\
                                            <span>' + apsa_admin_labels["width"] + ' </span>&nbsp;<span class="apsa-no-cap">(px/%)</span>\n\
                                            <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message="' + apsa_admin_labels["width_desc"] + '"></span>\n\
                                            <div class="apsa-input-block">\n\
                                            <input type="text" name="width" class="apsa-size" />\n\
                                            </div>\n\
                                            <span class="apsa-input-message" data-apsa-default-message="' + apsa_admin_labels["default"] + ' 300px">' + apsa_admin_labels["default"] + ' 300px</span>\n\
                                        </li>\n\
                                        <li class="apsa-form-item">\n\
                                            <span>' + apsa_admin_labels["height"] + '</span>&nbsp;<span class="apsa-no-cap">(px/%)</span>\n\
                                            <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message="' + apsa_admin_labels["height_desc"] + '"></span>\n\
                                            <div class="apsa-input-block">\n\
                                            <input type="text" name="height" class="apsa-size" />\n\
                                            </div>\n\
                                            <span class="apsa-input-message" data-apsa-default-message="' + apsa_admin_labels["default"] + ' 200px">' + apsa_admin_labels["default"] + ' 200px</span>\n\
                                        </li>\n\
                                        <li class="apsa-form-item">\n\
                                            <span>' + apsa_admin_labels["sticky_show_delay"] + ' </span>&nbsp;<span class="apsa-no-cap">(' + apsa_admin_labels["second"] + ')</span>\n\
                                            <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message="' + apsa_admin_labels["sticky_show_delay_desc"] + '"></span>\n\
                                            <div class="apsa-input-block">\n\
                                            <input type="text" class="apsa-positive-int" name="view_after" />\n\
                                            </div>\n\
                                            <span class="apsa-input-message" data-apsa-default-message="' + apsa_admin_labels["default"] + ' 0">' + apsa_admin_labels["default"] + ' 0</span>\n\
                                        </li>\n\
                                        <li class="apsa-form-item">\n\
                                            <span>' + apsa_admin_labels["close_button_delay"] + ' </span>&nbsp;<span class="apsa-no-cap">(' + apsa_admin_labels["second"] + ')</span>\n\
                                            <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message="' + apsa_admin_labels["close_button_delay_desc"] + '"></span>\n\
                                            <div class="apsa-input-block">\n\
                                            <input type="text" class="apsa-positive-int" name="show_close_after" />\n\
                                            </div>\n\
                                            <span class="apsa-input-message" data-apsa-default-message="' + apsa_admin_labels["default"] + ' 0">' + apsa_admin_labels["default"] + ' 0</span>\n\
                                        </li>\n\
                                        <li class="apsa-form-item">\n\
                                            <span>' + apsa_admin_labels["sticky_autoclose"] + ' </span>&nbsp;<span class="apsa-no-cap">(' + apsa_admin_labels["second"] + ')</span>\n\
                                            <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message="' + apsa_admin_labels["sticky_autoclose_desc"] + '"></span>\n\
                                            <div class="apsa-input-block">\n\
                                            <input type="text" class="apsa-positive-int" name="hide_element_after" />\n\
                                            </div>\n\
                                            <span class="apsa-input-message" data-apsa-default-message="' + apsa_admin_labels["default"] + ' ' + apsa_admin_labels["sticky_autoclose_def"] + '">' + apsa_admin_labels["default"] + ' ' + apsa_admin_labels["sticky_autoclose_def"] + '</span>\n\
                                        </li>\n\
                                        <li class="apsa-form-item">\n\
                                            <div class="apsa-input-block">\n\
                                            <span>' + apsa_admin_labels["put_in_frame"] + '</span>\n\
                                            <input type="checkbox" checked="checked" />\n\
                                            <input type="hidden" class="apsa-hold-checkbox" name="put_in_frame" value="on" />\n\
                                            <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message="' + apsa_admin_labels["put_in_frame_desc"] + '"></span>\n\
                                            </div>\n\
                                        </li>\n\
                                        <li class="apsa-form-item">\n\
                                            <span>' + apsa_admin_labels["sticky_color"] + '</span>\n\
                                            <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message="' + apsa_admin_labels["sticky_color_desc"] + '"></span>\n\
                                            <div class="apsa-input-block">\n\
                                            <input type="text" name="frame_color" class="apsa-extra-small-input apsa-colorpicker" data-default-color="#ffffff" value="#ffffff">\n\
                                            </div>\n\
                                            <span class="apsa-input-message">' + apsa_admin_labels["default"] + ' #ffffff</span>\n\
                                        </li>';
                }

                /** Append campaign block(li) */
                var current_hum_date = response['creation_date'];
                var campaign_name = apsa_admin_labels['camp_name_' + campaign_type] + " - (" + current_hum_date + ")";
                var camp_shortcode = "";
                var embed_alignment = "";
                var embed_placement = "";
                if (campaign_type == "embed") {
                    camp_shortcode = '<li><span class="apsa-dash-shortcode"><span>' + apsa_admin_labels["shortcode"] + ': </span><span class="apsa-click-select">[' + apsa_plugin_data["plugin_data"]["name"].toLowerCase() + ' id="' + campaign_id + '"]</span></span>\n\
                                        <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message="' + apsa_admin_labels["embed_shortcode_desc"] + '"></span>\n\
                                    </li>';
                    embed_alignment = '<li>\n\
                                            <div class="apsa-embed-alignment apsa-embeding-alignment" data-apsa-camp-id="' + campaign_id + '" data-apsa-plugin-name="' + apsa_plugin_data["plugin_data"]["name"].toLowerCase() + '">\n\
                                                <label>' + apsa_admin_labels["alignment"] + ': </label>\n\
                                                <ul>\n\
                                                    <li class="apsa-embed-alignment-left" data-apsa-embed-alignment="left" title="' + apsa_admin_labels["alignment_left"] + '"></li>\n\
                                                    <li class="apsa-embed-alignment-center" data-apsa-embed-alignment="center" title="' + apsa_admin_labels["alignment_center"] + '"></li>\n\
                                                    <li class="apsa-embed-alignment-right" data-apsa-embed-alignment="right" title="' + apsa_admin_labels["alignment_right"] + '"></li>\n\
                                                    <li class="apsa-embed-alignment-none" data-apsa-embed-alignment="none" title="' + apsa_admin_labels["alignment_none"] + '"></li>\n\
                                                </ul>\n\
                                            </div>\n\
                                        </li>';
                    embed_placement = '<li><span class="apsa-dash-code"><span>' + apsa_admin_labels["auto_placement"] + '</span></span>\n\
                                                    <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message="' + apsa_admin_labels["auto_placement_desc"] + '"></span>\n\
                                                </li>\n\
                                                <li class="apsa-form-item apsa-auto-placement">\n\
                                                    <span>' + apsa_admin_labels["embed_placement_before"] + '</span>\n\
                                                    <div class="apsa-input-block">\n\
                                                        <div class="apsa-placement-area">\n\
                                                            <input type="checkbox" class="apsa-auto-placement apsa-placement-before"/>\n\
                                                        </div><!--\n\
                                                         --><select name="before_align" class="apsa-placement-align apsa-before-align">\n\
                                                            <option value="none">' + apsa_admin_labels["alignment_none"] + '</option>\n\
                                                            <option value="left">' + apsa_admin_labels["alignment_left"] + '</option>\n\
                                                            <option value="center">' + apsa_admin_labels["alignment_center"] + '</option>\n\
                                                            <option value="right">' + apsa_admin_labels["alignment_right"] + '</option>\n\
                                                        </select>\n\
                                                    </div>\n\
                                                </li>\n\
                                                <li class="apsa-form-item apsa-auto-placement">\n\
                                                    <span>' + apsa_admin_labels["embed_placement_after"] + '</span>\n\
                                                    <div class="apsa-input-block">\n\
                                                        <div class="apsa-placement-area">\n\
                                                            <input type="checkbox" class="apsa-auto-placement apsa-placement-after"/>\n\
                                                        </div><!--\n\
							 --><select name="after_align" class="apsa-placement-align apsa-after-align">\n\
                                                            <option value="none">' + apsa_admin_labels["alignment_none"] + '</option>\n\
                                                            <option value="left">' + apsa_admin_labels["alignment_left"] + '</option>\n\
                                                            <option value="center">' + apsa_admin_labels["alignment_center"] + '</option>\n\
                                                            <option value="right">' + apsa_admin_labels["alignment_right"] + '</option>\n\
                                                        </select>\n\
                                                    </div>\n\
                                                </li>';
                }

                $('#apsa-campaigns-block').prepend('<li class="apsa-campaign-block apsa-slide-cont" data-apsa-camp-type="' + campaign_type + '" data-apsa-status="suspended" data-apsa-campaign-id="' + campaign_id + '" id="apsa-campaign-block-' + campaign_id + '">\n\
                        <div class="apsa-campaign-header apsa-slide-opener" data-apsa-open-slide="false">\n\
                            <div class="apsa-campaign-type-logo" data-apsa-campaign-type="' + campaign_type + '"></div>\n\
                            <div class="apsa-camp-info">\n\
                                <input type="text" class="apsa-suspended-input apsa-campaign-name" value="' + campaign_name + '" />\n\
                                <p class="apsa-dash-pub-date"><span>' + apsa_admin_labels["creation_date"] + '</span>&nbsp;' + current_hum_date + '</p>\n\
                            </div>\n\
                            <span class="apsa-campaign-status apsa-camp-status-suspended">' + apsa_admin_labels["status_suspended"] + '</span>\n\
                            <ul class="apsa-camp-data">\n\
                                <li class="apsa-fr-event-count">\n\
                                    <div>0</div>\n\
                                    <div>' + apsa_admin_labels["camp_event_count"] + '</div>\n\
                                </li>\n\
                                <li class="apsa-child-event-count apsa-inside-data">\n\
                                    <div>0</div>\n\
                                    <div>' + apsa_admin_labels["camp_event_count_child"] + '</div>\n\
                                </li>\n\
                                <li class="apsa-elements-count">\n\
                                    <div>0</div>\n\
                                    <div>' + apsa_admin_labels["camp_elements_count"] + '</div>\n\
                                </li>\n\
                            </ul>\n\
                            <span class="apsa-slide-open-pointer"></span>\n\
                        </div>\n\
                        <div class="apsa-campaign-content apsa-sliding-block" data-apsa-open="false">\n\
                            <div class="apsa-campaign-elements">\n\
                                <div class="apsa-elements-header">\n\
                                    <h3>' + apsa_admin_labels["camp_elements"] + '</h3>\n\
                                    <ul class="apsa-elements-filter">\n\
                                        <li data-apsa-element-filter="all">\n\
                                            <span class="apsa-element-status-name apsa-selected-status">' + apsa_admin_labels["filter_element_all"] + '</span>\n\
                                            <span class="apsa-status-count">0</span>\n\
                                        </li>\n\
                                        <li class="apsa-inside-li" data-apsa-element-filter="active">\n\
                                            <span class="apsa-element-status-name">' + apsa_admin_labels["filter_element_active"] + '</span>\n\
                                            <span class="apsa-status-count">0</span>\n\
                                        </li>\n\
                                        <li data-apsa-element-filter="suspended">\n\
                                            <span class="apsa-element-status-name">' + apsa_admin_labels["filter_element_suspended"] + '</span>\n\
                                            <span class="apsa-status-count">0</span>\n\
                                        </li>\n\
                                    </ul>\n\
                                    <div class="apsa-waiting-wrapper">\n\
                                        <button class="button button-primary apsa-add-element apsa-new" data-apsa-campaign-id="' + campaign_id + '" data-apsa-campaign-type="' + campaign_type + '">' + apsa_admin_labels["add_new_element"] + '</button>\n\
                                    </div>\n\
                                </div>\n\
                                <ul class="apsa-elements-list apsa-sortable">\n\
                                </ul>\n\
                            </div>\n\
                            <div class="apsa-campaign-settings">\n\
                                <div class="apsa-slide-cont apsa-campaign-fate apsa-item-part">\n\
                                    <h3 class="apsa-slide-opener" data-apsa-open-slide="true">\n\
                                        <span>' + apsa_admin_labels["general"] + '</span>\n\
                                        <span class="apsa-slide-open-pointer"></span>\n\
                                    </h3>\n\
                                    <div class="apsa-sliding-block" data-apsa-open="true">\n\
                                        <ul class="apsa-main-settings">' + camp_shortcode + embed_alignment + embed_placement + '\n\
                                            <ul class="apsa-actions apsa-campaign-actions">\n\
                                                <li data-apsa-action="active">\n\
                                                    <span class="apsa-action-name apsa-dash-activate">' + apsa_admin_labels["activate_camp"] + '</span>\n\
                                                </li>\n\
                                                <li data-apsa-action="export" class="apsa-export-camp-elements apsa-export-range">\n\
                                                    <span class="apsa-action-name apsa-dash-export">' + apsa_admin_labels["export_campaign_Stats"] + '</span>\n\
                                                </li>\n\
                                                <li data-apsa-action="delete">\n\
                                                    <span class="apsa-action-name apsa-dash-delete">' + apsa_admin_labels["delete_camp"] + '</span>\n\
                                                </li>\n\
                                            </ul>\n\
                                        </ul>\n\
                                        <div class="apsa-tags-inputs">\n\
                                            <span class="apsa-tags-label">' + apsa_admin_labels["include"] + '</span>\n\
                                            <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message=\'' + apsa_admin_labels["include_camp_desc"] + '\n\
                                                                                                                            <br><br><strong>' + apsa_admin_labels["specify_tag"] + ' - <span style=\"color: #F0AD4E\">' + apsa_admin_labels["yellow"] + '</span></strong>\n\
                                                                                                                            <br><strong>' + apsa_admin_labels["specify_category"] + ' - <span style=\"color: #337AB7\">' + apsa_admin_labels["blue"] + '</span></strong>\n\
                                                                                                                            <br><strong>' + apsa_admin_labels["specify_post"] + ' - <span style=\"color: #D9534F\">' + apsa_admin_labels["red"] + '</span></strong>\n\
                                                                                                                            <br><strong>' + apsa_admin_labels["specify_page"] + ' - <span style=\"color: #5CB85C\">' + apsa_admin_labels["green"] + '</span></strong>\n\
                                                                                                                            <br><strong>' + apsa_admin_labels["specify_language"] + ' - <span style=\"color: #5bc0de\">' + apsa_admin_labels["light_blue"] + '</span></strong>\n\
                                                                                                                            <br><strong>' + apsa_admin_labels["specify_device"] + ' - <span style=\"color: #aaaaaa\">' + apsa_admin_labels["grey"] + '</span></strong>\'></span>\n\
                                            <div class="apsa-tags">\n\
                                                <div class="apsa_tag_include">\n\
                                                    <input type="text" class="typeahead-auto"/>\n\
                                                </div>\n\
                                            </div>\n\
                                            <div class="apsa-slide-cont">\n\
                                                <span class="apsa-tags-label apsa-action-name apsa-slide-opener" data-apsa-open-slide="false">' + apsa_admin_labels["exclude"] + '</span>\n\
                                                <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message=\'' + apsa_admin_labels["exclude_camp_desc"] + '\n\
                                                                                                                            <br><br><strong>' + apsa_admin_labels["specify_tag"] + ' - <span style=\"color: #F0AD4E\">' + apsa_admin_labels["yellow"] + '</span></strong>\n\
                                                                                                                            <br><strong>' + apsa_admin_labels["specify_category"] + ' - <span style=\"color: #337AB7\">' + apsa_admin_labels["blue"] + '</span></strong>\n\
                                                                                                                            <br><strong>' + apsa_admin_labels["specify_post"] + ' - <span style=\"color: #D9534F\">' + apsa_admin_labels["red"] + '</span></strong>\n\
                                                                                                                            <br><strong>' + apsa_admin_labels["specify_page"] + ' - <span style=\"color: #5CB85C\">' + apsa_admin_labels["green"] + '</span></strong>\n\
                                                                                                                            <br><strong>' + apsa_admin_labels["specify_language"] + ' - <span style=\"color: #5bc0de\">' + apsa_admin_labels["light_blue"] + '</span></strong>\n\
                                                                                                                            <br><strong>' + apsa_admin_labels["specify_device"] + ' - <span style=\"color: #aaaaaa\">' + apsa_admin_labels["grey"] + '</span></strong>\'></span>\n\
                                                <div class="apsa-tags apsa-sliding-block" data-apsa-open="false">\n\
                                                    <div class="apsa_tag_exclude">\n\
                                                        <input type="text" class="typeahead-auto"/>\n\
                                                    </div>\n\
                                                </div>\n\
                                            </div>\n\
                                        </div>\n\
                                    </div>\n\
                                </div>\n\
                                <div class="apsa-save-camp-cont">\n\
                                    <div class="apsa-waiting-wrapper">\n\
                                        <input type="button" class="button button-primary apsa-save-campaign-options" apsa-data-campaign-id="' + campaign_id + '" value="' + apsa_admin_labels["update_camp"] + '" />\n\
                                    </div>\n\
                                </div>\n\
                                <div class="apsa-slide-cont apsa-item-part">\n\
                                    <h3 class="apsa-slide-opener" data-apsa-open-slide="false">\n\
                                        <span>' + apsa_admin_labels["options"] + '</span>\n\
                                        <span class="apsa-slide-open-pointer"></span>\n\
                                    </h3>\n\
                                    <div class="apsa-sliding-block" data-apsa-open="false">\n\
                                        <form class="apsa-campaign-options-form"> \n\
                                            <ul class="apsa-campaign-options">' + campaign_options + '</ul>\n\
                                        </form>\n\
                                        <form class="apsa-child-campaign-options-form">\n\
                                            <ul class="apsa-campaign-options">' + child_campaign_options + '</ul>\n\
                                        </form>\n\
                                    </div>\n\
                                </div>\n\
                                <div class="apsa-campaign-stat-cont apsa-stat-cont apsa-slide-cont apsa-item-part">\n\
                                    <h3 class="apsa-slide-opener apsa-camp-stat-opener" data-apsa-open-slide="false">\n\
                                        <span>' + apsa_admin_labels["statistics_camp"] + '</span>\n\
                                        <span class="apsa-slide-open-pointer"></span>\n\
                                    </h3>\n\
                                    <div class="apsa-sliding-block" data-apsa-open="false">\n\
                                        <div class="apsa-stat-filter">\n\
                                            <input type="text" class="apsa-stat-from" placeholder="' + apsa_admin_labels["stat_from_place"] + '" />\n\
                                            <span>-</span>\n\
                                            <input type="text" class="apsa-stat-to" placeholder="' + apsa_admin_labels["stat_to_place"] + '" />\n\
                                            <input type="button" class="button apsa-get-statistics" value="' + apsa_admin_labels["stat_show"] + '" />\n\
                                            <div class="apsa-export-single-stat apsa-export-camp-stat">\n\
                                                <span class="apsa-action-name apsa-dash-export" title="' + apsa_first_to_upper_case(apsa_admin_labels["export_stats"]) + '"></span>\n\
                                            </div>\n\
                                        </div>\n\
                                        <div class="apsa-campaign-stat apsa-stat-block" data-apsa-camp-id="' + campaign_id + '" data-apsa-camp-title="' + campaign_name + '"></div>\n\
                                    </div>\n\
                                </div>\n\
                            </div>\n\
                        </div>\n\
                    </li>');
                // Enhance all campaigns count
                var count = $("[data-apsa-filter='all']").find(".apsa-status-count").text();
                count++;
                $("[data-apsa-filter='all']").find(".apsa-status-count").text(count);
                // Enhance active campaigns count
                var count = $("[data-apsa-filter='suspended']").find(".apsa-status-count").text();
                count++;
                $("[data-apsa-filter='suspended']").find(".apsa-status-count").text(count);
                // Make sortable this child elements block
                apsa_make_elements_sortable();
                apsa_charts_datepicker();
                $('.apsa-colorpicker').wpColorPicker({
                    change: function () {
                        apsa_detect_change($(this));
                    }
                });
                apsa_make_bloodhound();
                $("#apsa-export-all").removeClass("apsa-all-action-hidden");
                $("#apsa-update-all").removeClass("apsa-all-action-hidden");
                // Success message
                apsa_action_message("success", apsa_admin_labels["created_new_camp_msg"]);
            },
            error: function () {
                // Error message
                apsa_action_message("error", apsa_admin_labels["cant_add_camp_msg"]);
            },
            complete: function () {
                camp_creation_process = false;
            }
        });
    }

    $("#apsa-campaign-save").click(function (e) {

        var campaign_type = $("#apsa-campaign-types").find(".apsa-block-selected").attr('data-apsa-campaign-type');
        if (campaign_type === undefined) {

            /** Close popup and return */
            $('#apsa-add-campaign-popup').attr('data-apsa-open', "false");
            jQuery('body').removeClass('modal-open');
            $('#apsa-managing-overlay').fadeOut(150);
            apsa_action_message("error", apsa_admin_labels["camp_choose_type_msg"]);
            return;
        }

        camp_creation_process = true;
        /** Ajax request for insert new campaign in db */
        apsa_add_new_campaign(campaign_type)

        /** Close popup after save */
        $('#apsa-add-campaign-popup').attr('data-apsa-open', "false");
        jQuery('body').removeClass('modal-open');
        $('#apsa-managing-overlay').fadeOut(150);
    });
    /**
     * Close popup
     */
    $(document).on('click', '#apsa-managing-overlay, .apsa-close-popup', function () {
        $('.apsa-popup').attr('data-apsa-open', "false");
        jQuery('body').removeClass('modal-open');
        $('#apsa-managing-overlay').fadeOut(150);
    });
    /** Close popup with escape key */
    $(document).on('keyup', function (e) {

        if (e.which === 27) {
            $('.apsa-popup').attr('data-apsa-open', "false");
            jQuery('body').removeClass('modal-open');
            $('#apsa-managing-overlay').fadeOut(150);
        }
    });
    /**
     * During openning campaign block close all other campaign blocks
     */
    $(document).on('click', '.apsa-campaign-header', function () {
        if ($(this).next('.apsa-sliding-block').attr('data-apsa-open') == 'true') {

            $(".apsa-campaign-header").each(function () {
                if ($(this).attr("data-apsa-open-slide") == "true") {
                    $(this).attr("data-apsa-open-slide", "false");
                    $(this).siblings('.apsa-sliding-block').attr('data-apsa-open', "false");
                }
            });
            $(this).attr("data-apsa-open-slide", "true");
            $(this).next('.apsa-sliding-block').attr('data-apsa-open', "true");
        }
    });
    /**
     * Save campaign options
     */
    $(document).on('click', '.apsa-save-campaign-options', function (e) {

        var that_button = $(this);
        // Display spinner
        that_button.closest(".apsa-waiting-wrapper").addClass('apsa-waiting-button');
        e.preventDefault();
        /** Check required fields */
        var is_error_exist = false;

        if ($(this).closest(".apsa-campaign-settings").find(".apsa-auto-placement").length != 0) {
            var auto_placement = "off";

            var auto_placement_before = $(this).closest(".apsa-campaign-settings").find(".apsa-placement-before").is(':checked');
            var auto_placement_after = $(this).closest(".apsa-campaign-settings").find(".apsa-placement-after").is(':checked');

            if (auto_placement_before == true && auto_placement_after == true) {
                auto_placement = "both";
            } else if (auto_placement_before == true || auto_placement_after == true) {
                auto_placement = ((auto_placement_before == true) ? "before" : ((auto_placement_after == true) ? "after" : "off"));
            }

            var before_align = $(this).closest(".apsa-campaign-settings").find(".apsa-before-align").val();
            var after_align = $(this).closest(".apsa-campaign-settings").find(".apsa-after-align").val();
        }

        var camp_include_tags = $(this).closest(".apsa-campaign-settings").find(".apsa_tag_include").find(".typeahead-auto").val();
        var camp_exclude_tags = $(this).closest(".apsa-campaign-settings").find(".apsa_tag_exclude").find(".typeahead-auto").val();
        // validation
        is_error_exist = apsa_validation($(this).closest('.apsa-campaign-block'));
        // validation for child options
        if (typeof apsa_child_valid == 'function') {
            var is_child_error_exist = apsa_child_valid($(this).closest('.apsa-campaign-block'));
            if (is_child_error_exist)
                is_error_exist = true;
        }
        //-------------------------

        var campaign_name = $(this).closest('.apsa-campaign-block').find(".apsa-campaign-name").val();
        var campaign_options = $(this).closest('.apsa-campaign-block').find('.apsa-campaign-options-form').serializeArray();
        // concat child campaign options
        if (typeof apsa_child_get_campaign_options == 'function') {
            var child_campaign_options = apsa_child_get_campaign_options($(this));
            campaign_options = campaign_options.concat(child_campaign_options);
        }

        if (typeof auto_placement != "undefined") {
            campaign_options.push({name: "auto_placement", value: auto_placement});
        }

        if (typeof before_align != "undefined") {
            campaign_options.push({name: "before_align", value: before_align});
        }

        if (typeof after_align != "undefined") {
            campaign_options.push({name: "after_align", value: after_align});
        }

        campaign_options.push({name: "camp_include_tags", value: camp_include_tags});
        campaign_options.push({name: "camp_exclude_tags", value: camp_exclude_tags});

        var campaign_id = $(this).attr('apsa-data-campaign-id');
        /** Save new and update old elements */
        var closest_campaign = $(this).closest('.apsa-campaign-block');
        var elements = new Array();
        closest_campaign.find('.apsa-element-form').each(function () {
            var element_data = {};
            var element_include_tags = $(this).find(".apsa_tag_include").find(".typeahead-auto").val();
            var element_exclude_tags = $(this).find(".apsa_tag_exclude").find(".typeahead-auto").val();
            var element_fr_options = $(this).find(".apsa-fr-option,.apsa-fr-option input").serializeArray();
            var element_info = {};
            var element_options = {};
            element_options["element_include_tags"] = element_include_tags;
            element_options["element_exclude_tags"] = element_exclude_tags;
            // check if element overdue
            var current_date = new Date();
            current_date.setHours(0, 0, 0, 0);
            var views = $(this).find('.apsa-fr-event-count div').first().text();
            var element_type = '';
            for (var i = 0; i < element_fr_options.length; i++) {
                if (element_fr_options[i]['name'] == 'schedule') {
                    var ad_schedule = new Date(element_fr_options[i]['value']);
                    ad_schedule.setHours(0, 0, 0, 0);
                    if (current_date.getTime() < ad_schedule.getTime()) {
                        $(this).find('.apsa-schedule-date').addClass('apsa-overdue-element');
                        $(this).find('.apsa-schedule-date').closest('.apsa-form-item').find('.apsa-input-message').addClass('apsa-warning-message').text(apsa_admin_labels["schedule_warning_message"]);
                    } else {
                        $(this).find('.apsa-schedule-date').removeClass('apsa-overdue-element');
                        $(this).find('.apsa-schedule-date').closest('.apsa-form-item').find('.apsa-input-message').removeClass('apsa-warning-message').text($(this).find('.apsa-schedule-date').closest('.apsa-form-item').find('.apsa-input-message').data('apsa-default-message'));
                    }
                    element_options[element_fr_options[i]['name']] = element_fr_options[i]['value'];
                }
                if (element_fr_options[i]['name'] == 'deadline') {
                    var ad_deadline = new Date(element_fr_options[i]['value']);
                    ad_deadline.setHours(0, 0, 0, 0);
                    if (current_date.getTime() > ad_deadline.getTime()) {
                        $(this).find('.apsa-deadline-date').addClass('apsa-overdue-element');
                        $(this).find('.apsa-deadline-date').closest('.apsa-form-item').find('.apsa-input-message').addClass('apsa-warning-message').text(apsa_admin_labels["deadline_warning_message"]);
                    } else {
                        $(this).find('.apsa-deadline-date').removeClass('apsa-overdue-element');
                        $(this).find('.apsa-deadline-date').closest('.apsa-form-item').find('.apsa-input-message').removeClass('apsa-warning-message').text($(this).find('.apsa-deadline-date').closest('.apsa-form-item').find('.apsa-input-message').data('apsa-default-message'));
                    }
                    element_options[element_fr_options[i]['name']] = element_fr_options[i]['value'];
                }
                if (element_fr_options[i]['name'] == 'restrict_views') {
                    if (!$(this).find('[name="restrict_views"]').hasClass('apsa-error-input')) {
                        var restrict_views = element_fr_options[i]['value'];
                        if (parseInt(views) >= parseInt(restrict_views)) {
                            $(this).find('[name="restrict_views"]').addClass('apsa-overdue-element');
                            $(this).find('[name="restrict_views"]').closest('.apsa-form-item').find('.apsa-input-message').addClass('apsa-warning-message').text(apsa_admin_labels["views_warning_message"]);
                        } else {
                            $(this).find('[name="restrict_views"]').removeClass('apsa-overdue-element');
                            $(this).find('[name="restrict_views"]').closest('.apsa-form-item').find('.apsa-input-message').removeClass('apsa-warning-message').text($(this).find('[name="restrict_views"]').closest('.apsa-form-item').find('.apsa-input-message').data('apsa-default-message'));
                        }
                        element_options[element_fr_options[i]['name']] = element_fr_options[i]['value'];
                    }
                }
                if (element_fr_options[i]['name'] == 'type') {
                    element_type = element_fr_options[i]['value'];
                    element_info[element_fr_options[i]['name']] = element_fr_options[i]['value'];
                }
                if (element_fr_options[i]['name'] == 'action') {
                    element_info[element_fr_options[i]['name']] = element_fr_options[i]['value'];
                }
                if (element_fr_options[i]['name'] == 'campaign_id') {
                    element_info[element_fr_options[i]['name']] = element_fr_options[i]['value'];
                }
                if (element_fr_options[i]['name'] == 'priority') {
                    element_info[element_fr_options[i]['name']] = element_fr_options[i]['value'];
                }
                if (element_fr_options[i]['name'] == 'element_id') {
                    element_info[element_fr_options[i]['name']] = element_fr_options[i]['value'];
                }
                if (element_fr_options[i]['name'] == 'title') {
                    element_info[element_fr_options[i]['name']] = element_fr_options[i]['value'];
                }
            }

            var apsa_child_element_data = apsa_get_child_element_options(this, element_type);
            $.extend(element_options, apsa_child_element_data, element_options);
            element_data['element_options'] = element_options;
            element_data['element_info'] = element_info;
            var apsa_data = element_data;
            elements.push(apsa_data);
        });
        if (is_error_exist == true) {
            that_button.closest(".apsa-waiting-wrapper").removeClass('apsa-waiting-button');
            apsa_action_message("error", apsa_admin_labels["check_form_req_msg"]);
            return false;
        }

        /** Ajax request for update campaign options */
        $.ajax({
            type: "POST",
            url: apsa_ajax_url,
            dataType: "json",
            data: {
                action: "apsa_ajax_update_campaign_options",
                campaign_name: campaign_name,
                campaign_id: campaign_id,
                campaign_options: campaign_options,
                campaign_elements: elements
            },
            success: function (response) {
                if (response['success'] != 1) {
                    apsa_action_message("error", apsa_admin_labels["camp_not_saved_msg"]);
                    return false;
                }

                closest_campaign.find('.apsa-element-action').val('update');
                apsa_action_message("success", apsa_admin_labels["camp_saved_msg"]);
                apsa_remove_detect_change(that_button);
            },
            error: function () {
                that_button.closest(".apsa-waiting-wrapper").removeClass('apsa-waiting-button');
                apsa_action_message("error", apsa_admin_labels["camp_not_saved_msg"]);
            }
        });
        e.stopPropagation();
    });
    /**
     * Hold form checkbox item value into hidden input
     */
    $(document).on('change', 'form [type=checkbox]', function () {
        if ($(this).is(':checked')) {
            $(this).next('.apsa-hold-checkbox').val('on');
        } else {
            $(this).next('.apsa-hold-checkbox').val('');
        }
    });
    /**
     * Add new element
     */
    function apsa_add_new_element(campaign_id, campaign_type, element_type) {
        $.ajax({
            type: "POST",
            url: apsa_ajax_url,
            dataType: "json",
            data: {
                action: "apsa_ajax_add_element",
                type: element_type,
                campaign_id: campaign_id,
            },
            success: function (response) {

                if (response['success'] != 1) {
                    apsa_action_message("error", apsa_admin_labels["cant_add_element_msg"]);
                    return false;
                }

                var element_id = response['element_id'];
                /** Determin default element name */
                var current_hum_date = response['creation_date'];
                var element_title = apsa_admin_labels["element_name_" + element_type] + " - (" + current_hum_date + ")";
                var element_child_block_html = apsa_create_child_element_option_block(element_type, campaign_type, apsa_admin_labels, apsa_effects['patterns']);
                var element_block_html = '<li id="apsa-element-block-' + element_id + '" class="apsa-element-block" data-apsa-status="active" data-apsa-element-id="' + element_id + '">\n\
                                        <form class="apsa-element-form">\n\
                                            <input type="hidden" name="action" class="apsa-element-action apsa-fr-option" value="update"/>\n\
                                            <input type="hidden" name="campaign_id" class="apsa-fr-option" value="' + campaign_id + '"/>\n\
                                            <input type="hidden" name="priority" class="apsa-element-priority apsa-fr-option" />\n\
                                            <input type="hidden" name="element_id" class="apsa-fr-option" value="' + element_id + '"/>\n\
                                            <div class="apsa-element-controls"><div class="apsa-sort-mover" title="' + apsa_first_to_upper_case(apsa_admin_labels["sort_mover_tile"]) + '"></div>\n\
                                                <ul class="apsa-actions apsa-element-actions">\n\
                                                    <li data-apsa-action="suspend" title="' + apsa_first_to_upper_case(apsa_admin_labels["suspend_element"]) + '">\n\
                                                        <span class="apsa-action-name apsa-dash-suspend"></span>\n\
                                                    </li>\n\
                                                    <li data-apsa-action="delete" title="' + apsa_first_to_upper_case(apsa_admin_labels["delete_element"]) + '">\n\
                                                        <span class="apsa-action-name apsa-dash-delete"></span>\n\
                                                    </li>\n\
                                                </ul>\n\
                                                <div class="apsa-element-header">\n\
                                                    <div class="apsa-element-type-logo" data-apsa-element-type="' + element_type + '">\n\
                                                        <input type="hidden" name="type" class="apsa-fr-option" value="' + element_type + '"/>\n\
                                                    </div>\n\
                                                    <div class="apsa-element-info">\n\
                                                        <input type="text" name="title" class="apsa-suspended-input apsa-element-name apsa-fr-option" value="' + element_title + '" title="' + element_title + '" />\n\
                                                        <p class="apsa-dash-pub-date"><span>' + apsa_admin_labels["creation_date"] + '</span>&nbsp;' + current_hum_date + '</p>\n\
                                                    </div>\n\
                                                    <span class="apsa-element-status apsa-element-status-active">' + apsa_admin_labels["status_active"].toLowerCase() + '</span>\n\
                                                    <ul class="apsa-element-data">\n\
                                                        <li class="apsa-fr-event-count">\n\
                                                            <div>0</div>\n\
                                                            <div>' + apsa_admin_labels["element_event_count"] + '</div>\n\
                                                        </li>\n\
                                                        <li class="apsa-child-event-count apsa-inside-data">\n\
                                                            <div>0</div>\n\
                                                            <div>' + apsa_admin_labels["element_event_count_child"] + '</div>\n\
                                                        </li>\n\
                                                        <li class="apsa-crt">\n\
                                                            <div>0</div>\n\
                                                            <div>' + apsa_admin_labels["element_rate"] + '(%)</div>\n\
                                                        </li>\n\
                                                    </ul>\n\
                                                </div>\n\
                                                <div class="apsa-tags-inputs">\n\
                                                    <span class="apsa-tags-label">' + apsa_admin_labels["include"] + '</span>\n\
                                                    <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message=\'' + apsa_admin_labels["include_element_desc"] + '\n\
                                                                                                                    <br><br><strong>' + apsa_admin_labels["specify_tag"] + ' - <span style=\"color: #F0AD4E\">' + apsa_admin_labels["yellow"] + '</span></strong>\n\
                                                                                                                    <br><strong>' + apsa_admin_labels["specify_category"] + ' - <span style=\"color: #337AB7\">' + apsa_admin_labels["blue"] + '</span></strong>\n\
                                                                                                                    <br><strong>' + apsa_admin_labels["specify_post"] + ' - <span style=\"color: #D9534F\">' + apsa_admin_labels["red"] + '</span></strong>\n\
                                                                                                                    <br><strong>' + apsa_admin_labels["specify_page"] + ' - <span style=\"color: #5CB85C\">' + apsa_admin_labels["green"] + '</span></strong>\n\
                                                                                                                    <br><strong>' + apsa_admin_labels["specify_language"] + ' - <span style=\"color: #5bc0de\">' + apsa_admin_labels["light_blue"] + '</span></strong>\n\
                                                                                                                    <br><strong>' + apsa_admin_labels["specify_device"] + ' - <span style=\"color: #aaaaaa\">' + apsa_admin_labels["grey"] + '</span></strong>\'></span>\n\
                                                    <div class="apsa-tags">\n\
                                                        <div class="apsa_tag_include">\n\
                                                            <input type="text" class="typeahead-auto"/>\n\
                                                        </div>\n\
                                                    </div>\n\
                                                    <div class="apsa-slide-cont">\n\
                                                        <span class="apsa-tags-label apsa-action-name apsa-slide-opener" data-apsa-open-slide="false">' + apsa_admin_labels["exclude"] + '</span>\n\
                                                        <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message=\'' + apsa_admin_labels["exclude_element_desc"] + '\n\
                                                                                                                    <br><br><strong>' + apsa_admin_labels["specify_tag"] + ' - <span style=\"color: #F0AD4E\">Yellow</span></strong>\n\
                                                                                                                    <br><strong>' + apsa_admin_labels["specify_category"] + ' - <span style=\"color: #337AB7\">Blue</span></strong>\n\
                                                                                                                    <br><strong>' + apsa_admin_labels["specify_post"] + ' - <span style=\"color: #D9534F\">Red</span></strong>\n\
                                                                                                                    <br><strong>' + apsa_admin_labels["specify_page"] + ' - <span style=\"color: #5CB85C\">Green</span></strong>\n\
                                                                                                                    <br><strong>' + apsa_admin_labels["specify_language"] + ' - <span style=\"color: #5bc0de\">' + apsa_admin_labels["light_blue"] + '</span></strong>\n\
                                                                                                                    <br><strong>' + apsa_admin_labels["specify_device"] + ' - <span style=\"color: #aaaaaa\">' + apsa_admin_labels["grey"] + '</span></strong>\'></span>\n\
                                                        <div class="apsa-tags apsa-sliding-block" data-apsa-open="false">\n\
                                                            <div class="apsa_tag_exclude">\n\
                                                                <input type="text" class="typeahead-auto"/>\n\
                                                            </div>\n\
                                                        </div>\n\
                                                    </div>\n\
                                                </div>\n\
                                            </div>\n\
                                            <!-- Element options -->\n\
                                            <div class="apsa-slide-cont apsa-element-options-cont">\n\
                                                <h3 class="apsa-slide-opener" data-apsa-open-slide="false">\n\
                                                    <span>' + apsa_admin_labels["options"] + '</span>\n\
                                                    <span class="apsa-slide-open-pointer"></span>\n\
                                                </h3>\n\
                                                <div class="apsa-sliding-block" data-apsa-open="false" >\n\
                                                    <ul class="apsa-element-options">\n\
                                                        <li class="apsa-form-item apsa-element-usual-input apsa-fr-option">\n\
                                                            <span>' + apsa_admin_labels["schedule"] + '</span>\n\
                                                            <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message="' + apsa_admin_labels["schedule_desc"] + '"></span>\n\
                                                            <div class="apsa-input-block">\n\
                                                            <input type="text" class="apsa-schedule-date" />\n\
                                                            <input type="hidden" name="schedule" />\n\
                                                            </div>\n\
                                                            <span class="apsa-input-message" data-apsa-default-message="' + apsa_admin_labels["default"] + ' ' + apsa_admin_labels["schedule_def"] + '">' + apsa_admin_labels["default"] + ' ' + apsa_admin_labels["schedule_def"] + '</span>\n\
                                                        </li>\n\
                                                        <li class="apsa-form-item apsa-element-usual-input apsa-fr-option">\n\
                                                            <span>' + apsa_admin_labels["deadline"] + '</span>\n\
                                                            <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message="' + apsa_admin_labels["deadline_desc"] + '"></span>\n\
                                                            <div class="apsa-input-block">\n\
                                                            <input type="text" class="apsa-deadline-date" />\n\
                                                            <input type="hidden" name="deadline" />\n\
                                                            </div>\n\
                                                            <span class="apsa-input-message" data-apsa-default-message="' + apsa_admin_labels["default"] + ' ' + apsa_admin_labels["deadline_def"] + '">' + apsa_admin_labels["default"] + ' ' + apsa_admin_labels["deadline_def"] + '</span>\n\
                                                        </li>\n\
                                                        <li class="apsa-form-item apsa-element-usual-input apsa-fr-option">\n\
                                                            <span>' + apsa_admin_labels["max_views"] + '</span>\n\
                                                            <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message="' + apsa_admin_labels["max_views_desc"] + '"></span>\n\
                                                            <div class="apsa-input-block">\n\
                                                            <input type="text" class="apsa-positive-int" name="restrict_views" />\n\
                                                            </div>\n\
                                                            <span class="apsa-input-message" data-apsa-default-message="' + apsa_admin_labels['default'] + ' ' + apsa_admin_labels['max_views_def'] + '">' + apsa_admin_labels["default"] + ' ' + apsa_admin_labels["max_views_def"] + '</span>\n\
                                                        </li>\n\
                                                    ' + element_child_block_html + '</ul></div>\n\
                                            </div>\n\
                                        </form>\n\
                                        <!-- element statistics by chart and table -->\n\
                                        <div class="apsa-slide-cont apsa-element-stat-cont apsa-stat-cont">\n\
                                            <h3 class="apsa-slide-opener apsa-element-stat-opener" data-apsa-open-slide="false">\n\
                                                <span>' + apsa_admin_labels["Statistics_element"] + '</span>\n\
                                                <span class="apsa-slide-open-pointer"></span>\n\
                                            </h3>\n\
                                            <div class="apsa-sliding-block apsa-range-stat" data-apsa-open="false" >\n\
                                                <div class="apsa-stat-filter">\n\
                                                    <input type="text" class="apsa-stat-from" placeholder="' + apsa_admin_labels["stat_from_place"] + '" />\n\
                                                    <span>-</span>\n\
                                                    <input type="text" class="apsa-stat-to" placeholder="' + apsa_admin_labels["stat_to_place"] + '" />\n\
                                                    <input type="button" class="button apsa-get-statistics" value="' + apsa_admin_labels["stat_show"] + '" />\n\
                                                    <div class="apsa-export-single-stat apsa-export-element-stat">\n\
                                                    <span class="apsa-action-name apsa-dash-export" title="' + apsa_first_to_upper_case(apsa_admin_labels["export_stats"]) + '"></span>\n\
                                                    </div>\n\
                                                </div>\n\
                                                <div class="apsa-stat-container apsa-stat-block" data-apsa-element-id="' + element_id + '" data-apsa-element-title="' + element_title + '"></div>\n\
                                            </div>\n\
                                        </div>\n\
                                    </li>';
                $('#apsa-campaign-block-' + campaign_id).find('.apsa-elements-list').prepend(element_block_html);
                // Set chart range datepicker
                apsa_charts_datepicker();
                // Set deadlines datepicker
                apsa_set_datepicker(".apsa-deadline-date");
                // Set schedules datepicker
                apsa_set_datepicker(".apsa-schedule-date");
                apsa_refresh_elements_priority($('#apsa-campaign-block-' + campaign_id).find('.apsa-campaign-elements'));
                var count = $('#apsa-campaign-block-' + campaign_id).find("[data-apsa-element-filter='active']").find(".apsa-status-count").text();
                count++;
                $('#apsa-campaign-block-' + campaign_id).find("[data-apsa-element-filter='active']").find(".apsa-status-count").text(count);
                var count = $('#apsa-campaign-block-' + campaign_id).find("[data-apsa-element-filter='all']").find(".apsa-status-count").text();
                count++;
                $('#apsa-campaign-block-' + campaign_id).find("[data-apsa-element-filter='all']").find(".apsa-status-count").text(count);
                var elements_count = $('#apsa-campaign-block-' + campaign_id + ' .apsa-element-block').length;
                $('#apsa-campaign-block-' + campaign_id + ' .apsa-elements-count div:first-child').text(elements_count);
                apsa_make_bloodhound();
                apsa_action_message("success", apsa_admin_labels["created_new_element_msg"]);
            },
            error: function () {
                apsa_action_message("error", apsa_admin_labels["cant_add_element_msg"]);
            },
            complete: function () {
                el_creation_process = false;
                $('[data-apsa-campaign-id="'+campaign_id+'"].apsa-add-element').closest('.apsa-waiting-wrapper').removeClass('apsa-waiting-button');
            }
        });
    }

    $(document).on('click', '.apsa-add-element', function () {

        // check if any element in creation process not open popup
        if (el_creation_process == true) {
            return false;
        }

        /** Append element type blocks to element types popup */
        var background_types = {};
        var popup_types = {};
        var embed_types = {};
        var sticky_types = {};
        for (var i = 0; i < apsa_plugin_data['element_data'].length; i++) {
            if ($.inArray('background', apsa_plugin_data['element_data'][i]['campaign_types']) != -1) {
                background_types[apsa_plugin_data['element_data'][i]['name']] = apsa_admin_labels["element_type_" + apsa_plugin_data['element_data'][i]['name'] + ""];
            }
            if ($.inArray('popup', apsa_plugin_data['element_data'][i]['campaign_types']) != -1) {
                popup_types[apsa_plugin_data['element_data'][i]['name']] = apsa_admin_labels["element_type_" + apsa_plugin_data['element_data'][i]['name'] + ""];
            }
            if ($.inArray('embed', apsa_plugin_data['element_data'][i]['campaign_types']) != -1) {
                embed_types[apsa_plugin_data['element_data'][i]['name']] = apsa_admin_labels["element_type_" + apsa_plugin_data['element_data'][i]['name'] + ""];
            }
            if ($.inArray('sticky', apsa_plugin_data['element_data'][i]['campaign_types']) != -1) {
                sticky_types[apsa_plugin_data['element_data'][i]['name']] = apsa_admin_labels["element_type_" + apsa_plugin_data['element_data'][i]['name'] + ""];
            }
        }

        // Create element type object
        var element_type = {};
        if (apsa_plugin_data['campaign_types']['background'] == 'true')
            element_type['background'] = background_types;
        if (apsa_plugin_data['campaign_types']['popup'] == 'true')
            element_type['popup'] = popup_types;
        if (apsa_plugin_data['campaign_types']['embed'] == 'true')
            element_type['embed'] = embed_types;
        if (apsa_plugin_data['campaign_types']['sticky'] == 'true')
            element_type['sticky'] = sticky_types;
        // Get current campaign type
        var campaign_type = $(this).attr('data-apsa-campaign-type');
        // Get element campaign id
        var campaign_id = $(this).attr('data-apsa-campaign-id');
        // Separate appropriate types        
        var required_types = element_type[campaign_type];
        // Append blocks

        if (Object.keys(required_types).length === 1) {
            el_creation_process = true;
            $(this).closest(".apsa-waiting-wrapper").addClass('apsa-waiting-button');            
            apsa_add_new_element(campaign_id, campaign_type, Object.keys(required_types)[0]);  
            return true;
        }

        $('#apsa-element-types').empty();
        $.each(required_types, function (type_key, type_name) {
            {
                $('#apsa-element-types').append('<div class="apsa-element-type-cont">\n\
                                      <h4>' + type_name + '</h4>\n\
                                    <div class="apsa-element-type apsa-selection-block" data-apsa-element-type="' + type_key + '">\n\
                                    <div></div>\n\
                                    </div>\n\
                                    </div>');
            }
        })

        /** Display popup */
        $('#apsa-managing-overlay').fadeIn(150);
        $('#apsa-add-element-popup').attr('data-apsa-open', "true");
        jQuery('body').addClass('modal-open');
        $('#apsa-add-element-popup').attr('data-apsa-type-for', campaign_id);
        $('#apsa-add-element-popup').attr('data-apsa-campaign-type', campaign_type);
    });
    /**
     * Choose element type and save in db, after save display element block
     */
    $(document).on('click', '#apsa-element-save', function () {

        var element_type = $("#apsa-element-types").find(".apsa-block-selected").attr('data-apsa-element-type');
        if (element_type === undefined) {

            /** Close popup after choose */
            $('#apsa-add-element-popup').attr('data-apsa-open', "false");
            jQuery('body').removeClass('modal-open');
            $('#apsa-managing-overlay').fadeOut(150);
            apsa_action_message("error", apsa_admin_labels["element_choose_type_msg"]);
            return;
        }

        var campaign_id = $('#apsa-add-element-popup').attr('data-apsa-type-for');
        var campaign_type = $('#apsa-add-element-popup').attr('data-apsa-campaign-type');

        el_creation_process = true;

        /** Ajax request for element add in db */
        apsa_add_new_element(campaign_id, campaign_type, element_type);
        /** Close popup after choose */
        $('#apsa-add-element-popup').attr('data-apsa-open', "false");
        jQuery('body').removeClass('modal-open');
        $('#apsa-managing-overlay').fadeOut(150);
    });
    /**
     * Open popup for typing code for element content, when clicked type code button
     */
    $(document).on('click', '.apsa-type-code', function () {
        /** Append current element code to popup textarea */
        var custom_code = $(this).closest('.apsa-code-button').find('.apsa-hold-code-content').val();
        myCodeMirror.setValue(custom_code);
        $('#apsa-managing-overlay').fadeIn(150);
        $('#apsa-code-popup').attr('data-apsa-open', "true");
        jQuery('body').addClass('modal-open');
        if (jQuery('.apsa-popup .CodeMirror').css('max-height') == '100%') {
            jQuery('.CodeMirror').height(window.innerHeight - jQuery('[data-apsa-open="true"] .apsa-popup-header').outerHeight(true) - jQuery('[data-apsa-open="true"] .apsa-popup-footer').outerHeight(true));
        } else {
            jQuery('.CodeMirror').attr('style', '');
        }
        myCodeMirror.refresh();
        custom_code_owner = $(this);
    });
    $(document).on('click', '#apsa-save-code', function () {

        var custom_code = myCodeMirror.getValue();
        custom_code_owner.closest('.apsa-code-button').find('.apsa-hold-code-content').text(custom_code).trigger('change');
        /** Close popup */
        $('#apsa-code-popup').attr('data-apsa-open', "false");
        jQuery('body').removeClass('modal-open');
        $('#apsa-managing-overlay').fadeOut(150);
    });
    /**
     * Filter campaigns by status
     */
    $('.apsa-campaigns-filter li').on('click', function () {

        /** Change selected status */
        $('.apsa-status-name').removeClass('apsa-selected-status');
        $(this).find('.apsa-status-name').addClass('apsa-selected-status');
        var filter_by = $(this).attr('data-apsa-filter');
        if (filter_by == "all") {
            $('.apsa-campaign-block').removeClass('apsa-campaign-hidden');
        } else {
            $('.apsa-campaign-block').each(function () {
                if ($(this).attr('data-apsa-status') == filter_by) {
                    $(this).removeClass('apsa-campaign-hidden');
                } else {
                    $(this).addClass('apsa-campaign-hidden');
                }
            });
        }
    });

    /**
     * campaign actions functionality
     */
    $(document).on('click', '.apsa-campaign-actions li', function (e) {
        var campaign_action = $(this).attr('data-apsa-action');
        var campaign_type = $(this).closest(".apsa-campaign-block").attr("data-apsa-camp-type");

        if (campaign_action == "export") {
            return false;
        }

        if (!$(this).hasClass("apsa-confirmated") && campaign_action == "delete") {
            apsa_confirm_click_message($(this), apsa_admin_labels["confirm_del_camp"], apsa_admin_labels["confirm_del_camp_title"]);
            return false;
        }

        var campaign_id = $(this).closest('.apsa-campaign-block').attr('data-apsa-campaign-id');
        var this_button = $(this);

        /** Ajax request for update campaign status or delete */
        $.ajax({
            type: "POST",
            url: apsa_ajax_url,
            dataType: "json",
            data: {
                action: "apsa_ajax_update_campaign_status",
                campaign_id: campaign_id,
                campaign_action: campaign_action,
                campaign_type: campaign_type,
            },
            success: function (response) {
                if (response['success'] != 1) {
                    apsa_action_message("error", apsa_admin_labels["camp_action_err_msg"]);
                    return false;
                }

                if (campaign_action == "delete") {
                    // Get deleted campaign status
                    var campaign_status = $("#apsa-campaign-block-" + campaign_id).attr("data-apsa-status");
                    // Reduce current type status count
                    var count = $("[data-apsa-filter='" + campaign_status + "']").find(".apsa-status-count").text();
                    count--;
                    $("[data-apsa-filter='" + campaign_status + "']").find(".apsa-status-count").text(count);
                    // Reduce all campaigns count
                    var count = $("[data-apsa-filter='all']").find(".apsa-status-count").text();
                    count--;
                    $("[data-apsa-filter='all']").find(".apsa-status-count").text(count);
                    $("#apsa-campaign-block-" + campaign_id).remove();
                    if ($(".apsa-campaign-block").length == 0) {
                        $("#apsa-export-all").addClass("apsa-all-action-hidden");
                        $("#apsa-update-all").addClass("apsa-all-action-hidden");
                    }

                    apsa_action_message("success", apsa_admin_labels["camp_deleted_msg"]);
                } else {
                    var status_name;
                    $("#apsa-campaign-block-" + campaign_id).attr("data-apsa-status", campaign_action);
                    if (campaign_action == "active") {

                        this_button.find(".apsa-action-name").removeClass("apsa-dash-activate").addClass("apsa-dash-suspend");
                        this_button.find('.apsa-action-name').text(apsa_admin_labels["suspend_camp"]);
                        this_button.attr("data-apsa-action", "suspended");
                        var count = $("[data-apsa-filter='active']").find(".apsa-status-count").text();
                        count++;
                        $("[data-apsa-filter='active']").find(".apsa-status-count").text(count);
                        var count = $("[data-apsa-filter='suspended']").find(".apsa-status-count").text();
                        count--;
                        $("[data-apsa-filter='suspended']").find(".apsa-status-count").text(count);
                        status_name = apsa_admin_labels["status_active"].toLowerCase();
                        apsa_action_message("success", apsa_admin_labels["camp_active_msg"]);
                    } else if (campaign_action == "suspended") {

                        this_button.find(".apsa-action-name").removeClass("apsa-dash-suspend").addClass("apsa-dash-activate");
                        this_button.find('.apsa-action-name').text(apsa_admin_labels["activate_camp"]);
                        this_button.attr("data-apsa-action", "active");
                        var count = $("[data-apsa-filter='suspended']").find(".apsa-status-count").text();
                        count++;
                        $("[data-apsa-filter='suspended']").find(".apsa-status-count").text(count);
                        var count = $("[data-apsa-filter='active']").find(".apsa-status-count").text();
                        count--;
                        $("[data-apsa-filter='active']").find(".apsa-status-count").text(count);
                        status_name = apsa_admin_labels["status_suspended"];
                        apsa_action_message("success", apsa_admin_labels["camp_suspend_msg"]);
                    }

                    /** Also change status on header */
                    this_button.closest(".apsa-campaign-block").find(".apsa-campaign-status").text(status_name);
                    this_button.closest(".apsa-campaign-block").find(".apsa-campaign-status").removeClass("apsa-camp-status-suspended apsa-camp-status-active").addClass("apsa-camp-status-" + campaign_action);
                    if ($(".apsa-selected-status").closest("li").attr("data-apsa-filter") !== "all") {
                        $("#apsa-campaign-block-" + campaign_id).addClass('apsa-campaign-hidden');
                    }
                }
            },
            error: function () {
                apsa_action_message("error", apsa_admin_labels["camp_action_err_msg"]);
            }
        });
        e.stopPropagation();
    });
    /**
     * element actions functionality
     */
    $(document).on('click', '.apsa-element-actions li', function () {

        var element_id = $(this).closest('.apsa-element-block').attr('data-apsa-element-id');
        var this_button = $(this);
        var elements_block = $(this).closest(".apsa-campaign-elements");
        if ($(this).attr("data-apsa-action") == "delete") {
            if (!$(this).hasClass("apsa-confirmated")) {
                apsa_confirm_click_message($(this), apsa_admin_labels["confirm_del_element"], apsa_admin_labels["confirm_del_element_title"]);
                return false;
            }

            /** Ajax request for delete element */
            $.ajax({
                type: "POST",
                url: apsa_ajax_url,
                dataType: "json",
                data: {
                    action: "apsa_ajax_delete_element",
                    element_id: element_id,
                },
                success: function (response) {
                    if (response['success'] != 1) {
                        apsa_action_message('error', apsa_admin_labels["element_del_err_msg"]);
                        return false;
                    }

                    var element_status = $("#apsa-element-block-" + element_id).attr("data-apsa-status");
                    var count = this_button.closest(".apsa-campaign-block").find("[data-apsa-element-filter='" + element_status + "']").find(".apsa-status-count").text();
                    count--;
                    this_button.closest(".apsa-campaign-block").find("[data-apsa-element-filter='" + element_status + "']").find(".apsa-status-count").text(count);
                    // Reduce all elements count
                    var count = this_button.closest(".apsa-campaign-block").find("[data-apsa-element-filter='all']").find(".apsa-status-count").text();
                    count--;
                    this_button.closest(".apsa-campaign-block").find("[data-apsa-element-filter='all']").find(".apsa-status-count").text(count);
                    var elements_count = this_button.closest(".apsa-campaign-block").find('.apsa-element-block').length;
                    elements_count--;
                    this_button.closest(".apsa-campaign-block").find('.apsa-elements-count div:first-child').text(elements_count);
                    $("[data-apsa-element-id='" + element_id + "']").remove();
                    apsa_refresh_elements_priority(elements_block);
                    apsa_action_message("success", apsa_admin_labels["element_del_msg"]);
                },
                error: function () {
                    apsa_action_message('error', apsa_admin_labels["element_del_err_msg"]);
                }
            });
        } else {
            var status;
            if ($(this).attr("data-apsa-action") == "activate") {
                status = "active";
            } else if ($(this).attr("data-apsa-action") == "suspend") {
                status = "suspended";
            }

            /** Ajax request for update element */
            $.ajax({
                type: "POST",
                url: apsa_ajax_url,
                dataType: "json",
                data: {
                    action: "apsa_ajax_update_element_status",
                    element_id: element_id,
                    status: status
                },
                success: function (response) {
                    if (response['success'] != 1) {
                        apsa_action_message('error', apsa_admin_labels["element_action_err_msg"]);
                        return false;
                    }

                    if (this_button.attr("data-apsa-action") == "activate") {
                        this_button.closest('.apsa-element-block').attr("data-apsa-status", "active");
                        this_button.attr("data-apsa-action", "suspend");
                        this_button.attr("title", apsa_admin_labels["suspend_element"]);
                        this_button.find(".apsa-action-name").removeClass("apsa-dash-activate").addClass("apsa-dash-suspend");
                        this_button.closest(".apsa-element-controls").find(".apsa-element-status").text(apsa_admin_labels["status_active"].toLowerCase());
                        this_button.closest(".apsa-element-controls").find(".apsa-element-status").removeClass("apsa-element-status-suspended").addClass("apsa-element-status-active");
                        var count = this_button.closest(".apsa-campaign-block").find("[data-apsa-element-filter='active']").find(".apsa-status-count").text();
                        count++;
                        this_button.closest(".apsa-campaign-block").find("[data-apsa-element-filter='active']").find(".apsa-status-count").text(count);
                        var count = this_button.closest(".apsa-campaign-block").find("[data-apsa-element-filter='suspended']").find(".apsa-status-count").text();
                        count--;
                        this_button.closest(".apsa-campaign-block").find("[data-apsa-element-filter='suspended']").find(".apsa-status-count").text(count);
                        apsa_action_message('success', apsa_admin_labels["element_active_msg"]);
                    } else if (this_button.attr("data-apsa-action") == "suspend") {
                        this_button.closest('.apsa-element-block').attr("data-apsa-status", "suspended");
                        this_button.attr("data-apsa-action", "activate");
                        this_button.attr("title", apsa_admin_labels["activate_element"]);
                        this_button.find(".apsa-action-name").removeClass("apsa-dash-suspend").addClass("apsa-dash-activate");
                        this_button.closest(".apsa-element-controls").find(".apsa-element-status").text(apsa_admin_labels["status_suspended"]);
                        this_button.closest(".apsa-element-controls").find(".apsa-element-status").removeClass("apsa-element-status-active").addClass("apsa-element-status-suspended");
                        var count = this_button.closest(".apsa-campaign-block").find("[data-apsa-element-filter='suspended']").find(".apsa-status-count").text();
                        count++;
                        this_button.closest(".apsa-campaign-block").find("[data-apsa-element-filter='suspended']").find(".apsa-status-count").text(count);
                        var count = this_button.closest(".apsa-campaign-block").find("[data-apsa-element-filter='active']").find(".apsa-status-count").text();
                        count--;
                        this_button.closest(".apsa-campaign-block").find("[data-apsa-element-filter='active']").find(".apsa-status-count").text(count);
                        apsa_action_message('success', apsa_admin_labels["element_suspend_msg"]);
                    }
                },
                error: function () {
                    apsa_action_message('error', apsa_admin_labels["element_action_err_msg"]);
                }
            });
        }
    });
    /*
     * Show a prompt before the user leaves the current page
     */
    $(".apsa-sortable").on("sortstop", function () {
        apsa_detect_change($(this));
    });
    $(window).load(function () {
        $(document).on('change', 'input, textarea, select', function () {
            apsa_detect_change($(this));
        });
    });
    $(document).on('click', '.apsa-camp-stat-opener', function () {
        apsa_create_camp_chart($(this).parent('div').find(".apsa-campaign-stat"));
        $(this).removeClass('apsa-camp-stat-opener');
    });
    $(document).on('click', '.apsa-element-stat-opener', function () {
        apsa_create_element_chart($(this).parent('div').find(".apsa-stat-container"));
        $(this).removeClass('apsa-element-stat-opener');
    });
    /** Add datpickers */
    apsa_charts_datepicker();
    /** Get element statistics by days */
    $(document).on("click", ".apsa-get-statistics", function () {
        var container = $(this).closest(".apsa-stat-cont").find(".apsa-stat-block");
        var from = jQuery.datepicker.formatDate("yy-mm-dd", $(this).closest(".apsa-stat-filter").find(".apsa-stat-from").datepicker('getDate'));
        var to = jQuery.datepicker.formatDate("yy-mm-dd", $(this).closest(".apsa-stat-filter").find(".apsa-stat-to").datepicker('getDate'));
        container.empty();
        if ($(this).closest('.apsa-stat-cont').hasClass('apsa-campaign-stat-cont')) {
            apsa_create_camp_chart(container, from, to);
        } else if ($(this).closest('.apsa-stat-cont').hasClass('apsa-element-stat-cont')) {
            apsa_create_element_chart(container, from, to);
        }
    });
    // Set deadlines datepicker
    apsa_set_datepicker(".apsa-deadline-date");
    // Set schedules datepicker
    apsa_set_datepicker(".apsa-schedule-date");
    $('.apsa-colorpicker').wpColorPicker({
        change: function () {
            apsa_detect_change($(this));
        }
    });
    // Click to campaign name stopPropagation
    $(document).on("click", ".apsa-campaign-name", function (e) {
        e.stopPropagation();
    });
    /** Always kepp element name on stat chart */
    $(document).on("keyup", ".apsa-element-name", function () {
        var element_name = $(this).val();
        $(this).closest(".apsa-element-block").find(".highcharts-subtitle").text(element_name);
        $(this).closest(".apsa-element-block").find(".apsa-stat-container").attr("data-apsa-element-title", element_name);
    });
    /** Update statistics chart when updated element name, for refresh stat subtitle */
    $(document).on("change", ".apsa-element-name", function () {
        $(this).closest(".apsa-element-block").find(".apsa-get-statistics").trigger("click");
    });
    /** Always kepp campaign name on stat chart */
    $(document).on("keyup", ".apsa-campaign-name", function () {
        var campaign_name = $(this).val();
        $(this).closest(".apsa-campaign-block").find('.apsa-campaign-stat').find(".highcharts-subtitle").text(campaign_name);
        $(this).closest(".apsa-campaign-block").find('.apsa-campaign-stat').attr("data-apsa-camp-title", campaign_name);
    });
    /** Update statistics chart when updated element name, for refresh stat subtitle */
    $(document).on("change", ".apsa-campaign-name", function () {
        $(this).closest(".apsa-campaign-block").find('.apsa-campaign-stat-cont').find(".apsa-get-statistics").trigger("click");
    });
    /** create canvas function from highcharts */
    (function (H) {
        H.Chart.prototype.createCanvas = function (range) {
            var title = this.title.textStr + '<br /><span style="font-size:12px;">' + range + '</span>';
            var svg = this.getSVG({chart: {width: Math.ceil(3035 / 3), height: Math.ceil(2008 / 3)}, title: {text: title}});
            canvas = document.createElement('canvas');
            canvas.width = Math.ceil(3035 / 2);
            canvas.height = Math.ceil(2008 / 2);
            if (canvas.getContext && canvas.getContext('2d')) {

                canvg(canvas, svg, {
                    ignoreDimensions: true,
                    scaleWidth: canvas.width,
                    scaleHeight: canvas.height
                });
                return canvas.toDataURL("image/jpeg");
            } else {
                apsa_action_message('error', apsa_admin_labels["general_err_msg"]);
                return false;
            }

        }
    }(Highcharts));
    /**
     * Export camp elements stats or all camp elements stats
     */
    $(document).on("click", ".apsa-export-range", function () {

        if (ie_version && ie_version <= 9) {
            apsa_action_message("info", apsa_admin_labels["update_browser"]);
            return false;
        }

        var camp_id = "";
        if ($(this).hasClass("apsa-export-camp-elements")) {
            camp_id = $(this).closest('.apsa-campaign-block').data('apsa-campaign-id');
        } else if ($(this).hasClass("apsa-export-all")) {
            if ($(".apsa-campaign-block").length == 0) {
                apsa_action_message("info", apsa_admin_labels["no_camp_stat_msg"]);
                return false;
            }
            camp_id = "";
        }

        $("#apsa-export-period-stats").attr("data-apsa-campaign-id", camp_id);
        /** Display popup */
        $('#apsa-export-range').find("input").val("");
        $('#apsa-managing-overlay').fadeIn(150);
        $('#apsa-export-range').attr('data-apsa-open', "true");
        jQuery('body').addClass('modal-open');
    });
    $(document).on("click", "#apsa-export-period-stats", function () {

        var camp_id = $(this).attr('data-apsa-campaign-id');
        var from_date = $("#apsa-export-range").find(".apsa-stat-from").val();
        var to_date = $("#apsa-export-range").find(".apsa-stat-to").val();
        var from = jQuery.datepicker.formatDate("yy-mm-dd", $("#apsa-export-range").find(".apsa-stat-from").datepicker('getDate'));
        var to = jQuery.datepicker.formatDate("yy-mm-dd", $("#apsa-export-range").find(".apsa-stat-to").datepicker('getDate'));
        var that_button = $(this);
        // Display spinner
        $(this).closest(".apsa-waiting-wrapper").addClass('apsa-waiting-button');
        /** Ajax request for get campaign elements statistics */
        $.ajax({
            type: "POST",
            url: apsa_ajax_url,
            dataType: "json",
            async: apsa_is_safari() ? false : true,
            data: {
                action: "apsa_ajax_get_elements_stat",
                camp_id: camp_id,
                from: from,
                to: to
            },
            success: function (camps_elements_statistics) {
                if (camps_elements_statistics == "" || camps_elements_statistics == undefined) {
                    /** Hide popup */
                    $('#apsa-export-range').attr('data-apsa-open', "false");
                    jQuery('body').removeClass('modal-open');
                    $('#apsa-managing-overlay').fadeOut(150);
                    apsa_action_message("error", apsa_admin_labels["cant_get_stat_msg"]);
                    return false;
                }

                $.each(camps_elements_statistics, function (camp_id, camp_elements_statistics) {
                    /** Draw campaign stat */
                    var camp_stat = camp_elements_statistics['camp_stat'];
                    var camp_stat_cont = $(".apsa-stat-block[data-apsa-camp-id='" + camp_id + "']");
                    var camp_title = camp_stat_cont.attr("data-apsa-camp-title");
                    camp_stat_cont.closest(".apsa-stat-cont").find(".apsa-stat-filter").find('input[type="text"]').val('');
                    camp_stat_cont.closest(".apsa-stat-cont").find(".apsa-slide-opener").addClass("apsa-camp-stat-opener");
                    camp_stat_cont.closest(".apsa-stat-cont").find(".apsa-camp-stat-opener").attr("data-apsa-open-slide", 'false');
                    camp_stat_cont.closest(".apsa-stat-cont").find(".apsa-sliding-block").attr("data-apsa-open", 'false');
                    apsa_make_camp_chart(camp_stat_cont, camp_stat, camp_title);
                    /** Draw elements stats */
                    var elements_statistics = camp_elements_statistics["elements_stats"];
                    if (elements_statistics) {
                        $.each(elements_statistics, function (element_id, element_stat) {
                            var element_stat_cont = $(".apsa-stat-block[data-apsa-element-id='" + element_id + "']");
                            var element_title = element_stat_cont.attr("data-apsa-element-title");
                            element_stat_cont.closest(".apsa-stat-cont").find(".apsa-stat-filter").find('input[type="text"]').val('');
                            element_stat_cont.closest(".apsa-stat-cont").find(".apsa-slide-opener").addClass("apsa-element-stat-opener");
                            element_stat_cont.closest(".apsa-stat-cont").find(".apsa-camp-stat-opener").attr("data-apsa-open-slide", 'false');
                            element_stat_cont.closest(".apsa-stat-cont").find(".apsa-sliding-block").attr("data-apsa-open", 'false');
                            apsa_make_element_chart(element_stat_cont, element_stat, element_title);
                        });
                    }
                });
                /** Export charts */
                var doc = new jsPDF("l", "mm", "a4");
                var range_date = '';
                if (from_date != '' && to_date != '') {
                    range_date = '<span style="font-weight:bold;font-size:12px;">' + apsa_admin_labels["stat_from_place"] + ' </span><span style="font-size:12px;">' + from_date + '</span><span style="font-weight:bold;font-size:12px;"> ' + apsa_admin_labels["stat_to_place"] + ' </span> <span style="font-size:12px;">' + to_date + '</span>';
                } else if (from_date != '') {
                    range_date = '<span style="font-weight:bold;font-size:12px;">' + apsa_admin_labels["stat_from_place"] + ' </span><span style="font-size:12px;">' + from_date + '</span>';
                } else if (to_date != '') {
                    range_date = '<span style="font-weight:bold;font-size:12px;">' + apsa_admin_labels["stat_to_place"] + ' </span><span style="font-size:12px;">' + to_date + '</span>';
                }

                if (camp_id != "") {

                    var imageData = $('[data-apsa-campaign-id="' + camp_id + '"]').find(".apsa-campaign-stat").highcharts().createCanvas(range_date);
                    var stat_count = $('[data-apsa-campaign-id="' + camp_id + '"]').find(".apsa-stat-container").length;
                    doc.addImage(imageData, 'JPEG', 15, 15, 257, 170);
                    if (stat_count != 0) {
                        doc.addPage();
                    }

                    $('[data-apsa-campaign-id="' + camp_id + '"]').find(".apsa-stat-container").each(function (index) {

                        var imageData = $(this).highcharts().createCanvas(range_date);
                        doc.addImage(imageData, 'JPEG', 15, 15, 257, 170);
                        if (index + 1 < stat_count) {
                            doc.addPage();
                        }
                    });
                    //save with name
                    doc.save(apsa_plugin_data["plugin_data"]["name"].toLowerCase() + '-stats.pdf');
                } else if (camp_id == "") {

                    //loop through each chart
                    var camps_count = $(".apsa-campaign-block").length;
                    $.each($(".apsa-campaign-block"), function (index) {
                        var imageData = $(this).closest($(this)).find(".apsa-campaign-stat").highcharts().createCanvas(range_date);
                        var stat_count = $(this).find(".apsa-stat-container").length;
                        doc.addImage(imageData, 'JPEG', 15, 15, 257, 170);
                        if (index + 1 < camps_count || stat_count != 0) {
                            doc.addPage();
                        }

                        $(this).find(".apsa-stat-container").each(function (jindex) {

                            var imageData = $(this).highcharts().createCanvas(range_date);
                            doc.addImage(imageData, 'JPEG', 15, 15, 257, 170);
                            if (index + 1 < camps_count || jindex + 1 < stat_count) {
                                doc.addPage();
                            }
                        });
                    });
                    //save with name
                    doc.save(apsa_plugin_data["plugin_data"]["name"].toLowerCase() + '-stats.pdf');
                }
            },
            error: function () {
                apsa_action_message("error", apsa_admin_labels["cant_get_stat_msg"]);
            },
            complete: function () {
                // hide spinner
                that_button.closest(".apsa-waiting-wrapper").removeClass('apsa-waiting-button');
                /** Hide popup */
                $('#apsa-export-range').attr('data-apsa-open', "false");
                jQuery('body').removeClass('modal-open');
                $('#apsa-managing-overlay').fadeOut(150);
            }
        });
    });
    // Open campaign when clicked on campaign name
    $(document).on("click", ".apsa-campaign-name", function () {
        if ($(this).closest(".apsa-slide-opener").attr("data-apsa-open-slide") == "false") {
            $(this).closest(".apsa-slide-opener").trigger("click");
        }
    });
    $(document).on("focus", ".apsa-element-name, .apsa-campaign-name", function () {
        $(this).removeClass("apsa-suspended-input");
    }).on("blur", ".apsa-element-name, .apsa-campaign-name", function () {
        $(this).addClass("apsa-suspended-input");
    });
    /**
     * Filter elements by status
     */
    $(document).on('click', '.apsa-elements-filter li', function () {

        /** Change selected status */
        $(this).closest(".apsa-campaign-block").find('.apsa-element-status-name').removeClass('apsa-selected-status');
        $(this).find('.apsa-element-status-name').addClass('apsa-selected-status');
        var filter_by = $(this).attr('data-apsa-element-filter');
        if (filter_by == "all") {
            $(this).closest(".apsa-campaign-block").find('.apsa-element-block').removeClass('apsa-element-hidden');
        } else {
            $(this).closest(".apsa-campaign-block").find('.apsa-element-block').each(function () {
                if ($(this).attr('data-apsa-status') == filter_by) {
                    $(this).removeClass('apsa-element-hidden');
                } else {
                    $(this).addClass('apsa-element-hidden');
                }
            });
        }
    });

    /**
     * Import already setted tags
     */
    $(".apsa-tags").each(function () {

        var tag_values = $(this).find(".apsa-tags-values").data("apsa-tags-values");

        tag_values = tag_values.split("%" + apsa_plugin_data["plugin_data"]["name"].toLowerCase() + "%,");

        for (var i = 0; i < tag_values.length; i++) {
            var tag_value = tag_values[i];

            if (i == tag_values.length - 1) {
                tag_value = tag_value.substr(0, tag_value.indexOf('%' + apsa_plugin_data["plugin_data"]["name"].toLowerCase() + '%'));
            }

            if (tag_value != "") {

                var tag_text = tag_value.split("%").slice(2);
                var tag_type = tag_value.substr(0, tag_value.indexOf('%'));
                tag_value = tag_value + "%" + apsa_plugin_data["plugin_data"]["name"].toLowerCase() + "%";
                var apsa_value = tag_value.split("%").slice(1)[0];
                if (apsa_value == 'apsa_all_pages' || apsa_value == 'apsa_all_posts') {
                    tag_text = apsa_admin_labels[apsa_value]['loc'];
                }
                $(this).find(".typeahead-auto").tagsinput('add', {"value": tag_value, "text": tag_text, "type": tag_type});
            }
        }
    });
    /**
     * Export campaign statistics
     */
    $(document).on("click", ".apsa-export-single-stat", function () {
        if (ie_version && ie_version <= 9) {
            apsa_action_message("info", apsa_admin_labels["update_browser"]);
            return false;
        }
        var container = $(this).closest('.apsa-stat-cont').find('.apsa-stat-block');
        var from = $.datepicker.formatDate("yy-mm-dd", $(this).closest(".apsa-stat-filter").find(".apsa-stat-from").datepicker('getDate'));
        var to = $.datepicker.formatDate("yy-mm-dd", $(this).closest(".apsa-stat-filter").find(".apsa-stat-to").datepicker('getDate'));
        if ($(this).closest('.apsa-export-single-stat').hasClass('apsa-export-camp-stat')) {
            apsa_create_camp_chart(container, from, to, true);
        } else if ($(this).closest('.apsa-export-single-stat').hasClass('apsa-export-element-stat')) {
            apsa_create_element_chart(container, from, to, true);
        }
    });
    /**
     * Save all campaigns with elements
     */
    $(document).on('click', '#apsa-update-all', function (e) {
        $('.apsa-deadline-date').each(function () {
            if ($(this).val() == '') {
                $(this).next('[type="hidden"]').val('');
            }
        });
        $('.apsa-schedule-date').each(function () {
            if ($(this).val() == '') {
                $(this).next('[type="hidden"]').val('');
            }
        });
        // Display spinner
        $(this).closest(".apsa-waiting-wrapper").addClass('apsa-waiting-button');
        e.preventDefault();
        /** Check required fields */
        var is_error_exist = false;
        // validation
        is_error_exist = apsa_validation($("#apsa-campaigns-block"));
        // validation for child options
        if (typeof apsa_child_valid == 'function') {
            var is_child_error_exist = apsa_child_valid($("#apsa-campaigns-block"));
            if (is_child_error_exist)
                is_error_exist = true;
        }
        //-------------------------

        var all_camps = {};
        $(".apsa-save-campaign-options").each(function () {

            if ($(this).closest(".apsa-campaign-settings").find(".apsa-auto-placement").length != 0) {
                var auto_placement = "off";
                var auto_placement_before = $(this).closest(".apsa-campaign-settings").find(".apsa-placement-before").is(':checked');
                var auto_placement_after = $(this).closest(".apsa-campaign-settings").find(".apsa-placement-after").is(':checked');

                if (auto_placement_before == true && auto_placement_after == true) {
                    auto_placement = "both";
                } else if (auto_placement_before == true || auto_placement_after == true) {
                    auto_placement = ((auto_placement_before == true) ? "before" : ((auto_placement_after == true) ? "after" : "off"));
                }

                var before_align = $(this).closest(".apsa-campaign-settings").find(".apsa-before-align").val();
                var after_align = $(this).closest(".apsa-campaign-settings").find(".apsa-after-align").val();
            }

            var camp_include_tags = $(this).closest(".apsa-campaign-settings").find(".apsa_tag_include").find(".typeahead-auto").val();
            var camp_exclude_tags = $(this).closest(".apsa-campaign-settings").find(".apsa_tag_exclude").find(".typeahead-auto").val();
            var campaign_name = $(this).closest('.apsa-campaign-block').find(".apsa-campaign-name").val();
            var campaign_options = $(this).closest('.apsa-campaign-block').find('.apsa-campaign-options-form').serializeArray();
            // concat child campaign options
            if (typeof apsa_child_get_campaign_options == 'function') {
                var child_campaign_options = apsa_child_get_campaign_options($(this));
                campaign_options = campaign_options.concat(child_campaign_options);
            }

            if (typeof auto_placement != "undefined") {
                campaign_options.push({name: "auto_placement", value: auto_placement});
            }

            if (typeof before_align != "undefined") {
                campaign_options.push({name: "before_align", value: before_align});
            }

            if (typeof after_align != "undefined") {
                campaign_options.push({name: "after_align", value: after_align});
            }

            campaign_options.push({name: "campaign_name", value: campaign_name});
            campaign_options.push({name: "camp_include_tags", value: camp_include_tags});
            campaign_options.push({name: "camp_exclude_tags", value: camp_exclude_tags});
            var campaign_id = $(this).attr('apsa-data-campaign-id');
            all_camps[campaign_id] = {};
            all_camps[campaign_id]['campaign_options'] = campaign_options;
            /**
             * Save new and update old elements
             */
            var closest_campaign = $(this).closest('.apsa-campaign-block');
            var elements = new Array();
            closest_campaign.find('.apsa-element-form').each(function () {
                var element_data = {};
                var element_include_tags = $(this).find(".apsa_tag_include").find(".typeahead-auto").val();
                var element_exclude_tags = $(this).find(".apsa_tag_exclude").find(".typeahead-auto").val();
                var element_fr_options = $(this).find(".apsa-fr-option,.apsa-fr-option input").serializeArray();
                var element_info = {};
                var element_options = {};
                element_options["element_include_tags"] = element_include_tags;
                element_options["element_exclude_tags"] = element_exclude_tags;
                // check if element overdue
                var current_date = new Date();
                current_date.setHours(0, 0, 0, 0);
                var views = $(this).find('.apsa-fr-event-count div').first().text();
                var element_type = '';
                for (var i = 0; i < element_fr_options.length; i++) {
                    if (element_fr_options[i]['name'] == 'schedule') {
                        var ad_schedule = new Date(element_fr_options[i]['value']);
                        ad_schedule.setHours(0, 0, 0, 0);
                        if (current_date.getTime() < ad_schedule.getTime()) {
                            $(this).find('.apsa-schedule-date').addClass('apsa-overdue-element');
                            $(this).find('.apsa-schedule-date').closest('.apsa-form-item').find('.apsa-input-message').addClass('apsa-warning-message').text(apsa_admin_labels["schedule_warning_message"]);
                        } else {
                            $(this).find('.apsa-schedule-date').removeClass('apsa-overdue-element');
                            $(this).find('.apsa-schedule-date').closest('.apsa-form-item').find('.apsa-input-message').removeClass('apsa-warning-message').text($(this).find('.apsa-schedule-date').closest('.apsa-form-item').find('.apsa-input-message').data('apsa-default-message'));
                        }
                        element_options[element_fr_options[i]['name']] = element_fr_options[i]['value'];
                    }
                    if (element_fr_options[i]['name'] == 'deadline') {
                        var ad_deadline = new Date(element_fr_options[i]['value']);
                        ad_deadline.setHours(0, 0, 0, 0);
                        if (current_date.getTime() > ad_deadline.getTime()) {
                            $(this).find('.apsa-deadline-date').addClass('apsa-overdue-element');
                            $(this).find('.apsa-deadline-date').closest('.apsa-form-item').find('.apsa-input-message').addClass('apsa-warning-message').text(apsa_admin_labels["deadline_warning_message"]);
                        } else {
                            $(this).find('.apsa-deadline-date').removeClass('apsa-overdue-element');
                            $(this).find('.apsa-deadline-date').closest('.apsa-form-item').find('.apsa-input-message').removeClass('apsa-warning-message').text($(this).find('.apsa-deadline-date').closest('.apsa-form-item').find('.apsa-input-message').data('apsa-default-message'));
                        }
                        element_options[element_fr_options[i]['name']] = element_fr_options[i]['value'];
                    }
                    if (element_fr_options[i]['name'] == 'restrict_views') {
                        if (!$(this).find('[name="restrict_views"]').hasClass('apsa-error-input')) {
                            var restrict_views = element_fr_options[i]['value'];
                            if (parseInt(views) >= parseInt(restrict_views)) {
                                $(this).find('[name="restrict_views"]').addClass('apsa-overdue-element');
                                $(this).find('[name="restrict_views"]').closest('.apsa-form-item').find('.apsa-input-message').addClass('apsa-warning-message').text(apsa_admin_labels["views_warning_message"]);
                            } else {
                                $(this).find('[name="restrict_views"]').removeClass('apsa-overdue-element');
                                $(this).find('[name="restrict_views"]').closest('.apsa-form-item').find('.apsa-input-message').removeClass('apsa-warning-message').text($(this).find('[name="restrict_views"]').closest('.apsa-form-item').find('.apsa-input-message').data('apsa-default-message'));
                            }
                            element_options[element_fr_options[i]['name']] = element_fr_options[i]['value'];
                        }
                    }
                    if (element_fr_options[i]['name'] == 'type') {
                        element_type = element_fr_options[i]['value'];
                        element_info[element_fr_options[i]['name']] = element_fr_options[i]['value'];
                    }
                    if (element_fr_options[i]['name'] == 'action') {
                        element_info[element_fr_options[i]['name']] = element_fr_options[i]['value'];
                    }
                    if (element_fr_options[i]['name'] == 'campaign_id') {
                        element_info[element_fr_options[i]['name']] = element_fr_options[i]['value'];
                    }
                    if (element_fr_options[i]['name'] == 'priority') {
                        element_info[element_fr_options[i]['name']] = element_fr_options[i]['value'];
                    }
                    if (element_fr_options[i]['name'] == 'element_id') {
                        element_info[element_fr_options[i]['name']] = element_fr_options[i]['value'];
                    }
                    if (element_fr_options[i]['name'] == 'title') {
                        element_info[element_fr_options[i]['name']] = element_fr_options[i]['value'];
                    }
                }

                var apsa_child_element_data = apsa_get_child_element_options(this, element_type);
                $.extend(element_options, apsa_child_element_data, element_options);
                element_data['element_options'] = element_options;
                element_data['element_info'] = element_info;
                var apsa_data = element_data;
                elements.push(apsa_data);
            });
            all_camps[campaign_id]['campaign_elements'] = elements;
            e.stopPropagation();
        });
        if (is_error_exist == true) {
            $(this).closest(".apsa-waiting-wrapper").removeClass('apsa-waiting-button');
            apsa_action_message("error", apsa_admin_labels["check_form_req_msg"]);
            return false;
        }

        /** Ajax request for update all campaigns */
        $.ajax({
            type: "POST",
            url: apsa_ajax_url,
            dataType: "json",
            data: {
                action: "apsa_ajax_update_all_camps",
                all_camps: all_camps,
            },
            success: function (response) {
                if (response['success'] == 1) {
                    apsa_action_message("success", apsa_admin_labels["camps_updated_msg"]);
                    apsa_remove_detect_change();
                } else {
                    apsa_action_message("error", apsa_admin_labels["update_failed_msg"]);
                }
            },
            error: function () {
                apsa_action_message("error", apsa_admin_labels["update_failed_msg"]);
            },
            complete: function () {
                // Remove spinner
                $("#apsa-update-all").closest(".apsa-waiting-wrapper").removeClass('apsa-waiting-button');
            }
        });
    });
    /**
     * close anticache notice
     */
    $(document).on("click", ".apsa-dismissible", function () {
        $(this).parent('div').hide();
        apsa_set_cookie('apsa_anticache_notice', 'no');
    });
    // desable apsa-new class
    if (typeof apsa_new == 'undefined') {
        $('.apsa-new').each(function () {
            $(this).addClass('apsa-new-show');
        });
    }

    // for embed alignment
    $(document).on('click', '.apsa-embeding-alignment ul li', function () {
        var alignment = $(this).data('apsa-embed-alignment');
        var plugin_name = $(this).closest('.apsa-embed-alignment').data('apsa-plugin-name');
        var camp_id = $(this).closest('.apsa-embed-alignment').data('apsa-camp-id');
        var shortcode = '';
        if (alignment == 'none') {
            shortcode = '[' + plugin_name + ' id="' + camp_id + '"]';
        } else {
            shortcode = '[' + plugin_name + ' id="' + camp_id + '" align="' + alignment + '"]';
        }
        $('.apsa-click-select').text(shortcode);
    });

    // Broken link detection
    apsa_broken_link_detector();
    // function for change detected
    function apsa_detect_change(that) {
        apsa_leave_page(false);
        that.closest(".apsa-campaign-block").attr("data-apsa-camp-changed", "1");
        // added * after save buttons value
        var save_val = that.closest(".apsa-campaign-block").find('.apsa-save-campaign-options').val() ? that.closest(".apsa-campaign-block").find('.apsa-save-campaign-options').val() : '';
        if (save_val.indexOf("*") < 0) {
            that.closest(".apsa-campaign-block").find('.apsa-save-campaign-options').val(save_val + ' *');
        }
        var save_all_val = $('#apsa-update-all').text();
        if (save_all_val.indexOf("*") < 0) {
            $('#apsa-update-all').text(save_all_val + ' *');
        }
    }

    // function for remove change detected
    function apsa_remove_detect_change(that_button) {
        if (that_button) {
            that_button.closest(".apsa-campaign-block").attr("data-apsa-camp-changed", "0");
            that_button.closest(".apsa-waiting-wrapper").removeClass('apsa-waiting-button');
            // remove * from save buttons value
            var save_val = that_button.val().replace(" *", "");
            that_button.val(save_val);
            var can_change_update_all = true;
            that_button.closest("#apsa-campaigns-block").find('.apsa-campaign-block').each(function () {
                if ($(this).attr("data-apsa-camp-changed") == 1) {
                    can_change_update_all = false;
                }
            });
            if (can_change_update_all) {
                apsa_leave_page(true);
                var save_all_val = $('#apsa-update-all').text().replace(" *", "");
                $('#apsa-update-all').text(save_all_val);
            }
        } else {
            apsa_leave_page(true);
            $(".apsa-campaign-block").attr("data-apsa-camp-changed", "0");
            // remove * from save buttons value
            $('#apsa-campaigns-block').find('.apsa-save-campaign-options').each(function () {
                var save_val = $(this).val().replace(" *", "");
                $(this).val(save_val);
            });
            var save_all_val = $('#apsa-update-all').text().replace(" *", "");
            $('#apsa-update-all').text(save_all_val);
        }
    }

    //prevent form submition on enter key
    $(document).on('submit', '#apsa-managing-wrap form', function (e) {
        e.preventDefault();
        return false;
    });
    $(document).on('keydown', '#apsa-managing-wrap .iris-square-value', function (e) {
        e.preventDefault();
    });
});