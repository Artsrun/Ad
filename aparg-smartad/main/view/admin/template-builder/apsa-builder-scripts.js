/** Jquery template builder plugin */
(function ($) {
    var init_options = new Object;
    var init_block = new Object();

    /** Plugin solid defaults */
    var var_default_fields = {};
    var element_default_fields = {multi: '0'};

    // change options
    var apsa_change_called = false;
    var apsa_change_time = 0;

    // Textareas counter to set ids for using tinymce
    var textarea_c = 0;

    var tinymce_version = (typeof tinymce !== "undefined") ? tinymce.majorVersion : false;

    /**
     * Call on_change in a coherent manner
     */
    function apsa_on_change_timeout(item) {
        if (apsa_change_called == true) {
            clearTimeout(apsa_change_time);

            apsa_change_time = setTimeout(function () {
                init_options.on_change(item);
                apsa_change_called = false;
            }, 800);
        } else {
            apsa_change_called = true;

            apsa_change_time = setTimeout(function () {
                init_options.on_change(item);
                apsa_change_called = false;
            }, 800);
        }
    }

    /**
     * Remove element slug prefix
     */
    function apsa_remove_slug_prefix(el_slug) {

        var pf_index = el_slug.indexOf('apsa_');

        if (pf_index != -1 && pf_index != 0) {
            el_slug = el_slug.substring(el_slug.indexOf('apsa_') + 5);
        }

        return el_slug;
    }

    /**
     * Change element slugs for sort in template part
     */
    function apsa_regulate_element_slugs() {
        var slug_counter = 0;
        $('.apsa-template-cont .apsa-available-element').each(function () {
            var el_slug = $(this).attr('data-element-slug');

            var pf_index = el_slug.indexOf('apsa_');

            if (pf_index != -1) {
                el_slug = el_slug.substring(el_slug.indexOf('apsa_') + 5);
            }

            el_slug = slug_counter + 'apsa_' + el_slug;
            $(this).attr('data-element-slug', el_slug);

            slug_counter++;
        });
    }

    /**
     * Regulate element controls in template part
     */
    function apsa_regulate_element_controls() {
        // remove delete controls from unique required elements
        $('.apsa-template-cont .apsa-required-element').each(function () {

            var element_slug = apsa_remove_slug_prefix($(this).data('element-slug'));
            var similars_count = $('.apsa-template-cont').find('.apsa-' + element_slug).length;

            if (similars_count == 1) {
                $('.apsa-template-cont .apsa-' + element_slug + ' > .apsa-sliding-block > .apsa-element-control .apsa-remove-control').addClass('apsa-hidden-control');
            } else if (similars_count > 1) {
                $('.apsa-template-cont .apsa-' + element_slug).each(function () {
                    $(this).find('>.apsa-sliding-block > .apsa-element-control .apsa-remove-control').removeClass('apsa-hidden-control');
                });
            }
        });

        // remove delete controls from fixed elements
        $('.apsa-template-cont .apsa-fixed-element').each(function () {
            $(this).find('.apsa-remove-control').addClass('apsa-hidden-control');
        });
    }

    /**
     * Accurate builder floatings parts
     */
    function apsa_regulate_floating_parts() {
        // Check if builder loaded
        if ($(init_block).find('.apsa-builder-wrapper').length == 0) {
            return false;
        }

        /** regulate floating availables block */
        var float_availables = $(init_block).find('.apsa-availables-wrapper');
        var delete_area = $(init_block).find('.apsa-delete-area');

        var builder_top_static = $(init_block).find('.apsa-builder-wrapper').offset().top;
        var builder_top_viewport = builder_top_static - $(window).scrollTop();

        /** Make floating availables block */
        if ($(float_availables).outerHeight(true) + 64 < window.innerHeight && builder_top_viewport <= 64 && window.innerWidth >= 990) {
            if ($(init_block).find('.apsa-builder-wrapper').outerHeight(true) + builder_top_viewport <= $(float_availables).outerHeight(true) + 64) {
                $(float_availables).removeClass('apsa-float-top');
                $(float_availables).addClass('apsa-float-bottom');
                $(float_availables).css('top', "auto");
            } else {
                $(float_availables).removeClass('apsa-float-bottom');
                $(float_availables).addClass('apsa-float-top');
                $(float_availables).css('top', $(window).scrollTop() - builder_top_static + 64);
            }
        } else {
            // set width with %                
            $(float_availables).removeClass('apsa-float-bottom');
            $(float_availables).removeClass('apsa-float-top');
            $(float_availables).css('top', "auto");
        }

        /** Make floating availables delete araea */
        if ($(float_availables).outerHeight(true) + 64 < window.innerHeight && builder_top_viewport <= 32 && $(delete_area).outerHeight(true) + 32 >= $(window).outerHeight(true) && window.innerWidth >= 990) {
            if ($(init_block).find('.apsa-builder-wrapper').outerHeight(true) + builder_top_viewport <= $(delete_area).outerHeight(true) + 32) {
                $(delete_area).removeClass('apsa-float-top');
                $(delete_area).addClass('apsa-float-bottom');
            } else {
                $(delete_area).removeClass('apsa-float-bottom');
                $(delete_area).addClass('apsa-float-top');
                $(delete_area).css('top', $(window).scrollTop() - builder_top_static + 32);
            }
        } else {
            // set width with %                
            $(delete_area).removeClass('apsa-float-bottom');
            $(delete_area).removeClass('apsa-float-top');
            $(delete_area).css('top', "auto");
        }

    }

    /**
     * Revise builder parts styles during possible changes of sizes
     */
    function apsa_regulate_builder_styles() {
        var window_height = $(window).outerHeight(true);

        var availables_height = $('.apsa-availables-wrapper').outerHeight(true);
        $(init_block).find('.apsa-builder-wrapper').css('min-height', availables_height);
        $(init_block).find('.apsa-delete-area').css('max-height', window_height - 32);

        /** If template elements container is empty write message */
        $(init_block).find('.apsa-template-cont, .apsa-parent-cont').each(function () {
            if ($(this).find('li').length == 0) {
                $(this).addClass('apsa-empty-template');
                $(this).attr('data-apsa-text', init_options.labels.drop_here);
            } else {
                $(this).removeClass('apsa-empty-template');
                $(this).removeAttr('data-apsa-text');
            }
        });

        /** If abailable elements container is empty write message */
        $(init_block).find('.apsa-available-elements').each(function () {
            if ($(this).find('li').not('.apsa-unusable-element').length == 0) {
                $(this).addClass('apsa-empty-availables');
                $(this).attr('data-apsa-text', init_options.labels.no_more_elements);
            } else {
                $(this).removeClass('apsa-empty-availables');
                $(this).removeAttr('data-apsa-text');
            }
        });

        apsa_regulate_floating_parts();
    }

    /**
     * Delete template element
     */
    function apsa_delete_element(element) {
        if (tinymce_version === "4") {
            $(element).find('.apsa-richtextarea').each(function () {
                tinymce.execCommand('mceRemoveEditor', true, $(this).attr('id'));
            });
        }

        var element_slug = apsa_remove_slug_prefix($(element).data('element-slug'));

        // check if required element prevent removing from template    
        if (element.hasClass('apsa-required-element')) {
            var similars_count = $('.apsa-template-cont').find('.apsa-' + element_slug).length;

            if (similars_count <= 1) {
                return true;
            }
        }

        if (!element.hasClass('apsa-multi-element')) {
            // Determine apropriate connection class
            var connect_class = $(element).attr('data-connect-class');
            $('.apsa-available-elements.' + connect_class + ' .apsa-' + element_slug).removeClass('apsa-unusable-element');
        }

        $(element).find('.apsa-available-element').not('.apsa-multi-element').each(function () {
            //count in all template
            var child_element_slug = apsa_remove_slug_prefix($(this).data('element-slug'));
            var child_similars_count = $('.apsa-template-cont').find('.apsa-' + child_element_slug).length;
            var child_connect_class = $(this).attr('data-connect-class');

            if (child_similars_count === 1) {
                $('.apsa-available-elements.' + child_connect_class + ' .apsa-' + child_element_slug).removeClass('apsa-unusable-element');
            }
        });

        $(element).remove();

        apsa_regulate_element_controls();
        init_options.on_change();
    }

    /**
     * Add ids to template part textareas for using tinymce
     */
    function apsa_regulate_textarea_ids(area) {
        var new_ids = new Array();

        $(area).find('.apsa-richtextarea').each(function () {
            var mce_id = $(this).attr('id');

            if (typeof mce_id == "undefined") {
                textarea_c = textarea_c + 1;
                $(this).attr('id', 'apsa-mce-' + textarea_c);
                new_ids.push('apsa-mce-' + textarea_c);
            }
        });

        return new_ids
    }

    /**
     * Add timymce editor to elements within selected area
     */
    function apsa_add_editor(area) {

        if (tinymce_version !== "4") {
            return false;
        }

        var new_ids = apsa_regulate_textarea_ids($(area));

        if (new_ids.length === 0) {
            return false;
        }

        var new_ids_str = "#" + new_ids.join(", #");

        tinymce.init({
            selector: new_ids_str,
            menubar: false,
            min_height: 200,
            branding: false,
            setup: function (editor) {
                editor.on('change', function (e) {
                    apsa_on_change_timeout(editor);
                });
            },
        });
    }

    /**
     * Add colorpickers
     */
    function apsa_add_colorpicker(area) {
        /** Refresh colorpickers in this item */
        $(area).find('.apsa-colorpicker').each(function () {
            $(this).css('display', 'initial').removeClass('wp-color-picker');
            $(this).closest('.apsa-input-block').empty().append($(this));
        });

        $(area).find('.apsa-colorpicker').wpColorPicker({
            change: function (event, ui) {
                $(event.target).val(ui.color.toString());
                apsa_on_change_timeout($(event.target));
            },
            clear: function (event) {
                apsa_on_change_timeout($(event.target));
            }
        });
    }

    function apsa_child_sortable(scope, connect_class) {
        $(scope).sortable({
            connectWith: '.' + connect_class,
            cursor: "pointer",
            cancel: ".mce-resizehandle,input,textarea,button,select,option",
            items: "> li:not(.apsa-fixed-element)",
            update: function (event, ui) {
                if (apsa_change_called == true) {
                    clearTimeout(apsa_change_time);
                }
            },
            receive: function (event, ui) {
                if ($(this).hasClass('apsa-parent-cont')) {
                    var item_clone = $(ui.item).clone(true);

                    if (!$(ui.item).hasClass('apsa-multi-element')) {
                        $(item_clone).addClass('apsa-unusable-element');
                    }

                    // Save item clone in abailables block
                    item_clone.appendTo('.apsa-available-elements.' + connect_class);

                    $(ui.item).find('h3').first().removeClass("apsa-slide-noopen");
                    $(ui.item).find('h3').first().addClass("apsa-element-opener");

                    apsa_add_editor($(ui.item));
                    apsa_add_colorpicker($(ui.item));
                }
            },
            start: function (event, ui) {
                // check if required element prevent removing from template
                var element_slug = apsa_remove_slug_prefix($(ui.item).data('element-slug'));
                var similars_count = $('.apsa-template-cont').find('.apsa-' + element_slug).length;

                if ($(this).hasClass('apsa-parent-cont') && (!$(ui.item).hasClass('apsa-required-element') || similars_count > 2)) {
                    $(this).sortable("option", "axis", false);
                    $('.apsa-delete-area').addClass('apsa-waiting-delete');
                } else if ($(this).hasClass('apsa-parent-cont')) {
                    $(this).sortable("option", "axis", "y");
                    ui.item.data('unremovable', true);
                }
            },
            sort: function () {
                $('.apsa-template-cont .apsa-parent-cont' + '.' + connect_class).closest('.apsa-sliding-block').attr('data-apsa-open', "true");
                $('.apsa-template-cont .apsa-parent-cont' + '.' + connect_class).closest('.apsa-sliding-block').prev('.apsa-element-opener').attr('data-apsa-open-slide', true);
                $('.' + connect_class).addClass('apsa-waiting-sort');
                if ($(this).hasClass('apsa-available-elements') && $('.apsa-template-cont .' + connect_class).length == 0) {
                    $('.apsa-available-elements .' + connect_class).closest('.apsa-available-element').addClass('apsa-waiting-parent');
                }
            },
            stop: function (event, ui) {
                $('.' + connect_class).removeClass('apsa-waiting-sort');
                $('.apsa-available-elements .' + connect_class).closest('.apsa-available-element').removeClass('apsa-waiting-parent');
                if ($(this).hasClass('apsa-parent-cont')) {
                    $('.apsa-delete-area').removeClass('apsa-waiting-delete');

                    var delete_area = $(init_block).find('.apsa-delete-area');
                    var element_position = ui.offset;
                    var area_position = $(delete_area).offset();

                    var area_height = $(delete_area).outerHeight(true);
                    var area_width = $(delete_area).outerWidth(true);

                    if (ui.item.data('unremovable') !== true
                            && element_position.left > area_position.left
                            && element_position.left < area_position.left + area_width
                            && element_position.top > area_position.top
                            && element_position.top < area_position.top + area_height) {

                        var element = $(ui.item);
                        apsa_delete_element(element);
                        apsa_regulate_builder_styles();
                    } else {
                        if (ui.item.parent().hasClass('apsa-available-elements')) {
                            $(this).sortable('cancel');
                            var sort_canceled = true;
                        }

                        if (tinymce_version === "4") {
                            $(ui.item).find('.apsa-richtextarea').each(function () {
                                tinymce.execCommand('mceRemoveEditor', true, $(this).attr('id'));
                                tinymce.execCommand('mceAddEditor', true, $(this).attr('id'));
                            });
                        }
                    }
                }

                if (sort_canceled !== true) {
                    apsa_regulate_element_slugs();
                    apsa_regulate_element_controls();
                    apsa_regulate_builder_styles();
                    init_options.on_change($(ui.item));
                }
            }
        });
    }

    /**
     * template builder
     */
    $.fn.template_builder = function (options) {
        if (this.length == 0) {
            return undefined;
        }

        init_block = this;

        init_block.completed = false;

        /** Default options */
        init_options = $.extend({}, $.fn.template_builder.default_options, options);

        var labels = init_options.labels;

        /**
         * Draw vars form
         */
        function draw_vars_form(vars_slug, vars_group) {
            var vars_form = $('<form>', {class: 'apsa-var-options'}).attr('data-vars-slug', apsa_remove_slug_prefix(vars_slug));
            var vars_form_header = $('<h4>');

            var vars_label = (vars_group['label'] === undefined) ? labels['vars_group'] : vars_group['label'];
            vars_form_header.text(vars_label);

            vars_form_header.appendTo(vars_form);
            var var_options = vars_group['vars'];

            $.each(var_options, function (key, option_fields) {

                var option = $.extend({}, var_default_fields, option_fields);
                if (!option['form_type']) {
                    return true;
                }

                var option_html = $('<div>', {class: 'apsa-form-item'});

                if (option['form_type'] != 'hidden') {
                    option_html.append($('<span>', {text: option['label']}));

                    if (option['desc'] !== undefined) {
                        option_html.append($('<span>', {
                            class: "apsa-with-question",
                            title: labels['detailed_description']
                        }).attr('data-apsa-message', option['desc']));
                    }
                }

                // input filed warapper block
                var option_input = $('<div>', {class: 'apsa-input-block'}).appendTo(option_html);

                // input field class
                var option_class = (option['class'] !== undefined) ? option['class'] : '';

                // error message wrapper block
                $('<span>', {class: 'apsa-input-message'}).appendTo(option_html);

                /** Check form element type and draw element html */
                switch (option['form_type']) {
                    case "hidden":
                        var option_field_obj = $('<input>', {
                            type: 'hidden',
                            name: key,
                            value: option['value'],
                            class: 'apsa-element-option ' + option_class
                        });
                        option_field_obj.appendTo(option_input);
                        break;
                    case "text":
                        var option_field_obj = $('<input>', {
                            type: 'text',
                            name: key,
                            value: option['value'],
                            class: 'apsa-element-option ' + option_class
                        });
                        option_field_obj.appendTo(option_input);
                        break;
                    case "checkbox":
                        var option_field_obj = $('<input>', {
                            type: 'checkbox',
                            class: 'apsa-element-option ' + option_class
                        });
                        if (option['value'] == "true") {
                            option_field_obj.prop('checked', true);
                        } else {
                            option_field_obj.prop('checked', false);
                        }

                        var option_hidden_obj = $('<input>', {type: 'hidden', name: key, value: option['value']});

                        option_field_obj.appendTo(option_input);
                        option_hidden_obj.appendTo(option_input);
                        break;
                    case "radio":
                        $.each(option['values'], function (label, value) {
                            var option_field_obj = $('<input>', {
                                type: 'radio',
                                name: key,
                                value: value,
                                class: 'apsa-element-option ' + option_class
                            });

                            if (option['value'] == value) {
                                option_field_obj.prop('checked', true);
                            } else {
                                option_field_obj.prop('checked', false);
                            }

                            $('<span>', {text: label}).appendTo(option_input);
                            option_field_obj.appendTo(option_input);
                            $('<br>').appendTo(option_input);
                        });
                        break;
                    case "textarea":
                        var option_field_obj = $('<textarea>', {
                            name: key,
                            class: 'apsa-element-option ' + option_class
                        });

                        option_field_obj.text(option['value']);
                        option_field_obj.appendTo(option_input);
                        break;
                    case "richtextarea":
                        var option_field_obj = $('<textarea>', {
                            name: key,
                            class: 'apsa-element-option apsa-richtextarea ' + option_class
                        });

                        option_field_obj.text(option['value']);
                        option_field_obj.appendTo(option_input);
                        break;
                    case "select":
                        var option_field_obj = $('<select>', {name: key, class: 'apsa-element-option ' + option_class});
                        $.each(option['values'], function (label, value) {
                            var select_option_obj = $('<option>', {value: value});
                            select_option_obj.text(label);
                            if (option['value'] == value) {
                                select_option_obj.prop('selected', true);
                            } else {
                                select_option_obj.prop('selected', false);
                            }
                            select_option_obj.appendTo(option_field_obj);
                        });
                        option_field_obj.appendTo(option_input);
                        break;
                    case "image":
                        var choose_file_obj = $('<button>', {
                            type: 'button',
                            text: labels['choose_file'],
                            name: key,
                            value: option['value'],
                            class: 'apsa-upload-var-file button '
                        }).attr('data-apsa-file-type', "image");
                        var option_field_obj = $('<input>', {
                            type: 'text',
                            name: key,
                            value: option['value'],
                            class: 'apsa-hold-element-content apsa-element-option ' + option_class
                        });
                        $('<div>', {class: 'apsa-upload-wrap'}).append(choose_file_obj).appendTo(option_input);
                        $('<div>', {class: 'apsa-upload-input-wrap'}).append(option_field_obj).appendTo(option_input);
                        break;
                    case "file":
                        var choose_file_obj = $('<button>', {
                            type: 'button',
                            text: labels['choose_file'],
                            name: key,
                            value: option['value'],
                            class: 'apsa-upload-var-file button '
                        }).attr('data-apsa-file-type', "file");
                        var option_field_obj = $('<input>', {
                            type: 'text',
                            name: key,
                            value: option['value'],
                            class: 'apsa-hold-element-content apsa-element-option ' + option_class
                        });
                        $('<div>', {class: 'apsa-upload-wrap'}).append(choose_file_obj).appendTo(option_input);
                        $('<div>', {class: 'apsa-upload-input-wrap'}).append(option_field_obj).appendTo(option_input);
                        break;
                    case "color":
                        var option_field_obj = $('<input>', {
                            type: 'text',
                            name: key,
                            value: option['value'],
                            class: 'apsa-extra-small-input apsa-colorpicker apsa-element-option ' + option_class
                        });
                        option_field_obj.appendTo(option_input);
                        break;
                    default :
                        option_input = "";
                }

                var var_option = $('<div>', {class: 'apsa-var-option'});
                if (option['form_type'] == 'hidden') {
                    var_option.addClass('apsa-hidden-var');
                }

                var_option.append(option_html).appendTo(vars_form);
            });

            return vars_form;
        }

        /**
         * Draw element options html
         */
        function draw_element_options(el_options) {

            var var_groups = $.extend(true, {}, el_options['var_groups']);

            var html = $('<div>', {class: "apsa-element-var-forms"});

            /**Display group forms  */
            var groups = el_options['groups'];
            var choosen_groups = el_options['choosen_groups'];

            if (groups != undefined) {
                $.each(groups, function (group_key, group) {

                    var forms_group = $('<div>', {class: "apsa-group-var-forms"});

                    // make vars form changer in group
                    var form_changer = $('<select>', {name: group_key, class: 'apsa-form-changer'});
                    $.each(group, function (key, group_slug) {
                        var option_name = el_options['var_groups'][group_slug]['label'];

                        var select_form_obj = $('<option>', {value: group_slug});
                        select_form_obj.text(option_name);
                        select_form_obj.appendTo(form_changer);
                    });
                    form_changer.prependTo(forms_group);

                    /** Show choosen group if exists else first for each group */
                    if (choosen_groups != undefined) {
                        var group_slug = choosen_groups[group_key];

                        form_changer.val(group_slug);

                        var vars_group = var_groups[group_slug];

                        var vars_form = draw_vars_form(group_slug, vars_group);

                        vars_form.appendTo(forms_group);
                    } else {
                        var group_slug = group[0];

                        form_changer.val(group_slug);

                        var vars_group = var_groups[group_slug];

                        var vars_form = draw_vars_form(group_slug, vars_group);

                        vars_form.appendTo(forms_group);
                    }

                    $.each(group, function (key, group_slug) {

                        delete var_groups[group_slug];
                    });

                    forms_group.appendTo(html);
                });
            }

            /** Display single forms */
            $.each(var_groups, function (key, vars_group) {
                var vars_form = draw_vars_form(key, vars_group);
                vars_form.appendTo(html);
            });

            return html;
        }

        /**
         * Initaializing template builder with options and default/saved elements
         */
        function init_builder(unfilter_availables, saved_elements) {

            /**  Extend saved elements from availables */

            var not_multies_flag = [];

            for (var i in saved_elements) {
                var slug = apsa_remove_slug_prefix(i);

                // Check if element removed from template config or here in after not multi, remove from saved data
                if (!unfilter_availables[slug] || not_multies_flag.indexOf(slug) != -1) {
                    delete(saved_elements[i]);
                    continue;
                }

                saved_elements[i] = $.extend(true, {}, unfilter_availables[slug], saved_elements[i]);

                if (saved_elements[i]['multi'] != "1")
                    not_multies_flag.push(slug);

                /** Change var values if not hidden */
                var var_groups = saved_elements[i]['var_groups'];

                $.map(var_groups, function (vars_group, group_slug) {
                    if (!unfilter_availables[slug]['var_groups'][group_slug]) {
                        delete var_groups[group_slug];
                        return true;
                    }

                    var vars = vars_group['vars'];
                    $.map(vars, function (var_options, var_slug) {
                        // Check if hidden with available element
                        var var_form_type = var_options['form_type'];

                        if (var_form_type == "hidden") {
                            var var_val = unfilter_availables[slug]['var_groups'][group_slug]['vars'][var_slug]['value'];
                            if (var_val == undefined)
                                var_val = "";

                            var_options['value'] = var_val;
                        }
                    });
                });
            }

            var availables = $.extend(true, {}, unfilter_availables);

            saved_elements = ((saved_elements == undefined) ? {} : saved_elements);

            /** Construct available elements block */
            var availables_block_obj = $('<div>', {class: "apsa-availables-wrapper apsa-builder-part"});
            availables_block_obj.append($('<div>', {class: "apsa-delete-area"}));
            availables_block_obj.append($('<h2>' + labels['available_elements'] + '</h2>'));

            this.available_elements = $('<ul>', {class: "apsa-available-elements apsa-connect-sortables"});

            /** Check if required elements in available elements, if exists, ad to saved elements */

            var req_elements = {};
            var fixed_elements = {};

            gin_requireds_loop: for (i in availables) {

                var ext_element_data = $.extend({}, element_default_fields, availables[i]);
                availables[i] = ext_element_data;

                var gin_element_data = availables[i];

                /** Check if fixed, change options to fixed elements defaults */
                if (gin_element_data['fixed'] == "1") {
                    gin_element_data['multi'] = "0";
                    gin_element_data['required'] = "1";

                    availables[i] = gin_element_data;
                }

                if (gin_element_data['required'] != "1") {
                    continue;
                }

                for (var j in saved_elements) {

                    if (apsa_remove_slug_prefix(i) == apsa_remove_slug_prefix(j)) {
                        continue gin_requireds_loop;
                    }
                }

                if (gin_element_data['fixed'] == "1") {
                    fixed_elements[i] = gin_element_data;
                } else {
                    req_elements[i] = gin_element_data;
                }
            }

            var fixed_req_elements = $.extend(true, {}, fixed_elements, req_elements);
            var all_saved_elements = $.extend(true, {}, fixed_req_elements, saved_elements);

            /** Move all fixed elements to top of array */
            var all_fixed_elements = {};
            var all_saved_without_fixed = {};

            $.each(all_saved_elements, function (key, value) {
                if (value['fixed'] == "1") {
                    all_fixed_elements[key] = value;
                } else {
                    all_saved_without_fixed[key] = value;
                }
            });

            all_saved_elements = $.extend(true, {}, all_fixed_elements, all_saved_without_fixed);

            // Hold two arrays lenght for further comparing
            var saved_lenght = JSON.stringify(saved_elements);
            var all_saved_lenght = JSON.stringify(all_saved_elements);

            saved_elements = all_saved_elements;

            /** Check if element saved or not allowed multi choose, if true continue */

            // separate child and parent element slugs
            var child_elements = [];
            var parent_elements = [];
            $.each(unfilter_availables, function (slug, element) {
                if (element['childrens'] != undefined) {

                    // check if string make array with single element
                    if (typeof element['childrens'] == 'string') {
                        element['childrens'] = [element['childrens']];
                    }

                    child_elements = child_elements.concat(element['childrens']);
                    parent_elements.push(slug);
                }
            });

            // Separate elements which are not saved, or saved but multiple
//            for (var i in availables) {
//                for (j in saved_elements) {
//                    if (apsa_remove_slug_prefix(i) == apsa_remove_slug_prefix(j) && availables[apsa_remove_slug_prefix(i)]['multi'] == "0") {
//
//                        delete availables[apsa_remove_slug_prefix(i)];
//                    }
//                }
//            }

            // display usual availables
            for (var i in availables) {

                var element_data = availables[i];

                if (child_elements.indexOf(apsa_remove_slug_prefix(i)) !== -1) {
                    continue;
                }

                var available_block = $('<li>', {
                    'data-element-slug': apsa_remove_slug_prefix(i),
                    'data-connect-class': 'apsa-connect-sortables',
                    'data-element-data': JSON.stringify(element_data),
                    class: 'apsa-available-element apsa-slide-cont apsa-item-part apsa-' + apsa_remove_slug_prefix(i)
                });

                for (j in saved_elements) {
                    if (apsa_remove_slug_prefix(i) == apsa_remove_slug_prefix(j) && availables[apsa_remove_slug_prefix(i)]['multi'] == "0") {

                        $(available_block).addClass('apsa-unusable-element');
                    }
                }

                if (element_data['multi'] == "1") {
                    available_block.addClass('apsa-multi-element');
                }

                if (element_data['required'] == "1") {
                    available_block.addClass('apsa-required-element');
                }

                var element_head = $('<h3>', {class: 'apsa-slide-noopen', 'data-apsa-open-slide': 'false'});
                element_head.append($('<span>' + element_data['label'] + '</span>'));
                element_head.append($('<span>', {class: "apsa-slide-open-pointer"}));
                element_head.appendTo(available_block);

                var element_cont = $('<div>', {
                    class: 'apsa-sliding-block',
                    'data-apsa-open': 'false'
                }).append(draw_element_options(element_data));

                if (element_data['childrens'] != undefined) {
                    var parent_childs = $('<ul>', {class: "apsa-parent-cont apsa-child-elemnts"});
                    parent_childs.addClass('apsa-' + apsa_remove_slug_prefix(i) + '-childs');
                    parent_childs.attr('data-class-holder', 'apsa-' + apsa_remove_slug_prefix(i) + '-childs');
                    parent_childs.appendTo(element_cont);
                }

                var element_control = $('<div>', {class: 'apsa-element-control'});
                element_control.append($('<span>', {
                    class: "apsa-remove-control apsa-delete-element",
                    text: labels['delete']
                }));
                element_control.appendTo(element_cont);

                element_cont.appendTo(available_block);

                available_block.appendTo(this.available_elements);
            }

            this.available_elements.appendTo(availables_block_obj);

            //show parent blocks
            for (var i in parent_elements) {

                // Show child blocks into parent block

                var parent_data = unfilter_availables[parent_elements[i]];

                var child_elements_cont = $('<ul>', {class: "apsa-available-elements apsa-child-elemnts"});
                child_elements_cont.addClass('apsa-' + parent_elements[i] + '-childs');
                child_elements_cont.attr('data-class-holder', 'apsa-' + parent_elements[i] + '-childs');

                var childs = parent_data['childrens'];
                for (j in childs) {
                    var child_data = unfilter_availables[childs[j]];

                    if (typeof availables[childs[j]] == 'undefined') {
                        continue;
                    }

                    var available_block = $('<li>', {
                        'data-element-slug': apsa_remove_slug_prefix(childs[j]),
                        'data-connect-class': 'apsa-' + parent_elements[i] + '-childs',
                        'data-element-data': JSON.stringify(child_data),
                        class: 'apsa-available-element apsa-slide-cont apsa-item-part apsa-' + apsa_remove_slug_prefix(childs[j])
                    });

                    for (var k in saved_elements) {
                        if (apsa_remove_slug_prefix(childs[j]) == apsa_remove_slug_prefix(k) && availables[apsa_remove_slug_prefix(childs[j])]['multi'] == "0") {

                            $(available_block).addClass('apsa-unusable-element');
                        }
                    }

                    if (child_data['multi'] == "1") {
                        available_block.addClass('apsa-multi-element');
                    }

                    if (child_data['required'] == "1") {
                        available_block.addClass('apsa-required-element');
                    }

                    var element_head = $('<h3>', {class: 'apsa-slide-noopen', 'data-apsa-open-slide': 'false'});
                    element_head.append($('<span>' + child_data['label'] + '</span>'));
                    element_head.append($('<span>', {class: "apsa-slide-open-pointer"}));
                    element_head.appendTo(available_block);

                    var element_cont = $('<div>', {
                        class: 'apsa-sliding-block',
                        'data-apsa-open': 'false'
                    }).append(draw_element_options(child_data));

                    var element_control = $('<div>', {class: 'apsa-element-control'});
                    element_control.append($('<span>', {
                        class: "apsa-remove-control apsa-delete-element",
                        text: labels['delete']
                    }));
                    element_control.appendTo(element_cont);

                    element_cont.appendTo(available_block);

                    available_block.appendTo(child_elements_cont);
                }

                var child_elements_wrapper = $('<div>', {class: "apsa-childs-wrapper"}).append(child_elements_cont).prepend('<h2>' + parent_data['label'] + ' ' + labels['child_elements'] + '</h2>');
                child_elements_wrapper.appendTo(availables_block_obj);
            }

            /** Construct template block */
            var template_block_obj = $('<div>', {class: "apsa-template-wrapper apsa-builder-part"});
            template_block_obj.append('<h2>' + labels['template'] + '</h2>');

            this.template_elements = $('<ul>', {class: "apsa-template-cont apsa-connect-sortables"});

            // make slugs array for correct sorting
            var saved_slugs = [];
            for (slug in saved_elements) {
                saved_slugs.push(slug);
            }

            // sort slugs
            saved_slugs.sort(function (a, b) {

                var a_pf_index = a.indexOf('apsa_');
                var a_num = parseInt(a.substring(0, a_pf_index + 5));

                var b_pf_index = b.indexOf('apsa_');
                var b_num = parseInt(b.substring(0, b_pf_index + 5));

                return ((a_num < b_num) ? -1 : ((a_num > b_num) ? 1 : 0));
            });

            for (i in saved_slugs) {
                var el_slug = saved_slugs[i];

                if (child_elements.indexOf(apsa_remove_slug_prefix(el_slug)) !== -1) {
                    continue;
                }

                /** Set default values for required fields of element */
                var saved_data = saved_elements[el_slug];

                var element_block = $('<li>', {
                    'data-element-slug': apsa_remove_slug_prefix(el_slug),
                    'data-connect-class': 'apsa-connect-sortables',
                    'data-element-data': JSON.stringify(saved_data),
                    class: 'apsa-available-element apsa-slide-cont apsa-item-part apsa-' + apsa_remove_slug_prefix(el_slug)
                });
                if (saved_data['multi'] == "1") {
                    element_block.addClass('apsa-multi-element');
                }
                if (saved_data['required'] == "1") {
                    element_block.addClass('apsa-required-element');
                }
                if (saved_data['fixed'] == "1") {
                    element_block.addClass('apsa-fixed-element');
                }

                var element_head = $('<h3>', {class: 'apsa-element-opener', 'data-apsa-open-slide': 'false'});
                element_head.append($('<span>' + saved_data['label'] + '</span>'));
                element_head.append($('<span>', {class: "apsa-slide-open-pointer"}));
                element_head.appendTo(element_block);

                var element_cont = $('<div>', {
                    class: 'apsa-sliding-block',
                    'data-apsa-open': 'false'
                }).append(draw_element_options(saved_data));

                // check if element is parent, add space for childs
                if (saved_data['childrens'] != undefined) {
                    var parent_childs = $('<ul>', {class: "apsa-parent-cont apsa-child-elemnts"});
                    parent_childs.addClass('apsa-' + apsa_remove_slug_prefix(el_slug) + '-childs');
                    parent_childs.attr('data-class-holder', 'apsa-' + apsa_remove_slug_prefix(el_slug) + '-childs');

                    /**
                     * Make aray of showing childs rom saved data and required elements
                     */
                    var show_childs = [];

                    if (saved_data['choosen_childs'] != undefined) {
                        show_childs = show_childs.concat(saved_data['choosen_childs']);
                    }

                    if (typeof saved_data['childrens'] == "string") {
                        saved_data['childrens'] = [saved_data['childrens']];
                    }

                    for (var j in saved_data['childrens']) {
                        if (req_elements[saved_data['childrens'][j]] != undefined) {
                            show_childs.push(saved_data['childrens'][j]);
                        }
                    }

                    if (show_childs.length != 0) {
                        for (i in show_childs) {
                            var child_data = saved_elements[show_childs[i]];

                            var child_block = $('<li>', {
                                'data-element-slug': apsa_remove_slug_prefix(show_childs[i]),
                                'data-connect-class': 'apsa-' + apsa_remove_slug_prefix(el_slug) + '-childs',
                                'data-element-data': JSON.stringify(child_data),
                                class: 'apsa-available-element apsa-slide-cont apsa-item-part apsa-' + apsa_remove_slug_prefix(show_childs[i])
                            });
                            if (child_data['multi'] == "1") {
                                child_block.addClass('apsa-multi-element');
                            }
                            if (child_data['required'] == "1") {
                                child_block.addClass('apsa-required-element');
                            }
                            if (child_data['fixed'] == "1") {
                                child_block.addClass('apsa-fixed-element');
                            }

                            var child_head = $('<h3>', {class: 'apsa-element-opener', 'data-apsa-open-slide': 'false'});
                            child_head.append($('<span>' + child_data['label'] + '</span>'));
                            child_head.append($('<span>', {class: "apsa-slide-open-pointer"}));
                            child_head.appendTo(child_block);

                            var child_cont = $('<div>', {
                                class: 'apsa-sliding-block',
                                'data-apsa-open': 'false'
                            }).append(draw_element_options(child_data));

                            var element_control = $('<div>', {class: 'apsa-element-control'});
                            element_control.append($('<span>', {
                                class: "apsa-remove-control apsa-delete-element",
                                text: labels['delete']
                            }));
                            element_control.appendTo(child_cont);

                            child_cont.appendTo(child_block);
                            child_block.appendTo(parent_childs);
                        }
                    }

                    parent_childs.appendTo(element_cont);
                }

                var element_control = $('<div>', {class: 'apsa-element-control'});
                element_control.append($('<span>', {
                    class: "apsa-remove-control apsa-delete-element",
                    text: labels['delete']
                }));
                element_control.appendTo(element_cont);

                element_cont.appendTo(element_block);

                element_block.appendTo(this.template_elements);
            }

            this.template_elements.appendTo(template_block_obj);

            /** Construct builder content block */
            var builder_content = $('<div>', {class: "apsa-builder-wrapper apsa-floating-parts"});
            builder_content.append(availables_block_obj);
            builder_content.append(template_block_obj);

            init_block.empty();
            init_block.append(builder_content);

            //revrite template elements slugs
            apsa_regulate_element_slugs();
            apsa_regulate_element_controls();
            apsa_regulate_builder_styles();

            if (saved_lenght !== all_saved_lenght) {
                init_options.on_change();
            }

            /** After constructing builder content make elements sortable */
            $([this.available_elements, this.template_elements]).sortable({
                connectWith: ".apsa-connect-sortables",
                cursor: "pointer",
                cancel: ".mce-resizehandle,input,textarea,button,select,option",
                items: "> li:not(.apsa-fixed-element)",
                update: function (event, ui) {
                    if (apsa_change_called == true) {
                        clearTimeout(apsa_change_time);
                    }
                },
                receive: function (event, ui) {
                    if ($(this).hasClass('apsa-template-cont')) {
                        var item_clone = $(ui.item).clone(true);

                        if (!$(ui.item).hasClass('apsa-multi-element')) {
                            $(item_clone).addClass('apsa-unusable-element');
                        }

                        // Save item clone in abailables block
                        item_clone.appendTo('.apsa-available-elements.apsa-connect-sortables');

                        $(ui.item).find('h3').first().removeClass("apsa-slide-noopen");
                        $(ui.item).find('h3').first().addClass("apsa-element-opener");

                        // Determine apropriate connection class
                        var connect_class = $(ui.item).find('.apsa-child-elemnts').attr('data-class-holder');

                        apsa_child_sortable($(ui.item).find('.apsa-child-elemnts'), connect_class);

                        apsa_add_editor($(ui.item));
                        apsa_add_colorpicker($(ui.item));
                    }
                },
                sort: function (event, ui) {
                    $('.apsa-template-cont').addClass('apsa-waiting-sort');
                },
                start: function (event, ui) {
                    // check if required element prevent removing from template
                    var element_slug = apsa_remove_slug_prefix($(ui.item).data('element-slug'));
                    var similars_count = $('.apsa-template-cont').find('.apsa-' + element_slug).length;

                    if ($(this).hasClass('apsa-template-cont') && (!$(ui.item).hasClass('apsa-required-element') || similars_count > 2)) {
                        $(this).sortable("option", "axis", false);
                        $('.apsa-delete-area').addClass('apsa-waiting-delete');
                    } else if ($(this).hasClass('apsa-template-cont')) {
                        $(this).sortable("option", "axis", "y");
                        ui.item.data('unremovable', true);
                    }
                },
                stop: function (event, ui) {
                    $('.apsa-template-cont').removeClass('apsa-waiting-sort');
                    if ($(this).hasClass('apsa-template-cont')) {
                        $('.apsa-delete-area').removeClass('apsa-waiting-delete');

                        var delete_area = $(init_block).find('.apsa-delete-area');

                        var element_position = ui.offset;
                        var area_position = $(delete_area).offset();

                        var area_height = $(delete_area).outerHeight(true);
                        var area_width = $(delete_area).outerWidth(true);

                        if (ui.item.data('unremovable') !== true
                                && element_position.left > area_position.left
                                && element_position.left < area_position.left + area_width
                                && element_position.top > area_position.top
                                && element_position.top < area_position.top + area_height) {

                            var element = $(ui.item);
                            apsa_delete_element(element);
                            apsa_regulate_builder_styles();
                        } else {
                            if (ui.item.parent().hasClass('apsa-available-elements')) {
                                $(this).sortable('cancel');
                                var sort_canceled = true;
                            }

                            if (tinymce_version === "4") {
                                $(ui.item).find('.apsa-richtextarea').each(function () {
                                    tinymce.execCommand('mceRemoveEditor', true, $(this).attr('id'));
                                    tinymce.execCommand('mceAddEditor', true, $(this).attr('id'));
                                });
                            }
                        }
                    }

                    if (sort_canceled !== true) {
                        init_options.on_change($(ui.item));
                        apsa_regulate_element_slugs();
                        apsa_regulate_element_controls();
                        apsa_regulate_builder_styles();
                    }
                }
            });

            // make child elements sortable
            $('.apsa-childs-wrapper .apsa-child-elemnts, .apsa-template-wrapper .apsa-child-elemnts').each(function () {

                // Determine apropriate connection class
                var connect_class = $(this).attr('data-class-holder');

                apsa_child_sortable($(this), connect_class);
            });

            // add colorpicker
            apsa_add_colorpicker('.apsa-template-wrapper');

            apsa_regulate_textarea_ids('.apsa-template-wrapper');

            //Check if richtextareas exists
            var richs_count = $('.apsa-template-wrapper .apsa-richtextarea').length;

            if (tinymce_version !== "4" || richs_count === 0) {
                init_block.completed = true;
                init_options.on_complete();
            } else {
                tinymce.init({
                    selector: '.apsa-template-wrapper .apsa-richtextarea',
                    menubar: false,
                    min_height: 200,
                    branding: false,
                    setup: function (editor) {
                        editor.on('change', function (e) {
                            apsa_on_change_timeout(editor);
                        });
                    },
                }).then(function () {
                    init_block.completed = true;
                    init_options.on_complete();
                });
            }
        }

        /**
         * Collect data and return
         */
        function save_data() {
            var saved_data = {};

            if (init_block.completed != true) {
                return saved_data;
            }

            /** Overwrite textareas contents */
            if (tinymce_version === "4") {
                // Save editors contents in textareas
                tinymce.triggerSave();
            }

            this.template_elements.find('.apsa-available-element').each(function () {

                // fully extend element data for avoid duplication of data
                var el_data_full = $.extend(true, {}, $(this).data('element-data'));
                var el_data = {var_groups: el_data_full['var_groups']};

                var el_slug = $(this).attr('data-element-slug');
                var el_vars = el_data['var_groups'];

                // check if have child save as attribute child elements slugs
                if ($(this).find('.apsa-available-element').length > 0) {
                    el_data['choosen_childs'] = [];

                    $(this).find('.apsa-available-element').each(function () {
                        el_data['choosen_childs'].push($(this).attr('data-element-slug'));
                    });
                } else {
                    delete el_data['choosen_childs'];
                }

                $(this).find('> .apsa-sliding-block > .apsa-element-var-forms form').each(function () {
                    var vars_slug = $(this).attr('data-vars-slug');

                    if (tinymce_version == "4") {
                        $(this).find('.apsa-richtextarea').each(function () {
                            var object = $('<div>' + $(this).val() + '</div>');
                            object.find('.apsa-reset-stop').each(function () {
                                $(this).replaceWith($(this).html());
                            });
                            $(this).val('<div class="apsa-reset-stop">' + object.html() + '</div>');
                        });
                    }

                    var form_data = $(this).serializeArray();

                    $.each(form_data, function (key, option) {
                        el_vars[vars_slug]['vars'][option['name']] = {'value': option['value']};
                    });
                });

                var choosen_groups = {};
                $(this).find('> .apsa-sliding-block > .apsa-element-var-forms .apsa-form-changer').each(function () {
                    var group_key = $(this).attr('name');

                    choosen_groups[group_key] = $(this).val();
                });
                if (!$.isEmptyObject(choosen_groups)) {
                    el_data['choosen_groups'] = choosen_groups;
                }

                $(this).attr('data-element-data', JSON.stringify(el_data));

                saved_data[el_slug] = el_data;
            });

            init_options.on_save(saved_data);

            return saved_data;
        }

        function change_template(item) {
            init_options.on_change(item);
        }

        init_block.data('template_builder', builder);

        /** Switch vars group */
        $(document).on('change', '.apsa-form-changer', function () {
            var el_data = $(this).closest('.apsa-available-element').data('element-data');
            var el_vars = el_data['var_groups'];
            var choosen_group = $(this).val();

            var vars_form = draw_vars_form(choosen_group, el_vars[choosen_group]);

            $(this).closest('.apsa-group-var-forms').find('.apsa-var-options').remove();
            $(this).closest('.apsa-group-var-forms').append(vars_form);

            apsa_add_colorpicker($(this).closest('.apsa-available-element'));

            apsa_add_editor($(this).closest('.apsa-available-element'));
        });

        var builder = {
            init_builder: init_builder,
            save_data: save_data,
            change_template: change_template
        }

        return builder;
    }

    /** Plugin defaults – added as a property on our plugin function.*/
    $.fn.template_builder.default_options = {
        on_change: function () {
        },
        on_save: function () {
        },
        on_complete: function () {
        },
        labels: {
            available_elements: "Available Elements",
            template: "Template Elements",
            save_element: "Save",
            vars_group: "Variables Group",
            detailed_description: "Detailed description.",
            child_elements: "child elements",
            choose_file: "Choose",
            delete: "Delete",
            drop_here: "Drop here",
            no_more_elements: "No more elements"
        },
    };

    /** Checkbox selection */
    $(document).on('change', '.apsa-var-option [type="checkbox"]', function () {
        var is_cheked = $(this).prop('checked');

        $(this).next('[type="hidden"]').val(is_cheked);
    });

    /** call on_change method during element form changing */
    $(document).on('change keyup', '.apsa-form-changer, .apsa-var-option input, .apsa-var-option textarea, .apsa-var-option select', function (e) {
        if (e.type == "keyup") {
            apsa_on_change_timeout($(this));
        } else {
            if (apsa_change_called == true) {
                clearTimeout(apsa_change_time);
            }
            init_options.on_change($(this));
        }
    });

    /** prevent form submition on enter key */
    $(document).on('submit', '.apsa-var-options', function (e) {
        e.preventDefault();
        return false;
    });

    /*
     * Add file uploader from media library
     */
    $(document).on('click', '.apsa-upload-var-file', function (e) {

        that_button = $(this);
        e.preventDefault();

        // Extend the wp.media object
        var search_type = that_button.attr('data-apsa-file-type');
        if (search_type == "file") {
            search_type = "";
        }

        if (search_type == 'application')
            search_type = search_type + '/x-shockwave-flash';
        else if (search_type != "") {
            search_type = search_type + '/';
        }

        var add_file_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose file',
            button: {
                text: 'Choose file'
            },
            library: {type: search_type},
            multiple: false
        });

        // When a file is selected, grab required info about that file
        add_file_uploader.on('select', function () {
            var attachment = add_file_uploader.state().get('selection').first().toJSON();
            /** Check if required type of file choosen */

            if (attachment.type == that_button.attr('data-apsa-file-type') || that_button.attr('data-apsa-file-type') == "file") {
                if (attachment.type == 'application' && attachment.subtype != 'x-shockwave-flash') {
                    apsa_action_message("error", apsa_admin_labels["unc_type_of_file_msg"]);
                    return false;
                }
                var file_url = attachment.url;
                that_button.closest('.apsa-form-item').find('.apsa-hold-element-content').val(file_url).trigger('change');
            } else {
                apsa_action_message("error", apsa_admin_labels["unc_type_of_file_msg"]);
            }
            return false;
        });
        add_file_uploader.on('close', function () {
            add_file_uploader.remove();
        });
        // Open the uploader dialog
        add_file_uploader.open();
    });

    /**
     * Element opener functional
     */
    $(document).on('click', '.apsa-element-opener', function () {

        if ($(this).siblings('.apsa-sliding-block').attr('data-apsa-open') == 'false') {
            $(this).siblings('.apsa-sliding-block').attr('data-apsa-open', "true");
        } else {
            $(this).siblings('.apsa-sliding-block').attr('data-apsa-open', "false");
        }

        $(this).attr("data-apsa-open-slide", $(this).attr("data-apsa-open-slide") == "false" ? "true" : "false");
    });

    /**
     * Remove elemet
     */
    $(document).on('click', '.apsa-delete-element', function () {
        var element = $(this).closest('.apsa-available-element');

        apsa_delete_element(element);
        apsa_regulate_builder_styles();
    });

    /**
     * Window scrool events
     */
    $(window).on('scroll resize', function () {
        apsa_regulate_floating_parts();
    });

}(jQuery));