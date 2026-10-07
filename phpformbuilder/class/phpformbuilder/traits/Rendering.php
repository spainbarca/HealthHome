<?php

declare(strict_types=1);

namespace phpformbuilder\traits;

use phpformbuilder\Form;

trait Rendering
{
    /**
     * Get the code for rendering the form.
     *
     * @param bool $debug Whether to enable debug mode or not.
     *                    With debug mode enabled, the HTML code will be displayed inside a <pre> tag.
     * @return string The generated HTML code for rendering the form.
     */
    public function getCode(bool $debug = false): string
    {
        // wrapper for popover | modal plugins
        $html = $this->formStartWrapper;

        if (!empty($_SERVER['QUERY_STRING'])) {
            $get = '?' . $_SERVER['QUERY_STRING'];
        }
        if (empty($this->action)) {
            $this->action = htmlspecialchars($_SERVER["PHP_SELF"]);
        }
        $html .= '<form ';
        if (!empty($this->formId)) {
            $html .= 'id="' . $this->formId . '" ';
        }
        $html .= 'action="' . $this->action;
        if (isset($get) && $this->addGetVars) {
            $html .= $get;
        }
        $html .= '" method="' . $this->method . '"';
        if ($this->hasFile) {
            $html .= ' enctype="multipart/form-data"';
        }

        /* layout */

        $attr = $this->getAttributes($this->formAttr);

        if (count($this->errorFields) > 0) {
            $attr = $this->addClass('phpfb-has-error', $attr);
        }

        if (strpos($attr, 'data-accordion') !== false) {
            $attr = $this->addClass('js-badger-accordion', $attr);
        }

        if ($this->layout == 'horizontal' && !empty($this->options->getString('formHorizontalClass'))) {
            $attr = $this->addClass($this->options->getString('formHorizontalClass'), $attr);
        } elseif ($this->layout == 'inline' && !empty($this->options->getString('formInlineClass'))) {
            $attr = $this->addClass($this->options->getString('formInlineClass'), $attr);
        } elseif (!empty($this->options->getString('formVerticalClass'))) {
            $attr = $this->addClass($this->options->getString('formVerticalClass'), $attr);
        }

        // validator class to help with plugins
        if (in_array('formvalidation', $this->jsPlugins)) {
            $attr = $this->addClass('has-validator', $attr);
        }

        // ajax class to help with plugins
        if ($this->options->getBool('ajax')) {
            $attr = $this->addClass('ajax-form', $attr);
        }
        if ($this->framework == 'material') {
            $attr = $this->addClass('material-form', $attr);
            if (!in_array('materialize', $this->jsPlugins)) {
                $attr = $this->addClass('materialize-form', $attr);
            }
        } else {
            $attr = $this->addClass($this->framework . '-form', $attr);
            if ($this->framework === 'bulma' && $this->layout === 'horizontal') {
                $attr = $this->addClass('bulma-form-horizontal', $attr);
            }
        }
        if (!empty($attr)) {
            $html .= ' ' . $attr;
        }
        $html .= '>';
        if (!empty($this->errorMsg)) { // if iCheck used with material
            $html .= $this->errorMsg;
        }
        if (!empty($this->hiddenFields)) {
            $html .= '<div>' . $this->hiddenFields . '</div>';
        }
        $html .= $this->html;
        if (!empty($this->txt)) {
            $html .= $this->txt;
        }
        if (!empty($this->endFieldset)) {
            $html .= $this->endFieldset;
        }
        $html .= '</form>';
        $html .= $this->formEndWrapper;

        // add Recaptcha js callback function
        if (!empty($this->recaptchaJsCallback)) {
            $html .= $this->recaptchaJsCallback;
        }

        if ($debug) {
            $html = $this->cleanHtmlOutput($html); // beautify html
        }

        if ($this->options->getBool('ajax') && $_SERVER['REQUEST_METHOD'] !== 'POST') { // if ajax option enabled
            $cssfiles = explode("\n", $this->getIncludes('css'));
            $jsfiles  = preg_replace("/[\r\n]/", "", $this->getIncludes('js'));
            $html .= $jsfiles;

            $script = '<script>' . "\n";
            // set the 'loadAjaxForm' CustomEvent
            $script .= 'if (typeof(loadAjaxFormEvent) === \'undefined\') {' . "\n";
            $script .= '    var loadAjaxFormEvent = [];' . "\n";
            $script .= '}' . "\n";
            $script .= 'loadAjaxFormEvent[\'' . $this->formId . '\'] = new Event(\'loadAjaxForm' . $this->formId . '\');' . "\n";
            // script to submit with ajax
            $script .= 'if (typeof(link0) === \'undefined\') {' . "\n";
            $cssfilescount = count($cssfiles);
            for ($i = 0; $i < $cssfilescount; $i++) {
                $cssfile = $cssfiles[$i];
                $linkVar = 'link' . $i;
                if (preg_match('`href="([^"]+)"`', $cssfile, $out)) {
                    $script .= '    let ' . $linkVar . ' = document.createElement("link");' . "\n";
                    $script .= '    ' . $linkVar . '.rel = "stylesheet";' . "\n";
                    $script .= '    ' . $linkVar . '.media = "screen";' . "\n";
                    $script .= '    ' . $linkVar . '.href = "' . $out[1] . '";' . "\n";
                    $script .= '    document.head.appendChild(' . $linkVar . ');' . "\n";
                }
            }
            $script .= '}' . "\n";
            $script .= $this->options->getString('openDomReady') . "\n";
            $script .= '    var $form = document.getElementById(\'' . $this->formId . '\');' . "\n";

            // if formvalidation plugin is enabled,
            // ajax submit is done by the plugin with the 'core.form.valid' event
            $script .= '    if(!$form.classList.contains("has-validator")) {' . "\n";
            $script .= '        $form.addEventListener(\'submit\', function (e) {' . "\n";
            $script .= '            e.preventDefault();' . "\n";
            $script .= '            let data = new FormData($form);' . "\n";
            $script .= '            fetch($form.getAttribute(\'action\'), {' . "\n";
            $script .= '                method: \'post\',' . "\n";
            $script .= '                body: new URLSearchParams(data).toString(),' . "\n";
            $script .= '                headers: {' . "\n";
            $script .= '                    \'Content-type\': \'application/x-www-form-urlencoded\'' . "\n";
            $script .= '                },' . "\n";
            $script .= '                cache: \'no-store\',' . "\n";
            $script .= '                credentials: \'include\'' . "\n";
            $script .= '            }).then(function (response) {' . "\n";
            $script .= '                return response.text()' . "\n";
            $script .= '            }).then(function (data) {' . "\n";
            $script .= '                let $formContainer = document.querySelector(\'*[data-ajax-form-id="' . $this->formId . '"]\');' . "\n";
            $script .= '                $formContainer.innerHTML = \'\';' . "\n";
            $script .= '                loadData(data, \'#\' + $formContainer.id).then(() => {' . "\n";
            $script .= '                    window.document.dispatchEvent(loadAjaxFormEvent[\'' . $this->formId . '\']);' . "\n";
            $script .= '                });' . "\n";
            $script .= '            }).catch(function (error) {' . "\n";
            $script .= '                console.log(error);' . "\n";
            $script .= '            });' . "\n";
            $script .= '        });' . "\n";
            $script .= '    };' . "\n";
            $script .= $this->options->getString('closeDomReady') . "\n";
            $script .= '</script>' . "\n";

            $html .= $script;
            $html .= $this->getJsCode(false);

            // trigger the 'loadAjaxForm' CustomEvent
            $html .= '<script>window.document.dispatchEvent(loadAjaxFormEvent[\'' . $this->formId . '\']);</script>' . "\n";
        }
        if ($debug) {
            echo '<pre class="prettyprint">' . htmlspecialchars($html) . '</pre>';
        }

        return $html;
    }

