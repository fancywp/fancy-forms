<?php
defined('ABSPATH') || die();
$id = get_the_ID();
?>

<div class="fancyforms-settings-row fancyforms-form-row">
    <label class="fancyforms-setting-label"><?php esc_html_e('Choose Form to Preview', 'fancy-forms'); ?></label>
    <select id="fancyforms-template-preview-form-id">
        <?php
        $forms = FancyFormsBuilder::get_all_forms();
        ?>
        <option value=""><?php esc_html_e('Default Demo Form', 'fancy-forms'); ?></option>
        <?php
        foreach ($forms as $form) {
            ?>
            <option value="<?php echo esc_attr($form->id); ?>"><?php echo esc_html($form->name); ?></option>
        <?php } ?>
    </select>
</div>

<h2 class="fancyforms-settings-heading"><?php esc_html_e('Form', 'fancy-forms'); ?><span class="mdi mdi-triangle-small-down"></span></h2>

<div class="fancyforms-form-settings">
    <div class="fancyforms-settings-row">
        <label class="fancyforms-setting-label"><?php esc_html_e('Column Gap', 'fancy-forms'); ?></label>
        <div class="fancyforms-setting-fields fancyforms-range-slider-wrap">
            <div class="fancyforms-range-slider"></div>
            <input data-unit="px" id="fancyforms-form-column-gap" class="fancyforms-range-input-selector" type="number" name="fancyforms_styles[form][column_gap]" value="<?php echo is_numeric($fancyforms_styles['form']['column_gap']) ? intval($fancyforms_styles['form']['column_gap']) : ''; ?>" min="0" max="100" step="1"> px
        </div>
    </div>

    <div class="fancyforms-settings-row">
        <label class="fancyforms-setting-label"><?php esc_html_e('Row Gap', 'fancy-forms'); ?></label>
        <div class="fancyforms-setting-fields fancyforms-range-slider-wrap">
            <div class="fancyforms-range-slider"></div>
            <input data-unit="px" id="fancyforms-form-row-gap" class="fancyforms-range-input-selector" type="number" name="fancyforms_styles[form][row_gap]" value="<?php echo is_numeric($fancyforms_styles['form']['row_gap']) ? intval($fancyforms_styles['form']['row_gap']) : ''; ?>" min="0" max="100" step="1"> px
        </div>
    </div>

    <div class="fancyforms-settings-row">
        <label class="fancyforms-setting-label fancyforms-color-input-label"><?php esc_html_e('Background Color', 'fancy-forms'); ?></label>
        <div class="fancyforms-setting-fields fancyforms-color-input-field">
            <input id="fancyforms-form-bg-color" type="text" class="color-picker fancyforms-color-picker" data-alpha-enabled="true" data-alpha-custom-width="30px" data-alpha-color-type="hex" name="fancyforms_styles[form][bg_color]" value="<?php echo esc_attr($fancyforms_styles['form']['bg_color']); ?>">
        </div>
    </div>

    <div class="fancyforms-settings-row">
        <label class="fancyforms-setting-label"><?php esc_html_e('Box Shadow', 'fancy-forms') ?></label>
        <div class="fancyforms-setting-fields">
            <ul class="fancyforms-shadow-fields">
                <li class="fancyforms-shadow-settings-field">
                    <input id="fancyforms-form-shadow-x" data-unit="px" type="number" name="fancyforms_styles[form][shadow][x]" value="<?php echo esc_attr($fancyforms_styles['form']['shadow']['x']); ?>">
                    <label><?php esc_html_e('H', 'fancy-forms') ?></label>
                </li>
                <li class="fancyforms-shadow-settings-field">
                    <input id="fancyforms-form-shadow-y" data-unit="px" type="number" name="fancyforms_styles[form][shadow][y]" value="<?php echo esc_attr($fancyforms_styles['form']['shadow']['y']); ?>">
                    <label><?php esc_html_e('V', 'fancy-forms') ?></label>
                </li>
                <li class="fancyforms-shadow-settings-field">
                    <input id="fancyforms-form-shadow-blur" data-unit="px" type="number" name="fancyforms_styles[form][shadow][blur]" value="<?php echo esc_attr($fancyforms_styles['form']['shadow']['blur']); ?>">
                    <label><?php esc_html_e('Blur', 'fancy-forms') ?></label>
                </li>
                <li class="fancyforms-shadow-settings-field">
                    <input id="fancyforms-form-shadow-spread" data-unit="px" type="number" name="fancyforms_styles[form][shadow][spread]" value="<?php echo esc_attr($fancyforms_styles['form']['shadow']['spread']); ?>">
                    <label><?php esc_html_e('Spread', 'fancy-forms') ?></label>
                </li>
            </ul>
            <div class="fancyforms-shadow-settings-field">
                <div class="fancyforms-color-input-field">
                    <input id="fancyforms-form-shadow-color" type="text" class="color-picker fancyforms-color-picker" data-alpha-enabled="true" data-alpha-custom-width="30px" data-alpha-color-type="hex" name="fancyforms_styles[form][shadow][color]" value="<?php echo esc_attr($fancyforms_styles['form']['shadow']['color']); ?>">
                </div>
            </div>
        </div>
    </div>

    <div class="fancyforms-settings-row">
        <label class="fancyforms-setting-label fancyforms-color-input-label"><?php esc_html_e('Border Color', 'fancy-forms'); ?></label>
        <div class="fancyforms-setting-fields fancyforms-color-input-field">
            <input id="fancyforms-form-border-color" type="text" class="color-picker fancyforms-color-picker" data-alpha-enabled="true" data-alpha-custom-width="30px" data-alpha-color-type="hex" name="fancyforms_styles[form][border_color]" value="<?php echo esc_attr($fancyforms_styles['form']['border_color']); ?>">
        </div>
    </div>

    <div class="fancyforms-settings-row">
        <label class="fancyforms-setting-label"><?php esc_html_e('Border Width', 'fancy-forms') ?></label>
        <ul class="fancyforms-unit-fields">
            <li class="fancyforms-unit-settings-field">
                <input id="fancyforms-form-border-top" data-unit="px" type="number" name="fancyforms_styles[form][border][top]" value="<?php echo esc_attr($fancyforms_styles['form']['border']['top']); ?>" min="0">
                <label><?php esc_html_e('Top', 'fancy-forms') ?></label>
            </li>
            <li class="fancyforms-unit-settings-field">
                <input id="fancyforms-form-border-right" data-unit="px" type="number" name="fancyforms_styles[form][border][right]" value="<?php echo esc_attr($fancyforms_styles['form']['border']['right']); ?>" min="0">
                <label><?php esc_html_e('Right', 'fancy-forms') ?></label>
            </li>
            <li class="fancyforms-unit-settings-field">
                <input id="fancyforms-form-border-bottom" data-unit="px" type="number" name="fancyforms_styles[form][border][bottom]" value="<?php echo esc_attr($fancyforms_styles['form']['border']['bottom']); ?>" min="0">
                <label><?php esc_html_e('Bottom', 'fancy-forms') ?></label>
            </li>
            <li class="fancyforms-unit-settings-field">
                <input id="fancyforms-form-border-left" data-unit="px" type="number" name="fancyforms_styles[form][border][left]" value="<?php echo esc_attr($fancyforms_styles['form']['border']['left']); ?>" min="0">
                <label><?php esc_html_e('Left', 'fancy-forms') ?></label>
            </li>
            <li class="fancyforms-unit-settings-field">
                <div class="fancyforms-link-button">
                    <span class="dashicons dashicons-admin-links fancyforms-linked"></span>
                    <span class="dashicons dashicons-editor-unlink fancyforms-unlinked"></span>
                </div>
            </li>
        </ul>
    </div>

    <div class="fancyforms-settings-row">
        <label class="fancyforms-setting-label"><?php esc_html_e('Border Radius', 'fancy-forms') ?></label>
        <ul class="fancyforms-unit-fields">
            <li class="fancyforms-unit-settings-field">
                <input id="fancyforms-form-border-radius-top" data-unit="px" type="number" name="fancyforms_styles[form][border_radius][top]" value="<?php echo esc_attr($fancyforms_styles['form']['border_radius']['top']); ?>" min="0">
                <label><?php esc_html_e('Top', 'fancy-forms') ?></label>
            </li>
            <li class="fancyforms-unit-settings-field">
                <input id="fancyforms-form-border-radius-right" data-unit="px" type="number" name="fancyforms_styles[form][border_radius][right]" value="<?php echo esc_attr($fancyforms_styles['form']['border_radius']['right']); ?>" min="0">
                <label><?php esc_html_e('Right', 'fancy-forms') ?></label>
            </li>
            <li class="fancyforms-unit-settings-field">
                <input id="fancyforms-form-border-radius-bottom" data-unit="px" type="number" name="fancyforms_styles[form][border_radius][bottom]" value="<?php echo esc_attr($fancyforms_styles['form']['border_radius']['bottom']); ?>" min="0">
                <label><?php esc_html_e('Bottom', 'fancy-forms') ?></label>
            </li>
            <li class="fancyforms-unit-settings-field">
                <input id="fancyforms-form-border-radius-left" data-unit="px" type="number" name="fancyforms_styles[form][border_radius][left]" value="<?php echo esc_attr($fancyforms_styles['form']['border_radius']['left']); ?>" min="0">
                <label><?php esc_html_e('Left', 'fancy-forms') ?></label>
            </li>
            <li class="fancyforms-unit-settings-field">
                <div class="fancyforms-link-button">
                    <span class="dashicons dashicons-admin-links fancyforms-linked"></span>
                    <span class="dashicons dashicons-editor-unlink fancyforms-unlinked"></span>
                </div>
            </li>
        </ul>
    </div>

    <div class="fancyforms-settings-row">
        <label class="fancyforms-setting-label"><?php esc_html_e('Padding', 'fancy-forms') ?></label>
        <ul class="fancyforms-unit-fields">
            <li class="fancyforms-unit-settings-field">
                <input id="fancyforms-form-padding-top" data-unit="px" type="number" name="fancyforms_styles[form][padding][top]" value="<?php echo esc_attr($fancyforms_styles['form']['padding']['top']); ?>" min="0">
                <label><?php esc_html_e('Top', 'fancy-forms') ?></label>
            </li>
            <li class="fancyforms-unit-settings-field">
                <input id="fancyforms-form-padding-right" data-unit="px" type="number" name="fancyforms_styles[form][padding][right]" value="<?php echo esc_attr($fancyforms_styles['form']['padding']['right']); ?>" min="0">
                <label><?php esc_html_e('Right', 'fancy-forms') ?></label>
            </li>
            <li class="fancyforms-unit-settings-field">
                <input id="fancyforms-form-padding-bottom" data-unit="px" type="number" name="fancyforms_styles[form][padding][bottom]" value="<?php echo esc_attr($fancyforms_styles['form']['padding']['bottom']); ?>" min="0">
                <label><?php esc_html_e('Bottom', 'fancy-forms') ?></label>
            </li>
            <li class="fancyforms-unit-settings-field">
                <input id="fancyforms-form-padding-left" data-unit="px" type="number" name="fancyforms_styles[form][padding][left]" value="<?php echo esc_attr($fancyforms_styles['form']['padding']['left']); ?>" min="0">
                <label><?php esc_html_e('Left', 'fancy-forms') ?></label>
            </li>
            <li class="fancyforms-unit-settings-field">
                <div class="fancyforms-link-button">
                    <span class="dashicons dashicons-admin-links fancyforms-linked"></span>
                    <span class="dashicons dashicons-editor-unlink fancyforms-unlinked"></span>
                </div>
            </li>
        </ul>
    </div>
