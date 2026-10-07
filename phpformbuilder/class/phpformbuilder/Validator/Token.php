<?php

// phpcs:disable PSR1.Files.SideEffects

//MAIN CONFIG FILE OF AUTO PHP LICENSER. CAN BE EDITED MANUALLY OR GENERATED USING Extra Tools > Configuration Generator TAB IN AUTO PHP LICENSER DASHBOARD. THE FILE MUST BE INCLUDED IN YOUR SCRIPT BEFORE YOU PROVIDE IT TO USER.


//-----------BASIC SETTINGS-----------//

if (!defined("APL_SALT")) {
    //Random salt used for encryption. It should contain random symbols (16 or more recommended) and be different for each application you want to protect. Cannot be modified after installing script.
    define("APL_SALT", "g5*j%EF56BB=-eDf");

    //The URL (without / at the end) where Auto PHP Licenser from /WEB directory is installed on your server. No matter how many applications you want to protect, a single installation is enough.
    define("APL_ROOT_URL", "https://www.miglisoft.com/phpmillion");

    //Unique numeric ID of product that needs to be licensed. Can be obtained by going to Products > View Products tab in Auto PHP Licenser dashboard and selecting product to be licensed. At the end of URL, you will see something like products_edit.php?product_id=NUMBER, where NUMBER is unique product ID. Cannot be modified after installing script.
    define("APL_PRODUCT_ID", 2);

    //Time period (in days) between automatic license verifications. The lower the number, the more often license will be verified, but if many end users use your script, it can cause extra load on your server. Available values are between 1 and 365. Usually 7 or 14 days are the best choice.
    define("APL_DAYS", 7);

    //Place to store license signature and other details. "DATABASE" means data will be stored in MySQL database (recommended), "FILE" means data will be stored in local file. Only use "FILE" if your application doesn't support MySQL. Otherwise, "DATABASE" should always be used. Cannot be modified after installing script.
    define("APL_STORAGE", "FILE");

    //Name of table (will be automatically created during installation) to store license signature and other details. Only used when "APL_STORAGE" set to "DATABASE". The more "harmless" name, the better. Cannot be modified after installing script.
    define("APL_DATABASE_TABLE", "");

    //Name and location (relative to directory where "apl_core_configuration.php" file is located, cannot be moved outside this directory) of file to store license signature and other details. Can have ANY name and extension. The more "harmless" location and name, the better. Cannot be modified after installing script. Only used when "APL_STORAGE" set to "FILE" (file itself can be safely deleted otherwise).
    $mainHost = getDomain($_SERVER['HTTP_HOST']);

    define("APL_LICENSE_FILE_LOCATION", '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . $mainHost . DIRECTORY_SEPARATOR . 'license.php');

    //Name and location (relative to directory where "apl_core_configuration.php" file is located, cannot be moved outside this directory) of MySQL connection file. Only used when "APL_STORAGE" set to "DATABASE" (file itself can be safely deleted otherwise).
    define("APL_MYSQL_FILE_LOCATION", "");

    //Notification to be displayed when operation fails because of connection issues (no Internet connection, your domain is blacklisted by user, etc.) Other notifications will be automatically fetched from your Auto PHP Licenser installation.
    define("APL_NOTIFICATION_NO_CONNECTION", "Can't connect to licensing server.");

    //Notification to be displayed when updating database fails. Only used when APL_STORAGE set to DATABASE.
    define("APL_NOTIFICATION_DATABASE_WRITE_ERROR", "Can't write to database.");

    //Notification to be displayed when updating license file fails. Only used when APL_STORAGE set to FILE.
    define("APL_NOTIFICATION_LICENSE_FILE_WRITE_ERROR", "Can't write to license file.");

    //Notification to be displayed when installation wizard is launched again after script was installed.
    define("APL_NOTIFICATION_SCRIPT_ALREADY_INSTALLED", "Script is already installed (or database not empty).");

    //Notification to be displayed when license could not be verified because license is not installed yet or corrupted.
    define("APL_NOTIFICATION_LICENSE_CORRUPTED", "License is not installed yet or corrupted.");

    //Notification to be displayed when license verification does not need to be performed. Used for debugging purposes only, should never be displayed to end user.
    define("APL_NOTIFICATION_BYPASS_VERIFICATION", "No need to verify");


    //-----------ADVANCED SETTINGS-----------//


    //Secret key used to verify if configuration file included in your script is genuine (not replaced with 3rd party files). It can contain any number of random symbols and should be different for each application you want to protect. You should also change its name from "APL_INCLUDE_KEY_CONFIG" to something else, let's say "MY_CUSTOM_SECRET_KEY"
    define("TOKEN_CONFIG", "885kufR**.xp5e6S");

    //IP address of your Auto PHP Licenser installation. If IP address is set, script will always check if "APL_ROOT_URL" resolves to this IP address (very useful against users who may try blocking or nullrouting your domain on their servers). However, use it with caution because if IP address of your server is changed in future, old installations of protected script will stop working (you will need to update this file with new IP and send updated file to end user). If you want to verify licensing server, but don't want to lock it to specific IP address, you can use APL_ROOT_NAMESERVERS option (because nameservers change is unlikely).
    define("APL_ROOT_IP", "");

    //Nameservers of your domain with Auto PHP Licenser installation (only works with domains and NOT subdomains). If nameservers are set, script will always check if "APL_ROOT_NAMESERVERS" match actual DNS records (very useful against users who may try blocking or nullrouting your domain on their servers). However, use it with caution because if nameservers of your domain are changed in future, old installations of protected script will stop working (you will need to update this file with new nameservers and send updated file to end user). Nameservers should be formatted as an array. For example: array("ns1.phpmillion.com", "ns2.phpmillion.com"). Nameservers are NOT CAse SensitIVE.
    define("APL_ROOT_NAMESERVERS", array()); //ATTENTION! THIS FEATURE ONLY WORKS WITH PHP 7, SO UNCOMMENT THIS LINE ONLY IF PROTECTED SCRIPT WILL RUN ON PHP 7 SERVER!

    //When option set to "YES", all files and MySQL data will be deleted when illegal usage is detected. This is very useful against users who may try using pirated software; if someone shares his license with 3rd parties (by sending it to a friend, posting on warez forums, etc.) and you cancel this license, Auto PHP Licenser will try to delete all script files and any data in MySQL database for everyone who uses cancelled license. For obvious reasons, data will only be deleted if license is cancelled. If license is invalid or expired, no data will be modified. Use at your own risk!
    define("APL_DELETE_CANCELLED", "NO");

    //When option set to "YES", all files and MySQL data will be deleted when cracking attempt is detected. This is very useful against users who may try cracking software; if some unauthorized changes in core functions are detected, Auto PHP Licenser will try to delete all script files and any data in MySQL database. Use at your own risk!
    define("APL_DELETE_CRACKED", "NO");


    //-----------NOTIFICATIONS FOR DEBUGGING PURPOSES ONLY. SHOULD NEVER BE DISPLAYED TO END USER-----------//


    define("APL_CORE_NOTIFICATION_INVALID_SALT", "Configuration error: invalid or default encryption salt");
    define("APL_CORE_NOTIFICATION_INVALID_ROOT_URL", "Configuration error: invalid root URL of Auto PHP Licenser installation");
    define("APL_CORE_NOTIFICATION_INVALID_PRODUCT_ID", "Configuration error: invalid product ID");
    define("APL_CORE_NOTIFICATION_INVALID_VERIFICATION_PERIOD", "Configuration error: invalid license verification period");
    define("APL_CORE_NOTIFICATION_INVALID_STORAGE", "Configuration error: invalid license storage option");
    define("APL_CORE_NOTIFICATION_INVALID_TABLE", "Configuration error: invalid MySQL table name to store license signature");
    define("APL_CORE_NOTIFICATION_INVALID_LICENSE_FILE", "Configuration error: invalid license file location (or file not writable)");
    define("APL_CORE_NOTIFICATION_INVALID_MYSQL_FILE", "Configuration error: invalid MySQL file location (or file not readable)");
    define("APL_CORE_NOTIFICATION_INVALID_ROOT_IP", "Configuration error: invalid IP address of your Auto PHP Licenser installation");
    define("APL_CORE_NOTIFICATION_INVALID_ROOT_NAMESERVERS", "Configuration error: invalid nameservers of your Auto PHP Licenser installation");
    define("APL_CORE_NOTIFICATION_INACCESSIBLE_ROOT_URL", "Server error: impossible to establish connection to your Auto PHP Licenser installation");
    define("APL_CORE_NOTIFICATION_INVALID_DNS", "License error: actual IP address and/or nameservers of your Auto PHP Licenser installation don't match specified IP address and/or nameservers");


    //-----------SOME EXTRA STUFF. SHOULD NEVER BE REMOVED OR MODIFIED-----------//
    define("APL_DIRECTORY", __DIR__);
    define("APL_MYSQL_QUERY", "LOCAL");
}

/**
 * get the main domain from any domain or subdomain
 *
 * https://stackoverflow.com/questions/1201194/php-getting-domain-name-from-subdomain
 *
 * @param string $host
 * @return String
 **/
function getDomain($host)
{
    $myhost = strtolower(trim($host));
    $count = substr_count($myhost, '.');
    if ($count === 1 || $count === 2) {
        if (strlen(explode('.', $myhost)[1]) > 3) {
            $myhost = explode('.', $myhost, 2)[1];
        }
    } elseif ($count > 2) {
        $myhost = getDomain(explode('.', $myhost, 2)[1]);
    }
    return $myhost;
}

//ALL THE FUNCTIONS IN THIS FILE START WITH apl TO PREVENT DUPLICATED NAMES WHEN AUTO PHP LICENSER SOURCE FILES ARE INTEGRATED INTO ANY SCRIPT

//encrypt text with custom key
/**
 * Encrypts a string using AES 256 encryption in CBC mode.
 *
 * @param string $string The string to be encrypted.
 * @param string $key The encryption key.
 * @return string The encrypted string.
 */
function aplCustomEncrypt(string $string, string $key): string
{
    $encryptedString = null;

    if (!empty($string) && !empty($key)) {
        $iv = (string) openssl_random_pseudo_bytes((int) openssl_cipher_iv_length("aes-256-cbc")); //generate an initialization vector

        $encryptedString = openssl_encrypt($string, "aes-256-cbc", $key, 0, $iv); //encrypt the string using AES 256 encryption in CBC mode using encryption key and initialization vector
        $encryptedString = base64_encode($encryptedString . "::" . $iv); //the $iv is just as important as the key for decrypting, so save it with encrypted string using a unique separator "::"
    }

    return (string) $encryptedString;
}

/**
 * Decrypts a string using a custom key.
 *
 * @param string $string The string to be decrypted.
 * @param string $key The decryption key.
 * @return string The decrypted string, or empty string if decryption fails.
 */
function aplCustomDecrypt(string $string, string $key): string
{
    $decryptedString = '';

    if (!empty($string) && !empty($key)) {
        $string = (string) base64_decode($string); //remove the base64 encoding from string (it's always encoded using base64_encode)
        if (stristr($string, "::")) { //unique separator "::" found, most likely it's valid encrypted string
            $stringIvArray = explode("::", $string, 2); //to decrypt, split the encrypted string from $iv - unique separator used was "::"
            if (count($stringIvArray) == 2) { //proper $stringIvArray should contain exactly two values - $encryptedString and $iv
                list($encryptedString, $iv) = $stringIvArray;

                $decryptedString = (string) openssl_decrypt($encryptedString, "aes-256-cbc", $key, 0, $iv);
            }
        }
    }

    return $decryptedString;
}



/**
 * Validates a number or range and checks if it falls within the specified minimum and maximum values.
 *
 * @param mixed $number The number or range to be validated.
 * @param mixed $minValue The minimum value.
 * @param mixed $maxValue The maximum value. Defaults to INF.
 * @return bool True if the number or range is valid, false otherwise.
 */
function aplValidateNumberOrRange($number, $minValue, $maxValue = INF): bool
{
    $result = false;

    if (filter_var($number, FILTER_VALIDATE_INT) === 0 || !filter_var($number, FILTER_VALIDATE_INT) === false) { //number provided
        if ($number >= $minValue && $number <= $maxValue) {
            $result = true;
        } else {
            $result = false;
        }
    }

    if (stristr($number, "-")) { //range provided
        $numbersArray = explode("-", $number);
        if (filter_var($numbersArray[0], FILTER_VALIDATE_INT) === 0 || !filter_var($numbersArray[0], FILTER_VALIDATE_INT) === false && filter_var($numbersArray[1], FILTER_VALIDATE_INT) === 0 || !filter_var($numbersArray[1], FILTER_VALIDATE_INT) === false) {
            if ($numbersArray[0] >= $minValue && $numbersArray[1] <= $maxValue && $numbersArray[0] <= $numbersArray[1]) {
                $result = true;
            } else {
                $result = false;
            }
        }
    }

    return $result;
}


/**
 * Validates a raw domain.
 *
 * This function checks if the provided URL is a valid raw domain. A valid raw domain should have the format (sub.)domain.com.
 *
 * @param mixed $url The URL to be validated.
 * @return bool True if the URL is a valid raw domain, false otherwise.
 */
function aplValidateRawDomain($url): bool
{
    $result = false;

    if (!empty($url)) {
        if (preg_match('/^[a-z0-9-.]+\.[a-z\.]{2,7}$/', strtolower($url))) {
            $result = true;
        } else {
            $result = false;
        }
    }

    return $result;
}


/**
 * Get the raw domain from a URL.
 *
 * This function extracts the raw domain (e.g., (sub.)domain.com) from a given URL.
 *
 * @param string $url The URL to extract the raw domain from.
 * @return string The raw domain if it exists, '' otherwise.
 */
function aplGetRawDomain(string $url): string
{
    $rawDomain = '';

    if (!empty($url)) {
        $urlArray = parse_url($url);
        if (empty($urlArray['scheme'])) {
            $url = "http://" . $url;
            $urlArray = parse_url($url);
        }

        if (!empty($urlArray['host'])) {
            $rawDomain = $urlArray['host'];
            $rawDomain = trim(str_ireplace("www.", "", (string) filter_var($rawDomain, FILTER_SANITIZE_URL)));
        }
    }

    return $rawDomain;
}

/**
 * Get the current page URL.
 *
 * This function returns the current page URL, optionally removing the last slash and specific strings from the URL.
 *
 * @param int|null $removeLastSlash Whether to remove the last slash from the URL. Defaults to null.
 * @param string[]|null $stringToRemoveArray An array of strings to remove from the URL. Defaults to null.
 * @return string The current page URL if it exists, '' otherwise.
 */
function aplGetCurrentUrl(?int $removeLastSlash = null, ?array $stringToRemoveArray = null): string
{
    $currentUrl = '';

    $protocol = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== "off" ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? null;
    $script = $_SERVER['SCRIPT_NAME'] ?? null;
    $params = $_SERVER['QUERY_STRING'] ?? null;

    if (!empty($host) && !empty($script)) {
        $currentUrl = $protocol . '://' . $host . $script;

        if (!empty($params)) {
            $currentUrl .= '?' . $params;
        }

        if (!empty($stringToRemoveArray) && is_array($stringToRemoveArray)) {
            foreach ($stringToRemoveArray as $value) {
                $currentUrl = (string) str_ireplace($value, "", $currentUrl);
            }
        }

        if ($removeLastSlash === 1) {
            while (substr($currentUrl, -1) === "/") {
                $currentUrl = (string) substr($currentUrl, 0, -1);
            }
        }
    }

    return $currentUrl;
}

/**
 * Return root URL from a long URL.
 *
 * This function returns the root URL from a given long URL. It removes the scheme, www., and the last slash if needed.
 *
 * @param string $url The long URL to extract the root URL from.
 * @param bool $removeScheme Whether to remove the scheme from the URL. Defaults to false.
 * @param bool $removeWww Whether to remove the 'www.' from the URL. Defaults to false.
 * @param bool $removePath Whether to remove everything after the first slash in the URL. Defaults to false.
 * @param bool $removeLastSlash Whether to remove the last slash from the URL. Defaults to false.
 * @return string The root URL if it exists, an empty string otherwise.
 */
function aplGetROOTURL(string $url, bool $removeScheme = false, bool $removeWww = false, bool $removePath = false, bool $removeLastSlash = false): string
{
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        $urlArray = (array) parse_url($url);

        $url = str_ireplace($urlArray['scheme'] ?? '' . "://", "", $url);

        if ($removePath) {
            $firstSlashPosition = stripos($url, "/");
            if ($firstSlashPosition > 0) {
                $url = substr($url, 0, $firstSlashPosition + 1);
            }
        } else {
            $lastSlashPosition = strripos($url, "/");
            if ($lastSlashPosition > 0) {
                $url = substr($url, 0, $lastSlashPosition + 1);
            }
        }

        if (!$removeScheme) {
            $url = $urlArray['scheme'] ?? '' . "://" . $url;
        }

        if ($removeWww) {
            $url = str_ireplace("www.", "", $url);
        }

        if ($removeLastSlash) {
            while (substr($url, -1) === "/") {
                $url = substr($url, 0, -1);
            }
        }
    }

    return trim($url);
}


