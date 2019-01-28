function apsa_fashion_resize(that) {
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
        if (ratio < 0.2) {
            jQuery(that).find(".apsa-template-container").addClass("apsa-banner-h6");
            jQuery(that).find(".apsa-info-block").css('width', '100%');
            jQuery(that).find(".apsa-block:nth-child(1)").css({"width": 100 * wP + "px", "height": 40 * hP + "px", "top": " 0", "margin": "auto"});
            jQuery(that).find(".apsa-block:nth-child(2)").css({"width": 100 * wP + "px", "height": 40 * hP + "px", "top": "auto", "bottom": "0", "margin": "auto"});
            jQuery(that).find(".apsa-circle-text").css({"font-size": 8 * wP + "px", "width": 30 * wP + "px", "height": 30 * wP + "px", "top": 32 * hP + "px", "left": 10 * wP + "px", "right": "auto"});
            jQuery(that).find(".apsa-link-heading").css({"font-size": 0.8 * hP + "px"});
            jQuery(that).find(".apsa-footer-heading").css({"font-size": 2 * hP + "px"});
            jQuery(that).find(".apsa-button-wrap").css({"font-size": 1.5 * hP + "px"});
            jQuery(that).find(".apsa-click-btn").css({"border-width": 0.4 * hP + "px", "top": (4.4 * hP) + "px"});

        } else if (ratio >= 0.2 && ratio < 0.33) {
            jQuery(that).find(".apsa-template-container").addClass("apsa-banner-h4");
            jQuery(that).find(".apsa-info-block").css('width', '100%');
            jQuery(that).find(".apsa-block:nth-child(1)").css({"width": 85 * wP + "px", "height": 85 * wP + "px", "top": 8 * hP + "px", "margin": "auto"});
            jQuery(that).find(".apsa-block:nth-child(2)").css({"width": 85 * wP + "px", "height": 85 * wP + "px", "top": "auto", "bottom": 8 * hP + "px", "left": "0", "right": "0", "margin": "auto"});
            jQuery(that).find(".apsa-circle-text").css({"font-size": 6 * wP + "px", "width": "25%", "height": "25%", "top": 5 * wP + "px", "left": 30 * wP + "px", "right": "auto"});
            jQuery(that).find(".apsa-link-heading").css({"font-size": 1.5 * hP + "px"});
            jQuery(that).find(".apsa-footer-heading").css({"font-size": 2 * hP + "px"});
            jQuery(that).find(".apsa-button-wrap").css({"font-size": 2.5 * hP + "px"});
            jQuery(that).find(".apsa-click-btn").css({"border-width": 0.8 * hP + "px", "top": (3.2 * hP) + "px"});

        } else if (ratio >= 0.33 && ratio < 0.8) {
            jQuery(that).find(".apsa-template-container").addClass("apsa-banner-h2");
            jQuery(that).find(".apsa-info-block").css('width', '100%');
            jQuery(that).find(".apsa-block:nth-child(1)").css({"width": 85 * wP + "px", "height": 85 * wP + "px", "top": 8 * hP + "px", "margin": "auto"});
            jQuery(that).find(".apsa-block:nth-child(2)").css({"width": 85 * wP + "px", "height": 85 * wP + "px", "top": 8 * hP + "px", "left": "0", "right": "0", "margin": "auto"});
            jQuery(that).find(".apsa-circle-text").css({"font-size": 6 * wP + "px", "width": "25%", "height": "25%", "top": 5 * wP + "px", "left": 30 * wP + "px", "right": "auto"});
            jQuery(that).find(".apsa-link-heading").css({"font-size": 2.5 * hP + "px"});
            jQuery(that).find(".apsa-footer-heading").css({"font-size": 2 * hP + "px"});
            jQuery(that).find(".apsa-button-wrap").css({"font-size": 4 * hP + "px"});
            jQuery(that).find(".apsa-click-btn").css({"border-width": 0.8 * hP + "px", "top": (2 * hP) + "px"});

        } else if (ratio >= 0.8 && ratio < 1.25) {
            jQuery(that).find(".apsa-template-container").addClass("apsa-banner-wh");
            jQuery(that).find(".apsa-info-block").css('width', '100%');
            jQuery(that).find(".apsa-block:nth-child(1)").css({"width": 56 * wP + "px", "height": 56 * wP + "px", "top": 10 * hP + "px", "left": "0", "margin": "auto"});
            jQuery(that).find(".apsa-block:nth-child(2)").css({"width": 56 * wP + "px", "height": 56 * wP + "px", "top": 10 * hP + "px", "left": "0", "right": "0"});
            jQuery(that).find(".apsa-circle-text").css({"font-size": 4 * wP + "px", "width": "25%", "height": "25%", "top": 8.5 * wP + "px", "left": 15 * wP + "px", "right": "auto"});
            jQuery(that).find(".apsa-link-heading").css({"font-size": 2.5 * (wP > hP ? wP : hP) + "px"});
            jQuery(that).find(".apsa-footer-heading").css({"font-size": 2 * (wP > hP ? wP : hP) + "px"});
            jQuery(that).find(".apsa-button-wrap").css({"font-size": 4 * hP + "px"});
            jQuery(that).find(".apsa-click-btn").css({"border-width": 0.8 * hP + "px", "top": (2 * hP) + "px"});


        } else if (ratio >= 1.25 && ratio < 3) {
            jQuery(that).find(".apsa-template-container").addClass("apsa-banner-w2");
            jQuery(that).find(".apsa-info-block").css('width', bWidth - ((8 * hP) + (75 * hP)) + 'px');
            jQuery(that).find(".apsa-block:nth-child(1)").css({"width": 75 * hP + "px", "height": 75 * hP + "px", "top": 12.5 * hP + "px", "left": 8 * hP + "px", "margin": "0"});
            jQuery(that).find(".apsa-block:nth-child(2)").css({"width": 75 * hP + "px", "height": 75 * hP + "px", "top": 12.5 * hP + "px", "left": "auto", "right": 8 * hP + "px"});
            jQuery(that).find(".apsa-circle-text").css({"font-size": 6 * hP + "px", "width": "25%", "height": "25%", "top": 5 * hP + "px", "left": 30 * hP + "px", "right": "auto"});
            jQuery(that).find(".apsa-link-heading").css({"font-size": 2 * wP + "px"});
            jQuery(that).find(".apsa-footer-heading").css({"font-size": 1.5 * wP + "px"});
            jQuery(that).find(".apsa-button-wrap").css({"font-size": 2.3 * wP + "px"});
            jQuery(that).find(".apsa-click-btn").css({"border-width": 0.1 * wP + "px", "top": (20.4 * hP - 3.88 * wP) / 2 + "px"});

        } else if (ratio >= 3 && ratio < 5) {
            jQuery(that).find(".apsa-template-container").addClass("apsa-banner-w4");
            jQuery(that).find(".apsa-info-block").css('width', bWidth - ((30 * hP) + (100 * hP)) + 'px');
            jQuery(that).find(".apsa-block:nth-child(1)").css({"width": 100 * hP + "px", "height": 100 * hP + "px", "top": 0 + "px", "left": 30 * hP + "px", "margin": "0"});
            jQuery(that).find(".apsa-block:nth-child(2)").css({"width": 100 * hP + "px", "height": 100 * hP + "px", "top": 0 + "px", "right": 30 * hP + "px", "left": "auto", "margin": "0"});
            jQuery(that).find(".apsa-circle-text").css({"font-size": 6 * hP + "px", "width": "25%", "height": "25%", "top": 5 * hP + "px", "left": 30 * hP + "px", "right": "auto"});
            jQuery(that).find(".apsa-link-heading").css({"font-size": 2 * wP + "px"});
            jQuery(that).find(".apsa-footer-heading").css({"font-size": 1.5 * wP + "px"});
            jQuery(that).find(".apsa-button-wrap").css({"font-size": 1.5 * wP + "px"});
            jQuery(that).find(".apsa-click-btn").css({"border-width": 0.08 * wP + "px", "top": (20.4 * hP - 2.56 * wP) / 2 + "px"});

        } else {
            if (bHeight > 120) {
                jQuery(that).find(".apsa-template-container").addClass("apsa-banner-w4");
                jQuery(that).find(".apsa-info-block").css('width', bWidth - ((30 * hP) + (100 * hP)) + 'px');
                jQuery(that).find(".apsa-block:nth-child(1)").css({"width": 100 * hP + "px", "height": 100 * hP + "px", "top": 0 + "px", "left": 30 * hP + "px", "margin": "0"});
                jQuery(that).find(".apsa-block:nth-child(2)").css({"width": 100 * hP + "px", "height": 100 * hP + "px", "top": 0 + "px", "right": 30 * hP + "px", "left": "auto", "margin": "0"});
                jQuery(that).find(".apsa-circle-text").css({"font-size": 5 * hP + "px", "width": "25%", "height": "25%", "top": 5 * hP + "px", "left": 30 * hP + "px", "right": "auto"});
                jQuery(that).find(".apsa-link-heading").css({"font-size": 0.9 * wP + "px"});
                jQuery(that).find(".apsa-footer-heading").css({"font-size": 0.7 * wP + "px"});
                jQuery(that).find(".apsa-button-wrap").css({"font-size": 0.7 * wP + "px"});
                jQuery(that).find(".apsa-click-btn").css({"border-width": 0.08 * wP + "px", "top": (20.4 * hP - 1.44 * wP) / 2 + "px"});
            } else {
                var xwP = bWidth > 1100 ? 11 : wP;
                xwP = bHeight < 90 ? 4 : xwP;
                jQuery(that).find(".apsa-template-container").addClass("apsa-banner-w6");
                jQuery(that).find(".apsa-info-block").css('width', bWidth - ((40 * wP)) + 'px');
                jQuery(that).find(".apsa-block:nth-child(1)").css({"width": 40 * wP + "px", "height": 100 * hP + "px", "top": 0 + "px", "left": "0", "margin": "0"});
                jQuery(that).find(".apsa-block:nth-child(2)").css({"width": 40 * wP + "px", "height": 100 * hP + "px", "top": 0 + "px", "left": "auto", "right": "0", "margin": "0"});
                jQuery(that).find(".apsa-block:nth-child(1) .apsa-circle-text").css({"font-size": 8 * hP + "px", "width": 30 * hP + "px", "height": 30 * hP + "px", "top": 60 * hP + "px", "left": "auto", "right": "-" + 15 * hP + "px"});
                jQuery(that).find(".apsa-block:nth-child(2) .apsa-circle-text").css({"font-size": 8 * hP + "px", "width": 30 * hP + "px", "height": 30 * hP + "px", "top": 60 * hP + "px", "right": "auto", "left": "-" + 15 * hP + "px"});
                jQuery(that).find(".apsa-link-heading").css({"font-size": 2 * xwP + "px"});
                jQuery(that).find(".apsa-footer-heading").css({"font-size": 1.5 * xwP + "px"});
                jQuery(that).find(".apsa-button-wrap").css({"font-size": 2 * xwP + "px"});
                jQuery(that).find(".apsa-click-btn").css({"border-width": 0.4 * xwP + "px", "top": (50 * hP - 4 * xwP) / 2 + "px"});
            }
        }
    }
    jQuery(that).find(".apsa-circle-text").css('line-height', parseFloat(jQuery(that).find('.apsa-circle-text').css('height')) + "px")
    jQuery(that).find('.apsa-circle-text').each(function () {

        var out_of_box = parseFloat(jQuery(this).find('span').css('width')) - parseFloat(jQuery(this).css('width'));
        if (out_of_box > 0) {
            var percent = (out_of_box + parseFloat(jQuery(this).css('width'))) / parseFloat(jQuery(this).css('width'));
            var new_font_size = parseFloat(jQuery(this).css('font-size')) / percent;
            jQuery(this).css('font-size', new_font_size + 'px');

        }
    })
    if (!jQuery(that).find(".apsa-block").length) {
        jQuery(that).find(".apsa-info-block").css({"width": "100%", "top": "0", "left": "0", "bottom": "0", "position": "absolute", "right": "0", "margin": "auto"});
    }
    if (!jQuery(that).find(".apsa-info-block").length) {
        jQuery(that).find(".apsa-block").css({"top": "0", "left": "0", "bottom": "0", "position": "absolute", "right": "0", "margin": "auto"});
    }
    if (jQuery(that).find(".apsa-click-btn").text() === '') {
        jQuery(that).find(".apsa-click-btn").css('display', 'none')
    }
    if (arguments[1]) {
        jQuery(that).find('.apsa-template-container').css('visibility', 'visible');
    }
}