    /**
     * Retrieves the includes for a specific type.
     *
     * @param string $type The type of includes to retrieve.
     * @return string The includes HTML code for the specified type. (<link href="..." rel="stylesheet" /> or <script src="..."></script>)
     */
    public function getIncludes(string $type)
    {
        $this->registerIncludes($type);
        $normalOutput        = '';
        $compressedOutput    = '';
        if (!empty($this->framework)) {
            $framework = $this->framework;
            if ($this->framework == 'material' && !in_array('materialize', $this->jsPlugins)) {
                $framework = 'materialize';
            }
            $compressedFileUrl  = $this->pluginsUrl . 'min/' . $type . '/' . $framework . '-' . $this->formId . '.min.' . $type;
            $compressedFilePath = $this->pluginsPath . 'min/' . $type . '/' . $framework . '-' . $this->formId . '.min.' . $type;
        } else {
            $compressedFileUrl  = $this->pluginsUrl . 'min/' . $type . '/' . $this->formId . '.min.' . $type;
            $compressedFilePath = $this->pluginsPath . 'min/' . $type . '/' . $this->formId . '.min.' . $type;
        }
        $finalOutput         = '';
        $pluginsFiles        = [];

        if ($type == 'css') {
            $compressedOutput = '<link href="' . $compressedFileUrl . '" rel="stylesheet" media="screen">' . "\n";
            foreach ($this->cssIncludes as $pluginName) {
                foreach ($pluginName as $cssFile) {
                    if (strlen($cssFile) > 0 && !in_array($cssFile, Form::$instances['css_files'] ?? [])) {
                        Form::$instances['css_files'][] = $cssFile;
                        if (!preg_match('`^(http(s)?:)?//`', $cssFile)) { // if absolute path in XML
                            $cssFile = $this->pluginsUrl . $cssFile;
                        }
                        if ($this->mode == 'production') {
                            $pluginsFiles[] = $cssFile;
                        }
                        $normalOutput .= '<link href="' . $cssFile . '" rel="stylesheet" media="screen">' . "\n";
                    }
                }
            }
        } elseif ($type == 'js') {
            $defer = ' defer';
            if ($this->options->getBool('deferScripts') !== true) {
                $defer = '';
            }
            if ($this->options->getBool('useLoadJs') !== true) {
                $compressedOutput = '<script src="' . $compressedFileUrl . '"' . $defer . '></script>';
            }
            foreach ($this->jsIncludes as $pluginName) {
                foreach ($pluginName as $jsFile) {
                    if (strlen($jsFile) > 0 && !in_array($jsFile, Form::$instances['js_files'] ?? [])) {
                        Form::$instances['js_files'][] = $jsFile;
                        if (!preg_match('`^(http(s)?:)?//`', $jsFile)) { // if relative path in XML
                            $jsFile = $this->pluginsUrl . $jsFile;
                        }
                        if ($this->mode == 'production') {
                            $pluginsFiles[] = $jsFile;
                            if (strpos($jsFile, 'hcaptcha.com') !== false || strpos($jsFile, 'www.google.com/recaptcha') !== false || strpos($jsFile, 'tinymce/tinymce.min.js') !== false) {
                                $compressedOutput = $compressedOutput . '<script src="' . $jsFile . '"></script>';
                            }
                        }
                        if ($this->options->getBool('useLoadJs') !== true || strpos($jsFile, 'hcaptcha.com') !== false || strpos($jsFile, 'www.google.com/recaptcha') !== false || strpos($jsFile, 'tinymce/tinymce.min.js') !== false) {
                            $normalOutput .= '<script src="' . $jsFile . '"></script>' . "\n";
                        }
                    }
                }
            }
        }
        if ($this->mode == 'production') {
            $finalOutput = $compressedOutput;
            $errorMsg = '';

            $rewriteCombinedFile = $this->checkRewriteCombinedFiles($pluginsFiles, $compressedFilePath);

            if ($rewriteCombinedFile) {
                $errorMsg = $this->combinePluginFiles($type, $pluginsFiles, $compressedFilePath);
                if (!empty($errorMsg)) {
                    $this->buildErrorMsg($errorMsg);
                    $finalOutput = $normalOutput;
                }
            }
        } else {
            $finalOutput = $normalOutput;
        }

        return $finalOutput;
    }

