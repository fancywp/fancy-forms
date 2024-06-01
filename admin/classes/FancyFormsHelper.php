<?php
defined('ABSPATH') || die();

class FancyFormsHelper {

    public static function get_fields_array($form_id) {
        $fields = FancyFormsFields::get_form_fields($form_id);

        $values['fields'] = array();

        if (empty($fields))
            return $values;

        foreach ((array) $fields as $field) {
            $field_array = FancyFormsFields::covert_field_obj_to_array($field);
            $values['fields'][] = $field_array;
        }

        $form_options_defaults = self::get_form_options_default();

        return array_merge($form_options_defaults, $values);
    }

    /* Sanitizes value and returns param value */

    public static function get_var($param, $sanitize = 'sanitize_text_field', $default = '') {
        $value = (($_GET && isset($_GET[$param])) ? wp_unslash($_GET[$param]) : $default);
        return self::sanitize_value($sanitize, $value);
    }

    public static function get_post($param, $sanitize = 'sanitize_text_field', $default = '', $sanitize_array = array()) {
        $value = (isset($_POST[$param]) ? wp_unslash($_POST[$param]) : $default);
        if (!empty($sanitize_array) && is_array($value)) {
            return self::sanitize_array($value, $sanitize_array);
        }
        return self::sanitize_value($sanitize, $value);
    }

    public static function sanitize_value($sanitize, &$value) {
        if (!empty($sanitize)) {
            if (is_array($value)) {
                $temp_values = $value;
                foreach ($temp_values as $k => $v) {
                    $value[$k] = self::sanitize_value($sanitize, $value[$k]);
                }
            } else {
                $value = call_user_func($sanitize, ($value ? htmlspecialchars_decode($value) : ''));
            }
        }

        return $value;
    }

    public static function get_unique_key($table_name, $column_name, $limit = 6) {
        $values = 'ABCDEFGHIJKLMOPQRSTUVXWYZ0123456789';
        $count = strlen($values);
        $count--;
        $key = '';
        for ($x = 1; $x <= $limit; $x++) {
            $rand_var = rand(0, $count);
            $key .= substr($values, $rand_var, 1);
        }

        $key = strtolower($key);
        $existing_keys = self::check_table_keys($table_name, $column_name);

        if (in_array($key, $existing_keys)) {
            self::get_unique_key($table_name, $column_name, $limit = 6);
        }

        return $key;
    }

    public static function check_table_keys($table_name, $column_name) {
        global $wpdb;
        $tbl_name = $wpdb->prefix . $table_name;
        $query = $wpdb->prepare("SELECT {$column_name} FROM {$tbl_name} WHERE id!=%d", 0);
        $results = $wpdb->get_results($query, ARRAY_A);
        return array_column($results, $column_name);
    }

    public static function is_admin_page($page = 'fancyforms') {
        $get_page = self::get_var('page', 'sanitize_title');
        if (is_admin() && $get_page === $page) {
            return true;
        }

        return false;
    }

    public static function is_preview_page() {
        $action = self::get_var('action', 'sanitize_title');
        return (is_admin() && ( $action == 'fancyforms_preview'));
    }

    public static function is_form_builder_page() {
        $action = self::get_var('fancyforms_action', 'sanitize_title');
        $builder_actions = self::get_form_builder_actions();
        return self::is_admin_page('fancyforms') && ( in_array($action, $builder_actions) );
    }

    public static function is_form_listing_page() {
        if (!self::is_admin_page('fancyforms')) {
            return false;
        }

        $action = self::get_var('fancyforms_action', 'sanitize_title');
        $builder_actions = self::get_form_builder_actions();
        return !$action || in_array($action, $builder_actions);
    }

    public static function get_form_builder_actions() {
        return array('edit', 'settings', 'style');
    }

    public static function start_field_array($field) {
        return array(
            'id' => $field->id,
            'default_value' => $field->default_value,
            'name' => $field->name,
            'description' => $field->description,
            'options' => $field->options,
            'required' => $field->required,
            'field_key' => $field->field_key,
            'field_order' => $field->field_order,
            'form_id' => $field->form_id,
        );
    }

    public static function show_search_box($atts) {
        $defaults = array(
            'placeholder' => '',
            'tosearch' => '',
            'text' => esc_html__('Search', 'fancy-forms'),
            'input_id' => '',
        );
        $atts = array_merge($defaults, $atts);
        $class = 'fancyforms-search-fields-input';
        $input_id = $atts['input_id'] . '-search-input';
        ?>
        <div class="fancyforms-search-fields">
            <span class="mdi mdi-magnify"></span>
            <input type="search" id="<?php echo esc_attr($input_id); ?>" name="s" value="<?php _admin_search_query(); ?>" placeholder="<?php echo esc_attr($atts['placeholder']); ?>" class="<?php echo esc_attr($class); ?>" data-tosearch="<?php echo esc_attr($atts['tosearch']); ?>" <?php if (!empty($atts['tosearch'])) { ?> autocomplete="off"<?php } ?> />
            <?php if (empty($atts['tosearch'])) submit_button($atts['text'], 'button-secondary', '', false, array('id' => 'search-submit')); ?>
        </div>
        <?php
    }