</div>

<h2 class="fancyforms-settings-heading"><?php esc_html_e('Labels', 'fancy-forms'); ?><span class="mdi mdi-triangle-small-down"></span></h2>

<div class="fancyforms-form-settings">
    <div class="fancyforms-settings-row">
        <label class="fancyforms-setting-label"><?php esc_html_e('Typography', 'fancy-forms'); ?></label>
        <?php self::get_typography_fields('fancyforms_styles', $fancyforms_styles, 'label'); ?>
    </div>

    <div class="fancyforms-settings-row">
        <label class="fancyforms-setting-label"><?php esc_html_e('Bottom Spacing', 'fancy-forms'); ?></label>
        <div class="fancyforms-setting-fields fancyforms-range-slider-wrap">
            <div class="fancyforms-range-slider"></div>
            <input data-unit="px" id="fancyforms-label-spacing" class="fancyforms-range-input-selector" type="number" name="fancyforms_styles[label][spacing]" value="<?php echo is_numeric($fancyforms_styles['label']['spacing']) ? intval($fancyforms_styles['label']['spacing']) : ''; ?>" min="0" max="100" step="1"> px
        </div>
    </div>
    <div class="fancyforms-settings-row">
        <label class="fancyforms-setting-label fancyforms-color-input-label"><?php esc_html_e('Required Text Color', 'fancy-forms'); ?></label>
        <div class="fancyforms-setting-fields fancyforms-color-input-field">
            <input id="fancyforms-label-required-color" type="text" class="color-picker fancyforms-color-picker" data-alpha-enabled="true" data-alpha-custom-width="30px" data-alpha-color-type="hex" name="fancyforms_styles[label][required_color]" value="<?php echo esc_attr($fancyforms_styles['label']['required_color']); ?>">
        </div>
    </div>
</div>


<h2 class="fancyforms-settings-heading"><?php esc_html_e('Description', 'fancy-forms'); ?><span class="mdi mdi-triangle-small-down"></span></h2>
<div class="fancyforms-form-settings">
    <div class="fancyforms-settings-row">
        <label class="fancyforms-setting-label"><?php esc_html_e('Typography', 'fancy-forms'); ?></label>
        <?php self::get_typography_fields('fancyforms_styles', $fancyforms_styles, 'desc'); ?>
    </div>

    <div class="fancyforms-settings-row">
        <label class="fancyforms-setting-label"><?php esc_html_e('Top Spacing', 'fancy-forms'); ?></label>
        <div class="fancyforms-setting-fields fancyforms-range-slider-wrap">
            <div class="fancyforms-range-slider"></div>
            <input data-unit="px" id="fancyforms-desc-spacing" class="fancyforms-range-input-selector" type="number" name="fancyforms_styles[desc][spacing]" value="<?php echo is_numeric($fancyforms_styles['desc']['spacing']) ? intval($fancyforms_styles['desc']['spacing']) : ''; ?>" min="0" max="100" step="1"> px
        </div>
    </div>
</div>


<h2 class="fancyforms-settings-heading"><?php esc_html_e('Fields', 'fancy-forms'); ?><span class="mdi mdi-triangle-small-down"></span></h2>

