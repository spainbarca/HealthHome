<?php

declare(strict_types=1);

namespace phpformbuilder\traits;

trait Elements
{
    /**
     * Adds an input element to the form.
     *
     * @param string $type  The type of the input element.
     *                      Accepts all input html5 types except checkbox and radio :
     *                      button, color, date, datetime, datetime-local,
     *                      email, file, hidden, image, month, number, password,
     *                      range, reset, search, submit, tel, text, time, url, week
     * @param string $name  The name of the input element.
     * @param mixed $value  (Optional) The default value of the input element.
     * @param string $label (Optional) The label of the input element.
     * @param string $attr  (Optional) Any additional attributes for the input element.
     *                      Can be any HTML input attribute or js event.
     *                      attributes must be listed separated with commas.
     *                      If you don't specify any ID as attr, the ID will be the input's name.
     *                      Example : class=my-class,placeholder=My Text,onclick=alert(\'clicked\');
     * @return self
     */
    public function addInput(string $type, string $name, $value = '', string $label = '', string $attr = ''): self
    {
        if ($type == 'file') {
            $this->hasFile = true;
        }
        $attr          = $this->getAttributes($attr); // returns linearised attributes (with ID)
        $arrayValues  = $this->getID($name, $attr); // if $attr contains no ID, field ID will be $name.
        $id            = $arrayValues['id'];
        $attr          = $arrayValues['attributs']; // if $attr contains an ID, we remove it.
        if ($type != 'hidden') {
            $attr = $this->addElementClass('input', $name, $attr);
        }
        if ($this->isGrouped($name)) {
            $attr = $this->addClass('fv-group', $attr);
        }
        if (in_array($name, $this->fieldsWithHelpers)) {
            $attr = $this->addAttribute('aria-describedby', $name . '-helper', $attr);
        }
        $value         = $this->getValue($name, $value);
        $startWrapper = '';
        $endWrapper   = '';
        $startLabel   = '';
        $endLabel     = '';
        $startCol     = '';
        $endCol       = '';
        $element       = '';
        $hasAddon     = \array_key_exists($name, $this->fieldsWithAddons);
        $hasIcon      = \array_key_exists($name, $this->fieldsWithIcons);
        // auto-add date/time pickers in material forms
        if ($this->framework == 'material') {
            if (strpos($attr, 'datepicker') !== false) {
                $this->addPlugin('material-datepicker', '#' . $id);
            } elseif (strpos($attr, 'timepicker') !== false) {
                $this->addPlugin('material-timepicker', '#' . $id);
            }
        }
        if ($type == 'hidden' && strpos($attr, 'data-signature-pad') === false) {
            $this->hiddenFields .= '<input name="' . $name . '" type="hidden" value="' . $value . '" ' . $attr . '>';
        } else {
            // form-group wrapper
            $startWrapper = $this->setInputGroup($name, 'start', 'elementsWrapper');
            $startWrapper .= $this->addErrorWrapper($name, 'start');

            // label
            if (!empty($label)) {
                if ($this->hasLabelWrapper()) {
                    $startLabel  .= $this->getLabelCol('start');
                }
                $labelClass = $this->getLabelClass();
                if (strpos($attr, 'form-control-sm')) {
                    $labelClass = $this->addClass('col-form-label-sm', $labelClass);
                } elseif (strpos($attr, 'form-control-lg')) {
                    $labelClass = $this->addClass('col-form-label-lg', $labelClass);
                }
                $startLabel .= '<label for="' . $id . '"' . $labelClass . '>' . $this->getRequired($label, $attr);
                $endLabel = '</label>';
                if ($this->hasLabelWrapper()) {
                    $endLabel   .= $this->getLabelCol('end');
                }
            }

            // daterange picker
            if (strpos($attr, 'data-litepick') !== false) {
                $this->addPlugin('litepicker', 'input[data-litepick=\'true\']', 'default');
            }

            $startCol .= $this->getElementCol('start', 'input', $label); // col-sm-8
            $element .= $this->getErrorInputWrapper($name, $label, 'start'); // has-error
            if ($this->framework === 'bulma') {
                $element .= $this->startElementCustomWrapper($name, 'bulma_control');
            }
            $element .= $this->getHtmlElementContent($name, 'before', 'outside_wrapper');
            if (isset($this->inputWrapper[$name])) {
                $element .= static::buildElementWrapper($this->inputWrapper[$name], 'start'); // input-group (icons, addons)
            }
            // icons, addons, custom html with addHtml()
            $htmlBefore = $this->getHtmlElementContent($name, 'before', 'inside_wrapper');
            $htmlAfter = $this->getHtmlElementContent($name, 'after', 'inside_wrapper');
            if ($this->framework == 'material') {
                if ($hasAddon) {
                    $attr = $this->addClass('has-addon-' . $this->fieldsWithAddons[$name], $attr);
                } elseif ($hasIcon) {
                    $attr = $this->addClass('has-icon-' . $this->fieldsWithIcons[$name], $attr);
                }
            } elseif ($this->framework == 'tailwind') {
                if ($hasAddon) {
                    $clazz = 'pl-10';
                    $replace = 'rounded-r-';
                    if ($this->fieldsWithAddons[$name] == 'after') {
                        $clazz = 'pr-10';
                        $replace = 'rounded-l-';
                    }
                    $attr = \str_replace('rounded-', $replace, $attr);
                    $attr = $this->addClass($clazz, $attr);
                }
                if ($hasIcon) {
                    $clazz = 'pl-10';
                    if ($this->fieldsWithIcons[$name] == 'after') {
                        $clazz = 'pr-10';
                    }
                    $attr = $this->addClass($clazz, $attr);
                }
            }
            $element .= $htmlBefore;
            $ariaLabel = $this->getAriaLabel($label, $attr);
            if ($this->framework === 'foundation' && ($hasAddon || $hasIcon)) {
                $attr = $this->addClass('input-group-field', $attr);
            } elseif ($this->framework == 'bulma' && $hasAddon) {
                $element .= '<div class="control is-expanded">';
            }
            $element .= '<input id="' . $id . '" name="' . $name . '" type="' . $type . '" value="' . $value . '" ' . $attr . $ariaLabel . '>';
            if ($type === 'hidden' && strpos($attr, 'data-signature-pad') !== false) {
                $element .= '<canvas id="' . $id . '-canvas" class="signature-pad-canvas"></canvas>';
                $this->addPlugin('signature-pad', '#' . $id, 'default');
            }
            if ($this->framework == 'bulma' && $hasAddon) {
                $element .= '</div>';
            }
            $element .= $htmlAfter;
            if (isset($this->inputWrapper[$name])) {
                $element .= static::buildElementWrapper($this->inputWrapper[$name], 'end'); // end input-group
            }
            $element .= $this->getHtmlElementContent($name, 'after', 'outside_wrapper'); // -------------------Desired Username
            if ($this->framework === 'bulma') {
                $element .= $this->endElementCustomWrapper();
            }
            $element .= $this->getErrorInputWrapper($name, $label, 'end'); // end has-error
            $element .= $this->getError($name); // -------------------Error txt
            $endCol .= $this->getElementCol('end', 'input', $label); // end col-sm-8
            $endWrapper .= $this->addErrorWrapper($name, 'end');
            $endWrapper .= $this->setInputGroup($name, 'end', 'elementsWrapper'); // end form-group
            // output
            $this->html .= $this->outputElement($startWrapper, $endWrapper, $startLabel, $endLabel, $startCol, $endCol, $element, $this->options->getBool('wrapElementsIntoLabels'));

            //  if bootstrap-input-spinner enabled
            if (strpos($attr, 'data-input-spinner') !== false) {
                $xmlNode = 'default';
                if ($this->framework == 'bs4') {
                    $xmlNode = 'bs4';
                }
                $this->addPlugin('bootstrap-input-spinner', '#' . $id, $xmlNode);
            }

            //  if intl-tel-input enabled
            if (strpos($attr, 'data-intphone') !== false) {
                $this->addPlugin('intl-tel-input', '#' . $id);

                // add the intl-tel-input hidden field
                $this->addInput('hidden', $id . '-full-phone');
            }

            //  if colorpicker enabled
            if (strpos($attr, 'colorpicker') !== false) {
                $theme = 'classic';
                if (preg_match('`data-theme="(monolith|nano)"`', $attr, $out)) {
                    $theme = $out[1];
                }
                $this->addPlugin('colorpicker', '#' . $id, 'default', ['theme' => $theme]);
            }

            //  if js-captcha enabled
            if (strpos($attr, 'jCaptcha') !== false) {
                $this->addInput('hidden', 'jcaptcha-server-side-verification', '', '');
                $this->addPlugin('js-captcha', '.jCaptcha', 'default', ['formId' => $this->formId, 'errorClass' => $this->options->getString('elementsErrorClass'), 'helperStart' => $this->options->getStartWrapper('helper'), 'helperEnd' => $this->options->getStartWrapper('helper')]);
            }
        }
        $this->registerField($name, $attr);

        return $this;
    }

