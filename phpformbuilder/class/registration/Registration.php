<?php

namespace registration;

class Registration
{
    /**
     * Checks the server status.
     *
     * @return array<int, string>|bool An array of errors if the server is not properly configured, or true if everything is OK.
     */
    public static function checkServer()
    {
        $errors = [];

        $php_extensions = get_loaded_extensions();

        if (!in_array('curl', $php_extensions)) {
            $errors[] = '<strong>PHP CURL extension is NOT enabled.<br>You\'ve got to enable it to register your PHP Form Builder copy.</strong>';
        }

        if (!empty($errors)) {
            return $errors;
        }

        return true;
    }

    /**
     * Returns the current URL.
     * (also remove specific strings and last slash if needed)
     *
     * @param bool|null $remove_last_slash Whether to remove the last slash from the URL.
     * @param string[]|null $string_to_remove_array An array of strings to remove from the URL.
     * @return string The current URL.
     */
    public static function getCurrentUrl(?bool $remove_last_slash = null, ?array $string_to_remove_array = null): string
    {
        $current_url = null;

        if ((!empty($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] == 'https') || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443')) {
            $protocol = 'https';
        } else {
            $protocol = 'http';
        }

        $host = $_SERVER['HTTP_HOST'] ?? null;
        $script = $_SERVER['SCRIPT_NAME'] ?? null;
        $params = $_SERVER['QUERY_STRING'] ?? null;

        if (!empty($host) && !empty($script)) { //return URL only when script is executed via browser (because no URL should exist when executed from command line)
            $current_url = $protocol . '://' . $host . $script;

            if (!empty($params)) {
                $current_url .= '?' . $params;
            }

            if (!empty($string_to_remove_array) && is_array($string_to_remove_array)) { //remove specific strings from URL
                foreach ($string_to_remove_array as $value) {
                    $current_url = str_ireplace($value, "", $current_url);
                }
            }

            if ($remove_last_slash === true) { //remove / from the end of URL if it exists
                while (substr($current_url, -1) == "/") { //use cycle in case URL already contained multiple // at the end
                    $current_url = (string) substr($current_url, 0, -1);
                }
            }
        }

        return (string) $current_url;
    }
}
