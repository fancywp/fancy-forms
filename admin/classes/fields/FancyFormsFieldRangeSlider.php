<?php
defined('ABSPATH') || die();

class FancyFormsFieldRangeSlider extends FancyFormsFieldType {

    protected $type = 'range_slider';

    public function field_settings_for_type() {
        return array(
            'range' => true
        );
    }

    public function validate($args) {
        $errors = array();

        if (!is_numeric($args['value']) && '' !== $args['value'])
            $errors['field' . $args['id']] = FancyFormsFields::get_error_msg($this->field, 'invalid');

        if ($args['value'] != '') {
            $minnum = FancyFormsFields::get_option($this->field, 'minnum');
            $maxnum = FancyFormsFields::get_option($this->field, 'maxnum');
            if ($maxnum !== '' && $minnum !== '') {
                $value = (float) $args['value'];
                if ($value < $minnum) {
                    $errors['field' . $args['id']] = esc_html__('Please select a higher number', 'fancy-forms');
                } elseif ($value > $maxnum) {
                    $errors['field' . $args['id']] = esc_html__('Please select a lower number', 'fancy-forms');
                }
            }
            $this->validate_step($errors, $args);
        }
        return $errors;
    }

    private function validate_step(&$errors, $args) {
        if (isset($errors['field' . $args['id']])) {
            return;
        }
        $step = FancyFormsFields::get_option($this->field, 'step');
        if (!$step || !is_numeric($step)) {
            return;
        }
        $result = $this->check_value_is_valid_with_step($args['value'], $step);
        if (!$result) {
            return;
        }
        $errors['field' . $args['id']] = sprintf(__('Please enter a valid value. Two nearest valid values are %1$s and %2$s', 'fancy-forms'), floatval($result[0]), floatval($result[1]));
    }

    private function check_value_is_valid_with_step($value, $step) {
        $decimals = max(FancyFormsHelper::count_decimals($value), FancyFormsHelper::count_decimals($step));
        $pow = pow(10, $decimals);
        $value = intval($pow * $value);
        $step = intval($pow * $step);
        $div = $value / $step;
        if (is_int($div)) {
            return 0;
        }
        $div = floor($div);
        return array($div * $step / $pow, ( $div + 1 ) * $step / $pow);
    }

    public function sanitize_value(&$value) {
        return fancyforms_sanitize_float($value);
    }

    protected function input_html() {
        $field = $this->get_field();
        ?>
        <div class="fancyforms-range-slider-container">
            <div class="fancyforms-range-slider-wrap">
                <div class="fancyforms-range-slider"></div>
                <input class="fancyforms-range-input-selector" type="number" <?php $this->field_attrs(); ?>>
            </div>
        </div>
        <?php
    }

}