    /**
     * Adds a file upload element to the form.
     *
     * @param string $name The name of the file upload element.
     * @param string $value The default value of the file upload element.
     * @param string $label The label text for the file upload element.
     * @param string $attr Additional attributes for the file upload element.
     * @param array<string, mixed> $fileUploadConfig Configuration options for the file uploader.
     *                               Available options:
     *                                  'xml'           [string]       => (Optional) The XML configuration file for the file uploader.
     *                                                                    Default: 'default'
     *                                  'uploader'      [string]       => (Optional) The URL of the file uploader script.
     *                                                                    Default: 'ajax_upload_file.php'
     *                                  'upload_dir'    [string]       => (Optional) The directory where uploaded files will be stored.
     *                                                                    Default: '../../../../../file-uploads/'
     *                                  'limit'         [int]          => (Optional) The maximum number of files that can be uploaded.
     *                                                                    Default: 1
     *                                  'extensions'    [array|string] => (Optional) The allowed file extensions.
     *                                                                    Example: ['jpg', 'jpeg', 'png', 'audio/mp3', 'text/plain']
     *                                                                    Default: ['jpg', 'jpeg', 'png', 'gif']
     *                                  'file_max_size' [null|int]     => (Optional) The maximum size of each file in MB.
     *                                                                    Default: 5
     *                                  'thumbnails'    [bool]         => (Optional) Whether the uploader creates thumbnails or not.
     *                                                                    Thumbnails paths and sizing are handled by the plugin.
     *                                                                    Default: false
     *                                  'editor'        [bool]         => (Optional) Whether the user can crop/rotate the uploaded image.
     *                                                                    Default: false
     *                                  'width'         [null|int]     => (Optional) The maximum width of the uploaded image in pixels.
     *                                                                    Default: null
     *                                  'height'        [null|int]     => (Optional) The maximum height of the uploaded image in pixels.
     *                                                                    Default: null
     *                                  'crop'          [bool]         => (Optional) Whether the uploader crops the uploaded image or not.
     *                                                                    Default: false
     *                                  'debug'         [bool]         => (Optional) Whether to log the result in the browser's console
     *                                                                    and show an error message on the page if the uploader fails to parse the JSON result.
     *                                                                    Default: false
     * @param array<mixed> $currentFile The current file(s) associated with the file upload element.
     * @return self
     */
    public function addFileUpload(string $name, string $value = '', string $label = '', string $attr = '', array $fileUploadConfig = [], array  $currentFile = []): self
    {
        $this->hasFile = true;
        $attr           = $this->getAttributes($attr); // returns linearised attributes (with ID)
        $arrayValues   = $this->getID($name, $attr); // if $attr contains no ID, field ID will be $name.
        $attr           = $arrayValues['attributs']; // if $attr contains an ID, we remove it.
        $attr           = $this->addElementClass('input', $name, $attr);
        $value          = $this->getValue($name, $value);
        $startWrapper  = '';
        $endWrapper    = '';
        $startLabel    = '';
        $endLabel      = '';
        $startCol      = '';
        $endCol        = '';
        $element        = '';

        /* hidden field which will be posted in JSON with the uploaded file names. */
        $attr .= ' data-fileuploader-listInput="' . $name . '"';

        /* adding plugin */

        $defaultConfig = [
            'xml'           => 'default',
            'uploader'      => 'ajax_upload_file.php',
            'upload_dir'    => '../../../../../file-uploads/',
            'limit'         => 1,
            'extensions'    => ['jpg', 'jpeg', 'png', 'gif'],
            'file_max_size' => 5,
            'thumbnails'    => false,
            'editor'        => false,
            'width'         => null,
            'height'        => null,
            'crop'          => false,
            'debug'         => false
        ];

        $fileUploadConfig = array_merge($defaultConfig, $fileUploadConfig);

        // replace boolean values for javascript
        $bool = ['thumbnails', 'editor', 'crop', 'debug'];
        foreach ($bool as $b) {
            if ($fileUploadConfig[$b]) {
                $fileUploadConfig[$b] = 'true';
            } else {
                $fileUploadConfig[$b] = 'false';
            }
        }

        if (is_array($fileUploadConfig['extensions'])) {
            $fileUploadConfig['extensions'] = "['" . implode("', '", $fileUploadConfig['extensions']) . "']";
        }

        // set session vars which will be controlled & added to the PHP uploader
        // (plugins/fileuploader/[uploader]/php/ajax_upload_file.php)
        $formId = $this->formId;
        $_SESSION[$formId]['upload_config']['uploader-' . $name] = [
            'limit'         => $fileUploadConfig['limit'],
            'file_max_size' => $fileUploadConfig['file_max_size'],
            'extensions'    => $fileUploadConfig['extensions'],
            'upload_dir'    => $fileUploadConfig['upload_dir']
        ];

        $hash = sha1($fileUploadConfig['limit'] . $fileUploadConfig['file_max_size'] . $fileUploadConfig['extensions'] . $fileUploadConfig['upload_dir']);

        $xmlReplacements = [
            'limit'       => $fileUploadConfig['limit'],
            'uploader'    => $fileUploadConfig['uploader'],
            'uploadDir'   => $fileUploadConfig['upload_dir'],
            'extensions'  => $fileUploadConfig['extensions'],
            'fileMaxSize' => $fileUploadConfig['file_max_size'],
            'thumbnails'  => $fileUploadConfig['thumbnails'],
            'editor'      => $fileUploadConfig['editor'],
            'debug'       => $fileUploadConfig['debug'],
            'width'       => $fileUploadConfig['width'],
            'height'      => $fileUploadConfig['height'],
            'crop'        => $fileUploadConfig['crop'],
            'index'       => $this->fileuploaderCount,
            'hash'        => $hash,
            'formId'      => $this->formId,
            'PLUGINS_URL' => $this->pluginsUrl
        ];

        $this->addPlugin('fileuploader', '#uploader-' . $name, $fileUploadConfig['xml'], $xmlReplacements);

        // increment index
        $this->fileuploaderCount++;

        // form-group wrapper
        $startWrapper = $this->setInputGroup($name, 'start', 'elementsWrapper');
        $startWrapper .= $this->addErrorWrapper($name, 'start');

        // label
        if (!empty($label)) {
            if ($this->hasLabelWrapper()) {
                $startLabel  .= $this->getLabelCol('start');
            }
            $startLabel .= '<label for="uploader-' . $name . '"' . $this->getLabelClass('fileinput') . '>';
            if (in_array(str_replace('[]', '', $name), array_keys($this->errorFields))) {
                $startLabel .= '<span class="' . $this->options->getString('textErrorClass') . '">' . $this->getRequired($label, $attr) . '</span>';
            } else {
                $startLabel .= $this->getRequired($label, $attr);
            }
            $endLabel = '</label>';
            if ($this->hasLabelWrapper()) {
                $endLabel   .= $this->getLabelCol('end');
            }
        }

        // input
        $startCol .= $this->getElementCol('start', 'input', $label); // col-sm-8
        $element .= $this->getErrorInputWrapper($name, $label, 'start'); // has-error
        if ($this->framework === 'bulma') {
            $element .= $this->startElementCustomWrapper($name, 'bulma_control');
        }
        $element .= $this->getHtmlElementContent($name, 'before', 'outside_wrapper');
        if (isset($this->inputWrapper[$name])) {
            $element .= static::buildElementWrapper($this->inputWrapper[$name], 'start'); // input-group
        }
        $element .= $this->getHtmlElementContent($name, 'before', 'inside_wrapper');
        $currentFile_json_data = '';
        if (!empty($currentFile) && is_array($currentFile)) {
            if (isset($currentFile[0])) {
                // if several files passed as array
                $json = '';
                foreach ($currentFile as $cf) {
                    $json .= json_encode($cf) . ',';
                }
                $currentFile = rtrim($json, ',');
            } else {
                $currentFile = json_encode($currentFile);
            }
            $currentFile_json_data = ' data-fileuploader-files=\'[' . $currentFile . ']\'';
        }
        $element .= '<input type="file" name="uploader-' . $name . '" id="uploader-' . $name . '"' . $attr . $currentFile_json_data . '>';
        $element .= $this->getHtmlElementContent($name, 'after', 'inside_wrapper');
        if (isset($this->inputWrapper[$name])) {
            $element .= $this->getError($name);
            $element .= static::buildElementWrapper($this->inputWrapper[$name], 'end'); // end input-group
        }
        $element .= $this->getHtmlElementContent($name, 'after', 'outside_wrapper');
        if ($this->framework === 'bulma') {
            $element .= $this->endElementCustomWrapper();
        }
        $element .= $this->getErrorInputWrapper($name, $label, 'end'); // end has-error
        $element .= $this->getError($name);
        $endCol .= $this->getElementCol('end', 'input', $label); // end col-sm-8
        $endWrapper .= $this->addErrorWrapper($name, 'end');
        $endWrapper .= $this->setInputGroup($name, 'end', 'elementsWrapper'); // end form-group
        // output
        $this->html .= $this->outputElement($startWrapper, $endWrapper, $startLabel, $endLabel, $startCol, $endCol, $element, $this->options->getBool('wrapElementsIntoLabels'));
        $this->registerField($name, $attr);

        return $this;
    }

