<?php // phpcs:disable PSR1.Files.SideEffects

use phpformbuilder\Form;
use registration\Registration;

include_once 'autoload.php';

define('CURL_VERBOSE', true);

$userMsg           = '';
$userMsgClass     = 'danger';
$alreadyRegistered = false;
$justRegistered    = false;
$justUnregistered  = false;

$serverMsg = Registration::checkServer();

if (is_array($serverMsg)) {
    $userMsg = '<p class="mb-0">';
    foreach ($serverMsg as $msg) {
        $userMsg .= $msg . '<br>';
    }
    $userMsg .= '</p>';
    $userMsgClass = 'danger';
}

$scriptUrl = array(
    'script_url' => str_ireplace('/phpformbuilder/register.php', '', 'http' . (empty($_SERVER['HTTPS']) ? '' : 's') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'])
);

$currentUrl = array(
    'current_url' => Registration::getCurrentUrl(true)
);

try {
    $alreadyRegistered = Form::checkRegistration();
    $mainHost = Form::getDomain($_SERVER['HTTP_HOST']);
    if ($alreadyRegistered !== true && (is_dir(__DIR__ . '/' . $mainHost) && file_exists(__DIR__ . '/' . $mainHost . '/license.php')) && $handle = fopen(__DIR__ . '/' . $mainHost . '/license.php', "w+")) {
        // empty license.php if wrong registration data
        fclose($handle);
    }
    $license  = file_get_contents(__DIR__ . '/' . $mainHost . '/license.php');
} catch (Exception $e) {
    var_dump($e);
    exit('License file not found');
}

if (isset($_POST['registration-form']) && $alreadyRegistered === false) {
    try {
        $ch = curl_init();

        $postfields = array_merge($_POST, $scriptUrl);

        $userAgent = "Mozilla/5.0 (Windows NT 6.3; WOW64; rv:48.0) Gecko/20100101 Firefox/48.0"; //set user agent
        $connectTimeout = 10; //set connection timeout

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.phpformbuilder.pro/registration/register.php");
        curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $connectTimeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $connectTimeout);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postfields));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);

        if (CURL_VERBOSE === true) {
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            $verbose = fopen('php://temp', 'w+');
            curl_setopt($ch, CURLOPT_STDERR, $verbose);
        }

        $jsonResult = curl_exec($ch);

        if ($jsonResult === false) {
            throw new Exception(curl_error($ch), curl_errno($ch));
        }
    } catch (Exception $e) {
        trigger_error(
            sprintf(
                'Curl failed with error #%d: %s',
                $e->getCode(),
                $e->getMessage()
            ),
            E_USER_ERROR
        );
    }

    $result = json_decode((string) $jsonResult);
    // if no JSON error
    if (json_last_error() === JSON_ERROR_NONE) {
        if (isset($result->error_msg) && is_string($result->error_msg)) {
            $userMsg = $result->error_msg;
            $userMsgClass = 'danger';
        } elseif (is_string($result->notification_case) && is_string($result->notification_text)) {
            if ($result->notification_case !== "notification_license_ok") {
                $userMsg = $result->notification_text;
                $userMsgClass = 'danger';
            } else {
                try {
                    file_put_contents(__DIR__ . '/' . $mainHost . '/license.php', $result->notification_text);
                    $userMsg = '<p>thank you for registering your copy.</p><p>You can now freely use PHP Form Builder.</p><hr><p><strong><u>Important</u></strong>:<br>delete this file from your server now to avoid any unwanted use.</p>';
                    $userMsgClass = 'success';
                    $justRegistered = true;
                } catch (Exception $e) {
                    var_dump($e);
                    exit('Unable to write file /' . $mainHost . '/license.php - please check your write access (chmod)');
                }
            }
        } else {
            $userMsg = '<p>An error occured during the registration process.</p>';
            $curlErrno = curl_errno($ch);
            $userMsg .= '<p><strong>cUrl error #' . $curlErrno . ' ' . htmlspecialchars(curl_error($ch)) . '</strong></p>';
            if ($curlErrno == 7) {
                $userMsg .= '<p><a href="https://www.google.com/search?q=php+curl+Ierror+7+Permission+denied&oq=php+curl+Ierror+7+Permission+denied">CURL ERROR 7 Failed to connect to ... Permission denied error</a> is caused, when for any reason curl request is blocked by some firewall or similar thing.</p>';
                $userMsg .= '<p>To solve this issue you have to change your firewall settings.</p>';
                $userMsg .= '<p>If your server uses <abbr title="Security-Enhanced Linux">SELinux</abbr> security module, you can:</p>';
                $userMsg .= '<ul class="d-inline-block text-left">';
                $userMsg .= '<li><a href="https://www.google.com/search?q=disable+selinux">disable SELinux</a></li>';
                $userMsg .= '<li><a href="https://www.google.com/search?q=configure+selinux+policy">configure SELinux policy</a></li>';
                $userMsg .= '</ul>';
            }
            if (CURL_VERBOSE === true && is_resource($verbose)) {
                rewind($verbose);
                $verboseLog = stream_get_contents($verbose);
                $userMsg .= '<p><strong>Verbose information:</strong></p><div class="d-inline-block"><pre class="text-left">' . htmlspecialchars((string) $verboseLog) . '</pre></div>';
            }
            $userMsgClass = 'danger';
        }
    }
    curl_close($ch);
}