    public static function convert_date_format($date) {
        $timestamp = strtotime($date);

        $new_date = date('Y/m/d', $timestamp);
        $new_time = date('g:i a', $timestamp);

        return $new_date . ' ' . esc_html__('at', 'fancy-forms') . ' ' . $new_time;
    }

    public static function parse_json_array($array = array()) {
        $array = json_decode($array, true);
        $fields = array();
        foreach ($array as $val) {
            $name = $val['name'];
            $value = $val['value'];
            if (strpos($name, '[]') !== false) {
                $fields[str_replace('[]', '', $name)][] = $value;
            } else if (strpos($name, '[') !== false) {
                $ids = explode('[', str_replace(']', '', $name));
                $count = count($ids);

                switch ($count):
                    case 1:
                        $fields[$ids[0]] = $value;
                        break;
                    case 2:
                        $fields[$ids[0]][$ids[1]] = $value;
                        break;
                    case 3:
                        $fields[$ids[0]][$ids[1]][$ids[2]] = $value;
                        break;
                    case 4:
                        $fields[$ids[0]][$ids[1]][$ids[2]][$ids[3]] = $value;
                        break;
                    case 5:
                        $fields[$ids[0]][$ids[1]][$ids[2]][$ids[3]][$ids[4]] = $value;
                        break;
                endswitch;
            }else {
                $fields[$name] = $value;
            }
        }
        return $fields;
    }

    public static function process_form_array($form) {
        if (!$form) {
            return;
        }

        $new_values = array(
            'id' => $form->id,
            'form_key' => $form->form_key,
            'name' => $form->name,
            'description' => $form->description,
            'status' => $form->status,
        );

        if (is_array($form->options)) {
            $form_options = wp_parse_args($form->options, self::get_form_options_default());

            foreach ($form_options as $opt => $value) {
                $new_values[$opt] = $value;
            }
        }

        return $new_values;
    }

    public static function recursive_parse_args($args, $defaults) {
        $new_args = (array) $defaults;
        foreach ($args as $key => $value) {
            if (is_array($value) && isset($new_args[$key])) {
                $new_args[$key] = self::recursive_parse_args($value, $new_args[$key]);
            } else {
                $new_args[$key] = $value;
            }
        }
        return $new_args;
    }

    public static function get_form_options_checkbox_settings() {
        return array(
            'show_title' => 'off',
            'show_description' => 'off',
        );
    }

    public static function get_form_settings_checkbox_settings() {
        return array(
            'enable_ar' => 'off',
        );
    }

    public static function get_form_options_default() {
        return array(
            'show_title' => 'on',
            'show_description' => 'off',
            'title' => '',
            'description' => '',
            'submit_value' => esc_html__('Submit', 'fancy-forms'),
            'form_css_class' => '',
            'submit_btn_css_class' => '',
            'submit_btn_alignment' => 'left',
        );
    }

    public static function get_form_settings_default($name = '') {
        $return = array(
            'email_to' => '[admin_email]',
            'email_from' => '[admin_email]',
            'reply_to_email' => '',
            'email_from_name' => get_bloginfo('name'),
            'email_subject' => esc_html__('New Entry: ', 'fancy-forms') . esc_html($name),
            'email_message' => '#form_details',
            'enable_ar' => 'off',
            'from_ar' => '[admin_email]',
            'from_ar_name' => get_bloginfo('name'),
            'reply_to_ar' => '',
            'email_subject_ar' => esc_html__('Entry Submitted: ', 'fancy-forms') . esc_html($name),
            'email_message_ar' => esc_html__('Thank you for sending email. We will get back to you as soon as possible.', 'fancy-forms'),
            'confirmation_type' => 'show_message',
            'confirmation_message' => esc_html__('Form Submitted Successfully', 'fancy-forms'),
            'error_message' => esc_html__('Sorry, An error Occurred! Your form cannot be submitted.', 'fancy-forms'),
            'show_page_id' => '',
            'redirect_url_page' => '',
        );
        return apply_filters('fancyforms_form_settings_default', $return);
    }

    public static function get_form_styles_default() {
        return array(
            'form_style' => '',
        );
    }

    public static function get_form_options_sanitize_rules() {
        return array(
            'show_title' => 'fancyforms_sanitize_checkbox',
            'show_description' => 'fancyforms_sanitize_checkbox',
            'title' => 'sanitize_text_field',
            'description' => 'sanitize_text_field',
            'submit_value' => 'sanitize_text_field',
            'form_css_class' => 'sanitize_text_field',
            'submit_btn_css_class' => 'sanitize_text_field',
            'submit_btn_alignment' => 'sanitize_text_field',
        );
    }

