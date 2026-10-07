<?php

declare(strict_types=1);

namespace phpformbuilder;

/**
 * PHP Form Builder
 *
 * @author  Gilles Migliori - gilles.migliori@gmail.com
 * @link    https://www.phpformbuilder.pro/
 * @version 6.0.5
 */

use phpformbuilder\Options\FormOptionsFactory;
use phpformbuilder\Options\FormOptionsInterface;
use phpformbuilder\traits\Elements;
use phpformbuilder\traits\Internal;
use phpformbuilder\traits\Plugins;
use phpformbuilder\traits\Rendering;
use phpformbuilder\traits\StaticFunctions;
use phpformbuilder\traits\Utilities;

class Form
{
    use Elements;
    use Internal;
    use Plugins;
    use Rendering;
    use StaticFunctions;
    use Utilities;

    /* general */

    /**
     * @var string $formId The ID of the form.
     */
    public string $formId = '';

    /**
     * @var string $framework The framework used for the form.
     */
    public string $framework = '';

    /**
     * @var string $pluginsUrl The URL for the plugins used in the form.
     */
    public string $pluginsUrl = '';

    /**
     * @var string $errorMsg The error message for the form.
     */
    public string $errorMsg = '';

    /**
     * @var string $html The HTML content of the form.
     */
    public string $html = '';

    /**
     * @var array<string, mixed>|null $instances An array of form instances.
     */
    public static ?array $instances = null;

    /**
     * @var string $formAttr The attributes of the form.
     */
    protected string $formAttr = '';

    /**
     * @var string $action The action URL for the form.
     */
    protected string $action = '';

    /**
     * @var bool $addGetVars Whether to add GET variables to the form action URL.
     */
    protected bool $addGetVars = true;

    /**
     * @var FormOptionsInterface $options The options for the form.
     */
    protected FormOptionsInterface $options;

    /**
     * @var array<string, string> $errors
     */
    protected array $errors   = [];

    /**
     * @var array<string, string> $errorFields
     */
    protected array $errorFields = [];

    /**
     * @var string $layout
     */
    protected string $layout; /* horizontal | vertical */

    /**
     * @var array<string, mixed> $btn
     */
    protected array  $btn                           = [];

    /**
     * @var array<string, mixed> $checkbox
     */
    protected array  $checkbox                      = [];

    /**
     * @var array<int|string, mixed> $currentDependentData
     */
    protected array  $currentDependentData        = [];

    /**
     * @var string $endFieldset
     */
    protected string $endFieldset                  = '';

    /**
     * @var string $formStartWrapper
     */
    protected string $formStartWrapper            = '';

    /**
     * @var string $formEndWrapper
     */
    protected string $formEndWrapper              = '';

    /**
     * @var int $fileuploaderCount
     */
    protected int    $fileuploaderCount            = 0;

    /**
     * @var array<string, mixed> $fieldsWithAddons
     */
    protected array  $fieldsWithAddons            = [];

    /**
     * @var array<int|string, mixed> $fieldsWithHelpers
     */
    protected array  $fieldsWithHelpers           = [];

    /**
     * @var array<string, mixed> $fieldsWithIcons
     */
    protected array  $fieldsWithIcons             = [];

    /**
     * @var array<string, mixed> $groupName
     */
    protected array  $groupName                    = [];

    /**
     * @var bool $hasAltchaError
     */
    protected bool   $hasAltchaError              = false;

    /**
     * @var string $hcaptchaErrorText
     */
    protected string $altchaErrorText            = '';

    /**
     * @var bool $hasHcaptchaError
     */
    protected bool   $hasHcaptchaError            = false;

    /**
     * @var string $hcaptchaErrorText
     */
    protected string $hcaptchaErrorText           = '';

    /**
     * @var bool $hasAccordion
     */
    protected bool   $hasAccordion                  = false;

    /**
     * @var bool $hasDependentField
     */
    protected bool   $hasDependentField             = false;

    /**
     * @var bool $hasFile
     */
    protected bool   $hasFile                      = false;

