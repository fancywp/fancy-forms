<?php
defined('ABSPATH') || die();

$id = htmlspecialchars_decode(FancyFormsHelper::get_var('id', 'absint'));
$form = FancyFormsBuilder::get_form_vars($id);

if (!$form) {
    ?>
    <h3><?php esc_html_e('You are trying to edit a form that does not exist.', 'fancy-forms'); ?></h3>
    <?php
    return;
}
$fields = FancyFormsFields::get_form_fields($form->id);
$values = FancyFormsHelper::process_form_array($form);

$edit_message = '<span class="mdi mdi-check-circle"></span>' . esc_html__('Form was successfully updated.', 'fancy-forms');
$has_fields = isset($fields) && !empty($fields);

if (!empty($fields)) {
    $vars = FancyFormsHelper::get_fields_array($id);
}

if (defined('DOING_AJAX')) {
    wp_die();
} else {
    ?>
    <div id="fancyforms-wrap" class="fancyforms-content">
        <?php
        self::get_admin_header(
                array(
                    'form' => $form,
                    'class' => 'fancyforms-header-nav',
                )
        );
        ?>
        <div class="fancyforms-body">
            <?php require( FANCYFORMS_PATH . 'admin/forms/build/sidebar.php' ); ?>

            <div id="fancyforms-form-panel">
                <div class="fancyforms-form-wrap">
                    <form method="post">
                        <?php require( FANCYFORMS_PATH . 'admin/forms/build/builder.php' ); ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php
}