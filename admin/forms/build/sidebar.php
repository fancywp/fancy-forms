<?php
defined('ABSPATH') || die();
?>

<div class="fancyforms-fields-sidebar">
    <div class="fancyforms-fields-container">
        <ul id="fancyforms-fields-tabs" class="fancyforms-fields-tabs">
            <li class="fancyforms-active-tab"><a href="#fancyforms-add-fields-panel" id="fancyforms-add-fields-tab"><?php esc_html_e('Add Fields', 'fancy-forms'); ?></a></li>
            <li><a href="#fancyforms-options-panel" id="fancyforms-options-tab"><?php esc_html_e('Field Options', 'fancy-forms'); ?></a></li>
            <li><a href="#fancyforms-meta-panel" id="fancyforms-design-tab"><?php esc_html_e('Form Title', 'fancy-forms'); ?></a></li>
        </ul>

        <div class="fancyforms-fields-panels">
            <div id="fancyforms-add-fields-panel" class="ht-fields-panel">
                <?php
                FancyFormsHelper::show_search_box(array(
                    'input_id' => 'field-list',
                    'placeholder' => esc_html__('Search Fields', 'fancy-forms'),
                    'tosearch' => 'fancyforms-field-box',
                ));
                ?>
                <ul class="fancyforms-fields-list">
                    <?php
                    $registered_fields = FancyFormsFields::field_selection();
                    foreach ($registered_fields as $field_key => $field_type) {
                        ?>
                        <li class="fancyforms-field-box fancyforms_<?php echo esc_attr($field_key); ?>" id="<?php echo esc_attr($field_key); ?>">
                            <a href="#" class="fancyforms-add-field" title="<?php echo esc_html($field_type['name']); ?>">
                                <i class="<?php echo esc_attr($field_type['icon']); ?>"></i>
                                <span><?php echo esc_html($field_type['name']); ?></span>
                            </a>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
            </div>

            <div id="fancyforms-options-panel" class="ht-fields-panel">
                <div class="fancyforms-fields-settings">
                    <div class="fancyforms-no-field-placeholder">
                        <div class="fancyforms-no-field-msg"><?php esc_html_e('Select a field to see the options', 'fancy-forms'); ?></div>
                    </div>
                </div>

                <form method="post" id="fancyforms-fields-form">
                    <input type="hidden" name="id" id="fancyforms-form-id" value="<?php echo esc_attr($values['id']); ?>" />
                    <?php wp_nonce_field('fancyforms_save_form_nonce', 'fancyforms_save_form'); ?>
                    <input type="hidden" id="fancyforms-end-form-marker" />
                </form>
            </div>

            <div id="fancyforms-meta-panel" class="ht-fields-panel">
                <form method="post" id="fancyforms-meta-form">
                    <div class="fancyforms-form-container fancyforms-grid-container">
                        <div class="fancyforms-form-row">
                            <label><?php esc_html_e('Form Title', 'fancy-forms'); ?></label>
                            <input type="text" name="title" value="<?php echo esc_attr($values['name']); ?>">
                        </div>

                        <div class="fancyforms-form-row">
                            <label>
                                <input type="checkbox" name="show_title" value="on" <?php isset($values['show_title']) ? checked($values['show_title'], 'on') : ''; ?> />
                                <?php esc_html_e('Show the form title', 'fancy-forms'); ?>
                            </label>
                        </div>

                        <div class="fancyforms-form-row">
                            <label><?php esc_html_e('Form Description', 'fancy-forms'); ?></label>
                            <textarea name="description"><?php echo esc_textarea($values['description']); ?></textarea>
                        </div>

                        <div class="fancyforms-form-row">
                            <label>
                                <input type="checkbox" name="show_description" value="on" <?php isset($values['show_description']) ? checked($values['show_description'], 'on') : ''; ?> />
                                <?php esc_html_e('Show the form description', 'fancy-forms'); ?>
                            </label>
                        </div>

                        <div class="fancyforms-form-row">
                            <label><?php esc_html_e('Submit Button Text', 'fancy-forms'); ?></label>
                            <input type="text" name="submit_value" value="<?php echo isset($values['submit_value']) ? esc_attr($values['submit_value']) : ''; ?>" data-changeme="fancyforms-editor-submit-button">
                        </div>

                        <div class="fancyforms-form-row">
                            <label><?php esc_html_e('Form CSS Class', 'fancy-forms'); ?></label>
                            <input type="text" name="form_css_class" value="<?php echo isset($values['form_css_class']) ? esc_attr($values['form_css_class']) : ''; ?>">
                        </div>

                        <div class="fancyforms-form-row">
                            <label><?php esc_html_e('Submit Button CSS Class', 'fancy-forms'); ?></label>
                            <input type="text" name="submit_btn_css_class" value="<?php echo isset($values['submit_btn_css_class']) ? esc_attr($values['submit_btn_css_class']) : ''; ?>">
                        </div>

                        <div class="fancyforms-form-row">
                            <label><?php esc_html_e('Submit Button Alignment', 'fancy-forms'); ?></label>
                            <select name="submit_btn_alignment">
                                <option value="left" <?php isset($values['submit_btn_alignment']) ? selected($values['submit_btn_alignment'], 'left') : ''; ?>>
                                    <?php esc_html_e('Left', 'fancy-forms'); ?>
                                </option>
                                <option value="right" <?php isset($values['submit_btn_alignment']) ? selected($values['submit_btn_alignment'], 'right') : ''; ?>>
                                    <?php esc_html_e('Right', 'fancy-forms'); ?>
                                </option>
                                <option value="center" <?php isset($values['submit_btn_alignment']) ? selected($values['submit_btn_alignment'], 'center') : ''; ?>>
                                    <?php esc_html_e('Center', 'fancy-forms'); ?>
                                </option>
                            </select>
                        </div>
                    </div>
                </form>

                <div class="fancyforms-hidden">
                    <?php wp_editor('', 'fancyforms-init-tinymce'); ?>
                </div>
            </div>
        </div>
    </div>
</div>
