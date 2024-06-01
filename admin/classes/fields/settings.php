<?php
defined('ABSPATH') || die();
?>

<div class="fancyforms-fields-settings fancyforms-hidden fancyforms-fields-type-<?php echo esc_attr($field_type); ?>" id="fancyforms-fields-settings-<?php echo esc_attr($field_id); ?>" data-fid="<?php echo esc_attr($field_id); ?>">
    <input type="hidden" name="fancyforms-form-submitted[]" value="<?php echo absint($field_id); ?>" />
    <input type="hidden" name="field_options[field_order_<?php echo absint($field_id); ?>]" value="<?php echo esc_attr($field['field_order']); ?>"/>
    <input type="hidden" name="field_options[grid_id_<?php echo absint($field_id); ?>]" value="<?php echo esc_attr($field['grid_id']); ?>" id="fancyforms-grid-class-<?php echo esc_attr($field_id); ?>" />

    <div class="fancyforms-field-panel-header">
        <h3><?php printf(esc_html__('%s Field', 'fancy-forms'), esc_html($type_name)); ?></h3>
        <div class="fancyforms-field-panel-id">(ID <?php echo esc_html($field_id); ?>)</div>
    </div>

    <div class="fancyforms-form-container fancyforms-grid-container">
        <?php
        if ($field_type === 'captcha' && !FancyFormsFieldCaptcha::should_show_captcha()) {
            ?>
            <div class="fancyforms-form-row">
                <?php printf(esc_html__('Captchas will not work untill the Site and Secret Keys are set up. Add Keys %1$shere%2$s.', 'fancy-forms'), '<a href="?page=fancyforms-settings" target="_blank">', '</a>'); ?>
                <label class="fancyforms-field-desc"><?php printf(esc_html__('Tutorial to %1$sGenerate Site and Secret Keys%2$s', 'fancy-forms'), '<a href="https://fancywp.com/" target="_blank">', '</a>'); ?></label>
            </div>
            <?php
        }

        if ($display['label']) {
            ?>
            <div class="fancyforms-form-row">
                <label><?php esc_html_e('Field Label', 'fancy-forms'); ?> </label>
                <input type="text" name="field_options[name_<?php echo absint($field_id); ?>]" value="<?php echo esc_attr($field['name']); ?>" data-changeme="fancyforms-editor-field-label-text-<?php echo esc_attr($field_id); ?>" data-label-show-hide="fancyforms-label-show-hide" />
            </div>

            <div class="fancyforms-form-row fancyforms-grid-3">
                <label><?php esc_html_e('Label Position', 'fancy-forms'); ?></label>
                <select name="field_options[label_position_ <?php echo absint($field_id); ?>]">
                    <option value="top" <?php isset($field['label_position']) ? selected($field['label_position'], 'top') : ''; ?>>
                        <?php esc_html_e('Top', 'fancy-forms'); ?>
                    </option>
                    <option value="left" <?php isset($field['label_position']) ? selected($field['label_position'], 'left') : ''; ?>>
                        <?php esc_html_e('Left', 'fancy-forms'); ?>
                    </option>
                    <option value="right" <?php isset($field['label_position']) ? selected($field['label_position'], 'right') : ''; ?>>
                        <?php esc_html_e('Right', 'fancy-forms'); ?>
                    </option>
                    <option value="hide" <?php isset($field['label_position']) ? selected($field['label_position'], 'hide') : ''; ?>>
                        <?php esc_html_e('Hide', 'fancy-forms'); ?>
                    </option>
                </select>
            </div>

            <div class="fancyforms-form-row fancyforms-grid-3">
                <label><?php esc_html_e('Label Alignment', 'fancy-forms'); ?></label>
                <select name="field_options[label_alignment_<?php echo absint($field_id); ?>]">
                    <option value="left" <?php selected($field['label_alignment'], 'left'); ?>>
                        <?php esc_html_e('Left', 'fancy-forms'); ?>
                    </option>
                    <option value="right" <?php selected($field['label_alignment'], 'right'); ?>>
                        <?php esc_html_e('Right', 'fancy-forms'); ?>
                    </option>
                    <option value="center" <?php selected($field['label_alignment'], 'center'); ?>>
                        <?php esc_html_e('Center', 'fancy-forms'); ?>
                    </option>
                </select>
            </div>

            <div class="fancyforms-form-row">
                <label for="fancyforms-hide-label-field-<?php echo absint($field_id); ?>">
                    <input id="fancyforms-hide-label-field-<?php echo absint($field_id); ?>" type="checkbox" name="field_options[hide_label_<?php echo absint($field_id); ?>]" value="1" <?php checked((isset($field['hide_label']) && $field['hide_label']), 1); ?> data-label-show-hide-checkbox="fancyforms-label-show-hide" />
                    <?php esc_html_e('Hide Label', 'fancy-forms'); ?>
                </label>
            </div>
            <?php
        }

        if ($field_type === 'heading') {
            ?>
            <div class="fancyforms-form-row">
                <label><?php esc_html_e('Select Heading', 'fancy-forms'); ?></label>
                <select name="field_options[heading_type_<?php echo esc_attr($field_id); ?>]">
                    <option value="h1" <?php isset($field['heading_type']) ? selected($field['heading_type'], 'h1') : ''; ?>>
                        <?php esc_html_e('H1', 'fancy-forms'); ?>
                    </option>
                    <option value="h2" <?php isset($field['heading_type']) ? selected($field['heading_type'], 'h2') : ''; ?>>
                        <?php esc_html_e('H2', 'fancy-forms'); ?>
                    </option>
                    <option value="h3" <?php isset($field['heading_type']) ? selected($field['heading_type'], 'h3') : ''; ?> >
                        <?php esc_html_e('H3', 'fancy-forms'); ?>
                    </option>
                    <option value="h4" <?php isset($field['heading_type']) ? selected($field['heading_type'], 'h4') : ''; ?>>
                        <?php esc_html_e('H4', 'fancy-forms'); ?>
                    </option>
                    <option value="h5" <?php isset($field['heading_type']) ? selected($field['heading_type'], 'h5') : ''; ?>>
                        <?php esc_html_e('H5', 'fancy-forms'); ?>
                    </option>
                    <option value="h6" <?php isset($field['heading_type']) ? selected($field['heading_type'], 'h6') : ''; ?>>
                        <?php esc_html_e('H6', 'fancy-forms'); ?>
                    </option>
                </select>
            </div>
            <?php
        }

        if ($display['content']) {
            ?>
            <div class="fancyforms-form-row">
                <label><?php esc_html_e('Content', 'fancy-forms'); ?></label>
                <div class="fancyforms-form-textarea">
                    <textarea name="field_options[content_<?php echo esc_attr($field_id); ?>]" data-changeme="fancyforms-field-<?php echo esc_attr($field_id) ?>"><?php echo isset($field['content']) ? esc_textarea($field['content']) : ''; ?></textarea>
                </div>
            </div>

            <div class="fancyforms-form-row">
                <label><?php esc_html_e('Text Alignment', 'fancy-forms'); ?></label>
                <select name="field_options[text_alignment_<?php echo esc_attr($field_id); ?>]">
                    <option value="left" <?php isset($field['text_alignment']) ? selected($field['text_alignment'], 'left') : ''; ?>>
                        <?php esc_html_e('Left', 'fancy-forms'); ?>
                    </option>
                    <option value="right" <?php isset($field['text_alignment']) ? selected($field['text_alignment'], 'right') : ''; ?>>
                        <?php esc_html_e('Right', 'fancy-forms'); ?>
                    </option>
                    <option value="center" <?php isset($field['text_alignment']) ? selected($field['text_alignment'], 'center') : ''; ?>>
                        <?php esc_html_e('Center', 'fancy-forms'); ?>
                    </option>
                </select>
            </div>
            <?php
        }

        if ($field_type === 'image_select') {
            ?>
            <div class="fancyforms-form-row">
                <label><?php esc_html_e('Select Type', 'fancy-forms'); ?></label>
                <select class="fancyforms-select-image-type" name="field_options[select_option_type_<?php echo esc_attr($field_id); ?>]" data-is-id="<?php echo esc_attr($field_id); ?>">
                    <option value="checkbox" <?php isset($field['select_option_type']) ? selected($field['select_option_type'], 'checkbox') : ''; ?>>
                        <?php esc_html_e('Multiple', 'fancy-forms'); ?>
                    </option>
                    <option value="radio" <?php isset($field['select_option_type']) ? selected($field['select_option_type'], 'radio') : ''; ?>>
                        <?php esc_html_e('Single', 'fancy-forms'); ?>
                    </option>
                </select>
            </div>
            <?php
            $columns = array(
                'small' => esc_html__('Small', 'fancy-forms'),
                'medium' => esc_html__('Medium', 'fancy-forms'),
                'large' => esc_html__('Large', 'fancy-forms'),
                'xlarge' => esc_html__('Extra Large', 'fancy-forms'),
            );
            ?>
            <div class="fancyforms-form-row">
                <label><?php esc_html_e('Image Size', 'fancy-forms'); ?></label>
                <select name="field_options[image_size_<?php echo absint($field_id); ?>]">
                    <?php foreach ($columns as $col => $col_label) { ?>
                        <option value="<?php echo esc_attr($col); ?>" <?php selected($field['image_size'], $col); ?>>
                            <?php echo esc_html($col_label); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <?php
        }

        if ($field_type === 'image') {
            $image_id = $image = '';
            if (isset($field['image_id'])) {
                $image_id = $field['image_id'];
                $image = wp_get_attachment_image_src($field['image_id'], 'full');
                $image = isset($image[0]) ? $image[0] : '';
            }
            ?>
            <div class="fancyforms-form-row">
                <label><?php esc_html_e('Select Image', 'fancy-forms'); ?></label>
                <div class="fancyforms-image-preview">
                    <input type="hidden" class="fancyforms-image-id" name="field_options[image_id_<?php echo esc_attr($field_id); ?>]" id="fancyforms-field-image-<?php echo absint($field_id); ?>" value="<?php echo esc_attr($image_id); ?>"/>
                    <div class="fancyforms-image-preview-wrap<?php echo ($image ? '' : ' fancyforms-hidden'); ?>">
                        <div class="fancyforms-image-preview-box">
                            <img id="fancyforms-image-preview-<?php echo absint($field_id); ?>" src="<?php echo esc_url($image); ?>" />
                        </div>
                        <button type="button" class="button fancyforms-remove-image">
                            <span class="mdi mdi-trash-can-outline"></span>
                            <?php esc_html_e('Delete', 'fancy-forms'); ?>
                        </button>
                    </div>
                    <button type="button" class="button fancyforms-choose-image<?php echo($image ? ' fancyforms-hidden' : ''); ?>">
                        <span class="mdi mdi-tray-arrow-up"></span>
                        <?php esc_attr_e('Upload image', 'fancy-forms'); ?>
                    </button>
                </div>
            </div>
            <?php
        }

        if ($field_type === 'spacer') {
            ?>
            <div class="fancyforms-form-row">
                <label><?php esc_html_e('Height (px)', 'fancy-forms'); ?></label>
                <input type="number" name="field_options[spacer_height_<?php echo absint($field_id); ?>]" value="<?php echo isset($field['spacer_height']) ? esc_attr($field['spacer_height']) : ''; ?>" data-changeheight="field_change_height_<?php echo absint($field_id) ?>"/>
            </div>
            <?php
        }

        if ($field_type === 'time') {
            ?>
            <div class="fancyforms-form-row">
                <label><?php esc_html_e('Step', 'fancy-forms'); ?></label>
                <input type="number" name="field_options[step_<?php echo absint($field_id); ?>]" value="<?php echo isset($field['step']) ? esc_attr($field['step']) : ''; ?>" min="1"/>
            </div>
            <div class="fancyforms-form-row">
                <label><?php esc_html_e('Min Time', 'fancy-forms'); ?></label>
                <input type="text" class="min-value-field" name="field_options[min_time_<?php echo absint($field_id); ?>]" value="<?php echo isset($field['min_time']) ? esc_attr($field['min_time']) : ''; ?>"/>
            </div>
            <div class="fancyforms-form-row">
                <label><?php esc_html_e('Max Time', 'fancy-forms'); ?></label>
                <input type="text" class="max-value-field" name="field_options[max_time_<?php echo absint($field_id); ?>]" value="<?php echo isset($field['max_time']) ? esc_attr($field['max_time']) : ''; ?>"/>
            </div>
            <?php
        }

        if ($field_type === 'date') {
            ?>
            <div class="fancyforms-form-row">
                <label><?php esc_html_e('Date Format', 'fancy-forms'); ?></label>
                <select name="field_options[date_format_<?php echo esc_attr($field_id); ?>]">
                    <option value="MM dd, yy" <?php isset($field['date_format']) ? selected($field['date_format'], 'MM dd, yy') : ''; ?>>
                        <?php esc_html_e('September 19, 2023', 'fancy-forms'); ?>
                    </option>
                    <option value="yy-mm-dd" <?php isset($field['date_format']) ? selected($field['date_format'], 'yy-mm-dd') : ''; ?>>
                        <?php esc_html_e('2023-09-19', 'fancy-forms'); ?>
                    </option>
                    <option value="mm/dd/yy" <?php isset($field['date_format']) ? selected($field['date_format'], 'mm/dd/yy') : ''; ?>>
                        <?php esc_html_e('09/19/2023', 'fancy-forms'); ?>
                    </option>
                    <option value="dd/mm/yy" <?php isset($field['date_format']) ? selected($field['date_format'], 'dd/mm/yy') : ''; ?>>
                        <?php esc_html_e('19/09/2023', 'fancy-forms'); ?>
                    </option>
                </select>
            </div>
            <?php
        }

        if ($field_type === 'textarea') {
            ?>
            <div class="fancyforms-form-row">
                <label><?php esc_html_e('Rows', 'fancy-forms'); ?></label>
                <input type="number" name="field_options[rows_<?php echo absint($field_id); ?>]" value="<?php echo (isset($field['rows']) ? esc_attr($field['rows']) : ''); ?>" data-changerows="<?php echo esc_attr($this->html_id()); ?>"/>
            </div>
            <?php
        }

        if ($field_type === 'separator') {
            ?>
            <div class="fancyforms-form-row">
                <label><?php esc_html_e('Divider Type', 'fancy-forms'); ?></label>
                <select name="field_options[border_style_<?php echo esc_attr($field_id); ?>]" data-changebordertype="field_change_style_<?php echo esc_attr($field_id) ?>">
                    <option value="solid" <?php isset($field['border_style']) ? selected($field['border_style'], 'solid') : ''; ?>>
                        <?php esc_html_e('Solid', 'fancy-forms'); ?>
                    </option>
                    <option value="double" <?php isset($field['border_style']) ? selected($field['border_style'], 'double') : ''; ?>>
                        <?php esc_html_e('Double', 'fancy-forms'); ?>
                    </option>
                    <option value="dotted" <?php isset($field['border_style']) ? selected($field['border_style'], 'dotted') : ''; ?>>
                        <?php esc_html_e('Dotted', 'fancy-forms'); ?>
                    </option>
                    <option value="dashed" <?php isset($field['border_style']) ? selected($field['border_style'], 'dashed') : ''; ?>>
                        <?php esc_html_e('Dashed', 'fancy-forms'); ?>
                    </option>
                    <option value="groove" <?php isset($field['border_style']) ? selected($field['border_style'], 'groove') : ''; ?>>
                        <?php esc_html_e('Groove', 'fancy-forms'); ?>
                    </option>
                    <option value="ridge" <?php isset($field['border_style']) ? selected($field['border_style'], 'ridge') : ''; ?>>
                        <?php esc_html_e('Ridge', 'fancy-forms'); ?>
                    </option>
                </select>
            </div>

            <div class="fancyforms-form-row">
                <label><?php esc_html_e('Divider Height (px)', 'fancy-forms'); ?></label>
                <input type="number" name="field_options[border_width_<?php echo absint($field_id); ?>]" value="<?php echo (isset($field['border_width']) ? esc_attr($field['border_width']) : ''); ?>" data-changeborderwidth="field_change_style_<?php echo absint($field_id) ?>"/>
            </div>
            <?php
        }

        if ($display['required']) {
            ?>
            <div class="fancyforms-form-row">
                <label for="fancyforms-req-field-<?php echo absint($field_id); ?>">
                    <input type="checkbox" class="fancyforms-form-field-required" id="fancyforms-req-field-<?php echo absint($field_id); ?>" name="field_options[required_<?php echo absint($field_id); ?>]" value="1" <?php checked($field['required'], 1); ?> />
                    <?php esc_html_e('Required', 'fancy-forms'); ?>
                </label>
            </div>
            <?php
        }

        if ($display['range']) {
            ?>
            <div class="fancyforms-form-row">
                <label><?php esc_html_e('Number Range', 'fancy-forms'); ?></label>
                <div class="fancyforms-grid-container">
                    <div class="fancyforms-form-row fancyforms-grid-2">
                        <label><?php esc_html_e('From', 'fancy-forms'); ?></label>
                        <input type="number" name="field_options[minnum_<?php echo absint($field_id); ?>]" value="<?php echo esc_attr($field['minnum']); ?>" data-changeme="fancyforms-field-<?php echo esc_attr($field['field_key']); ?>" data-changeatt="min" <?php echo ($field_type === 'range_slider' ? 'data-changemin="field_change_min_' . esc_attr($field['field_key']) . '"' : ''); ?>/>
                    </div>

                    <div class="fancyforms-form-row fancyforms-grid-2">
                        <label><?php esc_html_e('To', 'fancy-forms'); ?></label>
                        <input type="number" name="field_options[maxnum_<?php echo absint($field_id); ?>]" value="<?php echo esc_attr($field['maxnum']); ?>" data-changeme="fancyforms-field-<?php echo esc_attr($field['field_key']); ?>" data-changeatt="max" <?php echo ($field_type === 'range_slider' ? 'data-changemax="field_change_max_' . esc_attr($field['field_key']) . '"' : ''); ?>/>
                    </div>

                    <div class="fancyforms-form-row fancyforms-grid-2">
                        <label><?php esc_html_e('Step', 'fancy-forms'); ?></label>
                        <input type="number" name="field_options[step_<?php echo absint($field_id); ?>]" value="<?php echo esc_attr($field['step']); ?>" data-changeatt="step" data-changeme="fancyforms-field-<?php echo esc_attr($field['field_key']); ?>"/>
                    </div>
                </div>
            </div>
            <?php
        }

        $this->show_primary_options();

        if ($field_type === 'upload') {
            ?>
            <div class="fancyforms-form-row">
                <label><?php esc_html_e('Upload Label', 'fancy-forms'); ?></label>
                <input type="text" name="field_options[upload_label_<?php echo absint($field_id); ?>]" value="<?php echo esc_attr($field['upload_label']); ?>" data-changeme="fancyforms-editor-upload-label-text-<?php echo absint($field_id); ?>"/>
            </div>

            <div class="fancyforms-form-row">
                <label><?php esc_html_e('Extensions', 'fancy-forms'); ?></label>
                <input type="text" name="field_options[extensions_<?php echo absint($field_id); ?>]" value="<?php echo esc_attr($field['extensions']); ?>"/>
                <label class="fancyforms-field-desc"><?php esc_html_e('The allowed extensions are pdf, doc, docx, xls, xlsx, odt, ppt, pptx, pps, ppsx, jpg, jpeg, png, gif, bmp, mp3, mp4, ogg, wav, mp4, m4v, mov, wmv, avi, mpg, ogv, 3gp, txt, zip, rar, 7z, csv', 'fancy-forms'); ?></label>
            </div>

            <div class="fancyforms-form-row">
                <label><?php esc_html_e('Maximum File Size Allowed to Upload', 'fancy-forms'); ?></label>
                <input type="number" name="field_options[max_upload_size_<?php echo absint($field_id); ?>]" value="<?php echo esc_attr($field['max_upload_size']); ?>"/>
            </div>

            <div class="fancyforms-form-row">
                <label>
                    <input type="hidden" name="field_options[multiple_uploads_<?php echo absint($field_id); ?>]" value="off" />
                    <input type="checkbox" name="field_options[multiple_uploads_<?php echo absint($field_id); ?>]" value="on" data-condition="toggle" id="fancyforms-multiple-uploads-<?php echo absint($field_id); ?>" <?php checked($field['multiple_uploads'], 'on'); ?>/>
                    <?php esc_html_e('Multiple Uploads', 'fancy-forms'); ?>
                </label>
            </div>

            <div class="fancyforms-form-row" data-condition-toggle="fancyforms-multiple-uploads-<?php echo absint($field_id); ?>">
                <label>
                    <?php esc_html_e('Multiple Uploads Limit', 'fancy-forms'); ?>
                    <input type="number" name="field_options[multiple_uploads_limit_<?php echo absint($field_id); ?>]" value="<?php echo esc_attr($field['multiple_uploads_limit']); ?>"/>
                </label>
            </div>
            <?php
        }

        if ($display['css']) {
            ?>
            <div class="fancyforms-form-row">
                <label><?php esc_html_e('CSS Classes', 'fancy-forms'); ?></label>
                <input type="text" name="field_options[classes_<?php echo absint($field_id); ?>]" value="<?php echo esc_attr($field['classes']); ?>"/>
            </div>
            <?php
        }

        if ($field_type === 'select' || $field_type === 'radio' || $field_type === 'checkbox' || $field_type === 'image_select') {
            $this->show_field_choices();
        }

        if ($display['auto_width']) {
            ?>
            <div class="fancyforms-form-row">
                <label>
                    <input type="hidden" name="field_options[auto_width_<?php echo absint($field_id); ?>]" value="off" />
                    <input type="checkbox" name="field_options[auto_width_<?php echo absint($field_id); ?>]" value="on" <?php checked($field['auto_width'], 'on'); ?>/>
                    <?php esc_html_e('Automatic Width', 'fancy-forms'); ?>
                </label>
            </div>
            <?php
        }

        if ($display['default']) {
            $field_type_attr_val = 'text';
            if ($field_type == 'range_slider' || $field_type == 'number' || $field_type == 'spinner') {
                $field_type_attr_val = 'number';
            }

            if ($field_type == 'email') {
                $field_type_attr_val = 'email';
            }
            ?>
            <div class="fancyforms-form-row">
                <label><?php esc_html_e('Default Value', 'fancy-forms'); ?></label>
                <input type="<?php echo esc_attr($field_type_attr_val); ?>" name="<?php echo 'default_value_' . absint($field_id); ?>" value="<?php echo esc_attr($field['default_value']); ?>" class="fancyforms-default-value-field" data-changeme="fancyforms-field-<?php echo esc_attr($field['field_key']); ?>" data-changeatt="value"/>
            </div>
            <?php
        }

        $this->show_after_default();

        if ($display['clear_on_focus']) {
            ?>
            <div class="fancyforms-form-row">
                <label><?php esc_html_e('Placeholder', 'fancy-forms'); ?></label>
                <?php
                if ($field_type === 'textarea') {
                    ?>
                    <textarea id="fancyforms-placeholder-<?php echo absint($field_id); ?>" name="field_options[placeholder_<?php echo absint($field_id); ?>]" rows="3" data-changeme="fancyforms-field-<?php echo esc_attr($field['field_key']); ?>" data-changeatt="placeholder"><?php echo esc_textarea($field['placeholder']); ?></textarea>
                    <?php
                } else {
                    ?>
                    <input id="fancyforms-placeholder-<?php echo absint($field_id); ?>" type="text" name="field_options[placeholder_<?php echo absint($field_id); ?>]" value="<?php echo esc_attr($field['placeholder']); ?>" data-changeme="fancyforms-field-<?php echo esc_attr($field['field_key']); ?>" data-changeatt="placeholder" />
                    <?php
                }
                ?>
            </div>
            <?php
        }

        if ($display['description']) {
            ?>
            <div class="fancyforms-form-row">
                <label><?php esc_html_e('Field Description', 'fancy-forms'); ?></label>
                <textarea name="field_options[description_<?php echo absint($field_id); ?>]" data-changeme="fancyforms-field-desc-<?php echo absint($field_id); ?>"><?php echo esc_textarea($field['description']); ?></textarea>
            </div>
            <?php
        }

        if ($display['format']) {
            ?>
            <div class="fancyforms-form-row">
                <label><?php esc_html_e('Format', 'fancy-forms'); ?></label>
                <input type="text" value="<?php echo esc_attr($field['format']); ?>" name="field_options[format_<?php echo absint($field_id); ?>]" data-fid="<?php echo absint($field_id); ?>" />
                <p class="description"><?php esc_html_e('Enter a Regex Format to validate.', 'fancy-forms'); ?> <a href="https://www.phpliveregex.com" target="_blank"><?php esc_html_e('Generate Regex', 'fancy-forms'); ?></a></p>
            </div>
            <?php
        }

        if ($display['required']) {
            ?>
            <div class="fancyforms-form-row fancyforms-grid-3 fancyforms-required-detail-<?php echo esc_attr($field_id) . ($field['required'] ? '' : ' fancyforms-hidden'); ?>">
                <label><?php esc_html_e('Required Field Indicator', 'fancy-forms'); ?></label>
                <input type="text" name="field_options[required_indicator_<?php echo absint($field_id); ?>]" value="<?php echo esc_attr($field['required_indicator']); ?>" data-changeme="fancyforms-editor-field-required-<?php echo absint($field_id); ?>" />
            </div>
            <?php
        }

        if ($field_type === 'radio' || $field_type === 'checkbox' || $field_type === 'image_select') {
            ?>
            <div class="fancyforms-form-row fancyforms-grid-3">
                <label><?php esc_html_e('Options Layout', 'fancy-forms'); ?></label>
                <select name="field_options[options_layout_<?php echo absint($field_id); ?>]">
                    <option value="inline" <?php selected($field['options_layout'], 'inline'); ?>>
                        <?php esc_html_e('Inline', 'fancy-forms'); ?>
                    </option>
                    <option value="1" <?php selected($field['options_layout'], '1'); ?>>
                        <?php esc_html_e('1 Column', 'fancy-forms'); ?>
                    </option>
                    <option value="2" <?php selected($field['options_layout'], '2'); ?>>
                        <?php esc_html_e('2 Columns', 'fancy-forms'); ?>
                    </option>
                    <option value="3" <?php selected($field['options_layout'], '3'); ?>>
                        <?php esc_html_e('3 Columns', 'fancy-forms'); ?>
                    </option>
                    <option value="4" <?php selected($field['options_layout'], '4'); ?>>
                        <?php esc_html_e('4 Columns', 'fancy-forms'); ?>
                    </option>
                    <option value="5" <?php selected($field['options_layout'], '5'); ?>>
                        <?php esc_html_e('5 Columns', 'fancy-forms'); ?>
                    </option>
                    <option value="6" <?php selected($field['options_layout'], '6'); ?>>
                        <?php esc_html_e('6 Columns', 'fancy-forms'); ?>
                    </option>
                </select>
            </div>
            <?php
        }

        if ($display['max']) {
            ?>
            <div class="fancyforms-form-row fancyforms-grid-3">
                <label><?php esc_html_e('Max Characters', 'fancy-forms'); ?></label>
                <input type="number" name="field_options[max_<?php echo esc_attr($field_id); ?>]" value="<?php echo esc_attr($field['max']); ?>" size="5" data-fid="<?php echo absint($field_id); ?>" />
            </div>
            <?php
        }

        if ($display['max_width']) {
            ?>
            <div class="fancyforms-form-row">
                <label><?php esc_html_e('Field Max Width', 'fancy-forms'); ?></label>
                <div class="fancyforms-form-input-unit">
                    <input type="number" name="field_options[field_max_width_<?php echo esc_attr($field_id); ?>]" value="<?php echo (isset($field['field_max_width']) ? esc_attr($field['field_max_width']) : ''); ?>" />

                    <select name="field_options[field_max_width_unit_<?php echo esc_attr($field_id); ?>]">
                        <option value="%" <?php isset($field['field_max_width_unit']) ? selected($field['field_max_width_unit'], '%') : ''; ?>>
                            <?php esc_html_e('%', 'fancy-forms'); ?>
                        </option>
                        <option value="px" <?php isset($field['field_max_width_unit']) ? selected($field['field_max_width_unit'], 'px') : ''; ?>>
                            <?php esc_html_e('px', 'fancy-forms'); ?>
                        </option>
                    </select>
                </div>
            </div>
            <?php
        }

        if ($display['image_max_width']) {
            ?>
            <div class="fancyforms-form-row">
                <label><?php esc_html_e('Image Max Width', 'fancy-forms'); ?></label>
                <div class="fancyforms-form-input-unit">
                    <input type="number" name="field_options[image_max_width_<?php echo esc_attr($field_id); ?>]" value="<?php echo (isset($field['image_max_width']) ? esc_attr($field['image_max_width']) : ''); ?>" />

                    <select name="field_options[image_max_width_unit_<?php echo esc_attr($field_id); ?>]">
                        <option value="%" <?php isset($field['image_max_width_unit']) ? selected($field['image_max_width_unit'], '%') : ''; ?>>
                            <?php esc_html_e('%', 'fancy-forms'); ?>
                        </option>
                        <option value="px" <?php isset($field['image_max_width_unit']) ? selected($field['image_max_width_unit'], 'px') : ''; ?>>
                            <?php esc_html_e('px', 'fancy-forms'); ?>
                        </option>
                    </select>
                </div>
            </div>
            <?php
        }

        if ($display['field_alignment']) {
            $field_alignment = isset($field['field_alignment']) ? esc_attr($field['field_alignment']) : '';
            ?>
            <div class="fancyforms-form-row">
                <label><?php esc_html_e('Field Alignment', 'fancy-forms'); ?></label>
                <select name="field_options[field_alignment_<?php echo esc_attr($field_id); ?>]">
                    <option value="left" <?php selected($field_alignment, 'left'); ?>>
                        <?php esc_html_e('Left', 'fancy-forms'); ?>
                    </option>
                    <option value="right" <?php selected($field_alignment, 'right'); ?>>
                        <?php esc_html_e('Right', 'fancy-forms'); ?>
                    </option>
                    <option value="center" <?php selected($field_alignment, 'center'); ?>>
                        <?php esc_html_e('Center', 'fancy-forms'); ?>
                    </option>
                </select>
                <label class="fancyforms-field-desc"><?php esc_html_e('This option will only work if the Field Max Width is set and width is smaller than container.', 'fancy-forms'); ?></label>
            </div>
            <?php
        }

        $has_validation = ($display['invalid'] || $display['required']);
        $has_invalid = $display['invalid'];

        if ($field_type === 'upload') {
            $has_validation = true;
            $has_invalid = true;
        }

        if ($has_validation) {
            ?>
            <h4 class="fancyforms-validation-header <?php echo ($has_invalid ? 'fancyforms-alway-show' : ($field['required'] ? '' : ' fancyforms-hidden')); ?>"> <?php esc_html_e('Validation Messages', 'fancy-forms'); ?></h4>
            <?php
        }

        if ($display['required']) {
            ?>
            <div class="fancyforms-form-row fancyforms-required-detail-<?php echo esc_attr($field_id) . ($field['required'] ? '' : ' fancyforms-hidden'); ?>">
                <label><?php esc_html_e('Required', 'fancy-forms'); ?></label>
                <input type="text" name="field_options[blank_<?php echo esc_attr($field_id); ?>]" value="<?php echo esc_attr($field['blank']); ?>"/>
            </div>
            <?php
        }

        if ($display['invalid']) {
            ?>
            <div class="fancyforms-form-row">
                <label><?php esc_html_e('Invalid Format', 'fancy-forms'); ?></label>
                <input type="text" name="field_options[invalid_<?php echo esc_attr($field_id); ?>]" value="<?php echo esc_attr($field['invalid']); ?>"/>
            </div>
            <?php
        }


        if ($field_type === 'upload') {
            ?>
            <div class="fancyforms-form-row">
                <label><?php esc_html_e('Extensions', 'fancy-forms'); ?></label>
                <input type="text" name="field_options[extensions_error_message_<?php echo absint($field_id); ?>]" value="<?php echo esc_attr($field['extensions_error_message']); ?>"/>
            </div>

            <div class="fancyforms-form-row" data-condition-toggle="fancyforms-multiple-uploads-<?php echo absint($field_id); ?>">
                <label><?php esc_html_e('Multiple Uploads', 'fancy-forms'); ?></label>
                <input type="text" name="field_options[multiple_uploads_error_message_<?php echo absint($field_id); ?>]" value="<?php echo esc_attr($field['multiple_uploads_error_message']); ?>"/>
            </div>
            <?php
        }
        ?>
    </div>
</div>
