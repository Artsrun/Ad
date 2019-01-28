// validation element options
function apsa_child_valid(scope) {
    // Check video urls
    var is_error_exist = false;
    scope.find('.apsa-video-url').each(function () {
        if (jQuery(this).val() !== "") {
            var parsed_url = urlParser.parse(jQuery(this).val());
            if (parsed_url == undefined || (parsed_url['provider'] != 'youtube' && parsed_url['provider'] != 'vimeo')) {
                jQuery(this).addClass('apsa-error-input');
                jQuery(this).closest('.apsa-form-item').find('.apsa-input-message').addClass('apsa-error-valid');
                jQuery(this).closest('.apsa-form-item').find('.apsa-input-message').text(apsa_admin_labels["valid_url_error"]);
                is_error_exist = true;
            } else {
                jQuery(this).removeClass('apsa-error-input');
                jQuery(this).closest('.apsa-form-item').find('.apsa-input-message').removeClass('apsa-error-valid');
                jQuery(this).closest('.apsa-form-item').find('.apsa-input-message').text(jQuery(this).closest('.apsa-form-item').find('.apsa-input-message').attr("data-apsa-default-message"));
            }
        } else {
            if (!jQuery(this).hasClass("apsa-required")) {
                jQuery(this).removeClass('apsa-error-input');
                jQuery(this).closest('.apsa-form-item').find('.apsa-input-message').removeClass('apsa-error-valid');
                jQuery(this).closest('.apsa-form-item').find('.apsa-input-message').text(jQuery(this).closest('.apsa-form-item').find('.apsa-input-message').attr("data-apsa-default-message"));
            }
        }
    });
    return is_error_exist;
}

// get element options
function apsa_get_child_element_options(that, type) {
    var apsa_child_data = {};
    var ad_data = jQuery(that).find('.apsa-child-option input,.apsa-child-option select,.apsa-child-option textarea').serializeArray();

    // check if ad overdue
    var visits = jQuery(that).find('.apsa-child-event-count div').first().text();
    for (var i = 0; i < ad_data.length; i++) {
        if (type == 'video' && ad_data[i]['name'] == 'element_content' && ad_data[i]['value'] != '') {
            var origin_url = ad_data[i]['value'];
            var url_data = new Object;
            url_data['parsed'] = urlParser.parse(origin_url);
            url_data['origin_url'] = origin_url;
            ad_data[i]['value'] = JSON.stringify(url_data)
        }
        if (ad_data[i]['name'] == 'restrict_visits') {
            if (!jQuery(that).find('[name="restrict_visits"]').hasClass('apsa-error-input')) {
                var restrict_visits = ad_data[i]['value'];
                if (parseInt(visits) >= parseInt(restrict_visits)) {
                    jQuery(that).find('[name="restrict_visits"]').addClass('apsa-overdue-element');
                    jQuery(that).find('[name="restrict_visits"]').closest('.apsa-form-item').find('.apsa-input-message').addClass('apsa-warning-message').text(apsa_admin_labels["visits_warning_message"]);
                } else {
                    jQuery(that).find('[name="restrict_visits"]').removeClass('apsa-overdue-element');
                    jQuery(that).find('[name="restrict_visits"]').closest('.apsa-form-item').find('.apsa-input-message').removeClass('apsa-warning-message').text(jQuery(that).find('[name="restrict_visits"]').closest('.apsa-form-item').find('.apsa-input-message').data('apsa-default-message'));
                }
            }
        }
        apsa_child_data[ad_data[i]['name']] = ad_data[i]['value'];
    }
    return apsa_child_data;
}