<div class="fancyforms-form-settings">
    <div class="fancyforms-settings-row">
        <label class="fancyforms-setting-label"><?php esc_html_e('Typography', 'fancy-forms'); ?></label>
        <?php self::get_typography_fields('fancyforms_styles', $fancyforms_styles, 'field', array('color')); ?>
    </div>

    <div class="fancyforms-tab-container">
        <ul class="fancyforms-setting-tab">
            <li data-tab="fancyforms-tab-normal" class="fancyforms-tab-active"><?php esc_html_e('Normal', 'fancy-forms'); ?></li>
            <li data-tab="fancyforms-tab-focus"><?php esc_html_e('Focus', 'fancy-forms'); ?></li>
        </ul>

        <div class="fancyforms-setting-tab-panel">
            <div class="fancyforms-tab-normal fancyforms-tab-content">
                <div class="fancyforms-settings-row">
                    <label class="fancyforms-setting-label fancyforms-color-input-label"><?php esc_html_e('Color', 'fancy-forms'); ?></label>
                    <div class="fancyforms-setting-fields fancyforms-color-input-field">
                        <input id="fancyforms-field-color-normal" type="text" class="color-picker fancyforms-color-picker" data-alpha-enabled="true" data-alpha-custom-width="30px" data-alpha-color-type="hex" name="fancyforms_styles[field][color_normal]" value="<?php echo esc_attr($fancyforms_styles['field']['color_normal']); ?>">
                    </div>
                </div>

                <div class="fancyforms-settings-row">
                    <label class="fancyforms-setting-label fancyforms-color-input-label"><?php esc_html_e('Background Color', 'fancy-forms'); ?></label>
                    <div class="fancyforms-setting-fields fancyforms-color-input-field">
                        <input id="fancyforms-field-bg-color-normal" type="text" class="color-picker fancyforms-color-picker" data-alpha-enabled="true" data-alpha-custom-width="30px" data-alpha-color-type="hex" name="fancyforms_styles[field][bg_color_normal]" value="<?php echo esc_attr($fancyforms_styles['field']['bg_color_normal']); ?>">
                    </div>
                </div>

                <div class="fancyforms-settings-row">
                    <label class="fancyforms-setting-label"><?php esc_html_e('Box Shadow', 'fancy-forms') ?></label>
                    <div class="fancyforms-setting-fields">
                        <ul class="fancyforms-shadow-fields">
                            <li class="fancyforms-shadow-settings-field">
                                <input id="fancyforms-field-shadow-normal-x" data-unit="px" type="number" name="fancyforms_styles[field][shadow_normal][x]" value="<?php echo esc_attr($fancyforms_styles['field']['shadow_normal']['x']); ?>">
                                <label><?php esc_html_e('H', 'fancy-forms') ?></label>
                            </li>
                            <li class="fancyforms-shadow-settings-field">
                                <input id="fancyforms-field-shadow-normal-y" data-unit="px" type="number" name="fancyforms_styles[field][shadow_normal][y]" value="<?php echo esc_attr($fancyforms_styles['field']['shadow_normal']['y']); ?>">
                                <label><?php esc_html_e('V', 'fancy-forms') ?></label>
                            </li>
                            <li class="fancyforms-shadow-settings-field">
                                <input id="fancyforms-field-shadow-normal-blur" data-unit="px" type="number" name="fancyforms_styles[field][shadow_normal][blur]" value="<?php echo esc_attr($fancyforms_styles['field']['shadow_normal']['blur']); ?>">
                                <label><?php esc_html_e('Blur', 'fancy-forms') ?></label>
                            </li>
                            <li class="fancyforms-shadow-settings-field">
                                <input id="fancyforms-field-shadow-normal-spread" data-unit="px" type="number" name="fancyforms_styles[field][shadow_normal][spread]" value="<?php echo esc_attr($fancyforms_styles['field']['shadow_normal']['spread']); ?>">
                                <label><?php esc_html_e('Spread', 'fancy-forms') ?></label>
                            </li>
                        </ul>
                        <div class="fancyforms-shadow-settings-field">
                            <div class="fancyforms-color-input-field">
                                <input id="fancyforms-field-shadow-normal-color" type="text" class="color-picker fancyforms-color-picker" data-alpha-enabled="true" data-alpha-custom-width="30px" data-alpha-color-type="hex" name="fancyforms_styles[field][shadow_normal][color]" value="<?php echo esc_attr($fancyforms_styles['field']['shadow_normal']['color']); ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="fancyforms-settings-row">
                    <label class="fancyforms-setting-label fancyforms-color-input-label"><?php esc_html_e('Border Color', 'fancy-forms'); ?></label>
                    <div class="fancyforms-setting-fields fancyforms-color-input-field">
                        <input id="fancyforms-field-border-color-normal" type="text" class="color-picker fancyforms-color-picker" data-alpha-enabled="true" data-alpha-custom-width="30px" data-alpha-color-type="hex" name="fancyforms_styles[field][border_color_normal]" value="<?php echo esc_attr($fancyforms_styles['field']['border_color_normal']); ?>">
                    </div>
                </div>
            </div>

            <div class="fancyforms-tab-focus fancyforms-tab-content" style="display: none;">
                <div class="fancyforms-settings-row">
                    <label class="fancyforms-setting-label fancyforms-color-input-label"><?php esc_html_e('Color (Focus)', 'fancy-forms'); ?></label>
                    <div class="fancyforms-setting-fields fancyforms-color-input-field">
                        <input id="fancyforms-field-color-focus" type="text" class="color-picker fancyforms-color-picker" data-alpha-enabled="true" data-alpha-custom-width="30px" data-alpha-color-type="hex" name="fancyforms_styles[field][color_focus]" value="<?php echo esc_attr($fancyforms_styles['field']['color_focus']); ?>">
                    </div>
                </div>

                <div class="fancyforms-settings-row">
                    <label class="fancyforms-setting-label fancyforms-color-input-label"><?php esc_html_e('Background Color (Focus)', 'fancy-forms'); ?></label>
                    <div class="fancyforms-setting-fields fancyforms-color-input-field">
                        <input id="fancyforms-field-bg-color-focus" type="text" class="color-picker fancyforms-color-picker" data-alpha-enabled="true" data-alpha-custom-width="30px" data-alpha-color-type="hex" name="fancyforms_styles[field][bg_color_focus]" value="<?php echo esc_attr($fancyforms_styles['field']['bg_color_focus']); ?>">
                    </div>
                </div>

                <div class="fancyforms-settings-row">
                    <label class="fancyforms-setting-label"><?php esc_html_e('Box Shadow (Focus)', 'fancy-forms') ?></label>
                    <div class="fancyforms-setting-fields">
                        <ul class="fancyforms-shadow-fields">
                            <li class="fancyforms-shadow-settings-field">
                                <input id="fancyforms-field-shadow-focus-x" data-unit="px" type="number" name="fancyforms_styles[field][shadow_focus][x]" value="<?php echo esc_attr($fancyforms_styles['field']['shadow_focus']['x']); ?>">
                                <label><?php esc_html_e('H', 'fancy-forms') ?></label>
                            </li>
                            <li class="fancyforms-shadow-settings-field">
                                <input id="fancyforms-field-shadow-focus-y" data-unit="px" type="number" name="fancyforms_styles[field][shadow_focus][y]" value="<?php echo esc_attr($fancyforms_styles['field']['shadow_focus']['y']); ?>">
                                <label><?php esc_html_e('V', 'fancy-forms') ?></label>
                            </li>
                            <li class="fancyforms-shadow-settings-field">
                                <input id="fancyforms-field-shadow-focus-blur" data-unit="px" type="number" name="fancyforms_styles[field][shadow_focus][blur]" value="<?php echo esc_attr($fancyforms_styles['field']['shadow_focus']['blur']); ?>">
                                <label><?php esc_html_e('Blur', 'fancy-forms') ?></label>
                            </li>
                            <li class="fancyforms-shadow-settings-field">
                                <input id="fancyforms-field-shadow-focus-spread" data-unit="px" type="number" name="fancyforms_styles[field][shadow_focus][spread]" value="<?php echo esc_attr($fancyforms_styles['field']['shadow_focus']['spread']); ?>">
                                <label><?php esc_html_e('Spread', 'fancy-forms') ?></label>
                            </li>
                        </ul>

                        <div class="fancyforms-shadow-settings-field">
                            <div class="fancyforms-color-input-field">
                                <input id="fancyforms-field-shadow-focus-color" type="text" class="color-picker fancyforms-color-picker" data-alpha-enabled="true" data-alpha-custom-width="30px" data-alpha-color-type="hex" name="fancyforms_styles[field][shadow_focus][color]" value="<?php echo esc_attr($fancyforms_styles['field']['shadow_focus']['color']); ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="fancyforms-settings-row">
                    <label class="fancyforms-setting-label fancyforms-color-input-label"><?php esc_html_e('Border Color (Focus)', 'fancy-forms'); ?></label>
                    <div class="fancyforms-setting-fields fancyforms-color-input-field">
                        <input id="fancyforms-field-border-color-focus" type="text" class="color-picker fancyforms-color-picker" data-alpha-enabled="true" data-alpha-custom-width="30px" data-alpha-color-type="hex" name="fancyforms_styles[field][border_color_focus]" value="<?php echo esc_attr($fancyforms_styles['field']['border_color_focus']); ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="fancyforms-settings-row">
        <label class="fancyforms-setting-label"><?php esc_html_e('Border Width', 'fancy-forms') ?></label>
        <ul class="fancyforms-unit-fields">
            <li class="fancyforms-unit-settings-field">
                <input id="fancyforms-field-border-top" data-unit="px" type="number" name="fancyforms_styles[field][border][top]" value="<?php echo esc_attr($fancyforms_styles['field']['border']['top']); ?>" min="0">
                <label><?php esc_html_e('Top', 'fancy-forms') ?></label>
            </li>
            <li class="fancyforms-unit-settings-field">
                <input id="fancyforms-field-border-right" data-unit="px" type="number" name="fancyforms_styles[field][border][right]" value="<?php echo esc_attr($fancyforms_styles['field']['border']['right']); ?>" min="0">
                <label><?php esc_html_e('Right', 'fancy-forms') ?></label>
            </li>
            <li class="fancyforms-unit-settings-field">
                <input id="fancyforms-field-border-bottom" data-unit="px" type="number" name="fancyforms_styles[field][border][bottom]" value="<?php echo esc_attr($fancyforms_styles['field']['border']['bottom']); ?>" min="0">
                <label><?php esc_html_e('Bottom', 'fancy-forms') ?></label>
            </li>
            <li class="fancyforms-unit-settings-field">
                <input id="fancyforms-field-border-left" data-unit="px" type="number" name="fancyforms_styles[field][border][left]" value="<?php echo esc_attr($fancyforms_styles['field']['border']['left']); ?>" min="0">
                <label><?php esc_html_e('Left', 'fancy-forms') ?></label>
            </li>
            <li class="fancyforms-unit-settings-field">
                <div class="fancyforms-link-button">
                    <span class="dashicons dashicons-admin-links fancyforms-linked"></span>
                    <span class="dashicons dashicons-editor-unlink fancyforms-unlinked"></span>
                </div>
            </li>
        </ul>
    </div>

    <div class="fancyforms-settings-row">
        <label class="fancyforms-setting-label"><?php esc_html_e('Border Radius', 'fancy-forms') ?></label>
        <ul class="fancyforms-unit-fields">
            <li class="fancyforms-unit-settings-field">
                <input id="fancyforms-field-border-radius-top" data-unit="px" type="number" name="fancyforms_styles[field][border_radius][top]" value="<?php echo esc_attr($fancyforms_styles['field']['border_radius']['top']); ?>" min="0">
                <label><?php esc_html_e('Top', 'fancy-forms') ?></label>
            </li>
            <li class="fancyforms-unit-settings-field">
                <input id="fancyforms-field-border-radius-right" data-unit="px" type="number" name="fancyforms_styles[field][border_radius][right]" value="<?php echo esc_attr($fancyforms_styles['field']['border_radius']['right']); ?>" min="0">
                <label><?php esc_html_e('Right', 'fancy-forms') ?></label>
            </li>
            <li class="fancyforms-unit-settings-field">
                <input id="fancyforms-field-border-radius-bottom" data-unit="px" type="number" name="fancyforms_styles[field][border_radius][bottom]" value="<?php echo esc_attr($fancyforms_styles['field']['border_radius']['bottom']); ?>" min="0">
                <label><?php esc_html_e('Bottom', 'fancy-forms') ?></label>
            </li>
            <li class="fancyforms-unit-settings-field">
                <input id="fancyforms-field-border-radius-left" data-unit="px" type="number" name="fancyforms_styles[field][border_radius][left]" value="<?php echo esc_attr($fancyforms_styles['field']['border_radius']['left']); ?>" min="0">
                <label><?php esc_html_e('Left', 'fancy-forms') ?></label>
            </li>
            <li class="fancyforms-unit-settings-field">
                <div class="fancyforms-link-button">
                    <span class="dashicons dashicons-admin-links fancyforms-linked"></span>
                    <span class="dashicons dashicons-editor-unlink fancyforms-unlinked"></span>
                </div>
            </li>
        </ul>
    </div>

    <div class="fancyforms-settings-row">
        <label class="fancyforms-setting-label"><?php esc_html_e('Padding', 'fancy-forms') ?></label>
        <ul class="fancyforms-unit-fields">
            <li class="fancyforms-unit-settings-field">
                <input id="fancyforms-field-padding-top" data-unit="px" type="number" name="fancyforms_styles[field][padding][top]" value="<?php echo esc_attr($fancyforms_styles['field']['padding']['top']); ?>" min="0">
                <label><?php esc_html_e('Top', 'fancy-forms') ?></label>
            </li>
            <li class="fancyforms-unit-settings-field">
                <input id="fancyforms-field-padding-right" data-unit="px" type="number" name="fancyforms_styles[field][padding][right]" value="<?php echo esc_attr($fancyforms_styles['field']['padding']['right']); ?>" min="0">
                <label><?php esc_html_e('Right', 'fancy-forms') ?></label>
            </li>
            <li class="fancyforms-unit-settings-field">
                <input id="fancyforms-field-padding-bottom" data-unit="px" type="number" name="fancyforms_styles[field][padding][bottom]" value="<?php echo esc_attr($fancyforms_styles['field']['padding']['bottom']); ?>" min="0">
                <label><?php esc_html_e('Bottom', 'fancy-forms') ?></label>
            </li>
            <li class="fancyforms-unit-settings-field">
                <input id="fancyforms-field-padding-left" data-unit="px" type="number" name="fancyforms_styles[field][padding][left]" value="<?php echo esc_attr($fancyforms_styles['field']['padding']['left']); ?>" min="0">
                <label><?php esc_html_e('Left', 'fancy-forms') ?></label>
            </li>
            <li class="fancyforms-unit-settings-field">
                <div class="fancyforms-link-button">
                    <span class="dashicons dashicons-admin-links fancyforms-linked"></span>
                    <span class="dashicons dashicons-editor-unlink fancyforms-unlinked"></span>
                </div>
            </li>
        </ul>
    </div>