if (isset($_POST['unregister-form']) && $alreadyRegistered === true) {
    $ch = curl_init();

    $postfields = array_merge($_POST, $currentUrl);

    $userAgent = "Mozilla/5.0 (Windows NT 6.3; WOW64; rv:48.0) Gecko/20100101 Firefox/48.0"; //set user agent
    $connectTimeout = 10; //set connection timeout

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://www.phpformbuilder.pro/registration/unregister.php");
    curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $connectTimeout);
    curl_setopt($ch, CURLOPT_TIMEOUT, $connectTimeout);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postfields));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
    $jsonResult = curl_exec($ch);
    $result = json_decode((string) $jsonResult);
    // $info = curl_getinfo($ch);
    curl_close($ch);

    if (isset($result->notification_case, $result->notification_text)) {
        if ($result->notification_case !== "notification_license_ok") {
            $userMsg = (string) $result->notification_text;
            $userMsgClass = 'danger';
        } else {
            try {
                $mainHost = Form::getDomain($_SERVER['HTTP_HOST']);
                if ($handle = fopen(__DIR__ . '/' . $mainHost . '/license.php', "w+")) {
                    fclose($handle);
                }
                $userMsg = '<p>Your copy has been successfully unregistered.</p><hr><p><strong><u>Important</u></strong>:<br>delete this file from your server now to avoid any unwanted use.</p>';
                $userMsgClass = 'success';
                $justUnregistered = true;
            } catch (Exception $e) {
                var_dump($e);
                exit('Unable to write file /' . $mainHost . '/license.php - please check your write access (chmod)');
            }
        }
    } else {
        $userMsg = 'An error occured during the un-registration process.';
        $userMsgClass = 'danger';
    }
} elseif ($alreadyRegistered === true) {
    $userMsg = '<p>Your PHP Form Builder copy is already registered on this domain.</p><hr><p>If you want to install on another domain<br>you have to unregister your copy.</p>';
    $userMsgClass = 'success';
}