/**
 * Make POST requests with cookies, referrers, etc.
 *
 * This function makes a POST request to the specified URL with optional cookies, referrers, and post data.
 *
 * @param string $url The URL to make the POST request to.
 * @param string|null $refer The referrer URL. Defaults to null.
 * @param mixed|null $postInfo The post data. Defaults to null.
 * @return mixed The result of the POST request.
 */
function aplCustomPost(string $url, ?string $refer = null, $postInfo = null)
{
    $userAgent = "Mozilla/5.0 (Windows NT 6.3; WOW64; rv:48.0) Gecko/20100101 Firefox/48.0"; // Set user agent
    $connectTimeout = 10; // Set connection timeout

    if (empty($refer) || !filter_var($refer, FILTER_VALIDATE_URL)) {
        $refer = $url;
    } // Use original URL as refer when no valid URL provided

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $connectTimeout);
    curl_setopt($ch, CURLOPT_TIMEOUT, $connectTimeout);
    curl_setopt($ch, CURLOPT_REFERER, $refer);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postInfo);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
    $result = curl_exec($ch);
    // $info = curl_getinfo($ch);
    curl_close($ch);
    return $result;
}


/**
 * Verify date according to provided format (such as Y-m-d).
 *
 * @param string $date The date to verify.
 * @param string $dateFormat The format of the date.
 * @return bool Returns true if the date is valid, false otherwise.
 */
