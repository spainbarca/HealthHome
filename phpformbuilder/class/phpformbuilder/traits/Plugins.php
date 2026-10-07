<?php

declare(strict_types=1);

namespace phpformbuilder\traits;

trait Plugins
{
    /**
     * Adds the addAltcha plugin
     *
     * @param  bool $spamfilter (Optional) If true, the plugin will add a spamfilter to the form
     * @return \phpformbuilder\Form
     */
    public function addAltcha(bool $spamfilter = false): self
    {
        // load the altcha API key
        require_once $this->pluginsPath . 'altcha/altcha_api_key.php';

        $spamfilterAttr = '';
        if ($spamfilter) {
            $spamfilterAttr = ' spamfilter';
        }

        // Generate a random ID for the widget
        $widgetId = 'altcha_' . bin2hex(random_bytes(8));

        $startWrapper = $this->setInputGroup('', 'start', 'elementsWrapper');
        $startCol     = $this->getElementCol('start', 'altcha');
        $endCol       = $this->getElementCol('end', 'altcha');
        $endWrapper   = $this->setInputGroup('', 'end', 'elementsWrapper');

        $this->addHtml($startWrapper);
        $this->addHtml($startCol);
        $this->addHtml('<altcha-widget id="' . $widgetId . '" challengeurl="https://eu.altcha.org/api/v1/challenge?apiKey=' . ALTCHA_API_KEY . '"' . $spamfilterAttr . '></altcha-widget>');
        $this->addHtml('<br>');
        if ($this->hasAltchaError) {
            $this->addHtml('<p class="has-error ' . $this->options->getString('textErrorClass') . '" style="display:block;">' . $this->altchaErrorText . '</p>');
        }
        $this->addHtml($endCol);
        $this->addHtml($endWrapper);
        $this->addPlugin('altcha', '#' . $this->formId, 'default', ['formId' => $this->formId, 'pluginsUrl' => $this->pluginsUrl, 'widgetId' => $widgetId]);

        return $this;
    }

    /**
     * Adds a hcaptcha div
     *
     * @param  string $sitekey hcaptcha key
     * @param  string $attr    (Optional) Can be any HTML input attribute
     * @return \phpformbuilder\Form
     */
    public function addHcaptcha(string $sitekey, string $attr = ''): self
    {
        if ($_SERVER['REMOTE_ADDR'] == '127.0.0.1' || $_SERVER['REMOTE_ADDR'] == '::1') {
            // localhost settings
            $sitekey = '10000000-ffff-ffff-ffff-000000000001';
        }
        $attr          = $this->getAttributes($attr);
        $attr          = $this->addClass('h-captcha', $attr);
        $startWrapper = $this->setInputGroup('', 'start', 'elementsWrapper');
        $startCol     = $this->getElementCol('start', 'recaptcha');
        $endCol       = $this->getElementCol('end', 'recaptcha');
        $endWrapper   = $this->setInputGroup('', 'end', 'elementsWrapper');
        $this->addHtml($startWrapper);
        $this->addHtml($startCol);
        $this->addHtml('<div id="hcaptcha-' . $this->formId . '" data-sitekey="' . $sitekey . '" ' . $attr . '></div>');

        if ($this->hasHcaptchaError) {
            $this->addHtml('<p class="has-error ' . $this->options->getString('textErrorClass') . '" style="display:block;">' . $this->hcaptchaErrorText . '</p>');
        }

        $this->addHtml('<br>');
        $this->addHtml($endCol);
        $this->addHtml($endWrapper);
        $this->addPlugin('hcaptcha', '#' . $this->formId, 'default', ['sitekey' => $sitekey, 'formId' => $this->formId]);

        return $this;
    }

