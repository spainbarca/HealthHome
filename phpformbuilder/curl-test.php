<?php

// phpcs:disable PSR1.Files.SideEffects

/**
 * Enhanced cURL request script with comprehensive diagnostics
 *
 * Test if cURL is available, send request, print response
 * On failure, provides detailed diagnostic information
 */

if (!function_exists('curl_init')) {
    die('cURL not available!');
}

// Configuration
$url = 'https://www.phpformbuilder.pro/registration/curl-test-answer.php';
$postData = array(
    'name' => 'John Doe',
    'submit' => '1'
);

echo "<h2>cURL Test for: " . htmlspecialchars($url) . "</h2>\n";

// Initialize cURL
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_FAILONERROR, true);
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postData));
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_TIMEOUT, 30);
curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);

// Enable verbose logging for diagnostics
curl_setopt($curl, CURLOPT_VERBOSE, true);
$verbose = fopen('php://temp', 'w+');
curl_setopt($curl, CURLOPT_STDERR, $verbose);

// Execute request
$output = curl_exec($curl);

// Get detailed information
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$error = curl_error($curl);
$errno = curl_errno($curl);
$info = curl_getinfo($curl);

// Get verbose output
$verboseLog = '';
if (is_resource($verbose)) {
    rewind($verbose);
    $verboseContent = stream_get_contents($verbose);
    $verboseLog = $verboseContent !== false ? $verboseContent : '';
    fclose($verbose);
}
// Ensure $verboseLog is always a string
$verboseLog = (string) $verboseLog;

curl_close($curl);

// Display results
if ($output === false) {
    echo '<h3 style="color:#B60000">&#10060; CURL Request FAILED</h3>';
    echo '<p><strong>Error:</strong> ' . htmlspecialchars($error) . '</p>';

    // Show comprehensive diagnostics
    showDiagnostics($url, $errno, $error, $httpCode, $info, $verboseLog);
} else {
    echo '<h4 style="color:#28B463">&#9989; CURL Request has been sent successfully.</h4>';
    echo '<p><strong>HTTP Code:</strong> ' . $httpCode . '</p>';

    if ($output !== '') {
        echo '<h4>Response:</h4>';
        echo '<div style="background:#f5f5f5; padding:10px; border:1px solid #ddd;">';
        echo htmlspecialchars((string) $output);
        echo '</div>';
    } else {
        echo '<h4 style="color:#B60000">&#9888; The CURL answer is empty. Your server may have blocked it.</h4>';
    }
}

/**
 * Show comprehensive diagnostics when cURL fails
 *
 * @param string $url The URL that was being accessed
 * @param int $errno The cURL error number
 * @param string $error The cURL error message
 * @param int $httpCode The HTTP response code
 * @param array<string, mixed> $info The cURL info array
 * @param string $verboseLog The verbose connection log
 */
