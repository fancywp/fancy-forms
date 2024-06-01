<?php

defined('ABSPATH') || die();

class FancyFormsFields {

    public function __construct() {
        self::include_field_class();
        add_action('wp_ajax_fancyforms_insert_field', array($this, 'create'));
        add_action('wp_ajax_fancyforms_delete_field', array($this, 'destroy'));
        add_action('wp_ajax_fancyforms_import_options', array($this, 'import_options'));
        //add_action('wp_ajax_fancyforms_duplicate_field', array($this, 'duplicate'));
    }

    public static function get_form_fields($form_id) {
        global $wpdb;
        $form_id = absint($form_id);
        $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}fancyforms_fields WHERE form_id=%d ORDER BY field_order", $form_id);
        $results = $wpdb->get_results($query);
        foreach ($results as $value) {
            foreach ($value as $key => $val) {
                $value->$key = maybe_unserialize($val);
            }
        }
        return $results;
    }

    public static function create() {
        if (!current_user_can('manage_options')) {
            return;
        }

        check_ajax_referer('fancyforms_ajax', 'nonce');
        $field_type = FancyFormsHelper::get_post('field_type', 'sanitize_text_field');
        $form_id = FancyFormsHelper::get_post('form_id', 'absint', 0);
        self::include_new_field($field_type, $form_id);
        wp_die();
    }

    public static function destroy() {
        if (!current_user_can('manage_options')) {
            return;
        }

        check_ajax_referer('fancyforms_ajax', 'nonce');
        $field_id = FancyFormsHelper::get_post('field_id', 'absint', 0);
        self::destroy_row($field_id);
        wp_die();
    }

    public static function include_new_field($field_type, $form_id) {
        $field_values = self::setup_new_field_vars($field_type, $form_id);
        $field_id = FancyFormsFields::create_row($field_values);
        if (!$field_id) {
            return false;
        }
        $field = self::get_field_vars($field_id);
        $field_array = self::covert_field_obj_to_array($field);
        $field_obj = FancyFormsFields::get_field_class($field_array['type'], $field_array);
        $field_obj->load_single_field();
    }

    public static function setup_new_field_vars($type = '', $form_id = '') {
        global $wpdb;
        $sql = $wpdb->prepare("SELECT field_order FROM {$wpdb->prefix}fancyforms_fields WHERE form_id=%d ORDER BY field_order DESC", $form_id);
        $field_count = $wpdb->get_var($sql);
        $values = self::get_default_field($type);
        $values['field_key'] = FancyFormsHelper::get_unique_key('fancyforms_fields', 'field_key');
        $values['form_id'] = $form_id;
        $values['field_order'] = $field_count + 1;
        return $values;
    }

    public static function covert_field_obj_to_array($field) {
        $field_array = json_decode(wp_json_encode($field), true);
        $field_options = $field_array['field_options'];
        unset($field_array['field_options']);
        return array_merge($field_array, $field_options);
    }

    public static function get_default_field($type) {
        $field_obj = FancyFormsFields::get_field_class($type);
        return $field_obj->get_new_field_defaults();
    }

    public static function import_options() {
        if (!current_user_can('manage_options')) {
            return;
        }

        $field_id = FancyFormsHelper::get_post('field_id', 'absint');
        $field = self::get_field_vars($field_id);
        if (!in_array($field->type, array('radio', 'checkbox', 'select'))) {
            return;
        }

        $field_array = self::covert_field_obj_to_array($field);
        $field_array['type'] = $field->type;
        $field_array['value'] = $field->default_value;

        $opts = htmlspecialchars_decode(FancyFormsHelper::get_post('opts', 'esc_html'));
        $opts = explode("\n", rtrim($opts, "\n"));
        $opts = array_map('trim', $opts);

        foreach ($opts as $opt_key => $opt) {
            $opts[$opt_key] = array(
                'label' => $opt
            );
        }

        $field_array['options'] = $opts;
        $field_obj = FancyFormsFields::get_field_class($field_array['type'], $field_array);
        $field_obj->show_single_option();
        wp_die();
    }

    public static function field_selection() {
        $fancyforms_fields = array(
            'name' => array(
                'name' => esc_html__('Name', 'fancy-forms'),
                'icon' => 'fancyformsicon fancyformsicon-name',
            ),
            'email' => array(
                'name' => esc_html__('Email', 'fancy-forms'),
                'icon' => 'fancyformsicon fancyformsicon-email',
            ),
            'phone' => array(
                'name' => esc_html__('Phone', 'fancy-forms'),
                'icon' => 'fancyformsicon fancyformsicon-phone',
            ),
            'url' => array(
                'name' => esc_html__('Website/URL', 'fancy-forms'),
                'icon' => 'fancyformsicon fancyformsicon-url',
            ),
            'address' => array(
                'name' => esc_html__('Address', 'fancy-forms'),
                'icon' => 'fancyformsicon fancyformsicon-address',
            ),
            'text' => array(
                'name' => esc_html__('Text', 'fancy-forms'),
                'icon' => 'fancyformsicon fancyformsicon-text',
            ),
            'textarea' => array(
                'name' => esc_html__('Text Area', 'fancy-forms'),
                'icon' => 'fancyformsicon fancyformsicon-textarea',
            ),
            'select' => array(
                'name' => esc_html__('Dropdown', 'fancy-forms'),
                'icon' => 'fancyformsicon fancyformsicon-select',
            ),
            'checkbox' => array(
                'name' => esc_html__('Checkboxes', 'fancy-forms'),
                'icon' => 'fancyformsicon fancyformsicon-check',
            ),
            'radio' => array(
                'name' => esc_html__('Radio Buttons', 'fancy-forms'),
                'icon' => 'fancyformsicon fancyformsicon-radio',
            ),
            'image_select' => array(
                'name' => esc_html__('Image Selector', 'fancy-forms'),
                'icon' => 'fancyformsicon fancyformsicon-image-select',
            ),
            'number' => array(
                'name' => esc_html__('Number', 'fancy-forms'),
                'icon' => 'fancyformsicon fancyformsicon-number',
            ),
            'range_slider' => array(
                'name' => esc_html__('Range Slider', 'fancy-forms'),
                'icon' => 'fancyformsicon fancyformsicon-range-slider',
            ),
            'star' => array(
                'name' => esc_html__('Star', 'fancy-forms'),
                'icon' => 'fancyformsicon fancyformsicon-stars',
            ),
            'spinner' => array(
                'name' => esc_html__('Spinner', 'fancy-forms'),
                'icon' => 'fancyformsicon fancyformsicon-quantity',
            ),
            'date' => array(
                'name' => esc_html__('Date', 'fancy-forms'),
                'icon' => 'fancyformsicon fancyformsicon-date',
            ),
            'time' => array(
                'name' => esc_html__('Time', 'fancy-forms'),
                'icon' => 'fancyformsicon fancyformsicon-time',
            ),
            'upload' => array(
                'name' => esc_html__('Upload', 'fancy-forms'),
                'icon' => 'fancyformsicon fancyformsicon-upload',
            ),
            'user_id' => array(
                'name' => esc_html__('User ID', 'fancy-forms'),
                'icon' => 'fancyformsicon fancyformsicon-user-id',
            ),
            'hidden' => array(
                'name' => esc_html__('Hidden', 'fancy-forms'),
                'icon' => 'fancyformsicon fancyformsicon-hidden',
            ),
            'heading' => array(
                'name' => esc_html__('Heading', 'fancy-forms'),
                'icon' => 'fancyformsicon fancyformsicon-heading',
            ),
            'paragraph' => array(
                'name' => esc_html__('Paragraph', 'fancy-forms'),
                'icon' => 'fancyformsicon fancyformsicon-paragraph',
            ),
            'separator' => array(
                'name' => esc_html__('Separator', 'fancy-forms'),
                'icon' => 'fancyformsicon fancyformsicon-divider-dash',
            ),
            'spacer' => array(
                'name' => esc_html__('Spacer', 'fancy-forms'),
                'icon' => 'fancyformsicon fancyformsicon-spacer',
            ),
            'image' => array(
                'name' => esc_html__('Image', 'fancy-forms'),
                'icon' => 'fancyformsicon fancyformsicon-image',
            ),
            'html' => array(
                'name' => esc_html__('HTML', 'fancy-forms'),
                'icon' => 'fancyformsicon fancyformsicon-html',
            ),
            'captcha' => array(
                'name' => esc_html__('reCAPTCHA', 'fancy-forms'),
                'icon' => 'fancyformsicon fancyformsicon-recaptcha',
            )
        );
        return apply_filters('fancyforms_field_selection', $fancyforms_fields);
    }

    public static function create_row($values, $return = true) {
        global $wpdb, $fancyforms_duplicate_ids;

        $new_values = array();
        $key = isset($values['field_key']) ? sanitize_text_field($values['field_key']) : sanitize_text_field($values['name']);

        $new_values['field_key'] = sanitize_text_field(FancyFormsHelper::get_unique_key('fancyforms_fields', 'field_key'));
        $new_values['name'] = sanitize_text_field($values['name']);
        $new_values['description'] = sanitize_text_field($values['description']);
        $new_values['type'] = sanitize_text_field($values['type']);
        $new_values['field_order'] = isset($values['field_order']) ? absint($values['field_order']) : '';
        $new_values['required'] = $values['required'] ? true : false;
        $new_values['form_id'] = isset($values['form_id']) ? absint($values['form_id']) : '';
        $new_values['created_at'] = sanitize_text_field(current_time('mysql'));

        $new_values['options'] = is_array($values['options']) ? FancyFormsHelper::sanitize_array($values['options']) : sanitize_text_field($values['options']);

        $new_values['field_options'] = FancyFormsHelper::sanitize_array($values['field_options'], FancyFormsHelper::get_field_options_sanitize_rules());

        if (isset($values['default_value'])) {
            $field_obj = FancyFormsFields::get_field_class($new_values['type']);
            $new_values['default_value'] = $field_obj->sanitize_value($new_values['default_value']);
        }

        self::preserve_format_option_backslashes($new_values);

        foreach ($new_values as $key => $val) {
            if (is_array($val)) {
                $new_values[$key] = serialize($val);
            }
        }

        $query_results = $wpdb->insert($wpdb->prefix . 'fancyforms_fields', $new_values);
        $new_id = 0;
        if ($query_results) {
            $new_id = $wpdb->insert_id;
        }

        if (!$return) {
            return false;
        }

        if ($query_results) {
            if (isset($values['id'])) {
                $fancyforms_duplicate_ids[$values['id']] = $new_id;
            }
            return $new_id;
        } else {
            return false;
        }
    }

    public static function update_form_fields($id, $values) {
        global $wpdb;
        $all_fields = self::get_form_fields($id);

        foreach ($all_fields as $fid) {
            $field_id = absint($fid->id);
            if ($field_id && (isset($values['fancyforms-form-submitted']) && in_array($field_id, $values['fancyforms-form-submitted']))) {
                $values['edited'][] = $field_id;
            }

            $field_array[$field_id] = $fid;
        }

        if (isset($values['edited'])) {
            foreach ($values['edited'] as $field_id) {
                $default_field_cols = FancyFormsHelper::get_form_fields_default();

                if (isset($field_array[$field_id])) {
                    $field = $field_array[$field_id];
                } else {
                    $field = self::get_field_vars($field_id);
                }

                if (!$field) {
                    continue;
                }

                //updating the fields
                $field_obj = self::get_field_object($field);
                $update_options = $field_obj->get_default_field_options();
                foreach ($update_options as $opt => $default) {
                    $field->field_options[$opt] = isset($values['field_options'][$opt . '_' . absint($field_id)]) ? $values['field_options'][$opt . '_' . absint($field_id)] : $default;
                }

                $new_field = array(
                    'field_options' => $field->field_options,
                    'default_value' => isset($values['default_value_' . absint($field_id)]) ? $values['default_value_' . absint($field_id)] : '',
                );

                foreach ($default_field_cols as $col => $default) {
                    $default = ( $default === '' ) ? $field->{$col} : $default;
                    $new_field[$col] = isset($values['field_options'][$col . '_' . absint($field->id)]) ? $values['field_options'][$col . '_' . absint($field->id)] : $default;
                }

                if (is_array($new_field['options']) && isset($new_field['options']['000'])) {
                    unset($new_field['options']['000']);
                }

                self::update_fields($field_id, $new_field);
            }
        }
    }

    public static function update_fields($id, $values) {
        global $wpdb;

        $values['required'] = $values['required'] ? true : false;

        $values['options'] = serialize(is_array($values['options']) ? FancyFormsHelper::sanitize_array($values['options']) : sanitize_text_field($values['options']));

        $values['field_options'] = serialize(FancyFormsHelper::sanitize_array($values['field_options'], FancyFormsHelper::get_field_options_sanitize_rules()));

        if (isset($values['default_value'])) {
            $field_obj = FancyFormsFields::get_field_class($values['type']);
            $values['default_value'] = serialize($field_obj->sanitize_value($values['default_value']));
        }

        $query_results = $wpdb->update($wpdb->prefix . 'fancyforms_fields', $values, array('id' => $id));
        return $query_results;
    }

    public static function duplicate_fields($old_form_id, $form_id) {
        global $wpdb;

        $query = $wpdb->prepare("SELECT fancyformsicon.*, fancyforms.name AS form_name 
            FROM {$wpdb->prefix}fancyforms_fields fancyformsicon 
            LEFT OUTER JOIN {$wpdb->prefix}fancyforms_forms fancyforms 
            ON fancyformsicon.form_id = fancyforms.id 
            WHERE fancyformsicon.form_id=%d 
            ORDER BY 'field_order'", $old_form_id
        );
        $fields = $wpdb->get_results($query);

        foreach ((array) $fields as $field) {
            $values = array();
            self::fill_field($values, $field, $form_id);
            self::create_row($values);
        }
    }

    public static function fill_field(&$values, $field, $form_id) {
        global $wpdb;
        $values['field_key'] = FancyFormsHelper::get_unique_key('fancyforms_fields', 'field_key');
        $values['form_id'] = $form_id;
        $cols_array = array('name', 'description', 'type', 'field_order', 'field_options', 'options', 'default_value', 'required');
        foreach ($cols_array as $col) {
            $values[$col] = maybe_unserialize($field->{$col});
        }
    }

    private static function preserve_format_option_backslashes(&$values) {
        if (isset($values['field_options']['format'])) {
            $values['field_options']['format'] = self::preserve_backslashes($values['field_options']['format']);
        }
    }

    public static function preserve_backslashes($value) {
        // If backslashes have already been added, don't add them again
        if (strpos($value, '\\\\') === false) {
            $value = addslashes($value);
        }

        return $value;
    }

    public static function destroy_row($field_id) {
        global $wpdb;
        $field = self::get_field_vars($field_id);
        if (!$field) {
            return false;
        }

        $query = $wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'fancyforms_entry_meta WHERE field_id=%d', absint($field_id));
        $wpdb->query($query);

        $query = $wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'fancyforms_fields WHERE id=%d', absint($field_id));
        return $wpdb->query($query);
    }

    public static function get_field_vars($field_id) {
        if (empty($field_id))
            return;
        global $wpdb;
        $query = $wpdb->prepare('SELECT * FROM ' . $wpdb->prefix . 'fancyforms_fields WHERE id=%d', absint($field_id)); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
        $results = $wpdb->get_row($query);
        if (empty($results)) {
            return $results;
        }

        self::prepare_options($results);
        return wp_unslash($results);
    }

    private static function prepare_options(&$results) {
        $results->field_options = maybe_unserialize($results->field_options);
        $results->options = maybe_unserialize($results->options);
        $results->default_value = maybe_unserialize($results->default_value);
    }

    public static function get_option($field, $option) {
        return is_array($field) ? self::get_option_in_array($field, $option) : self::get_option_in_object($field, $option);
    }

    public static function get_option_in_array($field, $option) {
        if (isset($field[$option])) {
            $this_option = $field[$option];
        } elseif (isset($field['field_options']) && is_array($field['field_options']) && isset($field['field_options'][$option])) {
            $this_option = $field['field_options'][$option];
        } else {
            $this_option = '';
        }
        return $this_option;
    }

    public static function get_option_in_object($field, $option) {
        return isset($field->field_options[$option]) ? $field->field_options[$option] : '';
    }

    public static function get_error_msg($field, $error) {
        $field_name = $field->name ? $field->name : '';
        $max_length = intval(FancyFormsFields::get_option($field, 'max'));

        $defaults = array(
            'invalid' => sprintf(esc_html__('%s is invalid.', 'fancy-forms'), $field_name),
            'blank' => sprintf(esc_html__('%s is required.', 'fancy-forms'), $field_name),
            'max_char' => sprintf(esc_html__('%s characters only allowed.', 'fancy-forms'), $max_length),
        );
        $msg = FancyFormsFields::get_option($field, $error);
        $msg = empty($msg) ? $defaults[$error] : $msg;
        return $msg;
    }

    public static function get_field_object($field) {
        if (!is_object($field)) {
            $field = self::get_field_vars($field);
        }
        return self::get_field_class($field->type, $field);
    }

    public static function get_field_class($field_type, $field = 0) {
        $class = self::get_field_type_class($field_type);
        $field_obj = new $class($field, $field_type);
        return $field_obj;
    }

    private static function get_field_type_class($field_type = '') {
        $type_classes = apply_filters('fancyforms_field_type_class', array(
            'text' => 'FancyFormsFieldText',
            'textarea' => 'FancyFormsFieldTextarea',
            'select' => 'FancyFormsFieldSelect',
            'radio' => 'FancyFormsFieldRadio',
            'checkbox' => 'FancyFormsFieldCheckbox',
            'image_select' => 'FancyFormsFieldImageSelect',
            'number' => 'FancyFormsFieldNumber',
            'phone' => 'FancyFormsFieldPhone',
            'url' => 'FancyFormsFieldUrl',
            'email' => 'FancyFormsFieldEmail',
            'user_id' => 'FancyFormsFieldUserID',
            'html' => 'FancyFormsFieldHTML',
            'hidden' => 'FancyFormsFieldHidden',
            'captcha' => 'FancyFormsFieldCaptcha',
            'name' => 'FancyFormsFieldName',
            'heading' => 'FancyFormsFieldHeading',
            'paragraph' => 'FancyFormsFieldParagraph',
            'image' => 'FancyFormsFieldImage',
            'spacer' => 'FancyFormsFieldSpacer',
            'range_slider' => 'FancyFormsFieldRangeSlider',
            'address' => 'FancyFormsFieldAddress',
            'star' => 'FancyFormsFieldStar',
            'separator' => 'FancyFormsFieldSeparator',
            'spinner' => 'FancyFormsFieldSpinner',
            'date' => 'FancyFormsFieldDate',
            'time' => 'FancyFormsFieldTime',
            'upload' => 'FancyFormsFieldUpload',
        ));
        if ($field_type) {
            return isset($type_classes[$field_type]) ? $type_classes[$field_type] : '';
        } else {
            return $type_classes;
        }
    }

    public static function include_field_class() {
        $classes = self::get_field_type_class();
        include FANCYFORMS_PATH . 'admin/classes/fields/FancyFormsFieldType.php';
        foreach ($classes as $class) {
            if (file_exists(FANCYFORMS_PATH . 'admin/classes/fields/' . $class . '.php')) {
                include FANCYFORMS_PATH . 'admin/classes/fields/' . $class . '.php';
            }
        }
        do_action('fancyforms_include_field_class');
    }

    public static function show_fields($fields) {
        foreach ($fields as $field) {
            $field_obj = FancyFormsFields::get_field_class($field['type'], $field);
            $field_obj->show_field();
        }
    }

}

new FancyFormsFields();
