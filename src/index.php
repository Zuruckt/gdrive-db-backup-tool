<?php

require_once(__DIR__ . '\..\vendor\autoload.php');

use App\Services\MysqlDumperService;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '\..\\');
$dotenv->load();

$temp_file = MysqlDumperService::dumpDatabase();