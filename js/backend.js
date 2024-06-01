var fancyFormsAdmin = fancyFormsAdmin || {};

(function ($) {
    'use strict';
    let $buildForm = $('#fancyforms-fields-form'),
            $formMeta = $('#fancyforms-meta-form'),
            $formSettings = $('#fancyforms-settings-form'),
            $styleSettings = $('#fancyforms-style-form'),
            copyHelper = false,
            fieldsUpdated = 0;
    var isCheckedField = false;

    fancyFormsAdmin = {
        init: function () {
            if ($formSettings.length > 0) {
                this.initFormSettings();

            } else if ($styleSettings.length > 0) {
                this.initStyleSettings();

            } else if ($buildForm.length > 0) {
                $('.fancyforms-ajax-udpate-button').on('click', fancyFormsAdmin.submitBuild);

            } else {
                this.initOtherSettings();
            }

            fancyFormsAdmin.liveChanges();

            fancyFormsAdmin.setupFieldOptionSorting($('.fancyforms-option-list'));

            fancyFormsAdmin.initBulkOptionsOverlay();

            fancyFormsAdmin.initNewFormModal();


            $(document).find('.fancyforms-color-picker').wpColorPicker();

            $(document).on('click', '#fancyforms-fields-tabs a', fancyFormsAdmin.clickNewTab);
            $(document).on('input', '.fancyforms-search-fields-input', fancyFormsAdmin.searchContent);
            $(document).on('click', '.fancyforms-settings-tab a', fancyFormsAdmin.clickNewTabSettings);

            /* Image */
            $(document).on('click', '.fancyforms-image-preview .fancyforms-choose-image', fancyFormsAdmin.addImage);
            $(document).on('click', '.fancyforms-image-preview .fancyforms-remove-image', fancyFormsAdmin.removeImage);

            /* Add field attr to form in Settings page */
            $(document).on('click', '.fancyforms-add-field-attr-to-form li', fancyFormsAdmin.addFieldAttrToForm);

            /* Open/Close embed popup */
            $(document).on('click', '.fancyforms-embed-button', function () {
                $('#fancyforms-shortcode-form-modal').addClass('fancyforms-open');
            });

            $(document).on('click', '.fancyforms-close-form-modal', function () {
                $('#fancyforms-shortcode-form-modal').removeClass('fancyforms-open');
            });

            $('.fancyforms-add-more-condition').on('click', fancyFormsAdmin.addConditionRepeaterBlock);
            $(document).on('click', '.fancyforms-condition-remove', fancyFormsAdmin.removeConditionRepeaterBlock);

            $(document).on('change', '.fancyforms-fields-type-time .default-value-field', fancyFormsAdmin.addTimeDefaultValue);
            $(document).on('change', '.fancyforms-fields-type-time .min-value-field, .fancyforms-fields-type-time .max-value-field, .fancyforms-fields-type-time .fancyforms-default-value-field', fancyFormsAdmin.validateTimeValue);

            $('.fancyforms-fields-type-date .fancyforms-default-value-field').datepicker({
                changeMonth: true,
            });

            document.addEventListener(
                "fancyforms_added_field", (e) => {
                    if (e.ffType == 'date') {
                        $(document).find('.fancyforms-fields-type-date .fancyforms-default-value-field').datepicker({
                            changeMonth: true,
                        });
                    }
                }, false,
            );
        },

        clickNewTab: function () {
            var href = $(this).attr('href'),
                    $link = $(this);
            if (typeof href === 'undefined') {
                return false;
            }

            $link.closest('li').addClass('fancyforms-active-tab').siblings('li').removeClass('fancyforms-active-tab');
            $link.closest('.fancyforms-fields-container').find('.ht-fields-panel').hide();
            $(href).show();
            return false;
        },

        searchContent: function () {
            var i,
                    searchText = $(this).val().toLowerCase(),
                    toSearch = $(this).attr('data-tosearch'),
                    $items = $('.' + toSearch);

            $items.each(function () {
                if ($(this).attr('id').indexOf(searchText) > -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        },

        clickNewTabSettings: function () {
            var id = this.getAttribute('href'),
                    $link = $(this);

            if (typeof id === 'undefined') {
                return false;
            }

            $link.closest('li').addClass('fancyforms-active').siblings('li').removeClass('fancyforms-active');
            $(id).removeClass('fancyforms-hidden').siblings().addClass('fancyforms-hidden');
            return false;
        },

        addImage: function (e) {
            e.preventDefault();
            const imagePreview = $(this).closest('.fancyforms-image-preview');
            const fileFrame = wp.media({
                multiple: false,
                library: {
                    type: ['image']
                }
            });

            fileFrame.on('select', function () {
                const attachment = fileFrame.state().get('selection').first().toJSON();
                imagePreview.find('img').attr('src', attachment.url);
                imagePreview.find('input.fancyforms-image-id').val(attachment.id);
                imagePreview.find('.fancyforms-image-preview-wrap').removeClass('fancyforms-hidden');
                imagePreview.find('.fancyforms-choose-image').addClass('fancyforms-hidden');

                const frontImagePreview = imagePreview.find('input.fancyforms-image-id').attr('id');
                $('.' + frontImagePreview).append('<img src="' + attachment.url + '"/>');
                $('.' + frontImagePreview).find('.fancyforms-no-image-field').addClass('fancyforms-hidden');
            });
            fileFrame.open();
        },

        removeImage: function (e) {
            const imagePreview = $(this).closest('.fancyforms-image-preview');
            e.preventDefault();
            imagePreview.find('img').attr('src', '');
            imagePreview.find('.fancyforms-image-preview-wrap').addClass('fancyforms-hidden');
            imagePreview.find('.fancyforms-choose-image').removeClass('fancyforms-hidden');
            imagePreview.find('input.fancyforms-image-id').val('');

            const frontImagePreview = imagePreview.find('input.fancyforms-image-id').attr('id');
            $('.' + frontImagePreview).find('.fancyforms-no-image-field').removeClass('fancyforms-hidden');
            $('.' + frontImagePreview).find('img').remove();
        },

        addFieldAttrToForm: function (e) {
            const fieldId = $(this).attr('data-value');
            const inputChange = $(this).closest('.fancyforms-form-row').find('input');
            const textAreaChange = $(this).closest('.fancyforms-form-row').find('textarea');

            if (fieldId && inputChange.length > 0) {
                inputChange.val(inputChange.val() + ' ' + fieldId);
            }

            if (fieldId && textAreaChange.length > 0) {
                textAreaChange.val(textAreaChange.val() + ' ' + fieldId);
            }
        },

        submitBuild: function (e) {
            e.preventDefault();
            var $thisEle = this;
            fancyFormsAdmin.preFormSave(this);
            var fancyforms_fields = JSON.stringify($buildForm.serializeArray());
            var fancyforms_settings = JSON.stringify($formMeta.serializeArray());

            jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: 'fancyforms_update_form',
                    fancyforms_fields: fancyforms_fields,
                    fancyforms_settings: fancyforms_settings,
                    nonce: fancyforms_backend_js.nonce
                },
                success: function (msg) {
                    fancyFormsAdmin.afterFormSave($thisEle);
                    var $postStuff = document.getElementById('fancyforms-form-panel');
                    var $html = document.createElement('div');
                    $html.setAttribute('class', 'fancyforms-updated-info');
                    $html.innerHTML = msg;
                    $postStuff.insertBefore($html, $postStuff.firstChild);
                }
            });
        },

        addImageToOption: function (e) {
            e.preventDefault();
            const imagePreview = e.target.closest('li');
            const fileFrame = wp.media({
                multiple: false,
                library: {
                    type: ['image']
                }
            });

            fileFrame.on('select', function () {
                const attachment = fileFrame.state().get('selection').first().toJSON();
                const $imagePreview = $(imagePreview);
                $imagePreview.find('.fancyforms-is-image-holder').html('<img src="' + attachment.url + '"/>');
                $imagePreview.find('.fancyforms-is-image-preview-box').addClass('fancyforms-image-added');

                $imagePreview.find('input.fancyforms-image-id').val(attachment.id).trigger('change');
                var fieldId = $imagePreview.closest('.fancyforms-fields-settings').data('fid');
                fancyFormsAdmin.resetDisplayedOpts(fieldId);
            });
            fileFrame.open();
        },

        removeImageFromOption: function (e) {
            var $this = $(this),
                    previewWrapper = $this.closest('li');
            e.preventDefault();
            e.stopPropagation();

            previewWrapper.find('.fancyforms-is-image-holder').html('');
            previewWrapper.find('.fancyforms-is-image-preview-box').removeClass('fancyforms-image-added');
            previewWrapper.find('input.fancyforms-image-id').val('').trigger('change');
            var fieldId = previewWrapper.closest('.fancyforms-fields-settings').data('fid');
            fancyFormsAdmin.resetDisplayedOpts(fieldId);
        },

        liveChanges: function () {
            $('#fancyforms-meta-panel').on('input', '[data-changeme]', fancyFormsAdmin.liveChangesInput);
            $('#fancyforms-meta-panel').on('change', 'select[name="submit_btn_alignment"]', fancyFormsAdmin.liveChangeButtonPosition);

            $buildForm.on('input, change', '[data-changeme]', fancyFormsAdmin.liveChangesInput);

            $buildForm.on('click', 'input.fancyforms-form-field-required', fancyFormsAdmin.markRequired);

            $buildForm.on('click', '.fancyforms-add-option', fancyFormsAdmin.addFieldOption);
            $buildForm.on('input', '.fancyforms-single-option input[type="text"]', fancyFormsAdmin.resetOptOnChange);
            $buildForm.on('mousedown', '.fancyforms-single-option input[type=radio]', fancyFormsAdmin.maybeUncheckRadio);
            $buildForm.on('click', '.fancyforms-single-option .fancyforms-choice-input', fancyFormsAdmin.resetOptOnChange);
            $buildForm.on('change', '.fancyforms-image-id', fancyFormsAdmin.resetOptOnChange);

            $buildForm.on('click', '.fancyforms-single-option a[data-removeid]', fancyFormsAdmin.deleteFieldOption);

            $buildForm.on('click', '.fancyforms-is-image-preview-box', fancyFormsAdmin.addImageToOption);
            $buildForm.on('click', '.fancyforms-is-remove-image', fancyFormsAdmin.removeImageFromOption);

            $buildForm.on('input', '[data-changeheight]', fancyFormsAdmin.liveChangeHeight);
            $buildForm.on('input', '[data-changerows]', fancyFormsAdmin.liveChangeRows);
            $buildForm.on('input', '[data-changestars]', fancyFormsAdmin.liveChangeStars);

            $buildForm.on('change', 'select[name^="field_options[label_position"]', fancyFormsAdmin.liveChangeLabelPosition);
            $buildForm.on('change', 'select[name^="field_options[label_alignment"]', fancyFormsAdmin.liveChangeLabelAlignment);

            $buildForm.on('change', 'select[name^="field_options[options_layout"]', fancyFormsAdmin.liveChangeOptionsLayout);
            $buildForm.on('change', 'select[name^="field_options[heading_type"]', fancyFormsAdmin.liveChangeHeadingType);
            $buildForm.on('change', 'select[name^="field_options[text_alignment"]', fancyFormsAdmin.liveChangeTextAlignment);
            $buildForm.on('change', 'select.fancyforms-select-image-type', fancyFormsAdmin.liveChangeSelectImageType);

            $buildForm.on('change', '[data-changebordertype]', fancyFormsAdmin.liveChangeBorderType);
            $buildForm.on('input', '[data-changeborderwidth]', fancyFormsAdmin.liveChangeBorderWidth);

            $buildForm.on('input', 'input[name^="field_options[field_max_width"]', fancyFormsAdmin.liveChangeFieldMaxWidth);
            $buildForm.on('change', 'select[name^="field_options[field_max_width_unit"]', fancyFormsAdmin.liveChangeFieldMaxWidth);

            $buildForm.on('input', 'input[name^="field_options[image_max_width"]', fancyFormsAdmin.liveChangeImageMaxWidth);
            $buildForm.on('change', 'select[name^="field_options[image_max_width_unit"]', fancyFormsAdmin.liveChangeImageMaxWidth);

            $buildForm.on('change click', '[data-disablefield]', fancyFormsAdmin.liveChangeAddressFields);

            $buildForm.on('change click', 'input[name^="field_options[auto_width"]', fancyFormsAdmin.liveChangeAutoWidth);

            $buildForm.on('change', 'select[name^="field_options[field_alignment"]', fancyFormsAdmin.liveChangeFieldAlignment);

            $buildForm.on('change', '[data-row-show-hide]', fancyFormsAdmin.liveChangeHideShowRow);
            $buildForm.on('input', '[data-label-show-hide]', fancyFormsAdmin.liveChangeHideShowLabel);
            $buildForm.on('change', '[data-label-show-hide-checkbox]', fancyFormsAdmin.liveChangeHideShowLabelCheckbox);
        },

        liveChangesInput: function () {
            var option,
                    newValue = this.value,
                    changes = document.getElementById(this.getAttribute('data-changeme')),
                    att = this.getAttribute('data-changeatt'),
                    fieldAttrType = this.getAttribute('type'),
                    parentField = $(changes).closest('.fancyforms-editor-form-field');

            if (att == 'value' && fieldAttrType == "email") {
                $(this).closest('div').find('.fancyforms-error').remove();
                if (newValue && !fancyFormsAdmin.isEmail(newValue)) {
                    $(this).closest('div').append('<p class="fancyforms-error">Invalid Email Value</p>');
                }
            }

            if (att == 'value' && parentField.attr('data-type') == 'url') {
                $(this).closest('div').find('.fancyforms-error').remove();
                if (newValue && !fancyFormsAdmin.isUrl(newValue)) {
                    $(this).closest('div').append('<p class="fancyforms-error">Invalid Website/URL Value. Please add full URL value</p>');
                }
            }

            if (parentField.attr('data-type') == 'range_slider') {
                setTimeout(function () {
                    var newSlider = parentField.find('.fancyforms-range-input-selector');
                    var sliderValue = newSlider.val();
                    var sliderMinValue = parseFloat(newSlider.attr('min'));
                    var sliderMaxValue = parseFloat(newSlider.attr('max'));
                    var sliderStepValue = parseFloat(newSlider.attr('step'));
                    sliderValue = sliderValue < sliderMinValue ? sliderMinValue : sliderValue;
                    sliderValue = sliderValue > sliderMaxValue ? sliderMaxValue : sliderValue;
                    var remainder = sliderValue % sliderStepValue;
                    sliderValue = sliderValue - remainder;
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
                }, 100)
            }

            if (changes === null) {
                return;
            }

            if (att !== null) {
                if (changes.tagName === 'SELECT' && att === 'placeholder') {
                    option = changes.options[0];
                    if (option.value === '') {
                        option.innerHTML = newValue;
                    } else {
                        // Create a placeholder option if there are no blank values.
                        fancyFormsAdmin.addBlankSelectOption(changes, newValue);
                    }
                } else if (att === 'class') {
                    fancyFormsAdmin.changeFieldClass(changes, this);
                } else {
                    if ('TEXTAREA' === changes.nodeName && att == 'value') {
                        changes.innerHTML = newValue;
                    } else {
                        changes.setAttribute(att, newValue);
                    }
                }
            } else if (changes.id.indexOf('setup-message') === 0) {
                if (newValue !== '') {
                    changes.innerHTML = '<input type="text" value="" disabled />';
                }
            } else {
                changes.innerHTML = newValue;

                if ('TEXTAREA' === changes.nodeName && changes.classList.contains('wp-editor-area')) {
                    $(changes).trigger('change');
                }

                if (changes.classList.contains('fancyforms-form-label') && 'break' === changes.nextElementSibling.getAttribute('data-type')) {
                    changes.nextElementSibling.querySelector('.fancyforms-editor-submit-button').textContent = newValue;
                }
            }
        },

        liveChangeButtonPosition: function (e) {
            $('.fancyforms-editor-submit-button-wrap').removeClass('fancyforms-submit-btn-align-left fancyforms-submit-btn-align-right fancyforms-submit-btn-align-center').addClass('fancyforms-submit-btn-align-' + e.target.value);
        },

        markRequired: function () {
            var thisid = this.id.replace('fancyforms-', ''),
                    fieldId = thisid.replace('req-field-', ''),
                    checked = this.checked,
                    label = $('#fancyforms-editor-field-required-' + fieldId);

            fancyFormsAdmin.toggleValidationBox(checked, '.fancyforms-required-detail-' + fieldId);

            if (checked) {
                var $reqBox = $('input[name="field_options[required_indicator_' + fieldId + ']"]');
                if ($reqBox.val() === '') {
                    $reqBox.val('*');
                }
                label.removeClass('fancyforms-hidden');
            } else {
                label.addClass('fancyforms-hidden');
            }
        },

        //Add new option or "Other" option to radio/checkbox/dropdown
        addFieldOption: function () {
            /*jshint validthis:true */
            var fieldId = $(this).closest('.fancyforms-fields-settings').data('fid'),
                    newOption = $('#fancyforms-field-options-' + fieldId + ' .fancyforms-option-template').prop('outerHTML'),
                    optType = $(this).data('opttype'),
                    optKey = 0,
                    oldKey = '000',
                    lastKey = fancyFormsAdmin.getHighestOptKey(fieldId);

            if (lastKey !== oldKey) {
                optKey = lastKey + 1;
            }

            //Update hidden field
            if (optType === 'other') {
                document.getElementById('other_input_' + fieldId).value = 1;

                //Hide "Add Other" option now if this is radio field
                var ftype = $(this).data('ftype');
                if (ftype === 'radio' || ftype === 'select') {
                    $(this).fadeOut('slow');
                }

                var data = {
                    action: 'fancyforms-add-field_option',
                    field_id: fieldId,
                    opt_key: optKey,
                    opt_type: optType,
                    nonce: fancyforms_backend_js.nonce
                };

                jQuery.post(ajaxurl, data, function (msg) {
                    $('#fancyforms-field-options-' + fieldId).append(msg);
                    fancyFormsAdmin.resetDisplayedOpts(fieldId);
                });

            } else {
                newOption = newOption.replace(new RegExp('optkey="' + oldKey + '"', 'g'), 'optkey="' + optKey + '"');
                newOption = newOption.replace(new RegExp('-' + oldKey + '_', 'g'), '-' + optKey + '_');
                newOption = newOption.replace(new RegExp('-' + oldKey + '"', 'g'), '-' + optKey + '"');
                newOption = newOption.replace(new RegExp('\\[' + oldKey + '\\]', 'g'), '[' + optKey + ']');
                newOption = newOption.replace('fancyforms-hidden fancyforms-option-template', '');
                newOption = {newOption};

                $('#fancyforms-field-options-' + fieldId).append(newOption.newOption);
                fancyFormsAdmin.resetDisplayedOpts(fieldId);
            }
        },

        resetOptOnChange: function () {
            var field, thisOpt;
            var check = $(this);

            field = fancyFormsAdmin.getFieldKeyFromOpt(this);
            if (!field) {
                return;
            }

            thisOpt = $(this).closest('li');
            fancyFormsAdmin.resetSingleOpt(field.fieldId, field.fieldKey, thisOpt);

            setTimeout(function () {
                check.next('input').trigger('change');
            }, 100);
        },

        maybeUncheckRadio: function () {
            var $self, uncheck, unbind, up;

            $self = $(this);
            if ($self.is(':checked')) {
                uncheck = function () {
                    setTimeout(function () {
                        $self.prop('checked', false);
                    }, 0);
                };

                unbind = function () {
                    $self.off('mouseup', up);
                };

                up = function () {
                    uncheck();
                    unbind();
                };

                $self.on('mouseup', up);
                $self.one('mouseout', unbind);
            } else {
                $self.closest('li').siblings().find('.fancyforms-choice-input').prop('checked', false);
            }
        },

        deleteFieldOption: function () {
            var otherInput,
                    parentLi = this.closest('li'),
                    parentUl = parentLi.parentNode,
                    fieldId = this.getAttribute('data-fid');

            $(parentLi).fadeOut('slow', function () {
                $(parentLi).remove();
                var hasOther = $(parentUl).find('.fancyforms_other_option');
                if (hasOther.length < 1) {
                    otherInput = document.getElementById('other_input_' + fieldId);
                    if (otherInput !== null) {
                        otherInput.value = 0;
                    }
                    $('#other_button_' + fieldId).fadeIn('slow');
                }
                fancyFormsAdmin.resetDisplayedOpts(fieldId);
            });
        },

        liveChangeHeight: function () {
            var newValue = this.value,
                    changes = document.getElementById(this.getAttribute('data-changeheight'));

            if (changes === null) {
                return;
            }

            $(changes).css("height", newValue);
        },

        liveChangeRows: function () {
            var newValue = this.value,
                    changes = document.getElementById(this.getAttribute('data-changerows'));

            if (changes === null) {
                return;
            }

            $(changes).attr("rows", newValue);
        },

        liveChangeStars: function () {
            var newValue = this.value,
                    stars = '',
                    changes = document.getElementById(this.getAttribute('data-changestars'));

            if (changes === null) {
                return;
            }

            for (var i = 0; i < newValue; i++) {
                stars = stars + '<label class="fancyforms-star-rating"><input type="radio"><span class="mdi mdi-star-outline"></span></label>';
            }
            $(changes).html(stars);
        },

        liveChangeLabelPosition: function (e) {
            const fieldId = $(this).closest('.fancyforms-fields-settings').data('fid');
            $('#fancyforms-editor-field-id-' + fieldId).removeClass('fancyforms-label-position-top').removeClass('fancyforms-label-position-left').removeClass('fancyforms-label-position-right').removeClass('fancyforms-label-position-hide').addClass('fancyforms-label-position-' + e.target.value);
        },

        liveChangeLabelAlignment: function (e) {
            const fieldId = $(this).closest('.fancyforms-fields-settings').data('fid');
            $('#fancyforms-editor-field-id-' + fieldId).removeClass('fancyforms-label-alignment-left').removeClass('fancyforms-label-alignment-right').removeClass('fancyforms-label-alignment-center').addClass('fancyforms-label-alignment-' + e.target.value);
        },

        liveChangeOptionsLayout: function (e) {
            const fieldId = $(this).closest('.fancyforms-fields-settings').data('fid');
            $('#fancyforms-editor-field-id-' + fieldId).removeClass('fancyforms-options-layout-inline').removeClass('fancyforms-options-layout-1').removeClass('fancyforms-options-layout-2').removeClass('fancyforms-options-layout-3').removeClass('fancyforms-options-layout-4').removeClass('fancyforms-options-layout-5').removeClass('fancyforms-options-layout-6').addClass('fancyforms-options-layout-' + e.target.value);
        },

        liveChangeHeadingType: function (e) {
            const fieldId = $(this).closest('.fancyforms-fields-settings').data('fid');
            $('#fancyforms-field-' + fieldId).replaceWith(function () {
                return '<' + e.target.value + ' id="' + 'fancyforms-field-' + fieldId + '">' + $(this).html() + '</' + e.target.value + '>';
            });
        },

        liveChangeTextAlignment: function (e) {
            const fieldId = $(this).closest('.fancyforms-fields-settings').data('fid');
            $('#fancyforms-editor-field-id-' + fieldId).removeClass('fancyforms-text-alignment-left').removeClass('fancyforms-text-alignment-right').removeClass('fancyforms-text-alignment-center').addClass('fancyforms-text-alignment-' + e.target.value);
        },

        liveChangeSelectImageType: function () {
            var option = $(this).val();
            var id = $(this).attr('data-is-id');
            $('#fancyforms-field-options-' + id).find('.fancyforms-choice-input').prop('checked', false);
            $('#fancyforms-editor-field-container-' + id).find('input').prop('checked', false);
            $('#fancyforms-field-options-' + id).find('.fancyforms-choice-input').attr('type', option);
            $('#fancyforms-editor-field-container-' + id).find('input').attr('type', option);
        },

        liveChangeBorderType: function (e) {
            $('#' + this.getAttribute('data-changebordertype')).css("border-bottom-style", this.value);
        },

        liveChangeBorderWidth: function (e) {
            $('#' + this.getAttribute('data-changeborderwidth')).css("border-bottom-width", this.value + 'px');
        },

        liveChangeFieldMaxWidth: function () {
            const settings = $(this).closest('.fancyforms-fields-settings');
            const fieldId = settings.data('fid');
            const fieldMaxWidth = settings.find('input[name^="field_options[field_max_width"]').val();
            const fieldMaxWidthUnit = settings.find('select[name^="field_options[field_max_width_unit"]').val();
            if (parseInt(fieldMaxWidth) > 0) {
                $('#fancyforms-editor-field-container-' + fieldId).css('--fancyforms-width', parseInt(fieldMaxWidth) + fieldMaxWidthUnit);
            } else {
                $('#fancyforms-editor-field-container-' + fieldId).prop('style').removeProperty('--fancyforms-width');
            }
        },

        liveChangeImageMaxWidth: function () {
            const settings = $(this).closest('.fancyforms-fields-settings');
            const fieldId = settings.data('fid');
            const imageMaxWidth = settings.find('input[name^="field_options[image_max_width"]').val();
            const imageMaxWidthUnit = settings.find('select[name^="field_options[image_max_width_unit"]').val();
            if (parseInt(imageMaxWidth) > 0) {
                $('#fancyforms-editor-field-container-' + fieldId).css('--fancyforms-image-width', parseInt(imageMaxWidth) + imageMaxWidthUnit);
            } else {
                $('#fancyforms-editor-field-container-' + fieldId).prop('style').removeProperty('--fancyforms-image-width');
            }
        },

        liveChangeAddressFields: function () {
            const disableField = $(this).attr('data-disablefield');
            if ($(this).is(":checked")) {
                $(document).find('#' + disableField).addClass('fancyforms-hidden');
            } else {
                $(document).find('#' + disableField).removeClass('fancyforms-hidden');
            }
        },

        liveChangeAutoWidth: function (e) {
            const fieldId = $(this).closest('.fancyforms-fields-settings').data('fid');
            if ($(this).is(":checked")) {
                $('#fancyforms-editor-field-id-' + fieldId).addClass('fancyforms-auto-width');
            } else {
                $('#fancyforms-editor-field-id-' + fieldId).removeClass('fancyforms-auto-width');
            }
        },

        liveChangeFieldAlignment: function (e) {
            const fieldId = $(this).closest('.fancyforms-fields-settings').data('fid');
            $('#fancyforms-editor-field-id-' + fieldId).removeClass('fancyforms-field-alignment-left').removeClass('fancyforms-field-alignment-right').removeClass('fancyforms-field-alignment-center').addClass('fancyforms-field-alignment-' + e.target.value);
        },

        initFormSettings: function () {
            $('.fancyforms-ajax-udpate-button').on('click', fancyFormsAdmin.submitSettingsBuild);
            $('.fancyforms-multiple-rows').on('click', '.fancyforms-add-email', function () {
                $(this).closest('.fancyforms-multiple-rows').find('.fancyforms-multiple-email').append('<div class="fancyforms-email-row"><input type="email" name="email_to[]" value=""/><span class="mdi mdi-trash-can-outline fancyforms-delete-email-row"></span></div>');
            })
            $(document).on('click', '.fancyforms-multiple-rows .fancyforms-delete-email-row', function () {
                $(this).closest('.fancyforms-email-row').remove();
            })
        },

        addConditionRepeaterBlock: async function (e) {
            e.preventDefault();
            const parentBlock = $(this).closest('.fancyforms-form-row');
            const parentRepeaterBlock = parentBlock.find('.fancyforms-condition-repeater-blocks');
            await $.ajax({
                url: ajaxurl,
                type: 'post',
                data: {
                    action: 'fancyforms_add_more_condition_block',
                    form_id: $("#form_id").val()
                },
                success: function (msg) {
                    parentRepeaterBlock.append(msg);
                }
            })
        },

        removeConditionRepeaterBlock: function () {
            const parentBlock = $(this).closest('.fancyforms-condition-repeater-block');
            parentBlock.remove();
        },

        submitSettingsBuild: function (e) {
            e.preventDefault();
            var $thisEle = this;
            fancyFormsAdmin.preFormSave(this);
            var v = JSON.stringify($formSettings.serializeArray());
            $('#fancyforms_compact_fields').val(v);
            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: 'fancyforms_save_form_settings',
                    fancyforms_compact_fields: v,
                    nonce: fancyforms_backend_js.nonce
                },
                success: function (msg) {
                    fancyFormsAdmin.afterFormSave($thisEle);
                    var $postStuff = document.getElementById('fancyforms-form-panel');
                    var $html = document.createElement('div');
                    $html.setAttribute('class', 'fancyforms-updated-info');
                    $html.innerHTML = msg;
                    $postStuff.insertBefore($html, $postStuff.firstChild);
                }
            });
        },

        initStyleSettings: function () {
            $('.fancyforms-ajax-udpate-button').on('click', fancyFormsAdmin.submitStylesBuild);
            $('#fancyforms-form-style-template').on('change', function (e) {
                e.preventDefault();
                const templateID = $(this).val();
                var style = '';
                if (templateID) {
                    style = $(document).find('option[value="' + templateID + '"]').attr('data-style');
                }
                $('style.fancyforms-style-content').text(style);
            });
            $('#fancyforms-form-style-select').on('change', function (e) {
                e.preventDefault();
                const styleClass = $(this).find(":selected").val();
                $(document).find('form.fancyforms-form').removeClass('fancyforms-form-no-style').removeClass('fancyforms-form-default-style').removeClass('fancyforms-form-custom-style').addClass('fancyforms-form-' + styleClass);
            });
        },

        submitStylesBuild: function (e) {
            e.preventDefault();
            var $thisEle = this;
            fancyFormsAdmin.preFormSave(this);
            var v = JSON.stringify($styleSettings.serializeArray());
            $('#fancyforms_compact_fields').val(v);
            jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: 'fancyforms_save_form_style',
                    'fancyforms_compact_fields': v,
                    nonce: fancyforms_backend_js.nonce
                },
                success: function (msg) {
                    fancyFormsAdmin.afterFormSave($thisEle);
                    var $postStuff = document.getElementById('fancyforms-form-panel');
                    var $html = document.createElement('div');
                    $html.setAttribute('class', 'fancyforms-updated-info');
                    $html.innerHTML = msg;
                    $postStuff.insertBefore($html, $postStuff.firstChild);
                }
            });
        },

        initOtherSettings: function () {
            $(document).on('click', '#fancyforms-test-email-button', function (e) {
                e.preventDefault();
                const testEmailButton = $(this);
                const testEmail = $(document).find('#fancyforms-test-email').val();
                $(document).find('.fancyforms-error').remove();
                if (!fancyFormsAdmin.isEmail(testEmail)) {
                    testEmailButton.closest('.fancyforms-grid-3').append('<div class="fancyforms-error">Invalid Email</div>');
                    return;
                }
                testEmailButton.addClass('fancyforms-loading-button');
                var emailTemplate = $('#fancyforms-settings-email-template').val();
                $('.fancyforms-test-email-notice').html('');
                jQuery.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: {
                        action: 'fancyforms_test_email_template',
                        email_template: emailTemplate,
                        test_email: testEmail,
                        nonce: fancyforms_backend_js.nonce
                    },
                    success: function (res) {
                        testEmailButton.removeClass('fancyforms-loading-button');
                        const response = JSON.parse(res);
                        if (response.success) {
                            testEmailButton.closest('.fancyforms-settings-row').find('.fancyforms-test-email-notice').html('<div class="fancyforms-success">' + response.message + '</div>');
                        } else {
                            testEmailButton.closest('.fancyforms-settings-row').find('.fancyforms-test-email-notice').html('<div class="fancyforms-error">' + response.message + '</div>');
                        }
                    }
                });
            })
        },

        isEmail: function (email) {
            var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            return regex.test(email);
        },

        isUrl: function (url) {
            var regex = /^(https?|s?ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i;
            return regex.test(url);
        },

        setupFieldOptionSorting: function (sort) {
            var opts = {
                items: 'li',
                axis: 'y',
                opacity: 0.65,
                forcePlaceholderSize: false,
                handle: '.fancyforms-drag',
                helper: function (e, li) {
                    if (li.find('input[type="radio"]:checked, input[type="checkbox"]:checked').length > 0) {
                        isCheckedField = true;
                    }
                    copyHelper = li.clone().insertAfter(li);
                    return li.clone();
                },
                stop: function (e, ui) {
                    copyHelper && copyHelper.remove();
                    var fieldId = ui.item.attr('id').replace('fancyforms-option-list-', '').replace('-' + ui.item.data('optkey'), '');
                    fancyFormsAdmin.resetDisplayedOpts(fieldId);
                    var uiSortField = ui.item.find('input[type="radio"], input[type="checkbox"]');

                    if (isCheckedField) {
                        uiSortField.prop('checked', true);
                        ui.item.find('input[type="radio"]').trigger('click');
                        isCheckedField = false;
                    }
                }
            };
            $(sort).sortable(opts);
        },

        getFieldKeyFromOpt: function (object) {
            var allOpts, fieldId, fieldKey;

            allOpts = $(object).closest('.fancyforms-option-list');
            if (!allOpts.length) {
                return false;
            }

            fieldId = allOpts.attr('id').replace('fancyforms-field-options-', '');
            fieldKey = allOpts.data('key');

            return {
                fieldId: fieldId,
                fieldKey: fieldKey
            };
        },

        usingSeparateValues: function (fieldId) {
            var field = document.getElementById('separate_value_' + fieldId);
            if (field === null) {
                return false;
            } else {
                return field.checked;
            }
        },

        resetSingleOpt: function (fieldId, fieldKey, thisOpt) {
            var saved, text, defaultVal, previewInput,
                    optKey = thisOpt.data('optkey'),
                    separateValues = fancyFormsAdmin.usingSeparateValues(fieldId),
                    single = $('label[for="field_' + fieldKey + '-' + optKey + '"]'),
                    baseName = 'field_options[options_' + fieldId + '][' + optKey + ']',
                    label = $('input[name="' + baseName + '[label]"]');

            if (single.length < 1) {
                fancyFormsAdmin.resetDisplayedOpts(fieldId);

                // Set the default value.
                defaultVal = thisOpt.find('input[name^="default_value_"]');
                if (defaultVal.is(':checked') && label.length > 0) {
                    $('select[name^="item_meta[' + fieldId + ']"]').val(label.val());
                }
                return;
            }

            previewInput = single.children('input');

            if (label.length < 1) {
                // Check for other label.
                label = $('input[name="' + baseName + '"]');
                saved = label.val();
            } else if (separateValues) {
                saved = $('input[name="' + baseName + '[value]"]').val();
            } else {
                saved = label.val();
            }

            if (label.length < 1) {
                return;
            }

            // Set the displayed value.
            text = single[0].childNodes;
            text[ text.length - 1 ].nodeValue = ' ' + label.val();
            previewInput.closest('.fancyforms-choice').find('.fancyforms-field-is-label').text(saved);

            // Set saved value.
            previewInput.val(saved);

            // Set the default value.
            defaultVal = thisOpt.find('input[name^="default_value_"]');
            previewInput.prop('checked', defaultVal.is(':checked') ? true : false);
        },

        resetDisplayedOpts: function (fieldId) {
            var i, opts, type, placeholder, fieldInfo,
                    input = $('[name^="item_meta[' + fieldId + ']"]');

            if (input.length < 1) {
                return;
            }

            if (input.is('select')) {
                const selectedValDefault = input.val();
                placeholder = document.getElementById('fancyforms-placeholder-' + fieldId);

                if (placeholder !== null && placeholder.value === '') {
                    fancyFormsAdmin.fillDropdownOpts(input[0], {sourceID: fieldId});
                } else {
                    fancyFormsAdmin.fillDropdownOpts(input[0], {
                        sourceID: fieldId,
                        placeholder: placeholder.value
                    });
                }

                if ($('[name^="item_meta[' + fieldId + ']"]').length > 0 && $('[name^="item_meta[' + fieldId + ']"]')[0].contains(selectedValDefault)) {
                    $('[name^="item_meta[' + fieldId + ']"]').val(selectedValDefault);
                }
            } else {
                opts = fancyFormsAdmin.getMultipleOpts(fieldId);
                type = input.attr('type');
                $('#fancyforms-editor-field-container-' + fieldId + ' .fancyforms-choice-container').html('');
                fieldInfo = fancyFormsAdmin.getFieldKeyFromOpt($('#fancyforms-option-list-' + fieldId + '-000'));

                var container = $('#fancyforms-editor-field-container-' + fieldId + ' .fancyforms-choice-container');

                for (i = 0; i < opts.length; i++) {
                    container.append(fancyFormsAdmin.addRadioCheckboxOpt(type, opts[ i ], fieldId, fieldInfo.fieldKey));
                }
            }

            fancyFormsAdmin.adjustConditionalLogicOptionOrders(fieldId);
        },

        fillDropdownOpts: function (field, atts) {
            if (field === null) {
                return;
            }
            var sourceID = atts.sourceID,
                    placeholder = atts.placeholder,
                    showOther = atts.other;

            fancyFormsAdmin.removeDropdownOpts(field);
            var opts = fancyFormsAdmin.getMultipleOpts(sourceID),
                    hasPlaceholder = (typeof placeholder !== 'undefined');

            for (var i = 0; i < opts.length; i++) {
                var label = opts[ i ].label,
                        isOther = opts[ i ].key.indexOf('other') !== -1;

                if (hasPlaceholder && label !== '') {
                    fancyFormsAdmin.addBlankSelectOption(field, placeholder);
                } else if (hasPlaceholder) {
                    label = placeholder;
                }
                hasPlaceholder = false;

                if (!isOther || showOther) {
                    var opt = document.createElement('option');
                    opt.value = opts[ i ].saved;
                    opt.innerHTML = label;
                    field.appendChild(opt);
                }
            }
        },

        addRadioCheckboxOpt: function (type, opt, fieldId, fieldKey) {
            var single,
                    id = 'fancyforms-field-' + fieldKey + '-' + opt.key;

            single = '<div class="fancyforms-choice fancyforms-' + type + '" id="fancyforms-' + type + '-' + fieldId + '-' + opt.key + '"><label for="' + id +
                    '"><input type="' + type +
                    '" name="item_meta[' + fieldId + ']' + (type === 'checkbox' ? '[]' : '') +
                    '" value="' + opt.saved + '" id="' + id + '"' + (opt.checked ? ' checked="checked"' : '') + '> ' + opt.label + '</label>' +
                    '</div>';

            return single;
        },

        adjustConditionalLogicOptionOrders: function (fieldId) {
            var row, rowIndex, opts, logicId, valueSelect, rowOptions, expectedOrder, optionLength, optionIndex, expectedOption, optionMatch,
                    rows = document.getElementById('fancyforms-wrap').querySelectorAll('.fancyforms_logic_row'),
                    rowLength = rows.length,
                    fieldOptions = fancyFormsAdmin.getFieldOptions(fieldId),
                    optionLength = fieldOptions.length;

            for (rowIndex = 0; rowIndex < rowLength; rowIndex++) {
                row = rows[ rowIndex ];
                opts = row.querySelector('.fancyforms_logic_field_opts');

                if (opts.value != fieldId) {
                    continue;
                }

                logicId = row.id.split('_')[ 2 ];
                valueSelect = row.querySelector('select[name="field_options[hide_opt_' + logicId + '][]"]');

                for (optionIndex = optionLength - 1; optionIndex >= 0; optionIndex--) {
                    expectedOption = fieldOptions[ optionIndex ];
                    optionMatch = valueSelect.querySelector('option[value="' + expectedOption + '"]');

                    if (optionMatch === null) {
                        optionMatch = document.createElement('option');
                        optionMatch.setAttribute('value', expectedOption);
                        optionMatch.textContent = expectedOption;
                    }

                    valueSelect.prepend(optionMatch);
                }

                optionMatch = valueSelect.querySelector('option[value=""]');
                if (optionMatch !== null) {
                    valueSelect.prepend(optionMatch);
                }
            }
        },

        initBulkOptionsOverlay: function () {
            var $info = fancyFormsAdmin.initModal('#fancyforms-bulk-edit-modal', '700px');
            if ($info === false)
                return;
            $('.fancyforms-insert-preset').on('click', function (event) {
                var opts = JSON.parse(this.getAttribute('data-opts'));
                event.preventDefault();
                document.getElementById('fancyforms-bulk-options').value = opts.join('\n');
                return false;
            });

            $buildForm.on('click', 'a.fancyforms-bulk-edit-link', function (event) {
                event.preventDefault();
                var i, key, label,
                        content = '',
                        optList,
                        opts,
                        fieldId = $(this).closest('[data-fid]').data('fid'),
                        separate = fancyFormsAdmin.usingSeparateValues(fieldId);

                optList = document.getElementById('fancyforms-field-options-' + fieldId);
                if (!optList)
                    return;

                opts = optList.getElementsByTagName('li');
                document.getElementById('bulk-field-id').value = fieldId;

                for (i = 0; i < opts.length; i++) {
                    key = opts[i].getAttribute('data-optkey');
                    if (key !== '000') {
                        label = document.getElementsByName('field_options[options_' + fieldId + '][' + key + '][label]')[0];
                        if (typeof label !== 'undefined') {
                            content += label.value;
                            if (separate) {
                                content += '|' + document.getElementsByName('field_options[options_' + fieldId + '][' + key + '][value]')[0].value;
                            }
                            content += '\r\n';
                        }
                    }

                    if (i >= opts.length - 1) {
                        document.getElementById('fancyforms-bulk-options').value = content;
                    }
                }
                $info.dialog('open');
                return false;
            });

            $('#fancyforms-update-bulk-options').on('click', function () {
                var fieldId = document.getElementById('bulk-field-id').value;
                var optionType = document.getElementById('bulk-option-type').value;
                if (optionType)
                    return;
                this.classList.add('fancyforms-loading-button');
                var separate = fancyFormsAdmin.usingSeparateValues(fieldId),
                        action = 'fancyforms_import_options';
                jQuery.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: {
                        action: action,
                        field_id: fieldId,
                        opts: document.getElementById('fancyforms-bulk-options').value,
                        separate: separate,
                        nonce: fancyforms_backend_js.nonce
                    },
                    success: function (html) {
                        document.getElementById('fancyforms-field-options-' + fieldId).innerHTML = html;
                        fancyFormsAdmin.resetDisplayedOpts(fieldId);
                        if (typeof $info !== 'undefined') {
                            $info.dialog('close');
                            document.getElementById('fancyforms-update-bulk-options').classList.remove('fancyforms-loading-button');
                        }
                    }
                });
            });
        },

        initModal: function (id, width) {
            const $info = $(id);
            if (!$info.length)
                return false;
            if (typeof width === 'undefined')
                width = '550px';
            const dialogArgs = {
                dialogClass: 'fancyforms-dialog',
                modal: true,
                autoOpen: false,
                closeOnEscape: true,
                width: width,
                resizable: false,
                draggable: false,
                open: function () {
                    $('.ui-dialog-titlebar').addClass('fancyforms-hidden').removeClass('ui-helper-clearfix');
                    $('#wpwrap').addClass('fancyforms_overlay');
                    $('.fancyforms-dialog').removeClass('ui-widget ui-widget-content ui-corner-all');
                    $info.removeClass('ui-dialog-content ui-widget-content');
                    fancyFormsAdmin.bindClickForDialogClose($info);
                },
                close: function () {
                    $('#wpwrap').removeClass('fancyforms_overlay');
                    $('.spinner').css('visibility', 'hidden');

                    this.removeAttribute('data-option-type');
                    const optionType = document.getElementById('bulk-option-type');
                    if (optionType) {
                        optionType.value = '';
                    }
                }
            };
            $info.dialog(dialogArgs);
            return $info;
        },

        initNewFormModal: function () {
            $(document).on('click', '.fancyforms-trigger-modal', () => {
                $('#fancyforms-add-form-modal').addClass('fancyforms-open');
            });

            $(document).on('click', '.fancyforms-close-form-modal', () => {
                $('#fancyforms-add-form-modal').removeClass('fancyforms-open');
            });

            $(document).on('submit', '#fancyforms-add-template', function (event) {
                event.preventDefault();
                const addTemplateButton = $(this).closest('#fancyforms-add-template').find('button');
                if (!addTemplateButton.hasClass('fancyforms-updating')) {
                    var template_name = $(this).closest('#fancyforms-add-template').find('input[name=template_name]').val();
                    addTemplateButton.addClass('fancyforms-updating');
                    jQuery.ajax({
                        type: 'POST',
                        url: ajaxurl,
                        data: {
                            action: 'fancyforms_create_form',
                            name: template_name,
                            nonce: fancyforms_backend_js.nonce
                        },
                        success: function (response) {
                            const res = JSON.parse(response)
                            if (typeof res.redirect !== 'undefined') {
                                const redirect = res.redirect;
                                window.location = redirect;
                            }
                        }
                    });
                }
            });
        },

        preFormSave: function (b) {
            fancyFormsBuilder.removeWPUnload();
            if ($('form.inplace_form').length) {
                $('.inplace_save, .postbox').trigger('click');
            }

            if (b.classList.contains('fancyforms-ajax-udpate-button')) {
                b.classList.add('fancyforms-updating');
            } else {
                b.classList.add('fancyforms_loading_button');
            }
            b.setAttribute('aria-busy', 'true');
        },

        afterFormSave: function (button) {
            button.classList.remove('fancyforms-updating');
            button.classList.remove('fancyforms_loading_button');
            fancyFormsBuilder.resetOptionTextDetails();
            fieldsUpdated = 0;
            button.setAttribute('aria-busy', 'false');

            setTimeout(function () {
                $('.fancyforms-updated-info').fadeOut('slow', function () {
                    this.parentNode.removeChild(this);
                });
            }, 5000);
        },

        toggleValidationBox: function (hasValue, messageClass) {
            var $msg = $(messageClass);
            if (hasValue) {
                $msg.removeClass('fancyforms-hidden');
                $msg.closest('.fancyforms-form-container').find('.fancyforms-validation-header').removeClass('fancyforms-hidden');
            } else {
                $msg.addClass('fancyforms-hidden');
                $msg.closest('.fancyforms-form-container').find('.fancyforms-validation-header').addClass('fancyforms-hidden');
            }
        },

        addTimeDefaultValue: function () {
            const that = $(this);
            if (that.val() && !that.val().match(/^(2[0-3]|[01][0-9]):[0-5][0-9]$/)) {
                that.val('00:00');
            }
            const fieldId = that.closest('.fancyforms-fields-settings').data('fid');
            const [hourString, minute] = that.val().split(":");
            const hour = +hourString % 24;
            $('#fancyforms-editor-field-container-' + fieldId + ' .fancyforms-timepicker').val(minute && (hour % 12 || 12) + ':' + minute + (hour < 12 ? "am" : "pm"));
        },

        validateTimeValue: function () {
            const that = $(this);
            if (that.val() && !that.val().match(/^(2[0-3]|[01][0-9]):[0-5][0-9]$/)) {
                that.val('00:00');
            }
            that.trigger('input');
        },

        removeDropdownOpts: function (field) {
            var i;
            if (typeof field.options === 'undefined') {
                return;
            }

            for (i = field.options.length - 1; i >= 0; i--) {
                field.remove(i);
            }
        },

        getMultipleOpts: function (fieldId) {
            var i, saved, labelName, label, key, optObj,
                    image, savedLabel, input, field, checkbox, fieldType,
                    checked = false,
                    opts = [],
                    imageUrl = '',
                    hasImageOptions = document.getElementsByName('field_options[select_option_type_' + fieldId + ']').length > 0,
                    optVals = $('input[name^="field_options[options_' + fieldId + ']"]'),
                    separateValues = fancyFormsAdmin.usingSeparateValues(fieldId);

            for (i = 0; i < optVals.length; i++) {
                if (optVals[ i ].name.indexOf('[000]') > 0 || optVals[ i ].name.indexOf('[value]') > 0 || optVals[ i ].name.indexOf('[image_id]') > 0 || optVals[ i ].name.indexOf('[price]') > 0) {
                    continue;
                }
                saved = optVals[ i ].value;
                label = saved;
                key = optVals[ i ].name.replace('field_options[options_' + fieldId + '][', '').replace('[label]', '').replace(']', '');

                if (separateValues) {
                    labelName = optVals[ i ].name.replace('[label]', '[value]');
                    saved = $('input[name="' + labelName + '"]').val();
                }

                checked = fancyFormsBuilder.getChecked(optVals[ i ].getAttribute('class'));

                if (hasImageOptions) {
                    imageUrl = fancyFormsBuilder.getImageUrlFromInput(optVals[i]);
                    fieldType = document.getElementsByName('field_options[select_option_type_' + fieldId + ']').value;
                    label = fancyFormsBuilder.getImageLabel(label, false, imageUrl, fieldType);
                }

                optObj = {
                    saved: saved,
                    label: label,
                    checked: checked,
                    key: key
                };
                opts.push(optObj);
            }
            return opts;
        },

        getFieldOptions: function (fieldId) {
            var index, input, li,
                    listItems = document.getElementById('fancyforms-field-options-' + fieldId).querySelectorAll('.fancyforms_single_option'),
                    options = [],
                    length = listItems.length;
            for (index = 0; index < length; index++) {
                li = listItems[ index ];

                if (li.classList.contains('fancyforms-hidden')) {
                    continue;
                }

                input = li.querySelector('.field_' + fieldId + '_option');
                options.push(input.value);
            }
            return options;
        },

        getHighestOptKey: function (fieldId) {
            var i = 0,
                    optKey = 0,
                    opts = $('#fancyforms-field-options-' + fieldId + ' li'),
                    lastKey = 0;

            for (i; i < opts.length; i++) {
                optKey = opts[i].getAttribute('data-optkey');
                if (opts.length === 1) {
                    return optKey;
                }
                if (optKey !== '000') {
                    optKey = optKey.replace('other_', '');
                    optKey = parseInt(optKey, 10);
                }

                if (!isNaN(lastKey) && (optKey > lastKey || lastKey === '000')) {
                    lastKey = optKey;
                }
            }
            return lastKey;
        },

        liveChangeHideShowRow: function () {
            const that = $(this),
                    parentRow = that.closest('.fancyforms-form-container');
            var val = that.val();
            parentRow.find('.fancyforms-row-show-hide').addClass('fancyforms-hidden');
            var valArray = val.split('_');
            $.each(valArray, function (index, value) {
                parentRow.find('.fancyforms-row-show-hide.fancyforms-sub-field-' + value).removeClass('fancyforms-hidden');
            });
        },

        liveChangeHideShowLabel: function () {
            const that = $(this);
            var val = that.val();
            const parentFieldSetting = $(this).closest('.fancyforms-fields-settings'),
                    fieldId = parentFieldSetting.data('fid'),
                    fieldLabel = $('#fancyforms-editor-field-id-' + fieldId).find('label.fancyforms-label-show-hide');

            if (!val || (parentFieldSetting.find('[data-label-show-hide-checkbox]').is(':checked'))) {
                fieldLabel.addClass('fancyforms-hidden');
            } else {
                fieldLabel.removeClass('fancyforms-hidden');
            }
        },

        liveChangeHideShowLabelCheckbox: function () {
            const that = $(this);
            const parentFieldSetting = $(this).closest('.fancyforms-fields-settings'),
                    fieldId = parentFieldSetting.data('fid'),
                    fieldLabel = $('#fancyforms-editor-field-id-' + fieldId).find('label.fancyforms-label-show-hide');

            if (that.is(':checked') || !parentFieldSetting.find('[data-label-show-hide]').val()) {
                fieldLabel.addClass('fancyforms-hidden');
            } else {
                fieldLabel.removeClass('fancyforms-hidden');
            }
        },

        bindClickForDialogClose: function ($modal) {
            const closeModal = function () {
                $modal.dialog('close');
            };
            $('.ui-widget-overlay').on('click', closeModal);
            $modal.on('click', 'a.dismiss', closeModal);
        },

    };

    $(function () {
        fancyFormsAdmin.init();
    });
})(jQuery);


HTMLSelectElement.prototype.contains = function(value) {
    for (var i = 0, l = this.options.length; i < l; i++) {
        if (this.options[i].value == value) {
            return true;
        }
    }
    return false;
}