</div>

<h2 class="fancyforms-settings-heading"><?php esc_html_e('Upload Button', 'fancy-forms'); ?><span class="mdi mdi-triangle-small-down"></span></h2>
<div class="fancyforms-form-settings">
    <div class="fancyforms-tab-container">
        <ul class="fancyforms-setting-tab">
            <li data-tab="fancyforms-tab-normal" class="fancyforms-tab-active"><?php esc_html_e('Normal', 'fancy-forms'); ?></li>
            <li data-tab="fancyforms-tab-hover"><?php esc_html_e('Hover', 'fancy-forms'); ?></li>
        </ul>

        <div class="fancyforms-setting-tab-panel">
            <div class="fancyforms-tab-normal fancyforms-tab-content">
                <div class="fancyforms-settings-row">
                    <label class="fancyforms-setting-label fancyforms-color-input-label"><?php esc_html_e('Color', 'fancy-forms'); ?></label>
                    <div class="fancyforms-setting-fields fancyforms-color-input-field">
                        <input id="fancyforms-upload-color-normal" type="text" class="color-picker fancyforms-color-picker" data-alpha-enabled="true" data-alpha-custom-width="30px" data-alpha-color-type="hex" name="fancyforms_styles[upload][color_normal]" value="<?php echo esc_attr($fancyforms_styles['upload']['color_normal']); ?>">
                    </div>
                </div>

                <div class="fancyforms-settings-row">
                    <label class="fancyforms-setting-label fancyforms-color-input-label"><?php esc_html_e('Background Color', 'fancy-forms'); ?></label>
                    <div class="fancyforms-setting-fields fancyforms-color-input-field">
                        <input id="fancyforms-upload-bg-color-normal" type="text" class="color-picker fancyforms-color-picker" data-alpha-enabled="true" data-alpha-custom-width="30px" data-alpha-color-type="hex" name="fancyforms_styles[upload][bg_color_normal]" value="<?php echo esc_attr($fancyforms_styles['upload']['bg_color_normal']); ?>">
                    </div>
                </div>

                <div class="fancyforms-settings-row">
                    <label class="fancyforms-setting-label"><?php esc_html_e('Box Shadow', 'fancy-forms') ?></label>
                    <div class="fancyforms-setting-fields">
                        <ul class="fancyforms-shadow-fields">
                            <li class="fancyforms-shadow-settings-field">
                                <input id="fancyforms-upload-shadow-normal-x" data-unit="px" type="number" name="fancyforms_styles[upload][shadow_normal][x]" value="<?php echo esc_attr($fancyforms_styles['upload']['shadow_normal']['x']); ?>">
                                <label><?php esc_html_e('H', 'fancy-forms') ?></label>
                            </li>
                            <li class="fancyforms-shadow-settings-field">
                                <input id="fancyforms-upload-shadow-normal-y" data-unit="px" type="number" name="fancyforms_styles[upload][shadow_normal][y]" value="<?php echo esc_attr($fancyforms_styles['upload']['shadow_normal']['y']); ?>">
                                <label><?php esc_html_e('V', 'fancy-forms') ?></label>
                            </li>
                            <li class="fancyforms-shadow-settings-field">
                                <input id="fancyforms-upload-shadow-normal-blur" data-unit="px" type="number" name="fancyforms_styles[upload][shadow_normal][blur]" value="<?php echo esc_attr($fancyforms_styles['upload']['shadow_normal']['blur']); ?>">
                                <label><?php esc_html_e('Blur', 'fancy-forms') ?></label>
                            </li>
                            <li class="fancyforms-shadow-settings-field">
                                <input id="fancyforms-upload-shadow-normal-spread" data-unit="px" type="number" name="fancyforms_styles[upload][shadow_normal][spread]" value="<?php echo esc_attr($fancyforms_styles['upload']['shadow_normal']['spread']); ?>">
                                <label><?php esc_html_e('Spread', 'fancy-forms') ?></label>
                            </li>
                        </ul>
                        <div class="fancyforms-shadow-settings-field">
                            <div class="fancyforms-color-input-field">
                                <input id="fancyforms-upload-shadow-normal-color" type="text" class="color-picker fancyforms-color-picker" data-alpha-enabled="true" data-alpha-custom-width="30px" data-alpha-color-type="hex" name="fancyforms_styles[upload][shadow_normal][color]" value="<?php echo esc_attr($fancyforms_styles['upload']['shadow_normal']['color']); ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="fancyforms-settings-row">
                    <label class="fancyforms-setting-label fancyforms-color-input-label"><?php esc_html_e('Border Color', 'fancy-forms'); ?></label>
                    <div class="fancyforms-setting-fields fancyforms-color-input-field">
                        <input id="fancyforms-upload-border-color-normal" type="text" class="color-picker fancyforms-color-picker" data-alpha-enabled="true" data-alpha-custom-width="30px" data-alpha-color-type="hex" name="fancyforms_styles[upload][border_color_normal]" value="<?php echo esc_attr($fancyforms_styles['upload']['border_color_normal']); ?>">
                    </div>
                </div>
            </div>

            <div class="fancyforms-tab-hover fancyforms-tab-content" style="display: none;">
                <div class="fancyforms-settings-row">
                    <label class="fancyforms-setting-label fancyforms-color-input-label"><?php esc_html_e('Color (Hover)', 'fancy-forms'); ?></label>
                    <div class="fancyforms-setting-fields fancyforms-color-input-field">
                        <input id="fancyforms-upload-color-hover" type="text" class="color-picker fancyforms-color-picker" data-alpha-enabled="true" data-alpha-custom-width="30px" data-alpha-color-type="hex" name="fancyforms_styles[upload][color_hover]" value="<?php echo esc_attr($fancyforms_styles['upload']['color_hover']); ?>">
                    </div>
                </div>

                <div class="fancyforms-settings-row">
                    <label class="fancyforms-setting-label fancyforms-color-input-label"><?php esc_html_e('Background Color (Hover)', 'fancy-forms'); ?></label>
                    <div class="fancyforms-setting-fields fancyforms-color-input-field">
                        <input id="fancyforms-upload-bg-color-hover" type="text" class="color-picker fancyforms-color-picker" data-alpha-enabled="true" data-alpha-custom-width="30px" data-alpha-color-type="hex" name="fancyforms_styles[upload][bg_color_hover]" value="<?php echo esc_attr($fancyforms_styles['upload']['bg_color_hover']); ?>">
                    </div>
                </div>

                <div class="fancyforms-settings-row">
                    <label class="fancyforms-setting-label"><?php esc_html_e('Box Shadow (Hover)', 'fancy-forms') ?></label>
                    <div class="fancyforms-setting-fields">
                        <ul class="fancyforms-shadow-fields">
                            <li class="fancyforms-shadow-settings-field">
                                <input id="fancyforms-upload-shadow-hover-x" data-unit="px" type="number" name="fancyforms_styles[upload][shadow_hover][x]" value="<?php echo esc_attr($fancyforms_styles['upload']['shadow_hover']['x']); ?>">
                                <label><?php esc_html_e('H', 'fancy-forms') ?></label>
                            </li>
                            <li class="fancyforms-shadow-settings-field">
                                <input id="fancyforms-upload-shadow-hover-y" data-unit="px" type="number" name="fancyforms_styles[upload][shadow_hover][y]" value="<?php echo esc_attr($fancyforms_styles['upload']['shadow_hover']['y']); ?>">
                                <label><?php esc_html_e('V', 'fancy-forms') ?></label>
                            </li>
                            <li class="fancyforms-shadow-settings-field">
                                <input id="fancyforms-upload-shadow-hover-blur" data-unit="px" type="number" name="fancyforms_styles[upload][shadow_hover][blur]" value="<?php echo esc_attr($fancyforms_styles['upload']['shadow_hover']['blur']); ?>">
                                <label><?php esc_html_e('Blur', 'fancy-forms') ?></label>
                            </li>
                            <li class="fancyforms-shadow-settings-field">
                                <input id="fancyforms-upload-shadow-hover-spread" data-unit="px" type="number" name="fancyforms_styles[upload][shadow_hover][spread]" value="<?php echo esc_attr($fancyforms_styles['upload']['shadow_hover']['spread']); ?>">
                                <label><?php esc_html_e('Spread', 'fancy-forms') ?></label>
                            </li>
                        </ul>
                        <div class="fancyforms-shadow-settings-field">
                            <div class="fancyforms-color-input-field">
                                <input id="fancyforms-upload-shadow-hover-color" type="text" class="color-picker fancyforms-color-picker" data-alpha-enabled="true" data-alpha-custom-width="30px" data-alpha-color-type="hex" name="fancyforms_styles[upload][shadow_hover][color]" value="<?php echo esc_attr($fancyforms_styles['upload']['shadow_hover']['color']); ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="fancyforms-settings-row">
                    <label class="fancyforms-setting-label fancyforms-color-input-label"><?php esc_html_e('Border Color (Hover)', 'fancy-forms'); ?></label>
                    <div class="fancyforms-setting-fields fancyforms-color-input-field">
                        <input id="fancyforms-upload-border-color-hover" type="text" class="color-picker fancyforms-color-picker" data-alpha-enabled="true" data-alpha-custom-width="30px" data-alpha-color-type="hex" name="fancyforms_styles[upload][border_color_hover]" value="<?php echo esc_attr($fancyforms_styles['upload']['border_color_hover']); ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="fancyforms-settings-row">
        <label class="fancyforms-setting-label"><?php esc_html_e('Border Width', 'fancy-forms') ?></label>
        <ul class="fancyforms-unit-fields">
            <li class="fancyforms-unit-settings-field">
                <input id="fancyforms-upload-border-top" data-unit="px" type="number" name="fancyforms_styles[upload][border][top]" value="<?php echo esc_attr($fancyforms_styles['upload']['border']['top']); ?>" min="0">
                <label><?php esc_html_e('Top', 'fancy-forms') ?></label>
            </li>
            <li class="fancyforms-unit-settings-field">
                <input id="fancyforms-upload-border-right" data-unit="px" type="number" name="fancyforms_styles[upload][border][right]" value="<?php echo esc_attr($fancyforms_styles['upload']['border']['right']); ?>" min="0">
                <label><?php esc_html_e('Right', 'fancy-forms') ?></label>
            </li>
            <li class="fancyforms-unit-settings-field">
                <input id="fancyforms-upload-border-bottom" data-unit="px" type="number" name="fancyforms_styles[upload][border][bottom]" value="<?php echo esc_attr($fancyforms_styles['upload']['border']['bottom']); ?>" min="0">
                <label><?php esc_html_e('Bottom', 'fancy-forms') ?></label>
            </li>
            <li class="fancyforms-unit-settings-field">
                <input id="fancyforms-upload-border-left" data-unit="px" type="number" name="fancyforms_styles[upload][border][left]" value="<?php echo esc_attr($fancyforms_styles['upload']['border']['left']); ?>" min="0">
                <label><?php esc_html_e('Left', 'fancy-forms') ?></label>
            </li>
            <li class="fancyforms-unit-settings-field">
                <div class="fancyforms-link-button">
                    <span class="dashicons dashicons-admin-links fancyforms-linked"></span>
                    <span class="dashicons dashicons-editor-unlink fancyforms-unlinked"></span>
                </div>
            </li>
        </ul>
    </div>

    <div class="fancyforms-settings-row">
        <label class="fancyforms-setting-label"><?php esc_html_e('Border Radius', 'fancy-forms') ?></label>
        <ul class="fancyforms-unit-fields">
            <li class="fancyforms-unit-settings-field">
                <input id="fancyforms-upload-border-radius-top" data-unit="px" type="number" name="fancyforms_styles[upload][border_radius][top]" value="<?php echo esc_attr($fancyforms_styles['upload']['border_radius']['top']); ?>" min="0">
                <label><?php esc_html_e('Top', 'fancy-forms') ?></label>
            </li>
            <li class="fancyforms-unit-settings-field">
                <input id="fancyforms-upload-border-radius-right" data-unit="px" type="number" name="fancyforms_styles[upload][border_radius][right]" value="<?php echo esc_attr($fancyforms_styles['upload']['border_radius']['right']); ?>" min="0">
                <label><?php esc_html_e('Right', 'fancy-forms') ?></label>
            </li>
            <li class="fancyforms-unit-settings-field">
                <input id="fancyforms-upload-border-radius-bottom" data-unit="px" type="number" name="fancyforms_styles[upload][border_radius][bottom]" value="<?php echo esc_attr($fancyforms_styles['upload']['border_radius']['bottom']); ?>" min="0">
                <label><?php esc_html_e('Bottom', 'fancy-forms') ?></label>
            </li>
            <li class="fancyforms-unit-settings-field">
                <input id="fancyforms-upload-border-radius-left" data-unit="px" type="number" name="fancyforms_styles[upload][border_radius][left]" value="<?php echo esc_attr($fancyforms_styles['upload']['border_radius']['left']); ?>" min="0">
                <label><?php esc_html_e('Left', 'fancy-forms') ?></label>
            </li>
            <li class="fancyforms-unit-settings-field">
                <div class="fancyforms-link-button">
                    <span class="dashicons dashicons-admin-links fancyforms-linked"></span>
                    <span class="dashicons dashicons-editor-unlink fancyforms-unlinked"></span>
                </div>
            </li>
        </ul>
    </div>

    <div class="fancyforms-settings-row">
        <label class="fancyforms-setting-label"><?php esc_html_e('Padding', 'fancy-forms') ?></label>
        <ul class="fancyforms-unit-fields">
            <li class="fancyforms-unit-settings-field">
                <input id="fancyforms-upload-padding-top" data-unit="px" type="number" name="fancyforms_styles[upload][padding][top]" value="<?php echo esc_attr($fancyforms_styles['upload']['padding']['top']); ?>" min="0">
                <label><?php esc_html_e('Top', 'fancy-forms') ?></label>
            </li>
            <li class="fancyforms-unit-settings-field">
                <input id="fancyforms-upload-padding-right" data-unit="px" type="number" name="fancyforms_styles[upload][padding][right]" value="<?php echo esc_attr($fancyforms_styles['upload']['padding']['right']); ?>" min="0">
                <label><?php esc_html_e('Right', 'fancy-forms') ?></label>
            </li>
            <li class="fancyforms-unit-settings-field">
                <input id="fancyforms-upload-padding-bottom" data-unit="px" type="number" name="fancyforms_styles[upload][padding][bottom]" value="<?php echo esc_attr($fancyforms_styles['upload']['padding']['bottom']); ?>" min="0">
                <label><?php esc_html_e('Bottom', 'fancy-forms') ?></label>
            </li>
            <li class="fancyforms-unit-settings-field">
                <input id="fancyforms-upload-padding-left" data-unit="px" type="number" name="fancyforms_styles[upload][padding][left]" value="<?php echo esc_attr($fancyforms_styles['upload']['padding']['left']); ?>" min="0">
                <label><?php esc_html_e('Left', 'fancy-forms') ?></label>
            </li>
            <li class="fancyforms-unit-settings-field">
                <div class="fancyforms-link-button">
                    <span class="dashicons dashicons-admin-links fancyforms-linked"></span>
                    <span class="dashicons dashicons-editor-unlink fancyforms-unlinked"></span>
                </div>
            </li>
        </ul>
    </div>