    /**
     * Adds textarea to the form
     * @param string $name  The textarea name
     * @param string $value (Optional) The textarea default value
     * @param string $label (Optional) The textarea label
     * @param string $attr  (Optional) Can be any HTML input attribute or js event.
     *                      attributes must be listed separated with commas.
     *                      If you don't specify any ID as attr, the ID will be the name of the textarea.
     *                      Example : cols=30, rows=4;
     * @return self
     */
    public function addTextarea(string $name, string $value = '', string $label = '', string $attr = ''): self
    {
        $attr          = $this->getAttributes($attr); // returns linearised attributes (with ID)
        $arrayValues  = $this->getID($name, $attr); // if $attr contains no ID, field ID will be $name.
        $id            = $arrayValues['id'];
        $attr          = $arrayValues['attributs']; // if $attr contains an ID, we remove it.
        $attr          = $this->addElementClass('textarea', $name, $attr);
        $startWrapper = '';
        $endWrapper   = '';
        $startLabel   = '';
        $endLabel     = '';
        $startCol     = '';
        $endCol       = '';
        $element       = '';
        if ($this->framework == 'material') {
            $attr         = $this->addClass("materialize-textarea", $attr);
        }
        $value        = $this->getValue($name, $value);
        // form-group wrapper
        $startWrapper = $this->setInputGroup($name, 'start', 'elementsWrapper');
        $startWrapper .= $this->addErrorWrapper($name, 'start');
        // label
        if (!empty($label)) {
            if ($this->hasLabelWrapper()) {
                $startLabel  .= $this->getLabelCol('start');
            }
            $startLabel .= '<label for="' . $id . '"' . $this->getLabelClass() . '>' . $this->getRequired($label, $attr);
            $endLabel = '</label>';
            if ($this->hasLabelWrapper()) {
                $endLabel   .= $this->getLabelCol('end');
            }
        }
        $startCol .= $this->getElementCol('start', 'textarea', $label);
        $element .= $this->getErrorInputWrapper($name, $label, 'start');
        if ($this->framework === 'bulma') {
            $element .= $this->startElementCustomWrapper($name, 'bulma_control');
        }
        $element .= $this->getHtmlElementContent($name, 'before');
        $ariaLabel = $this->getAriaLabel($label, $attr);
        $element .= '<textarea id="' . $id . '" name="' . $name . '" ' . $attr . $ariaLabel . '>' . $value . '</textarea>';
        $element .= $this->getHtmlElementContent($name, 'after');
        $element .= $this->getError($name);
        if ($this->framework === 'bulma') {
            $element .= $this->endElementCustomWrapper();
        }
        $element .= $this->getErrorInputWrapper($name, $label, 'end');
        $endCol .= $this->getElementCol('end', 'textarea', $label);
        $endWrapper = $this->addErrorWrapper($name, 'end');
        $endWrapper .= $this->setInputGroup($name, 'end', 'elementsWrapper'); // end form-group
        $this->html .= $this->outputElement($startWrapper, $endWrapper, $startLabel, $endLabel, $startCol, $endCol, $element, $this->options->getBool('wrapElementsIntoLabels'));
        $this->registerField($name, $attr);

        return $this;
    }

    /**
     * Adds option to the $selectName element
     *
     * IMPORTANT : Always add your options BEFORE creating the select element
     *
     * @param string $selectName The name of the select element
     * @param mixed $value       The option value
     * @param string $txt         The text that will be displayed
     * @param string $groupName  (Optional) the optgroup name
     * @param string $attr        (Optional) Can be any HTML input attribute or js event.
     *                            attributes must be listed separated with commas.
     *                            If you don't specify any ID as attr, the ID will be the name of the option.
     *                            Example : class=my-class
     * @return self
     */
    public function addOption(string $selectName, $value, string $txt, string $groupName = '', string $attr = ''): self
    {
        $optionValues = ['value' => $value, 'txt' => $txt, 'attributs' => $attr];
        if (!empty($groupName)) {
            $this->option[$selectName][$groupName][] = $optionValues;
            if (!isset($this->groupName[$selectName])) {
                $this->groupName[$selectName] = [];
            }
            if (!in_array($groupName, $this->groupName[$selectName])) {
                $this->groupName[$selectName][] = $groupName;
            }
        } else {
            $this->option[$selectName][] = $optionValues;
        }

        return $this;
    }

