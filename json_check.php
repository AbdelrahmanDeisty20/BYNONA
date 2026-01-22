<?php
$path = 'resources/lang/ar.json';
$c = file_get_contents($path);
if (substr($c, 0, 3) === "\u{FEFF}") {
    $c = substr($c, 3);
    echo "BOM detected and removed.\n";
} else {
    echo "No BOM detected.\n";
}

$d = json_decode($c, true);
if (json_last_error() === JSON_ERROR_NONE) {
    file_put_contents($path, $c);
    echo "Success: JSON is valid. File updated.\n";
} else {
    echo 'Error: JSON is still invalid. Error: ' . json_last_error_msg() . "\n";
    // Check for trailing commas or other common issues if it's still invalid
    // But usually BOM is the primary culprit if the tail looks fine.
}
