<?php

namespace App\Services;

use Spatie\DbDumper\Databases\MySql;
use Spatie\DbDumper\Exceptions\CannotStartDump;
use Spatie\DbDumper\Exceptions\DumpFailed;

class MysqlDumperService
{
    public static function dumpDatabase(): string
    {
        self::handleDirectory();
        $filename = 'dumps/' . $_ENV['DB_NAME'] . '-' . date('Y-m-d-H-i-s') . '.sql';

        try {
            MySql::create()
                    ->setDbName($_ENV['DB_NAME'])
                    ->setUserName($_ENV['DB_USERNAME'])
                    ->setPassword($_ENV['DB_PASSWORD'])
                    ->dumpToFile($filename);
        return str_replace('dumps/', '', $filename);
        } catch (CannotStartDump $exception) {
            die('Error: ' . $exception->getMessage() . PHP_EOL . 'MySQL server connection failed, please check your .env configuration!');
        } catch (DumpFailed $exception) {
            die('Error: ' . $exception->getMessage() . PHP_EOL . 'Unknown database selected, please check your .env configuration.');
        }   
    }
    private static function handleDirectory(): void
    {
        if(!is_dir('dumps')) mkdir('dumps');
    }
}