    /**
     * Gets and tests plugins url ($this->pluginsUrl).
     * Adds a javascript plugin to the selected field(s)
     *
     * @param  string $pluginName     The name of the plugin,
     *                                 must be the name of the
     *                                 xml file in
     *                                 plugins-config dir
     *                                 without extension.
     *                                 Example : colorpicker
     * @param  string $selector        The jQuery style selector.
     *                                 Examples : #colorpicker
     *                                 .colorpicker
     * @param  string $jsConfig       (Optional) The xml node where your plugin code is
     *                                 in plugins-config/[your-plugin.xml] file
     * @param  array<string, mixed>  $pluginSettings (Optional) An associative array containing
     *                                 the strings to search as keys
     *                                 and replacement values as data.
     *                                 Strings will be replaced with data
     *                                 in js_code xml node of your
     *                                 plugins-config/[your-plugin.xml] file.
     * @return \phpformbuilder\Form
     */
    public function addPlugin(string $pluginName, string $selector, string $jsConfig = 'default', array $pluginSettings = []): self
    {
        $keepOriginalSelectorPlugins = ['modal', 'popover'];

        // standardizes boolean values by converting them to strings
        $pluginSettings = $this->booleanToString($pluginSettings);

        if (!in_array($pluginName, $keepOriginalSelectorPlugins) && !preg_match('`' . $this->formId . '`', $selector) && !preg_match('`form`', $selector)) {
            // add the form id to selector
            $selector = '#' . $this->formId . ' ' . $selector;
        }
        if ($pluginName == 'nice-check' && $this->framework == 'material') {
            $this->buildErrorMsg('NICE-CHECK PLUGIN + MATERIAL<br>nice-check plugin cannot be used with Material plugin.');
        } elseif ($pluginName == 'bootstrap-select' && $this->framework == 'foundation') {
            $this->buildErrorMsg('BOOTSTRAP SELECT PLUGIN + FOUNDATION<br>Bootstrap Select plugin cannot be used with FOUNDATION.<br>Use <em>select2</em> instead.');
        }

        // custom PHPCG settings
        /** @scrutinizer ignore */
        if (defined('DATETIMEPICKERS_STYLE') && \DATETIMEPICKERS_STYLE == 'material' && $pluginName == 'pickadate') {
            if ($jsConfig == 'pickatime') {
                $pluginName = 'material-timepicker';
                $jsConfig = 'default';
            } else {
                $pluginName = 'material-datepicker';
            }
        }

        if ($pluginName == 'materialize') {
            $_SESSION['phpfb_framework'] = 'material-bootstrap';
        }
        if ($pluginName == 'material-datepicker' || $pluginName == 'material-timepicker') {
            // add Material base css & js if needed
            // if form is loaded with Ajax,
            // material-pickers-base css & js have to be loaded in the main page manually
            if (!in_array('material-pickers-base', $this->jsPlugins)) {
                if ($this->framework == 'material') {
                    $this->addPlugin('material-pickers-base', '#', 'materialize', ['pluginsUrl' => $this->pluginsUrl]);
                } else {
                    $this->addPlugin('material-pickers-base', '#', 'default');
                }
            }
            // set pickers default language
            if (!array_key_exists('language', $pluginSettings)) {
                if (defined('DATETIMEPICKERS_LANG')) {
                    $pluginSettings['language'] = \DATETIMEPICKERS_LANG;
                } else {
                    $pluginSettings['language'] = 'en_EN';
                }
            }
        } elseif ($pluginName == 'pickadate' && !array_key_exists('language', $pluginSettings)) {
            // set pickers default language
            if (defined('DATETIMEPICKERS_LANG')) {
                $pluginSettings['language'] = \DATETIMEPICKERS_LANG;
            } else {
                $pluginSettings['language'] = 'en_EN';
            }
        } elseif ($pluginName == 'nice-check' && !array_key_exists('skin', $pluginSettings)) {
            // set pickers default language
            $pluginSettings['skin'] = 'green';
        } elseif ($pluginName == 'pretty-checkbox' && !array_key_exists('class', $pluginSettings)) {
            // set pretty-checkbox defaults
            $defaults = [
                'checkboxStyle'  => 'default',
                'radioStyle'     => 'round',
                'color'          => '',
                'fill'           => '',
                'icon'           => '',
                'plain'          => '',
                'animations'     => '',
                'size'           => '',
                'toggle'         => 'false',
                'toggleOnLabel'  => '',
                'toggleOnColor'  => '',
                'toggleOnIcon'   => '',
                'toggleOffLabel' => '',
                'toggleOffColor' => '',
                'toggleOffIcon'  => ''
            ];
            $pluginSettings = \array_merge($defaults, $pluginSettings);
        } elseif ($pluginName == 'select2' && !array_key_exists('language', $pluginSettings)) {
            // set pickers default language
            $pluginSettings['language'] = 'en';
        } elseif ($pluginName == 'intl-tel-input' || $pluginName == 'slimselect') {
            // set slimselect theme
            $find = ['bs4', 'bs5'];
            $replace = ['bootstrap4', 'bootstrap5'];
            $pluginSettings['FRAMEWORK'] = str_replace($find, $replace, $this->framework);
        } elseif ($pluginName == 'word-character-count' || ($pluginName == 'tinymce' && $jsConfig == 'word-char-count')) {
            // pass the $pluginSettings array as a Js object
            if (!array_key_exists('wrapperClassName', $pluginSettings)) {
                $wrapperClassName = [
                    'bs4'         => 'form-text',
                    'bs5'         => 'form-text',
                    'bulma'       => 'help',
                    'foundation'  => 'help-text',
                    'material'    => 'form-text',
                    'tailwind'    => 'mt-2 text-sm',
                    'uikit'       => 'uk-text-small'
                ];
                $className = [
                    'bs4'         => 'text-muted mb-0',
                    'bs5'         => 'text-muted mb-0',
                    'bulma'       => 'has-text-grey',
                    'foundation'  => 'text-muted',
                    'material'    => 'text-muted',
                    'tailwind'    => 'text-gray-500 dark:text-gray-400',
                    'uikit'       => 'uk-text-muted uk-margin-remove-bottom'
                ];
                $errorClassName = [
                    'bs4'         => 'text-danger mb-0',
                    'bs5'         => 'text-danger mb-0',
                    'bulma'       => 'has-text-danger',
                    'foundation'  => 'text-danger',
                    'material'    => 'text-danger',
                    'tailwind'    => 'text-danger-500 dark:text-danger-400',
                    'uikit'       => 'uk-text-danger uk-margin-remove-bottom'
                ];
                $framework = $this->framework;
                if (array_key_exists($this->framework, $wrapperClassName)) {
                    $pluginSettings['wrapperClassName'] = $wrapperClassName[$framework];
                }
                if (array_key_exists($this->framework, $className)) {
                    $pluginSettings['className'] = $className[$framework];
                }
                if (array_key_exists($this->framework, $errorClassName)) {
                    $pluginSettings['errorClassName'] = $errorClassName[$framework];
                }
            }
            $pluginSettings['options'] = json_encode($pluginSettings);
        }
        if (!in_array($pluginName, $this->jsPlugins)) {
            $this->jsPlugins[] = $pluginName;
        }
        if (!isset($this->jsFields[$pluginName]) || !in_array($selector, $this->jsFields[$pluginName]) || !in_array($jsConfig, $this->jsContent[$pluginName])) {
            $this->jsFields[$pluginName][]       = $selector;
            $this->jsContent[$pluginName][]      = $jsConfig;
            $this->pluginSettings[$pluginName][] = $pluginSettings;
        }

        return $this;
    }