function aplVerifyDate(string $date, string $dateFormat): bool
{
    $datetime = DateTime::createFromFormat($dateFormat, $date);
    $errors = DateTime::getLastErrors();
    if (!$datetime || !empty($errors['warning_count'])) {
        // Date was invalid
        $dateCheckOk = false;
    } else {
        // Everything is OK
        $dateCheckOk = true;
    }

    return $dateCheckOk;
}


/**
 * Calculate the number of days between two dates.
 *
 * This function calculates the number of days between the given start date and end date.
 *
 * @param string $dateFrom The start date in the format "Y-m-d".
 * @param string $dateTo The end date in the format "Y-m-d".
 * @return int The number of days between the start and end date.
 */
function aplGetDaysBetweenDates(string $dateFrom, string $dateTo): int
{
    $numberOfDays = 0;

    if (aplVerifyDate($dateFrom, "Y-m-d") && aplVerifyDate($dateTo, "Y-m-d")) {
        $dateTo = new DateTime($dateTo);
        $dateFrom = new DateTime($dateFrom);
        $numberOfDays = (int) $dateFrom->diff($dateTo)->format("%a");
    }

    return $numberOfDays;
}


/**
 * Parse values between specified xml-like tags.
 *
 * This function parses the content and extracts the values between the specified xml-like tags.
 *
 * @param string $content The content to parse.
 * @param string $tagName The name of the xml-like tag.
 * @return mixed The parsed value or null if no value is found.
 */
