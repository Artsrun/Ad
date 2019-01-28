// Hold if allow leave page
apsa_allow_leave_page = true;

/**
 * Change  apsa_allow_leave_page value
 * @param {type} allow
 * @returns {undefined}
 */
function apsa_leave_page(allow) {
    if (typeof allow === 'undefined' || typeof allow !== 'boolean') {
        allow = true;
    }

    apsa_allow_leave_page = allow;
}


/**
 * Set coockie function
 * 
 * @param str cname
 * @param str cvalue
 * @param int exseconds
 * @returns {undefined}
 */
function apsa_set_cookie(cname, cvalue, exseconds) {
    var d = new Date();
    d.setTime(d.getTime() + (exseconds * 1000));
    var expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires + "; path=/";
}

/**
 * Display Success or Error message
 * 
 * @param str message_type
 * @param str message_text
 * @returns {undefined}
 */
function apsa_action_message(message_type, message_text) {
    if (message_text === undefined) {
        message_text = message_type;
        message_type = 'info';
    }

    //Determine message show time
    var message_length = message_text.length;
    var show_time = 70 * message_length;
    if (show_time < 2100) {
        show_time = 2100;
    }

    if (typeof message_time !== 'undefined') {
        clearTimeout(message_time);
    }
    jQuery("#apsa-message-popup").removeAttr('class').addClass('apsa-popup');
    jQuery("#apsa-message-popup").addClass("apsa-" + message_type + "-message");
    jQuery("#apsa-message-popup").find('p').html(apsa_first_to_upper_case(message_text));
    jQuery('#apsa-message-popup').attr('data-apsa-open', "true");

    message_time = setTimeout(function () {
        jQuery('#apsa-message-popup').attr('data-apsa-open', "false");
        jQuery("#apsa-message-popup").removeClass("apsa-" + message_type + "-message");
    }, show_time);
}

function apsa_confirm_click_message(dom_obj, message_text, confirm_title) {

    if (confirm_title === undefined || confirm_title === "") {
        confirm_title = "Confirm";
    }

    confirm_title = apsa_first_to_upper_case(confirm_title);

    jQuery("#apsa-confirm-message").find(".apsa-popup-title").text(confirm_title);
    jQuery("#apsa-confirm-text").text(apsa_first_to_upper_case(message_text));
    jQuery("#apsa-confirm-options").empty();
    apsa_confirm_dom = dom_obj;

    jQuery("#apsa-confirm-options").append("<li class='apsa-confirm-option' data-apsa-confirm-option='1'><button class='button button-primary'>" + apsa_admin_labels["confirm_yes"] + "</button></li>");
    jQuery("#apsa-confirm-options").append("<li class='apsa-confirm-option' data-apsa-confirm-option='0'><button class='button button-primary'>" + apsa_admin_labels["confirm_cancel"] + "</button></li>");

    jQuery('#apsa-managing-overlay').fadeIn(150);
    jQuery('#apsa-confirm-message').attr('data-apsa-open', "true");
    jQuery('body').addClass('modal-open');
}

/**
 * Detect IE version
 * 
 * @returns {Boolean}
 */
function apsa_ie_version() {

    var ua = window.navigator.userAgent;   

    var edge = ua.indexOf('Edge/');
    
    var msie = ua.indexOf("MSIE ");
    if (msie > 0)  // If Internet Explorer, return version number
    {
        return parseInt(ua.substring(msie + 5, ua.indexOf(".", msie)));
    } else if (!!navigator.userAgent.match(/Trident.*rv\:11\./)) {
        return 11;
    } else if (edge > 0) {
        return (parseInt(ua.substring(edge + 5, ua.indexOf('.', edge)), 10));
    }

    return false;
}

/**
 * Detect Safari
 * 
 * @returns {Boolean}
 */
function apsa_is_safari() {
    return /Version\/[\d\.]+.*Safari/.test(navigator.userAgent);
}

/**
 * Check if positive integer
 * 
 * @param val
 * @returns {Number|Boolean}
 */
function apsa_is_positive_integer(val) {
    return val == "0" || ((val | 0) > 0 && val % 1 == 0);
}

/**
 * Check if valid block size
 * 
 * @param string val
 * @returns {Boolean}
 */
function apsa_is_size(val) {

    var px_index = val.lastIndexOf('px');
    var percent_index = val.lastIndexOf('%');

    var int_val = false;

    if (val.length > 2 && px_index == val.length - 2) {
        int_val = val.replace(/px$/, '');
    } else if (val.length > 1 && percent_index == val.length - 1) {
        int_val = val.replace(/%$/, '');
    } else {
        return false;
    }

    return apsa_is_positive_integer(int_val);
}

/**
 * Check if string valid url
 * 
 * @param str val
 * @returns {Boolean}
 */
