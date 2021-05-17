<?php

require_once(__DIR__ . '\..\vendor\autoload.php');

use App\Services\GoogleDriveService;
use App\Services\MysqlDumperService;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '\..\\');
$dotenv->load();

$fileName = MysqlDumperService::dumpDatabase();
$fileContent = file_get_contents('dumps/' . $fileName);

$drive = new GoogleDriveService;

$uploadedFile = $drive->uploadFile($fileName, $fileContent);

echo 'File uploaded succesfully.' . PHP_EOL;
echo 'Uploaded to: database_backups' . PHP_EOL; 
echo 'Filename: ' . $uploadedFile->getName() . PHP_EOL;