    /**
     * @var bool $hasRecaptchaError
     */
    protected bool   $hasRecaptchaError           = false;

    /**
     * @var string $hiddenFields
     */
    protected string $hiddenFields                 = '';

    /**
     * @var array<string, array<int|string, array<int, string>>> $htmlElementContent
     */
    protected array  $htmlElementContent          = []; // ex : $this->htmlElementContent[$elementName][$pos][]  = $html

    /**
     * @var array<int|string, mixed> $inputGrouped
     */
    protected array  $inputGrouped                 = [];

    /**
     * @var array<string, string> $inputWrapper
     */
    protected array  $inputWrapper                 = [];

    /**
     * @var array<string, mixed> $jsContent
     */
    protected array  $jsContent                    = [];

    /**
     * @var array<string, mixed> $jsFields
     */
    protected array  $jsFields                     = [];

    /**
     * @var string $method
     */
    protected string $method                        = 'POST';

    /**
     * @var string $mode
     */
    protected string $mode                          = 'production';

    /**
     * @var array<string, mixed> $option
     */
    protected array  $option                        = [];

    /**
     * @var array<string, mixed> $optiongroupId
     */
    protected array  $optiongroupId                = [];

    /**
     * @var string $pluginsPath
     */
    protected string $pluginsPath                  = '';

    /**
     * @var array<string, mixed> $pluginSettings
     */
    protected array  $pluginSettings               = [];

    /**
     * @var array<string, mixed> $radio
     */
    protected array  $radio                         = [];

    /**
     * @var string $recaptchaJsCallback
     */
    protected string $recaptchaJsCallback         = '';

    /**
     * @var string $recaptchaErrorText
     */
    protected string $recaptchaErrorText          = '';

    /**
     * @var string $token
     */
    protected string $token                         = '';

    /**
     * @var string $txt
     */
    protected string $txt                           = '';

    /**
     * @var array<string> $jsPlugins
     */
    protected array  $jsPlugins         = [];

    /**
     * @var array<string, array<string>> $cssIncludes
     */
    protected array  $cssIncludes       = [];

    /**
     * @var array<string, array<string>> $jsIncludes
     */
    protected array  $jsIncludes        = [];

    /**
     * @var string $fileuploadJsCode
     */
    protected string $fileuploadJsCode = '';

    /**
     * @var string $jsCode
     */
    protected string $jsCode            = '';

    /**
     * @var bool $hasPopover
     */
    protected bool   $hasPopover        = false;

    /**
     * @var string $popoverJsCode
     */
    protected string $popoverJsCode    = '';