    /**
     * Adds a select element
     *
     * IMPORTANT : Always add your options BEFORE creating the select element
     *
     * @param string $selectName        The name of the select element
     * @param string $label              (Optional) The select label
     * @param string $attr               (Optional)  Can be any HTML input attribute or js event.
     *                                   attributes must be listed separated with commas.
     *                                   If you don't specify any ID as attr, the ID will be the input's name.
     *                                   Example : class=my-class
     * @param bool   $displayGroupLabels (Optional) True or false.
     *                                   Default is true.
     * @return self
     */
    public function addSelect(string $selectName, string $label = '', string $attr = '', bool $displayGroupLabels = true): self
    {
        $attr          = $this->getAttributes($attr); // returns linearised attributes (with ID)
        $arrayValues  = $this->getID($selectName, $attr); // if $attr contains no ID, field ID will be $selectName.
        $id            = $arrayValues['id'];
        $attr          = $arrayValues['attributs']; // if $attr contains an ID, we remove it.
        $isUikitSlimselect = $this->framework === 'uikit' && strpos($attr, 'data-slimselect') !== false;
        if ($this->framework !== 'material' && !$isUikitSlimselect) { // don't add select class if material
            $attr          = $this->addElementClass('select', $selectName, $attr);
        }
        $startWrapper = '';
        $endWrapper   = '';
        $startLabel   = '';
        $endLabel     = '';
        $startCol     = '';
        $endCol       = '';
        $element       = '';

        // form-group wrapper
        $startWrapper = $this->setInputGroup($selectName, 'start', 'elementsWrapper');
        $startWrapper .= $this->addErrorWrapper($selectName, 'start');
        // label
        if (!empty($label)) {
            if ($this->hasLabelWrapper()) {
                $startLabel  .= $this->getLabelCol('start');
            }
            $labelClass = $this->getLabelClass();
            if (strpos($attr, 'form-control-sm')) {
                $labelClass = $this->addClass('col-form-label-sm', $labelClass);
            } elseif (strpos($attr, 'form-control-lg')) {
                $labelClass = $this->addClass('col-form-label-lg', $labelClass);
            }
            $startLabel .= '<label for="' . $id . '"' . $labelClass . '>' . $this->getRequired($label, $attr);
            $endLabel = '</label>';
            if ($this->hasLabelWrapper()) {
                $endLabel   .= $this->getLabelCol('end');
            }
        }
        $startCol .= $this->getElementCol('start', 'select', $label);
        $element .= $this->getErrorInputWrapper($selectName, $label, 'start');
        if ($this->framework === 'bulma') {
            $element .= $this->startElementCustomWrapper($selectName, 'bulma_control');
        }
        $element .= $this->getHtmlElementContent($selectName, 'before', 'outside_wrapper');
        // icons, addons, custom html with addHtml()
        $htmlBefore = $this->getHtmlElementContent($selectName, 'before', 'inside_wrapper');
        $htmlAfter = $this->getHtmlElementContent($selectName, 'after', 'inside_wrapper');
        if (isset($this->inputWrapper[$selectName])) {
            $element .= static::buildElementWrapper($this->inputWrapper[$selectName], 'start'); // input-group (icons, addons)
        }
        if ($this->framework == 'material') {
            if (strpos($htmlBefore, 'icon-before')) {
                $attr = $this->addClass('has-icon-before', $attr);
            }
            if (strpos($htmlAfter, 'icon-after')) {
                $attr = $this->addClass('has-icon-after', $attr);
            }
            if (strpos($htmlBefore, 'addon-before')) {
                $attr = $this->addClass('has-addon-before', $attr);
            }
            if (strpos($htmlAfter, 'addon-after')) {
                $attr = $this->addClass('has-addon-after', $attr);
            }
            if (strpos($attr, 'selectpicker') !== false || strpos($attr, 'data-slimselect') !== false || strpos($attr, 'select2') !== false) {
                $attr = $this->addClass('browser-default no-autoinit', $attr);
            }
        }
        if (strpos($attr, 'selectpicker') !== false) {
            if (!strpos('data-icon-base', $attr)) {
                $attr .= ' data-icon-base="fa"';
            }
            if (!strpos('data-tick-icon', $attr)) {
                $attr .= ' data-tick-icon="fa-check"';
            }
            if (!strpos('data-show-tick', $attr)) {
                $attr .= ' data-show-tick=true';
            }
        }
        $element .= $htmlBefore;
        if ($this->framework === 'bulma') {
            $element .= $this->startElementCustomWrapper($selectName, 'bulma_control');
            $customWrapperConf = 'bulma_select';
            if (strpos($attr, 'multiple')) {
                $customWrapperConf = 'bulma_select_multiple';
            }
            $element .= $this->startElementCustomWrapper($selectName, $customWrapperConf);
        }
        $ariaLabel = $this->getAriaLabel($label, $attr);
        $element .= '<select id="' . $id . '" name="' . $selectName . '" ' . $attr . $ariaLabel . '>';
        $nbreOptions = 0;
        if (isset($this->option[$selectName])) {
            $nbreOptions = count($this->option[$selectName]);
        }
        for ($i = 0; $i < $nbreOptions; $i++) {
            if (isset($this->option[$selectName][$i])) {
                $txt         = $this->option[$selectName][$i]['txt'];
                $value       = $this->option[$selectName][$i]['value'];
                $optionAttr = $this->option[$selectName][$i]['attributs'];
                $optionAttr = $this->getAttributes($optionAttr);
                $element     .= '<option value="' . $value . '"';
                $optionAttr = $this->getCheckedOrSelected($selectName, $value, $optionAttr, 'select');
                $element     .= ' ' . $optionAttr . '>' . $txt . '</option>';
            }
        }
        if (isset($this->groupName[$selectName])) {
            foreach ($this->groupName[$selectName] as $groupName) {
                $nbreOptions = count($this->option[$selectName][$groupName]);
                $groupLabel = '';
                if ($displayGroupLabels) {
                    $groupLabel = ' label="' . $groupName . '"';
                }
                $element .= '<optgroup' . $groupLabel . '>';
                for ($i = 0; $i < $nbreOptions; $i++) {
                    $txt         = $this->option[$selectName][$groupName][$i]['txt'];
                    $value       = $this->option[$selectName][$groupName][$i]['value'];
                    $optionAttr = $this->option[$selectName][$groupName][$i]['attributs'];
                    $optionAttr = $this->getAttributes($optionAttr);
                    $element     .= '<option value="' . $value . '"';
                    $optionAttr = $this->getCheckedOrSelected($selectName, $value, $optionAttr, 'select');
                    $element     .= ' ' . $optionAttr . '>' . $txt . '</option>';
                }
                $element .= '</optgroup>';
            }
        }
        $element .= '</select>';

        if ($this->framework === 'bulma') {
            $element .= $this->endElementCustomWrapper();
            $element .= $this->endElementCustomWrapper();
        }
        $element .= $htmlAfter;
        if ($this->framework === 'bulma') {
            $element .= $this->endElementCustomWrapper();
        }
        if (isset($this->inputWrapper[$selectName])) {
            $element .= static::buildElementWrapper($this->inputWrapper[$selectName], 'end');
        }
        $element .= $this->getHtmlElementContent($selectName, 'after', 'outside_wrapper');
        $element .= $this->getErrorInputWrapper($selectName, $label, 'end');
        $element .= $this->getError($selectName);
        $endCol .= $this->getElementCol('end', 'select', $label);
        $endWrapper = $this->addErrorWrapper($selectName, 'end');
        $endWrapper .= $this->setInputGroup($selectName, 'end', 'elementsWrapper'); // end form-group
        if (strpos($attr, 'selectpicker') !== false) {
            $bootstrapVersion = 5;
            if ($this->framework == 'bs4') {
                $bootstrapVersion = 4;
            }
            $this->addPlugin('bootstrap-select', '#' . $this->formId . ' select[name=\'' . $selectName . '\']', 'default', ['bootstrapversion' => $bootstrapVersion]);
        } elseif (strpos($attr, 'data-slimselect') !== false) {
            $this->addPlugin('slimselect', '#' . $this->formId . ' select[data-slimselect=true]');
        } elseif (strpos($attr, 'select2') !== false) {
            $theme = 'clean';
            $language = 'en';
            if ($this->framework == 'material') {
                $theme = 'material';
            }
            if (preg_match('`data-language="([^"]+)`', $attr, $out)) {
                $language = $out[1];
            }
            $this->addPlugin('select2', '.select2', 'default', ['theme' => $theme, 'language' => $language]);
        }

        // output
        $this->html .= $this->outputElement($startWrapper, $endWrapper, $startLabel, $endLabel, $startCol, $endCol, $element, $this->options->getBool('wrapElementsIntoLabels'));
        $this->registerField($selectName, $attr);

        return $this;
    }

