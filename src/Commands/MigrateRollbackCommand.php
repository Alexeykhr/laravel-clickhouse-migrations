<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Alexeykhr\ClickhouseMigrations\Migrations\Migrator;
use Alexeykhr\ClickhouseMigrations\Concerns\MigrationPath;
use Alexeykhr\ClickhouseMigrations\Concerns\MigrationStep;
use Alexeykhr\ClickhouseMigrations\Concerns\MigrationOutput;

class MigrateRollbackCommand extends Command
{
    use ConfirmableTrait, MigrationPath, MigrationStep, MigrationOutput;

    /**
     * @inheritDoc
     */
    protected $signature = 'clickhouse-migrate:rollback
                {--force : Force the operation to run when in production}
                {--output : Show migrations to apply before executing}
                {--path= : Path to Clickhouse directory with migrations}
                {--realpath : Indicate any provided migration file paths are pre-resolved absolute paths}
                {--step=1 : Number of migrations to rollback}';

    /**
     * @inheritDoc
     */
    protected $description = 'Rollback the ClickHouse database migrations';

    /**
     * Execute the console command
     *
     * @param  Migrator  $migrator
     * @return void
     */
    public function handle(Migrator $migrator): void
    {
        $migrator->ensureTableExists()
            ->setOutput($this->getOutput())
            ->setMigrationPath($this->getMigrationPath());

        $migrations = $migrator->getMigrationsDown();

        if (! $this->outputMigrations($migrations) || ! $this->confirmToProceed()) {
            return;
        }

        $migrator->runDown($this->getStep());
    }
}