function aplParseXmlTags(string $content, string $tagName)
{
    $parsedValue = null;

    if (!empty($content) && !empty($tagName)) {
        preg_match_all("/<" . preg_quote($tagName, "/") . ">(.*?)<\/" . preg_quote($tagName, "/") . ">/ims", $content, $outputArray, PREG_SET_ORDER);

        if (!empty($outputArray[0][1])) {
            $parsedValue = trim($outputArray[0][1]);
        }
    }

    return $parsedValue;
}

/**
 * Parse license notifications tags generated by Auto PHP Licenser server.
 *
 * This function parses the license notifications tags generated by the Auto PHP Licenser server.
 *
 * @param mixed $content The content to parse.
 * @return array<string, mixed> The parsed notifications array.
 */
function aplParseServerNotifications($content): array
{
    $notificationsArray = [];
    $notificationsArray['notification_case'] = null;
    $notificationsArray['notification_text'] = null;

    if (!empty($content)) {
        preg_match_all("/<notification_([a-z_]+)>(.*?)<\/notification_([a-z_]+)>/", $content, $outputArray, PREG_SET_ORDER);

        if (!empty($outputArray[0][1]) && $outputArray[0][1] == $outputArray[0][3] && !empty($outputArray[0][2])) {
            $notificationsArray['notification_case'] = "notification_" . $outputArray[0][1];
            $notificationsArray['notification_text'] = $outputArray[0][2];
        }
    }

    return $notificationsArray;
}

/**
 * Generate signature to be submitted to Auto PHP Licenser server.
 *
 * This function generates a signature based on the provided parameters to be submitted to the Auto PHP Licenser server.
 *
 * @param string $ROOT_URL The root URL.
 * @param string $CLIENT_EMAIL The client email.
 * @param string $LICENSE_CODE The license code.
 * @return string The generated script signature.
 */
function aplGenerateScriptSignature(string $ROOT_URL, string $CLIENT_EMAIL, string $LICENSE_CODE): string
{
    $scriptSignature = '';
    $rootIpsArray = gethostbynamel((string) aplGetRawDomain(APL_ROOT_URL));

    if (!empty($rootIpsArray)) {
        $scriptSignature = hash("sha256", gmdate("Y-m-d") . $ROOT_URL . $CLIENT_EMAIL . $LICENSE_CODE . APL_PRODUCT_ID . implode("", $rootIpsArray));
    }

    return $scriptSignature;
}


/**
 * Verify signature received from Auto PHP Licenser server.
 *
 * This function verifies the signature received from the Auto PHP Licenser server based on the provided parameters.
 *
 * @param mixed $content The content received from the server.
 * @param string $ROOT_URL The root URL.
 * @param string $CLIENT_EMAIL The client email.
 * @param string $LICENSE_CODE The license code.
 * @return bool Returns true if the signature is valid, false otherwise.
 */
function aplVerifyServerSignature($content, string $ROOT_URL, string $CLIENT_EMAIL, string $LICENSE_CODE): bool
{
    $result = false;
    $rootIpsArray = gethostbynamel(aplGetRawDomain(APL_ROOT_URL));
    $serverSignature = aplParseXmlTags($content, "license_signature");

    if (!empty($rootIpsArray) && !empty($serverSignature) && hash("sha256", implode("", $rootIpsArray) . APL_PRODUCT_ID . $LICENSE_CODE . $CLIENT_EMAIL . $ROOT_URL . gmdate("Y-m-d")) == $serverSignature) {
        $result = true;
    }

    return $result;
}


/**
 * Check Auto PHP Licenser core configuration and return an array with error messages if something is wrong.
 *
 * This function checks the Auto PHP Licenser core configuration and returns an array with error messages if any of the settings are invalid.
 *
 * @return array<mixed> Returns an array with the error messages.
 */