    /**
     * adds a country select list with flags
     * @param string  $selectName
     * @param string $label        (Optional) The select label
     * @param string $attr         (Optional)  Can be any HTML input attribute or js event.
     *                             attributes must be listed separated with commas.
     *                             If you don't specify any ID as attr, the ID will be the name of the select.
     *                             Example : class=my-class
     * @param array<string, mixed>  $userOptions (Optional) :
     *                             plugin          : slimselect | select2 | bootstrap-select    Default: 'slimselect'
     *                             lang            : MUST correspond to one subfolder of [$this->pluginsUrl]countries/country-list/country/cldr/
     *                             *** for example 'en', or 'fr_FR'                 Default : 'en'
     *                             flags           : true or false.                 Default : true
     *                             *** displays flags into option list
     *                             flag_size       : 16 or 32                       Default : 32
     *                             return_value    : 'name' or 'code'               Default : 'name'
     *                             *** type of the value that will be returned
     * @return self
     */
    public function addCountrySelect(string $selectName, string $label = '', string $attr = '', array $userOptions = []): self
    {

        /* define options*/

        $options = [
            'plugin'       => 'slimselect',
            'lang'         => 'en',
            'flags'        => true,
            'flag_size'    => 32,
            'return_value' => 'name',
        ];
        $options = \array_merge($options, $userOptions);
        $class = '';
        if (preg_match('`class(\s)?=(\s)?([^,]+)`', $attr, $out)) {
            $class = $out[3] . ' ';
        }
        if ($options['plugin'] == 'select2') {
            $class .= 'select2country ' . $this->options->getString('elementsClass');
        } elseif ($options['plugin'] == 'bootstrap-select') {
            $class .= 'selectpicker ' . $this->options->getString('elementsClass');
        }
        if ($this->framework == 'material') {
            $class .= ' no-autoinit';
        }
        $xmlNode = 'default';
        if ($options['flags']) {
            $class .= ' f' . $options['flag_size'];
            $xmlNode = 'countries-flags-' . $options['flag_size'];
        }
        $dataAttr = '';
        if ($options['plugin'] == 'bootstrap-select') {
            $dataAttr .= ' data-live-search="true" data-show-tick="true"';
        } elseif ($options['plugin'] == 'slimselect') {
            $dataAttr .= ' data-slimselect=true';
            if ($options['flags']) {
                $dataAttr .= ' data-flag-size=' . $options['flag_size'];
            }
        }
        $countries    = include $this->pluginsPath . 'countries/country-list/country/cldr/' . $options['lang'] . '/country.php';
        $attr         = $this->getAttributes($attr); // returns linearised attributes (with ID)
        $arrayValues = $this->getID($selectName, $attr); // if $attr contains no ID, field ID will be $selectName.
        $id           = $arrayValues['id'];
        $attr         = $arrayValues['attributs']; // if $attr contains an ID, we remove it.
        $attr         = $this->removeAttribute('class', $attr);
        $startWrapper = '';
        $endWrapper   = '';
        $startLabel   = '';
        $endLabel     = '';
        $startCol     = '';
        $endCol       = '';
        $element       = '';
        $startWrapper = $this->setInputGroup($selectName, 'start', 'elementsWrapper');
        $startWrapper .= $this->addErrorWrapper($selectName, 'start');
        if ($options['plugin'] == 'slimselect') {
            $this->addPlugin('slimselect', '#' . $id, 'default', ['pluginsUrl' => $this->pluginsUrl]);
        } elseif ($options['plugin'] == 'select2') {
            $theme = 'clean';
            if ($this->framework == 'material') {
                $theme = 'material';
            }
            $this->addPlugin('select2', '#' . str_replace('[]', '', $selectName), $xmlNode, ['theme' => $theme]);
        } else {
            $this->addPlugin('bootstrap-select', '.selectpicker', $xmlNode);
        }
        // label
        if (!empty($label)) {
            if ($this->hasLabelWrapper()) {
                $startLabel  .= $this->getLabelCol('start');
            }
            $startLabel .= '<label for="' . $id . '"' . $this->getLabelClass() . '>' . $this->getRequired($label, $attr);
            $endLabel = '</label>';
            if ($this->hasLabelWrapper()) {
                $endLabel   .= $this->getLabelCol('end');
            }
        }
        $startCol .= $this->getElementCol('start', 'select', $label);
        $element .= $this->getErrorInputWrapper($selectName, $label, 'start');
        if ($this->framework === 'bulma') {
            $element .= $this->startElementCustomWrapper($selectName, 'bulma_select');
        }
        $element .= $this->getHtmlElementContent($selectName, 'before');
        $ariaLabel = $this->getAriaLabel($label, $attr);
        $element .= '<select name="' . $selectName . '" id="' . $id . '" class="' . $class . '"' . $dataAttr . ' ' . $attr . $ariaLabel . '>';
        $optionList = '';
        if ($options['return_value'] == 'name') {
            foreach ($countries as $countryCode => $countryName) {
                $optionList .= '<option value="' . $countryName . '" class="flag ' . mb_strtolower($countryCode) . '"';
                $optionAttr = $this->getCheckedOrSelected(mb_strtolower($selectName), $countryName, '', 'select');
                $optionList .= ' ' . $optionAttr . '>' . $countryName . '</option>';
            }
        } else {
            foreach ($countries as $countryCode => $countryName) {
                $optionList .= '<option value="' . $countryCode . '" class="flag ' . mb_strtolower($countryCode) . '"';
                $optionAttr = $this->getCheckedOrSelected(mb_strtolower($selectName), $countryCode, '', 'select');
                $optionList .= ' ' . $optionAttr . '>' . $countryName . '</option>';
            }
        }
        $element .= $optionList;
        $element .= '</select>';
        $element .= $this->getHtmlElementContent($selectName, 'after');
        if ($this->framework === 'bulma') {
            $element .= $this->endElementCustomWrapper();
        }
        $element .= $this->getError($selectName);
        $element .= $this->getErrorInputWrapper($selectName, $label, 'end');
        $endCol .= $this->getElementCol('end', 'select', $label);
        $endWrapper = $this->addErrorWrapper($selectName, 'end');
        $endWrapper .= $this->setInputGroup($selectName, 'end', 'elementsWrapper'); // end form-group

        // output
        $this->html .= $this->outputElement($startWrapper, $endWrapper, $startLabel, $endLabel, $startCol, $endCol, $element, $this->options->getBool('wrapElementsIntoLabels'));
        $this->registerField($selectName, $attr);

        return $this;
    }

    /**
     * adds an hours:minutes select dropdown
     * @param  string $selectName
     * @param  string $label       (Optional) The select label
     * @param  string $attr        (Optional) Can be any HTML input attribute or js event.
     *                             attributes must be listed separated with commas.
     *                             If you don't specify any ID as attr, the ID will be the name of the select.
     *                             Example : class=my-class
     * @param array<string, mixed>  $userOptions (Optional) :
     *                             min       : the minimum time in hour:minutes                   Default: '00:00'
     *                             max       : the maximum time in hour:minutes                   Default: '23:59'
     *                             step      : the step interval in minutes between each option   Default: 1
     *                             format    : '12hours' or '24hours'                             Default : '24hours'
     *                             display_separator : the displayed separator character between hours and minutes  Default : 'h'
     *                             value_separator   : the value separator character between hours and minutes  Default : ':'
     * @return self
     */
    public function addTimeSelect(string $selectName, string $label = '', string $attr = '', array $userOptions = []): self
    {
        /* define options*/
        $options = [
            'min'                => '00:00',
            'max'                => '23:59',
            'step'               => 1,
            'format'             => '24hours',
            'display_separator'  => 'h',
            'value_separator'    => ':'
        ];
        $options = \array_merge($options, $userOptions);
        $minXp = \explode(':', $options['min']);
        $min = [
            'hour'   => (int) $minXp[0],
            'minute' => (int) $minXp[1]
        ];
        $maxXp = \explode(':', $options['max']);
        $max = [
            'hour'   => (int) $maxXp[0],
            'minute' => (int) $maxXp[1]
        ];

        $minMinutes = $min['hour'] * 60 + $min['minute'];
        $maxMinutes = $max['hour'] * 60 + $max['minute'];

        for ($i = $minMinutes; $i <= $maxMinutes; $i += $options['step']) {
            $hours = 0;
            $currentMinutes = $i;
            while ($currentMinutes >= 60) {
                $hours += 1;
                $currentMinutes -= 60;
            }
            $currentMinutes_pad = str_pad((string) $currentMinutes, 2, '0', STR_PAD_LEFT);
            if ($options['format'] === '12hours') {
                if ($hours < 13) {
                    $optValue = str_pad((string) $hours, 2, '0', STR_PAD_LEFT) . $options['value_separator'] . $currentMinutes_pad . ' am';
                } else {
                    $optValue = str_pad((string) ($hours - 12), 2, '0', STR_PAD_LEFT) . $options['value_separator'] . $currentMinutes_pad . ' pm';
                }
            } else {
                $optValue = str_pad((string) $hours, 2, '0', STR_PAD_LEFT) . $options['value_separator'] . $currentMinutes_pad;
            }
            $this->addOption($selectName, $optValue, str_replace($options['value_separator'], $options['display_separator'], $optValue));
        }

        $this->addSelect($selectName, $label, $attr);

        return $this;
    }

