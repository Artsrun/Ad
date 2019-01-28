
function apsa_travel_resize(that) {
    /*==================Info column Link  open type=================*/
    jQuery(that).find('.apsa-click-btn').off('click.apsa-click-btn').on('click.apsa-click-btn', function (e) {
        if (jQuery(that).closest('.apsa-preview-wrap').length) {
            return false;  //disable btn in privew mode
        }
        e.preventDefault();
        var link_type = jQuery(this).attr('target');
        var link_to = jQuery(this).attr('href');
        link_to = (link_type == "_self" && link_to == '') ? window.location.href : link_to;
        var location = (link_type == "_window") ? "scrollbars=yes,resizable=yes,toolbar=no,width=700,height=600" : null;
        window.open(link_to, link_type, location);
        e.stopPropagation();
    })
    if (that.offsetParent !== null) {
        jQuery(that).find(".apsa-template-container").removeClass().addClass('apsa-template-container');
        var bWidth = parseFloat(jQuery(that).find(".apsa-template-container").css('width'));
        var bHeight = parseFloat(jQuery(that).find(".apsa-template-container").css('height'));
        var wP = bWidth / 100;
        var hP = bHeight / 100;
        var ratio = bWidth / bHeight;
        var btn_flag = false;
        if (ratio < 0.2) {
            jQuery(that).find(".apsa-template-container").addClass("apsa-banner-h6");
            jQuery(that).find(".apsa-heading-one").css({"font-size": 20 * wP + "px"});
            jQuery(that).find(".apsa-heading-item").eq(0).css({"top": 2 * hP + "px", "left": "auto"});
            jQuery(that).find(".apsa-heading-item").eq(1).css({"top": 5 * hP + "px", "left": "auto"});
            jQuery(that).find(".apsa-heading-two").css({"font-size": 10 * wP + "px"});
            jQuery(that).find(".apsa-heading-three").css({"font-size": 8 * wP + "px", "top": "auto", "left": "auto"});
            jQuery(that).find(".apsa-button-wrap").css({"height": 15 * wP + "px", "bottom": 10 * hP + "px", "left": 20 * wP + "px"});
            jQuery(that).find(".apsa-click-btn").css({"padding": "0 " + wP * 4 + "px"});
            jQuery(that).find(".apsa-template-container").css({"padding": 0});
            jQuery(that).find(".apsa-template-body").css({"border-width": 0});
        } else if (ratio >= 0.2 && ratio < 0.33) {
            jQuery(that).find(".apsa-template-container").addClass("apsa-banner-h4");
            jQuery(that).find(".apsa-heading-one").css({"font-size": 20 * wP + "px"});
            jQuery(that).find(".apsa-heading-item").eq(0).css({"top": 2 * hP + "px", "left": "auto"});
            jQuery(that).find(".apsa-heading-item").eq(1).css({"top": 5 * hP + "px", "left": "auto"});
            jQuery(that).find(".apsa-heading-two").css({"font-size": 10 * wP + "px"});
            jQuery(that).find(".apsa-heading-three").css({"font-size": 8 * wP + "px", "top": "auto", "left": "auto"});
            jQuery(that).find(".apsa-button-wrap").css({"height": 15 * wP + "px", "bottom": 5 * hP + "px", "left": 16 * wP + "px"});
            jQuery(that).find(".apsa-click-btn").css({"padding": "0 " + wP * 4 + "px"});
            jQuery(that).find(".apsa-template-container").css({"padding": 3 * wP + "px"});
            jQuery(that).find(".apsa-template-body").css({"border-width": 2 * wP + "px"});
        } else if (ratio >= 0.33 && ratio < 0.8) {
            jQuery(that).find(".apsa-template-container").addClass("apsa-banner-h2");
            jQuery(that).find(".apsa-heading-one").css({"font-size": 15 * wP + "px"});
            jQuery(that).find(".apsa-heading-item").css({"top": 1.5 * hP + "px", "left": 5 * wP + "px"});
            jQuery(that).find(".apsa-heading-two").css({"font-size": 7.5 * wP + "px"});
            jQuery(that).find(".apsa-heading-three").css({"font-size": 3 * wP + "px", "top": "auto"});
            jQuery(that).find(".apsa-button-wrap").css({"height": 5 * hP + "px", "bottom": 26 * hP + "px", "left": 2 * wP + "px"});
            jQuery(that).find(".apsa-click-btn").css({"padding": "0 " + hP * 1.1 + "px"});
            jQuery(that).find(".apsa-template-container").css({"padding": 3 * wP + "px"});
            jQuery(that).find(".apsa-template-body").css({"border-width": 2 * wP + "px"});
        } else if (ratio >= 0.8 && ratio < 1.25) {
            jQuery(that).find(".apsa-template-container").addClass("apsa-banner-wh");
            jQuery(that).find(".apsa-heading-one").css({"top": 2.5 * hP + "px", "font-size": 8.5 * hP + "px"});
            jQuery(that).find(".apsa-heading-item").eq(0).css({"left": 2 * wP + "px"});
            jQuery(that).find(".apsa-heading-item").eq(1).css({"left": 4 * wP + "px"});
            jQuery(that).find(".apsa-heading-two").css({"top": 5.5 * hP + "px", "font-size": 4 * hP + "px"});
            jQuery(that).find(".apsa-heading-three").css({"font-size": 2 * hP + "px", "top": "auto", "left": "auto"});
            jQuery(that).find(".apsa-button-wrap").css({"height": 5 * hP + "px", "bottom": 16 * hP + "px", "left": 2 * wP + "px"});
            jQuery(that).find(".apsa-click-btn").css({"padding": "0 " + hP * 1.1 + "px"});
            jQuery(that).find(".apsa-template-container").css({"padding": 3 * hP + "px"});
            jQuery(that).find(".apsa-template-body").css({"border-width": 2 * hP + "px"});
        } else if (ratio >= 1.25 && ratio < 3) {
            jQuery(that).find(".apsa-template-container").addClass("apsa-banner-w2");
            jQuery(that).find(".apsa-heading-one").css({"top": 2.5 * hP + "px", "font-size": 14 * hP + "px"});
            jQuery(that).find(".apsa-heading-item").eq(0).css({"left": 2 * wP + "px"});
            jQuery(that).find(".apsa-heading-item").eq(1).css({"left": 4 * wP + "px"});
            jQuery(that).find(".apsa-heading-two").css({"top": 6 * hP + "px", "font-size": 7 * hP + "px"});
            jQuery(that).find(".apsa-heading-three").css({"font-size": 4 * hP + "px", "top": "auto", "left": "auto"});
            jQuery(that).find(".apsa-button-wrap").css({"height": 6 * hP + "px", "bottom": 21 * hP + "px", "left": 2 * wP + "px"});
            jQuery(that).find(".apsa-click-btn").css({"padding": "0 " + hP * 2 + "px"});
            jQuery(that).find(".apsa-template-container").css({"padding": 3 * hP + "px"});
            jQuery(that).find(".apsa-template-body").css({"border-width": 2 * hP + "px"});
        } else if (ratio >= 3 && ratio < 5) {
            jQuery(that).find(".apsa-click-btn").css({"padding": "0 " + hP * 3.5 + "px"});
            jQuery(that).find(".apsa-template-container").addClass("apsa-banner-w4");
            jQuery(that).find(".apsa-heading-one").css({"font-size": 16 * hP + "px"});
            jQuery(that).find(".apsa-heading-item").eq(0).css({"top": 5 * hP + "px", "left": "auto"});
            jQuery(that).find(".apsa-heading-item").eq(1).css({"top": 12 * hP + "px", "left": "auto"});
            jQuery(that).find(".apsa-heading-two").css({"font-size": 8 * hP + "px"});
            jQuery(that).find(".apsa-heading-three").css({"font-size": 4 * hP + "px", "top": "auto", "left": "auto"});
            jQuery(that).find(".apsa-template-container").css({"padding": 3 * hP + "px"});
            jQuery(that).find(".apsa-template-body").css({"border-width": 2 * hP + "px"});
            jQuery(that).find(".apsa-button-wrap").css({"height": 8 * hP + "px", "bottom": 21 * hP + "px"});
            btn_flag = true;
        } else {
            jQuery(that).find(".apsa-click-btn").css({"padding": "0 " + wP * 2.5 + "px"});
            jQuery(that).find(".apsa-template-container").addClass("apsa-banner-w6");
            jQuery(that).find(".apsa-heading-one").css({"font-size": 28 * hP + "px"});
            jQuery(that).find(".apsa-heading-item").eq(0).css({"top": 1 * hP + "px", "left": "auto"});
            jQuery(that).find(".apsa-heading-item").eq(1).css({"top": 4.5 * hP + "px", "left": "auto"});
            jQuery(that).find(".apsa-heading-two").css({"font-size": 14 * hP + "px"});
            jQuery(that).find(".apsa-heading-three").css({"font-size": 7 * hP + "px", "top": "auto", "left": "auto"});
            jQuery(that).find(".apsa-template-container").css({"padding": 0});
            jQuery(that).find(".apsa-template-body").css({"border-width": 0});
            jQuery(that).find(".apsa-button-wrap").css({"height": 12 * hP + "px", "bottom": 16 * hP + "px"});
            btn_flag = true;
        }
    }
    jQuery(that).find('.apsa-heading-wrap>div').each(function () {
        var out_of_box = parseFloat(jQuery(this).find('span').css('width')) - parseFloat(jQuery(this).css('width'));
        if (out_of_box > 0) {
            var percent = (out_of_box + parseFloat(jQuery(this).css('width'))) / parseFloat(jQuery(this).css('width'));
            var new_font_size = parseFloat(jQuery(this).css('font-size')) / percent;
            jQuery(this).css('font-size', new_font_size + 'px');

        }
    })
    if (jQuery(that).find(".apsa-click-btn").text() === '') {
        jQuery(that).find(".apsa-button-wrap").css('display', 'none')
    }
    jQuery(that).find(".apsa-click-btn").css({"line-height": jQuery(that).find(".apsa-button-wrap").height() + "px", "font-size": 0.5 * jQuery(that).find(".apsa-button-wrap").height() + "px"});
    if (btn_flag) {
        jQuery(that).find(".apsa-button-wrap").css({"left": (parseFloat(jQuery(that).find(".apsa-heading-wrap").css('width')) - parseFloat(jQuery(that).find(".apsa-click-btn").css('width'))) / 2 + "px"});
        btn_flag = false;
    }
    if (arguments[1]) {
        jQuery(that).find('.apsa-template-container').css('visibility', 'visible');
    }
}