    public static function get_form_settings_sanitize_rules() {
        $return = array(
            'email_to' => 'sanitize_text_field',
            'email_from' => 'sanitize_text_field',
            'reply_to_email' => 'sanitize_text_field',
            'email_from_name' => 'sanitize_text_field',
            'email_subject' => 'sanitize_text_field',
            'email_message' => 'sanitize_text_field',
            'enable_ar' => 'fancyforms_sanitize_checkbox',
            'from_ar' => 'sanitize_text_field',
            'from_ar_name' => 'sanitize_text_field',
            'reply_to_ar' => 'sanitize_text_field',
            'email_subject_ar' => 'sanitize_text_field',
            'email_message_ar' => 'sanitize_text_field',
            'confirmation_type' => 'sanitize_text_field',
            'confirmation_message' => 'sanitize_text_field',
            'error_message' => 'sanitize_text_field',
            'show_page_id' => 'sanitize_text_field',
            'redirect_url_page' => 'sanitize_url',
            'condition_action' => array(
                'sanitize_text_field'
            ),
            'compare_from' => array(
                'sanitize_text_field'
            ),
            'compare_to' => array(
                'sanitize_text_field'
            ),
            'compare_condition' => array(
                'sanitize_text_field'
            ),
            'compare_value' => array(
                'sanitize_text_field'
            )
        );
        return apply_filters('fancyforms_settings_sanitize_rules', $return);
    }

    public static function get_form_styles_sanitize_rules() {
        return array(
            'form_style' => 'sanitize_text_field',
            'form_style_template' => 'absint'
        );
    }

    public static function get_form_fields_default() {
        return array(
            'field_order' => 0,
            'field_key' => '',
            'required' => false,
            'type' => '',
            'description' => '',
            'options' => '',
            'name' => '',
        );
    }