    /**
     * Adds checkbox to $groupName
     *
     * @param string $groupName The checkbox button groupname
     * @param string $label      The checkbox label
     * @param mixed $value      The checkbox value
     * @param string $attr       The checkbox attributes
     * @return self
     */
    public function addCheckbox(string $groupName, string $label, $value, string $attr = ''): self
    {
        if ($this->framework == 'material') {
            $this->checkbox[$groupName]['label'][] = '<span>' . $label . '</span>';
        } else {
            $this->checkbox[$groupName]['label'][] = $label;
        }
        $this->checkbox[$groupName]['value'][]     = $value;
        $this->checkbox[$groupName]['attr'][]      = $attr;

        return $this;
    }

    /**
     * Prints checkbox group.
     *
     * @param string $groupName The checkbox group name (will be converted to an array of indexed value)
     * @param string $label      (Optional) The checkbox group label
     * @param bool   $inline     (Optional) True or false.
     *                           Default is true.
     * @param string $attr       (Optional) Can be any HTML input attribute or js event.
     *                           attributes must be listed separated with commas.
     *                           Example : class=my-class
     * @return self
     */
    public function printCheckboxGroup(string $groupName, string $label = '', bool $inline = true, string $attr = ''): self
    {
        $startWrapper = '';
        $endWrapper   = '';
        $startLabel   = '';
        $endLabel     = '';
        $startCol     = '';
        $endCol       = '';
        $element       = '';
        $startWrapper = $this->setInputGroup($groupName, 'start', 'elementsWrapper');
        if ($this->framework === 'tailwind' && $this->layout === 'vertical' && $inline) {
            $startWrapper .= $this->startElementCustomWrapper($groupName, 'tailwind_vertical_radio_checkbox_inline');
        }
        $startWrapper .= $this->addErrorWrapper($groupName, 'start');
        $hasSwitch    = false;
        $attr          = $this->getAttributes($attr); // returns linearised attributes (with ID)
        if (strpos($attr, 'data-lcswitch') !== false) {
            $hasSwitch = true;
            $lcswitchGlobal = [];
            $lcswitchAttributes = ['data-ontext', 'data-offtext', 'data-oncolor', 'data-oncss'];
            foreach ($lcswitchAttributes as $lcswitchAttr) {
                if (strpos($attr, $lcswitchAttr) !== false) {
                    $lcswitchGlobal[$lcswitchAttr] = $this->getAttribute($lcswitchAttr, $attr);
                }
            }
        }
        if (!empty($label)) {
            $attr = $this->addClass('main-label', $attr);
            if ($inline) {
                $attr = $this->addClass('main-label-inline', $attr);
            }
            if ($hasSwitch) {
                $attr = str_replace('data-lcswitch', 'data-has-switch', $attr);
            }
            if ($this->layout == 'horizontal') {
                if (!$this->options->getBool('horizontalLabelWrapper')) {
                    $class     = $this->options->getString('horizontalLabelCol') . ' ' . $this->options->getString('horizontalLabelClass');
                    $attr      = $this->addClass($class, $attr);
                    $startLabel = '<label ' . $this->removeAttribute('required', $attr) . '>' . $this->getRequired($label, $attr);
                    $endLabel = '</label>';
                } else {
                    // wrap label into div with horizontalLabelClass
                    $class        = $this->options->getString('horizontalLabelClass');
                    $attr         = $this->addClass($class, $attr);
                    $startLabel  = $this->getLabelCol('start');
                    $startLabel .= '<label ' . $this->removeAttribute('required', $attr) . '>' . $this->getRequired($label, $attr);
                    $endLabel    = '</label>';
                    $endLabel   .= $this->getLabelCol('end');
                }
            } else {
                if (!$this->options->getBool('verticalLabelWrapper')) {
                    $class     = $this->options->getString('verticalLabelClass');
                    $attr      = $this->addClass($class, $attr);
                    $startLabel = '<label ' . $this->removeAttribute('required', $attr) . '>' . $this->getRequired($label, $attr);
                    $endLabel = '</label>';
                } else {
                    // wrap label into div with verticalLabelClass
                    $startLabel  = $this->getLabelCol('start');
                    $startLabel .= '<label ' . $this->removeAttribute('required', $attr) . '>' . $this->getRequired($label, $attr);
                    $endLabel    = '</label>';
                    $endLabel   .= $this->getLabelCol('end');
                }
            }
        }
        $startCol .= $this->getElementCol('start', 'checkbox', $label);
        if ($this->framework === 'tailwind' && $this->layout === 'horizontal' && $inline) {
            $startCol .= $this->startElementCustomWrapper($groupName, 'tailwind_horizontal_radio_checkbox_inline');
        }
        $element .= $this->getErrorInputWrapper($groupName, $label, 'start');
        if ($this->framework === 'bulma') {
            if ($inline) {
                $element .= $this->startElementCustomWrapper($groupName, 'bulma_field_multiline');
            } else {
                $element .= $this->startElementCustomWrapper($groupName, 'bulma_field');
            }
        }
        $element .= $this->getHtmlElementContent($groupName, 'before');
        for ($i = 0; $i < count($this->checkbox[$groupName]['label']); $i++) {
            $checkboxStartLabel   = '';
            $checkboxEndLabel     = '';
            $checkboxInput         = '';
            if (!empty($this->options->getString('checkboxWrapper')) && $inline !== true) {
                $element .= $this->options->getStartWrapper('checkbox');
            } elseif (!empty($this->options->getString('inlineCheckboxWrapper')) && $inline) {
                $element .= $this->options->getStartWrapper('inlineCheckbox');
            }
            $checkboxLabel = $this->checkbox[$groupName]['label'][$i];
            $checkboxValue = $this->checkbox[$groupName]['value'][$i];
            $checkboxAttr = $this->getAttributes($this->checkbox[$groupName]['attr'][$i]);
            if ($this->framework == 'bs4' || $this->framework == 'bs5') {
                $checkboxAttr = $this->addClass('form-check-input', $checkboxAttr);
            }
            // lcswitch plugin
            if ($hasSwitch !== false && isset($lcswitchAttributes)) {
                // add global lcswitch attributes if no individuals found
                foreach ($lcswitchAttributes as $lcswitchAttr) {
                    if (strpos($checkboxAttr, $lcswitchAttr) === false && isset($lcswitchGlobal[$lcswitchAttr]) && $lcswitchGlobal[$lcswitchAttr] !== null) {
                        $checkboxAttr = $this->addAttribute($lcswitchAttr, $lcswitchGlobal[$lcswitchAttr], $checkboxAttr);
                    }
                }
                $this->addPlugin('lcswitch', '#' . $groupName . '_' . $i);
            }
            $checkboxStartLabel = '<label for="' . $groupName . '_' . $i . '"' . $this->getLabelClass('checkbox', $inline) . '>';
            $checkboxInput = '<input type="checkbox" id="' . $groupName . '_' . $i . '" name="' . $groupName . '[]" value="' . $checkboxValue . '"';
            $checkboxAttr = $this->getCheckedOrSelected($groupName, $checkboxValue, $checkboxAttr, 'checkbox');
            $checkboxInput .= ' ' . $checkboxAttr . '>';
            $checkboxEndLabel = $checkboxLabel . '</label>';

            if ($this->options->getBool('wrapCheckboxesIntoLabels')) {
                $element .= $checkboxStartLabel . $checkboxInput . $checkboxEndLabel;
            } else {
                $element .= $checkboxInput . $checkboxStartLabel . $checkboxEndLabel;
            }
            if (!empty($this->options->getString('checkboxWrapper')) && !$inline) {
                $element .= $this->options->getEndWrapper('checkbox');
            } elseif (!empty($this->options->getString('inlineCheckboxWrapper')) && $inline) {
                $element .= $this->options->getEndWrapper('inlineCheckbox');
            }
        }
        $element .= $this->getHtmlElementContent($groupName, 'after');
        if ($this->framework === 'bulma') {
            $element .= $this->endElementCustomWrapper();
        }
        $element .= $this->getError($groupName);
        $element .= $this->getErrorInputWrapper($groupName, $label, 'end');

        if ($this->framework === 'tailwind' && $this->layout === 'horizontal' && $inline) {
            $endCol .= $this->endElementCustomWrapper();
        }

        $endCol .= $this->getElementCol('end', 'checkbox', $label);
        $endWrapper = $this->addErrorWrapper($groupName, 'end');
        if ($this->framework === 'tailwind' && $this->layout === 'vertical' && $inline) {
            $endWrapper .= $this->endElementCustomWrapper();
        }
        $endWrapper .= $this->setInputGroup($groupName, 'end', 'elementsWrapper'); // end form-group
        $this->html .= $this->outputElement($startWrapper, $endWrapper, $startLabel, $endLabel, $startCol, $endCol, $element, false);

        $this->registerField($groupName, $attr);

        return $this;
    }