    /**
     * Defines the layout (horizontal | vertical).
     * Default is 'horizontal'
     * Clears values from the PHP session if static::clear has been called before
     * Catches posted errors
     * Adds hidden field with the form ID
     * Sets elements wrappers
     *
     * @param  string $formId   The ID of the form
     * @param  string $layout    (Optional) Can be 'horizontal' or 'vertical'
     * @param  string $attr      (Optional) Can be any HTML input attribute or js event EXCEPT class
     *                           (class is defined in layout param).
     *                           attributes must be listed separated with commas.
     *                           Example : novalidate,onclick=alert(\'clicked\');
     * @param  string $framework (Optional) bs4 | bs5 | bulma | foundation | material | tailwind | uikit
     *                           (Bootstrap 4, Bootstrap 5, Bulma, Foundation, Material design, Tailwind, UIkit)
     * @return $this
     */
    public function __construct(string $formId, string $layout = 'horizontal', string $attr = '', string $framework = 'bs5')
    {
        if (Form::$instances === null) {
            Form::$instances = [
                'css_files' => [],
                'js_files' => []
            ];
        }
        $this->action      = '';
        $this->formAttr   = $attr;
        $this->formId     = $formId;
        $this->framework   = $framework;
        $this->layout      = $layout;
        $this->token       = $this->generateToken();
        $this->setPluginsUrl();

        // check if the server's PHP SESSION parameters are correct
        if (isset($_POST[$formId]) && !isset($_SESSION[$formId]) && $this->mode === 'development') {
            $this->buildErrorMsg('PHP SESSION ERROR<br>You have an error in your PHP SESSION settings. Please refer to the help center here:<br><a href="https://www.phpformbuilder.pro/documentation/help-center.php#php-session-settings-error" style="color:#fff">https://www.phpformbuilder.pro/documentation/help-center.php#php-session-settings-error</a>.');
        }

        // set framework options
        $this->options = FormOptionsFactory::create($framework, $layout);

        if (!isset($_SESSION['clear_form'][$formId])) {
            $_SESSION['clear_form'][$formId] = false;
        } elseif ($_SESSION['clear_form'][$formId] === true) {
            $_SESSION['clear_form'][$formId] = false; // reset after clearing
        } elseif (isset($_POST[$formId])) {
            static::registerValues($formId);
        }

        if (isset($_SESSION['errors'][$formId])) {
            $this->registerErrors();
            unset($_SESSION['errors'][$formId]);
        }

        $wrappersMapping = [
            'button',
            'checkbox',
            'elements',
            'helper',
            'iconAfter',
            'iconBefore',
            'inlineCheckbox',
            'inlineRadio',
            'radio'
        ];

        foreach ($wrappersMapping as $key) {
            $this->options->setWrapperStartEndHtml($key);
        }

        $this->addInput('hidden', $formId . '-token', $this->token);
        $this->addInput('hidden', $formId, 'true');

        $registeredFrameworks = ['bs4', 'bs5', 'bulma', 'foundation', 'material', 'tailwind', 'uikit'];
        if (in_array($this->framework, $registeredFrameworks)) {
            $this->addPlugin('frameworks/' . $this->framework, '#' . $formId);

            // register the framework to get the sendMail() sent_message html alert code after posting
            // if the form uses the materialize plugin, $_SESSION['phpfb_framework'] will switch to 'material-bootstrap'
            $_SESSION['phpfb_framework'] = $this->framework;
        }

        // accordion plugin
        if (strpos($attr, 'data-accordion') !== false) {
            $this->hasAccordion = true;
            $this->addPlugin('accordion', '#' . $formId);
        }

        // floating-labels plugin
        if (strpos($attr, 'data-no-floating-labels') === false && $this->framework !== 'material') {
            $this->addPlugin('floating-labels', '#' . $formId);
        }
    }

    /**
     * Redefines form action
     *
     * @param string  $url          The URL to post the form to
     * @param boolean $addGetVars (Optional) If $addGetVars is set to false,
     *                              url vars will be removed from destination page.
     *                              Example : www.myUrl.php?var=value => www.myUrl.php
     *
     * @return $this
     */
    public function setAction(string $url, bool $addGetVars = true)
    {
        $this->action = $url;
        $this->addGetVars = $addGetVars;

        return $this;
    }

    /**
     * Set the layout of the form.
     *
     * @param string $layout The layout to use. Can be "horizontal" or "vertical".
     * @return $this
     */
    public function setLayout(string $layout): Form
    {
        $this->layout = $layout;

        return $this;
    }