</div>

<h2 class="fancyforms-settings-heading"><?php esc_html_e('Submit Button', 'fancy-forms'); ?><span class="mdi mdi-triangle-small-down"></span></h2>
<div class="fancyforms-form-settings">
    <div class="fancyforms-settings-row">
        <label class="fancyforms-setting-label"><?php esc_html_e('Typography', 'fancy-forms'); ?></label>
        <?php self::get_typography_fields('fancyforms_styles', $fancyforms_styles, 'button', array('color')); ?>
    </div>

    <div class="fancyforms-tab-container">
        <ul class="fancyforms-setting-tab">
            <li data-tab="fancyforms-tab-normal" class="fancyforms-tab-active"><?php esc_html_e('Normal', 'fancy-forms'); ?></li>
            <li data-tab="fancyforms-tab-hover"><?php esc_html_e('Hover', 'fancy-forms'); ?></li>
        </ul>

        <div class="fancyforms-setting-tab-panel">
            <div class="fancyforms-tab-normal fancyforms-tab-content">
                <div class="fancyforms-settings-row">
                    <label class="fancyforms-setting-label fancyforms-color-input-label"><?php esc_html_e('Color', 'fancy-forms'); ?></label>
                    <div class="fancyforms-setting-fields fancyforms-color-input-field">
                        <input id="fancyforms-button-color-normal" type="text" class="color-picker fancyforms-color-picker" data-alpha-enabled="true" data-alpha-custom-width="30px" data-alpha-color-type="hex" name="fancyforms_styles[button][color_normal]" value="<?php echo esc_attr($fancyforms_styles['button']['color_normal']); ?>">
                    </div>
                </div>

                <div class="fancyforms-settings-row">
                    <label class="fancyforms-setting-label fancyforms-color-input-label"><?php esc_html_e('Background Color', 'fancy-forms'); ?></label>
                    <div class="fancyforms-setting-fields fancyforms-color-input-field">
                        <input id="fancyforms-button-bg-color-normal" type="text" class="color-picker fancyforms-color-picker" data-alpha-enabled="true" data-alpha-custom-width="30px" data-alpha-color-type="hex" name="fancyforms_styles[button][bg_color_normal]" value="<?php echo esc_attr($fancyforms_styles['button']['bg_color_normal']); ?>">
                    </div>
                </div>

                <div class="fancyforms-settings-row">
                    <label class="fancyforms-setting-label"><?php esc_html_e('Box Shadow', 'fancy-forms') ?></label>
                    <div class="fancyforms-setting-fields">
                        <ul class="fancyforms-shadow-fields">
                            <li class="fancyforms-shadow-settings-field">
                                <input id="fancyforms-button-shadow-normal-x" data-unit="px" type="number" name="fancyforms_styles[button][shadow_normal][x]" value="<?php echo esc_attr($fancyforms_styles['button']['shadow_normal']['x']); ?>">
                                <label><?php esc_html_e('H', 'fancy-forms') ?></label>
                            </li>
                            <li class="fancyforms-shadow-settings-field">
                                <input id="fancyforms-button-shadow-normal-y" data-unit="px" type="number" name="fancyforms_styles[button][shadow_normal][y]" value="<?php echo esc_attr($fancyforms_styles['button']['shadow_normal']['y']); ?>">
                                <label><?php esc_html_e('V', 'fancy-forms') ?></label>
                            </li>
                            <li class="fancyforms-shadow-settings-field">
                                <input id="fancyforms-button-shadow-normal-blur" data-unit="px" type="number" name="fancyforms_styles[button][shadow_normal][blur]" value="<?php echo esc_attr($fancyforms_styles['button']['shadow_normal']['blur']); ?>">
                                <label><?php esc_html_e('Blur', 'fancy-forms') ?></label>
                            </li>
                            <li class="fancyforms-shadow-settings-field">
                                <input id="fancyforms-button-shadow-normal-spread" data-unit="px" type="number" name="fancyforms_styles[button][shadow_normal][spread]" value="<?php echo esc_attr($fancyforms_styles['button']['shadow_normal']['spread']); ?>">
                                <label><?php esc_html_e('Spread', 'fancy-forms') ?></label>
                            </li>
                        </ul>
                        <div class="fancyforms-shadow-settings-field">
                            <div class="fancyforms-color-input-field">
                                <input id="fancyforms-button-shadow-normal-color" type="text" class="color-picker fancyforms-color-picker" data-alpha-enabled="true" data-alpha-custom-width="30px" data-alpha-color-type="hex" name="fancyforms_styles[button][shadow_normal][color]" value="<?php echo esc_attr($fancyforms_styles['button']['shadow_normal']['color']); ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="fancyforms-settings-row">
                    <label class="fancyforms-setting-label fancyforms-color-input-label"><?php esc_html_e('Border Color', 'fancy-forms'); ?></label>
                    <div class="fancyforms-setting-fields fancyforms-color-input-field">
                        <input id="fancyforms-button-border-color-normal" type="text" class="color-picker fancyforms-color-picker" data-alpha-enabled="true" data-alpha-custom-width="30px" data-alpha-color-type="hex" name="fancyforms_styles[button][border_color_normal]" value="<?php echo esc_attr($fancyforms_styles['button']['border_color_normal']); ?>">
                    </div>
                </div>
            </div>

            <div class="fancyforms-tab-hover fancyforms-tab-content" style="display: none;">
                <div class="fancyforms-settings-row">
                    <label class="fancyforms-setting-label fancyforms-color-input-label"><?php esc_html_e('Color (Hover)', 'fancy-forms'); ?></label>
                    <div class="fancyforms-setting-fields fancyforms-color-input-field">
                        <input id="fancyforms-button-color-hover" type="text" class="color-picker fancyforms-color-picker" data-alpha-enabled="true" data-alpha-custom-width="30px" data-alpha-color-type="hex" name="fancyforms_styles[button][color_hover]" value="<?php echo esc_attr($fancyforms_styles['button']['color_hover']); ?>">
                    </div>
                </div>

                <div class="fancyforms-settings-row">
                    <label class="fancyforms-setting-label fancyforms-color-input-label"><?php esc_html_e('Background Color (Hover)', 'fancy-forms'); ?></label>
                    <div class="fancyforms-setting-fields fancyforms-color-input-field">
                        <input id="fancyforms-button-bg-color-hover" type="text" class="color-picker fancyforms-color-picker" data-alpha-enabled="true" data-alpha-custom-width="30px" data-alpha-color-type="hex" name="fancyforms_styles[button][bg_color_hover]" value="<?php echo esc_attr($fancyforms_styles['button']['bg_color_hover']); ?>">
                    </div>
                </div>

                <div class="fancyforms-settings-row">
                    <label class="fancyforms-setting-label"><?php esc_html_e('Box Shadow (Hover)', 'fancy-forms') ?></label>
                    <div class="fancyforms-setting-fields">
                        <ul class="fancyforms-shadow-fields">
                            <li class="fancyforms-shadow-settings-field">
                                <input id="fancyforms-button-shadow-hover-x" data-unit="px" type="number" name="fancyforms_styles[button][shadow_hover][x]" value="<?php echo esc_attr($fancyforms_styles['button']['shadow_hover']['x']); ?>">
                                <label><?php esc_html_e('H', 'fancy-forms') ?></label>
                            </li>
                            <li class="fancyforms-shadow-settings-field">
                                <input id="fancyforms-button-shadow-hover-y" data-unit="px" type="number" name="fancyforms_styles[button][shadow_hover][y]" value="<?php echo esc_attr($fancyforms_styles['button']['shadow_hover']['y']); ?>">
                                <label><?php esc_html_e('V', 'fancy-forms') ?></label>
                            </li>
                            <li class="fancyforms-shadow-settings-field">
                                <input id="fancyforms-button-shadow-hover-blur" data-unit="px" type="number" name="fancyforms_styles[button][shadow_hover][blur]" value="<?php echo esc_attr($fancyforms_styles['button']['shadow_hover']['blur']); ?>">
                                <label><?php esc_html_e('Blur', 'fancy-forms') ?></label>
                            </li>
                            <li class="fancyforms-shadow-settings-field">
                                <input id="fancyforms-button-shadow-hover-spread" data-unit="px" type="number" name="fancyforms_styles[button][shadow_hover][spread]" value="<?php echo esc_attr($fancyforms_styles['button']['shadow_hover']['spread']); ?>">
                                <label><?php esc_html_e('Spread', 'fancy-forms') ?></label>
                            </li>
                        </ul>
                        <div class="fancyforms-shadow-settings-field">
                            <div class="fancyforms-color-input-field">
                                <input id="fancyforms-button-shadow-hover-color" type="text" class="color-picker fancyforms-color-picker" data-alpha-enabled="true" data-alpha-custom-width="30px" data-alpha-color-type="hex" name="fancyforms_styles[button][shadow_hover][color]" value="<?php echo esc_attr($fancyforms_styles['button']['shadow_hover']['color']); ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="fancyforms-settings-row">
                    <label class="fancyforms-setting-label fancyforms-color-input-label"><?php esc_html_e('Border Color (Hover)', 'fancy-forms'); ?></label>
                    <div class="fancyforms-setting-fields fancyforms-color-input-field">
                        <input id="fancyforms-button-border-color-hover" type="text" class="color-picker fancyforms-color-picker" data-alpha-enabled="true" data-alpha-custom-width="30px" data-alpha-color-type="hex" name="fancyforms_styles[button][border_color_hover]" value="<?php echo esc_attr($fancyforms_styles['button']['border_color_hover']); ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="fancyforms-settings-row">
        <label class="fancyforms-setting-label"><?php esc_html_e('Border Width', 'fancy-forms') ?></label>
        <ul class="fancyforms-unit-fields">
            <li class="fancyforms-unit-settings-field">
                <input id="fancyforms-button-border-top" data-unit="px" type="number" name="fancyforms_styles[button][border][top]" value="<?php echo esc_attr($fancyforms_styles['button']['border']['top']); ?>" min="0">
                <label><?php esc_html_e('Top', 'fancy-forms') ?></label>
            </li>
            <li class="fancyforms-unit-settings-field">
                <input id="fancyforms-button-border-right" data-unit="px" type="number" name="fancyforms_styles[button][border][right]" value="<?php echo esc_attr($fancyforms_styles['button']['border']['right']); ?>" min="0">
                <label><?php esc_html_e('Right', 'fancy-forms') ?></label>
            </li>
            <li class="fancyforms-unit-settings-field">
                <input id="fancyforms-button-border-bottom" data-unit="px" type="number" name="fancyforms_styles[button][border][bottom]" value="<?php echo esc_attr($fancyforms_styles['button']['border']['bottom']); ?>" min="0">
                <label><?php esc_html_e('Bottom', 'fancy-forms') ?></label>
            </li>
            <li class="fancyforms-unit-settings-field">
                <input id="fancyforms-button-border-left" data-unit="px" type="number" name="fancyforms_styles[button][border][left]" value="<?php echo esc_attr($fancyforms_styles['button']['border']['left']); ?>" min="0">
                <label><?php esc_html_e('Left', 'fancy-forms') ?></label>
            </li>
            <li class="fancyforms-unit-settings-field">
                <div class="fancyforms-link-button">
                    <span class="dashicons dashicons-admin-links fancyforms-linked"></span>
                    <span class="dashicons dashicons-editor-unlink fancyforms-unlinked"></span>
                </div>
            </li>
        </ul>
    </div>

    <div class="fancyforms-settings-row">
        <label class="fancyforms-setting-label"><?php esc_html_e('Border Radius', 'fancy-forms') ?></label>
        <ul class="fancyforms-unit-fields">
            <li class="fancyforms-unit-settings-field">
                <input id="fancyforms-button-border-radius-top" data-unit="px" type="number" name="fancyforms_styles[button][border_radius][top]" value="<?php echo esc_attr($fancyforms_styles['button']['border_radius']['top']); ?>" min="0">
                <label><?php esc_html_e('Top', 'fancy-forms') ?></label>
            </li>
            <li class="fancyforms-unit-settings-field">
                <input id="fancyforms-button-border-radius-right" data-unit="px" type="number" name="fancyforms_styles[button][border_radius][right]" value="<?php echo esc_attr($fancyforms_styles['button']['border_radius']['right']); ?>" min="0">
                <label><?php esc_html_e('Right', 'fancy-forms') ?></label>
            </li>
            <li class="fancyforms-unit-settings-field">
                <input id="fancyforms-button-border-radius-bottom" data-unit="px" type="number" name="fancyforms_styles[button][border_radius][bottom]" value="<?php echo esc_attr($fancyforms_styles['button']['border_radius']['bottom']); ?>" min="0">
                <label><?php esc_html_e('Bottom', 'fancy-forms') ?></label>
            </li>
            <li class="fancyforms-unit-settings-field">
                <input id="fancyforms-button-border-radius-left" data-unit="px" type="number" name="fancyforms_styles[button][border_radius][left]" value="<?php echo esc_attr($fancyforms_styles['button']['border_radius']['left']); ?>" min="0">
                <label><?php esc_html_e('Left', 'fancy-forms') ?></label>
            </li>
            <li class="fancyforms-unit-settings-field">
                <div class="fancyforms-link-button">
                    <span class="dashicons dashicons-admin-links fancyforms-linked"></span>
                    <span class="dashicons dashicons-editor-unlink fancyforms-unlinked"></span>
                </div>
            </li>
        </ul>
    </div>

    <div class="fancyforms-settings-row">
        <label class="fancyforms-setting-label"><?php esc_html_e('Padding', 'fancy-forms') ?></label>
        <ul class="fancyforms-unit-fields">
            <li class="fancyforms-unit-settings-field">
                <input id="fancyforms-button-padding-top" data-unit="px" type="number" name="fancyforms_styles[button][padding][top]" value="<?php echo esc_attr($fancyforms_styles['button']['padding']['top']); ?>" min="0">
                <label><?php esc_html_e('Top', 'fancy-forms') ?></label>
            </li>
            <li class="fancyforms-unit-settings-field">
                <input id="fancyforms-button-padding-right" data-unit="px" type="number" name="fancyforms_styles[button][padding][right]" value="<?php echo esc_attr($fancyforms_styles['button']['padding']['right']); ?>" min="0">
                <label><?php esc_html_e('Right', 'fancy-forms') ?></label>
            </li>
            <li class="fancyforms-unit-settings-field">
                <input id="fancyforms-button-padding-bottom" data-unit="px" type="number" name="fancyforms_styles[button][padding][bottom]" value="<?php echo esc_attr($fancyforms_styles['button']['padding']['bottom']); ?>" min="0">
                <label><?php esc_html_e('Bottom', 'fancy-forms') ?></label>
            </li>
            <li class="fancyforms-unit-settings-field">
                <input id="fancyforms-button-padding-left" data-unit="px" type="number" name="fancyforms_styles[button][padding][left]" value="<?php echo esc_attr($fancyforms_styles['button']['padding']['left']); ?>" min="0">
                <label><?php esc_html_e('Left', 'fancy-forms') ?></label>
            </li>
            <li class="fancyforms-unit-settings-field">
                <div class="fancyforms-link-button">
                    <span class="dashicons dashicons-admin-links fancyforms-linked"></span>
                    <span class="dashicons dashicons-editor-unlink fancyforms-unlinked"></span>
                </div>
            </li>
        </ul>
    </div>
