<?php

declare(strict_types=1);

namespace phpformbuilder\traits;

trait Internal
{
    /**
     * Add new class to $attr.(see options).
     *
     * @param  string $newclassname The new class
     * @param  string $attr         The element attributes
     * @return string $attr including new class.
     */
    protected function addClass(string $newclassname, string $attr): string
    {

        /* if $attr already contains a class we keep it and add newclassname */

        if (preg_match('`class="([^"]+)"`', $attr, $out)) {
            $newClass =  'class="' . $out[1] . ' ' . $newclassname . '"';

            return (string) preg_replace('`class="([^"]+)"`', $newClass, $attr, 1);
        } else { // if $attr contains no class we add elementClass
            return $attr . ' class="' . $newclassname . '"';
        }
    }

    /**
     * @param  string $attrName
     * @param  string $attrValue
     * @param  string $attrString
     * @return string attributes with the added one
     */
    protected function addAttribute(string $attrName, string $attrValue, string $attrString): string
    {
        if (empty($attrString)) {
            $attrString = ' ' . $attrName . '="' . $attrValue . '"';
        } else {
            $attrString = ' ' . $attrName . '="' . $attrValue . '" ' . $attrString;
        }

        return $attrString;
    }

    /**
     * Add default element class to $attr.(see options).
     *
     * @param  string $elementType The element type: input|textarea|select
     * @param  string $name         The element name
     * @param  string $attr         The element attributes
     * @return string The element class with the one defined in options added.
     */
    protected function addElementClass(string $elementType, string $name, string $attr): string
    {
        $elClass = $this->options->getString('elementsClass');
        if ($this->framework === 'bs5' && $elementType === 'select' && !strpos($attr, 'selectpicker')) {
            $elClass = 'form-select';
        } elseif ($this->framework === 'bulma') {
            if ($elementType !== 'select') {
                $elClass = $elementType;
            } else {
                $elClass = '';
            }
        } elseif ($this->framework === 'uikit') {
            $elClass = 'uk-' . $elementType;
        }

        /* we retrieve error if any */

        $errorClass = '';
        if (in_array(str_replace('[]', '', $name), array_keys($this->errorFields)) && !empty($this->options->getString('elementsErrorClass'))) {
            $errorClass = ' ' . $this->options->getString('elementsErrorClass');
        }

        /* if $attr already contains a class we keep it and add elementClass */

        $output = '';

        if (preg_match('`class="([^"]+)"`', $attr, $out)) {
            $newClass =  'class="' . $out[1] . ' ' . $elClass . $errorClass . '"';

            $output = preg_replace('`class="([^"]+)"`', $newClass, $attr);
        } else { /* if $attr contains no class we add elementClass */
            if (empty($elClass)) {
                if (empty($errorClass)) {
                    $output = $attr;
                } else {
                    $output = ' class="' . $errorClass . '"';
                }
            } else {
                $output = $attr . ' class="' . $elClass . $errorClass . '"';
            }
        }

        return (string) $output;
    }

    /**
     * Adds warnings class to elements wrappers
     *
     * @param  string $name          The element name
     * @param  string $pos           The position of the wrapper: start|end
     * @return string                The wrapper HTML tag with or without error class
     */
    protected function addErrorWrapper(string $name, string $pos): string
    {
        $errorWrapper = '';
        if (in_array(str_replace('[]', '', $name), array_keys($this->errorFields)) && !empty($this->options->getString('wrapperErrorClass'))) {
            if ($pos == 'start') {
                $errorWrapper = '<div class="' . $this->options->getString('wrapperErrorClass') . '">';
            } else {
                $errorWrapper = '</div>';
            }
        }

        return $errorWrapper;
    }

    /**
     * convert boolean values to string recursively in an associative array
     *
     * @param  array<string, mixed> $array
     * @return array<string, mixed>
     */
    protected function booleanToString(array $array): array
    {
        foreach ($array as $key => $value) {
            if (!is_array($value)) {
                if (is_bool($value)) {
                    if ($value) {
                        $array[$key] = 'true';
                    } else {
                        $array[$key] = 'false';
                    }
                }
            } else {
                $array[$key] = $this->booleanToString($array[$key]);
            }
        }

        return $array;
    }

    /**
     * display various error messages about server or plugins settings
     *
     * @param string $msg
     * @return void
     */
    protected function buildErrorMsg(string $msg)
    {
        $this->errorMsg .= '<div style="line-height:30px;border-radius:5px;border-bottom:1px solid #ac2925;background-color: #c9302c;margin:10px auto;"><p style="color:#fff;text-align:center;font-size:16px;margin:0">' . $msg . '</p></div>';
    }

    /**
     *
     * @param array<int, string> $pluginsFiles
     * @param string $compressedFilePath
     * @param bool $debug
     * @return bool
     */
    private function checkRewriteCombinedFiles(array $pluginsFiles, string $compressedFilePath, bool $debug = false): bool
    {
        $rewriteCombinedFile = false;
        $combinedFileTime = 0;
        if ($this->mode == 'production') {
            if (!file_exists($compressedFilePath)) {
                // if minified combined file doesn't exist
                $rewriteCombinedFile = true;
            } else {
                clearstatcache(true, $_SERVER['SCRIPT_FILENAME']);
                clearstatcache(true, $compressedFilePath);
                $parentFileTime   = filemtime($_SERVER['SCRIPT_FILENAME']);
                $combinedFileTime = filemtime($compressedFilePath);
                if ($parentFileTime >= $combinedFileTime) {
                    $rewriteCombinedFile = true;
                }
                if ($debug) {
                    $willCombine = 'false';
                    if ($rewriteCombinedFile) {
                        $willCombine = true;
                    }

                    echo "<p>parent_file = " . $_SERVER['SCRIPT_FILENAME'] . "</p>";
                    echo "<p>combined_file = $compressedFilePath</p>";
                    echo "<p>parent_file_time = $parentFileTime</p>";
                    echo "<p>combined_file_time = $combinedFileTime</p>";
                    echo "<p>will_combine = $willCombine</p>";
                }
            }
            foreach ($pluginsFiles as $file) {
                if ($rewriteCombinedFile !== true && !preg_match('`www.google.com/recaptcha`', $file)) {
                    // recaptcha is not combined
                    // check if we have to recreate minified combined file
                    $ftime = false;
                    if (function_exists('filemtime')) {
                        $ftime = filemtime(str_replace($this->pluginsUrl, $this->pluginsPath, $file));
                    }
                    if (!$ftime || $ftime > $combinedFileTime) {
                        // if file is newer than combined one
                        $rewriteCombinedFile = true;
                    }
                }
            }
        }

        return $rewriteCombinedFile;
    }

