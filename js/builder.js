var fancyFormsBuilder = fancyFormsBuilder || {};

(function ($) {
    'use strict';
    let $editorFieldsWrap = $('#fancyforms-editor-fields'),
            $editorWrap = $('#fancyforms-editor-wrap'),
            $buildForm = $('#fancyforms-fields-form'),
            buildForm = document.getElementById('fancyforms-fields-form'),
            $formMeta = $('#fancyforms-meta-form'),
            $formSettings = $('#fancyforms-settings-form'),
            currentFormId = $('#fancyforms-form-id').val(),
            copyHelper = false,
            // fieldsUpdated = 0,
            autoId = 0;


    const wysiwyg = {
        init(editor, { setupCallback, height, addFocusEvents } = {}) {
            if (isTinyMceActive()) {
                setTimeout(resetTinyMce, 0);
            } else {
                initQuickTagsButtons();
            }

            setUpTinyMceVisualButtonListener();
            setUpTinyMceHtmlButtonListener();

            function initQuickTagsButtons() {
                if ('function' !== typeof window.quicktags || typeof window.QTags.instances[ editor.id ] !== 'undefined') {
                    return;
                }

                const id = editor.id;
                window.quicktags({
                    name: 'qt_' + id,
                    id: id,
                    canvas: editor,
                    settings: {id},
                    toolbar: document.getElementById('qt_' + id + '_toolbar'),
                    theButtons: {}
                });
            }

            function initRichText() {
                const key = Object.keys(tinyMCEPreInit.mceInit)[0];
                const orgSettings = tinyMCEPreInit.mceInit[ key ];

                const settings = Object.assign(
                        {},
                        orgSettings,
                        {
                            selector: '#' + editor.id,
                            body_class: orgSettings.body_class.replace(key, editor.id)
                        }
                );

                settings.setup = editor => {
                    if (addFocusEvents) {
                        function focusInCallback() {
                            $(editor.targetElm).trigger('focusin');
                            editor.off('focusin', '**');
                        }

                        editor.on('focusin', focusInCallback);

                        editor.on('focusout', function () {
                            editor.on('focusin', focusInCallback);
                        });
                    }
                    if (setupCallback) {
                        setupCallback(editor);
                    }
                };

                if (height) {
                    settings.height = height;
                }

                tinymce.init(settings);
            }

            function removeRichText() {
                tinymce.EditorManager.execCommand('mceRemoveEditor', true, editor.id);
            }

            function resetTinyMce() {
                removeRichText();
                initRichText();
            }

            function isTinyMceActive() {
                const id = editor.id;
                const wrapper = document.getElementById('wp-' + id + '-wrap');
                return null !== wrapper && wrapper.classList.contains('tmce-active');
            }

            function setUpTinyMceVisualButtonListener() {
                $(document).on(
                        'click', '#' + editor.id + '-html',
                        function () {
                            editor.style.visibility = 'visible';
                            initQuickTagsButtons(editor);
                        }
                );
            }

            function setUpTinyMceHtmlButtonListener() {
                $('#' + editor.id + '-tmce').on('click', handleTinyMceHtmlButtonClick);
            }

            function handleTinyMceHtmlButtonClick() {
                if (isTinyMceActive()) {
                    resetTinyMce();
                } else {
                    initRichText();
                }

                const wrap = document.getElementById('wp-' + editor.id + '-wrap');
                wrap.classList.add('tmce-active');
                wrap.classList.remove('html-active');
            }
        }
    };

    fancyFormsBuilder = {
        init: function () {
            fancyFormsBuilder.initBuild();

        },

        initBuild: function () {
            $('ul.fancyforms-fields-list, .fancyforms-fields-list li').disableSelection();

            fancyFormsBuilder.setupSortable('ul.fancyforms-editor-sorting');
            document.querySelectorAll('.fancyforms-fields-list > li').forEach(fancyFormsBuilder.makeDraggable);

            $editorFieldsWrap.on('click', 'li.fancyforms-editor-field-box.ui-state-default', fancyFormsBuilder.clickField);
            $editorFieldsWrap.on('click', '.fancyforms-editor-delete-action', fancyFormsBuilder.clickDeleteField);
            $editorFieldsWrap.on('mousedown', 'input, textarea, select', fancyFormsBuilder.stopFieldFocus);
            $editorFieldsWrap.on('click', 'input[type=radio], input[type=checkbox]', fancyFormsBuilder.stopFieldFocus);

            $('#fancyforms-add-fields-panel').on('click', '.fancyforms-add-field', fancyFormsBuilder.addFieldClick);

            fancyFormsBuilder.renumberMultiSteps();
            $editorWrap.on('click', '.fancyforms-step-item', fancyFormsBuilder.reorderStep);

            fancyFormsBuilder.resetToFirstStep();
        },

        setupSortable: function (sortableSelector) {
            document.querySelectorAll(sortableSelector).forEach(
                    list => {
                        fancyFormsBuilder.makeDroppable(list);
                        Array.from(list.children).forEach(
                                child => fancyFormsBuilder.makeDraggable(child, '.fancyforms-editor-move-action')
                        );
                    }
            );

        },

        makeDroppable: function (list) {
            $(list).droppable({
                accept: '.fancyforms-field-box, .fancyforms-editor-field-box',
                deactivate: fancyFormsBuilder.handleFieldDrop,
                over: fancyFormsBuilder.onDragOverDroppable,
                out: fancyFormsBuilder.onDraggableLeavesDroppable,
                tolerance: 'pointer'
            });
        },

        makeDraggable: function (draggable, handle) {
            const settings = {
                helper: function (event) {
                    const draggable = event.delegateTarget;

                    if (draggable.classList.contains('fancyforms-editor-field-box') && !draggable.classList.contains('fancyforms-editor-form-field')) {
                        const newTextFieldClone = '';
                        newTextFieldClone.querySelector('span').textContent = 'Field Group';
                        newTextFieldClone.classList.add('fancyforms-editor-field-box');
                        newTextFieldClone.classList.add('ui-sortable-helper');
                        return newTextFieldClone;
                    }

                    let copyTarget;
                    const isNewField = draggable.classList.contains('fancyforms-field-box');
                    if (isNewField) {
                        copyTarget = draggable.cloneNode(true);
                        copyTarget.classList.add('ui-sortable-helper');
                        draggable.classList.add('fancyforms-added-field');
                        return copyTarget;
                    }

                    if (draggable.hasAttribute('data-type')) {
                        const fieldType = draggable.getAttribute('data-type');
                        copyTarget = document.getElementById('fancyforms-add-fields-panel').querySelector('.fancyforms_' + fieldType);
                        copyTarget = copyTarget.cloneNode(true);
                        copyTarget.classList.add('fancyforms-editor-form-field');

                        copyTarget.classList.add('ui-sortable-helper');

                        if (copyTarget) {
                            return copyTarget.cloneNode(true);
                        }
                    }

                    return fancyFormsBuilder.div({className: 'fancyforms-field-box'});
                },
                revert: 'invalid',
                delay: 10,
                start: function (event, ui) {
                    document.body.classList.add('fancyforms-dragging');
                    ui.helper.addClass('fancyforms-sortable-helper');

                    event.target.classList.add('fancyforms-drag-fade');

                    fancyFormsBuilder.unselectFieldGroups();
                    fancyFormsBuilder.deleteEmptyDividerWrappers();
                    fancyFormsBuilder.maybeRemoveGroupHoverTarget();
                },
                stop: function () {
                    document.body.classList.remove('fancyforms-dragging');

                    const fade = document.querySelector('.fancyforms-drag-fade');
                    if (fade) {
                        fade.classList.remove('fancyforms-drag-fade');
                    }
                },
                drag: function (event, ui) {
                    // maybeScrollBuilder( event );
                    const draggable = event.target;
                    const droppable = fancyFormsBuilder.getDroppableTarget();

                    let placeholder = document.getElementById('fancyforms-placeholder');

                    if (!fancyFormsBuilder.allowDrop(draggable, droppable)) {
                        if (placeholder) {
                            placeholder.remove();
                        }
                        return;
                    }

                    if (!placeholder) {
                        placeholder = fancyFormsBuilder.tag('li', {
                            id: 'fancyforms-placeholder',
                            className: 'sortable-placeholder'
                        });
                    }
                    const ffSortableHelper = ui.helper.get(0);

                    if ('fancyforms-editor-fields' === droppable.id || droppable.classList.contains('start_divider')) {
                        placeholder.style.left = 0;
                        fancyFormsBuilder.handleDragOverYAxis({droppable, y: event.clientY, placeholder});
                        return;
                    }

                    placeholder.style.top = '';
                    fancyFormsBuilder.handleDragOverFieldGroup({droppable, x: event.clientX, placeholder});
                },
                cursor: 'grabbing',
                refreshPositions: true,
                cursorAt: {
                    top: 0,
                    left: 90 // The width of draggable button is 180. 90 should center the draggable on the cursor.
                }
            };
            if ('string' === typeof handle) {
                settings.handle = handle;
            }
            $(draggable).draggable(settings);
        },

        div: function (args) {
            return fancyFormsBuilder.tag('div', args);
        },

        tag: function (type, args = {}) {
            const output = document.createElement(type);
            if ('string' === typeof args) {
                output.textContent = args;
                return output;
            }

            const {id, className, children, child, text, data} = args;

            if (id) {
                output.id = id;
            }
            if (className) {
                output.className = className;
            }
            if (children) {
                children.forEach(child => output.appendChild(child));
            } else if (child) {
                output.appendChild(child);
            } else if (text) {
                output.textContent = text;
            }
            if (data) {
                Object.keys(data).forEach(function (dataKey) {
                    output.setAttribute('data-' + dataKey, data[dataKey]);
                });
            }
            return output;
        },

        deleteEmptyDividerWrappers: function () {
            const dividers = document.querySelectorAll('ul.start_divider');
            if (!dividers.length) {
                return;
            }
            dividers.forEach(
                    function (divider) {
                        const children = [].slice.call(divider.children);
                        children.forEach(
                                function (child) {
                                    if (0 === child.children.length) {
                                        child.remove();
                                    } else if (1 === child.children.length && 'ul' === child.firstElementChild.nodeName.toLowerCase() && 0 === child.firstElementChild.children.length) {
                                        child.remove();
                                    }
                                }
                        );
                    }
            );
        },

        maybeRemoveGroupHoverTarget: function () {
            var controls, previousHoverTarget;

            controls = document.getElementById('fancyforms_field_group_controls');
            if (null !== controls) {
                controls.style.display = 'none';
            }

            previousHoverTarget = document.querySelector('.fancyforms-field-group-hover-target');
            if (null === previousHoverTarget) {
                return false;
            }

            $('#wpbody-content').off('mousemove', fancyFormsBuilder.maybeRemoveHoverTargetOnMouseMove);
            previousHoverTarget.classList.remove('fancyforms-field-group-hover-target');
            return previousHoverTarget;
        },

        getDroppableTarget: function () {
            let droppable = document.getElementById('fancyforms-editor-fields');
            while (droppable.querySelector('.fancyforms-dropabble')) {
                droppable = droppable.querySelector('.fancyforms-dropabble');
            }
            if ('fancyforms-editor-fields' === droppable.id && !droppable.classList.contains('fancyforms-dropabble')) {
                droppable = false;
            }
            return droppable;
        },

        handleDragOverYAxis: function ( {droppable, y, placeholder}) {
            const $list = $(droppable);
            let top;

            const $children = $list.children().not('.fancyforms-editor-field-type-end_divider');
            if (0 === $children.length) {
                $list.prepend(placeholder);
                top = 0;
            } else {
                const insertAtIndex = fancyFormsBuilder.determineIndexBasedOffOfMousePositionInList($list, y);
                if (insertAtIndex === $children.length) {
                    const $lastChild = $($children.get(insertAtIndex - 1));
                    top = $lastChild.offset().top + $lastChild.outerHeight();
                    $list.append(placeholder);

                    // Make sure nothing gets inserted after the end divider.
                    const $endDivider = $list.children('.fancyforms-editor-field-type-end_divider');
                    if ($endDivider.length) {
                        $list.append($endDivider);
                    }
                } else {
                    top = $($children.get(insertAtIndex)).offset().top;
                    $($children.get(insertAtIndex)).before(placeholder);
                }
            }
            top -= $list.offset().top;
            placeholder.style.top = top + 'px';
        },

        handleDragOverFieldGroup: function ( {droppable, x, placeholder}) {
            const $row = $(droppable);
            const $children = fancyFormsBuilder.getFieldsInRow($row);
            if (!$children.length) {
                return;
            }
            let left;
            const insertAtIndex = fancyFormsBuilder.determineIndexBasedOffOfMousePositionInRow($row, x);

            if (insertAtIndex === $children.length) {
                const $lastChild = $($children.get(insertAtIndex - 1));
                left = $lastChild.offset().left + $lastChild.outerWidth();
                $row.append(placeholder);
            } else {
                left = $($children.get(insertAtIndex)).offset().left;
                $($children.get(insertAtIndex)).before(placeholder);

                const amountToOffsetLeftBy = 0 === insertAtIndex ? 4 : 8; // Offset by 8 in between rows, but only 4 for the first item in a group.
                left -= amountToOffsetLeftBy; // Offset the placeholder slightly so it appears between two fields.
            }
            left -= $row.offset().left;
            placeholder.style.left = left + 'px';
        },

        determineIndexBasedOffOfMousePositionInRow: function ($row, x) {
            var $inputs = fancyFormsBuilder.getFieldsInRow($row),
                    length = $inputs.length,
                    index, input, inputLeft, returnIndex;
            returnIndex = 0;
            for (index = length - 1; index >= 0; --index) {
                input = $inputs.get(index);
                inputLeft = $(input).offset().left;
                if (x > inputLeft) {
                    returnIndex = index;
                    if (x > inputLeft + ($(input).outerWidth() / 2)) {
                        returnIndex = index + 1;
                    }
                    break;
                }
            }
            return returnIndex;
        },

        getFieldsInRow: function ($row) {
            let $fields = $();
            const row = $row.get(0);
            if (!row.children) {
                return $fields;
            }

            Array.from(row.children).forEach(
                    child => {
                        if ('none' === child.style.display) {
                            return;
                        }
                        const classes = child.classList;
                        if (!classes.contains('fancyforms-editor-form-field') || classes.contains('fancyforms-editor-field-type-end_divider') || classes.contains('fancyforms-sortable-helper')) {
                            return;
                        }
                        $fields = $fields.add(child);
                    }
            );
            return $fields;
        },

        allowDrop: function (draggable, droppable) {
            if (false === droppable) {
                return false;
            }

            if (droppable.closest('.fancyforms-sortable-helper')) {
                return false;
            }

            if ('fancyforms-editor-fields' === droppable.id) {
                return true;
            }

            if (!droppable.classList.contains('start_divider')) {
                const $fieldsInRow = fancyFormsBuilder.getFieldsInRow($(droppable));
                if (!fancyFormsBuilder.groupCanFitAnotherField($fieldsInRow, $(draggable))) {
                    // Field group is full and cannot accept another field.
                    return false;
                }
            }

            const isNewField = draggable.classList.contains('fancyforms-added-field');
            if (isNewField) {
                return fancyFormsBuilder.allowNewFieldDrop(draggable, droppable);
            }
            return fancyFormsBuilder.allowMoveField(draggable, droppable);
        },

        groupCanFitAnotherField: function (fieldsInRow, $field) {
            var fieldId;
            if (fieldsInRow.length < 6) {
                return true;
            }
            if (fieldsInRow.length > 6) {
                return false;
            }
            fieldId = $field.attr('data-fid');
            // allow 6 if we're not changing field groups.
            return 1 === $(fieldsInRow).filter('[data-fid="' + fieldId + '"]').length;
        },

        allowNewFieldDrop: function (draggable, droppable) {
            const classes = draggable.classList;
            const newPageBreakField = classes.contains('fancyforms_break');
            const newHiddenField = classes.contains('fancyforms_hidden');
            const newSectionField = classes.contains('fancyforms_divider');
            const newEmbedField = classes.contains('fancyforms_form');

            const newFieldWillBeAddedToAGroup = !('fancyforms-editor-fields' === droppable.id || droppable.classList.contains('start_divider'));
            if (newFieldWillBeAddedToAGroup) {
                if (fancyFormsBuilder.groupIncludesBreakOrHidden(droppable)) {
                    return false;
                }
                return !newHiddenField && !newPageBreakField;
            }

            const fieldTypeIsAlwaysAllowed = !newPageBreakField && !newHiddenField && !newSectionField && !newEmbedField;
            if (fieldTypeIsAlwaysAllowed) {
                return true;
            }

            const newFieldWillBeAddedToASection = droppable.classList.contains('start_divider') || null !== droppable.closest('.start_divider');
            if (newFieldWillBeAddedToASection) {
                return !newEmbedField && !newSectionField;
            }

            return true;
        },

        allowMoveField: function (draggable, droppable) {
            if (draggable.classList.contains('fancyforms-editor-field-box') && !draggable.classList.contains('fancyforms-editor-form-field')) {
                return fancyFormsBuilder.allowMoveFieldGroup(draggable, droppable);
            }

            const isPageBreak = draggable.classList.contains('fancyforms-editor-field-type-break');
            if (isPageBreak) {
                return false;
            }

            if (droppable.classList.contains('start_divider')) {
                return fancyFormsBuilder.allowMoveFieldToSection(draggable);
            }

            const isHiddenField = draggable.classList.contains('fancyforms-editor-field-type-hidden');
            if (isHiddenField) {
                return false;
            }
            return fancyFormsBuilder.allowMoveFieldToGroup(draggable, droppable);
        },

        allowMoveFieldGroup: function (fieldGroup, droppable) {
            if (droppable.classList.contains('start_divider') && null === fieldGroup.querySelector('.start_divider')) {
                // Allow a field group with no section inside of a section.
                return true;
            }
            return false;
        },

        allowMoveFieldToSection: function (draggable) {
            const draggableIncludeEmbedForm = draggable.classList.contains('fancyforms-editor-field-type-form') || draggable.querySelector('.fancyforms-editor-field-type-form');
            if (draggableIncludeEmbedForm) {
                // Do not allow an embedded form inside of a section.
                return false;
            }

            const draggableIncludesSection = draggable.classList.contains('fancyforms-editor-field-type-divider') || draggable.querySelector('.fancyforms-editor-field-type-divider');
            if (draggableIncludesSection) {
                // Do not allow a section inside of a section.
                return false;
            }

            return true;
        },

        allowMoveFieldToGroup: function (draggable, group) {
            if (fancyFormsBuilder.groupIncludesBreakOrHidden(group)) {
                // Never allow any field beside a page break or a hidden field.
                return false;
            }

            const isFieldGroup = $(draggable).children('ul.fancyforms-editor-sorting').not('.start_divider').length > 0;
            if (isFieldGroup) {
                // Do not allow a field group directly inside of a field group unless it's in a section.
                return false;
            }

            const draggableIncludesASection = draggable.classList.contains('fancyforms-editor-field-type-divider') || draggable.querySelector('.fancyforms-editor-field-type-divider');
            const draggableIsEmbedField = draggable.classList.contains('fancyforms-editor-field-type-form');
            const groupIsInASection = null !== group.closest('.start_divider');
            if (groupIsInASection && (draggableIncludesASection || draggableIsEmbedField)) {
                // Do not allow a section or an embed field inside of a section.
                return false;
            }

            return true;
        },

        groupIncludesBreakOrHidden: function (group) {
            return null !== group.querySelector('.fancyforms-editor-field-type-multi_step, .fancyforms-editor-field-type-hidden');
        },

        unselectFieldGroups: function (event) {
            if ('undefined' !== typeof event) {
                if (null !== event.originalEvent.target.closest('#fancyforms-editor-fields')) {
                    return;
                }
                if (event.originalEvent.target.classList.contains('fancyforms-merge-fields-into-row')) {
                    return;
                }
                if (null !== event.originalEvent.target.closest('.fancyforms-merge-fields-into-row')) {
                    return;
                }
                if (event.originalEvent.target.classList.contains('fancyforms-custom-field-group-layout')) {
                    return;
                }
                if (event.originalEvent.target.classList.contains('fancyforms-cancel-custom-field-group-layout')) {
                    return;
                }
            }
            $('.fancyforms-selected-field-group').removeClass('fancyforms-selected-field-group');
            $(document).off('click', fancyFormsBuilder.unselectFieldGroups);
        },

        clickField: function (e) {
            /*jshint validthis:true */
            var currentClass, originalList;

            currentClass = e.target.classList;

            if (currentClass.contains('fancyforms-collapse-page') || currentClass.contains('fancyforms-sub-label') || e.target.closest('.dropdown') !== null) {
                return;
            }

            if (this.closest('.start_divider') !== null) {
                e.stopPropagation();
            }

            if (this.classList.contains('fancyforms-editor-field-type-divider')) {
                originalList = e.originalEvent.target.closest('ul.fancyforms-editor-sorting');
                if (null !== originalList) {
                    // prevent section click if clicking a field group within a section.
                    if (originalList.classList.contains('fancyforms-editor-field-type-divider') || originalList.parentNode.parentNode.classList.contains('start_divider')) {
                        return;
                    }
                }
            }

            fancyFormsBuilder.clickAction(this);
        },

        clickAction: function (obj) {
            var $thisobj = $(obj);
            if (obj.className.indexOf('selected') !== -1)
                return;
            if (obj.className.indexOf('fancyforms-editor-field-type-end_divider') !== -1 && $thisobj.closest('.fancyforms-editor-field-type-divider').hasClass('no_repeat_section'))
                return;
            fancyFormsBuilder.deselectFields();
            $thisobj.addClass('selected');
            fancyFormsBuilder.showFieldOptions(obj);
        },

        showFieldOptions: function (obj) {
            var i, singleField,
                    fieldId = obj.getAttribute('data-fid'),
                    fieldType = obj.getAttribute('data-type'),
                    allFieldSettings = document.querySelectorAll('.fancyforms-fields-settings:not(.fancyforms-hidden)');

            for (i = 0; i < allFieldSettings.length; i++) {
                allFieldSettings[i].classList.add('fancyforms-hidden');
            }

            singleField = document.getElementById('fancyforms-fields-settings-' + fieldId);
            fancyFormsBuilder.moveFieldSettings(singleField);

            singleField.classList.remove('fancyforms-hidden');
            document.getElementById('fancyforms-options-tab').click();

            const editor = singleField.querySelector('.wp-editor-area');
            if (editor) {
                wysiwyg.init(editor, {setupCallback: fancyFormsBuilder.setupTinyMceEventHandlers});
            }
        },

        clickDeleteField: function () {
            if (confirm("Are you sure?")) {
                fancyFormsBuilder.deleteFields($(this).attr('data-deletefield'));
            }
            return false;
        },

        deleteFields: function (fieldId) {
            var field = $('#fancyforms-editor-field-id-' + fieldId);

            fancyFormsBuilder.deleteField(fieldId);
            if (field.hasClass('fancyforms-editor-field-type-divider')) {
                field.find('li.fancyforms-editor-field-box').each(function () {
                    fancyFormsBuilder.deleteField(this.getAttribute('data-fid'));
                });
            }
            fancyFormsBuilder.toggleSectionHolder();
        },

        deleteField: function (fieldId) {
            jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: 'fancyforms_delete_field',
                    field_id: fieldId,
                    nonce: fancyforms_backend_js.nonce
                },
                success: function () {
                    var $thisField = $('#fancyforms-editor-field-id-' + fieldId),
                            type = $thisField.data('type'),
                            settings = $('#fancyforms-fields-settings-' + fieldId);

                    // Remove settings from sidebar.
                    if (settings.is(':visible')) {
                        document.getElementById('fancyforms-add-fields-tab').click();
                    }
                    settings.remove();

                    $thisField.fadeOut('fast', function () {
                        var $section = $thisField.closest('.start_divider'),
                                type = $thisField.data('type'),
                                $adjacentFields = $thisField.siblings('li.fancyforms-editor-form-field'),
                                $liWrapper;

                        if (!$adjacentFields.length) {
                            if ($thisField.is('.fancyforms-editor-field-type-end_divider')) {
                                $adjacentFields.length = $thisField.closest('li.fancyforms-editor-form-field').siblings();
                            } else {
                                $liWrapper = $thisField.closest('ul.fancyforms-editor-sorting').parent();
                            }
                        }

                        $thisField.remove();
                        if ($('#fancyforms-editor-fields li').length === 0) {
                            document.getElementById('fancyforms-editor-wrap').classList.remove('fancyforms-editor-has-fields');
                        } else if ($section.length) {
                            fancyFormsBuilder.toggleOneSectionHolder($section);
                        }
                        if ($adjacentFields.length) {
                            fancyFormsBuilder.syncLayoutClasses($adjacentFields.first());
                        } else {
                            $liWrapper.remove();
                        }

                        if (type === 'multi_step') {
                            fancyFormsBuilder.renumberMultiSteps();
                        }
                    });
                }
            });
        },

        toggleSectionHolder: function () {
            document.querySelectorAll('.start_divider').forEach(
                    function (divider) {
                        fancyFormsBuilder.toggleOneSectionHolder($(divider));
                    }
            );
        },

        addFieldClick: function () {
            /*jshint validthis:true */
            const $thisObj = $(this);
            // there is no real way to disable a <a> (with a valid href attribute) in HTML - https://css-tricks.com/how-to-disable-links/
            if ($thisObj.hasClass('disabled')) {
                return false;
            }

            $thisObj.parent('.fancyforms-field-box').addClass('fancyforms-added-field');

            const $button = $thisObj.closest('.fancyforms-field-box');
            const fieldType = $button.attr('id');

            let hasBreak = 0;
            if ('summary' === fieldType) {
                hasBreak = $editorFieldsWrap.children('li[data-type="break"]').length > 0 ? 1 : 0;
            }

            var formId = document.getElementById('fancyforms-form-id').value;
            jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: 'fancyforms_insert_field',
                    form_id: formId,
                    field_type: fieldType,
                    nonce: fancyforms_backend_js.nonce,
                },
                success: function (msg) {
                    document.getElementById('fancyforms-editor-wrap').classList.add('fancyforms-editor-has-fields');
                    const replaceWith = fancyFormsBuilder.wrapFieldLi(msg);
                    $editorFieldsWrap.append(replaceWith);
                    fancyFormsBuilder.afterAddField(msg, true);

                    replaceWith.each(
                            function () {
                                fancyFormsBuilder.makeDroppable(this.querySelector('ul.fancyforms-editor-sorting'));
                                fancyFormsBuilder.makeDraggable(this.querySelector('.fancyforms-editor-form-field'), '.fancyforms-editor-move-action');
                            }
                    );
                    fancyFormsBuilder.maybeFixRangeSlider();
                    setTimeout(function() {
                        $(document).find('.fancyforms-color-picker').wpColorPicker();
                    }, 1000)
                },
                error: fancyFormsBuilder.handleInsertFieldError
            });
            return false;
        },

        stopFieldFocus: function (e) {
            e.preventDefault();
        },

        deselectFields: function (preventFieldGroups) {
            $('li.ui-state-default.selected').removeClass('selected');
            if (!preventFieldGroups) {
                fancyFormsBuilder.unselectFieldGroups();
            }
        },

        moveFieldSettings: function (singleField) {
            if (singleField === null)
                return;
            var classes = singleField.parentElement.classList;
            if (classes.contains('fancyforms-editor-field-box') || classes.contains('divider_section_only')) {
                var endMarker = document.getElementById('fancyforms-end-form-marker');
                buildForm.insertBefore(singleField, endMarker);
            }
        },

        debounce: function (func, wait = 100) {
            let timeout;
            return function (...args) {
                clearTimeout(timeout);
                timeout = setTimeout(
                        () => func.apply(this, args),
                        wait
                        );
            };
        },

        infoModal: function (msg) {
            var $info = fancyFormsBuilder.initModal('#fancyforms_info_modal', '400px');
            if ($info === false) {
                return false;
            }
            $('.fancyforms-info-msg').html(msg);
            $info.dialog('open');
            return false;
        },

        handleFieldDrop: function (_, ui) {
            const draggable = ui.draggable[0];
            const placeholder = document.getElementById('fancyforms-placeholder');

            if (!placeholder) {
                ui.helper.remove();
                fancyFormsBuilder.syncAfterDragAndDrop();
                return;
            }
            const $previousFieldContainer = ui.helper.parent();
            const previousSection = ui.helper.get(0).closest('ul.start_divider');
            const newSection = placeholder.closest('ul.fancyforms-editor-sorting');

            if (draggable.classList.contains('fancyforms-added-field')) {
                fancyFormsBuilder.insertNewFieldByDragging(draggable.id);
            } else {
                fancyFormsBuilder.moveFieldThatAlreadyExists(draggable, placeholder);
            }

            const previousSectionId = previousSection ? parseInt(previousSection.closest('.fancyforms-editor-field-type-divider').getAttribute('data-fid')) : 0;
            const newSectionId = newSection.classList.contains('start_divider') ? parseInt(newSection.closest('.fancyforms-editor-field-type-divider').getAttribute('data-fid')) : 0;

            placeholder.remove();
            ui.helper.remove();

            const $previousContainerFields = $previousFieldContainer.length ? fancyFormsBuilder.getFieldsInRow($previousFieldContainer) : [];
            fancyFormsBuilder.maybeUpdatePreviousFieldContainerAfterDrop($previousFieldContainer, $previousContainerFields);
            fancyFormsBuilder.maybeUpdateDraggableClassAfterDrop(draggable, $previousContainerFields);

            if (previousSectionId !== newSectionId) {
                fancyFormsBuilder.updateFieldAfterMovingBetweenSections($(draggable), previousSection);
            }
            fancyFormsBuilder.syncAfterDragAndDrop();
        },

        syncAfterDragAndDrop: function () {
            fancyFormsBuilder.fixUnwrappedListItems();
            fancyFormsBuilder.toggleSectionHolder();
            fancyFormsBuilder.maybeFixEndDividers();
            fancyFormsBuilder.maybeDeleteEmptyFieldGroups();
            fancyFormsBuilder.updateFieldOrder();

            const event = new Event('fancyforms_sync_after_drag_and_drop', {bubbles: false});
            document.dispatchEvent(event);
            fancyFormsBuilder.maybeFixRangeSlider();
            setTimeout(function() {
                $(document).find('.fancyforms-color-picker').wpColorPicker();
            }, 1000)
        },

        fixUnwrappedListItems: function () {
            const lists = document.querySelectorAll('ul#fancyforms-editor-fields, ul.start_divider');
            lists.forEach(
                    list => {
                        list.childNodes.forEach(
                                child => {
                                    if ('undefined' === typeof child.classList) {
                                        return;
                                    }

                                    if (child.classList.contains('fancyforms-editor-field-type-end_divider')) {
                                        // Never wrap end divider in place.
                                        return;
                                    }

                                    if ('undefined' !== typeof child.classList && child.classList.contains('fancyforms-editor-form-field')) {
                                        fancyFormsBuilder.wrapFieldLiInPlace(child);
                                    }
                                }
                        );
                    }
            );
        },

        toggleOneSectionHolder: function ($section) {
            var noSectionFields, $rows, length, index, sectionHasFields;
            if (!$section.length) {
                return;
            }

            $rows = $section.find('ul.fancyforms-editor-sorting');
            sectionHasFields = false;
            length = $rows.length;
            for (index = 0; index < length; ++index) {
                if (0 !== fancyFormsBuilder.getFieldsInRow($($rows.get(index))).length) {
                    sectionHasFields = true;
                    break;
                }
            }

            noSectionFields = $section.parent().children('.fancyforms_no_section_fields').get(0);
            noSectionFields.classList.toggle('fancyforms_block', !sectionHasFields);
        },

        maybeFixEndDividers: function () {
            document.querySelectorAll('.fancyforms-editor-field-type-end_divider').forEach(
                    endDivider => endDivider.parentNode.appendChild(endDivider)
            );
        },

        maybeDeleteEmptyFieldGroups: function () {
            document.querySelectorAll('li.form_field_box:not(.fancyforms-editor-form-field)').forEach(
                    fieldGroup => !fieldGroup.children.length && fieldGroup.remove()
            );
        },

        updateFieldOrder: function () {
            var fields, fieldId, field, currentOrder, newOrder;
            $('#fancyforms-editor-fields').each(function (i) {
                fields = $('li.fancyforms-editor-field-box', this);
                for (i = 0; i < fields.length; i++) {
                    fieldId = fields[ i ].getAttribute('data-fid');
                    field = $('input[name="field_options[field_order_' + fieldId + ']"]');
                    currentOrder = field.val();
                    newOrder = i + 1;

                    if (currentOrder != newOrder) {
                        field.val(newOrder);
                        var singleField = document.getElementById('fancyforms-fields-settings-' + fieldId);
                        fancyFormsBuilder.moveFieldSettings(singleField);
                        // fancyFormsBuilder.fieldUpdated();
                    }
                }
            });
        },

        setupTinyMceEventHandlers: function (editor) {
            editor.on('Change', function () {
                fancyFormsBuilder.handleTinyMceChange(editor);
            });
        },

        handleTinyMceChange: function (editor) {
            if (!fancyFormsBuilder.isTinyMceActive() || tinyMCE.activeEditor.isHidden()) {
                return;
            }

            editor.targetElm.value = editor.getContent();
            $(editor.targetElm).trigger('change');
        },

        isTinyMceActive: function () {
            var activeSettings, wrapper;

            activeSettings = document.querySelector('.fancyforms-fields-settings:not(.fancyforms-hidden)');
            if (!activeSettings) {
                return false;
            }

            wrapper = activeSettings.querySelector('.wp-editor-wrap');
            return null !== wrapper && wrapper.classList.contains('tmce-active');
        },

        // fieldUpdated: function () {
        //     if (!fieldsUpdated) {
        //         fieldsUpdated = 1;
        //         window.addEventListener('beforeunload', fancyFormsBuilder.confirmExit);
        //     }
        // },

        // confirmExit: function (event) {
        //     if (fieldsUpdated) {
        //         event.preventDefault();
        //         event.returnValue = '';
        //     }
        // },

        maybeFixRangeSlider: function () {
            setTimeout(() => {
                $(document).find('.fancyforms-range-input-selector').each(function () {
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
                })
            }, 1000);
        },

        wrapFieldLiInPlace: function (li) {
            const ul = fancyFormsBuilder.tag('ul', {
                className: 'fancyforms-editor-grid-container fancyforms-editor-sorting'
            });
            const wrapper = fancyFormsBuilder.tag('li', {
                className: 'fancyforms-editor-field-box',
                child: ul
            });

            li.replaceWith(wrapper);
            ul.appendChild(li);

            fancyFormsBuilder.makeDroppable(ul);
            fancyFormsBuilder.makeDraggable(wrapper, '.fancyforms-editor-move-action');
        },

        maybeUpdatePreviousFieldContainerAfterDrop: function ($previousFieldContainer, $previousContainerFields) {
            if (!$previousFieldContainer.length) {
                return;
            }

            if ($previousContainerFields.length) {
                fancyFormsBuilder.syncLayoutClasses($previousContainerFields.first());
            } else {
                fancyFormsBuilder.maybeDeleteAnEmptyFieldGroup($previousFieldContainer.get(0));
            }
        },

        maybeUpdateDraggableClassAfterDrop: function (draggable, $previousContainerFields) {
            if (0 !== $previousContainerFields.length || 1 !== fancyFormsBuilder.getFieldsInRow($(draggable.parentNode)).length) {
                fancyFormsBuilder.syncLayoutClasses($(draggable));
            }
        },

        maybeDeleteAnEmptyFieldGroup: function (previousFieldContainer) {
            const closestFieldBox = previousFieldContainer.closest('li.fancyforms-editor-field-box');
            if (closestFieldBox && !closestFieldBox.classList.contains('fancyforms-editor-field-type-divider')) {
                closestFieldBox.remove();
            }
        },

        determineIndexBasedOffOfMousePositionInList: function ($list, y) {
            const $items = $list.children().not('.fancyforms-editor-field-type-end_divider');
            const length = $items.length;
            let index, item, itemTop, returnIndex;
            returnIndex = 0;
            for (index = length - 1; index >= 0; --index) {
                item = $items.get(index);
                itemTop = $(item).offset().top;
                if (y > itemTop) {
                    returnIndex = index;
                    if (y > itemTop + ($(item).outerHeight() / 2)) {
                        returnIndex = index + 1;
                    }
                    break;
                }
            }
            return returnIndex;
        },

        onDragOverDroppable: function (event, ui) {
            const droppable = event.target;
            const draggable = ui.draggable[0];
            if (!fancyFormsBuilder.allowDrop(draggable, droppable)) {
                droppable.classList.remove('fancyforms-dropabble');
                $(droppable).parents('ul.fancyforms-editor-sorting').addClass('fancyforms-dropabble');
                return;
            }
            document.querySelectorAll('.fancyforms-dropabble').forEach(droppable => droppable.classList.remove('fancyforms-dropabble'));
            droppable.classList.add('fancyforms-dropabble');
            $(droppable).parents('ul.fancyforms-editor-sorting').addClass('fancyforms-dropabble');
        },

        onDraggableLeavesDroppable: function (event) {
            const droppable = event.target;
            droppable.classList.remove('fancyforms-dropabble');
        },

        syncLayoutClasses: function ($item, type) {
            var $fields, size, layoutClasses, classToAddFunction;
            if ('undefined' === typeof type) {
                type = 'even';
            }
            $fields = $item.parent().children('li.fancyforms-editor-form-field, li.fancyforms-field-loading').not('.fancyforms-editor-field-type-end_divider');
            size = $fields.length;
            layoutClasses = fancyFormsBuilder.getLayoutClasses();

            if ('even' === type && 5 !== size) {
                $fields.each(fancyFormsBuilder.getSyncLayoutClass(layoutClasses, fancyFormsBuilder.getEvenClassForSize(size)));
            } else if ('clear' === type) {
                $fields.each(fancyFormsBuilder.getSyncLayoutClass(layoutClasses, ''));
            } else {
                if (-1 !== ['left', 'right', 'middle', 'even'].indexOf(type)) {
                    classToAddFunction = function (index) {
                        return fancyFormsBuilder.getClassForBlock(size, type, index);
                    };
                } else {
                    classToAddFunction = function (index) {
                        var size = type[ index ];
                        return fancyFormsBuilder.getLayoutClassForSize(size);
                    };
                }
                $fields.each(fancyFormsBuilder.getSyncLayoutClass(layoutClasses, classToAddFunction));
            }
        },

        getSyncLayoutClass: function (layoutClasses, classToAdd) {
            return function (itemIndex) {
                var currentClassToAdd, length, layoutClassIndex, currentClass, activeLayoutClass, fieldId, layoutClassesInput;
                currentClassToAdd = 'function' === typeof classToAdd ? classToAdd(itemIndex) : classToAdd;
                length = layoutClasses.length;
                activeLayoutClass = false;
                for (layoutClassIndex = 0; layoutClassIndex < length; ++layoutClassIndex) {
                    currentClass = layoutClasses[ layoutClassIndex ];
                    if (this.classList.contains(currentClass)) {
                        activeLayoutClass = currentClass;
                        break;
                    }
                }

                fieldId = this.dataset.fid;
                if ('undefined' === typeof fieldId) {
                    // we are syncing the drag/drop placeholder before the actual field has loaded.
                    // this will get called again afterward and the input will exist then.
                    this.classList.add(currentClassToAdd);
                    return;
                }

                fancyFormsBuilder.moveFieldSettings(document.getElementById('fancyforms-fields-settings-' + fieldId));
                var gridClassInput = document.getElementById('fancyforms-grid-class-' + fieldId);

                if (null === gridClassInput) {
                    // not every field type has a layout class input.
                    return;
                }

                gridClassInput.value = currentClassToAdd;
                fancyFormsBuilder.changeFieldClass(document.getElementById('fancyforms-editor-field-id-' + fieldId), currentClassToAdd);
            };
        },

        getLayoutClasses: function () {
            return ['fancyforms-grid-1', 'fancyforms-grid-2', 'fancyforms-grid-3', 'fancyforms-grid-4', 'fancyforms-grid-5', 'fancyforms-grid-6', 'fancyforms-grid-7', 'fancyforms-grid-8', 'fancyforms-grid-9', 'fancyforms-grid-10', 'fancyforms-grid-11', 'fancyforms-grid-12'];
        },

        getSectionForFieldPlacement: function (currentItem) {
            var section = '';
            if (typeof currentItem !== 'undefined' && !currentItem.hasClass('fancyforms-editor-field-type-divider')) {
                section = currentItem.closest('.fancyforms-editor-field-type-divider');
            }
            return section;
        },

        getFormIdForFieldPlacement: function (section) {
            var formId = '';
            if (typeof section[0] !== 'undefined') {
                var sDivide = section.children('.start_divider');
                sDivide.children('.fancyforms-editor-field-type-end_divider').appendTo(sDivide);
                if (typeof section.attr('data-formid') !== 'undefined') {
                    var fieldId = section.attr('data-fid');
                    formId = $('input[name="field_options[form_select_' + fieldId + ']"]').val();
                }
            }
            if (typeof formId === 'undefined' || formId === '') {
                formId = currentFormId;
            }
            return formId;
        },

        getSectionIdForFieldPlacement: function (section) {
            var sectionId = 0;
            if (typeof section[0] !== 'undefined') {
                sectionId = section.attr('id').replace('fancyforms-editor-field-id-', '');
            }

            return sectionId;
        },

        updateFieldAfterMovingBetweenSections: function (currentItem, previousSection) {
            if (!currentItem.hasClass('fancyforms-editor-form-field')) {
                fancyFormsBuilder.getFieldsInRow($(currentItem.get(0).firstChild)).each(
                        function () {
                            fancyFormsBuilder.updateFieldAfterMovingBetweenSections($(this), previousSection);
                        }
                );
                return;
            }
            const fieldId = currentItem.attr('id').replace('fancyforms-editor-field-id-', '');
            const section = fancyFormsBuilder.getSectionForFieldPlacement(currentItem);
            const formId = fancyFormsBuilder.getFormIdForFieldPlacement(section);
            const sectionId = fancyFormsBuilder.getSectionIdForFieldPlacement(section);
            const previousFormId = previousSection ? fancyFormsBuilder.getFormIdForFieldPlacement($(previousSection.parentNode)) : 0;

            jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: 'fancyforms_update_field_after_move',
                    form_id: formId,
                    field: fieldId,
                    section_id: sectionId,
                    previous_form_id: previousFormId,
                    nonce: fancyforms_backend_js.nonce
                },
                success: function () {
                    fancyFormsBuilder.toggleSectionHolder();
                    fancyFormsBuilder.updateInSectionValue(fieldId, sectionId);
                }
            });
        },

        insertNewFieldByDragging: function (fieldType) {
            const placeholder = document.getElementById('fancyforms-placeholder');
            const loadingID = fieldType.replace('|', '-') + '_' + fancyFormsBuilder.getAutoId();
            const loading = fancyFormsBuilder.tag('li', {
                id: loadingID,
                className: 'fancyforms-wait fancyforms-field-loading'
            });
            const $placeholder = $(loading);
            const currentItem = $(placeholder);
            const section = fancyFormsBuilder.getSectionForFieldPlacement(currentItem);
            const formId = fancyFormsBuilder.getFormIdForFieldPlacement(section);
            const sectionId = fancyFormsBuilder.getSectionIdForFieldPlacement(section);
            placeholder.parentNode.insertBefore(loading, placeholder);
            placeholder.remove();
            fancyFormsBuilder.syncLayoutClasses($placeholder);
            let hasBreak = 0;
            if ('summary' === fieldType) {
                hasBreak = $('.fancyforms-field-loading#' + loadingID).prevAll('li[data-type="break"]').length ? 1 : 0;
            }
            jQuery.ajax({
                type: 'POST', url: ajaxurl,
                data: {
                    action: 'fancyforms_insert_field',
                    form_id: formId,
                    field_type: fieldType,
                    nonce: fancyforms_backend_js.nonce,
                },
                success: function (msg) {
                    let replaceWith;
                    document.getElementById('fancyforms-editor-wrap').classList.add('fancyforms-editor-has-fields');
                    const $siblings = $placeholder.siblings('li.fancyforms-editor-form-field').not('.fancyforms-editor-field-type-end_divider');
                    if (!$siblings.length) {
                        replaceWith = fancyFormsBuilder.wrapFieldLi(msg);
                    } else {
                        replaceWith = fancyFormsBuilder.msgAsObject(msg);
                        if (!$placeholder.get(0).parentNode.parentNode.classList.contains('ui-draggable')) {
                            fancyFormsBuilder.makeDraggable($placeholder.get(0).parentNode.parentNode, '.fancyforms-editor-move-action');
                        }
                    }
                    $placeholder.replaceWith(replaceWith);
                    fancyFormsBuilder.updateFieldOrder();
                    fancyFormsBuilder.afterAddField(msg, false);
                    if ($siblings.length) {
                        fancyFormsBuilder.syncLayoutClasses($siblings.first());
                    }
                    fancyFormsBuilder.toggleSectionHolder();
                    if (!$siblings.length) {
                        fancyFormsBuilder.makeDroppable(replaceWith.get(0).querySelector('ul.fancyforms-editor-sorting'));
                        fancyFormsBuilder.makeDraggable(replaceWith.get(0).querySelector('li.fancyforms-editor-form-field'), '.fancyforms-editor-move-action');
                    } else {
                        fancyFormsBuilder.makeDraggable(replaceWith.get(0), '.fancyforms-editor-move-action');
                    }
                },
                error: fancyFormsBuilder.handleInsertFieldError
            });
        },

        moveFieldThatAlreadyExists: function (draggable, placeholder) {
            placeholder.parentNode.insertBefore(draggable, placeholder);
        },

        msgAsObject: function (msg) {
            const element = fancyFormsBuilder.div();
            element.innerHTML = msg;
            return $(element.innerHTML);
        },

        handleInsertFieldError: function (jqXHR, _, errorThrown) {
            fancyFormsBuilder.maybeShowInsertFieldError(errorThrown, jqXHR);
        },

        maybeShowInsertFieldError: function (errorThrown, jqXHR) {
            if (!jqXHRAborted(jqXHR)) {
                fancyFormsBuilder.infoModal(errorThrown + '. Please try again.');
            }
        },

        jqXHRAborted: function (jqXHR) {
            return jqXHR.status === 0 || jqXHR.readyState === 0;
        },

        getAutoId: function () {
            return ++autoId;
        },

        maybeRemoveHoverTargetOnMouseMove: function (event) {
            var elementFromPoint = document.elementFromPoint(event.clientX, event.clientY);
            if (null !== elementFromPoint && null !== elementFromPoint.closest('#fancyforms-editor-fields')) {
                return;
            }
            fancyFormsBuilder.maybeRemoveGroupHoverTarget();
        },

        wrapFieldLi: function (field) {
            const wrapper = fancyFormsBuilder.div();
            if ('string' === typeof field) {
                wrapper.innerHTML = field;
            } else {
                wrapper.appendChild(field);
            }

            let result = $();
            Array.from(wrapper.children).forEach(
                    li => {
                        result = result.add(
                                $('<li>')
                                .addClass('fancyforms-editor-field-box')
                                .html($('<ul>').addClass('fancyforms-editor-grid-container fancyforms-editor-sorting').append(li))
                                );
                    }
            );
            return result;
        },

        afterAddField: function (msg, addFocus) {
            var regex = /id="(\S+)"/,
                    match = regex.exec(msg),
                    field = document.getElementById(match[1]),
                    section = '#' + match[1] + '.fancyforms-editor-field-type-divider ul.fancyforms-editor-sorting.start_divider',
                    $thisSection = $(section),
                    toggled = false,
                    $parentSection;
            var type = field.getAttribute('data-type');

            fancyFormsBuilder.setupSortable(section);
            if ($thisSection.length) {
                $thisSection.parent('.fancyforms-editor-field-box').children('.fancyforms_no_section_fields').addClass('fancyforms_block');
            } else {
                $parentSection = $(field).closest('ul.fancyforms-editor-sorting.start_divider');
                if ($parentSection.length) {
                    fancyFormsBuilder.toggleOneSectionHolder($parentSection);
                    toggled = true;
                }
            }

            $(field).addClass('fancyforms-newly-added');
            setTimeout(function () {
                field.classList.remove('fancyforms-newly-added');
            }, 1000);

            if (addFocus) {
                var bounding = field.getBoundingClientRect(),
                        container = document.getElementById('fancyforms-form-panel'),
                        inView = (bounding.top >= 0 &&
                                bounding.left >= 0 &&
                                bounding.right <= (window.innerWidth || document.documentElement.clientWidth) &&
                                bounding.bottom <= (window.innerHeight || document.documentElement.clientHeight)
                                );

                if (!inView) {
                    container.scroll({
                        top: container.scrollHeight,
                        left: 0,
                        behavior: 'smooth'
                    });
                }

                if (toggled === false) {
                    fancyFormsBuilder.toggleOneSectionHolder($thisSection);
                }
            }

            fancyFormsBuilder.deselectFields();

            const addedEvent = new Event('fancyforms_added_field', {bubbles: false});
            addedEvent.ffField = field;
            addedEvent.ffSection = section;
            addedEvent.ffType = type;
            addedEvent.ffToggles = toggled;
            document.dispatchEvent(addedEvent);
            if(type="multi_step") {
                fancyFormsBuilder.resetToFirstStep();
                fancyFormsBuilder.renumberMultiSteps();
            }
        },

        getClassForBlock: function (size, type, index) {
            if ('even' === type) {
                return fancyFormsBuilder.getEvenClassForSize(size, index);
            } else if ('middle' === type) {
                if (3 === size) {
                    return 1 === index ? 'fancyforms-grid-6' : 'fancyforms-grid-3';
                }
                if (5 === size) {
                    return 2 === index ? 'fancyforms-grid-4' : 'fancyforms-grid-2';
                }
            } else if ('left' === type) {
                return 0 === index ? fancyFormsBuilder.getLargeClassForSize(size) : fancyFormsBuilder.getSmallClassForSize(size);
            } else if ('right' === type) {
                return index === size - 1 ? fancyFormsBuilder.getLargeClassForSize(size) : fancyFormsBuilder.getSmallClassForSize(size);
            }
            return 'fancyforms-grid-12';
        },

        getEvenClassForSize: function (size, index) {
            if (-1 !== [2, 3, 4, 6].indexOf(size)) {
                return fancyFormsBuilder.getLayoutClassForSize(12 / size);
            }
            if (5 === size && 'undefined' !== typeof index) {
                return 0 === index ? 'fancyforms-grid-4' : 'fancyforms-grid-2';
            }
            return 'fancyforms-grid-12';
        },

        getSmallClassForSize: function (size) {
            switch (size) {
                case 2:
                case 3:
                    return 'fancyforms-grid-3';
                case 4:
                    return 'fancyforms-grid-2';
                case 5:
                    return 'fancyforms-grid-2';
                case 6:
                    return 'fancyforms-grid-1';
            }
            return 'fancyforms-grid-12';
        },

        getLargeClassForSize: function (size) {
            switch (size) {
                case 2:
                    return 'fancyforms-grid-9';
                case 3:
                case 4:
                    return 'fancyforms-grid-6';
                case 5:
                    return 'fancyforms-grid-4';
                case 6:
                    return 'fancyforms-grid-7';
            }
            return 'fancyforms-grid-12';
        },

        getLayoutClassForSize: function (size) {
            return 'fancyforms-grid-' + size;
        },

        resetOptionTextDetails: function () {
            $('.fancyforms-fields-settings ul input[type="text"][name^="field_options[options_"]').filter('[data-value-on-load]').removeAttr('data-value-on-load');
            $('input[type="hidden"][name^=optionmap]').remove();
        },

        addBlankSelectOption: function (field, placeholder) {
            var opt = document.createElement('option'),
                    firstChild = field.firstChild;

            opt.value = '';
            opt.innerHTML = placeholder;
            if (firstChild !== null) {
                field.insertBefore(opt, firstChild);
                field.selectedIndex = 0;
            } else {
                field.appendChild(opt);
            }
        },

        getImageLabel: function (label, showLabelWithImage, imageUrl, fieldType) {
            var imageLabelClass, fullLabel,
                    originalLabel = label;

            fullLabel = '<div class="fancyforms-field-is-image">';
            fullLabel += '<span class="fancyforms-field-is-checked mdi-check-circle"></span>';
            if (imageUrl) {
                fullLabel += '<img src="' + imageUrl + '" alt="' + originalLabel + '" />';
            }
            fullLabel += '</div>';
            fullLabel += '<div class="fancyforms-field-is-label">' + originalLabel + '</div>';

            imageLabelClass = showLabelWithImage ? ' fancyforms-field-is-has-label' : '';

            return ('<div class="fancyforms-field-is-container' + imageLabelClass + '">' + fullLabel + '</div>');
        },

        getImageUrlFromInput: function (optVal) {
            var img, wrapper = $(optVal).closest('li').find('.fancyforms-is-image-preview');

            if (!wrapper.length) {
                return '';
            }

            img = wrapper.find('img');
            if (!img.length) {
                return '';
            }

            return img.attr('src');
        },

        getChecked: function (id) {
            var field = $('.' + id);

            if (field.length === 0) {
                return false;
            }

            var checkbox = field.siblings('.fancyforms-choice-input');
            return checkbox.length && checkbox.prop('checked');
        },


        changeFieldClass: function (field, setting) {
            var classes = field.className.split(' ');
            var filteredClasses = classes.filter(function (value, index, arr) {
                return value.indexOf('fancyforms-grid-');
            });
            filteredClasses.push(setting);
            field.className = filteredClasses.join(' ');
        },

        removeWPUnload: function () {
            window.onbeforeunload = null;
            var w = $(window);
            w.off('beforeunload.widgets');
            w.off('beforeunload.edit-post');
        },

        maybeAddSaveAndDragIcons: function (fieldId) {
            var fieldOptions = document.querySelectorAll(`[id^=fancyforms-option-list-${fieldId}-]`);

            if (fieldOptions.length < 2) {
                return;
            }

            let options = [...fieldOptions].slice(1);
            options.forEach((li, _key) => {
                if (li.classList.contains('fancyforms_other_option')) {
                    return;
                }
            });
        },

        renumberMultiSteps: function () {
            var i, containerClass,
                steps = $('.fancyforms-step-num');

            if (steps.length > 1) {
                $('#fancyforms-first-step').removeClass('fancyforms-hidden');
                for (i = 0; i < steps.length; i++) {
                    steps[i].textContent = (i + 1);
                }
            } else {
                $('#fancyforms-first-step').addClass('fancyforms-hidden');
            }
        },


        toggleCollapseStep: function(field) {
            var toCollapse = fancyFormsBuilder.getAllFieldsForStep(field.get(0).parentNode.closest('li.fancyforms-editor-field-box').nextElementSibling);
            fancyFormsBuilder.toggleStep(field, toCollapse);
        },

        reorderStep: function() {
            var field = $(this).closest('.fancyforms-editor-form-field[data-type="multi_step"]');
            if (field.length) {
                fancyFormsBuilder.toggleCollapseStep(field);
            } else {
                fancyFormsBuilder.toggleCollapseFirstStep();
            }
        },

        toggleCollapseFirstStep: function() {
            var topLevel = document.getElementById('fancyforms-first-step'),
                firstField = document.getElementById('fancyforms-editor-fields').firstElementChild,
                toCollapse = fancyFormsBuilder.getAllFieldsForStep(firstField);

            if (firstField.getAttribute('data-type') === 'multi_step') {
                return;
            }
            fancyFormsBuilder.toggleStep(jQuery(topLevel), toCollapse);
        },

        toggleStep: function(field, toCollapse) {
            var i,
                fieldCount = toCollapse.length;

            jQuery('ul#fancyforms-editor-fields > li.fancyforms-editor-field-box').each(function() {
                const tfield = $(this),
                    isStepField = tfield.find('.fancyforms-editor-form-field[data-type="multi_step"]');

                if(isStepField.length < 1) {
                    tfield.slideUp(150, function() {
                        tfield.hide();
                    });
                }
            });

            for (i = 0; i < fieldCount; i++) {
                const stepItem = $(toCollapse[i]);
                if (stepItem.find('.fancyforms-editor-form-field[data-type="multi_step"]').length > 0) {
                    break;
                }
                if (i === fieldCount - 1) {
                    stepItem.slideDown(150, function() {
                        toCollapse.show();
                    });
                } else {
                    stepItem.slideDown(150);
                }
            }
        },

        getAllFieldsForStep: function(firstWrapper) {
            var $fieldsForPage, currentWrapper;
            $fieldsForPage = jQuery();
            if (null === firstWrapper) {
                return $fieldsForPage;
            }

            currentWrapper = firstWrapper;
            do {
                if (null !== currentWrapper.querySelector('.edit_field_type_break')) {
                    break;
                }
                $fieldsForPage = $fieldsForPage.add(jQuery(currentWrapper));
                currentWrapper = currentWrapper.nextElementSibling;
            } while (null !== currentWrapper);
            return $fieldsForPage;
        },

        resetToFirstStep: function() {
            if($('.fancyforms-step-item').length > 1) {
                $('.fancyforms-step-item#fancyforms-first-step').trigger('click');
            }
        }
    }

    $(function () {
        fancyFormsBuilder.init();
    });

})(jQuery);