</div>


<h2 class="fancyforms-settings-heading"><?php esc_html_e('Validation', 'fancy-forms'); ?><span class="mdi mdi-triangle-small-down"></span></h2>
<div class="fancyforms-form-settings">
    <div class="fancyforms-settings-row">
        <label class="fancyforms-setting-label"><?php esc_html_e('Typography', 'fancy-forms'); ?></label>
        <?php self::get_typography_fields('fancyforms_styles', $fancyforms_styles, 'validation'); ?>
    </div>

    <div class="fancyforms-settings-row">
        <label class="fancyforms-setting-label"><?php esc_html_e('Top Spacing', 'fancy-forms'); ?></label>
        <div class="fancyforms-setting-fields fancyforms-range-slider-wrap">
            <div class="fancyforms-range-slider"></div>
            <input data-unit="px" id="fancyforms-validation-spacing" class="fancyforms-range-input-selector" type="number" name="fancyforms_styles[validation][spacing]" value="<?php echo is_numeric($fancyforms_styles['validation']['spacing']) ? intval($fancyforms_styles['validation']['spacing']) : ''; ?>" min="0" max="100" step="1"> px
        </div>
    </div>

    <div class="fancyforms-settings-row">
        <label class="fancyforms-setting-label"><?php esc_html_e('Text Align', 'fancy-forms') ?></label>
        <select id="fancyforms-validation-textalign" name="fancyforms_styles[validation][textalign]">
            <option value="left" <?php selected($fancyforms_styles['validation']['textalign'], 'left'); ?>><?php esc_html_e('Left', 'fancy-forms'); ?></option>
            <option value="center" <?php selected($fancyforms_styles['validation']['textalign'], 'center'); ?>><?php esc_html_e('Center', 'fancy-forms'); ?></option>
            <option value="right" <?php selected($fancyforms_styles['validation']['textalign'], 'right'); ?>><?php esc_html_e('Right', 'fancy-forms'); ?></option>
        </select>
    </div>