    /**
     * combine css|js files in phpformbuilder/plugins/min/[css|js]
     *
     * @param  string $type                 css|js
     * @param  array<string>  $pluginsFiles
     * @param  string $compressedFilePath the combined file path with filename = $this->pluginsPath . 'min/' . $type . '/' . $this->formId . '.min.' . $type
     * @param string  $inlineStyle
     * @return string $errorMsg - empty if all is ok
     */
    private function combinePluginFiles(string $type, array $pluginsFiles, string $compressedFilePath, string $inlineStyle = ''): string
    {
        $errorMsg = '';
        if (!file_exists($this->pluginsPath . '/min') && !mkdir($this->pluginsPath . '/min')) {
            $errorMsg = 'Unable to create <strong><i>/min</i></strong> folder<br>Try to change permissions (chmod 0755) on your server<br> or set mode to "development": <code>$form->setMode(\'development\')</code>;';
        }
        if (!file_exists($this->pluginsPath . '/min/' . $type) && !mkdir($this->pluginsPath . '/min/' . $type)) {
            $errorMsg = 'Unable to create <strong><i>/min/' . $type . '</i></strong> folder<br>Try to change permissions (chmod 0755) on your server<br> or set mode to "development": <code>$form->setMode(\'development\')</code>;';
        }
        $newFileContent = '';
        foreach ($pluginsFiles as $file) {
            if (strpos($file, 'www.google.com/recaptcha') === false && strpos($file, 'hcaptcha.com') === false) {
                $currentFileContent = php_strip_whitespace(str_replace($this->pluginsUrl, $this->pluginsPath, $file)) . "\n";
                if ($type == 'css') {
                    // workaround to use $this in preg_replace_callback with php < 5.4
                    $self = $this;
                    // convert relative urls to absolute
                    $currentFileContent = (string) preg_replace_callback(
                        '`url\(([^\)]+)\)`',
                        function ($matches) use ($self, $file) {
                            return 'url(' . $self->rel2abs($matches[1], $file) . ')';
                        },
                        $currentFileContent
                    );
                }
                // remove sourcemaps
                $currentFileContent = preg_replace('~//[#@]\s(source(?:Mapping)?URL)=\s*(\S+)~', '', $currentFileContent);
                $newFileContent .= $currentFileContent;
            }
        }
        if ($type == 'css') {
            $newFileContent .= $inlineStyle;
        }

        try {
            //open file for writing
            if (!file_put_contents($compressedFilePath, $newFileContent)) {
                throw new \Exception();
            }
        } catch (\Exception $e) {
            $errorMsg = 'Unable to open ' . $compressedFilePath . ' for writing.<br>Try to change permissions (chmod 0755) on your server<br> or set mode to "development": <code>$form->setMode(\'development\')</code>;';
        }

        return $errorMsg;
    }

    /**
     * Encloses each key of $array with $char
     *
     * @param  string $char  The enclosing character
     * @param  array<string, mixed>  $array The target associative array
     * @return array<string, mixed>
     */
    protected function encloseArrayKeys(string $char, array $array): array
    {
        $newArray = [];
        foreach ($array as $key => $value) {
            $enclosedKey = $char . $key . $char;
            $newArray[$enclosedKey] = $value;
        }

        return $newArray;
    }

    /**
     * end element Custom Wrapper
     *
     * @return string
     */
    protected function endElementCustomWrapper(): string
    {
        return '</div>';
    }

    /**
     * generate token to protect against CSRF
     *
     * @return string $token
     */
    protected function generateToken(): string
    {
        $token = uniqid((string) rand(), true);
        $tokenName = $this->formId;
        $_SESSION[$this->formId][$tokenName . '_token'] = $token;
        $_SESSION[$this->formId][$tokenName . '_token_time'] = time();

        return $token;
    }

    /**
     * Gets element getAriaLabel.
     *
     * @param  string $label The element label
     * @param  string $attr The element attributes
     * @return string returns the element placeholder in $attr if any,
     *                else returns an empty string
     */
    protected function getAriaLabel(string $label, string $attr): string
    {
        if (empty($label) && preg_match('`placeholder="([^"]+)"`', $attr, $out) && !strpos($attr, 'aria-label')) {
            return ' aria-label="' . $out[1] . '"';
        }

        return '';
    }

    /**
     * find any attribute value in $attr
     *
     * @param  string  $search
     * @param  string $attr   Linearised attributes
     *                        Example: size="30"
     *                        required="required"
     * @return ?string The attribute value or null if not found
     */
    protected function getAttribute(string $search, string $attr): ?string
    {
        if (empty($attr)) {
            return null;
        } else {
            // replace protected commas with expression
            $attr = str_replace('\\,', '[comma]', $attr);

            // replace protected equals with expression
            $attr = str_replace('\\=', '[equal]', $attr);

            if (preg_match('`' . $search . '="([^,]+)"`', $attr, $out)) {
                return trim(str_replace(['[comma]', '[equal]'], [',', '='], $out[1]));
            }

            return null;
        }
    }

    /**
     * Returns linearised attributes.
     *
     * @param  string $attr The element attributes
     * @return string Linearised attributes
     *                Example: size=30, required=required => size="30" required="required"
     */
    protected function getAttributes(string $attr): string
    {
        if (empty($attr)) {
            return '';
        } else {
            $cleanAttr = '';

            // replace protected commas with expression
            $attr = str_replace('\\,', '[comma]', $attr);

            // replace protected equals with expression
            $attr = str_replace('\\=', '[equal]', $attr);

            // split with commas
            if ($attr = preg_split('`,`', $attr)) {
                foreach ($attr as $a) {
                    // add quotes
                    if (preg_match('`=`', $a)) {
                        $a = preg_replace('`\s*=\s*`', '="', trim($a)) .  '" ';
                    } else {
                        // no quote for single attributes
                        $a = trim($a) . ' ';
                    }
                    $cleanAttr .= $a;
                }

                // get back protected commas, equals and trim
                $cleanAttr = trim(str_replace(['[comma]', '[equal]'], [',', '='], $cleanAttr));
            }

            return $cleanAttr;
        }
    }