    /**
     * Adds a Google Invisible Recaptcha field
     *
     * @param string $sitekey Google recaptcha key
     * @param string $action Google recaptcha action
     * @param string $responseFieldname Google recaptcha response fieldname
     * @param string $xmlConfig The xml configuration of the plugin
     * @return \phpformbuilder\Form
     */
    public function addRecaptchaV3($sitekey, $action = 'default', $responseFieldname = 'g-recaptcha-response', $xmlConfig = 'default'): self
    {
        $action = str_replace('-', '_', $action);
        if (!preg_match('`^[a-zA-Z_0-9]+$`', $action)) {
            $this->buildErrorMsg('Recaptcha V3 Action contains invalid characters. Allowed characters: lowercase, uppercase, underscore');
        } else {
            $this->addInput('hidden', $responseFieldname);
            $this->addPlugin('recaptcha-v3', '#' . $this->formId, $xmlConfig, ['sitekey' => $sitekey, 'action' => $action, 'response_fieldname' => $responseFieldname]);

            if ($this->hasRecaptchaError) {
                $this->addHtml('<p class="has-error ' . $this->options->getString('textErrorClass') . '" style="display:block">' . $this->recaptchaErrorText . '</p>');
            }
        }

        return $this;
    }

    /**
     * Ends a dependent field block
     *
     * @return \phpformbuilder\Form
     */
    public function endDependentFields(): self
    {
        $this->addHtml('</div>');

        // remove the last current_dependent_data
        \array_pop($this->currentDependentData);

        return $this;
    }

