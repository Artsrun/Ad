function apsa_food_resize(that) {

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
    /*=====  template view calculation  ======*/
    if (that.offsetParent !== null) {
        jQuery(that).find(".apsa-template-body").removeClass().addClass('apsa-template-body');
        var height = parseFloat(jQuery(that).find('.apsa-temp-inner-wrap').css('height')) + 58;
        var width = parseFloat(jQuery(that).find('.apsa-temp-inner-wrap').css('width')) + 58;
        var size;
        var ratio = width / height;
        if (ratio < 0.6) {
            size = width;
            jQuery(that).find(".apsa-click-btn").css('top', 'auto');
            jQuery(that).find(".apsa-template-body").addClass("apsa-banner-1x2");
        } else if (ratio >= 0.6 && ratio <= 1.2) {
            size = width;
            jQuery(that).find(".apsa-click-btn").css('top', 'auto');
            jQuery(that).find(".apsa-template-body").addClass("apsa-banner-1x1");
        } else {
            size = height;

            jQuery(that).find(".apsa-click-btn").css('top', parseFloat(jQuery(that).find('.apsa-link-btn-wrap').css('height')) - (parseFloat(jQuery(that).find(".apsa-click-btn").css('height')) / 2) + "px");
            jQuery(that).find(".apsa-template-body").addClass("apsa-banner-2x1");
        }

        jQuery(that).find(".apsa-font-logo-wrap>div, .apsa-above-title>div,.apsa-below-title>div").css({"font-size": size * 0.04 + "px"});
        jQuery(that).find(".apsa-click-btn").css({"font-size": size * 0.03 + "px"});
        jQuery(that).find(".apsa-click-btn").css({"height": size * 0.07 + "px", "line-height": size * 0.07 + "px", "padding": "0 " + size * 0.07 + "px"});
        jQuery(that).find(".apsa-center-logo-text").css({"font-size": size * 0.16 + "px"});
        jQuery(that).find(".apsa-center-logo>span>span").css({"font-size": size * 0.13 + "px"});
        jQuery(that).find(".apsa-center-shape").css({"height": size * 0.15 + "px", "width": size * 0.15 + "px"});
        jQuery(that).find(".apsa-center-shape>span").css({"font-size": size * 0.03 + "px"});
        jQuery(that).find(".apsa-font-logo").css({"height": size * 0.08 + "px", "width": size * 0.08 + "px"});
        if (width < 260 || height < 250) {

            if (height > 250 || (width < 390 && height < 250) || height < 169) {
                jQuery(that).find(".apsa-template-body").addClass("apsa-remove-frame");
                jQuery(that).find(".apsa-click-btn").css('top', 'auto');
            }

            jQuery(that).find(".apsa-font-logo-wrap>div,.apsa-above-title>div,.apsa-below-title>div").css({"font-size": size * 0.06 + "px"});
            jQuery(that).find(".apsa-center-shape>span,.apsa-click-btn").css({"font-size": size * 0.05 + "px"});
            jQuery(that).find(".apsa-click-btn").css({"height": size * 0.12 + "px", "line-height": size * 0.12 + "px", "padding": "0 " + size * 0.12 + "px"});
            jQuery(that).find(".apsa-center-logo-text").css({"font-size": size * 0.2 + "px"});
            jQuery(that).find(".apsa-center-logo>span>span").css({"font-size": size * 0.18 + "px"});
            jQuery(that).find(".apsa-center-shape").css({"height": size * 0.2 + "px", "width": size * 0.2 + "px"});
            jQuery(that).find(".apsa-font-logo").css({"height": size * 0.1 + "px", "width": size * 0.1 + "px"});

        }
        if (width < 135 && height > 400) {
            jQuery(that).find(".apsa-font-logo-wrap>div,.apsa-above-title>div,.apsa-below-title>div").css({"font-size": size * 0.08 + "px"});
            jQuery(that).find(".apsa-center-shape>span,.apsa-click-btn").css({"font-size": size * 0.07 + "px"});
            jQuery(that).find(".apsa-click-btn").css({"height": size * 0.14 + "px", "line-height": size * 0.14 + "px", "padding": "0 " + size * 0.14 + "px"});
            jQuery(that).find(".apsa-click-btn").css({"height": size * 0.14 + "px", "line-height": size * 0.14 + "px", "padding": "0 " + size * 0.05 + "px"});
            jQuery(that).find(".apsa-center-logo-text").css({"font-size": size * 0.3 + "px"});
            jQuery(that).find(".apsa-center-logo>span>span").css({"font-size": size * 0.25 + "px"});
            jQuery(that).find(".apsa-center-shape").css({"height": size * 0.3 + "px", "width": size * 0.3 + "px"});
            jQuery(that).find(".apsa-font-logo").css({"height": size * 0.12 + "px", "width": size * 0.12 + "px"});
        }
        if (height < 110 && width > 250 || (height > 250 && width < 100)) {
            if (width > 250) {
                jQuery(that).find(".apsa-template-body").addClass("apsa-remove-titles");
            }
            jQuery(that).find(".apsa-font-logo-wrap>div,.apsa-above-title>div,.apsa-below-title>div").css({"font-size": size * 0.15 + "px"});
            jQuery(that).find(".apsa-center-shape>span,.apsa-click-btn").css({"font-size": size * 0.09 + "px"});
            jQuery(that).find(".apsa-click-btn").css({"height": size * 0.18 + "px", "line-height": size * 0.18 + "px", "padding": "0 " + size * 0.18 + "px"});
            jQuery(that).find(".apsa-center-logo-text").css({"font-size": size * 0.4 + "px"});
            jQuery(that).find(".apsa-center-logo>span>span").css({"font-size": size * 0.35 + "px"});
            jQuery(that).find(".apsa-center-shape").css({"height": size * 0.35 + "px", "width": size * 0.35 + "px"});
            jQuery(that).find(".apsa-font-logo").css({"height": size * 0.22 + "px", "width": size * 0.22 + "px"});
        }
        if (height < 101 && width < 200) {
            jQuery(that).find(".apsa-template-body").addClass("apsa-remove-titles apsa-remove-btn");
            jQuery(that).on('click', function () {
                var link_type = jQuery(that).find('.apsa-click-btn').attr('target');
                var link_to = jQuery(that).find('.apsa-click-btn').attr('href');
                link_to = (link_type == "_self" && link_to == '') ? window.location.href : link_to;
                var location = (link_type == "_window") ? "scrollbars=yes,resizable=yes,toolbar=no,width=700,height=600" : null;
                window.open(link_to, link_type, location);
            })
            jQuery(that).find(".apsa-font-logo").css({"height": size * 0.08 + "px", "width": size * 0.08 + "px"});

        }
    }
    jQuery(that).find('.apsa-center-logo').each(function () {
        var out_of_box = parseFloat(jQuery(this).children('span').css('height')) - parseFloat(jQuery(this).css('height'));
        if (out_of_box > 0) {
            var percent = (out_of_box + parseFloat(jQuery(this).css('height'))) / parseFloat(jQuery(this).css('height'));
            var new_font_size = parseFloat(jQuery(this).find('.apsa-center-logo-text').css('font-size')) / percent;
            jQuery(this).find('.apsa-center-logo-text').css('font-size', new_font_size + 'px');

        }
    })

    if (jQuery(that).find(".apsa-click-btn").text() === '') {
        jQuery(that).find(".apsa-click-btn").css('display', 'none')
    }
    /*=====  template view calculation  ======*/
    if (arguments[1]) {
        jQuery(that).find('.apsa-template-body').css('visibility', 'visible');
    }
}