    /**
     * Retrieves the JavaScript code for the form.
     *
     * @param bool $debug (optional) Whether to include debug information in the JavaScript code.
     * @return string The JavaScript code for the form.
     */
    public function getJsCode(bool $debug = false)
    {
        $this->registerJsCode();

        if ($debug) {
            echo '<pre class="prettyprint">' . htmlspecialchars($this->popoverJsCode) . '</pre>';
            echo '<pre class="prettyprint">' . htmlspecialchars($this->jsCode) . '</pre>';
        }

        return $this->popoverJsCode . $this->jsCode;
    }

    /**
     * Renders the form.
     *
     * @param bool $debug (optional) Whether to enable debug mode. Default is false.
     * @return void
     */
    public function render(bool $debug = false): void
    {
        echo $this->getCode($debug);
    }

    /**
     * Prints html code to include css or js dependancies required by plugins.
     * i.e.:
     *     <link rel="stylesheet" ... />
     *     <script src="..."></script>
     *
     * @param string $type The type of includes to print ('css' or 'js')
     * @param bool   $debug (optional) Whether to enable debug mode. Default is false.
     * @return void
     */
    public function printIncludes(string $type, bool $debug = false)
    {
        $output = $this->getIncludes($type);

        if ($debug) {
            echo '<pre class="prettyprint">' . htmlspecialchars($output) . '</pre>';
        }

        echo $output;
    }

    /**
     * Prints the JavaScript code for the form.
     *
     * @param bool $debug (optional) Whether to enable debug mode. Default is false.
     * @return void
     */
    public function printJsCode(bool $debug = false)
    {
        echo $this->getJsCode($debug);
    }
}