function aplCheckSettings(): array
{
    $notificationsArray = [];

    if (empty(APL_SALT) || APL_SALT === "some_random_text") { // @phpstan-ignore-line
        $notificationsArray[] = APL_CORE_NOTIFICATION_INVALID_SALT; // Invalid encryption salt
    }

    if (!filter_var(APL_ROOT_URL, FILTER_VALIDATE_URL) || !ctype_alnum(substr(APL_ROOT_URL, -1))) {
        $notificationsArray[] = APL_CORE_NOTIFICATION_INVALID_ROOT_URL; // Invalid APL installation URL
    }

    if (!filter_var(APL_PRODUCT_ID, FILTER_VALIDATE_INT)) { // @phpstan-ignore-line
        $notificationsArray[] = APL_CORE_NOTIFICATION_INVALID_PRODUCT_ID; // Invalid APL product ID
    }

    if (!aplValidateNumberOrRange(APL_DAYS, 1, 365)) {
        $notificationsArray[] = APL_CORE_NOTIFICATION_INVALID_VERIFICATION_PERIOD; // Invalid verification period
    }

    if (APL_STORAGE !== "DATABASE" && APL_STORAGE !== "FILE") { // @phpstan-ignore-line
        $notificationsArray[] = APL_CORE_NOTIFICATION_INVALID_STORAGE; // Invalid data storage
    }

    if (APL_STORAGE === "DATABASE" && !ctype_alnum(str_ireplace("_", "", APL_DATABASE_TABLE))) { // @phpstan-ignore-line
        $notificationsArray[] = APL_CORE_NOTIFICATION_INVALID_TABLE; // Invalid license table name
    }

    if (APL_STORAGE === "FILE" && !is_writable(APL_DIRECTORY . "/" . APL_LICENSE_FILE_LOCATION)) {
        $notificationsArray[] = APL_CORE_NOTIFICATION_INVALID_LICENSE_FILE; // Invalid license file
    }

    if (APL_STORAGE === "DATABASE" && !is_readable(APL_DIRECTORY . "/" . APL_MYSQL_FILE_LOCATION)) { // @phpstan-ignore-line
        $notificationsArray[] = APL_CORE_NOTIFICATION_INVALID_MYSQL_FILE; // Invalid MySQL file
    }

    if (!empty(APL_ROOT_IP) && !filter_var(APL_ROOT_IP, FILTER_VALIDATE_IP)) { // @phpstan-ignore-line
        $notificationsArray[] = APL_CORE_NOTIFICATION_INVALID_ROOT_IP; // Invalid APL server IP
    }

    if (!empty(APL_ROOT_IP) && !in_array(APL_ROOT_IP, gethostbynamel(aplGetRawDomain(APL_ROOT_URL)))) { // @phpstan-ignore-line
        $notificationsArray[] = APL_CORE_NOTIFICATION_INVALID_DNS; // Actual IP address of APL server domain doesn't match specified IP address
    }

    if (defined("APL_ROOT_NAMESERVERS") && !empty(APL_ROOT_NAMESERVERS)) {
        foreach (APL_ROOT_NAMESERVERS as $nameserver) {
            if (!aplValidateRawDomain($nameserver)) {
                $notificationsArray[] = APL_CORE_NOTIFICATION_INVALID_ROOT_NAMESERVERS; // Invalid APL server nameservers
                break;
            }
        }
    }

    if (defined("APL_ROOT_NAMESERVERS") && !empty(APL_ROOT_NAMESERVERS)) {
        $aplRootNameserversArray = APL_ROOT_NAMESERVERS; // Create a variable from the constant in order to use sort and other array functions
        $fetchedNameserversArray = [];

        $dnsRecordsArray = (array) dns_get_record(aplGetRawDomain(APL_ROOT_URL), DNS_NS);
        foreach ($dnsRecordsArray as $record) {
            if (isset($record['target']) && !empty($record['target'])) {
                $fetchedNameserversArray[] = $record['target'];
            }
        }

        $aplRootNameserversArray = array_map("strtolower", $aplRootNameserversArray); // Convert root nameservers to lowercase
        $fetchedNameserversArray = array_map("strtolower", $fetchedNameserversArray); // Convert fetched nameservers to lowercase

        sort($aplRootNameserversArray); // Sort both arrays before comparison
        sort($fetchedNameserversArray);
        if ($aplRootNameserversArray !== $fetchedNameserversArray) {
            $notificationsArray[] = APL_CORE_NOTIFICATION_INVALID_DNS; // Actual nameservers of APL server domain don't match specified nameservers
        }
    }

    return $notificationsArray;
}


/**
 * Parse the config file and create an array with settings.
 *
 * @return array<mixed> Returns an array with the parsed file settings.
 */
function aplGetFileSettings(): array
{
    $fileSettingsArray = [];

    $fileContent = file_get_contents(APL_DIRECTORY . "/" . APL_LICENSE_FILE_LOCATION);
    if ($fileContent !== false) {
        preg_match_all("/<([A-Z_]+)>(.*?)<\/([A-Z_]+)>/", $fileContent, $matches, PREG_SET_ORDER);
        if (!empty($matches)) {
            foreach ($matches as $value) {
                if (!empty($value[1]) && $value[1] === $value[3]) {
                    $fileSettingsArray[$value[1]] = $value[2];
                }
            }
        }
    }

    return $fileSettingsArray;
}

/**
 * Check if the connection is OK.
 *
 * @param mixed $aplROOTURL The root URL of the APL server.
 *
 * @return array<mixed> Returns an array of notifications.
 */
function aplCheckConnection($aplROOTURL): array
{
    $notificationsArray = [];

    if (aplCustomPost($aplROOTURL . "/apl_callbacks/connection_test.php", $aplROOTURL, "connection_hash=" . rawurlencode(hash("sha256", "connection_test"))) != "<connection_test>OK</connection_test>") {
        $notificationsArray[] = APL_CORE_NOTIFICATION_INACCESSIBLE_ROOT_URL;
    }

    return $notificationsArray;
}

/**
 * Get the license data.
 *
 * Retrieves the license data from either the database or a file, based on the APL_STORAGE constant.
 *
 * @param mixed $MYSQLI_LINK The MySQLi link identifier.
 * @return array<mixed> Returns an array with the license data.
 */