// create element options block html
function apsa_create_child_element_option_block(ad_type, campaign_type, apsa_admin_labels, apsa_patterns) {
    var overlay_patterns = '';
    jQuery.each(apsa_patterns, function (key, value) {
        overlay_patterns += '<option value="' + key + '">' + key + '</option>';
    });
    var ad_block_file = '';
    if (ad_type == "image") {
        ad_block_file = '<li class="apsa-form-item apsa-element-large-input apsa-child-option">\n\
                                <span>' + apsa_admin_labels["image_file"] + '</span>\n\
                                <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message="' + apsa_admin_labels["image_file_desc"] + '"></span>\n\
                                <div class="apsa-input-block">\n\
                                <div class="apsa-upload-wrap">\n\
                                <button type="button" class="apsa-upload-file button" data-apsa-file-type="image">' + apsa_admin_labels["choose_img_file"] + '</button>\n\
                                </div>\n\
                                <div class="apsa-upload-input-wrap">\n\
                                <input type="text" class="apsa-hold-element-content" name="element_content" value="" />\n\
                                </div>\n\
                                </div>\n\
                                <span class="apsa-input-message"></span>\n\
                            </li>';
    } else if (ad_type == "flash") {
        ad_block_file = '<li class="apsa-form-item apsa-element-large-input apsa-child-option">\n\
                                <span>' + apsa_admin_labels["swf_file"] + '</span>\n\
                                <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message="' + apsa_admin_labels["swf_file_desc"] + '"></span>\n\
                                <div class="apsa-input-block">\n\
                                <div class="apsa-upload-wrap">\n\
                                <button type="button" class="apsa-upload-file button" data-apsa-file-type="application">' + apsa_admin_labels["choose_swf_file"] + '</button>\n\
                                </div>\n\
                                <div class="apsa-upload-input-wrap">\n\
                                <input type="text" class="apsa-hold-element-content" name="element_content" value="" />\n\
                                </div>\n\
                                </div>\n\
                                <span class="apsa-input-message"></span>\n\
                            </li>';
    } else if (ad_type == "video") {
        ad_block_file = '<li class="apsa-form-item apsa-element-large-input apsa-child-option">\n\
                                <span>' + apsa_admin_labels["video_url"] + '</span>\n\
                                <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message="' + apsa_admin_labels["video_url_desc"] + '"></span>\n\
                                <div class="apsa-input-block">\n\
                                <input type="text" class="apsa-hold-element-content apsa-video-url" name="element_content" value="" />\n\
                                </div>\n\
                                <span class="apsa-input-message"></span>\n\
                            </li>';
    } else if (ad_type == "iframe") {
        ad_block_file = '<li class="apsa-form-item apsa-element-large-input apsa-child-option">\n\
                                <span>' + apsa_admin_labels["source_url"] + '</span>\n\
                                <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message="' + apsa_admin_labels["source_url_desc"] + '"></span>\n\
                                <div class="apsa-input-block">\n\
                                <input type="text" class="apsa-link apsa-broken-link apsa-hold-element-content" name="element_content" value="" />\n\
                                </div>\n\
                                <span class="apsa-input-message"></span>\n\
                            </li>';
    } else if (ad_type == "code") {
        ad_block_file = '<li class="apsa-form-item apsa-element-large-input apsa-child-option">\n\
                                <span>' + apsa_admin_labels["html_code"] + '</span>\n\
                                <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message="' + apsa_admin_labels["html_code_desc"] + '"></span>\n\
                                <div class="apsa-input-block apsa-code-button">\n\
                                <input type="button" class="button apsa-type-code" value="' + apsa_admin_labels["type_code"] + '" />\n\
                                <textarea class="apsa-hold-element-content apsa-hold-code-content apsa-hidden-textarea" name="element_content"></textarea>\n\
                                </div>\n\
                                <span class="apsa-input-message"></span>\n\
                            </li>';
    }else if (ad_type == "custom") {//===========Custom=====
        var forms = apsa_all_forms;
        ad_block_file = '<li class="apsa-form-item apsa-element-usual-input apsa-child-option"><span>' + apsa_admin_labels["custom_ad"] + '</span>\n\
                        <span class="apsa-with-question" title="' + (apsa_admin_labels["detailed_description"]) + '" data-apsa-message="' + apsa_admin_labels["choose_custom_ads_desc"] + '"></span>\n\
                         <div class="apsa-input-block">\n\
                         <select class="apsa-hold-element-content" name="element_content">\n\
                         <option value="">' + apsa_admin_labels["alignment_none"] + '</option>';
        if (forms.length !== 0) {
            jQuery.each(forms, function (index, value) {
                ad_block_file += '<option value="' + value.id + '">' + (value['title']=="" ? "(" +apsa_admin_labels["no_title"]+")":value['title'])  + '</option>'
            });
        }
        ad_block_file += '</select> </div> </li>';
    }

    var ad_block_html = '<li class="apsa-form-item apsa-element-usual-input apsa-child-option">\n\
                                    <span>' + apsa_admin_labels["max_clicks"] + '</span>\n\
                                    <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message="' + apsa_admin_labels["max_clicks_desc"] + '"></span>\n\
                                    <div class="apsa-input-block">\n\
                                    <input type="text" class="apsa-positive-int" name="restrict_visits" />\n\
                                    </div>\n\
                                    <span class="apsa-input-message" data-apsa-default-message="' + apsa_admin_labels['default'] + ' ' + apsa_admin_labels['max_visits_def'] + '">' + apsa_admin_labels["default"] + ' ' + apsa_admin_labels['max_visits_def'] + '</span>\n\
                                </li>\n\
                                <li class="apsa-form-item apsa-element-extra-large-input apsa-child-option">\n\
                                    <span>' + apsa_admin_labels["link_to"] + '</span>\n\
                                    <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message="' + apsa_admin_labels["link_to_desc"] + '"></span>\n\
                                    <div class="apsa-input-block">\n\
                                    <input type="text" class="apsa-link apsa-broken-link" name="link_to" />\n\
                                    </div>\n\
                                    <span class="apsa-input-message"></span>\n\
                                </li>' + ad_block_file;

    if (campaign_type == 'background') {
        if (ad_type == 'image') {
            ad_block_html += '<li class="apsa-form-item apsa-element-option-select apsa-child-option">\n\
                                        <span>' + apsa_admin_labels["bg_img_type"] + '</span>\n\
                                        <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message="' + apsa_admin_labels["bg_img_type_desc"] + '"></span>\n\
                                        <div>\n\
                                            <select name="background_type">\n\
                                                <option value="cover_bg">' + apsa_admin_labels["cover"] + '</option>\n\
                                                <option value="repeat_bg">' + apsa_admin_labels["repeat"] + '</option>\n\
                                                <option value="cover_bg_parallax">' + apsa_admin_labels["cover"] + ' (Parallax)</option>\n\
                                                <option value="repeat_bg_parallax">' + apsa_admin_labels["repeat"] + ' (Parallax)</option>\n\
                                            </select>\n\
                                        </div>\n\
                                    </li>';

        }
    }
    if (campaign_type == 'popup') {
        if (ad_type == 'image') {
            ad_block_html += '<li class="apsa-form-item apsa-element-option-select apsa-child-option">\n\
                                    <span>' + apsa_admin_labels["bg_img_type"] + '</span>\n\
                                    <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message="' + apsa_admin_labels["bg_img_type_desc"] + '"></span>\n\
                                    <div>\n\
                                        <select name="background_type">\n\
                                            <option value="contain">' + apsa_admin_labels["contain"] + '</option>\n\
                                            <option value="cover">' + apsa_admin_labels["cover"] + '</option>\n\
                                        </select>\n\
                                    </div>\n\
                                </li>';
        }
        if (ad_type == 'video') {
            ad_block_html += '<li class="apsa-form-item apsa-element-option-select apsa-child-option">\n\
                                    <span>' + apsa_admin_labels["auto_play_video"] + '</span>\n\
                                    <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message="' + apsa_admin_labels["auto_play_video_desc"] + '"></span>\n\
                                    <div>\n\
                                        <input type="checkbox" />\n\
                                        <input type="hidden" class="apsa-hold-checkbox" name="auto_play_video">\n\
                                    </div>\n\
                                </li>';
        }
    }
    if (campaign_type == 'embed') {
        if (ad_type == 'image') {
            ad_block_html += '<li class="apsa-form-item apsa-element-option-select apsa-child-option">\n\
                                        <span>' + apsa_admin_labels["bg_img_type"] + '</span>\n\
                                        <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message="' + apsa_admin_labels["bg_img_type_desc"] + '"></span>\n\
                                        <div>\n\
                                            <select name="background_type">\n\
                                                <option value="contain">' + apsa_admin_labels["contain"] + '</option>\n\
                                                <option value="cover">' + apsa_admin_labels["cover"] + '</option>\n\
                                            </select>\n\
                                        </div>\n\
                                    </li>';
        }
        if (ad_type == 'video') {
            ad_block_html += '<li class="apsa-form-item apsa-element-option-select apsa-child-option">\n\
                                    <span>' + apsa_admin_labels["auto_play_video"] + '</span>\n\
                                    <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message="' + apsa_admin_labels["auto_play_video_desc"] + '"></span>\n\
                                    <div>\n\
                                        <input type="checkbox" />\n\
                                        <input type="hidden" class="apsa-hold-checkbox" name="auto_play_video">\n\
                                    </div>\n\
                                </li>';
        }
    }
    if (campaign_type == 'sticky') {
        if (ad_type == 'image') {
            ad_block_html += '<li class="apsa-form-item apsa-element-option-select apsa-child-option">\n\
                                    <span>' + apsa_admin_labels["bg_img_type"] + '</span>\n\
                                    <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message="' + apsa_admin_labels["bg_img_type_desc"] + '"></span>\n\
                                    <div>\n\
                                        <select name="background_type">\n\
                                            <option value="contain">' + apsa_admin_labels["contain"] + '</option>\n\
                                            <option value="cover">' + apsa_admin_labels["cover"] + '</option>\n\
                                        </select>\n\
                                    </div>\n\
                                </li>';
        }
        if (ad_type == 'video') {
            ad_block_html += '<li class="apsa-form-item apsa-element-option-select apsa-child-option">\n\
                                    <span>' + apsa_admin_labels["auto_play_video"] + '</span>\n\
                                    <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message="' + apsa_admin_labels["auto_play_video_desc"] + '"></span>\n\
                                    <div>\n\
                                        <input type="checkbox" />\n\
                                        <input type="hidden" class="apsa-hold-checkbox" name="auto_play_video">\n\
                                    </div>\n\
                                </li>';
        }
    }
    return ad_block_html;
}