    /**
     * set sending method
     *
     * @param  string $method POST|GET
     * @return $this
     */
    public function setMethod(string $method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * set the form mode to 'development' or 'production'
     * in production mode, all the plugins dependencies are combined and compressed in a single css or js file.
     * the css | js files are saved in plugins/min/css and plugins/min/js folders.
     * these 2 folders have to be wrirable (chmod 0755+)
     *
     * @param  string $mode 'development' | 'production'
     * @return $this
     */
    public function setMode(string $mode): Form
    {
        $this->mode = $mode;

        return $this;
    }

    /**
     * Get one or several option(s) value(s)
     *
     * @param  array<string>|null $options An array of options names, or null to return all the options.
     * @return array<string, mixed> Array of the options keys => values
     */
    public function getOptions(?array $options = null)
    {
        $return = 'Error - option(s) not found';
        if (is_null($options)) {
            // return all the options properties and values inside an associative array
            $return = $this->options->getProperties();
        } elseif (is_array($options)) {
            // return an associative array with the options names as keys and values as data
            $output = [];
            foreach ($options as $opt) {
                if (\property_exists($this->options, $opt)) {
                    $output[$opt] = $this->options->get($opt);
                }
            }
            $return = $output;
        }

        return $return;
    }

    /**
     * Sets form layout options to match your framework
     *
     * @param  array<string, mixed> $userOptions (Optional) An associative array containing the
     *                             options names as keys and values as data.
     * @return $this
     */
    public function setOptions(array $userOptions = [])
    {
        foreach ($userOptions as $key => $value) {
            if (\property_exists($this->options, $key)) {
                $this->options->set($key, $value);

                if ($key == 'ajax' && $value === true) {
                    // disable defered scripts & replace domready with a 'loadAjaxForm' CustomEvent if ajax form
                    $this->options->set('deferScripts', false);
                    $this->options->set('openDomReady', 'document.addEventListener(\'loadAjaxForm' . $this->formId . '\', function (e) {');
                }

                /* redefining starting & ending wrappers */

                $wrappersMapping = [
                    'buttonWrapper',
                    'checkboxWrapper',
                    'elementsWrapper',
                    'helperWrapper',
                    'iconAfterWrapper',
                    'iconBeforeWrapper',
                    'inlineCheckboxWrapper',
                    'inlineRadioWrapper',
                    'radioWrapper'
                ];

                if (in_array($key, $wrappersMapping)) {
                    $this->options->setWrapperStartEndHtml(str_replace('Wrapper', '', $key));
                }
            }
        }

        return $this;
    }

    /**
     * load scripts with loadJS
     * https://github.com/muicss/loadjs
     *
     * @param  string $bundle optional loadjs bundle name to wait for
     * @return void
     */
    public function useLoadJs(string $bundle = '')
    {
        $this->setOptions(['useLoadJs' => true, 'loadJsBundle' => $bundle]);
    }

    /**
     * get plugins folder url from Form.php path + DOCUMENT_ROOT path
     * plugins_url will be the complete url to plugins dir
     * i.e. http(s)://www.your-site.com[/subfolder(s)]/phpformbuilder/plugins/
     *
     * @param string $forcedUrl optional URL
     * @return void
     */
    public function setPluginsUrl(string $forcedUrl = ''): void
    {
        // get the current path
        $currentPath = __DIR__;
        $info = new \SplFileInfo($currentPath);
        $realCurrentPath = strtr($info->getRealPath(), [DIRECTORY_SEPARATOR => '/']) . '/';
        // get the root path of the project
        $realRootPath = strtr($realCurrentPath, ['/phpformbuilder/class/phpformbuilder' => '']);
        // Get the path of the plugins folder
        $this->pluginsPath = $realRootPath . 'phpformbuilder/plugins/';
        if (!empty($forcedUrl)) {
            $this->pluginsUrl = $forcedUrl;
            return;
        }
        try {
            if (isset($_SERVER['DOCUMENT_ROOT']) && is_string($_SERVER['DOCUMENT_ROOT'])) {
            // Get the relative path of plugins from the root of the project
            $documentRoot = '';
            if (isset($_SERVER['DOCUMENT_ROOT'])) {
                $rp = realpath($_SERVER['DOCUMENT_ROOT']);
                if ($rp !== false) {
                    $documentRoot = rtrim(strtr($rp, [DIRECTORY_SEPARATOR => '/']), '/') . '/';
                }
            }
            $relativePluginsPath = str_replace($documentRoot, '', $this->pluginsPath);
            // Get the URL of the plugins folder
            $this->pluginsUrl = (((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/' . $relativePluginsPath;
            } else {
                throw new \Exception('$_SERVER[\'DOCUMENT_ROOT\'] is not set or not a string');
            }
        } catch (\Exception $e) {
            throw new \Exception('Error in Form::setPluginsUrl() : ' . $e->getMessage());
        }
    }
}