    /**
     * used for chexkboxes | select options only.
     * adds or remove 'checked' or 'selected' according to default / session values.
     *
     * @param  string $fieldName
     * @param  mixed $value
     * @param  string $attr       ex : checked="checked", class="my-class"
     * @param  string $fieldType select | checkbox
     * @return string $attr
     */
    protected function getCheckedOrSelected(string $fieldName, $value, string $attr, string $fieldType): string
    {
        $formId = $this->formId;
        $nameWithoutHook = preg_replace('`(.*)\[\]`', '$1', $fieldName);
        if ($fieldType == 'select') {
            $attrSelected = 'selected';
        } else {
            $attrSelected = 'checked';
        }
        if (isset($_SESSION[$formId][$nameWithoutHook])) {
            if (!is_array($_SESSION[$formId][$nameWithoutHook])) {
                if ($_SESSION[$formId][$nameWithoutHook] == $value) {
                    if (!preg_match('`' . $attrSelected . '`', $attr)) {
                        $attr = $this->addAttribute($attrSelected, $attrSelected, $attr);
                    }
                } else { // we remove 'selected' from $checkboxAttr as user has previously selected another, memorized in session.
                    $attr = $this->removeAttribute($attrSelected, $attr);
                }
            } else {
                if (in_array($value, $_SESSION[$formId][$nameWithoutHook])) {
                    if (!preg_match('`' . $attrSelected . '`', $attr)) {
                        $attr = $this->addAttribute($attrSelected, $attrSelected, $attr);
                    }
                } else { // we remove 'selected' from $attr as user has previously selected another, memorized in session.
                    $attr = $this->removeAttribute('selected', $attr);
                }
            }
        }

        return $attr;
    }

    /**
     * Wrapps element with col if needed (see options).
     *
     * @param  string $pos        'start' or 'end'
     * @param  string $label      The element label
     * @param  string $fieldType input|textarea|select|radio|checkbox|button|recaptcha
     * @return string The html code of the element wrapper.
     */
    protected function getElementCol(string $pos, string $fieldType, string $label = ''): string
    {
        $output = '';

        if ($this->layout == 'horizontal' && !empty($this->options->getString('horizontalElementCol'))) {
            if ($pos == 'start') {
                if (empty($label)) {
                    $output = '<div class="' . trim($this->options->getString('horizontalOffsetCol') . ' ' . $this->options->getString('horizontalElementCol')) . '">';
                } else {
                    $output = '<div class="' . $this->options->getString('horizontalElementCol') . '">';
                }
            } else { // end
                $output = '</div>';
            }
        } elseif ($this->framework == 'foundation' && ($fieldType == 'radio' || $fieldType == 'checkbox' || $fieldType == 'button' || $fieldType == 'recaptcha')) {
            if ($pos == 'start') {
                // foundation checkboxes, radio & button need column wrapper - in both horizontal & vertical forms
                $output = '<div class="' . $this->options->getString('horizontalElementCol') . '">';
            } else { // end
                $output = '</div>';
            }
        }

        return $output;
    }

    /**
     * Adds warnings if the form was posted with errors
     *
     * Warnings are stored in session, and will be displayed
     * even if your form was called back with header function.
     *
     * @param  string $name The element name
     * @return string The html error
     */
    protected function getError(string $name): string
    {
        $noHookName = str_replace('[]', '', $name);
        if (in_array($noHookName, array_keys($this->errorFields))) {
            return '<p class="' . $this->options->getString('textErrorClass') . '">' . $this->errorFields[$noHookName] . '</p>';
        }

        // Default
        return '';
    }

    /**
     * wrap element itself with error div if input is grouped or if $label is empty
     *
     * @param  string $name
     * @param  string $label
     * @param  string $pos   'start' | 'end'
     * @return string div start | end
     */
    protected function getErrorInputWrapper(string $name, string $label, string $pos): string
    {
        $isGrouped = $this->isGrouped($name);
        if (($isGrouped || $label == '') && !empty($this->options->getString('wrapperErrorClass')) && in_array(str_replace('[]', '', $name), array_keys($this->errorFields))) {
            if ($pos == 'start') {
                return '<div class="' . $this->options->getString('wrapperErrorClass') . '">';
            }

            return '</div>';
        }

        return '';
    }

    /**
     * Gets html code to insert just berore or after the element
     *
     * @param  string $name                    The element name
     * @param  string $pos                     'start' or 'end'
     * @param  string $posRelativeToWrapper 'inside_wrapper' or 'outside_wrapper' (input groups are inside wrapper, help blocks are outside). Only for inputs.
     * @return string $return                  The html code to insert just before or after the element, inside or outside element wrapper
     */
    protected function getHtmlElementContent(string $name, string $pos, string $posRelativeToWrapper = ''): string
    {
        $return = '';
        if (isset($this->htmlElementContent[$name][$pos])) {
            for ($i = 0; $i < count($this->htmlElementContent[$name][$pos]); $i++) {
                $html = $this->htmlElementContent[$name][$pos][$i];
                if (empty($posRelativeToWrapper)) {
                    $return .= $html;
                } else {
                    $isAddon = false;
                    $addonClazz = [
                        'input-group-',
                        'addon-',
                        'uk-form-icon'
                    ];
                    foreach ($addonClazz as $clz) {
                        if (strpos($html, $clz) !== false) {
                            $isAddon = true;
                        }
                    }
                    if ($posRelativeToWrapper == 'outside_wrapper' && !$isAddon) {
                        $return .= $html;
                    } elseif ($posRelativeToWrapper == 'inside_wrapper' && $isAddon) {
                        $return .= $html;
                    }
                }
            }

            return $return;
        } else {
            return '';
        }
    }

    /**
     * Gets element ID.
     *
     * @param  string $name The element name
     * @param  string $attr The element attributes
     * @return array<string,string> The element ID and attributes
     */
    protected function getID(string $name, string $attr): array
    {
        if (empty($attr)) {  //
            $arrayValues['id'] = str_replace('`\[\]`', '', $name); // if $name is an array, we delete '[]'
            $arrayValues['attributs'] = '';
        } else {
            if (preg_match('`id="([a-zA-Z0-9_-]+)"`', $attr, $out)) {
                $arrayValues['id'] = $out[1];
                $arrayValues['attributs'] = (string) preg_replace('`id="([a-zA-Z0-9_-]+)"`', '', $attr);
            } else {
                $arrayValues['id'] = str_replace('`\[\]`', '', $name);
                $arrayValues['attributs'] = $attr;
            }
        }

        return $arrayValues;
    }