// return html campaign options for child 
function apsa_child_create_campaign_options(campaign_type, apsa_admin_labels, apsa_patterns) {
    var campaign_options = '';
    if (campaign_type == 'background') {
        campaign_options = '<li class="apsa-form-item">\n\
                                            <span>' + apsa_admin_labels["background_pattern"] + '</span>\n\
                                            <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message="' + apsa_admin_labels["background_pattern_desc"] + '"></span>\n\
                                            <div class="apsa-input-block">\n\
                                            <select name="background_pattern"><option value="none">None</option>' + apsa_patterns + '</select>\n\
                                            </div>\n\
                                            <span class="apsa-input-message"></span>\n\
                                        </li>\n\
                                        <li class="apsa-form-item">\n\
                                            <span>' + apsa_admin_labels["open_link_type"] + '</span>\n\
                                            <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message="' + apsa_admin_labels["open_link_type_desc"] + '"></span>\n\
                                            <div class="apsa-input-block">\n\
                                                <select name="link_type">\n\
                                                    <option value="_blank">' + apsa_admin_labels["open_link_type_blank"] + '</option>\n\
                                                    <option value="_self">' + apsa_admin_labels["open_link_type_self"] + '</option>\n\
                                                    <option value="_window">' + apsa_admin_labels["open_link_type_window"] + '</option>\n\
                                                </select>\n\
                                            </div>\n\
                                        </li>';
    }
    if (campaign_type == 'popup') {
        campaign_options = '<li class="apsa-form-item">\n\
                                    <span>' + apsa_admin_labels["open_link_type"] + '</span>\n\
                                    <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message="' + apsa_admin_labels["open_link_type_desc"] + '"></span>\n\
                                    <div class="apsa-input-block">\n\
                                        <select name="link_type">\n\
                                            <option value="_blank">' + apsa_admin_labels["open_link_type_blank"] + '</option>\n\
                                            <option value="_self">' + apsa_admin_labels["open_link_type_self"] + '</option>\n\
                                            <option value="_window">' + apsa_admin_labels["open_link_type_window"] + '</option>\n\
                                        </select>\n\
                                    </div>\n\
                                </li>\n\
                                <li class="apsa-form-item">\n\
                                    <div class="apsa-input-block">\n\
                                    <span>' + apsa_admin_labels["show_link"] + '</span>\n\
                                    <input type="checkbox" class="apsa-show-link" checked="checked" />\n\
                                    <input type="hidden" class="apsa-hold-checkbox" name="show_link" value="on" />\n\
                                    <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message="' + apsa_admin_labels["show_link_desc"] + '"></span>\n\
                                    </div>\n\
                                </li>\n\
                                <li class="apsa-form-item">\n\
                                    <span>' + apsa_admin_labels["link_color"] + '</span>\n\
                                    <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message="' + apsa_admin_labels["link_color_desc"] + '"></span>\n\
                                    <div class="apsa-input-block">\n\
                                    <input type="text" name="link_color" class="apsa-extra-small-input apsa-colorpicker" data-default-color="#ffffff" value="#ffffff">\n\
                                    </div>\n\
                                    <span class="apsa-input-message">' + apsa_admin_labels["default"] + ' #ffffff</span>\n\
                                </li>';
    }
    if (campaign_type == 'embed') {
        campaign_options = '<li class="apsa-form-item">\n\
                                    <span>' + apsa_admin_labels["open_link_type"] + '</span>\n\
                                    <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message="' + apsa_admin_labels["open_link_type_desc"] + '"></span>\n\
                                    <div class="apsa-input-block">\n\
                                        <select name="link_type">\n\
                                            <option value="_blank">' + apsa_admin_labels["open_link_type_blank"] + '</option>\n\
                                            <option value="_self">' + apsa_admin_labels["open_link_type_self"] + '</option>\n\
                                            <option value="_window">' + apsa_admin_labels["open_link_type_window"] + '</option>\n\
                                        </select>\n\
                                    </div>\n\
                                </li>\n\
                                <li class="apsa-form-item">\n\
                                    <div class="apsa-input-block">\n\
                                    <span>' + apsa_admin_labels["show_link"] + '</span>\n\
                                    <input type="checkbox" class="apsa-show-link" checked="checked" />\n\
                                    <input type="hidden" class="apsa-hold-checkbox" name="show_link" value="on" />\n\
                                    <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message="' + apsa_admin_labels["show_link_desc"] + '"></span>\n\
                                    </div>\n\
                                </li>\n\
                                <li class="apsa-form-item">\n\
                                    <span>' + apsa_admin_labels["link_color"] + '</span>\n\
                                    <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message="' + apsa_admin_labels["link_color_desc"] + '"></span>\n\
                                    <div class="apsa-input-block">\n\
                                    <input type="text" name="link_color" class="apsa-extra-small-input apsa-colorpicker" data-default-color="#808080" value="#808080">\n\
                                    </div>\n\
                                    <span class="apsa-input-message">' + apsa_admin_labels["default"] + ' #808080</span>\n\
                                </li>';
    }
    if (campaign_type == 'sticky') {
        campaign_options = '<li class="apsa-form-item">\n\
                                    <span>' + apsa_admin_labels["open_link_type"] + '</span>\n\
                                    <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message="' + apsa_admin_labels["open_link_type_desc"] + '"></span>\n\
                                    <div class="apsa-input-block">\n\
                                        <select name="link_type">\n\
                                            <option value="_blank">' + apsa_admin_labels["open_link_type_blank"] + '</option>\n\
                                            <option value="_self">' + apsa_admin_labels["open_link_type_self"] + '</option>\n\
                                            <option value="_window">' + apsa_admin_labels["open_link_type_window"] + '</option>\n\
                                        </select>\n\
                                    </div>\n\
                                </li>\n\
                                <li class="apsa-form-item">\n\
                                    <div class="apsa-input-block">\n\
                                    <span>' + apsa_admin_labels["show_link"] + '</span>\n\
                                    <input type="checkbox" class="apsa-show-link" checked="checked" />\n\
                                    <input type="hidden" class="apsa-hold-checkbox" name="show_link" value="on" />\n\
                                    <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message="' + apsa_admin_labels["show_link_desc"] + '"></span>\n\
                                    </div>\n\
                                </li>\n\
                                <li class="apsa-form-item">\n\
                                    <span>' + apsa_admin_labels["link_color"] + '</span>\n\
                                    <span class="apsa-with-question" title="' + apsa_first_to_upper_case(apsa_admin_labels["detailed_description"]) + '" data-apsa-message="' + apsa_admin_labels["link_color_desc"] + '"></span>\n\
                                    <div class="apsa-input-block">\n\
                                    <input type="text" name="link_color" class="apsa-extra-small-input apsa-colorpicker" data-default-color="#ffffff" value="#ffffff">\n\
                                    </div>\n\
                                    <span class="apsa-input-message">' + apsa_admin_labels["default"] + ' #ffffff</span>\n\
                                </li>';
    }
    return campaign_options;
}

// return child campaign options
function apsa_child_get_campaign_options(that) {
    var child_campaign_options = that.closest('.apsa-campaign-block').find('.apsa-child-campaign-options-form').serializeArray();
    return child_campaign_options;
}
