(function ($) {
    "use strict";

    var ajaxUrl = fancyforms_admin_js_obj.ajax_url;
    var adminNonce = fancyforms_admin_js_obj.ajax_nonce;

    $('.fancyforms-color-picker').wpColorPicker({
        change: function (event, ui) {
            var element = $(event.target).closest('.wp-picker-input-wrap').find('.wp-color-picker');
            if (element) {
                setTimeout(function () {
                    element.trigger('change');
                }, 100);
            }
        },
        clear: function (event) {
            var element = $(event.target).closest('.wp-picker-input-wrap').find('.wp-color-picker');
            if (element) {
                setTimeout(function () {
                    element.trigger('change');
                }, 100);
            }
        }
    });

    // Call all the necessary functions for Icon Picker
    $('body').on('click', '.fancyforms-icon-box-wrap .fancyforms-icon-list li', function () {
        var icon_class = $(this).find('i').attr('class');
        $(this).closest('.fancyforms-icon-box').find('.fancyforms-icon-list li').removeClass('icon-active');
        $(this).addClass('icon-active');
        $(this).closest('.fancyforms-icon-box').prev('.fancyforms-selected-icon').children('i').attr('class', '').addClass(icon_class);
        $(this).closest('.fancyforms-icon-box').next('input').val(icon_class).trigger('change');
        $(this).closest('.fancyforms-icon-box').slideUp();
    });

    $('body').on('click', '.fancyforms-icon-box-wrap .fancyforms-selected-icon', function () {
        $(this).next().slideToggle();
    });

    $('body').on('change', '.fancyforms-icon-box-wrap .fancyforms-icon-search select', function () {
        var selected = $(this).val();
        $(this).parents('.fancyforms-icon-box').find('.fancyforms-icon-search-input').val('');
        $(this).parents('.fancyforms-icon-box').children('.fancyforms-icon-list').hide().removeClass('active');
        $(this).parents('.fancyforms-icon-box').children('.' + selected).fadeIn().addClass('active');
        $(this).parents('.fancyforms-icon-box').children('.' + selected).find('li').show();
    });

    $('body').on('keyup', '.fancyforms-icon-box-wrap .fancyforms-icon-search input', function (e) {
        var $input = $(this);
        var keyword = $input.val().toLowerCase();
        var search_criteria = $input.closest('.fancyforms-icon-box').find('.fancyforms-icon-list.active i');
        delay(function () {
            $(search_criteria).each(function () {
                if ($(this).attr('class').indexOf(keyword) > -1) {
                    $(this).parent().show();
                } else {
                    $(this).parent().hide();
                }
            });
        }, 500);
    });

    var delay = (function () {
        var timer = 0;
        return function (callback, ms) {
            clearTimeout(timer);
            timer = setTimeout(callback, ms);
        };
    })();

    // Range JS
    $('.fancyforms-range-input-selector').each(function () {
        var newSlider = $(this);
        var sliderValue = newSlider.val();
        var sliderMinValue = parseFloat(newSlider.attr('min'));
        var sliderMaxValue = parseFloat(newSlider.attr('max'));
        var sliderStepValue = parseFloat(newSlider.attr('step'));
        newSlider.prev('.fancyforms-range-slider').slider({
            value: sliderValue,
            min: sliderMinValue,
            max: sliderMaxValue,
            step: sliderStepValue,
            range: 'min',
            slide: function (e, ui) {
                $(this).next().val(ui.value).trigger('change');
            }
        });
    });

    // Update slider if the input field loses focus as it's most likely changed
    $('.fancyforms-range-input-selector').blur(function () {
        var resetValue = isNaN($(this).val()) ? '' : $(this).val();

        if (resetValue) {
            var sliderMinValue = parseFloat($(this).attr('min'));
            var sliderMaxValue = parseFloat($(this).attr('max'));
            // Make sure our manual input value doesn't exceed the minimum & maxmium values
            if (resetValue < sliderMinValue) {
                resetValue = sliderMinValue;
                $(this).val(resetValue);
            }
            if (resetValue > sliderMaxValue) {
                resetValue = sliderMaxValue;
                $(this).val(resetValue);
            }
        }
        $(this).val(resetValue);
        $(this).prev('.fancyforms-range-slider').slider('value', resetValue);
    });

    // Show/ Hide Single Page Options
    $(document).on('change', '.fancyforms-typography-font-family', function () {
        var $this = $(this);
        var font_family = $(this).val();
        var standard_fonts = ['Default', 'Helvetica', 'Verdana', 'Arial', 'Times', 'Georgia', 'Courier', 'Trebuchet', 'Tahoma', 'Palatino'];
        if (!standard_fonts.includes(font_family)) {
            var fontId = $this.attr('id');
            var $fontId = $('link#' + fontId);

            if ($fontId.length > 0) {
                $fontId.remove();
            }
            $('head').append('<link rel="stylesheet" id="' + fontId + '" href="https://fonts.googleapis.com/css?family=' + font_family + ':100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i&subset=latin,latin-ext&display=swap" type="text/css" media="all">');
        }
        $.ajax({
            url: ajaxUrl,
            data: {
                action: 'fancyforms_get_google_font_variants',
                font_family: font_family,
                wp_nonce: adminNonce
            },
            beforeSend: function () {
                $this.closest('.fancyforms-typography-font-family-field').next('.fancyforms-typography-font-style-field').addClass('fancyforms-typography-loading');
            },
            success: function (response) {
                $this.closest('.fancyforms-typography-font-family-field').next('.fancyforms-typography-font-style-field').removeClass('fancyforms-typography-loading');
                $this.closest('.fancyforms-typography-font-family-field').next('.fancyforms-typography-font-style-field').find('select').html(response).trigger("chosen:updated").trigger('change');
            }
        });
    });

    $('body').find(".fancyforms-typography-fields select").chosen({width: "100%"});

    $('.fancyforms-style-sidebar [name]').on('change', function () {
        var id = $(this).attr('id');
        if (id) {
            var to = $(this).val();
            var unit = $(this).attr('data-unit');
            unit = (unit === undefined) ? '' : unit;

            if ($(this).attr('data-style')) {
                var weight = to.replace(/\D/g, '');
                var eid = id.replace('style', 'weight');
                var css = '--' + eid + ':' + weight + ';';

                var style = to.replace(/\d+/g, '');
                if ('' == style) {
                    style = 'normal';
                }
                css += '--' + id + ':' + style + '}';
            } else {
                var css = '--' + id + ':' + to + unit + '}';
            }
            ffDynamicCss(id, css, to);
        }
    });

    $('body').on('click', '.fancyforms-setting-tab li', function () {
        // Add and remove the class for active tab
        $(this).closest('.fancyforms-tab-container').find('.fancyforms-setting-tab li').removeClass('fancyforms-tab-active');
        $(this).addClass('fancyforms-tab-active');

        var selected_menu = $(this).attr('data-tab');

        $(this).closest('.fancyforms-tab-container').find('.fancyforms-tab-content').hide();

        // Display The Clicked Tab Content
        $(this).closest('.fancyforms-tab-container').find('.' + selected_menu).show();


    });

    $('body').on('click', '.fancyforms-settings-heading', function () {
        if ($(this).hasClass('fancyforms-active'))
            return;
        $(this).siblings('.fancyforms-form-settings').slideUp();
        $(this).siblings('.fancyforms-settings-heading').removeClass('fancyforms-active');
        $(this).addClass('fancyforms-active');
        $(this).next('.fancyforms-form-settings').slideToggle();
    });

    // Linked button
    $('.fancyforms-linked').on('click', function () {
        $(this).closest('.fancyforms-unit-fields').addClass('fancyforms-not-linked');
    });

    // Unlinked button
    $('.fancyforms-unlinked').on('click', function () {
        $(this).closest('.fancyforms-unit-fields').removeClass('fancyforms-not-linked');
    });

    // Values linked inputs
    $('.fancyforms-unit-fields input').on('input', function () {
        var $val = $(this).val();
        $(this).closest('.fancyforms-unit-fields:not(.fancyforms-not-linked)').find('input').each(function (key, value) {
            $(this).val($val).change();
        });
    });

    $('#fancyforms-template-preview-form-id').on('change', function () {
        const formId = $(this).val();
        const templateId = $('#post_ID').val();
        $('.fancyforms-form-wrap').html('');
        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'fancyforms_template_style_preview',
                form_id: formId,
                template_id: templateId
            },
            dataType: "html",
            success: function (data) {
                if (formId) {
                    data = data.replace('fancyforms-container-' + formId, 'fancyforms-container-00');
                }
                $('.fancyforms-form-wrap').html(data);
            }
        });
    })

    // Custom File Upload
    $(".fancyforms-dropzone").change(function () {
        var $input = $(this);
        var input = this;
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                var htmlPreview = '<p>' + input.files[0].name + '</p>';
                var wrapperZone = $input.parent();
                var previewZone = $input.parent().parent().find('.fancyforms-preview-zone');
                var boxZone = $input.closest('form').find('.fancyforms-box-body');

                wrapperZone.removeClass('dragover');
                previewZone.removeClass('hidden');
                boxZone.empty();
                boxZone.append(htmlPreview);
            };

            reader.readAsDataURL(input.files[0]);
        }
    });

    $('.fancyforms-dropzone-wrapper').on('dragover', function (e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).addClass('dragover');
    });

    $('.fancyforms-dropzone-wrapper').on('dragleave', function (e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass('dragover');
    });

    $('.fancyforms-remove-preview').on('click', function () {
        try {
            var boxZone = $(this).parents('.fancyforms-preview-zone').find('.box-body');
            var previewZone = $(this).parents('.fancyforms-preview-zone');
            var dropzone = $(this).parents('.fancyforms-preview-zone').siblings('.fancyforms-dropzone-wrapper').find('.fancyforms-dropzone');
            boxZone.empty();
            previewZone.addClass('hidden');
            dropzone.wrap('<form>').closest('form').get(0).reset();
            dropzone.unwrap();
        } catch (err) {
            console.log(err)
        }

    });

    $('body').on('click', '#fancyforms-copy-shortcode', function () {
        if ($(this).closest('#fancyforms-add-template').hasClass('fancyforms-success')) {
            return false;
        }
        var textToCopy = $(this).prev('input').val();
        var tempTextarea = $('<textarea>');
        var successDiv = $(this).closest('#fancyforms-add-template');
        $('body').append(tempTextarea);
        tempTextarea.val(textToCopy).select();
        document.execCommand('copy');
        tempTextarea.remove();
        successDiv.addClass('fancyforms-success');
        setTimeout(function () {
            successDiv.removeClass('fancyforms-success');
        }, 3000)
    });

    $('.fancyforms-activate-wp-mail-smtp-plugin').on('click', function (e) {
        e.preventDefault();
        var button = $(this);
        button.addClass('updating-message').html(fancyforms_admin_js_obj.activating_text);

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'fancyforms_activate_plugin',
                slug: 'wp-mail-smtp',
                file: 'wp_mail_smtp'
            },
        }).done(function (result) {
            var result = JSON.parse(result)
            if (result.success) {
                location.reload();
            } else {
                button.removeClass('updating-message').html(fancyforms_admin_js_obj.error);
            }

        });
    });

    $('.fancyforms-install-wp-mail-smtp-plugin').on('click', function (e) {
        e.preventDefault();
        var button = $(this);

        button.addClass('updating-message').html(fancyforms_admin_js_obj.installing_text);

        wp.updates.installPlugin({
            slug: 'wp-mail-smtp'
        }).done(function () {
            button.html(fancyforms_admin_js_obj.activating_text);
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'fancyforms_activate_plugin',
                    slug: 'wp-mail-smtp',
                    file: 'wp_mail_smtp'
                },
            }).done(function (result) {
                var result = JSON.parse(result)
                if (result.success) {
                    location.reload();
                } else {
                    button.removeClass('updating-message').html(fancyforms_admin_js_obj.error);
                }

            });
        });
    });

    $(document).ready(function () {
        setTimeout(function () {
            jQuery('.fancyforms-settings-updated').fadeOut('slow', function () {
                this.parentNode.removeChild(this);
            });
        }, 3000);
    });

    $(".fancyforms-field-content input, .fancyforms-field-content select, .fancyforms-field-content textarea").on('focus', function () {
        $(this).parent().addClass('fancyforms-field-focussed');
    }).on('focusout', function () {
        $(this).parent().removeClass('fancyforms-field-focussed');
    })
})(jQuery);

function ffDynamicCss(control, style, val) {
    ctrlEscaped = control.replaceAll('(', '\\(').replaceAll(')', '\\)');
    jQuery('style.' + ctrlEscaped).remove();
    if (val) {
        //console.log(style);
        jQuery('head').append('<style class="' + control + '">body #fancyforms-container-00{' + style + '}</style>');
    }
}