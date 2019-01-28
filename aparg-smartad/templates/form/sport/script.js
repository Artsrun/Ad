
function apsa_sport_resize(that) {
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
        jQuery(that).removeClass('apsa-banner-1x2 apsa-banner-1x1 apsa-banner-2x1');
        var width = parseFloat(jQuery(that).css('width'));
        var height = parseFloat(jQuery(that).css('height'));
        var ratio = width / height;
        if (ratio < 0.8) {
            jQuery(that).addClass("apsa-banner-1x2");
        } else if (ratio >= 0.8 && ratio <= 1.2) {
            jQuery(that).addClass("apsa-banner-1x1");
        } else {
            jQuery(that).addClass("apsa-banner-2x1");
        }
        if (!jQuery(that).find('.apsa-template-image').length) {
            jQuery(that).find(".apsa-info-block").css({"height": "100%", "width": "100%", "top": "auto", "left": "auto", "right": "auto"})
        }
        var Height = parseFloat(jQuery(that).find(".apsa-info-block").height());
        if (ratio > 0.25 && ratio < 4) {
            jQuery(that).find(".apsa-logo-title").css({"margin": Height * 0.05 + "px auto " + Height * 0.009 + "px", "width": "100%", "position": "static", "top": "auto", "bottom": "auto", "right": "auto"});
            jQuery(that).find(".apsa-font-logo").css({"height": Height * 0.1 + "px", "width": Height * 0.1 + "px"});
            jQuery(that).find(".apsa-heading-one").css({"font-size": Height * 0.2 + "px"});
            jQuery(that).find(".apsa-heading-two,.apsa-heading-three").css({"font-size": Height * 0.14 + "px"});
            jQuery(that).find(".apsa-headiing-time").css("font-size", Height * 0.05 + "px");
            jQuery(that).find(".apsa-heading-container").css({"margin": Height * 0.05 + "px auto", "width": "100%", "float": "none"});
            jQuery(that).find(".apsa-click-btn").css({"font-size": Height * 0.03 + "px", "margin": "auto", "height": Height * 0.08 + "px", "width": "100%", "line-height": Height * 0.08 + "px"});
            jQuery(that).find(".apsa-button-wrap").css({"width": "50%", "position": "static", "top": "auto", "bottom": "auto", "right": "auto"});
        } else if (ratio < 0.25) {
            jQuery(that).find(".apsa-logo-title").css({"margin": Height * 0.1 + "px auto " + Height * 0.01 + "px", "width": "100%", "position": "static", "top": "auto", "bottom": "auto", "right": "auto"});
            jQuery(that).find(".apsa-font-logo").css({"height": Height * 0.1 + "px", "width": Height * 0.1 + "px"});
            jQuery(that).find(".apsa-heading-one").css({"font-size": Height * 0.1 + "px"});
            jQuery(that).find(".apsa-heading-two,.apsa-heading-three").css({"font-size": Height * 0.07 + "px"});
            jQuery(that).find(".apsa-headiing-time").css("font-size", Height * 0.03 + "px");
            jQuery(that).find(".apsa-heading-container").css({"margin": Height * 0.12 + "px auto", "width": "100%", "float": "none"});
            jQuery(that).find(".apsa-click-btn").css({"font-size": Height * 0.02 + "px", "margin": "auto", "height": Height * 0.06 + "px", "width": "100%", "line-height": Height * 0.06 + "px"});
            jQuery(that).find(".apsa-button-wrap").css({"width": "50%", "position": "static", "top": "auto", "bottom": "auto", "right": "auto"});
        } else {
            jQuery(that).find(".apsa-logo-title").css({"margin": Height * 0.18 + "px auto " + Height * 0.1 + "px", "width": "50%"});
            jQuery(that).find(".apsa-font-logo").css({"height": Height * 0.2 + "px", "width": Height * 0.2 + "px"});
            jQuery(that).find(".apsa-heading-one").css({"font-size": Height * 0.25 + "px"});
            jQuery(that).find(".apsa-heading-two,.apsa-heading-three").css({"font-size": Height * 0.2 + "px"});
            jQuery(that).find(".apsa-headiing-time").css("font-size", Height * 0.08 + "px");
            jQuery(that).find(".apsa-heading-container").css({"margin": Height * 0.1 + "px auto", "width": "50%", "float": "left"});
            jQuery(that).find(".apsa-click-btn").css({"font-size": Height * 0.05 + "px", "margin": "0 20%", "height": Height * 0.1 + "px", "width": "60%", "line-height": Height * 0.1 + "px"});
            jQuery(that).find(".apsa-movable-element").eq(0).css({"width": "50%", "position": "absolute", "top": "5%", "right": "0"});
            jQuery(that).find(".apsa-movable-element").eq(1).css({"width": "50%", "position": "absolute", "bottom": "10%", "right": "0"});
        }
    }
    jQuery(that).find('.apsa-heading-container>p').each(function () {
        var out_of_box = parseFloat(jQuery(this).find('span').css('width')) - parseFloat(jQuery(this).css('width'));
        if (out_of_box > 0) {
            var percent = (out_of_box + parseFloat(jQuery(this).css('width'))) / parseFloat(jQuery(this).css('width'));
            var new_font_size = parseFloat(jQuery(this).css('font-size')) / percent;
            jQuery(this).css('font-size', new_font_size + 'px');

        }

        if (jQuery(that).find(".apsa-click-btn").text() === '') {
            jQuery(that).find(".apsa-button-wrap").css('display', 'none')
        }
    })
    if (arguments[1]) {
        jQuery(that).find('.apsa-info-block,.apsa-template-image').css('visibility', 'visible');
    }
}