if (!empty($userMsg)) {
    $userMsg = '<div class="alert alert-' . $userMsgClass . ' text-center my-5" role="alert">' . $userMsg . '</div>';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Php Form Builder - Register</title>
    <meta name="robots" content="noindex, nofollow">

    <!-- Bootstrap 4 CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">

    <!-- font-awesome CSS -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/solid.css" integrity="sha384-TbilV5Lbhlwdyc4RuIV/JhD8NR+BfMrvz4BL5QFa2we1hQu6wvREr3v6XSRfCTRp" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/fontawesome.css" integrity="sha384-ozJwkrqb90Oa3ZNb+yKFW2lToAWYdTiF1vt8JiH5ptTGHTGcN7qdoR1F95e0kYyG" crossorigin="anonymous">

    <link href="plugins/ladda/ladda-themeless.min.css" rel="stylesheet" media="screen">
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col">
                <?php echo $userMsg; ?>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-sm-10 col-md-8 col-lg-6">
                <?php if ($alreadyRegistered === true) {
                    if ($justUnregistered === false) { ?>
                        <form id="unregister-form" action="register.php" method="POST" novalidate>
                            <div>
                                <input name="unregister-form" type="hidden" value="1">
                                <input name="license_content" type="hidden" value="<?php echo $license; ?>">
                            </div>
                            <fieldset class="my-4">
                                <legend class="text-center">
                                    PHP Form Builder - Unregister
                                </legend>
                                <p class="text-center">Please fill-in the form below to unregister your copy</p>
                                <hr class="mb-5">
                                <div class="form-group">
                                    <label for="user-purchase-code" class="form-control-label">
                                        Enter your purchase code <sup class="text-danger">* </sup>
                                    </label>
                                    <input id="user-purchase-code" name="user-purchase-code" type="text" value="" required class="form-control">
                                </div>
                            </fieldset>
                            <div class="form-group text-center">
                                <button type="submit" name="submit-btn" value="1" class="btn btn-danger ladda-button" data-style="zoom-in">
                                    Unregister<i class="bi bi-x ms-3"></i>
                                </button>
                            </div>
                            <p>&nbsp;</p>
                            <p class="text-center my-5"><a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-">Where can I find my purchase code?</a></p>
                        </form>
                        <?php
                    }
                } elseif ($justRegistered === false) { ?>
                    <form id="registration-form" action="register.php" method="POST" novalidate>
                        <div>
                            <input name="registration-form" type="hidden" value="1">
                            <input name="license_content" type="hidden" value="<?php echo $license; ?>">
                        </div>
                        <fieldset class="my-4">
                            <legend class="text-center">
                                PHP Form Builder Registration
                            </legend>
                            <p class="text-center">Please fill-in the form below to register your copy</p>
                            <hr class="mb-5">
                            <div class="form-group">
                                <label for="user-purchase-code" class="form-control-label">
                                    Enter your purchase code <sup class="text-danger">* </sup>
                                </label>
                                <input id="user-purchase-code" name="user-purchase-code" type="text" value="" required class="form-control">
                            </div>
                        </fieldset>
                        <div class="form-group text-center">
                            <button type="submit" name="submit-btn" value="1" class="btn btn-success ladda-button" data-style="zoom-in">
                                Register<i class="bi bi-check ms-3"></i>
                            </button>
                        </div>
                        <p>&nbsp;</p>
                        <p class="text-center my-5"><a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-">Where can I find my purchase code?</a></p>
                    </form>
                <?php } ?>
            </div>
        </div>
    </div>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
    <!-- Bootstrap 4 JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>
    <script src="plugins/ladda/spin.min.js" defer></script>
    <script src="plugins/ladda/ladda.min.js" defer></script>
    <script type="text/javascript" defer>
        jQuery(document).ready(function($) {
            var forms = Array('#registration-form', '#unregister-form');
            for (var i = forms.length - 1; i >= 0; i--) {
                var form = forms[i];
                if ($(form)[0]) {
                    var l = Ladda.create(document.querySelector(form + ' .ladda-button')),
                        $laddaForm = $(form + ' .ladda-button').closest('form');
                    $(form + ' .ladda-button').on('click', function() {
                        l.start();
                        $(this).closest('form').submit();
                    });
                }
            }
        });
    </script>

</body>

</html>
