<?php
defined('ABSPATH') || die();

class FancyFormsFieldUpload extends FancyFormsFieldType {

    protected $type = 'upload';

    protected function field_settings_for_type() {
        return array(
            'default' => false,
        );
    }

    protected function extra_field_default_opts() {
        return array(
            'upload_label' => esc_html__('Upload File', 'fancy-forms'),
            'max_upload_size' => 10,
            'extensions' => 'jpg,jpeg,gif,png',
            'extensions_error_message' => esc_html__('Invalid Extension', 'fancy-forms'),
            'multiple_uploads' => 'on',
            'multiple_uploads_limit' => 5,
            'multiple_uploads_error_message' => esc_html__('Maximum file upload limit exceeded', 'fancy-forms'),
        );
    }

    protected function input_html() {
        $field = $this->get_field();
        $max_size = absint($field['max_upload_size']);
        $max_size = $max_size ? $max_size : 10;
        $max_size = $max_size * 1024 * 1024;
        $new_extensions = fancyforms_sanitize_allowed_file_extensions($field['extensions']);

        if (is_admin() && !FancyFormsHelper::is_preview_page()) {
            ?>
            <div class="fancyforms-file-uploader-wrapper">
                <div class="fancyforms-file-uploader">
                    <div class="qq-uploader">
                        <div id="fancyforms-editor-upload-label-text-<?php echo absint($field['id']); ?>" class="qq-upload-button"><?php esc_html_e($field['upload_label']); ?></div>
                    </div>
                </div>
            </div>
            <?php
        } else {
            ?>
            <div class="fancyforms-file-uploader-wrapper">
                <div class="fancyforms-file-uploader"
                     id="fancyforms-file-uploader-<?php echo mt_rand(100, 99999); ?>"
                     data-upload-label="<?php echo esc_attr($field['upload_label']); ?>"
                     data-extensions="<?php echo esc_attr($new_extensions); ?>"
                     data-extensions-error-message="<?php echo esc_attr($field['extensions_error_message']); ?>"
                     data-multiple-uploads="<?php echo $field['multiple_uploads'] == 'on' ? 'true' : 'false'; ?>"
                     data-multiple-uploads-limit="<?php echo $field['multiple_uploads'] == 'on' ? absint($field['multiple_uploads_limit']) : '-1'; ?>"
                     data-multiple-uploads-error-message="<?php echo esc_attr($field['multiple_uploads_error_message']); ?>"
                     data-max-upload-size="<?php echo esc_attr($max_size); ?>"
                     data-field-uploader-id="<?php echo esc_attr($this->html_id()); ?>">
                    <div class="qq-uploader qq-fake-uploader">
                        <div class="qq-upload-button" style="position: relative; overflow: hidden; direction: ltr;">
                            <?php echo esc_attr($field['upload_label']); ?>
                        </div>
                    </div>
                </div>

                <div class="fancyforms-file-preview"></div>

                <input type="hidden" class="fancyforms-uploaded-files" <?php $this->field_attrs(); ?>>
                <input type="hidden" class="fancyforms-multiple-upload-limit" value="0">
            </div>
            <?php
        }
    }

    public function set_value_before_save($files) {
        $new_files = array();
        $files_arr = explode(',', $files);
        FancyFormsBuilder::remove_old_temp_files();

        foreach ($files_arr as $file) {
            $file_info = pathinfo($file);
            $file_name = $file_info['basename'];
            $upload_dir = wp_upload_dir();

            $file_path = $upload_dir['basedir'] . FANCYFORMS_UPLOAD_DIR;
            $file_url = $upload_dir['baseurl'] . FANCYFORMS_UPLOAD_DIR;
            $temp_file_path = $file_path . '/temp/' . $file_name;
            $to_path = $file_path . '/' . $file_name;
            $to_url = $file_url . '/' . $file_name;

            if (copy($temp_file_path, $to_path)) {
                $new_files[] = $to_url;
            }
        }
        return implode(',', $new_files);
    }

}