function aplGetLicenseData($MYSQLI_LINK = null): array
{
    $settingsRow = [];

    if (APL_STORAGE === "DATABASE") { // // @phpstan-ignore-line
        // License stored in database (use @ before mysqli_ function to prevent errors when function is executed by aplInstallLicense function)
        $settingsResults = mysqli_query($MYSQLI_LINK, "SELECT * FROM " . APL_DATABASE_TABLE);
        if ($settingsResults instanceof mysqli_result) {
            $settingsRow = mysqli_fetch_assoc($settingsResults);
        }
    }

    if (APL_STORAGE === "FILE") {
        // License stored in file
        $settingsRow = aplParseLicenseFile();
    }

    return $settingsRow;
}

/**
 * Parse the license file and create an array with license data.
 *
 * @return array<mixed> Returns an array with the license data.
 */
function aplParseLicenseFile(): array
{
    $licenseDataArray = [];

    if (is_readable(APL_DIRECTORY . "/" . APL_LICENSE_FILE_LOCATION) && $fileContent = file_get_contents(APL_DIRECTORY . "/" . APL_LICENSE_FILE_LOCATION)) {
        preg_match_all("/<([A-Z_]+)>(.*?)<\/([A-Z_]+)>/", $fileContent, $matches, PREG_SET_ORDER);
        if (!empty($matches)) {
            foreach ($matches as $value) {
                if (!empty($value[1]) && $value[1] === $value[3]) {
                    $licenseDataArray[$value[1]] = $value[2];
                }
            }
        }
    }

    return $licenseDataArray;
}

/**
 * Check Auto PHP Licenser variables and return false if something is wrong.
 *
 * @param mixed $MYSQLI_LINK The MySQLi link identifier.
 * @return array<mixed> Returns an array with the data check result and error detected.
 */
function aplCheckData($MYSQLI_LINK = null): array
{
    $errorDetected = 0;
    $crackingDetected = 0;
    $dataCheckResult = false;

    $ld = aplGetLicenseData($MYSQLI_LINK);
    extract($ld); //get license data
    if (!empty($ROOT_URL) && !empty($INSTALLATION_HASH) && !empty($INSTALLATION_KEY) && !empty($LCD) && !empty($LRD)) { //do further check only if essential variables are valid
        $LCD = aplCustomDecrypt($LCD, APL_SALT . $INSTALLATION_KEY); //decrypt $LCD value for easier data check
        $LRD = aplCustomDecrypt($LRD, APL_SALT . $INSTALLATION_KEY); //decrypt $LRD value for easier data check

        if (!filter_var($ROOT_URL, FILTER_VALIDATE_URL) || !ctype_alnum(substr($ROOT_URL, -1))) { //invalid script url
            $errorDetected = 1;
        }

        if (filter_var(aplGetCurrentUrl(), FILTER_VALIDATE_URL) && stristr((string) preg_replace('/\\.[^.\\s]{2,4}$/', '', aplGetROOTURL(aplGetCurrentUrl(), true, true, true, true)), preg_replace('/\\.[^.\\s]{2,4}$/', '', aplGetROOTURL("$ROOT_URL/", true, true, true, true))) === false) { //script is opened via browser (current_url set), but current_url is different from value in database
            $errorDetected = 2;
        }

        if (!isset($CLIENT_EMAIL) || !isset($LICENSE_CODE) || $INSTALLATION_HASH != hash("sha256", $ROOT_URL . $CLIENT_EMAIL . $LICENSE_CODE)) { //invalid installation hash (value - $ROOT_URL, $CLIENT_EMAIL AND $LICENSE_CODE encrypted with sha256)
            $errorDetected = 3;
        }

        if (!password_verify($LRD, aplCustomDecrypt($INSTALLATION_KEY, APL_SALT . $ROOT_URL))) { //invalid installation key (value - current date ("Y-m-d") encrypted with password_hash and then encrypted with custom function (salt - $ROOT_URL). Put simply, it's LRD value, only encrypted different way)
            $errorDetected = 4;
        }

        if (!aplVerifyDate($LCD, "Y-m-d")) { //last check date is invalid
            $errorDetected = 5;
        }

        if (!aplVerifyDate($LRD, "Y-m-d")) { //last run date is invalid
            $errorDetected = 6;
        }

        //check for possible cracking attempts - starts
        if (aplVerifyDate($LCD, "Y-m-d") && $LCD > date("Y-m-d", strtotime("+1 day"))) { //last check date is VALID, but higher than current date (someone manually decrypted and overwrote it or changed system time back). Allow 1 day difference in case user changed his timezone and current date went 1 day back
            $errorDetected = 7;
            $crackingDetected = 1;
        }

        if (aplVerifyDate($LRD, "Y-m-d") && $LRD > date("Y-m-d", strtotime("+1 day"))) { //last run date is VALID, but higher than current date (someone manually decrypted and overwrote it or changed system time back). Allow 1 day difference in case user changed his timezone and current date went 1 day back
            $errorDetected = 8;
            $crackingDetected = 1;
        }

        if (aplVerifyDate($LCD, "Y-m-d") && aplVerifyDate($LRD, "Y-m-d") && $LCD > $LRD) { //last check date and last run date is VALID, but LCD is higher than LRD (someone manually decrypted and overwrote it or changed system time back)
            $errorDetected = 9;
            $crackingDetected = 1;
        }

        if ($crackingDetected == 1 && APL_DELETE_CRACKED == "YES") { // @phpstan-ignore-line
            //delete user data
            aplDeleteData($MYSQLI_LINK);
        }
        //check for possible cracking attempts - ends

        if ($errorDetected === 0 && $crackingDetected != 1) { //everything OK
            $dataCheckResult = true;
        }
    }

    return [
        'data_check_result' => $dataCheckResult,
        'error_detected' => $errorDetected
    ];
}


/**
 * Verify Envato purchase.
 *
 * @param mixed $LICENSE_CODE The license code.
 * @return array<mixed> Returns an array of notifications.
 */
