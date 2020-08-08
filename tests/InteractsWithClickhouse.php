<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Tests;

use ClickHouseDB\Client;
use Alexeykhr\ClickhouseMigrations\Clickhouse;
use Alexeykhr\ClickhouseMigrations\Migrations\MigrationRepository;

trait InteractsWithClickhouse
{
    /**
     * @return void
     */
    protected function refreshClickhouse(): void
    {
        $client = $this->clickhouse();
        $client->database('system');
        $client->write("DROP DATABASE IF EXISTS default");
        $client->write("CREATE DATABASE default");
        $client->database('default');
    }

    /**
     * @return Client
     */
    protected function clickhouse(): Client
    {
        static $clickhouse;

        return $clickhouse ?? $clickhouse = (new Clickhouse([
                'host' => 'localhost',
                'port' => 8123,
                'username' => 'default',
                'password' => '',
                'options' => [
                    'database' => 'default',
                    'timeout' => 1,
                    'connectTimeOut' => 2,
                ],
            ]))->getClient();
    }

    /**
     * @return MigrationRepository
     */
    protected function repository(): MigrationRepository
    {
        return new MigrationRepository('migrations', $this->clickhouse());
    }
}