    /**
     * wrap form in a modal
     *
     * @param  array<string, mixed> $options
     *               $modalOptions = [
     * string $title       The modal title
     * string $title-class The modal title classname(s)
     * string $title-tag   The modal title HTML tag
     * string $animation   E.g.: 'slide-in-top',
     * bool   $blurred     Choose whether the modal backdrop is blurred or not
     * ]
     * @return \phpformbuilder\Form
     */
    public function modal(array $options = []): self
    {
        $modalId = 'modal-' . $this->formId;
        $modalOptions = [
            'title'       => '',
            'title-class' => '',
            'title-tag'   => 'h2',
            'animation'   => 'fade-in',
            'blur'        => true
        ];
        $modalOptions = array_merge($modalOptions, $options);
        $this->addPlugin('modal', $modalId, 'default', ['formId' => $this->formId]);

        // set the title
        $modalTitleHtml = '';
        $modalTitleClass = '';

        if (!empty($modalOptions['title'])) {
            if (!empty($modalOptions['title-class'])) {
                $modalTitleClass .= ' class="' . $modalOptions['title-class'] . '"';
            }
            $modalTitleHtml = '<' . $modalOptions['title-tag'] . ' id="' . $modalId . '-title"' . $modalTitleClass . '>' . $modalOptions['title'] . '</' . $modalOptions['title-tag'] . '>';
        }

        $blurClass = '';
        if ($modalOptions['blur'] === true) {
            $blurClass = ' modal-overlay-blurred';
        }

        $this->formStartWrapper = '<div class="micromodal micromodal-' . $modalOptions['animation'] . '" id="' . $modalId . '" aria-hidden="true">';
        $this->formStartWrapper .= '<div tabindex="-1" class="modal-overlay' . $blurClass . '" data-micromodal-close>';

        $ariaLabelTitle = '';
        if (!empty($modalOptions['title'])) {
            $ariaLabelTitle = ' aria-labelledby="' . $modalId . '-title"';
        }
        $this->formStartWrapper .= '<div role="dialog" class="modal-container" aria-modal="true"' . $ariaLabelTitle . '">';
        $this->formStartWrapper .= '<button class="modal-close" aria-label="Close modal" data-micromodal-close></button>';
        $this->formStartWrapper .= '<header class="modal-header">';
        $this->formStartWrapper .= $modalTitleHtml;
        $this->formStartWrapper .= '</header>';
        $this->formStartWrapper .= '<div id="' . $modalId . '-content">';

        $this->formEndWrapper = '</div></div></div></div>';

        return $this;
    }

    /**
     * wrap form in a popover
     *
     * @return \phpformbuilder\Form
     */
    public function popover(): self
    {
        $this->hasPopover = true;
        $popoverId = 'popover-' . $this->formId;
        $this->addPlugin('tooltip', $popoverId, 'popover', ['formId' => $this->formId]);

        $this->formStartWrapper = '<template id="' . $popoverId . '" aria-hidden="true">';
        $this->formEndWrapper = '</template>';

        $this->options->set('openDomReady', '    if (typeof(popoverReady) === "undefined") { window.popoverReady = [];}' . "\n" . 'popoverReady["' . $this->formId . '"] = function() {' . "\n");

        $this->options->set('closeDomReady', '}' . "\n");

        // show popover if the form is posted with some errors
        if (count($this->errorFields) > 0) {
            $this->options->set('closeDomReady', $this->options->getString('closeDomReady') . 'setTimeout(() => {' . "\n" . 'document.querySelector(\'[data-popover-trigger="' . $this->formId . '"]\').dispatchEvent(new Event(\'click\', { bubbles: true }));' . "\n" . '}, 1000);' . "\n");
        }

        return $this;
    }

    /**
     * Start a hidden block
     * which can contain any element and html
     * Hiden block will be shown on $parentField change
     * if $parentField value matches one of $showValues
     *
     * @param  string  $parentField name of the field which will trigger show/hide
     * @param  mixed  $showValues  single value or comma separated values which will trigger show.
     * @param  bool $inverse      if true, dependent fields will be shown if any other value than $showValues is selected.
     * @return \phpformbuilder\Form
     */
    public function startDependentFields(string $parentField, $showValues, bool $inverse = false): self
    {
        $this->addHtml('<div class="hidden-wrapper off" data-parent="' . $parentField . '" data-show-values="' . $showValues . '" data-inverse="' . $inverse . '">');
        if (!in_array('dependent-fields', $this->jsPlugins)) {
            $this->addPlugin('dependent-fields', '.hidden-wrapper');
        }

        // register data to transmit to dependent fields. Data will be used to know if dependent fields are required or not, depending on parent posted value.
        $this->currentDependentData[] = [
            'parent_field' => $parentField,
            'show_values'  => $showValues,
            'inverse'      => $inverse
        ];

        return $this;
    }
}
