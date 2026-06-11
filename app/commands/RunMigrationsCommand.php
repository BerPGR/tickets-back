<?php

require_once __DIR__ . "/../MigrationRunner.php";

use flight\commands\AbstractBaseCommand;

class RunMigrationsCommand extends AbstractBaseCommand
{
    public function __construct(array $config)
    {
        parent::__construct("migrations:run", "Executa as migrations criadas", $config);
    }

    public function execute()
    {
        $io = $this->app()->io();

        $io->bgCyan("Rodando migrations...", true);

        $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . "/../../");
        $dotenv->load();

        $pdo = new PDO(
            "{$_ENV['DB_DRIVER']}:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']}",
            $_ENV['DB_USERNAME'],
            $_ENV['DB_PASSWORD'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]
        );

        $runner = new MigrationRunner($pdo);
        $runner->run(__DIR__ . "/../migrations", function (string $name) use ($io) {
            $io->ok("Migration $name ✅", true);
        });

        $io->ok("Migrations criadas!", true);
    }
}