    /**
     * Gets css or js files needed for js plugins
     *
     * @param  string $type 'css' or 'js'
     * @return void
     */
    protected function registerIncludes(string $type): void
    {
        foreach ($this->jsPlugins as $pluginName) {
            for ($i = 0; $i < count($this->jsContent[$pluginName]); $i++) {
                $jsConfig      = $this->jsContent[$pluginName][$i]; // default, custom, ...
                $pluginSettings = $this->pluginSettings[$pluginName][$i];

                if (file_exists(dirname(__FILE__, 4) . '/plugins-config-custom/' . $pluginName . '.xml')) {
                    // if custom config xml file
                    $xml = simplexml_load_file(dirname(__FILE__, 4) . '/plugins-config-custom/' . $pluginName . '.xml');

                    // if node doesn't exist, fallback to default xml
                    if (!isset($xml->{$jsConfig})) {
                        $xml = simplexml_load_file(dirname(__FILE__, 4) . '/plugins-config/' . $pluginName . '.xml');
                    }
                } else {
                    $xml = simplexml_load_file(dirname(__FILE__, 4) . '/plugins-config/' . $pluginName . '.xml');
                }

                if ($xml instanceof \SimpleXMLElement) {
                    // if custom include path doesn't exist, we keep default path
                    $path = '/root/' . $jsConfig . '/includes/' . $type . '/file';
                    if (!$xml->xpath($path)) {
                        $path = '/root/default/includes/' . $type . '/file';
                    }
                    $files = $xml->xpath($path);
                    if (is_array($files)) {
                        if (!isset($this->cssIncludes[$pluginName])) {
                            $this->cssIncludes[$pluginName] = [];
                        }
                        if (!isset($this->jsIncludes[$pluginName])) {
                            $this->jsIncludes[$pluginName] = [];
                        }
                        if (!empty($pluginSettings)) {
                            $pluginSettings = $this->encloseArrayKeys('%', $pluginSettings);
                        }
                        foreach ($files as $file) {
                            $file = (string) $file;
                            if (!empty($pluginSettings) && \strpos($file, '%') !== false) {
                                foreach ($pluginSettings as $key => $value) {
                                    if (is_array($value)) {
                                        $value =  json_encode($value);
                                    }
                                    // echo $key . ' - ' . $file . ' => ' . $value . '<br>';
                                    $file = str_replace($key, $value, $file);
                                }
                            }
                            if ($type == 'css' && !in_array($file, $this->cssIncludes[$pluginName])) {
                                $this->cssIncludes[$pluginName][] = (string) $file;
                            } elseif ($type == 'js' && !in_array($file, $this->jsIncludes[$pluginName])) {
                                $this->jsIncludes[$pluginName][] = (string) $file;

                                /* add framework & language includes for formvalidation plugin */

                                if ($pluginName == 'formvalidation') {
                                    $frameworks = [
                                        'bs4'         => 'Bootstrap',
                                        'bs5'         => 'Bootstrap5',
                                        'bulma'       => 'Bulma',
                                        'foundation'  => 'Foundation',
                                        'material'    => 'Materialize',
                                        'uikit'       => 'Uikit'
                                    ];
                                    if (array_key_exists($this->framework, $frameworks)) {
                                        $f = $this->framework;

                                        // add framework to plugin_settings in the xml js_code
                                        $this->pluginSettings[$pluginName][$i]['FRAMEWORK-LOWERCASE'] = strtolower(str_replace('5', '', $frameworks[$f]));
                                        $this->pluginSettings[$pluginName][$i]['FRAMEWORK']   = $frameworks[$f];
                                        $this->pluginSettings[$pluginName][$i]['PLUGINS_URL'] = $this->pluginsUrl;
                                    }
                                    $lang = 'en_US'; // default
                                    if (array_key_exists('language', $this->pluginSettings[$pluginName][$i])) {
                                        $lang = $this->pluginSettings[$pluginName][$i]['language'];
                                    }
                                    $file = 'formvalidation/js/locales/' . $lang . '.min.js';
                                    if (!in_array($file, $this->jsIncludes[$pluginName])) {
                                        $this->jsIncludes[$pluginName][] = (string) $file;
                                    }

                                    // add lang to plugin_settings in the xml js_code
                                    $this->pluginSettings[$pluginName][$i]['language'] = $lang;
                                } elseif ($pluginName == 'intl-tel-input') {
                                    $this->pluginSettings[$pluginName][$i]['PLUGINS_URL'] = $this->pluginsUrl;
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Gets js code generated by js plugins
     * Scroll to user error if any
     * @return void
     */
    protected function registerJsCode()
    {
        // reorder floating-labels plugin to be the last one
        if (in_array('floating-labels', $this->jsPlugins)) {
            unset($this->jsPlugins[array_search('floating-labels', $this->jsPlugins)]);
            array_push($this->jsPlugins, 'floating-labels');
// reindex array
            $this->jsPlugins = array_values($this->jsPlugins);
        }
        $nbrePlugins  = count($this->jsPlugins);
        $pluginsFiles = [];
        $pluginsNames = [];
        $recaptchaJs  = '';
        $this->jsCode  = '<script>' . "\n";
        $this->jsCode .= 'if (typeof forms === "undefined") {' . "\n";
        $this->jsCode .= '    var forms = [];' . "\n";
        $this->jsCode .= '}' . "\n";
        // define the constant for the form
        $this->jsCode .= 'forms["' . $this->formId . '"] = {};' . "\n";
        // load js files if loadJs enabled
        if ($this->options->getBool('useLoadJs')) {
            $this->jsCode .= 'if (typeof loadedCssFiles === "undefined") {var loadedCssFiles = [];}' . "\n";
            $this->jsCode .= 'if (typeof loadedJsFiles === "undefined") {var loadedJsFiles = [];}' . "\n";
            $this->getIncludes('css');
            $this->getIncludes('js');
            $fileTypes          = ['css', 'js'];
            $compressedFileUrl  = ['css', 'js'];
            $compressedFilePath = ['css', 'js'];
            $includes = [
                'css' => $this->cssIncludes,
                'js' => $this->jsIncludes
            ];
            foreach ($fileTypes as $type) {
                $pluginsFiles[$type] = [];
                if (!empty($this->framework)) {
                    $framework = $this->framework;
                    if ($this->framework == 'material' && !in_array('materialize', $this->jsPlugins)) {
                        $framework = 'materialize';
                    }
                    $compressedFileUrl[$type]  = $this->pluginsUrl . 'min/' . $type . '/' . $framework . '-' . $this->formId . '.min.' . $type;
                    $compressedFilePath[$type] = $this->pluginsPath . 'min/' . $type . '/' . $framework . '-' . $this->formId . '.min.' . $type;
                } else {
                    $compressedFileUrl[$type]  = $this->pluginsUrl . 'min/' . $type . '/' . $this->formId . '.min.' . $type;
                    $compressedFilePath[$type] = $this->pluginsPath . 'min/' . $type . '/' . $this->formId . '.min.' . $type;
                }
                // $this->jsIncludes[$pluginName][] = (string) $file;
                foreach ($includes[$type] as $pluginName => $filesArray) {
                    foreach ($filesArray as $file) {
                        if (strlen($file) > 0) {
                            if (!preg_match('`^(http(s)?:)?//`', $file)) { // if relative path in XML
                                $file = $this->pluginsUrl . $file;
                            }
                            $pluginsFiles[$type][] = $file;
                            $pluginsNames[$file]   = $pluginName;
                        }
                    }
                }
                if ($this->checkRewriteCombinedFiles($pluginsFiles[$type], $compressedFilePath[$type])) {
                    $this->combinePluginFiles($type, $pluginsFiles[$type], $compressedFilePath[$type]);
                }
            }

            // load css files
            if (count($pluginsFiles['css']) > 0) {
                $this->jsCode .= '    loadjs([';
                if ($this->mode == 'production') {
                    $this->jsCode .= '"' . $compressedFileUrl['css'] . '"' . "\n";
                } else {
                    $this->jsCode .= '"' . implode('", "', $pluginsFiles['css']) . '"' . "\n";
                }
                $this->jsCode .= '    ], {' . "\n";
                $this->jsCode .= '        before: function(path, scriptEl) {' . "\n";
                $this->jsCode .= '            if (loadedCssFiles.indexOf(path) !== -1) {' . "\n";
                $this->jsCode .= '                /* file already loaded - return `false` to bypass default DOM insertion mechanism */' . "\n";
                $this->jsCode .= '                return false;' . "\n";
                $this->jsCode .= '            }' . "\n";
                $this->jsCode .= '            loadedCssFiles.push(path);' . "\n";
                $this->jsCode .= '        }' . "\n";
                $this->jsCode .= '    });' . "\n";
            }

            // load js files
            $domReadyBundles = [];
            if (!empty($this->options->get('loadJsBundle'))) {
                if (is_array($this->options->get('loadJsBundle'))) {
                    $domReadyBundles = array_merge($domReadyBundles, $this->options->getArray('loadJsBundle'));
                } else {
                    $domReadyBundles[] = $this->options->getString('loadJsBundle');
                }
            }
            if ($this->mode == 'production') {
                $this->jsCode .= '    loadjs(["' . $compressedFileUrl['js'] . '"], "' . $this->formId . '", {' . "\n";
                $this->jsCode .= '            async: false' . "\n";
                $this->jsCode .= '        });' . "\n";
                $domReadyBundles[] = $this->formId;
            } else {
                foreach ($pluginsFiles['js'] as $jsFile) {
                    $bundleName = ltrim(str_replace($this->pluginsUrl, '', $jsFile), '/');
                    $domReadyBundles[] = $bundleName;
                    $this->jsCode .= '    if (!loadjs.isDefined("' . $bundleName . '")) {' . "\n";
                    $this->jsCode .= '        loadjs(["' . $jsFile . '"], "' . $bundleName . '", {' . "\n";
                    $this->jsCode .= '            async: false' . "\n";
                    $this->jsCode .= '        });' . "\n";
                    $this->jsCode .= '    }' . "\n";
                }
            }
            $this->options->set('openDomReady', '    loadjs.ready(["' . implode('", "', $domReadyBundles) . '"], function() {' . "\n");
            $this->options->set('closeDomReady', '});');
            if ($this->hasPopover) {
                $this->options->set('openDomReady', 'if (typeof(popoverReady) === "undefined") { window.popoverReady = [];}' . "\n" . '    window.popoverReady["' . $this->formId . '"] = function () {' . "\n");
                $this->options->set('closeDomReady', '};' . "\n");
            }
        }
        $this->jsCode .= $this->options->getString('openDomReady') . "\n";
        $this->jsCode .= '    if(top != self&&typeof location.ancestorOrigins!="undefined"){if(location.ancestorOrigins[0]!=="https://preview.codecanyon.net"&&!document.getElementById("drag-and-drop-preview") && document.getElementById("' . $this->formId . '")){document.getElementById("' . $this->formId . '").addEventListener("submit",function(e){e.preventDefault();console.log("not allowed");return false;});}}' . "\n";
        for ($i = 0; $i < $nbrePlugins; $i++) {
            $pluginName = $this->jsPlugins[$i]; // ex : colorpicker
            $nbre = count($this->jsFields[$pluginName]);
            for ($j = 0; $j < $nbre; $j++) {
                $selector        = $this->jsFields[$pluginName][$j];
                $pluginSettings = $this->pluginSettings[$pluginName][$j];
                $jsConfig       = $this->jsContent[$pluginName][$j];
                if (file_exists(dirname(__FILE__, 4) . '/plugins-config-custom/' . $pluginName . '.xml')) {
                    // if custom config xml file
                    $xml = simplexml_load_file(dirname(__FILE__, 4) . '/plugins-config-custom/' . $pluginName . '.xml');

                    // if node doesn't exist, fallback to default xml
                    if (!isset($xml->{$jsConfig})) {
                        $xml = simplexml_load_file(dirname(__FILE__, 4) . '/plugins-config/' . $pluginName . '.xml');
                    }
                } else {
                    $xml = simplexml_load_file(dirname(__FILE__, 4) . '/plugins-config/' . $pluginName . '.xml');
                }
                if ($xml instanceof \SimpleXMLElement) {
                    if ($pluginName == 'tooltip' && $jsConfig === 'popover' && !$this->options->getBool('useLoadJs')) { // popover without loadJs
                        $this->popoverJsCode = '<script>document.addEventListener(\'DOMContentLoaded\', function(event) {' . str_replace('%formId%', $this->formId, rtrim((string) $xml->$jsConfig->js_code) . '});</script>' . "\n");
                    } elseif ($pluginName == 'tooltip' && $jsConfig === 'popover' && $this->options->getBool('useLoadJs')) { // popover with loadJs
                        $this->popoverJsCode = '<script>loadjs.ready(["tippyjs/tippy.min.js"], function() {' . str_replace('%formId%', $this->formId, rtrim((string) $xml->$jsConfig->js_code) . '});</script>' . "\n");
                    } elseif (!empty($xml->$jsConfig->js_code)) { // others
                        $this->jsCode .= str_replace('%selector%', $selector, rtrim((string) $xml->$jsConfig->js_code) . "\n");
                    }
                }
                // ensure formValidation has replacements (wont if printIncludes('js') hasn't been called)
                if ($pluginName == 'formvalidation') {
                    // framework
                    $frameworks = [
                        'bs4'         => 'Bootstrap',
                        'bs5'         => 'Bootstrap5',
                        'bulma'       => 'Bulma',
                        'foundation'  => 'Foundation',
                        'material'    => 'Materialize',
                        'tailwind'    => 'Tailwind',
                        'uikit'       => 'Uikit'
                    ];
                    if (array_key_exists($this->framework, $frameworks)) {
                        $f = $this->framework;
                        $pluginSettings['FRAMEWORK-LOWERCASE'] = strtolower(str_replace('5', '', $frameworks[$f]));
                        $pluginSettings['FRAMEWORK']   = $frameworks[$f];
                        $pluginSettings['PLUGINS_URL'] = $this->pluginsUrl;
                        $defaultReplacements = ['language' => 'en_EN'];
                    }

                    if (isset($defaultReplacements)) {
                        foreach ($defaultReplacements as $key => $value) {
                            if (!isset($pluginSettings[$key])) {
                                $pluginSettings[$key] = $defaultReplacements[$key];
                            }
                        }
                    }
                }
                if (!empty($pluginSettings)) {
                    $pluginSettings = $this->encloseArrayKeys('%', $pluginSettings);
                    foreach ($pluginSettings as $key => $value) {
                        if ($value === null) {
                            $value = '';
                        }
                        if ($pluginName == 'jquery-fileupload') { // fileupload
                            $this->fileuploadJsCode = str_replace($key, (string) $value, $this->fileuploadJsCode);
                        } else { // others
                            if (is_array($value)) {
                                $value =  json_encode($value);
                            }
                            $this->jsCode = str_replace($key, (string) $value, $this->jsCode);
                        }
                    }
                }
            }
        }
        // scroll to user error
        if (!empty($this->options->getString('wrapperErrorClass')) && !in_array('modal', $this->jsPlugins) && !$this->hasPopover) {
            $this->jsCode .= "\n" . '    if (document.querySelector(".' . $this->options->getString('wrapperErrorClass') . '") !== null) {' . "\n";
            $this->jsCode .= '        window.scrollTo(0, document.querySelector(".' . $this->options->getString('wrapperErrorClass') . '").offsetTop - 400);' . "\n";
            $this->jsCode .= '    }' . "\n";
        }
        $this->jsCode .= $this->options->getString('closeDomReady') . "\n";

        // recaptcha callback has to be outside domready
        $this->jsCode .= $recaptchaJs;
        $this->jsCode .= '</script>' . "\n";
    }

    /**
     * Gets label class. (see options).
     *
     * @param  string $element (Optional) 'standardElement', 'radio' or 'checkbox'
     * @param  bool $inline  True or false
     * @return string The element class defined in form options.
     */
    protected function getLabelClass(string $element = 'standardElement', bool $inline = false)
    {
        $class = '';
        $output = '';
        if ($element == 'standardElement' || $element == 'fileinput') { // input, textarea, select
            if ($this->layout == 'horizontal') {
                if (!$this->options->getBool('horizontalLabelWrapper')) {
                    $class = $this->options->getString('horizontalLabelCol') . ' ' . $this->options->getString('horizontalLabelClass');
                } else {
                    $class = $this->options->getString('horizontalLabelClass');
                }
                if ($element == 'fileinput') {
                    $class .= ' fileinput-label';
                }
            } elseif ($this->layout == 'vertical') {
                if (!$this->options->getBool('verticalLabelWrapper')) {
                    $class     = $this->options->getString('verticalLabelClass');
                }
            }
            $class = trim($class);
            if (!empty($class)) {
                $output = ' class="' . $class . '"';
            } else {
                $output = '';
            }
        } elseif ($element == 'radio') {
            if ($inline && !empty($this->options->getString('inlineRadioLabelClass'))) {
                $output = ' class="' . $this->options->getString('inlineRadioLabelClass') . '"';
            } elseif (!$inline && !empty($this->options->getString('verticalRadioLabelClass'))) {
                $output = ' class="' . $this->options->getString('verticalRadioLabelClass') . '"';
            } else {
                $output = '';
            }
        } elseif ($element == 'checkbox') {
            if ($inline && !empty($this->options->getString('inlineCheckboxLabelClass'))) {
                $output = ' class="' . $this->options->getString('inlineCheckboxLabelClass') . '"';
            } elseif (!$inline && !empty($this->options->getString('verticalCheckboxLabelClass'))) {
                $output = ' class="' . $this->options->getString('verticalCheckboxLabelClass') . '"';
            } else {
                $output = '';
            }
        }

        return $output;
    }

    /**
     * Wrapps label with col if needed (see options).
     *
     * @param  string $pos 'start' or 'end'
     * @return string The html code of the element wrapper.
     */
    protected function getLabelCol(string $pos)
    {
        $output = '';
        if ($this->layout == 'horizontal' && !empty($this->options->getString('horizontalLabelCol'))) {
            if ($pos == 'start') {
                $output = '<div class="' . $this->options->getString('horizontalLabelCol') . '">';
            } else { // end
                $output = '</div>';
            }
        } elseif ($this->layout == 'vertical' && !empty($this->options->getString('verticalLabelClass'))) {
            if ($pos == 'start') {
                $output = '<div class="' . $this->options->getString('verticalLabelClass') . '">';
            } else { // end
                $output = '</div>';
            }
        }

        return $output;
    }

    /**
     * Automaticaly adds requiredMark (see options) to labels's required fields
     *
     * @param  string $label The element label
     * @param  string $attr  The element attributes
     * @return string The element label if required html markup if needed
     */
    protected function getRequired(string $label, string $attr)
    {
        if (preg_match('`required`', $attr)) {
            $dom = new \DOMDocument();
            $dom->loadXML('<div>' . $label . '</div>');
            $elements = $dom->documentElement;
            $output = '';

            if ($elements instanceof \DOMElement) {
                foreach ($elements->childNodes as $entry) {
                    if ($entry->nodeName == '#text') {
                        $output .= $entry->textContent . ' ' . $this->options->getString('requiredMark');
                    } else {
                        $output .= $entry->ownerDocument->saveHTML($entry);
                    }
                }
            }

            return $output;
        } else {
            return $label;
        }
    }

    /**
     * Gets element value
     *
     * Returns default value if not empty
     * Else returns session value if it exists
     * Else returns an emplty string
     *
     * @param  string $name  The element name
     * @param  mixed $value The default value
     * @return string The element value
     */
    protected function getValue(string $name, $value)
    {
        $output = '';

        $formId = $this->formId;
        if (!empty($value) || is_numeric($value)) {
            $output = htmlspecialchars((string) $value);
        } elseif (isset($_SESSION[$formId][$name])) {
            $output = htmlspecialchars((string) $_SESSION[$formId][$name]);
        } elseif (preg_match('`([^\\[]+)\[([^\\]]+)\]`', $name, $out)) { // arrays
            $arrayName = $out[1];
            $arrayKey = $out[2];
            if (isset($_SESSION[$formId][$arrayName][$arrayKey])) {
                $output = htmlspecialchars((string) $_SESSION[$formId][$arrayName][$arrayKey]);
            } else {
                $output = htmlspecialchars((string) $_SESSION[$formId][$name]);
            }
        }

        return $output;
    }

    /**
     * check the form layout & options to see if labels should or not be wrapped
     *
     * @return bool
     */
    protected function hasLabelWrapper()
    {
        return $this->layout === 'vertical' && $this->options->getBool('verticalLabelWrapper') || $this->layout === 'horizontal' && $this->options->getBool('horizontalLabelWrapper');
    }

    /**
     * check if name belongs to a group input
     *
     * @param  string $name
     * @return bool
     */
    protected function isGrouped(string $name)
    {
        foreach ($this->inputGrouped as $inputGrouped) {
            if (in_array($name, $inputGrouped)) {
                return true;
            }
        }
        return false;
    }

    /**
     * output element html code including wrapper, label, element with group, icons, ...
     *
     * @param  string  $startWrapper   i.e. <div class="row">
     * @param  string  $endWrapper     i.e. </div>
     * @param  string  $startLabel     i.e. <label class="small-3 columns text-right middle main-label">Vertical radio
     * @param  string  $endLabel       i.e. </label>
     * @param  string  $startCol       i.e. <div class="small-9 columns">
     * @param  string  $endCol         i.e. </div>
     * @param  string  $elementHtml    i.e. <fieldset><input type="radio" id="vertical-radio_0" name="vertical-radio" value="1" ><label for="vertical-radio_0" >One</label></fieldset>
     * @param  bool    $wrapIntoLabel
     * @return string element html code
     */
    protected function outputElement(string $startWrapper, string $endWrapper, string $startLabel, string $endLabel, string $startCol, string $endCol, string $elementHtml, bool $wrapIntoLabel)
    {
        $html = $startWrapper;
        if (!empty($startLabel) && $wrapIntoLabel) {
            $html .= $startLabel . $startCol . $elementHtml . $endCol . $endLabel;
        } else {
            $labelCol = 0;
            if (preg_match('`(\d+)`', $this->options->getString('horizontalLabelCol'), $out)) {
                $labelCol = $out[1];
            }
            if ($this->framework === 'material') {
                if ($labelCol < 1 && !strpos($startLabel, 'main-label') && !strpos($elementHtml, 'no-autoinit')) {
                    // label after element if label is into col div except radio & checkbox
                    $html .= $startCol . $elementHtml . $startLabel . $endLabel . $endCol;
                } else {
                    // label before element
                    $html .= $startLabel . $endLabel . $startCol . $elementHtml . $endCol;
                }
            } else {
                // label before element
                $html .= $startLabel . $endLabel . $startCol . $elementHtml . $endCol;
            }
        }
        $html .= $endWrapper;

        return $html;
    }

    /**
     * Gets errors stored in session
     */
    protected function registerErrors(): void
    {
        $formId = $this->formId;
        foreach ($_SESSION['errors'][$formId] as $field => $message) {
            /* remove dot syntax (field.index => field */

            $field = (string) preg_replace('`\.[^\s]+`', '', $field);
            $this->errorFields[$field] = $message;
        }

        if (isset($_SESSION['errors'][$formId])) {
            $errorKeys = array_keys($_SESSION['errors'][$formId]);
            // register altcha error
            if (in_array('altcha', $errorKeys)) {
                $this->hasAltchaError    = true;
                $this->altchaErrorText = $_SESSION['errors'][$formId]['altcha'];
            }
            // register hcaptcha error
            if (in_array('h-captcha-response', $errorKeys)) {
                $this->hasHcaptchaError  = true;
                $this->hcaptchaErrorText = $_SESSION['errors'][$formId]['h-captcha-response'];
            }
            // register recaptcha error
            if (in_array('g-recaptcha-response', $errorKeys)) {
                $this->hasRecaptchaError  = true;
                $this->recaptchaErrorText = $_SESSION['errors'][$formId]['g-recaptcha-response'];
            }
        }
    }

    /**
     * When the form is posted, values are passed in session
     * to be keeped and displayed again if posted values aren't correct.
     * @param string $name The name of the field
     * @param string $attr The attributes of the field
     */
    protected function registerField(string $name, string $attr): void
    {
        $formId = $this->formId;
        if (!isset($_SESSION[$formId])) {
            $_SESSION[$formId]           = [];
        }
        if (!isset($_SESSION[$formId]['fields'])) {
            $_SESSION[$formId]['fields'] = [];
        }
        if (!isset($_SESSION[$formId]['required_fields'])) {
            $_SESSION[$formId]['required_fields'] = [];
        }
        $name = preg_replace('`(.*)\[\]`', '$1', $name); // if $name is an array, we register without hooks ([])
        if (!in_array($name, $_SESSION[$formId]['fields'])) {
            $_SESSION[$formId]['fields'][] = $name;
        }
        if (!isset($_SESSION[$formId]['required_fields_conditions'])) {
            $_SESSION[$formId]['required_fields_conditions'] = [];
        } else {
            // reset dependent field condition
            $_SESSION[$formId]['required_fields_conditions'][$name] = '';
        }

        // register required fields
        if (preg_match('`required`', $attr) && !in_array($name, $_SESSION[$formId]['required_fields'])) {
            $_SESSION[$formId]['required_fields'][] = $name;
        }

        // register required conditions if we're into dependent fields
        if (!empty($this->currentDependentData)) {
            $lastElement = $this->currentDependentData[array_key_last($this->currentDependentData)];
            $_SESSION[$formId]['required_fields_conditions'][$name] = $lastElement;
        }
    }

    /**
     * Converts a relative URL to an absolute URL.
     *
     * @param string $rel  The URL to convert.
     * @param string $base The URL of the origin file.
     *
     * @return string The absolute URL.
     */
    private function rel2abs(string $rel, string $base)
    {
        $output = '';

        // Remove beginning & ending quotes
        $rel = (string) preg_replace('`^([\'"]?)([^\'"]+)([\'"]?)$`', '$2', $rel);

        // Parse base URL and convert to local variables: $scheme, $host, $path
        $baseUrl = parse_url($base);
        if ($baseUrl) {
            extract($baseUrl);

            if (isset($host) && isset($path)) {
                $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http');

                if (strpos($rel, "//") === 0) {
                    $output = $scheme . ':' . $rel;
                } elseif (parse_url($rel, PHP_URL_SCHEME) != '') {
                    // Return if already an absolute URL
                    $output = $rel;
                } elseif ($rel[0] == '#' || $rel[0] == '?') {
                    // Queries and anchors
                    $output = $base . $rel;
                } else {
                    // Remove non-directory element from path
                    $path = preg_replace('#/[^/]*$#', '', $path);

                    // Destroy path if the relative URL points to the root
                    if ($rel[0] ==  '/') {
                        $path = '';
                    }

                    // Dirty absolute URL
                    $abs = $host . $path . "/" . $rel;

                    // Replace '//' or '/./' or '/foo/../' with '/'
                    $abs = (string) preg_replace("/(\/\.?\/)/", "/", $abs);
                    $abs = preg_replace("/\/(?!\.\.)[^\/]+\/\.\.\//", "/", $abs);

                    $output = $scheme . '://' . $abs;
                }
            }
        }

        // Absolute URL is ready!
        return $output;
    }

    /**
     * removes specific attribute from list (ex : removes 'checked="checked"' from radio in other than default has been stored in session)
     *
     * @param  string $attrToRemove ex : checked
     * @param  string $attrString    ex : checked="checked", required
     * @return string attributes without the removed one
     */
    protected function removeAttribute(string $attrToRemove, string $attrString)
    {
        if (preg_match('`,(\s)?' . $attrToRemove . '((\s)?=(\s)?([\'|"])?' . $attrToRemove . '([\'|"])?)?`', $attrString)) { // beginning comma
            $attrString = preg_replace('`,(\s)?' . $attrToRemove . '((\s)?=(\s)?([\'|"])?' . $attrToRemove . '([\'|"])?)?`', '', $attrString);
        } elseif (preg_match('`' . $attrToRemove . '((\s)?=(\s)?([\'|"])?' . $attrToRemove . '([\'|"])?(\s)?)?,`', $attrString)) { // ending comma
            $attrString = preg_replace('`' . $attrToRemove . '((\s)?=(\s)?([\'|"])?' . $attrToRemove . '([\'|"])?(\s)?)?,`', '', $attrString);
        } elseif (preg_match('`\s?' . $attrToRemove . '((\s)?=(\s)?([\'|"])?' . $attrToRemove . '([\'|"])?(\s)?)?`', $attrString)) { // no comma
            $attrString = preg_replace('`\s?' . $attrToRemove . '((\s)?=(\s)?([\'|"])?' . $attrToRemove . '([\'|"])?(\s)?)?`', '', $attrString);
        }

        return (string) $attrString;
    }

    /**
     * Allows to group inputs in the same wrapper (12 inputs max.)
     *
     * @param string $name        The input name
     * @param string $wrapperPos start | end
     * @param string $wrapper     | end     elementsWrapper | checkboxWrapper | radioWrapper | buttonWrapper
     * @return string html of the wrapper
     */
    protected function setInputGroup(string $name, string $wrapperPos, string $wrapper)
    {
        $output = '';

        if (!empty($this->options->getString($wrapper))) {
            $grouped = false;
            $inputPos = ''; // start | middle | end
            $pattern_2_wrappers = '`<([^>]+)><([^>]+)></([^>]+)></([^>]+)>`';
            $startWrapper = '';
            $endWrapper = '';
            if ($wrapper == 'elementsWrapper') {
                $startWrapper = $this->options->getStartWrapper('elements');
                $endWrapper   = $this->options->getEndWrapper('elements');
            } elseif ($wrapper == 'checkboxWrapper') {
                $startWrapper = $this->options->getStartWrapper('checkbox');
                $endWrapper   = $this->options->getEndWrapper('checkbox');
            } elseif ($wrapper == 'radioWrapper') {
                $startWrapper = $this->options->getStartWrapper('radio');
                $endWrapper   = $this->options->getEndWrapper('radio');
            } elseif ($wrapper == 'buttonWrapper') {
                $startWrapper = $this->options->getStartWrapper('button');
                $endWrapper   = $this->options->getEndWrapper('button');
            }
            if ($wrapperPos == 'start') {
                if ($this->options->getArray('centerContent')['center']) {
                    $centeredClass = ' phpfb-centered';
                    if ($this->options->getArray('centerContent')['stack']) {
                        $centeredClass .= ' phpfb-centered-stacked';
                    }
                    $startWrapper = preg_replace('`class="([^"]+)"`', 'class="$1' . $centeredClass . '"', $startWrapper);
                }
                foreach ($this->inputGrouped as $inputGrouped) {
                    for ($i = 1; $i < 12; $i++) {
                        $input = 'input_' . ($i + 1);
                        $nextInput = 'input_' . ($i + 2);
                        if (isset($inputGrouped[$input]) && $name == $inputGrouped[$input]) {
                            $grouped = true;
                            if (isset($inputGrouped[$nextInput])) {
                                $inputPos = 'middle';
                            } else {
                                $inputPos = 'end';
                            }
                        }
                    }
                }
                if ($grouped && $inputPos == 'middle' || $inputPos == 'end') {
                    if (preg_match($pattern_2_wrappers, $this->options->getString($wrapper), $out)) {
                        $output = '<' . $out[2] . '>';
                    } else {
                        $output = '';
                    }
                } else {
                    $output = $startWrapper;
                }
            } elseif ($wrapperPos == 'end') {
                foreach ($this->inputGrouped as $inputGrouped) {
                    for ($i = 0; $i < 12; $i++) {
                        $input = 'input_' . ($i + 1);
                        $nextInput = 'input_' . ($i + 2);
                        if ($i == 0 && $name == $inputGrouped[$input]) {
                            $grouped = true;
                            $inputPos = 'start';
                        } elseif (isset($inputGrouped[$input]) && $name == $inputGrouped[$input] && isset($inputGrouped[$nextInput])) {
                            $inputPos = 'middle';
                        }
                    }
                }
                if ($grouped && $inputPos == 'start' || $inputPos == 'middle') {
                    if (preg_match($pattern_2_wrappers, $this->options->getString($wrapper), $out)) {
                        $output = '</' . $out[3] . '>';
                    } else {
                        $output = '';
                    }
                } else {
                    $output = $endWrapper;
                }
            }
        }

        return (string) $output;
    }

    /**
     * start custom wrapper div according to $confName
     *
     * @param string $name      the element name
     * @param string $confName specific configuration name: bulma_control|bulma_select|bulma_select_multiple|tailwind_horizontal_radio_checkbox_inline|tailwind_vertical_radio_checkbox_inline
     *
     * @return string $output       the html start div
     */
    protected function startElementCustomWrapper(string $name, string $confName)
    {
        $output = '';
        if ($confName === 'bulma_control') {
            $class = 'control is-expanded';
            if (\array_key_exists($name, $this->fieldsWithIcons)) {
                $iconPos = 'left';
                if ($this->fieldsWithIcons[$name] === 'after') {
                    $iconPos = 'right';
                }
                $class .= ' has-icons-' . $iconPos;
            }
            $output = '<div class="' . $class . '">';
        } elseif ($confName === 'bulma_select') {
            $output .= '<div class="select is-fullwidth">';
        } elseif ($confName === 'bulma_select_multiple') {
            $output .= '<div class="select is-multiple is-fullwidth">';
        } elseif ($confName === 'bulma_field' || $confName === 'bulma_field_multiline') {
            $class = 'field';
            if ($confName === 'bulma_field_multiline') {
                $class .= ' is-grouped is-grouped-multiline';
            }
            $output = '<div class="' . $class . '">';
        } elseif ($confName === 'tailwind_horizontal_radio_checkbox_inline' || $confName === 'tailwind_vertical_radio_checkbox_inline') {
            $output = '<div class="flex-auto">';
        }

        return $output;
    }
}
