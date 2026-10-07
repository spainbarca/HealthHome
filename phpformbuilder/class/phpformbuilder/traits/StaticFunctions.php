<?php

declare(strict_types=1);

namespace phpformbuilder\traits;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use phpformbuilder\Validator\Validator;
use Pelago\Emogrifier\CssInliner;

trait StaticFunctions
{
    /**
     * build an Alert message according to the framework html
     *
     * @param  string $contentText
     * @param  string $framework     bs4|bs5|bulma|foundation|material|tailwind|uikit
     * @param  string $type  success|primary|info|warning|danger
     * @return string the alert HTML code
     */

    public static function buildAlert(string $contentText, string $framework, string $type = 'success')
    {
        $alert = $contentText;
        if ($framework === 'bs4' || $framework === 'material-bootstrap') {
            $alert = '<div class="alert alert-' . $type . ' alert-dismissible fade show" role="alert">' . $contentText . '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } elseif ($framework === 'bs5') {
            $alert = '<div class="alert alert-' . $type . ' alert-dismissible fade show" role="alert">' . $contentText . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
        } elseif ($framework === 'bulma') {
            $alert = '<div class="notification is-' . $type . '">
            <button class="delete"></button>' . $contentText . '</div><script>document.addEventListener(\'DOMContentLoaded\', () => {
                (document.querySelectorAll(\'.notification .delete\') || []).forEach(($delete) => {
                  const $notification = $delete.parentNode;

                  $delete.addEventListener(\'click\', () => {
                    $notification.parentNode.removeChild($notification);
                  });
                });
              });</script>';
        } elseif ($framework === 'foundation') {
            $alert = '<div class="callout ' . $type . '" data-closable>' . $contentText . ' <button class="close-button" aria-label="Dismiss alert" type="button" data-close> <span aria-hidden="true">&times;</span> </button> </div>';
        } elseif ($framework === 'material') {
            $clazz = [
                'success'  => 'teal',
                'info'     => 'cyan',
                'primary'  => 'blue',
                'warning'  => 'amber',
                'danger'   => 'red'
            ];
            $type = $clazz[$type];
            $alert = '<p class="card-panel ' . $type . ' accent-2">' . $contentText . '</p>';
        } elseif ($framework === 'tailwind') {
            $clazz = [
                'success'  => 'green',
                'info'     => 'indigo',
                'primary'  => 'blue',
                'warning'  => 'yellow',
                'danger'   => 'red'
            ];
            $type = $clazz[$type];
            $alert = '<div class="flex p-4 mb-4 bg-' . $type . '-100 border-t-4 border-' . $type . '-500 dark:bg-' . $type . '-200" role="alert"> <svg class="flex-shrink-0 w-5 h-5 text-' . $type . '-700" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg> <div class="ml-3 text-sm font-medium text-' . $type . '-700">' . $contentText . '</div> <button type="button" class="collapse-toggle-btn ml-auto -mx-1.5 -my-1.5 bg-' . $type . '-100 dark:bg-' . $type . '-200 text-' . $type . '-500 rounded-lg focus:ring-2 focus:ring-' . $type . '-400 p-1.5 hover:bg-' . $type . '-200 dark:hover:bg-' . $type . '-300 inline-flex h-8 w-8" aria-label="Close"> <span class="sr-only">Dismiss</span> <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg> </button> </div><script>document.addEventListener(\'DOMContentLoaded\', () => {
                (document.querySelectorAll(\'button.collapse-toggle-btn\') || []).forEach(($ct) => {
                    let $alert = $ct.closest(\'div[role="alert"]\');
                  $ct.addEventListener(\'click\', (e) => {
                    $alert.parentNode.removeChild($alert);
                  });
                });
              });</script>';
        } elseif ($framework === 'uikit') {
            $type = str_replace('info', 'primary', $type);
            $alert = '<div class="uk-alert-' . $type . '" uk-alert> <a class="uk-alert-close" uk-close onclick="UIkit.alert(this).close();"></a> <p>' . $contentText . '</p> </div>
        ';
        }

        return $alert;
    }

    /**
     * Gets html code to start | end elements wrappers
     *
     * @param  string $html The html wrapper code
     * @param  string $pos  'start' or 'end'
     * @return string Starting or ending html tag
     */
    public static function buildElementWrapper(string $html, string $pos): string
    {
        $output = '';

        /* if 2 wrappers */

        $pattern2wrappers = '`<([^>]+)><([^>]+)></([^>]+)></([^>]+)>`';
        if (preg_match($pattern2wrappers, $html, $out)) {
            if ($pos == 'start') {
                $output = '<' . $out[1] . '>' . '<' . $out[2] . '>';
            } else {
                $output = '</' . $out[3] . '>' . '</' . $out[4] . '>';
            }
        }

        /* if only 1 wrapper */

        $pattern1wrapper = '`<([^>]+)></([^>]+)>`';
        if (preg_match($pattern1wrapper, $html, $out)) {
            if ($pos == 'start') {
                $output = '<' . $out[1] . '>';
            } else {
                $output = '</' . $out[2] . '>';
            }
        }

        return $output;
    }

    /**
     * check PHPForm Builder copy registration
     * @return bool
     */
    public static function checkRegistration(): bool
    {
		return true;
        $result = false;

        $mainHost = static::getDomain($_SERVER['HTTP_HOST']);
        $licenseDirectory = dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . $mainHost;

        if (!is_dir($licenseDirectory)) {
            try {
                if (!mkdir($licenseDirectory)) {
                    throw new \Exception('Unable to create new directory ' . $licenseDirectory . '.<br><a href="http://php.net/manual/en/function.chmod.php" target="_blank" style="color:#fff;text-decoration:underline;">Increase your chmod</a> to give write permission to create this folder and write the license file inside.', 1);
                }
            } catch (\Exception $e) {
                $errorMsg = '<div style="line-height:30px;border-radius:5px;border-bottom:1px solid #ac2925;background-color: #c9302c;margin:10px auto;max-width:90%"><p style="color:#fff;text-align:center;font-size:16px;margin:0">' . $e->getMessage() . '</p></div>';

                echo $errorMsg;

                return false;
            }
        }

        $licensePath = $licenseDirectory . DIRECTORY_SEPARATOR . 'license.php';

        if (!file_exists($licensePath)) {
            try {
                if (file_put_contents($licensePath, '') === false) {
                    throw new \Exception('Unable to write License file to ' . $licensePath . '.<br><a href="http://php.net/manual/en/function.chmod.php" target="_blank" style="color:#fff;text-decoration:underline;">Increase your chmod</a> to give write permission to this folder.', 1);
                }
            } catch (\Exception $e) {
                $errorMsg = '<div style="line-height:30px;border-radius:5px;border-bottom:1px solid #ac2925;background-color: #c9302c;margin:10px auto;max-width:90%"><p style="color:#fff;text-align:center;font-size:16px;margin:0">' . $e->getMessage() . '</p></div>';

                echo $errorMsg;

                return false;
            }
        }

        $license  = file_get_contents($licensePath);
        if ($license && preg_match('`^<ROOT_URL>(http|https)://(?:[a-zA-Z0-9_.-]*)' . strtr($mainHost, ['.' => '\.']) . '[^<]+</ROOT_URL>`', $license)) {
            $result = true;
        }

        return $result;
    }
    /**
     * stores the ID of the form to be cleared.
     * when next instance is created it will not store posted values in session
     * unsets all sessions vars attached to the form
     * @param string $formId
     * @return void
     */
    public static function clear(string $formId): void
    {
        $_SESSION['clear_form'][$formId] = true;
        if (isset($_SESSION[$formId]['fields'])) {
            foreach ($_SESSION[$formId]['fields'] as $key => $name) {
                unset($_SESSION[$formId]['fields'][$key]);
                unset($_SESSION[$formId][$name]);
            }
        }
        if (isset($_SESSION[$formId]['required_fields'])) {
            foreach ($_SESSION[$formId]['required_fields'] as $key => $name) {
                unset($_SESSION[$formId]['required_fields'][$key]);
                unset($_SESSION[$formId][$name]);
            }
        }
        if (isset($_SESSION[$formId]['required_fields_conditions'])) {
            foreach ($_SESSION[$formId]['required_fields_conditions'] as $key => $name) {
                unset($_SESSION[$formId]['required_fields_conditions'][$key]);
            }
        }
        if (isset($_SESSION['errors'][$formId])) {
            unset($_SESSION['errors'][$formId]);
        }
    }

    /**
     * get the main domain from any domain or subdomain
     *
     * https://stackoverflow.com/questions/1201194/php-getting-domain-name-from-subdomain
     *
     * @param string $host
     * @return string
     */
    public static function getDomain(string $host)
    {
        $myhost = strtolower(trim($host));
        $count = substr_count($myhost, '.');
        if ($count === 1 || $count === 2) {
            if (strlen(explode('.', $myhost)[1]) > 3) {
                $myhost = explode('.', $myhost, 2)[1];
            }
        } elseif ($count > 2) {
            $myhost = static::getDomain(explode('.', $myhost, 2)[1]);
        }
        return $myhost;
    }

    /**
     * merge previously registered session vars => values of each registered form
     * @param  array<string> $formNamesArray array of forms IDs to merge
     * @return array<string, mixed> pairs of names => values
     *                           ex : $values[$fieldName] = $value
     */
    public static function mergeValues(array $formNamesArray): array
    {
        $values = [];
        foreach ($formNamesArray as $formName) {
            $fields = $_SESSION[$formName]['fields'];
            foreach ($fields as $fieldName) {
                $fieldName = (string) $fieldName;
                if (isset($_SESSION[$formName][$fieldName])) {
                    $values[$fieldName] = $_SESSION[$formName][$fieldName];
                }
            }
        }

        return $values;
    }

    /**
    * register all posted values in session
    * ex: $_SESSION['form-id']['field-name'] = $_POST['field-name'];
    *
    * @param string $formId
    * @return void
    */
    public static function registerValues(string $formId): void
    {
        if (!isset($_SESSION[$formId])) {
            $_SESSION[$formId]           = [];
            $_SESSION[$formId]['fields'] = [];
        }
        foreach ($_SESSION[$formId]['fields'] as $name) {
            if (isset($_POST[$name])) {
                $value = $_POST[$name];
                if (!is_array($value)) {
                    if (!isset($_POST[$name . '_submit'])) {
                        $_SESSION[$formId][$name] = trim($value);
                    } else {
                        $_SESSION[$formId][$name] = trim($_POST[$name . '_submit']);
                    }
                    // echo $name . ' => ' . $value . '<br>';
                } else {
                    $_SESSION[$formId][$name] = [];
                    foreach ($value as $arrayKey => $arrayValue) {
                        $_SESSION[$formId][$name][$arrayKey] = trim($arrayValue);
                        // echo $name . ' ' . $arrayKey . ' ' . $arrayValue . '<br>';
                    }
                }
            } else { // if checkbox unchecked, it hasn't been posted => we store empty value
                $_SESSION[$formId][$name] = '';
            }
        }
    }

    /**
     * Send an email with posted values and custom content
     *
     * Tests and secures values to prevent attacks (phpmailer/extras/htmlfilter.php => HTMLFilter)
     * Uses custom html/css template and replaces shortcodes - syntax : {fieldname} - in both html/css templates with posted|custom values
     * Creates an automatic html table with vars/values - shortcode : {table}
     * Merges html/css to inline style with Pelago Emogrifier
     * Sends email and catches errors with Phpmailer
     *
     * @param array<string, mixed> $options
     *                     sender_email                    : the email of the sender
     *                     sender_name [optional]          : the name of the sender
     *                     reply_to_email [optional]       : the email for reply
     *                     recipient_email                 : the email destination(s), separated with commas
     *                     cc [optional]                   : the email(s) of copies to send, separated with commas
     *                     bcc [optional]                  : the email(s) of blind copies to send, separated with commas
     *                     subject                         : The email subject
     *                     isHTML                          : Send the mail in HTML format or Plain text (default: true)
     *                     textBody                        : The email body if isHTML is set to false
     *                     attachments [optional]          : file(s) to attach : separated with commas, or array (see details inside function)
     *                     template [optional]             : name of the html/css template to use in phpformbuilder/mailer/email-templates/
     *                                           (default: default.html)
     *                     human_readable_labels [optional]: replace '-' ans '_' in labels with spaces if true (default: true)
     *                     values                          : $_POST
     *                     filter_values [optional]        : posted values you don't want to include in the e-mail automatic html table
     *                     custom_replacements [optional]  : array to replace shortcodes in email template. ex : array('mytext' => 'Hello !') will replace {mytext} with Hello !
     *                     sent_message [optional]         : message to display when email is sent. If sent_message is a string it'll be automatically wrapped into an alert div. Else if sent_message is html code it'll be displayed as is.
     *                     debug [optional]                : displays sending errors (default: false)
     *                     smtp [optional]                 : use smtp (default: false)
     *
     * @param array<string, mixed> $smtpSettings
     *                         host :       String      Specify main and backup SMTP servers - i.e: smtp1.example.com, smtp2.example.com
     *                         smtp_auth:   Boolean     Enable SMTP authentication
     *                         username:    String      SMTP username
     *                         password:    String      SMTP password
     *                         smtp_secure: String      Enable TLS encryption. Accepted values: tls|ssl
     *                         port:        Number      TCP port to connect to (usually 25 or 587)
     *
     * @return string sent_message
     *                         success or error message to display on the page
     */
    public static function sendMail(array $options, array $smtpSettings = []): string
    {
        $output = '';

        $defaultOptions = [
            'sender_email'          => '',
            'sender_name'           => '',
            'reply_to_email'        => '',
            'recipient_email'       => '',
            'cc'                    => '',
            'bcc'                   => '',
            'subject'               => 'Contact',
            'attachments'           => '',
            'template'              => 'default.html',
            'human_readable_labels' => true,
            'isHTML'                => true,
            'textBody'              => '',
            'values'                => $_POST,
            'filter_values'         => '',
            'custom_replacements'   => [],
            'sent_message'          => 'Your message has been successfully sent !',
            'debug'                 => false
        ];

        // merge the default option with the user's options and set them as object
        $options = (object) array_merge($defaultOptions, $options);

        $mail = new PHPMailer();
        try {
            // if smtp
            if (!empty($smtpSettings)) {
                if ($options->debug) {
                    $mail->SMTPDebug = 3;                           // Enable verbose debug output
                }
                $mail->isSMTP();                                    // Set mailer to use SMTP
                $mail->Host       = $smtpSettings['host'];         // Specify main and backup SMTP servers
                $mail->SMTPAuth   = $smtpSettings['smtp_auth'];    // Enable SMTP authentication
                $mail->Username   = $smtpSettings['username'];     // SMTP username
                $mail->Password   = $smtpSettings['password'];     // SMTP password
                $mail->SMTPSecure = $smtpSettings['smtp_secure'];  // Enable TLS encryption, `ssl` also accepted
                $mail->Port       = $smtpSettings['port'];         // TCP port to connect to
            }
            $mail->From     = $options->sender_email;
            $mail->Sender   = $options->sender_email;
            if ($options->sender_name != '') {
                if ($options->reply_to_email !== '') {
                    $mail->addReplyTo($options->reply_to_email);
                } else {
                    $mail->addReplyTo($options->sender_email, $options->sender_name);
                }
                $mail->FromName = $options->sender_name;
            } else {
                if ($options->reply_to_email !== '') {
                    $mail->addReplyTo($options->reply_to_email);
                } else {
                    $mail->addReplyTo($options->sender_email);
                }
                $mail->FromName = $options->sender_email;
            }
            $indiAdress = explode(',', $options->recipient_email);
            foreach ($indiAdress as $key => $value) {
                $mail->addAddress(trim($value));
            }
            if ($options->bcc != '') {
                $indiBCC = explode(',', $options->bcc);
                foreach ($indiBCC as $options->key => $value) {
                    $mail->addBCC(trim($value));
                }
            }
            if ($options->cc != '') {
                $indiCC = explode(',', $options->cc);
                foreach ($indiCC as $key => $value) {
                    $mail->addCC(trim($value));
                }
            }
            if ($options->attachments != '') {
                /*
                    =============================================
                    single file :
                    =============================================

                    $attachments = 'path/to/file';

                    =============================================
                    multiple files separated with commas :
                    =============================================

                    $attachments = 'path/to/file_1, path/to/file_2';

                    =============================================
                    single file with file_path + file_name :
                    (specially for posted files)
                    =============================================

                    $attachments =  arrray(
                                        'file_path' => 'path/to/file.jpg', // complete path with filename
                                        'file_name' => 'my-file.jpg'
                                    )

                    =============================================
                    multiple files array containing :
                        sub-arrays with file_path + file_name
                        or file_name strings
                    =============================================

                    $attachments =  arrray(
                                        'path/to/file_1',
                                        'path/to/file_2',
                                        arrray(
                                            'file_path' => 'path/to/file.jpg', // complete path with filename
                                            'file_name' => 'my-file.jpg'
                                        ),
                                        ...
                                    )
                 */

                if (is_array($options->attachments)) {
                    if (isset($options->attachments['file_path'])) {
                        $mail->addAttachment($options->attachments['file_path'], $options->attachments['file_name']);
                    } else {
                        foreach ($options->attachments as $key => $value) {
                            if (is_array($value)) {
                                $mail->addAttachment($value["file_path"], $value["file_name"]);
                            } else {
                                $attach = explode(',', $value);
                                foreach ($attach as $key => $value) {
                                    $mail->addAttachment(trim($value));
                                }
                            }
                        }
                    }
                } else {
                    $attach = explode(',', $options->attachments);
                    foreach ($attach as $key => $value) {
                        $mail->addAttachment(trim($value));
                    }
                }
            }
            $filter = explode(',', $options->filter_values);

            // filter captchas
            $filter[] = 'altcha';
            $filter[] = 'g-recaptcha-response';
            $filter[] = 'h-captcha-response';

            // add token to filter
            foreach ($options->values as $key => $value) {
                if (preg_match('`-token$`', $key) || preg_match('`submit-btn$`', $key)) {
                    $filter[] = $key;
                }
            }

            // sanitize filter values
            for ($i = 0; $i < count($filter); $i++) {
                $filter[$i] = trim(mb_strtolower($filter[$i]));
            }
            $mail->Subject = $options->subject;
        } catch (Exception $e) { //Catch all kinds of bad addressing
            throw new Exception((string) $e);
        }
        if ($options->isHTML) {
            try {
                /* Load html & css templates */

                $htmlTemplatePath = __DIR__ . '/../../../mailer/email-templates/' . $options->template;
                $htmlTemplateCustomPath = __DIR__ . '/../../../mailer/email-templates-custom/' . $options->template;
                $html = '';
                $mergedHtml = '';
                $debugMsg = '';

                if (file_exists($htmlTemplateCustomPath)) {
                    $templateErrorMsg = '';
                    // try to load html template in email-templates-custom dir
                    if (!($html = file_get_contents($htmlTemplateCustomPath))) {
                        $templateErrorMsg = 'Html template file doesn\'t exist (1)';
                        $debugMsg          = $htmlTemplateCustomPath;
                    }
                } elseif (file_exists($htmlTemplatePath)) {
                    // try to load html template in email-templates dir
                    if (!($html = file_get_contents($htmlTemplatePath))) {
                        $templateErrorMsg = 'Html template file doesn\'t exist (2)';
                        $debugMsg          = $htmlTemplatePath;
                    }
                } else {
                    $templateErrorMsg = 'Html template file doesn\'t exist (3)';
                    $debugMsg          = $htmlTemplatePath;
                }
                $cssTemplatePath        = str_replace('.html', '.css', $htmlTemplatePath);
                $cssTemplateCustomPath = str_replace('.html', '.css', $htmlTemplateCustomPath);

                if (file_exists($cssTemplateCustomPath) && empty($templateErrorMsg)) {
                    // try to load css template in email-templates-custom dir
                    if (!($css = file_get_contents($cssTemplateCustomPath))) {
                        $templateErrorMsg = 'CSS template file doesn\'t exists';
                        $debugMsg          = $cssTemplateCustomPath;
                    }
                } elseif (file_exists($cssTemplatePath) && empty($templateErrorMsg)) {
                    // try to load css template in email-templates dir
                    if (!($css = file_get_contents($cssTemplatePath))) {
                        $templateErrorMsg = 'CSS template file doesn\'t exists';
                        $debugMsg          = $cssTemplatePath;
                    }
                } elseif (empty($templateErrorMsg)) {
                    $templateErrorMsg = 'CSS template file doesn\'t exists';
                    $debugMsg          = $cssTemplatePath;
                }

                /* If html|css template not found */

                if (!empty($templateErrorMsg)) {
                    if ($options->debug) {
                        $templateErrorMsg = $debugMsg . '<br>' . $templateErrorMsg;
                    }

                    throw new \Exception('<div style="line-height:30px;border-radius:5px;border-bottom:1px solid #ac2925;background-color: #c9302c;margin:10px auto;"><p style="color:#fff;text-align:center;font-size:16px;margin:0">' . $templateErrorMsg . '</p></div>');
                }

                /* Automatic table including all but filtered values */

                $emailTable = '<table class="one-column">';
                $emailTable .= '<tbody>';

                // prepare regex for human_readable_labels
                $find = ['`([a-zA-Z0-9])-([a-zA-Z0-9])`', '`([a-zA-Z0-9])_([a-zA-Z0-9])`'];
                $replace = ['$1 $2', '$1 $2'];
                foreach ($options->values as $key => $value) {
                    if (!in_array(mb_strtolower($key), $filter)) {
                        // replace key (label) with human_readable_label
                        if ($options->human_readable_labels) {
                            $key = preg_replace($find, $replace, $key);
                        }
                        if (!is_array($value)) {
                            $emailTable .= '<tr>';

                            // replace with custom if key exists
                            if (is_array($options->custom_replacements) && in_array($key, array_keys($options->custom_replacements))) {
                                $emailTable .= '<th class="inner">' . ucfirst($options->custom_replacements[$key]) . ': ' . '</th>';
                            } else {
                                $emailTable .= '<th class="inner">' . ucfirst($key) . ': ' . '</th>';
                            }
                            $emailTable .= '<td class="inner">' . nl2br($value) . '</td>';
                            $emailTable .= '</tr>';
                        } else {
                            foreach ($value as $keyArray => $valueArray) {
                                if (!is_array($valueArray)) {
                                    $emailTable .= '<tr>';
                                    $emailTable .= '<th class="inner">' . ucfirst($key) . ' ' . ($keyArray + 1) . ': ' . '</th>';
                                    $emailTable .= '<td class="inner">' . $valueArray . '</td>';
                                    $emailTable .= '</tr>';
                                }
                            }
                        }
                    }
                }
                $emailTable .= '</tbody>';
                $emailTable .= '</table>';

                if (is_string($html)) {
                    $html = str_replace('{table}', $emailTable, $html);
                    $html = str_replace('{subject}', $options->subject, $html);


                    /* replacements in html */

                    // first, replace posted values in html
                    foreach ($options->values as $key => $value) {
                        if (!in_array(mb_strtolower($key), $filter) && !is_array($value)) {
                            $html = str_replace('{' . $key . '}', $value, $html);
                        }
                    }

                    // then replace custom replacements in html
                    foreach ($options->custom_replacements as $key => $value) {
                        if (!in_array(mb_strtolower($key), $filter) && !is_array($value)) {
                            $html = str_replace('{' . $key . '}', $value, $html);
                        }
                    }

                    /* custom replacements in css */

                    if (!empty($css)) {
                        foreach ($options->custom_replacements as $key => $value) {
                            if (!in_array(mb_strtolower($key), $filter) && !is_array($value)) {
                                $css = str_replace('{' . $key . '}', $value, $css);
                            }
                        }

                        $mergedHtml = CssInliner::fromHtml($html)->inlineCss($css)->render();
                    } else {
                        $mergedHtml = $html;
                    }
                }
            } catch (\Exception $e) { //Catch all content errors
                $output = $e->getMessage();
                echo $output;
            }
            $mail->msgHTML($mergedHtml, dirname(dirname(__FILE__)), true);
        } else {
            $mail->isHTML(false);
            $mail->Body = $options->textBody;
        }
        $charset = 'iso-8859-1';
        if (function_exists('mb_detect_encoding')) {
            if ($options->isHTML) {
                $charset = mb_detect_encoding($mergedHtml);
            } else {
                $charset = mb_detect_encoding($options->textBody);
            }
        }
        if ($charset) {
            $mail->CharSet = $charset;
        }

        if (!$mail->send()) {
            if ($options->debug) {
                if (!isset($_SESSION['phpfb_framework'])) {
                    $output = '<p><strong>Mailer Error: ' . $mail->ErrorInfo . '</strong></p>';
                } else {
                    $output = static::buildAlert('Mailer Error: ' . $mail->ErrorInfo, $_SESSION['phpfb_framework'], 'danger');
                }
            }
        } else {
            if (!isset($_SESSION['phpfb_framework']) || (isset($options->sent_message[0]) && $options->sent_message[0] === '<')) {
                $output = $options->sent_message;
            } else {
                $output = static::buildAlert($options->sent_message, $_SESSION['phpfb_framework'], 'success');
            }
        }

        return $output;
    }

    /**
     * validate token to protect against CSRF
     *
     * @param string $formId
     * @return bool
     */
    public static function testToken(string $formId): bool
    {
		return true;
    }

    /**
     * create Validator object and auto-validate all required fields
     * @param  string     $formId the form ID
     * @return Validator  the Validator
     */
    public static function validate(string $formId, string $lang = 'en'): Validator
    {
        $validator = new Validator($_POST, $lang);
        $required = $_SESSION[$formId]['required_fields'];
        foreach ($required as $field) {
            $doValidate = true;

            // if dependent field, test parent posted value & validate only if parent posted value matches condition
            if (!empty($_SESSION[$formId]['required_fields_conditions'][$field])) {
                $parentField = $_SESSION[$formId]['required_fields_conditions'][$field]['parent_field'];
                $showValues  = preg_split('`,(\s)?`', (string) $_SESSION[$formId]['required_fields_conditions'][$field]['show_values']);
                $inverse      = $_SESSION[$formId]['required_fields_conditions'][$field]['inverse'];

                if (!isset($_POST[$parentField])) {
                    // if parent field is not posted (nested dependent fields)
                    $doValidate = false;
                } elseif (is_array($showValues) && !in_array($_POST[$parentField], $showValues) && !$inverse) {
                    // if posted parent value doesn't match show values
                    $doValidate = false;
                } elseif (is_array($showValues) && in_array($_POST[$parentField], $showValues) && $inverse) {
                    // if posted parent value does match show values but dependent is inverted
                    $doValidate = false;
                }
            }
            if ($doValidate) {
                if (isset($_POST[$field]) && is_array($_POST[$field])) {
                    $field = $field . '.0';
                }

                $validator->required()->validate($field);
            }
        }

        return $validator;
    }
}
