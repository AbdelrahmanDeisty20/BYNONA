<?php
$certPath = 'C:/xampp/php/extras/ssl/cacert.pem';
echo "Testing with cert: $certPath\n";

$url = 'https://repo.packagist.org/packages.json';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CAINFO, $certPath);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_VERBOSE, true);
$verbose = fopen('php://temp', 'w+');
curl_setopt($ch, CURLOPT_STDERR, $verbose);

$res = curl_exec($ch);

if ($res === false) {
    echo "VERIFICATION FAILED.\n";
    echo 'Curl error: ' . curl_error($ch) . "\n";

    echo "\nTrying WITHOUT verification...\n";
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $res2 = curl_exec($ch);
    if ($res2 !== false) {
        echo 'Success WITHOUT verification! Received ' . strlen($res2) . " bytes.\n";
    } else {
        echo 'Even without verification it FAILED: ' . curl_error($ch) . "\n";
    }
} else {
    echo 'Success WITH verification! Received ' . strlen($res) . " bytes.\n";
}

rewind($verbose);
$verboseLog = stream_get_contents($verbose);
echo "\n--- Verbose Log ---\n$verboseLog\n";

curl_close($ch);