    public static function get_countries() {
        $countries = array(
            esc_html__('Afghanistan', 'fancy-forms'),
            esc_html__('Aland Islands', 'fancy-forms'),
            esc_html__('Albania', 'fancy-forms'),
            esc_html__('Algeria', 'fancy-forms'),
            esc_html__('American Samoa', 'fancy-forms'),
            esc_html__('Andorra', 'fancy-forms'),
            esc_html__('Angola', 'fancy-forms'),
            esc_html__('Anguilla', 'fancy-forms'),
            esc_html__('Antarctica', 'fancy-forms'),
            esc_html__('Antigua and Barbuda', 'fancy-forms'),
            esc_html__('Argentina', 'fancy-forms'),
            esc_html__('Armenia', 'fancy-forms'),
            esc_html__('Aruba', 'fancy-forms'),
            esc_html__('Australia', 'fancy-forms'),
            esc_html__('Austria', 'fancy-forms'),
            esc_html__('Azerbaijan', 'fancy-forms'),
            esc_html__('Bahamas', 'fancy-forms'),
            esc_html__('Bahrain', 'fancy-forms'),
            esc_html__('Bangladesh', 'fancy-forms'),
            esc_html__('Barbados', 'fancy-forms'),
            esc_html__('Belarus', 'fancy-forms'),
            esc_html__('Belgium', 'fancy-forms'),
            esc_html__('Belize', 'fancy-forms'),
            esc_html__('Benin', 'fancy-forms'),
            esc_html__('Bermuda', 'fancy-forms'),
            esc_html__('Bhutan', 'fancy-forms'),
            esc_html__('Bolivia', 'fancy-forms'),
            esc_html__('Bonaire, Sint Eustatius and Saba', 'fancy-forms'),
            esc_html__('Bosnia and Herzegovina', 'fancy-forms'),
            esc_html__('Botswana', 'fancy-forms'),
            esc_html__('Bouvet Island', 'fancy-forms'),
            esc_html__('Brazil', 'fancy-forms'),
            esc_html__('British Indian Ocean Territory', 'fancy-forms'),
            esc_html__('Brunei', 'fancy-forms'),
            esc_html__('Bulgaria', 'fancy-forms'),
            esc_html__('Burkina Faso', 'fancy-forms'),
            esc_html__('Burundi', 'fancy-forms'),
            esc_html__('Cambodia', 'fancy-forms'),
            esc_html__('Cameroon', 'fancy-forms'),
            esc_html__('Canada', 'fancy-forms'),
            esc_html__('Cape Verde', 'fancy-forms'),
            esc_html__('Cayman Islands', 'fancy-forms'),
            esc_html__('Central African Republic', 'fancy-forms'),
            esc_html__('Chad', 'fancy-forms'),
            esc_html__('Chile', 'fancy-forms'),
            esc_html__('China', 'fancy-forms'),
            esc_html__('Christmas Island', 'fancy-forms'),
            esc_html__('Cocos (Keeling) Islands', 'fancy-forms'),
            esc_html__('Colombia', 'fancy-forms'),
            esc_html__('Comoros', 'fancy-forms'),
            esc_html__('Congo', 'fancy-forms'),
            esc_html__('Cook Islands', 'fancy-forms'),
            esc_html__('Costa Rica', 'fancy-forms'),
            esc_html__('C&ocirc;te d\'Ivoire', 'fancy-forms'),
            esc_html__('Croatia', 'fancy-forms'),
            esc_html__('Cuba', 'fancy-forms'),
            esc_html__('Curacao', 'fancy-forms'),
            esc_html__('Cyprus', 'fancy-forms'),
            esc_html__('Czech Republic', 'fancy-forms'),
            esc_html__('Denmark', 'fancy-forms'),
            esc_html__('Djibouti', 'fancy-forms'),
            esc_html__('Dominica', 'fancy-forms'),
            esc_html__('Dominican Republic', 'fancy-forms'),
            esc_html__('East Timor', 'fancy-forms'),
            esc_html__('Ecuador', 'fancy-forms'),
            esc_html__('Egypt', 'fancy-forms'),
            esc_html__('El Salvador', 'fancy-forms'),
            esc_html__('Equatorial Guinea', 'fancy-forms'),
            esc_html__('Eritrea', 'fancy-forms'),
            esc_html__('Estonia', 'fancy-forms'),
            esc_html__('Ethiopia', 'fancy-forms'),
            esc_html__('Falkland Islands (Malvinas)', 'fancy-forms'),
            esc_html__('Faroe Islands', 'fancy-forms'),
            esc_html__('Fiji', 'fancy-forms'),
            esc_html__('Finland', 'fancy-forms'),
            esc_html__('France', 'fancy-forms'),
            esc_html__('French Guiana', 'fancy-forms'),
            esc_html__('French Polynesia', 'fancy-forms'),
            esc_html__('French Southern Territories', 'fancy-forms'),
            esc_html__('Gabon', 'fancy-forms'),
            esc_html__('Gambia', 'fancy-forms'),
            esc_html__('Georgia', 'fancy-forms'),
            esc_html__('Germany', 'fancy-forms'),
            esc_html__('Ghana', 'fancy-forms'),
            esc_html__('Gibraltar', 'fancy-forms'),
            esc_html__('Greece', 'fancy-forms'),
            esc_html__('Greenland', 'fancy-forms'),
            esc_html__('Grenada', 'fancy-forms'),
            esc_html__('Guadeloupe', 'fancy-forms'),
            esc_html__('Guam', 'fancy-forms'),
            esc_html__('Guatemala', 'fancy-forms'),
            esc_html__('Guernsey', 'fancy-forms'),
            esc_html__('Guinea', 'fancy-forms'),
            esc_html__('Guinea-Bissau', 'fancy-forms'),
            esc_html__('Guyana', 'fancy-forms'),
            esc_html__('Haiti', 'fancy-forms'),
            esc_html__('Heard Island and McDonald Islands', 'fancy-forms'),
            esc_html__('Holy See', 'fancy-forms'),
            esc_html__('Honduras', 'fancy-forms'),
            esc_html__('Hong Kong', 'fancy-forms'),
            esc_html__('Hungary', 'fancy-forms'),
            esc_html__('Iceland', 'fancy-forms'),
            esc_html__('India', 'fancy-forms'),
            esc_html__('Indonesia', 'fancy-forms'),
            esc_html__('Iran', 'fancy-forms'),
            esc_html__('Iraq', 'fancy-forms'),
            esc_html__('Ireland', 'fancy-forms'),
            esc_html__('Israel', 'fancy-forms'),
            esc_html__('Isle of Man', 'fancy-forms'),
            esc_html__('Italy', 'fancy-forms'),
            esc_html__('Jamaica', 'fancy-forms'),
            esc_html__('Japan', 'fancy-forms'),
            esc_html__('Jersey', 'fancy-forms'),
            esc_html__('Jordan', 'fancy-forms'),
            esc_html__('Kazakhstan', 'fancy-forms'),
            esc_html__('Kenya', 'fancy-forms'),
            esc_html__('Kiribati', 'fancy-forms'),
            esc_html__('North Korea', 'fancy-forms'),
            esc_html__('South Korea', 'fancy-forms'),
            esc_html__('Kosovo', 'fancy-forms'),
            esc_html__('Kuwait', 'fancy-forms'),
            esc_html__('Kyrgyzstan', 'fancy-forms'),
            esc_html__('Laos', 'fancy-forms'),
            esc_html__('Latvia', 'fancy-forms'),
            esc_html__('Lebanon', 'fancy-forms'),
            esc_html__('Lesotho', 'fancy-forms'),
            esc_html__('Liberia', 'fancy-forms'),
            esc_html__('Libya', 'fancy-forms'),
            esc_html__('Liechtenstein', 'fancy-forms'),
            esc_html__('Lithuania', 'fancy-forms'),
            esc_html__('Luxembourg', 'fancy-forms'),
            esc_html__('Macao', 'fancy-forms'),
            esc_html__('Macedonia', 'fancy-forms'),
            esc_html__('Madagascar', 'fancy-forms'),
            esc_html__('Malawi', 'fancy-forms'),
            esc_html__('Malaysia', 'fancy-forms'),
            esc_html__('Maldives', 'fancy-forms'),
            esc_html__('Mali', 'fancy-forms'),
            esc_html__('Malta', 'fancy-forms'),
            esc_html__('Marshall Islands', 'fancy-forms'),
            esc_html__('Martinique', 'fancy-forms'),
            esc_html__('Mauritania', 'fancy-forms'),
            esc_html__('Mauritius', 'fancy-forms'),
            esc_html__('Mayotte', 'fancy-forms'),
            esc_html__('Mexico', 'fancy-forms'),
            esc_html__('Micronesia', 'fancy-forms'),
            esc_html__('Moldova', 'fancy-forms'),
            esc_html__('Monaco', 'fancy-forms'),
            esc_html__('Mongolia', 'fancy-forms'),
            esc_html__('Montenegro', 'fancy-forms'),
            esc_html__('Montserrat', 'fancy-forms'),
            esc_html__('Morocco', 'fancy-forms'),
            esc_html__('Mozambique', 'fancy-forms'),
            esc_html__('Myanmar', 'fancy-forms'),
            esc_html__('Namibia', 'fancy-forms'),
            esc_html__('Nauru', 'fancy-forms'),
            esc_html__('Nepal', 'fancy-forms'),
            esc_html__('Netherlands', 'fancy-forms'),
            esc_html__('New Caledonia', 'fancy-forms'),
            esc_html__('New Zealand', 'fancy-forms'),
            esc_html__('Nicaragua', 'fancy-forms'),
            esc_html__('Niger', 'fancy-forms'),
            esc_html__('Nigeria', 'fancy-forms'),
            esc_html__('Niue', 'fancy-forms'),
            esc_html__('Norfolk Island', 'fancy-forms'),
            esc_html__('Northern Mariana Islands', 'fancy-forms'),
            esc_html__('Norway', 'fancy-forms'),
            esc_html__('Oman', 'fancy-forms'),
            esc_html__('Pakistan', 'fancy-forms'),
            esc_html__('Palau', 'fancy-forms'),
            esc_html__('Palestine', 'fancy-forms'),
            esc_html__('Panama', 'fancy-forms'),
            esc_html__('Papua New Guinea', 'fancy-forms'),
            esc_html__('Paraguay', 'fancy-forms'),
            esc_html__('Peru', 'fancy-forms'),
            esc_html__('Philippines', 'fancy-forms'),
            esc_html__('Pitcairn', 'fancy-forms'),
            esc_html__('Poland', 'fancy-forms'),
            esc_html__('Portugal', 'fancy-forms'),
            esc_html__('Puerto Rico', 'fancy-forms'),
            esc_html__('Qatar', 'fancy-forms'),
            esc_html__('Reunion', 'fancy-forms'),
            esc_html__('Romania', 'fancy-forms'),
            esc_html__('Russia', 'fancy-forms'),
            esc_html__('Rwanda', 'fancy-forms'),
            esc_html__('Saint Barthelemy', 'fancy-forms'),
            esc_html__('Saint Helena, Ascension and Tristan da Cunha', 'fancy-forms'),
            esc_html__('Saint Kitts and Nevis', 'fancy-forms'),
            esc_html__('Saint Lucia', 'fancy-forms'),
            esc_html__('Saint Martin (French part)', 'fancy-forms'),
            esc_html__('Saint Pierre and Miquelon', 'fancy-forms'),
            esc_html__('Saint Vincent and the Grenadines', 'fancy-forms'),
            esc_html__('Samoa', 'fancy-forms'),
            esc_html__('San Marino', 'fancy-forms'),
            esc_html__('Sao Tome and Principe', 'fancy-forms'),
            esc_html__('Saudi Arabia', 'fancy-forms'),
            esc_html__('Senegal', 'fancy-forms'),
            esc_html__('Serbia', 'fancy-forms'),
            esc_html__('Seychelles', 'fancy-forms'),
            esc_html__('Sierra Leone', 'fancy-forms'),
            esc_html__('Singapore', 'fancy-forms'),
            esc_html__('Sint Maarten (Dutch part)', 'fancy-forms'),
            esc_html__('Slovakia', 'fancy-forms'),
            esc_html__('Slovenia', 'fancy-forms'),
            esc_html__('Solomon Islands', 'fancy-forms'),
            esc_html__('Somalia', 'fancy-forms'),
            esc_html__('South Africa', 'fancy-forms'),
            esc_html__('South Georgia and the South Sandwich Islands', 'fancy-forms'),
            esc_html__('South Sudan', 'fancy-forms'),
            esc_html__('Spain', 'fancy-forms'),
            esc_html__('Sri Lanka', 'fancy-forms'),
            esc_html__('Sudan', 'fancy-forms'),
            esc_html__('Suriname', 'fancy-forms'),
            esc_html__('Svalbard and Jan Mayen', 'fancy-forms'),
            esc_html__('Swaziland', 'fancy-forms'),
            esc_html__('Sweden', 'fancy-forms'),
            esc_html__('Switzerland', 'fancy-forms'),
            esc_html__('Syria', 'fancy-forms'),
            esc_html__('Taiwan', 'fancy-forms'),
            esc_html__('Tajikistan', 'fancy-forms'),
            esc_html__('Tanzania', 'fancy-forms'),
            esc_html__('Thailand', 'fancy-forms'),
            esc_html__('Timor-Leste', 'fancy-forms'),
            esc_html__('Togo', 'fancy-forms'),
            esc_html__('Tokelau', 'fancy-forms'),
            esc_html__('Tonga', 'fancy-forms'),
            esc_html__('Trinidad and Tobago', 'fancy-forms'),
            esc_html__('Tunisia', 'fancy-forms'),
            esc_html__('Turkey', 'fancy-forms'),
            esc_html__('Turkmenistan', 'fancy-forms'),
            esc_html__('Turks and Caicos Islands', 'fancy-forms'),
            esc_html__('Tuvalu', 'fancy-forms'),
            esc_html__('Uganda', 'fancy-forms'),
            esc_html__('Ukraine', 'fancy-forms'),
            esc_html__('United Arab Emirates', 'fancy-forms'),
            esc_html__('United Kingdom', 'fancy-forms'),
            esc_html__('United States', 'fancy-forms'),
            esc_html__('United States Minor Outlying Islands', 'fancy-forms'),
            esc_html__('Uruguay', 'fancy-forms'),
            esc_html__('Uzbekistan', 'fancy-forms'),
            esc_html__('Vanuatu', 'fancy-forms'),
            esc_html__('Vatican City', 'fancy-forms'),
            esc_html__('Venezuela', 'fancy-forms'),
            esc_html__('Vietnam', 'fancy-forms'),
            esc_html__('Virgin Islands, British', 'fancy-forms'),
            esc_html__('Virgin Islands, U.S.', 'fancy-forms'),
            esc_html__('Wallis and Futuna', 'fancy-forms'),
            esc_html__('Western Sahara', 'fancy-forms'),
            esc_html__('Yemen', 'fancy-forms'),
            esc_html__('Zambia', 'fancy-forms'),
            esc_html__('Zimbabwe', 'fancy-forms'),
        );

        sort($countries, SORT_LOCALE_STRING);
        return $countries;
    }

