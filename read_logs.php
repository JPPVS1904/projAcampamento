<?php 
require 'vendor/autoload.php';
$log = file('storage/logs/laravel.log'); 
$lines = array_slice($log, -100); 
foreach($lines as $l) { 
    if(strpos($l, 'local.ERROR') !== false) {
        echo $l . PHP_EOL; 
    }
}
