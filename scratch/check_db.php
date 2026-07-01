<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$tables = DB::select('SHOW TABLES');
$tableList = array_map('current', $tables);

echo "Tables in database:\n";
foreach ($tableList as $table) {
    echo "- $table\n";
    if (strpos($table, 'card') !== false || strpos($table, 'layout') !== false) {
        echo "  (Found match!)\n";
    }
}
