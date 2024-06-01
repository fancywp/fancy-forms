<?php
defined('ABSPATH') || die();

$styles = $form->styles ? $form->styles : array();
$style_id = isset($styles['form_style_template']) ? $styles['form_style_template'] : '';
$fancyforms_styles = get_post_meta($style_id, 'fancyforms_styles', true);
$submit_class = isset($form->options['submit_btn_alignment']) ? 'fancyforms-submit-btn-align-' . esc_html($form->options['submit_btn_alignment']) : 'fancyforms-submit-btn-align-left';
$submit = isset($form->options['submit_value']) ? esc_html($form->options['submit_value']) : esc_html__('Submit', 'fancy-forms');
$button_class = array('fancyforms-submit-button');
if (isset($form->options['submit_btn_css_class'])) {
    $button_class[] = esc_attr($form->options['submit_btn_css_class']);
}

$form_title = esc_html($form->name);
$form_description = esc_html($form->description);
$show_title = isset($form->options['show_title']) ? esc_html($form->options['show_title']) : 'on';
$show_description = isset($form->options['show_description']) ? esc_html($form->options['show_description']) : 'off';
$fancyforms_action = htmlspecialchars_decode(FancyFormsHelper::get_var('action'));

if (!$fancyforms_styles) {
    $fancyforms_styles = FancyFormsStyles::default_styles();
} else {
    $fancyforms_styles = FancyFormsHelper::recursive_parse_args($fancyforms_styles, FancyFormsStyles::default_styles());
}
?>

<div class="fancyforms-form-preview" id="fancyforms-container-<?php echo esc_attr($form->id); ?>">
    <?php
    if (empty($values) || !isset($values['fields']) || empty($values['fields'])) {
        ?>
        <div class="fancyforms-form-error">
            <strong><?php esc_html_e('Oops!', 'fancy-forms'); ?></strong>
            <?php printf(esc_html__('You did not add any fields to your form. %1$sGo back%2$s and add some.', 'fancy-forms'), '<a href="' . esc_url(admin_url('admin.php?page=fancyforms&fancyforms_action=edit&id=' . absint($id))) . '">', '</a>'); ?>
        </div>
        <?php
        return;
    }

    if ($show_title == 'on' && $form_title) {
        ?>
        <h3 class="fancyforms-form-title"><?php echo esc_html($form_title); ?></h3>
        <?php
    }

    if ($show_description == 'on' && $form_description) {
        ?>
        <div class="fancyforms-form-description"><?php echo esc_html($form_description); ?></div>
        <?php
    }
    ?>
    <div class="fancyforms-container">
        <input type="hidden" name="fancyforms_action" value="create" />
        <input type="hidden" name="form_id" value="<?php echo absint($form->id); ?>" />
        <input type="hidden" name="form_key" value="<?php echo esc_attr($form->form_key); ?>" />
        <input type="hidden" class="fancyforms-form-conditions" value="<?php echo esc_attr(htmlspecialchars(wp_json_encode(FancyFormsBuilder::get_show_hide_conditions(absint($form->id))), ENT_QUOTES, 'UTF-8')); ?>" />
        <?php
        wp_nonce_field('fancyforms_submit_entry_nonce', 'fancyforms_submit_entry_' . absint($form->id));

        if ($values['fields']) {
            FancyFormsFields::show_fields($values['fields']);
        }
        ?>
        <div class="fancyforms-submit-wrap <?php echo esc_attr($submit_class); ?>">
            <button class="<?php echo esc_attr(implode(' ', $button_class)) ?>" type="submit" <?php disabled($fancyforms_action, 'fancyforms_preview'); ?>><?php echo esc_html($submit); ?></button>
        </div>
    </div>
    <?php
    echo '<style class="fancyforms-style-content">';
    echo '#fancyforms-container-' . absint($form->id) . '{';
    FancyFormsStyles::get_style_vars($fancyforms_styles, '');
    echo '}';
    echo '</style>';
    ?>
</div>