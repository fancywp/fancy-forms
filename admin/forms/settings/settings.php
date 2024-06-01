<?php
defined('ABSPATH') || die();

$id = FancyFormsHelper::get_var('id', 'absint', 0);
$form = FancyFormsBuilder::get_form_vars($id);
$fields = FancyFormsFields::get_form_fields($id);

$settings = $form->settings ? $form->settings : FancyFormsHelper::get_form_settings_default();
?>
<div id="fancyforms-wrap" class="fancyforms-content">
    <?php
    self::get_admin_header(
            array(
                'form' => $form,
                'class' => 'fancyforms-header-nav',
            )
    );

    $sections = array(
        'email-settings' => array(
            'name' => esc_html__('Email Settings', 'fancy-forms'),
            'icon' => 'mdi mdi-email-outline'
        ),
        'auto-responder' => array(
            'name' => esc_html__('Auto Responder', 'fancy-forms'),
            'icon' => 'mdi mdi-email-arrow-left-outline'
        ),
        'form-confirmation' => array(
            'name' => esc_html__('Confirmation', 'fancy-forms'),
            'icon' => 'mdi mdi-send-check'
        ),
        'conditional-logic' => array(
            'name' => esc_html__('Conditional Logic', 'fancy-forms'),
            'icon' => 'mdi mdi-checkbox-multiple-marked-outline'
        ),
        'import-export' => array(
            'name' => esc_html__('Import/Export', 'fancy-forms'),
            'icon' => 'mdi mdi-swap-horizontal'
        ),
    );
    $sections = apply_filters('fancyforms_settings_sections', $sections);
    $current = 'email-settings';
    ?>

    <div class="fancyforms-body">
        <div class="fancyforms-fields-sidebar">
            <ul class="fancyforms-settings-tab">
                <?php foreach ($sections as $key => $section) { ?>
                    <li class="<?php echo ($current === $key ? 'fancyforms-active' : ''); ?>">
                        <a href="#fancyforms-<?php echo esc_attr($key); ?>">
                            <i class="<?php echo esc_attr($section['icon']) ?>"></i>
                            <?php echo esc_html($section['name']); ?>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </div>

        <div id="fancyforms-form-panel">
            <?php FancyFormsHelper::print_message(); ?>
            <div class="fancyforms-form-wrap">
                <form method="post" id="fancyforms-settings-form">
                    <input type="hidden" name="id" id="form_id" value="<?php echo esc_attr($id); ?>" />
                    <?php
                    wp_nonce_field('fancyforms_process_form_nonce', 'process_form');
                    foreach ($sections as $key => $section) {
                        ?>
                        <div id="fancyforms-<?php echo esc_attr($key); ?>" class="<?php echo (($current === $key) ? '' : ' fancyforms-hidden'); ?>">
                            <h2><?php echo esc_html($section['name']); ?></h2>
                            <?php
                            $file_path = FANCYFORMS_PATH . 'admin/forms/settings/';
                            if(file_exists( $file_path . esc_attr($key) . '.php')) {
                                require $file_path . esc_attr($key) . '.php';
                            }
                            do_action('fancyforms_settings_sections_content', array(
                                'section_key' => $key,
                                'settings' => $settings,
                                'fields' => $fields
                            ));
                            ?>
                        </div>
                    <?php } ?>
                </form>
            </div>
        </div>
    </div>
</div>