function aplVerifyEnvatoPurchase($LICENSE_CODE): array
{
    $notificationsArray = [];

    $result = aplCustomPost(
        APL_ROOT_URL . "/apl_callbacks/verify_envato_purchase.php",
        APL_ROOT_URL,
        "product_id=" . rawurlencode((string) APL_PRODUCT_ID) . "&license_code=" . rawurlencode($LICENSE_CODE) . "&connection_hash=" . rawurlencode(hash("sha256", "verify_envato_purchase"))
    );

    if ($result != "<head/><verify_envato_purchase>OK</verify_envato_purchase>" && $result != "<verify_envato_purchase>OK</verify_envato_purchase>") {
        $notificationsArray[] = APL_CORE_NOTIFICATION_INACCESSIBLE_ROOT_URL;
    }

    return $notificationsArray;
}

/**
 * Verify token.
 *
 * @param mixed $MYSQLI_LINK The MySQLi link.
 * @param int $FORCEVerification The force verification flag.
 * @return array<mixed> Returns an array of notifications.
 */
function verifyToken($MYSQLI_LINK = null, int $FORCEVerification = 0): array
{
    $notificationsArray = array();
    $updateLrdValue = 0;
    $updateLcdValue = 0;
    $aplCoreNotifications = aplCheckSettings(); //check core settings

    if (empty($aplCoreNotifications)) { //only continue if script is properly configured
        $aplCheckDataResult = aplCheckData($MYSQLI_LINK);
        if ($aplCheckDataResult['data_check_result'] === true) { //only continue if license is installed and properly configured
            $settingsRow = aplGetFileSettings();
            extract($settingsRow);

            $INSTALLATION_KEY = isset($INSTALLATION_KEY) ? $INSTALLATION_KEY : '';
            $LCD = isset($LCD) ? $LCD : '';
            $LRD = isset($LRD) ? $LRD : '';
            $ROOT_URL = isset($ROOT_URL) ? $ROOT_URL : '';
            $CLIENT_EMAIL = isset($CLIENT_EMAIL) ? $CLIENT_EMAIL : '';
            $LICENSE_CODE = isset($LICENSE_CODE) ? $LICENSE_CODE : '';
            $INSTALLATION_HASH = isset($INSTALLATION_HASH) ? $INSTALLATION_HASH : '';

            if (aplGetDaysBetweenDates(aplCustomDecrypt($LCD, APL_SALT . $INSTALLATION_KEY), date("Y-m-d")) < APL_DAYS && $FORCEVerification == 0) {
                //the only case when no verification is needed, return notification_license_ok case, so script can continue working
                $notificationsArray['notification_case'] = "notification_license_ok";
                $notificationsArray['notification_text'] = APL_NOTIFICATION_BYPASS_VERIFICATION;
            } else {
                //time to verify license (or use forced verification)
                $postInfo = "product_id=" . rawurlencode((string) APL_PRODUCT_ID) . "&client_email=" . rawurlencode($CLIENT_EMAIL) . "&license_code=" . rawurlencode($LICENSE_CODE) . "&root_url=" . rawurlencode($ROOT_URL) . "&installation_hash=" . rawurlencode($INSTALLATION_HASH) . "&license_signature=" . rawurlencode(aplGenerateScriptSignature($ROOT_URL, $CLIENT_EMAIL, $LICENSE_CODE));

                $content = aplCustomPost(APL_ROOT_URL . "/apl_callbacks/license_verify.php", $ROOT_URL, $postInfo);
                if (!empty($content)) { //content received, do other checks if needed
                    $notificationsArray = aplParseServerNotifications($content); //parse <notification_case> along with message

                    if ($notificationsArray['notification_case'] == "notification_license_ok" && aplVerifyServerSignature($content, $ROOT_URL, $CLIENT_EMAIL, $LICENSE_CODE)) {
                        //everything OK
                        $updateLcdValue = 1;
                    }

                    if ($notificationsArray['notification_case'] == "notification_license_cancelled" && APL_DELETE_CANCELLED == "YES") { // @phpstan-ignore-line
                        //license cancelled, data deletion activated, so delete user data
                        aplDeleteData($MYSQLI_LINK);
                    }
                } else {
                    //no content received
                    $notificationsArray['notification_case'] = "notification_no_connection";
                    $notificationsArray['notification_text'] = APL_NOTIFICATION_NO_CONNECTION;
                }
            }

            if (aplCustomDecrypt($LRD, APL_SALT . $INSTALLATION_KEY) < date("Y-m-d")) {
                //used to make sure database gets updated only once a day, not every time script is executed. do it BEFORE new $INSTALLATION_KEY is generated
                $updateLrdValue = 1;
            }

            if ($updateLrdValue == 1 || $updateLcdValue == 1) {
                //update database only if $LRD or $LCD were changed
                if ($updateLcdValue == 1) {
                    //generate new $LCD value ONLY if verification succeeded. Otherwise, old $LCD value should be used, so license will be verified again next time script is executed
                    $LCD = date("Y-m-d");
                } else {
                    //get existing DECRYPTED $LCD value because it will need to be re-encrypted using new $INSTALLATION_KEY in case license verification didn't succeed
                    $LCD = aplCustomDecrypt($LCD, APL_SALT . $INSTALLATION_KEY);
                }

                $INSTALLATION_KEY = aplCustomEncrypt(password_hash(date("Y-m-d"), PASSWORD_DEFAULT), APL_SALT . $ROOT_URL); //generate $INSTALLATION_KEY first because it will be used as salt to encrypt LCD and LRD!!!
                $LCD = aplCustomEncrypt($LCD, APL_SALT . $INSTALLATION_KEY); //finally encrypt $LCD value (it will contain either DECRYPTED old date, either non-encrypted today's date)
                $LRD = aplCustomEncrypt(date("Y-m-d"), APL_SALT . $INSTALLATION_KEY); //generate new $LRD value every time database needs to be updated (because if LCD is higher than LRD, cracking attempt will be detected).

                if ($handle = fopen(APL_DIRECTORY . "/" . APL_LICENSE_FILE_LOCATION, "w+")) {
                    $fwrite = fwrite($handle, "<ROOT_URL>$ROOT_URL</ROOT_URL><CLIENT_EMAIL>$CLIENT_EMAIL</CLIENT_EMAIL><LICENSE_CODE>$LICENSE_CODE</LICENSE_CODE><LCD>$LCD</LCD><LRD>$LRD</LRD><INSTALLATION_KEY>$INSTALLATION_KEY</INSTALLATION_KEY><INSTALLATION_HASH>$INSTALLATION_HASH</INSTALLATION_HASH>");
                    if ($fwrite === false) {
                        //updating file failed
                        echo APL_NOTIFICATION_LICENSE_FILE_WRITE_ERROR;
                        exit();
                    }
                    fclose($handle);
                }
            }
        } else {
            //license is not installed yet or corrupted
            $notificationsArray['notification_case'] = "notification_license_corrupted";
            $notificationsArray['notification_text'] = APL_NOTIFICATION_LICENSE_CORRUPTED . ' (' . $aplCheckDataResult['error_detected'] . ')';
        }
    } else {
        //script is not properly configured
        $notificationsArray['notification_case'] = "notification_script_corrupted";
        $notificationsArray['notification_text'] = implode("; ", $aplCoreNotifications);
    }

    return $notificationsArray;
}