</div>


<h2 class="fancyforms-settings-heading"><?php esc_html_e('Form Title', 'fancy-forms'); ?><span class="mdi mdi-triangle-small-down"></span></h2>
<div class="fancyforms-form-settings">
    <div class="fancyforms-settings-row">
        <label class="fancyforms-setting-label"><?php esc_html_e('Typography', 'fancy-forms'); ?></label>
        <?php self::get_typography_fields('fancyforms_styles', $fancyforms_styles, 'form_title'); ?>
    </div>

    <div class="fancyforms-settings-row">
        <label class="fancyforms-setting-label"><?php esc_html_e('Bottom Spacing', 'fancy-forms'); ?></label>
        <div class="fancyforms-setting-fields fancyforms-range-slider-wrap">
            <div class="fancyforms-range-slider"></div>
            <input data-unit="px" id="fancyforms-form-title-spacing" class="fancyforms-range-input-selector" type="number" name="fancyforms_styles[form_title][spacing]" value="<?php echo is_numeric($fancyforms_styles['form_title']['spacing']) ? intval($fancyforms_styles['form_title']['spacing']) : ''; ?>" min="0" max="100" step="1"> px
        </div>
    </div>
</div>


<h2 class="fancyforms-settings-heading"><?php esc_html_e('Form Description', 'fancy-forms'); ?><span class="mdi mdi-triangle-small-down"></span></h2>
<div class="fancyforms-form-settings">
    <div class="fancyforms-settings-row">
        <label class="fancyforms-setting-label"><?php esc_html_e('Typography', 'fancy-forms'); ?></label>
        <?php self::get_typography_fields('fancyforms_styles', $fancyforms_styles, 'form_desc'); ?>
    </div>

    <div class="fancyforms-settings-row">
        <label class="fancyforms-setting-label"><?php esc_html_e('Bottom Spacing', 'fancy-forms'); ?></label>
        <div class="fancyforms-setting-fields fancyforms-range-slider-wrap">
            <div class="fancyforms-range-slider"></div>
            <input data-unit="px" id="fancyforms-form-desc-spacing" class="fancyforms-range-input-selector" type="number" name="fancyforms_styles[form_desc][spacing]" value="<?php echo is_numeric($fancyforms_styles['form_desc']['spacing']) ? intval($fancyforms_styles['form_desc']['spacing']) : ''; ?>" min="0" max="100" step="1"> px
        </div>
    </div>
</div>


<h2 class="fancyforms-settings-heading"><?php esc_html_e('Heading', 'fancy-forms'); ?><span class="mdi mdi-triangle-small-down"></span></h2>
<div class="fancyforms-form-settings">
    <div class="fancyforms-settings-row">
        <label class="fancyforms-setting-label"><?php esc_html_e('Typography', 'fancy-forms'); ?></label>
        <?php self::get_typography_fields('fancyforms_styles', $fancyforms_styles, 'heading'); ?>
    </div>
</div>

