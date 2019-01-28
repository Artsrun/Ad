
function apsa_decor_resize(that) {
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
    if (!jQuery(that).find('.apsa-template-image').length) {//make info block fullwidth if image is missing
        jQuery(that).find(".apsa-info-block").css({"background-color": jQuery(that).find('.apsa-second-layer').css('background-color'), "height": "100%", "width": "100%", "top": "0", "left": "auto", "right": "auto"})
    }
    if (that.offsetParent !== null) {
        jQuery(that).removeClass('apsa-banner-1x2 apsa-banner-1x1 apsa-banner-2x1 apsa-banner-4x1 apsa-banner-1x4');
        var width = parseFloat(jQuery(that).css('width'));
        var height = parseFloat(jQuery(that).css('height'));
        var ratio = width / height;

        if (ratio < 0.25) {
            jQuery(that).addClass("apsa-banner-1x4");
            jQuery(that).find(".apsa-info-block-wrap:nth-child(1) .apsa-first-layer").css({"width": height * 1.1 + "px", "height": height * 1.1 + "px", "top": "auto", "left": (width - height * 1.15) / 2 + "px", "bottom": height * 0.595 + "px", "right": "auto"});
            jQuery(that).find(".apsa-info-block-wrap:nth-child(1) .apsa-second-layer").css({"width": height * 1 + "px", "height": height * 1 + "px", "top": "auto", "left": (width - height * 1) / 2 + "px", "bottom": height * 0.6 + "px", "right": "auto"});
            jQuery(that).find(".apsa-info-block-wrap:nth-child(2) .apsa-first-layer").css({"width": height * 1.1 + "px", "height": height * 1.1 + "px", "top": height * 0.595 + "px", "left": (width - height * 1.15) / 2 + "px", "bottom": "auto", "right": "auto"});
            jQuery(that).find(".apsa-info-block-wrap:nth-child(2) .apsa-second-layer").css({"width": height * 1 + "px", "height": height * 1 + "px", "top": height * 0.6 + "px", "left": (width - height * 1) / 2 + "px", "bottom": "auto", "right": "auto"});
        } else if (ratio >= 0.25 && ratio < 0.8) {
            jQuery(that).addClass("apsa-banner-1x2");
            jQuery(that).find(".apsa-info-block-wrap:nth-child(1) .apsa-first-layer").css({"width": height * 1.1 + "px", "height": height * 1.1 + "px", "top": "auto", "left": (width - height * 1.15) / 2 + "px", "bottom": height * 0.545 + "px", "right": "auto"});
            jQuery(that).find(".apsa-info-block-wrap:nth-child(1) .apsa-second-layer").css({"width": height * 1 + "px", "height": height * 1 + "px", "top": "auto", "left": (width - height * 1) / 2 + "px", "bottom": height * 0.55 + "px", "right": "auto"});
            jQuery(that).find(".apsa-info-block-wrap:nth-child(2) .apsa-first-layer").css({"width": height * 1.1 + "px", "height": height * 1.1 + "px", "top": height * 0.545 + "px", "left": (width - height * 1.15) / 2 + "px", "bottom": "auto", "right": "auto"});
            jQuery(that).find(".apsa-info-block-wrap:nth-child(2) .apsa-second-layer").css({"width": height * 1 + "px", "height": height * 1 + "px", "top": height * 0.55 + "px", "left": (width - height * 1) / 2 + "px", "bottom": "auto", "right": "auto"});
        } else if (ratio >= 0.8 && ratio < 1.2) {
            jQuery(that).addClass("apsa-banner-1x1");
            jQuery(that).find(".apsa-info-block-wrap:nth-child(1) .apsa-first-layer").css({"width": height * 1.1 + "px", "height": height * 1.1 + "px", "top": -height * 0.07 + "px", "left": "auto", "bottom": "auto", "right": width * 0.495 + "px"});
            jQuery(that).find(".apsa-info-block-wrap:nth-child(1) .apsa-second-layer").css({"width": height * 1 + "px", "height": height * 1 + "px", "top": height * 0.02 + "px", "left": "auto", "bottom": "auto", "right": width * 0.5 + "px"});
            jQuery(that).find(".apsa-info-block-wrap:nth-child(2) .apsa-first-layer").css({"width": height * 1.1 + "px", "height": height * 1.1 + "px", "top": -height * 0.07 + "px", "left": width * 0.495 + "px", "bottom": "auto", "right": "auto"});
            jQuery(that).find(".apsa-info-block-wrap:nth-child(2) .apsa-second-layer").css({"width": height * 1 + "px", "height": height * 1 + "px", "top": height * 0.02 + "px", "left": width * 0.5 + "px", "bottom": "auto", "right": "auto"});
        } else if (ratio >= 1.2 && ratio < 4) {
            jQuery(that).addClass("apsa-banner-2x1");
            jQuery(that).find(".apsa-info-block-wrap:nth-child(1) .apsa-first-layer").css({"width": width * 1.1 + "px", "height": width * 1.1 + "px", "top": (height - width * 1.15) / 2 + "px", "left": "auto", "bottom": "auto", "right": width * 0.545 + "px"});
            jQuery(that).find(".apsa-info-block-wrap:nth-child(1) .apsa-second-layer").css({"width": width * 1 + "px", "height": width * 1 + "px", "top": (height - width * 1) / 2 + "px", "left": "auto", "bottom": "auto", "right": width * 0.55 + "px"});
            jQuery(that).find(".apsa-info-block-wrap:nth-child(2) .apsa-first-layer").css({"width": width * 1.1 + "px", "height": width * 1.1 + "px", "top": (height - width * 1.15) / 2 + "px", "left": width * 0.545 + "px", "bottom": "auto", "right": "auto"});
            jQuery(that).find(".apsa-info-block-wrap:nth-child(2) .apsa-second-layer").css({"width": width * 1 + "px", "height": width * 1 + "px", "top": (height - width * 1) / 2 + "px", "left": width * 0.55 + "px", "bottom": "auto", "right": "auto"});
        } else {
            jQuery(that).addClass("apsa-banner-4x1");
            jQuery(that).find(".apsa-info-block-wrap:nth-child(1) .apsa-first-layer").css({"width": width * 1.1 + "px", "height": width * 1.1 + "px", "top": (height - width * 1.15) / 2 + "px", "left": "auto", "bottom": "auto", "right": width * 0.595 + "px"});
            jQuery(that).find(".apsa-info-block-wrap:nth-child(1) .apsa-second-layer").css({"width": width * 1 + "px", "height": width * 1 + "px", "top": (height - width * 1) / 2 + "px", "left": "auto", "bottom": "auto", "right": width * 0.6 + "px"});
            jQuery(that).find(".apsa-info-block-wrap:nth-child(2) .apsa-first-layer").css({"width": width * 1.1 + "px", "height": width * 1.1 + "px", "top": (height - width * 1.15) / 2 + "px", "left": width * 0.595 + "px", "bottom": "auto", "right": "auto"});
            jQuery(that).find(".apsa-info-block-wrap:nth-child(2) .apsa-second-layer").css({"width": width * 1 + "px", "height": width * 1 + "px", "top": (height - width * 1) / 2 + "px", "left": width * 0.6 + "px", "bottom": "auto", "right": "auto"});
        }
        var Height = parseFloat(jQuery(that).find(".apsa-info-block").css('height'));

        if (ratio > 0.25 && ratio < 4) {
            jQuery(that).find(".apsa-logo-title").css({"margin": Height * 0.05 + "px auto " + Height * 0.01 + "px", "width": "100%"});
            jQuery(that).find(".apsa-font-logo").css({"height": Height * 0.15 + "px", "width": Height * 0.15 + "px"});
            jQuery(that).find(".apsa-heading-two").css({"font-size": Height * 0.18 + "px", "margin-bottom": Height * 0.03 + "px"});
            jQuery(that).find(".apsa-heading-one").css("font-size", Height * 0.08 + "px");
            jQuery(that).find(".apsa-heading-three").css("font-size", Height * 0.04 + "px");
            jQuery(that).find(".apsa-heading").eq(0).css({"margin-top": Height * 0.05 + "px"});
            jQuery(that).find(".apsa-heading").eq(2).css({"margin-bottom": Height * 0.08 + "px"});
            jQuery(that).find(".apsa-click-btn").css({"font-size": Height * 0.03 + "px", "line-height": Height * 0.08 + "px", "height": Height * 0.08 + "px", "width": "100%"});
            jQuery(that).find(".apsa-button-wrap").css({"margin": Height * 0.08 + "px auto", "width": Height * 0.35 + "px"});

        } else if (ratio < 0.25) {
            jQuery(that).find(".apsa-logo-title").css({"margin": Height * 0.01 + "px auto " + Height * 0.05 + "px", "width": "100%", });
            jQuery(that).find(".apsa-font-logo").css({"height": Height * 0.15 + "px", "width": Height * 0.15 + "px"});
            jQuery(that).find(".apsa-heading-two").css({"font-size": Height * 0.12 + "px", "margin-bottom": Height * 0.05 + "px"});
            jQuery(that).find(".apsa-heading-one").css("font-size", Height * 0.06 + "px");
            jQuery(that).find(".apsa-heading-three").css("font-size", Height * 0.03 + "px");
            jQuery(that).find(".apsa-heading").eq(0).css({"margin-top": Height * 0.08 + "px"});
            jQuery(that).find(".apsa-heading").eq(2).css({"margin-bottom": Height * 0.1 + "px"});
            jQuery(that).find(".apsa-click-btn").css({"font-size": Height * 0.02 + "px", "line-height": Height * 0.08 + "px", "height": Height * 0.08 + "px", "width": "100%"});
            jQuery(that).find(".apsa-button-wrap").css({"margin": Height * 0.1 + "px auto", "width": Height * 0.2 + "px", });

        } else {
            jQuery(that).find(".apsa-logo-title").css({"margin": Height * 0.05 + "px auto " + Height * 0.05 + "px", "width": "50%"});
            jQuery(that).find(".apsa-font-logo").css({"height": Height * 0.25 + "px", "width": Height * 0.25 + "px"});
            jQuery(that).find(".apsa-heading-two").css({"font-size": Height * 0.24 + "px", "margin-bottom": Height * 0.1 + "px"});
            jQuery(that).find(".apsa-heading-one").css({"font-size": Height * 0.12 + "px"});
            jQuery(that).find(".apsa-heading-three").css({"font-size": Height * 0.06 + "px"});
            jQuery(that).find(".apsa-heading").eq(0).css({"margin-top": Height * 0.2 + "px"});
            jQuery(that).find(".apsa-heading").eq(2).css({"margin-bottom": Height * 0.2 + "px"});
            jQuery(that).find(".apsa-click-btn").css({"font-size": Height * 0.06 + "px", "line-height": Height * 0.15 + "px", "height": Height * 0.15 + "px", "width": "100%"});
            jQuery(that).find(".apsa-button-wrap").css({"margin": Height * 0.05 + "px 7%", "width": "36%"});

        }
    }
    jQuery(that).find('.apsa-heading').each(function () {
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

    if (arguments[1]) {
        jQuery(that).find('.apsa-info-block-wrap,.apsa-template-image').css('visibility', 'visible');
    }
}