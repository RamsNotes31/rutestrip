<?php
require 'vendor/autoload.php';
$app    = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

$routes = App\Models\HikingRoute::select('id', 'name')->get();
file_put_contents('routes_list.txt', '');
foreach ($routes as $r) {
    file_put_contents('routes_list.txt', $r->id . ': ' . $r->name . PHP_EOL, FILE_APPEND);
}
echo "Saved to routes_list.txt";