    public static function get_ages() {
        return array(
            esc_html__('Under 18', 'fancy-forms'),
            esc_html__('18-24', 'fancy-forms'),
            esc_html__('25-34', 'fancy-forms'),
            esc_html__('35-44', 'fancy-forms'),
            esc_html__('45-54', 'fancy-forms'),
            esc_html__('55-64', 'fancy-forms'),
            esc_html__('65 or Above', 'fancy-forms'),
            esc_html__('Prefer Not to Answer', 'fancy-forms'),
        );
    }

    public static function get_satisfaction() {
        return array(
            esc_html__('Very Unsatisfied', 'fancy-forms'),
            esc_html__('Unsatisfied', 'fancy-forms'),
            esc_html__('Neutral', 'fancy-forms'),
            esc_html__('Satisfied', 'fancy-forms'),
            esc_html__('Very Satisfied', 'fancy-forms'),
            esc_html__('N/A', 'fancy-forms'),
        );
    }

    public static function get_agreement() {
        return array(
            esc_html__('Strongly Disagree', 'fancy-forms'),
            esc_html__('Disagree', 'fancy-forms'),
            esc_html__('Neutral', 'fancy-forms'),
            esc_html__('Agree', 'fancy-forms'),
            esc_html__('Strongly Agree', 'fancy-forms'),
            esc_html__('N/A', 'fancy-forms'),
        );
    }

