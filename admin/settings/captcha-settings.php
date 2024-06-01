<?php
defined('ABSPATH') || die();

$captcha_lang = array('en' => 'English', 'af' => 'Afrikaans', 'am' => 'Amharic', 'ar' => 'Arabic', 'hy' => 'Armenian', 'az' => 'Azerbaijani', 'eu' => 'Basque', 'bn' => 'Bengali', 'bg' => 'Bulgarian', 'ca' => 'Catalan', 'zh-HK' => 'Chinese Hong Kong', 'zh-CN' => 'Chinese Simplified', 'zh-TW' => 'Chinese Traditional', 'hr' => 'Croatian', 'cs' => 'Czech', 'da' => 'Danish', 'nl' => 'Dutch', 'en-GB' => 'English/UK', 'et' => 'Estonian', 'fa' => 'Farsi/Persian', 'fil' => 'Filipino', 'fi' => 'Finnish', 'fr' => 'French', 'fr-CA' => 'French/Canadian', 'gl' => 'Galician', 'ka' => 'Georgian', 'de' => 'German', 'de-AT' => 'German/Austria', 'de-CH' => 'German/Switzerland', 'el' => 'Greek', 'gu' => 'Gujarati', 'he' => 'Hebrew', 'iw' => 'Hebrew', 'hi' => 'Hindi', 'hu' => 'Hungarian', 'is' => 'Icelandic', 'id' => 'Indonesian', 'it' => 'Italian', 'ja' => 'Japanese', 'kn' => 'Kannada', 'ko' => 'Korean', 'lo' => 'Laothian', 'lv' => 'Latvian', 'lt' => 'Lithuanian', 'ml' => 'Malayalam', 'ms' => 'Malaysian', 'mr' => 'Marathi', 'no' => 'Norwegian', 'pl' => 'Polish', 'pt' => 'Portuguese', 'pt-BR' => 'Portuguese/Brazilian', 'pt-PT' => 'Portuguese/Portugal', 'ro' => 'Romanian', 'ru' => 'Russian', 'sr' => 'Serbian', 'si' => 'Sinhalese', 'sk' => 'Slovak', 'sl' => 'Slovenian', 'es' => 'Spanish', 'es-419' => 'Spanish/Latin America', 'sw' => 'Swahili', 'sv' => 'Swedish', 'ta' => 'Tamil', 'te' => 'Telugu', 'th' => 'Thai', 'tr' => 'Turkish', 'uk' => 'Ukrainian', 'ur' => 'Urdu', 'vi' => 'Vietnamese', 'zu' => 'Zulu');
?>
<p><?php printf(esc_html__('%1$s requires a Site and Secret keys. Sign up for a %2$sfree %1$s key%3$s.', 'fancy-forms'), esc_html('reCAPTCHA'), '<a href="' . esc_url('https://www.google.com/recaptcha/') . '" target="_blank">', '</a>'); ?></p>
<p><?php printf(esc_html__('Tutorial to %1$sGenerate Site and Secret Keys%2$s', 'fancy-forms'), '<a href="https://fancywp.com/" target="_blank">', '</a>'); ?></p>

<div class="fancyforms-form-container fancyforms-grid-container">
    <div class="fancyforms-settings-row">
        <label class="fancyforms-setting-label"><?php esc_html_e('reCAPTCHA Type', 'fancy-forms'); ?></label>
        <select name="fancyforms_settings[re_type]" id="fancyforms-re-type" data-condition="toggle">
            <option value="v2" <?php selected($settings['re_type'], 'v2'); ?>><?php esc_html_e('Checkbox (V2)', 'fancy-forms'); ?></option>
            <option value="v3" <?php selected($settings['re_type'], 'v3'); ?>><?php esc_html_e('v3', 'fancy-forms'); ?></option>
        </select>
    </div>

    <div class="fancyforms-settings-row" data-condition-toggle="fancyforms-re-type" data-condition-val="v2">
        <label class="fancyforms-setting-label"><?php esc_html_e('v2 Site Key', 'fancy-forms'); ?></label>
        <input type="text" name="fancyforms_settings[pubkey_v2]" value="<?php echo esc_attr($settings['pubkey_v2']); ?>" />
    </div>

    <div class="fancyforms-settings-row" data-condition-toggle="fancyforms-re-type" data-condition-val="v2">
        <label class="fancyforms-setting-label"><?php esc_html_e('v2 Secret Key', 'fancy-forms'); ?></label>
        <input type="text" name="fancyforms_settings[privkey_v2]" value="<?php echo esc_attr($settings['privkey_v2']); ?>" />
    </div>

    <div class="fancyforms-settings-row" data-condition-toggle="fancyforms-re-type" data-condition-val="v3">
        <label class="fancyforms-setting-label"><?php esc_html_e('v3 Site Key', 'fancy-forms'); ?></label>
        <input type="text" name="fancyforms_settings[pubkey_v3]" value="<?php echo esc_attr($settings['pubkey_v3']); ?>" />
    </div>

    <div class="fancyforms-settings-row" data-condition-toggle="fancyforms-re-type" data-condition-val="v3">
        <label class="fancyforms-setting-label"><?php esc_html_e('v3 Secret Key', 'fancy-forms'); ?></label>
        <input type="text" name="fancyforms_settings[privkey_v3]" value="<?php echo esc_attr($settings['privkey_v3']); ?>" />
    </div>

    <div class="fancyforms-settings-row">
        <label class="fancyforms-setting-label"><?php esc_html_e('reCAPTCHA Language', 'fancy-forms'); ?></label>
        <select name="fancyforms_settings[re_lang]">
            <option value="" <?php selected($settings['re_lang'], ''); ?>><?php esc_html_e('Browser Default', 'fancy-forms'); ?></option>
            <?php foreach ($captcha_lang as $lang => $lang_name) { ?>
                <option value="<?php echo esc_attr($lang); ?>" <?php selected($settings['re_lang'], $lang); ?>><?php echo esc_html($lang_name); ?></option>
            <?php } ?>
        </select>
    </div>

    <div id="fancyforms-captcha-threshold-container" class="fancyforms-settings-row" data-condition-toggle="fancyforms-re-type" data-condition-val="v3">
        <label class="fancyforms-setting-label"><?php esc_html_e('reCAPTCHA Threshold', 'fancy-forms'); ?></label>
        <p class="fancyforms-description"><?php esc_html_e('A score of 0 is likely to be a bot and a score of 1 is likely not a bot. Setting a lower threshold will allow more bots, but it will also stop fewer real users.', 'fancy-forms'); ?></p>
        <div class="fancyforms-grid-container">
            <div class="fancyforms-setting-fields fancyforms-range-slider-wrap fancyforms-grid-3">
                <div class="fancyforms-range-slider"></div>
                <input id="fancyforms-re-threshold" class="fancyforms-range-input-selector" type="number" name="fancyforms_settings[re_threshold]" value="<?php echo esc_attr($settings['re_threshold']); ?>" min="0" max="1" step="0.1">
            </div>
        </div>
    </div>
</div>
