jQuery(function ($) {

    'use strict';

    $(document).on('submit.fancyforms-form', '.fancyforms-form', function (e) {
        e.preventDefault();
        var form = $(this);

        if (form.find('button.fancyforms-submit-button').hasClass('fancyforms-button-loading')) {
            return;
        } else {
            form.find('button.fancyforms-submit-button').addClass('fancyforms-button-loading');
        }

        const siteKey = $('.g-recaptcha').attr('data-sitekey');

        const isV3 = $('.g-recaptcha').attr('data-size') == "invisible";
        isV3 && grecaptcha.ready(function () {
            grecaptcha.execute(siteKey, {action: 'fancyforms'}).then(function (token) {
                form.append('<input type="hidden" id="recaptcha_token" value="' + token + '">');
            });
        });

        $('.fancyforms-error-msg').remove();
        $('.fancyforms-success-msg').remove();
        $('.fancyforms-failed-msg').remove();
        $(document).find('.fancyforms-error-container').removeClass('fancyforms-error-container');

        setTimeout(() => {
            var data = form.serializeArray();

            if (isV3) {
                const reCaptchaTokenValue = $(document).find('#recaptcha_token').val();
                $(document).find('#recaptcha_token').remove();
                data.forEach(function (item) {
                    if (item.name === 'g-recaptcha-response') {
                        item.value = item.value ? item.value : reCaptchaTokenValue;
                    }
                });
            }

            jQuery.ajax({
                type: 'POST',
                url: fancyforms_vars.ajaxurl,
                dataType: 'json',
                data: {
                    action: 'fancyforms_process_entry',
                    data: $.param(data)
                },
                success: function (response) {
                    form.find('button.fancyforms-submit-button').removeClass('fancyforms-button-loading');
                    if (response.status == "redirect") {
                        window.location.replace(response.message);
                    } else if (response.status == "success") {
                        form.trigger("reset");
                        form.find('.fancyforms-star-rating').removeClass('fancyforms-star-checked');
                        form.find('.fancyforms-range-input-selector').each(function () {
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
                                    $(this).next().val(ui.value);
                                }
                            });
                        });
                        $('body').find('.fancyforms-preview-remove').trigger('click');
                        form.append('<span class="fancyforms-success-msg">' + response.message + '</span>');
                    } else if (response.status == "failed") {
                        form.append('<span class="fancyforms-failed-msg">' + response.message + '</span>');
                    } else {
                        $.each(response.message, function (key, value) {
                            const errorFieldId = key.replace("field", "");
                            $('#' + 'fancyforms-field-container-' + errorFieldId).addClass('fancyforms-error-container').append('<span class="fancyforms-error-msg">' + value + '</span>');
                        });

                        const firstError = Object.keys(response.message)[0];
                        const subFieldIndex = firstError.indexOf('-');
                        var firstErrorItem;

                        if (subFieldIndex > 0) {
                            const errorFieldId = firstError.substr(0, subFieldIndex).replace("field", "");
                            const subField = firstError.substr(subFieldIndex + 1, firstError.length);
                            firstErrorItem = $('#' + 'fancyforms-subfield-container-' + subField + '-' + errorFieldId);
                        } else {
                            const errorFieldId = firstError.replace("field", "");
                            firstErrorItem = $('#' + 'fancyforms-field-container-' + errorFieldId);
                        }

                        $('html, body').animate({
                            scrollTop: firstErrorItem.offset().top - 300
                        }, 300);
                    }
                }
            });
        }, 1000);
    });

    $(document).find(".fancyforms-field-type-spinner .fancyforms-quantity .mdi-plus").click(function () {
        const parent = $(this).closest('.fancyforms-field-type-spinner');
        const numberInput = parent.find('input');
        const max = numberInput.attr('max');
        const numberInputVal = Number(numberInput.val());
        numberInput.val(numberInputVal < max ? numberInputVal + 1 : max);
    });

    $(document).find(".fancyforms-field-type-spinner .fancyforms-quantity .mdi-minus").click(function () {
        const parent = $(this).closest('.fancyforms-field-type-spinner');
        const numberInput = parent.find('input');
        const min = numberInput.attr('min');
        const numberInputVal = Number(numberInput.val());
        numberInput.val(numberInputVal > min ? numberInputVal - 1 : min);
    });

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
                $(this).next().val(ui.value);
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

    function hoverStars() {
        $(this).prevAll('.fancyforms-star-rating').addBack().addClass('fancyforms-star-hovered');
        $(this).nextAll('.fancyforms-star-rating').addClass('fancyforms-star-non-hovered');
    }

    function unhoverStars() {
        $(this).closest('.fancyforms-star-group').find('.fancyforms-star-rating').removeClass('fancyforms-star-hovered fancyforms-star-non-hovered');
    }

    function loadStars() {
        $(this).closest('.fancyforms-star-group').find('.fancyforms-star-rating').removeClass('fancyforms-star-checked');
        $(this).parent('.fancyforms-star-rating').prevAll('.fancyforms-star-rating').addBack().addClass('fancyforms-star-checked');
    }

    $(document).on('click', '.fancyforms-star-group input', loadStars);
    $(document).on('mouseenter', '.fancyforms-star-group .fancyforms-star-rating:not(.fancyforms-star-rating-readonly)', hoverStars);
    $(document).on('mouseleave', '.fancyforms-star-group .fancyforms-star-rating:not(.fancyforms-star-rating-readonly)', unhoverStars);

    $('.fancyforms-field-type-date input').each(function () {
        const $this = $(this);
        const dtFormat = $this.attr('data-format');
        const dtVal = $this.val();
        if(dtVal) {
            var date = new Date(dtVal);
            $this.val(date == 'Invalid Date' ? '' : moment(date).format(dtFormat.replace("dd", "DD").replace("MM", "MMMM").replace("mm", "MM")));
        }
        $this.datepicker({
            changeMonth: true,
            dateFormat: dtFormat,
        });
    })

    $('.fancyforms-field-type-time').each(function () {
        var timePickerWrap = $(this).find('.fancyforms-timepicker');
        var timePickerValueInput = $(this).find('.fancyforms-output');
        timePickerWrap.timepicker({
            'showDuration': false,
            'timeFormat': 'g:ia',
        });
    })

    function arrayValsCompare(compareValue, arrayVals, condition) {
        var retCase = false;
        switch (condition) {
            case 'equal':
                if($.inArray(compareValue, arrayVals) !== -1) {
                    retCase = true;
                }
                break;

            case 'less_than':
                retCase = arrayVals.length > 0 ? true : false;
                $.each(arrayVals, function(index, val) {
                    if (compareValue <= val) {
                        retCase = false;
                        return false;
                    }
                })
                break;

            case 'less_than_or_equal':
                retCase = arrayVals.length > 0 ? true : false;
                $.each(arrayVals, function(index, val) {
                    if (compareValue < val) {
                        retCase = false;
                        return false;
                    }
                })
                break;

            case 'greater_than':
                retCase = arrayVals.length > 0 ? true : false;
                $.each(arrayVals, function(index, val) {
                    if (compareValue >= val) {
                        retCase = false;
                        return false;
                    }
                })
                break;

            case 'greater_than_or_equal':
                console.log(arrayVals);
                console.log(arrayVals.length);
                retCase = arrayVals.length > 0 ? true : false;
                $.each(arrayVals, function(index, val) {
                    if (compareValue > val) {
                        retCase = false;
                        return false;
                    }
                })
                break;

            case 'is_like':
                $.each(arrayVals, function(index, val) {
                    if (val.indexOf(compareValue) >= 0) {
                        retCase = true;
                    }
                })
                break;
        }
        return retCase;
    }

    $('.fancyforms-form-conditions').each(function () {
        const $this = $(this);
        const parentForm = $this.closest('form');
        const conditions = JSON.parse($this.val());
        $.each(conditions, function (index, val) {
            var conditionTrigger = parentForm.find('[name="item_meta[' + val.compare_to + ']');
            var isArrayVals = false;
            const actionField = parentForm.find('#fancyforms-field-container-' + val.compare_from);
            const compareCondition = val.compare_condition;
            const compareValue = val.compare_value;
            const conditionAction = val.condition_action;

            if (!(conditionTrigger.length > 0)) {
                conditionTrigger = parentForm.find('[name="item_meta[' + val.compare_to + '][]');
                isArrayVals = true;
            }

            conditionTrigger.on('change', function () {
                var value = $(this).val();
                var selector = $(this);
                var arrayVals = [];
                if (isArrayVals) {
                    arrayVals = conditionTrigger.map(function () {
                        return $(this).is(':checked') ? $(this).val() : null;
                    }).toArray();
                }

                if ($(this).attr('type') && $(this).attr('type') == 'checkbox') {
                    if (!$(this).is(':checked')) {
                        value = '';
                    }
                }

                switch (compareCondition) {
                    case 'equal':
                        if (isArrayVals ? arrayValsCompare(compareValue, arrayVals, 'equal') : (value == compareValue)) {
                            if (actionField.length) {
                                if (conditionAction == 'show') {
                                    actionField.show();
                                } else {
                                    actionField.hide();
                                }
                            }

                        } else {
                            if (actionField.length) {
                                if (conditionAction == 'show') {
                                    actionField.hide();
                                } else {
                                    actionField.show();
                                }
                            }
                        }
                        break;

                    case 'not_equal':
                        if (!(isArrayVals ? arrayValsCompare(compareValue, arrayVals, 'equal') : (value == compareValue))) {
                            if (actionField.length) {
                                if (conditionAction == 'show') {
                                    actionField.show();
                                } else {
                                    actionField.hide();
                                }
                            }

                        } else {
                            if (actionField.length) {
                                if (conditionAction == 'show') {
                                    actionField.hide();
                                } else {
                                    actionField.show();
                                }
                            }
                        }
                        break;

                    case 'less_than':
                        value = (value == '') ? 0 : parseInt(value);
                        if (isArrayVals ? arrayValsCompare(compareValue, arrayVals, 'less_than') : (value < compareValue)) {
                            if (actionField.length) {
                                if (conditionAction == 'show') {
                                    actionField.show();
                                } else {
                                    actionField.hide();
                                }
                            }

                        } else {
                            if (actionField.length) {
                                if (conditionAction == 'show') {
                                    actionField.hide();
                                } else {
                                    actionField.show();
                                }
                            }
                        }
                        break;

                    case 'less_than_or_equal':
                        value = (value == '') ? 0 : parseInt(value);
                        if (isArrayVals ? arrayValsCompare(compareValue, arrayVals, 'less_than_or_equal') : (value <= compareValue)) {
                            if (actionField.length) {
                                if (conditionAction == 'show') {
                                    actionField.show();
                                } else {
                                    actionField.hide();
                                }
                            }

                        } else {
                            if (actionField.length) {
                                if (conditionAction == 'show') {
                                    actionField.hide();
                                } else {
                                    actionField.show();
                                }
                            }
                        }
                        break;

                    case 'greater_than':
                        value = (value == '') ? 0 : parseInt(value);
                        if (isArrayVals ? arrayValsCompare(compareValue, arrayVals, 'greater_than') : (value > compareValue)) {
                            if (actionField.length) {
                                if (conditionAction == 'show') {
                                    actionField.show();
                                } else {
                                    actionField.hide();
                                }
                            }

                        } else {
                            if (actionField.length) {
                                if (conditionAction == 'show') {
                                    actionField.hide();
                                } else {
                                    actionField.show();
                                }
                            }
                        }
                        break;

                    case 'greater_than_or_equal':
                        value = (value == '') ? 0 : parseInt(value);
                        if (isArrayVals ? arrayValsCompare(compareValue, arrayVals, 'greater_than_or_equal') : (value >= compareValue)) {
                            if (actionField.length) {
                                if (conditionAction == 'show') {
                                    actionField.show();
                                } else {
                                    actionField.hide();
                                }
                            }

                        } else {
                            if (actionField.length) {
                                if (conditionAction == 'show') {
                                    actionField.hide();
                                } else {
                                    actionField.show();
                                }
                            }
                        }
                        break;

                    case 'is_like':
                        if (isArrayVals ? arrayValsCompare(compareValue, arrayVals, 'is_like') : (value.indexOf(compareValue) >= 0)) {
                            if (actionField.length) {
                                if (conditionAction == 'show') {
                                    actionField.show();
                                } else {
                                    actionField.hide();
                                }
                            }

                        } else {
                            if (actionField.length) {
                                if (conditionAction == 'show') {
                                    actionField.hide();
                                } else {
                                    actionField.show();
                                }
                            }
                        }
                        break;

                    case 'is_not_like':
                        if (!(isArrayVals ? arrayValsCompare(compareValue, arrayVals, 'is_like') : (value.indexOf(compareValue) >= 0))) {
                            if (actionField.length) {
                                if (conditionAction == 'show') {
                                    actionField.show();
                                } else {
                                    actionField.hide();
                                }
                            }

                        } else {
                            if (actionField.length) {
                                if (conditionAction == 'show') {
                                    actionField.hide();
                                } else {
                                    actionField.show();
                                }
                            }
                        }
                        break;
                }
            }).trigger('change');
        });
    })

    $(".fancyforms-field-content input, .fancyforms-field-content select, .fancyforms-field-content textarea").on('focus', function () {
        $(this).parent().addClass('fancyforms-field-focussed');
    }).on('focusout', function () {
        $(this).parent().removeClass('fancyforms-field-focussed');
    })

    var upload_counter = 0;
    var uploader = {};
    $('.fancyforms-file-uploader').each(function () {
        upload_counter++;
        var attr_element_id = $(this).attr('id'),
                size = $(this).attr('data-max-upload-size'),
                limit_flag = 0,
                selector = $(this),
                uploader_label = $(this).attr('data-upload-label'),
                multiple_upload = ($(this).attr('data-multiple-uploads') == 'true') ? true : false,
                upload_limit = $(this).attr('data-multiple-uploads-limit'),
                upload_limit_message = $(this).attr('data-multiple-uploads-error-message'),
                extensions = $(this).attr('data-extensions'),
                extension_error_message = $(this).attr('data-extensions-error-message'),
                extensions_array = extensions.split(',');

        upload_limit = upload_limit < 1 ? 1 : upload_limit;

        uploader['uploader' + upload_counter] = new qq.FileUploader({
            element: document.getElementById(attr_element_id),
            action: fancyforms_vars.ajaxurl,
            params: {
                action: 'fancyforms_file_upload_action',
                file_uploader_nonce: fancyforms_vars.ajax_nounce,
                allowedExtensions: extensions_array,
                sizeLimit: size,
            },
            allowedExtensions: extensions_array,
            sizeLimit: size,
            minSizeLimit: 50,
            uploadButtonText: uploader_label,

            onSubmit: function (id, fileName) {
                if (multiple_upload == true && upload_limit != -1) {
                    var limit_counter = selector.parent().find('.fancyforms-multiple-upload-limit').val();
                    limit_counter++;
                    selector.parent().find('.fancyforms-multiple-upload-limit').val(limit_counter);
                    if (limit_counter > upload_limit) {
                        if (limit_flag == 0) {
                            upload_limit_message = (upload_limit_message != '') ? upload_limit_message : 'Maximum number of files allowed is ' + upload_limit;
                            selector.parent().find('.fancyforms-error').html(upload_limit_message);
                            limit_flag = 1;
                        }

                        selector.parent().find('.fancyforms-multiple-upload-limit').val(upload_limit);
                        return false;
                    }
                }
            },

            onProgress: function (id, fileName, loaded, total) {},

            onComplete: function (id, fileName, responseJSON) {

                if (responseJSON.success) {

                    $('#' + attr_element_id).closest('.fancyforms-file-uploader-wrapper').find('.fancyforms-error').html('');
                    var extension_array = fileName.split('.');
                    var extension = extension_array.pop();

                    if (extension == 'jpg' || extension == 'jpeg' || extension == 'png' || extension == 'gif' || extension == 'JPG' || extension == 'JPEG' || extension == 'PNG' || extension == 'GIF') {
                        var preview_img = responseJSON.url;
                    }

                    var preview_html = '<div class="fancyforms-prev-holder" id="fancyforms-uploaded-' + id + '">';
                    if (preview_img) {
                        preview_html += '<img src="' + preview_img + '" />';
                    }
                    preview_html += '<span class="fancyforms-prev-name">' + fileName + '</span></div>';

                    if (multiple_upload) {
                        var url = responseJSON.url;
                        var added_url = $('#' + attr_element_id).closest('.fancyforms-file-uploader-wrapper').find('.fancyforms-uploaded-files').val();
                        if (added_url == '') {
                            added_url = url;
                        } else {
                            var added_url_array = added_url.split(',');
                            added_url_array.push(url);
                            added_url = added_url_array.join();
                        }

                        $('#' + attr_element_id).closest('.fancyforms-file-uploader-wrapper').find('.fancyforms-uploaded-files').val(added_url);
                        $('#' + attr_element_id).closest('.fancyforms-file-uploader-wrapper').find('.fancyforms-file-preview').append(preview_html);

                    } else {
                        $('#' + attr_element_id).closest('.fancyforms-file-uploader-wrapper').find('.fancyforms-uploaded-files').val(responseJSON.url);
                        $('#' + attr_element_id).closest('.fancyforms-file-uploader-wrapper').find('.fancyforms-file-preview').html(preview_html);
                    }

                } else {
                    console.log(responseJSON);
                }
            },

            onCancel: function (id, fileName) {},
            onError: function (id, fileName, xhr) {},

            messages: {
                typeError: extension_error_message,
                sizeError: "{file} is too large, maximum file size is {sizeLimit}.",
                minSizeError: "{file} is too small, minimum file size is {minSizeLimit}.",
                emptyError: "{file} is empty, please select files again without it.",
                onLeave: "The files are being uploaded, if you leave now the upload will be cancelled."
            },

            showMessage: function (message) {
                alert(message);
            },

            multiple: multiple_upload
        });

    });


    $('body').on('click', '.fancyforms-preview-remove', function () {
        const selector = $(this);
        $.ajax({
            url: fancyforms_vars.ajaxurl,
            data: 'action=fancyforms_file_delete_action&path=' + selector.data('path') + '&_wpnonce=' + fancyforms_vars.ajax_nounce,
            type: 'post',
            success: function (res) {
                if (res == 'success') {
                    var parent_wrapper = selector.closest('.fancyforms-file-uploader-wrapper')
                    var prev_url = parent_wrapper.find('.fancyforms-uploaded-files').val();
                    var new_url = prev_url.replace(selector.data('url'), '');
                    new_url = new_url.replace(',,', ',');
                    parent_wrapper.find('.fancyforms-uploaded-files').val(new_url);

                    var limit_counter = parent_wrapper.find('.fancyforms-multiple-upload-limit').val();
                    limit_counter--;
                    limit_counter = (limit_counter < 0) ? 0 : limit_counter;
                    parent_wrapper.find('.fancyforms-multiple-upload-limit').val(limit_counter);

                    selector.parent().fadeOut('1500', function () {
                        selector.parent().remove();
                        parent_wrapper.find('#' + selector.attr('data-remove-id')).remove();
                    });
                }
            }
        });
    });

});