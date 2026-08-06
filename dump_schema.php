<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    $tables = DB::select('SHOW TABLES');
    $dbName = DB::getDatabaseName();
    $tableKey = "Tables_in_{$dbName}";

    echo "=== DATABASE SCHEMA OVERVIEW ===\n\n";

    foreach ($tables as $tableObj) {
        $tableName = $tableObj->$tableKey ?? reset($tableObj);
        echo "TABLE: {$tableName}\n";
        echo "----------------------------------------\n";
        
        $columns = DB::select("SHOW COLUMNS FROM `{$tableName}`");
        foreach ($columns as $col) {
            echo "  - {$col->Field} ({$col->Type})\n";
        }
        echo "\n";
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