function showDiagnostics(string $url, int $errno, string $error, int $httpCode, array $info, string $verboseLog): void
{
    echo '<div style="background:#fff3cd; padding:15px; border:1px solid #ffeaa7; margin:10px 0;">';
    echo '<h3>&#128269; Diagnostic Information</h3>';

    // Parse URL for individual tests
    $urlParts = parse_url($url);
    $host = (is_array($urlParts) && isset($urlParts['host'])) ? $urlParts['host'] : '';
    $port = (is_array($urlParts) && isset($urlParts['port'])) ? $urlParts['port'] : 443;

    echo '<h4>Basic Error Info:</h4>';
    echo '<ul>';
    echo '<li><strong>Error Code:</strong> ' . $errno . '</li>';
    echo '<li><strong>Error Message:</strong> ' . htmlspecialchars($error) . '</li>';
    echo '<li><strong>HTTP Code:</strong> ' . $httpCode . '</li>';
    echo '</ul>';

    // Error code explanations
    echo '<h4>Error Analysis:</h4>';
    switch ($errno) {
        case 6:
            echo '<p style="color:#d63031">&#127760; <strong>DNS Resolution Failed</strong> - Could not resolve hostname "' . $host . '"</p>';
            break;
        case 7:
            echo '<p style="color:#d63031">&#128683; <strong>Connection Refused</strong> - Server is not accepting connections (likely firewall block)</p>';
            break;
        case 28:
            echo '<p style="color:#d63031">&#8987; <strong>Timeout</strong> - Connection attempt timed out (likely firewall or network issue)</p>';
            break;
        case 35:
            echo '<p style="color:#d63031">&#128274; <strong>SSL/TLS Error</strong> - SSL handshake failed</p>';
            break;
        case 56:
            echo '<p style="color:#d63031">&#128225; <strong>Connection Reset</strong> - Connection was reset by peer (firewall dropping packets)</p>';
            break;
        default:
            echo '<p style="color:#d63031">&#10067; <strong>Other Error</strong> - See error message above</p>';
    }

    echo '<h4>Connection Timing:</h4>';
    echo '<ul>';
    echo '<li><strong>DNS Lookup Time:</strong> ' . number_format($info['namelookup_time'], 3) . 's</li>';
    echo '<li><strong>Connect Time:</strong> ' . number_format($info['connect_time'], 3) . 's</li>';
    echo '<li><strong>Total Time:</strong> ' . number_format($info['total_time'], 3) . 's</li>';
    echo '<li><strong>Target IP:</strong> ' . ($info['primary_ip'] ?? 'N/A') . '</li>';
    echo '<li><strong>Target Port:</strong> ' . ($info['primary_port'] ?? 'N/A') . '</li>';
    echo '</ul>';

    // Network-level tests
    echo '<h4>Network Connectivity Tests:</h4>';

    // DNS Test
    echo '<p><strong>DNS Resolution Test:</strong> ';
    $ip = gethostbyname($host);
    if ($ip !== $host) {
        echo '<span style="color:#00b894">&#9989; SUCCESS</span> - ' . $host . ' resolves to ' . $ip . '</p>';
    } else {
        echo '<span style="color:#d63031">&#10060; FAILED</span> - Cannot resolve ' . $host . '</p>';
    }

    // Socket Test
    echo '<p><strong>Socket Connection Test:</strong> ';
    $socket = @fsockopen($host, $port, $sockErrno, $sockErrstr, 10);
    if ($socket) {
        echo '<span style="color:#00b894">&#9989; SUCCESS</span> - Can connect to ' . $host . ':' . $port . '</p>';
        fclose($socket);
    } else {
        echo '<span style="color:#d63031">&#10060; FAILED</span> - Cannot connect to ' . $host . ':' . $port . ' (Error: ' . $sockErrno . ' - ' . $sockErrstr . ')</p>';
    }

    // Alternative HTTP test
    echo '<p><strong>Alternative HTTP Test:</strong> ';
    $context = stream_context_create([
        'http' => [
            'timeout' => 10,
            'ignore_errors' => true,
            'method' => 'GET'
        ]
    ]);
    $result = @file_get_contents($url, false, $context);
    if ($result !== false) {
        echo '<span style="color:#00b894">&#9989; SUCCESS</span> - file_get_contents() works</p>';
    } else {
        echo '<span style="color:#d63031">&#10060; FAILED</span> - file_get_contents() also fails</p>';
        $lastError = error_get_last();
        if ($lastError) {
            echo '<p style="margin-left:20px; font-size:0.9em;">Last error: ' . htmlspecialchars($lastError['message']) . '</p>';
        }
    }

    // Server Environment
    echo '<h4>Server Environment:</h4>';
    echo '<ul>';
    echo '<li><strong>PHP Version:</strong> ' . PHP_VERSION . '</li>';
    $curlVersion = curl_version();
    echo '<li><strong>cURL Version:</strong> ' . ($curlVersion !== false ? $curlVersion['version'] : 'N/A') . '</li>';
    echo '<li><strong>OpenSSL Version:</strong> ' . ($curlVersion !== false && isset($curlVersion['ssl_version']) ? $curlVersion['ssl_version'] : 'N/A') . '</li>';
    echo '<li><strong>Server IP:</strong> ' . ($_SERVER['SERVER_ADDR'] ?? gethostbyname(php_uname('n'))) . '</li>';
    echo '</ul>';

    // Recommendations - Enhanced visibility for firewall issues
    echo '<div style="background:#ff6b6b; color:white; padding:20px; margin:15px 0; border-radius:8px; box-shadow:0 4px 8px rgba(0,0,0,0.2);">';
    echo '<h3 style="margin:0 0 15px 0; font-size:1.4em;">&#128161; IMPORTANT - ACTION REQUIRED</h3>';

    if ($errno == 7 || $errno == 28) {
        echo '<div style="background:rgba(255,255,255,0.2); padding:15px; border-radius:5px; margin:10px 0;">';
        echo '<h4 style="margin:0 0 10px 0; font-size:1.2em;">&#128683; Most likely a FIREWALL issue</h4>';
        echo '<p style="font-size:1.1em; margin:5px 0;"><strong>CONTACT your hosting provider or IT administrator</strong></p>';
        echo '<p style="font-size:1.1em; margin:5px 0;"><strong>REQUEST to whitelist the domains: www.phpformbuilder.pro and www.miglisoft.com</strong></p>';
        echo '</div>';
    }
    echo '</div>';

    // Additional recommendations
    echo '<h4>&#128161; Additional Troubleshooting Steps:</h4>';
    echo '<ul>';

    if ($errno == 6) {
        echo '<li>Check DNS settings - try using 8.8.8.8 or 1.1.1.1 as DNS server</li>';
        echo '<li>Verify the domain name is correct and accessible</li>';
    } elseif ($errno == 35) {
        echo '<li>SSL/TLS issue - try disabling SSL verification (already done in this script)</li>';
        echo '<li>Check if the target server supports your SSL/TLS version</li>';
    }

    echo '<li>Test the URL manually in a browser from the same server</li>';
    echo '<li>Check server logs for additional error information</li>';
    echo '</ul>';

    // Verbose log (if available and not too long)
    if (!empty($verboseLog) && strlen($verboseLog) < 2000) {
        echo '<h4>Detailed Connection Log:</h4>';
        echo '<pre style="background:#f8f9fa; padding:10px; border:1px solid #dee2e6; font-size:0.8em; overflow-x:auto;">';
        echo htmlspecialchars($verboseLog);
        echo '</pre>';
    }

    echo '</div>';
}