<h2 class="fancyforms-settings-heading"><?php esc_html_e('Paragraph', 'fancy-forms'); ?><span class="mdi mdi-triangle-small-down"></span></h2>
<div class="fancyforms-form-settings">
    <div class="fancyforms-settings-row">
        <label class="fancyforms-setting-label"><?php esc_html_e('Typography', 'fancy-forms'); ?></label>
        <?php self::get_typography_fields('fancyforms_styles', $fancyforms_styles, 'paragraph'); ?>
    </div>
</div>

<h2 class="fancyforms-settings-heading"><?php esc_html_e('Divider', 'fancy-forms'); ?><span class="mdi mdi-triangle-small-down"></span></h2>
<div class="fancyforms-form-settings">
    <div class="fancyforms-settings-row">
        <label class="fancyforms-setting-label fancyforms-color-input-label"><?php esc_html_e('Color', 'fancy-forms'); ?></label>
        <div class="fancyforms-setting-fields fancyforms-color-input-field">
            <input id="fancyforms-divider-color" type="text" class="color-picker fancyforms-color-picker" data-alpha-enabled="true" data-alpha-custom-width="30px" data-alpha-color-type="hex" name="fancyforms_styles[divider_color]" value="<?php echo esc_attr($fancyforms_styles['divider_color']); ?>">
        </div>
    </div>
</div>

<h2 class="fancyforms-settings-heading"><?php esc_html_e('Star', 'fancy-forms'); ?><span class="mdi mdi-triangle-small-down"></span></h2>
<div class="fancyforms-form-settings">
    <div class="fancyforms-settings-row">
        <label class="fancyforms-setting-label"><?php esc_html_e('Size', 'fancy-forms'); ?></label>
        <div class="fancyforms-setting-fields fancyforms-range-slider-wrap">
            <div class="fancyforms-range-slider"></div>
            <input data-unit="px" id="fancyforms-star-size" class="fancyforms-range-input-selector" type="number" name="fancyforms_styles[star][size]" value="<?php echo is_numeric($fancyforms_styles['star']['size']) ? intval($fancyforms_styles['star']['size']) : ''; ?>" min="0" max="100" step="1"> px
        </div>
    </div>
    <div class="fancyforms-settings-row">
        <label class="fancyforms-setting-label fancyforms-color-input-label"><?php esc_html_e('Color', 'fancy-forms'); ?></label>
        <div class="fancyforms-setting-fields fancyforms-color-input-field">
            <input id="fancyforms-star-color" type="text" class="color-picker fancyforms-color-picker" data-alpha-enabled="true" data-alpha-custom-width="30px" data-alpha-color-type="hex" name="fancyforms_styles[star][color]" value="<?php echo esc_attr($fancyforms_styles['star']['color']); ?>">
        </div>
    </div>
    <div class="fancyforms-settings-row">
        <label class="fancyforms-setting-label fancyforms-color-input-label"><?php esc_html_e('Color (Active)', 'fancy-forms'); ?></label>
        <div class="fancyforms-setting-fields fancyforms-color-input-field">
            <input id="fancyforms-star-color-active" type="text" class="color-picker fancyforms-color-picker" data-alpha-enabled="true" data-alpha-custom-width="30px" data-alpha-color-type="hex" name="fancyforms_styles[star][color_active]" value="<?php echo esc_attr($fancyforms_styles['star']['color_active']); ?>">
        </div>
    </div>
</div>

<h2 class="fancyforms-settings-heading"><?php esc_html_e('Range Slider', 'fancy-forms'); ?><span class="mdi mdi-triangle-small-down"></span></h2>
<div class="fancyforms-form-settings">
    <div class="fancyforms-settings-row">
        <label class="fancyforms-setting-label"><?php esc_html_e('Height', 'fancy-forms'); ?></label>
        <div class="fancyforms-setting-fields fancyforms-range-slider-wrap">
            <div class="fancyforms-range-slider"></div>
            <input data-unit="px" id="fancyforms-range-height" class="fancyforms-range-input-selector" type="number" name="fancyforms_styles[range][height]" value="<?php echo is_numeric($fancyforms_styles['range']['height']) ? intval($fancyforms_styles['range']['height']) : ''; ?>" min="0" max="100" step="1"> px
        </div>
    </div>
    <div class="fancyforms-settings-row">
        <label class="fancyforms-setting-label"><?php esc_html_e('Handle Size', 'fancy-forms'); ?></label>
        <div class="fancyforms-setting-fields fancyforms-range-slider-wrap">
            <div class="fancyforms-range-slider"></div>
            <input data-unit="px" id="fancyforms-range-handle-size" class="fancyforms-range-input-selector" type="number" name="fancyforms_styles[range][handle_size]" value="<?php echo is_numeric($fancyforms_styles['range']['handle_size']) ? intval($fancyforms_styles['range']['handle_size']) : ''; ?>" min="0" max="100" step="1"> px
        </div>
    </div>
    <div class="fancyforms-settings-row">
        <label class="fancyforms-setting-label fancyforms-color-input-label"><?php esc_html_e('Bar Color', 'fancy-forms'); ?></label>
        <div class="fancyforms-setting-fields fancyforms-color-input-field">
            <input id="fancyforms-range-color" type="text" class="color-picker fancyforms-color-picker" data-alpha-enabled="true" data-alpha-custom-width="30px" data-alpha-color-type="hex" name="fancyforms_styles[range][color]" value="<?php echo esc_attr($fancyforms_styles['range']['color']); ?>">
        </div>
    </div>
    <div class="fancyforms-settings-row">
        <label class="fancyforms-setting-label fancyforms-color-input-label"><?php esc_html_e('Bar Color (Active)', 'fancy-forms'); ?></label>
        <div class="fancyforms-setting-fields fancyforms-color-input-field">
            <input id="fancyforms-range-color-active" type="text" class="color-picker fancyforms-color-picker" data-alpha-enabled="true" data-alpha-custom-width="30px" data-alpha-color-type="hex" name="fancyforms_styles[range][color_active]" value="<?php echo esc_attr($fancyforms_styles['range']['color_active']); ?>">
        </div>
    </div>
    <div class="fancyforms-settings-row">
        <label class="fancyforms-setting-label fancyforms-color-input-label"><?php esc_html_e('Handle Color', 'fancy-forms'); ?></label>
        <div class="fancyforms-setting-fields fancyforms-color-input-field">
            <input id="fancyforms-range-handle-color" type="text" class="color-picker fancyforms-color-picker" data-alpha-enabled="true" data-alpha-custom-width="30px" data-alpha-color-type="hex" name="fancyforms_styles[range][handle_color]" value="<?php echo esc_attr($fancyforms_styles['range']['handle_color']); ?>">
        </div>
    </div>
</div>

<?php do_action('fancyforms_styles_settings', $fancyforms_styles); ?>


<h2 class="fancyforms-settings-heading"><?php esc_html_e('Import/Export', 'fancy-forms'); ?><span class="mdi mdi-triangle-small-down"></span></h2>
<div class="fancyforms-form-settings">
    <p>
        <?php esc_html_e("You can export the form styles and then import the form styles in the same or different website.", "fancy-forms"); ?>
    </p>

    <div class="fancyforms-settings-row">
        <label class="fancyforms-setting-label"><?php esc_html_e('Export', 'fancy-forms'); ?></label>
        <div class="fancyforms-setting-fields">
            <form method="post"></form>
            <form method="post">
                <input type="hidden" name="fancyforms_imex_action" value="export_style" />
                <input type="hidden" name="fancyforms_style_id" value="<?php echo esc_attr($id); ?>" />
                <?php wp_nonce_field("fancyforms_imex_export_nonce", "fancyforms_imex_export_nonce"); ?>
                <button class="button button-primary" id="fancyforms_export" name="fancyforms_export"><span class="mdi mdi-tray-arrow-down"></span> <?php esc_html_e("Export Style", "fancy-forms") ?></button>
            </form>
        </div>
    </div>

    <div class="fancyforms-settings-row">
        <label class="fancyforms-setting-label"><?php esc_html_e('Import', 'fancy-forms'); ?></label>
        <div class="fancyforms-setting-fields">
            <form method="post" enctype="multipart/form-data">
                <div class="fancyforms-preview-zone hidden">
                    <div class="fancyforms-box fancyforms-box-solid">
                        <div class="fancyforms-box-body"></div>
                        <button type="button" class="button fancyforms-remove-preview">
                            <span class="mdi mdi-window-close"></span>
                        </button>
                    </div>
                </div>
                <div class="fancyforms-dropzone-wrapper">
                    <div class="fancyforms-dropzone-desc">
                        <span class="mdi mdi-file-image-plus-outline"></span>
                        <p><?php esc_html_e("Choose an json file or drag it here", "fancy-forms"); ?></p>
                    </div>
                    <input type="file" name="fancyforms_import_file" class="fancyforms-dropzone">
                </div>
                <button class="button button-primary" id="fancyforms_import" type="submit" name="fancyforms_import"><i class='icofont-download'></i> <?php esc_html_e("Import", "fancy-forms") ?></button>
                <input type="hidden" name="fancyforms_imex_action" value="import_style" />
                <input type="hidden" name="fancyforms_style_id" value="<?php echo esc_attr($id); ?>" />
                <?php wp_nonce_field("fancyforms_imex_import_nonce", "fancyforms_imex_import_nonce"); ?>
            </form>
        </div>
    </div>
</div>