    public static function get_likely() {
        return array(
            esc_html__('Extremely Unlikely', 'fancy-forms'),
            esc_html__('Unlikely', 'fancy-forms'),
            esc_html__('Neutral', 'fancy-forms'),
            esc_html__('Likely', 'fancy-forms'),
            esc_html__('Extremely Likely', 'fancy-forms'),
            esc_html__('N/A', 'fancy-forms'),
        );
    }

    public static function get_importance() {
        return array(
            esc_html__('Not at all Important', 'fancy-forms'),
            esc_html__('Somewhat Important', 'fancy-forms'),
            esc_html__('Neutral', 'fancy-forms'),
            esc_html__('Important', 'fancy-forms'),
            esc_html__('Very Important', 'fancy-forms'),
            esc_html__('N/A', 'fancy-forms'),
        );
    }

    public static function get_options_presets() {
        return array(
            'fancyforms-countries-opts' => array(
                'label' => esc_html__('Countries', 'fancy-forms'),
                'options' => self::get_countries()
            ),
            'fancyforms-age-opts' => array(
                'label' => esc_html__('Age', 'fancy-forms'),
                'options' => self::get_ages()
            ),
            'fancyforms-satisfaction-opts' => array(
                'label' => esc_html__('Satisfaction', 'fancy-forms'),
                'options' => self::get_satisfaction()
            ),
            'fancyforms-importance-opts' => array(
                'label' => esc_html__('Importance', 'fancy-forms'),
                'options' => self::get_importance()
            ),
            'fancyforms-agreement-opts' => array(
                'label' => esc_html__('Agreement', 'fancy-forms'),
                'options' => self::get_agreement()
            ),
            'fancyforms-likely-opts' => array(
                'label' => esc_html__('Likely', 'fancy-forms'),
                'options' => self::get_likely()
            ),
        );
    }