    /**
     * Adds radio button to $groupName element
     *
     * @param string $groupName The radio button groupname
     * @param string $label      The radio button label
     * @param mixed $value       The radio button value
     * @param string $attr       (Optional) Can be any HTML input attribute or js event.
     *                           attributes must be listed separated with commas.
     *                           Example : checked=checked
     * @return self
     */
    public function addRadio(string $groupName, string $label, $value, string $attr = ''): self
    {
        if ($this->framework == 'material') {
            $this->radio[$groupName]['label'][]  = '<span>' . $label . '</span>';
        } else {
            $this->radio[$groupName]['label'][]  = $label;
        }
        $this->radio[$groupName]['value'][]  = $value;
        $this->radio[$groupName]['attr'][]  = $attr;

        return $this;
    }

    /**
     * Prints radio buttons group.
     *
     * @param string $groupName The radio button group name
     * @param string $label      (Optional) The radio buttons group label
     * @param bool   $inline     (Optional) True or false.
     *                           Default is true.
     * @param string $attr       (Optional) Can be any HTML input attribute or js event.
     *                           attributes must be listed separated with commas.
     *                           Example : class=my-class
     * @return self
     */
    public function printRadioGroup(string $groupName, string $label = '', bool $inline = true, string $attr = ''): self
    {
        $formId       = $this->formId;
        $startWrapper = '';
        $endWrapper   = '';
        $startLabel   = '';
        $endLabel     = '';
        $startCol     = '';
        $endCol       = '';
        $element       = '';
        $startWrapper = $this->setInputGroup($groupName, 'start', 'elementsWrapper');
        if ($this->framework === 'tailwind' && $this->layout === 'vertical' && $inline) {
            $startWrapper .= $this->startElementCustomWrapper($groupName, 'tailwind_vertical_radio_checkbox_inline');
        }
        $startWrapper .= $this->addErrorWrapper($groupName, 'start');
        $hasSwitch    = false;
        $attr          = $this->getAttributes($attr); // returns linearised attributes (with ID)
        if (strpos($attr, 'data-lcswitch') !== false) {
            $hasSwitch = true;
            $lcswitchGlobal = [];
            $lcswitchAttributes = ['data-ontext', 'data-offtext', 'data-oncolor', 'data-oncss'];
            foreach ($lcswitchAttributes as $lcswitchAttr) {
                if (strpos($attr, $lcswitchAttr) !== false) {
                    $lcswitchGlobal[$lcswitchAttr] = $this->getAttribute($lcswitchAttr, $attr);
                }
            }
        }
        if (!empty($label)) {
            $attr = $this->addClass('main-label', $attr);
            if ($inline) {
                $attr = $this->addClass('main-label-inline', $attr);
            }
            if ($hasSwitch) {
                $attr = str_replace('data-lcswitch', 'data-has-switch', $attr);
            }
            if ($this->layout == 'horizontal') {
                if (!$this->options->getBool('horizontalLabelWrapper')) {
                    $class     = $this->options->getString('horizontalLabelCol') . ' ' . $this->options->getString('horizontalLabelClass');
                    $attr      = $this->addClass($class, $attr);
                    $startLabel = '<label ' . $this->removeAttribute('required', $attr) . '>' . $this->getRequired($label, $attr);
                    $endLabel = '</label>';
                } else {
                    // wrap label into div with horizontalLabelClass
                    $class        = $this->options->getString('horizontalLabelClass');
                    $attr         = $this->addClass($class, $attr);
                    $startLabel  = $this->getLabelCol('start');
                    $startLabel .= '<label ' . $this->removeAttribute('required', $attr) . '>' . $this->getRequired($label, $attr);
                    $endLabel    = '</label>';
                    $endLabel   .= $this->getLabelCol('end');
                }
            } else {
                if (!$this->options->getBool('verticalLabelWrapper')) {
                    $class     = $this->options->getString('verticalLabelClass');
                    $attr      = $this->addClass($class, $attr);
                    $startLabel = '<label ' . $this->removeAttribute('required', $attr) . '>' . $this->getRequired($label, $attr);
                    $endLabel = '</label>';
                } else {
                    // wrap label into div with verticalLabelClass
                    $startLabel  = $this->getLabelCol('start');
                    $startLabel .= '<label ' . $this->removeAttribute('required', $attr) . '>' . $this->getRequired($label, $attr);
                    $endLabel    = '</label>';
                    $endLabel   .= $this->getLabelCol('end');
                }
            }
        }
        $required = '';
        if (preg_match('`required`', $attr)) {
            $required = ' required';
        }
        $startCol .= $this->getElementCol('start', 'radio', $label);
        if ($this->framework === 'tailwind' && $this->layout === 'horizontal' && $inline) {
            $startCol .= $this->startElementCustomWrapper($groupName, 'tailwind_horizontal_radio_checkbox_inline');
        }
        $element .= $this->getErrorInputWrapper($groupName, $label, 'start');
        if ($this->framework === 'bulma') {
            if ($inline) {
                $element .= $this->startElementCustomWrapper($groupName, 'bulma_field_multiline');
            } else {
                $element .= $this->startElementCustomWrapper($groupName, 'bulma_field');
            }
        }
        $element .= $this->getHtmlElementContent($groupName, 'before');
        if (isset($this->inputWrapper[$groupName])) {
            $element .= static::buildElementWrapper($this->inputWrapper[$groupName], 'start'); // input-group
        }
        for ($i = 0; $i < count($this->radio[$groupName]['label']); $i++) {
            $radioStartLabel   = '';
            $radioEndLabel     = '';
            $radioInput         = '';
            if (!empty($this->options->getString('radioWrapper')) && $inline !== true) {
                $element .= $this->options->getStartWrapper('radio');
            } elseif (!empty($this->options->getString('inlineRadioWrapper')) && $inline) {
                $element .= $this->options->getStartWrapper('inlineRadio');
            }
            $radioLabel  = $this->radio[$groupName]['label'][$i];
            $radioValue  = $this->radio[$groupName]['value'][$i];
            $radioAttr   = $this->getAttributes($this->radio[$groupName]['attr'][$i]); // returns linearised attributes (with ID)
            if ($this->framework == 'material') {
                $radioAttr = $this->addClass('with-gap', $radioAttr);
            } elseif ($this->framework == 'bs4' || $this->framework == 'bs5') {
                $radioAttr = $this->addClass('form-check-input', $radioAttr);
            }
            // lcswitch plugin
            if ($hasSwitch !== false && isset($lcswitchAttributes)) {
                // add global lcswitch attributes if no individuals found
                foreach ($lcswitchAttributes as $lcswitchAttr) {
                    if (strpos($radioAttr, $lcswitchAttr) !== false && isset($lcswitchGlobal[$lcswitchAttr]) && $lcswitchGlobal[$lcswitchAttr] !== null) {
                        $radioAttr = $this->addAttribute($lcswitchAttr, $lcswitchGlobal[$lcswitchAttr], $attr);
                    }
                }
                $this->addPlugin('lcswitch', '#' . $groupName . '_' . $i);
            }
            $radioStartLabel .= '<label for="' . $groupName . '_' . $i . '" ' . $this->getLabelClass('radio', $inline) . '>';
            $radioInput .= '<input type="radio" id="' . $groupName . '_' . $i . '" name="' . $groupName . '" value="' . $radioValue . '"';
            if (isset($_SESSION[$formId][$groupName])) {
                if ($_SESSION[$formId][$groupName] == $radioValue) {
                    if (!preg_match('`checked`', $radioAttr)) {
                        $radioInput .= ' checked="checked"';
                    }
                } else { // we remove 'checked' from $radioAttr as user has previously checked another, memorized in session.
                    $radioAttr = $this->removeAttribute('checked', $radioAttr);
                }
            }
            $radioInput .= $required . ' ' . $radioAttr . '>';

            $radioEndLabel = $radioLabel . '</label>';
            if ($this->options->getBool('wrapRadiobtnsIntoLabels')) {
                $element .= $radioStartLabel . $radioInput . $radioEndLabel;
            } else {
                $element .= $radioInput . $radioStartLabel . $radioEndLabel;
            }
            if ($inline !== true) {
                if (!empty($this->options->getString('radioWrapper'))) {
                    $element .= $this->options->getEndWrapper('radio');
                } else {
                    $element .= '<br>';
                }
            } elseif (!empty($this->options->getString('inlineRadioWrapper'))) {
                $element .= $this->options->getEndWrapper('inlineRadio');
            }
        }
        if (isset($this->inputWrapper[$groupName])) {
            $element .= $this->getError($groupName);
            $element .= static::buildElementWrapper($this->inputWrapper[$groupName], 'end'); // end input-group
        }
        $element .= $this->getHtmlElementContent($groupName, 'after');
        if ($this->framework === 'bulma') {
            $element .= $this->endElementCustomWrapper();
        }
        $element .= $this->getError($groupName);
        $element .= $this->getErrorInputWrapper($groupName, $label, 'end');
        if ($this->framework === 'tailwind' && $this->layout === 'horizontal' && $inline) {
            $endCol .= $this->endElementCustomWrapper();
        }
        $endCol .= $this->getElementCol('end', 'radio', $label);
        $endWrapper = $this->addErrorWrapper($groupName, 'end');
        if ($this->framework === 'tailwind' && $this->layout === 'vertical' && $inline) {
            $endWrapper .= $this->endElementCustomWrapper();
        }
        $endWrapper .= $this->setInputGroup($groupName, 'end', 'elementsWrapper'); // end form-group
        $this->html .= $this->outputElement($startWrapper, $endWrapper, $startLabel, $endLabel, $startCol, $endCol, $element, false);

        $this->registerField($groupName, $attr);

        return $this;
    }