function apsa_is_url(val) {
    var myRegExp = /^(?:(?:https?|ftp):\/\/)(?:\S+(?::\S*)?@)?(?:(?!10(?:\.\d{1,3}){3})(?!127(?:\.\d{1,3}){3})(?!169\.254(?:\.\d{1,3}){2})(?!192\.168(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]+-?)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]+-?)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})))(?::\d{2,5})?(?:\/[^\s]*)?$/i;
    if (!myRegExp.test(val)) {
        return false;
    } else {
        return true;
    }
}

/**
 * Uppercase only first letter of string
 * @param str str
 * @returns str
 */
function apsa_first_to_upper_case(str) {
    return str.substr(0, 1).toUpperCase() + str.substr(1);
}

/**
 * Select inner text whwn clicked on element
 */
function apsa_select_all(e) {
    if ("undefined" != typeof window.getSelection && "undefined" != typeof document.createRange) {
        var t = document.createRange();
        t.selectNodeContents(e);
        var i = window.getSelection();
        i.removeAllRanges(), i.addRange(t)
    } else if ("undefined" != typeof document.selection && "undefined" != typeof document.body.createTextRange) {
        var o = document.body.createTextRange();
        o.moveToElementText(e), o.select()
    }
}

/*
 * validation
 */
function apsa_validation(scope) {
    // check requireds
    var error_exist = false;
    scope.find('.apsa-required').each(function () {
        if (jQuery(this).val() == "") {
            jQuery(this).addClass('apsa-error-input');
            jQuery(this).closest('.apsa-form-item').find('.apsa-input-message').addClass('apsa-error-valid');
            jQuery(this).closest('.apsa-form-item').find('.apsa-input-message').text(apsa_admin_labels["required_filed_error"]);
            error_exist = true;
        } else {
            jQuery(this).removeClass('apsa-error-input');
            jQuery(this).closest('.apsa-form-item').find('.apsa-input-message').removeClass('apsa-error-valid');
            jQuery(this).closest('.apsa-form-item').find('.apsa-input-message').text(jQuery(this).closest('.apsa-form-item').find('.apsa-input-message').attr("data-apsa-default-message"));
        }
    });

    // Check links
    scope.find('.apsa-link').each(function () {
        // hide broken link messege
        if (jQuery(this).hasClass('apsa-overdue-element')) {
            jQuery(this).removeClass('apsa-overdue-element');
            jQuery(this).closest('.apsa-form-item').find('.apsa-input-message').text('').removeClass('apsa-warning-message');
        }

        //-------
        if (jQuery(this).val() !== "") {
            if (!apsa_is_url(jQuery(this).val())) {
                jQuery(this).addClass('apsa-error-input');
                jQuery(this).closest('.apsa-form-item').find('.apsa-input-message').addClass('apsa-error-valid');
                jQuery(this).closest('.apsa-form-item').find('.apsa-input-message').text(apsa_admin_labels["valid_url_error"]);
                error_exist = true;
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

    // Check positive integers
    scope.find('.apsa-positive-int').each(function () {
        if (jQuery(this).val() !== "") {
            if (!apsa_is_positive_integer(jQuery(this).val())) {

                jQuery(this).removeClass('apsa-overdue-element');
                jQuery(this).closest('.apsa-form-item').find('.apsa-input-message').removeClass('apsa-warning-message');

                jQuery(this).addClass('apsa-error-input');
                jQuery(this).closest('.apsa-form-item').find('.apsa-input-message').addClass('apsa-error-valid');
                jQuery(this).closest('.apsa-form-item').find('.apsa-input-message').text(apsa_admin_labels["positive_int_error"]);
                error_exist = true;
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

    // Check size
    scope.find('.apsa-size').each(function () {
        if (jQuery(this).val() !== "") {
            if (!apsa_is_size(jQuery(this).val())) {
                jQuery(this).addClass('apsa-error-input');
                jQuery(this).closest('.apsa-form-item').find('.apsa-input-message').addClass('apsa-error-valid');
                jQuery(this).closest('.apsa-form-item').find('.apsa-input-message').text(apsa_admin_labels["positive_int_error"] + ' ' + '(px/%)');
                error_exist = true;
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
    return error_exist;
}

/**
 * Set datepicker on input element
 * 
 * @param object dom_element
 * @returns {undefined}
 */
function apsa_set_datepicker(dom_element) {
    jQuery(dom_element).datepicker({
        dateFormat: apsa_date_format,
        monthNames: apsa_month_names,
        monthNamesShort: apsa_month_short_names,
        dayNamesShort: apsa_day_short_names,
        dayNamesMin: apsa_day_short_names,
        dayNames: apsa_day_names,
        firstDay: apsa_start_of_week,
        changeMonth: true,
        changeYear: true,
        onSelect: function () {
            var wp_date = jQuery(this).datepicker('getDate');
            var date = jQuery.datepicker.formatDate("yy-mm-dd", wp_date);

            jQuery(this).next('[type="hidden"]').val(date);

            jQuery(this).trigger('change');
        }
    });
}

/**
 * Add file uploader from media library
 * @param {string} selector
 * @param {string} select_callback
 * @param {boolean} is_multiple
 * @param {string/boolean(false)} file_type 
 * @param {string/boolean(false)} sub_type 
 * @returns {undefined}
 */
function apsa_make_file_uploader(selector, select_callback, is_multiple, file_type, sub_type) {

    file_type = file_type || "";
    sub_type = sub_type || "";

    jQuery(document).on('click', selector, function (e) {
        e.preventDefault();
        that_button = jQuery(this);

        var search_type = "";
        if (file_type != "") {
            if (sub_type != "") {
                search_type = file_type + '/' + sub_type;
            } else {
                search_type = file_type + '/';
            }
        }

        var add_file_uploader = wp.media.frames.file_frame = wp.media({
            title: apsa_admin_labels["media_title_choose_file"],
            button: {
                text: apsa_admin_labels["media_button_choose_file"]
            },
            library: {type: search_type},
            multiple: is_multiple
        });

        // When a file is selected, grab required info about that file
        add_file_uploader.on('select', function () {
            var attachments = add_file_uploader.state().get('selection').toJSON();

            /** Check if required type of files choosen */
            var is_valid_type = true;

            jQuery.each(attachments, function (key, attachment) {
                if (file_type && attachment.type != file_type) {
                    is_valid_type = false;
                    return false;
                } else if (sub_type && attachment.subtype != sub_type) {
                    is_valid_type = false;
                    return false;
                }
            });

            // if any attacment not in valid type return error
            if (!is_valid_type) {
                apsa_action_message("error", apsa_admin_labels["unc_type_of_file_msg"]);
                return false;
            }

            // Call apropriate callback
            if (typeof window[select_callback] === "function") {
                window[select_callback](attachments, that_button);
            }

            return true;
        });

        add_file_uploader.on('close', function () {
            add_file_uploader.remove();
        });

        // Open the uploader dialog
        add_file_uploader.open();
    }
    );
}

/**
 * Add reset styles
 */
function apsa_reset_style() {
    jQuery('.apsa-reset-start').each(function () {
        jQuery(this).find("*").addBack().not(".apsa-reset-stop *").removeClass("apsa-reset-start apsa-reset-stop").addClass("apsa-reset-style");
    })
}

/** Document ready actions */
jQuery(document).ready(function ($) {

    /**
     * Slide block open and close
     */
    $(document).on('click', '.apsa-slide-opener', function () {

        if ($(this).siblings('.apsa-sliding-block').attr('data-apsa-open') == 'false') {
            $(this).siblings('.apsa-sliding-block').attr('data-apsa-open', "true");
        } else {
            $(this).siblings('.apsa-sliding-block').attr('data-apsa-open', "false");
        }

        $(this).attr("data-apsa-open-slide", $(this).attr("data-apsa-open-slide") == "false" ? "true" : "false");
    });

    /**
     * Display unsaved reload message if nneded
     */
    $(window).bind('beforeunload', function () {
        if (typeof apsa_allow_leave_page !== 'undefined' && apsa_allow_leave_page == false) {
            return apsa_admin_labels['unsaved_reload'];
        }
    });

    /**
     * Display detailed description of field
     */
    $(document).on("click", ".apsa-with-question", function () {
        apsa_action_message("info", $(this).attr("data-apsa-message"));
    });

    /**
     * Select datepicker input text on click event
     */
    $(document).on('click', '.hasDatepicker', function () {
        $(this).select();
    });

    /**
     * Let use only backspace and delete keys on datepicker inputs
     */
    $(document).on('keydown', '.hasDatepicker', function (e) {
        if (e.which != 46 && e.which != 8) {
            return false;
        } else {
            $(this).val('');
            $(this).trigger('change');
        }

        if ($(this).val() == '') {
            $(this).next('[type="hidden"]').val('');
        }
    });

    /**
     * Select inner text when clicked on element with apsa-click-select class
     */
    $(document).on('click', '.apsa-click-select', function () {
        apsa_select_all($(this).get(0));
    });

    /** Triger click event after confirmation */
    $(document).on("click", ".apsa-confirm-option", function () {
        if ($(this).attr("data-apsa-confirm-option") == "1") {
            apsa_leave_page(true);
            apsa_confirm_dom.addClass("apsa-confirmated");
            apsa_confirm_dom[0].click();
            apsa_confirm_dom = undefined;
        }

        $('#apsa-managing-overlay').fadeOut(150);
        $("#apsa-confirm-message").attr("data-apsa-open", "false");
        jQuery('body').removeClass('modal-open');
    });

    /**
     * Select datepicker input text on click event
     */
    $(document).on('click', '.hasDatepicker', function () {
        $(this).select();
    });

    /**
     * Let use only backspace and delete keys on datepicker inputs
     */
    $(document).on('keydown', '.hasDatepicker', function (e) {
        if (e.which != 46 && e.which != 8) {
            return false;
        } else {
            $(this).val('');
            $(this).trigger('change');
        }

        if ($(this).val() == '') {
            $(this).next('[type="hidden"]').val('');
        }
    });

});