    public static function get_user_id_param($user_id) {
        if (!$user_id || is_numeric($user_id)) {
            return $user_id;
        }
        $user_id = sanitize_text_field($user_id);
        if ($user_id == 'current') {
            $user_id = get_current_user_id();
        } else {
            if (is_email($user_id)) {
                $user = get_user_by('email', $user_id);
            } else {
                $user = get_user_by('login', $user_id);
            }
            if ($user) {
                $user_id = $user->ID;
            }
            unset($user);
        }
        return $user_id;
    }

    public static function get_ip() {
        $ip = self::get_ip_address();
        return $ip;
    }

    public static function get_ip_address() {
        $ip_options = array('REMOTE_ADDR');
        $ip = '';

        foreach ($ip_options as $key) {
            if (!isset($_SERVER[$key])) {
                continue;
            }
            $key = self::get_server_value($key);
            foreach (explode(',', $key) as $ip) {
                $ip = trim($ip); // Just to be safe.
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                    return sanitize_text_field($ip);
                }
            }
        }
        return sanitize_text_field($ip);
    }

    public static function get_server_value($value) {
        return isset($_SERVER[$value]) ? sanitize_text_field(wp_strip_all_tags(wp_unslash($_SERVER[$value]))) : '';
    }

    public static function count_decimals($num) {
        if (!is_numeric($num)) {
            return false;
        }
        $num = (string) $num;
        $parts = explode('.', $num);
        if (1 === count($parts)) {
            return 0;
        }
        return strlen($parts[count($parts) - 1]);
    }

    public static function print_message() {
        if (isset($_SESSION['fancyforms_message'])) {
            ?>
            <div class="fancyforms-settings-updated">
                <span class="mdi mdi-check-circle"></span>
                <?php
                echo esc_html(sanitize_text_field($_SESSION['fancyforms_message']));
                unset($_SESSION['fancyforms_message']);
                ?>
            </div>
            <?php
        }
    }

    public static function sanitize_array($array = array(), $sanitize_rule = array()) {
        $new_args = (array) $array;

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $new_args[$key] = self::sanitize_array($value, isset($sanitize_rule[$key]) ? $sanitize_rule[$key] : 'sanitize_text_field');
            } else {
                if (isset($sanitize_rule[$key]) && !empty($sanitize_rule[$key]) && function_exists($sanitize_rule[$key])) {
                    $sanitize_type = $sanitize_rule[$key];
                    $new_args[$key] = $sanitize_type($value);
                } else {
                    $new_args[$key] = sanitize_text_field($value);
                }
            }
        }

        return $new_args;
    }

    public static function get_field_options_sanitize_rules() {
        return array(
            'grid_id' => 'sanitize_text_field',
            'name' => 'sanitize_text_field',
            'label' => 'sanitize_text_field',
            'label_position' => 'sanitize_text_field',
            'label_alignment' => 'sanitize_text_field',
            'hide_label' => 'fancyforms_sanitize_checkbox_boolean',
            'heading_type' => 'sanitize_text_field',
            'text_alignment' => 'sanitize_text_field',
            'content' => 'sanitize_text_field',
            'select_option_type' => 'sanitize_text_field',
            'image_size' => 'sanitize_text_field',
            'image_id' => 'fancyforms_sanitize_number',
            'spacer_height' => 'fancyforms_sanitize_number',
            'step' => 'fancyforms_sanitize_float',
            'min_time' => 'sanitize_text_field',
            'max_time' => 'sanitize_text_field',
            'upload_label' => 'sanitize_text_field',
            'max_upload_size' => 'fancyforms_sanitize_number',
            'extensions' => 'fancyforms_sanitize_allowed_file_extensions',
            'extensions_error_message' => 'sanitize_text_field',
            'multiple_uploads' => 'sanitize_text_field',
            'multiple_uploads_limit' => 'fancyforms_sanitize_number',
            'multiple_uploads_error_message' => 'sanitize_text_field',
            'date_format' => 'sanitize_text_field',
            'border_style' => 'sanitize_text_field',
            'border_width' => 'fancyforms_sanitize_number',
            'minnum' => 'fancyforms_sanitize_float',
            'maxnum' => 'fancyforms_sanitize_float',
            'classes' => 'sanitize_text_field',
            'auto_width' => 'sanitize_text_field',
            'placeholder' => 'sanitize_text_field',
            'format' => 'sanitize_text_field',
            'required_indicator' => 'sanitize_text_field',
            'options_layout' => 'sanitize_text_field',
            'field_max_width' => 'fancyforms_sanitize_number',
            'field_max_width_unit' => 'sanitize_text_field',
            'image_max_width' => 'fancyforms_sanitize_number',
            'image_max_width_unit' => 'sanitize_text_field',
            'field_alignment' => 'sanitize_text_field',
            'blank' => 'sanitize_text_field',
            'invalid' => 'sanitize_text_field',
            'rows' => 'fancyforms_sanitize_number',
            'max' => 'fancyforms_sanitize_number',
            'disable' => array(
                'line1' => 'sanitize_text_field',
                'line2' => 'sanitize_text_field',
                'city' => 'sanitize_text_field',
                'state' => 'sanitize_text_field',
                'zip' => 'fancyforms_sanitize_number',
                'country' => 'sanitize_text_field'
            )
        );
    }

    public static function get_all_forms_list_options() {
        $all_forms = array();
        $forms = FancyFormsBuilder::get_all_forms();
        foreach ($forms as $form) {
            $all_forms[$form->id] = $form->name;
        }
        return $all_forms;
    }

    public static function getSalt() {
        $salt = get_option('_fancyforms_security_salt');
        if (!$salt) {
            $salt = wp_generate_password();
            update_option('_fancyforms_security_salt', $salt, 'no');
        }
        return $salt;
    }

    public static function encrypt($text) {
        $key = static::getSalt();
        $cipher = 'AES-128-CBC';
        $ivlen = openssl_cipher_iv_length($cipher);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt($text, $cipher, $key, $options = OPENSSL_RAW_DATA, $iv);
        $hmac = fancy_hmac('sha256', $ciphertext_raw, $key, $as_binary = true);
        return base64_encode($iv . $hmac . $ciphertext_raw);
    }

    public static function decrypt($text) {
        $key = static::getSalt();
        $c = base64_decode($text);
        $cipher = 'AES-128-CBC';
        $ivlen = openssl_cipher_iv_length($cipher);
        $iv = substr($c, 0, $ivlen);
        $hmac = substr($c, $ivlen, $sha2len = 32);
        $ciphertext_raw = substr($c, $ivlen + $sha2len);
        $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, $options = OPENSSL_RAW_DATA, $iv);
        $calcmac = fancy_hmac('sha256', $ciphertext_raw, $key, $as_binary = true);

        if (fancy_equals($hmac, $calcmac)) {
            return $original_plaintext;
        }
    }

    public static function get_field_input_value($value) {
        $entry_val = '';
        $entry_value = maybe_unserialize($value['value']);
        $entry_type = maybe_unserialize($value['type']);
        if (is_array($entry_value)) {
            if ($entry_type == 'name') {
                $entry_value = implode(' ', array_filter($entry_value));
            } elseif ($entry_type == 'repeater_field') {
                $entry_val = '<table><thead><tr>';
                foreach(array_keys($entry_value) as $key) {
                    $entry_val .= '<th>' . $key . '</th>';
                }
                $entry_val .= '</tr></thead><tbody>';
                $out = array();
                foreach ($entry_value as  $rowkey => $row) {
                    foreach($row as $colkey => $col){
                        $out[$colkey][$rowkey]=$col;
                    }
                }
                foreach($out as $key => $val) {
                    foreach($val as $eval) {
                        $entry_val .= '<td>' . $eval . '</td>';
                    }
                    $entry_val .= '</tr>';
                }
                $entry_val .= '</tbody></table>';
                $entry_value = $entry_val;
            } else {
                $entry_value = implode(',', array_filter($entry_value));
            }
        }
        return $entry_value;
    }

    public static function unserialize_or_decode($value) {
        if (is_array($value)) {
            return $value;
        }
        if (is_serialized($value)) {
            return self::maybe_unserialize_array($value);
        } else {
            return self::maybe_json_decode($value, false);
        }
    }


    public static function maybe_unserialize_array($value) {
        if (!is_string($value)) {
            return $value;
        }

        if (!is_serialized($value) || 'a:' !== substr($value, 0, 2)) {
            return $value;
        }

        $parsed = FancyFormsSerializedStrParser::get()->parse( $value );
        if (is_array($parsed)) {
            $value = $parsed;
        }
        return $value;
    }

    public static function maybe_json_decode($string, $single_to_array = true) {
        if (is_array($string) || is_null($string)) {
            return $string;
        }

        $new_string = json_decode($string, true);
        if (function_exists('json_last_error')) {
            $single_value = false;
            if (!$single_to_array) {
                $single_value = is_array($new_string) && count($new_string) === 1 && isset($new_string[0]);
            }
            if (json_last_error() == JSON_ERROR_NONE && is_array($new_string) && ! $single_value) {
                $string = $new_string;
            }
        }
        return $string;
    }

}