    /**
     * Adds a button to the form
     *
     * If $btnGroupName is empty, the button will be automatically displayed.
     * Otherwise, you'll have to call printBtnGroup to display your btnGroup.
     *
     * @param string $type         The html button type
     * @param string $name         The button name
     * @param mixed $value        The button value
     * @param string $text         The button text
     * @param string $attr         (Optional) Can be any HTML input attribute or js event.
     *                             attributes must be listed separated with commas.
     *                             If you don't specify any ID as attr, the ID will be the input's name.
     *                             Example : class=my-class,onclick=alert(\'clicked\');
     * @param string $btnGroupName (Optional) If you wants to group several buttons, group them then call printBtnGroup.
     * @return self
     */
    public function addBtn(string $type, string $name, $value, string $text, string $attr = '', string $btnGroupName = ''): self
    {

        /*  if $btnGroupName isn't empty, we just store values
        *   witch will be called back by printBtnGroup($btnGroupName)
        *   else we store the values in a new array, then call immediately printBtnGroup($btnGroupName)
        */

        if (empty($btnGroupName)) {
            $btnGroupName = 'btn-alone';
            $this->btn[$btnGroupName] = [];
        }

        /* Automagically add Ladda plugin */

        if (preg_match('`data-ladda-button`', $attr)) {
            $this->addPlugin('ladda', 'button[name=\'' . $name . '\']');
        }

        $this->btn[$btnGroupName]['type'][] = $type;
        $this->btn[$btnGroupName]['name'][] = $name;
        $this->btn[$btnGroupName]['value'][] = (string) $value;
        $this->btn[$btnGroupName]['text'][] = $text;
        $this->btn[$btnGroupName]['attr'][] = $attr;

        /*  if $btnGroupName was empty the button is displayed. */

        if ($btnGroupName == 'btn-alone') {
            $this->printBtnGroup($btnGroupName);
        }

        return $this;
    }

    /**
     * Prints buttons group.
     *
     * @param string $btnGroupName The buttons' group name
     * @param string $label        (Optional) The buttons group label
     * @return self
     */
    public function printBtnGroup(string $btnGroupName, string $label = ''): self
    {
        $btnAlone = false;
        $btnName  = '';
        $startWrapper = '';
        $endWrapper   = '';
        $startLabel   = '';
        $endLabel     = '';
        $startCol     = '';
        $endCol       = '';
        $element       = '';
        if ($btnGroupName == 'btn-alone') {
            $btnAlone = true;
            $btnName  = $this->btn[$btnGroupName]['name'][0];
        }
        $startWrapper = $this->setInputGroup($btnName, 'start', 'buttonWrapper');

        // label
        if (!empty($label)) {
            if ($this->hasLabelWrapper()) {
                $startLabel  .= $this->getLabelCol('start');
            }
            $startLabel .= '<label>' . $label;
            $endLabel = '</label>';
            if ($this->hasLabelWrapper()) {
                $endLabel   .= $this->getLabelCol('end');
            }
        }
        $startCol .= $this->getElementCol('start', 'button', $label);
        if (!empty($this->options->getString('btnGroupClass')) && !$btnAlone) {
            $element .= '<div class="' . $this->options->getString('btnGroupClass') . '">';
        }
        $element .= $this->getHtmlElementContent($btnGroupName, 'before');
        if ($btnAlone && isset($this->inputWrapper[$btnName])) {
            $element .= static::buildElementWrapper($this->inputWrapper[$btnName], 'start'); // input-group-btn
        }
        for ($i = 0; $i < count($this->btn[$btnGroupName]['type']); $i++) {
            $btnType     = $this->btn[$btnGroupName]['type'][$i];
            $btnName     = $this->btn[$btnGroupName]['name'][$i];
            $btnValue    = $this->btn[$btnGroupName]['value'][$i];
            $btnText     = $this->btn[$btnGroupName]['text'][$i];
            $btnAttr     = $this->btn[$btnGroupName]['attr'][$i];
            $btnAttr     = $this->getAttributes($btnAttr); // returns linearised attributes (with ID)
            $btnValue    = $this->getValue($btnName, $btnValue);
            if ($this->framework === 'bulma') {
                $element .= '<p class="control">';
            }
            $element .= '<button type="' . $btnType . '" name="' . $btnName . '" value="' . $btnValue . '" ' . $btnAttr . '>' . $btnText . '</button>';
            if ($this->framework === 'bulma') {
                $element .= '</p>';
            }
        }
        if (isset($this->inputWrapper[$btnName])) {
            $element .= $this->getError($btnName);
            $element .= static::buildElementWrapper($this->inputWrapper[$btnName], 'end'); // end input-group-btn
        }
        $element .= $this->getHtmlElementContent($btnGroupName, 'after');
        if (!empty($this->options->getString('btnGroupClass')) && !$btnAlone) {
            $element .= '</div>';
        }
        $endCol .= $this->getElementCol('end', 'button', $label);
        $endWrapper .= $this->setInputGroup($btnName, 'end', 'buttonWrapper');
        $this->html .= $this->outputElement($startWrapper, $endWrapper, $startLabel, $endLabel, $startCol, $endCol, $element, false);

        return $this;
    }
}