/**
 * Verify updates.
 *
 * This function verifies updates by sending a request to the server with the client's information and license details.
 * It returns an array of notifications received from the server.
 *
 * @param mixed $MYSQLI_LINK The MySQLi link identifier. (PHPSTAN level 6 standards)
 * @return array<mixed> An array of notifications received from the server.
 */
function aplVerifyUpdates($MYSQLI_LINK): array
{
    $notificationsArray = [];
    $aplCoreNotifications = aplCheckSettings(); //check core settings

    if (empty($aplCoreNotifications)) { //only continue if script is properly configured
        if (aplCheckData($MYSQLI_LINK)) { //only continue if license is installed and properly configured
            if (APL_STORAGE == "DATABASE") { // @phpstan-ignore-line
                //settings stored in database
                $settingsResults = mysqli_query($MYSQLI_LINK, "SELECT * FROM " . APL_DATABASE_TABLE);
                if ($settingsResults instanceof mysqli_result) {
                    while ($settingsRow = mysqli_fetch_assoc($settingsResults)) {
                        extract($settingsRow);
                    }
                }
            }

            if (APL_STORAGE == "FILE") { // @phpstan-ignore-line
                //settings stored in file
                $settingsRow = aplGetFileSettings();
                extract($settingsRow);
            }

            $CLIENT_EMAIL = isset($CLIENT_EMAIL) ? $CLIENT_EMAIL : '';
            $LICENSE_CODE = isset($LICENSE_CODE) ? $LICENSE_CODE : '';
            $ROOT_URL = isset($ROOT_URL) ? $ROOT_URL : '';
            $INSTALLATION_HASH = isset($INSTALLATION_HASH) ? $INSTALLATION_HASH : '';

            $postInfo = "product_id=" . rawurlencode((string) APL_PRODUCT_ID) . "&client_email=" . rawurlencode($CLIENT_EMAIL) . "&license_code=" . rawurlencode($LICENSE_CODE) . "&root_url=" . rawurlencode($ROOT_URL) . "&installation_hash=" . rawurlencode($INSTALLATION_HASH) . "&license_signature=" . rawurlencode(aplGenerateScriptSignature($ROOT_URL, $CLIENT_EMAIL, $LICENSE_CODE));

            $content = aplCustomPost(APL_ROOT_URL . "/apl_callbacks/license_updates.php", $ROOT_URL, $postInfo);
            if (!empty($content)) { //content received, do other checks if needed
                $notificationsArray = aplParseServerNotifications($content); //parse <notification_case> along with message
            } else {
                //no content received
                $notificationsArray['notification_case'] = "notification_no_connection";
                $notificationsArray['notification_text'] = APL_NOTIFICATION_NO_CONNECTION;
            }
        } else {
            //license is not installed yet or corrupted
            $notificationsArray['notification_case'] = "notification_license_corrupted";
            $notificationsArray['notification_text'] = APL_NOTIFICATION_LICENSE_CORRUPTED;
        }
    } else {
        //script is not properly configured
        $notificationsArray['notification_case'] = "notification_script_corrupted";
        $notificationsArray['notification_text'] = implode("; ", $aplCoreNotifications);
    }

    return $notificationsArray;
}

/**
 * Delete user data.
 *
 * This function deletes user data by removing the directory and its contents recursively.
 * If the storage is set to "DATABASE", it also deletes all data from the MySQL tables.
 *
 * @param mixed $MYSQLI_LINK The MySQLi link identifier.
 * @return void
 */
function aplDeleteData($MYSQLI_LINK): void
{
    if (is_dir(APL_DIRECTORY)) {
        $rootDirectory = dirname(APL_DIRECTORY); // APL_DIRECTORY actually is USER_INSTALLATION_FULL_PATH/SCRIPT (where this file is located), go one level up to enter root directory of protected script

        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($rootDirectory, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $path) {
            $path->isDir() && !$path->isLink() ? rmdir($path->getPathname()) : unlink($path->getPathname());
        }
        rmdir($rootDirectory);
    }

    if (APL_STORAGE == "DATABASE") { // @phpstan-ignore-line
        // Settings stored in database, delete MySQL data
        $databaseTablesArray = [];

        $tableListResults = mysqli_query($MYSQLI_LINK, "SHOW TABLES"); // Get list of tables in database
        if ($tableListResults instanceof mysqli_result) {
            while ($tableListRow = mysqli_fetch_row($tableListResults)) {
                $databaseTablesArray[] = $tableListRow[0];
            }
        }

        if (!empty($databaseTablesArray)) {
            foreach ($databaseTablesArray as $tableName) {
                // Delete all data from tables first
                mysqli_query($MYSQLI_LINK, "DELETE FROM $tableName");
            }

            foreach ($databaseTablesArray as $tableName) {
                // Now drop tables (do it later to prevent script from being aborted when no drop privileges are granted)
                mysqli_query($MYSQLI_LINK, "DROP TABLE $tableName");
            }
        }
    }

    exit(); // Abort further execution
}
