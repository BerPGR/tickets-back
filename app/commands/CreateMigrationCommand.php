<?php

declare(strict_types= 1);
use flight\commands\AbstractBaseCommand;

class CreateMigrationCommand extends AbstractBaseCommand {
    public function __construct(array $config) {
        parent::__construct("migration:create", "Criar um arquivo de migration", $config);
        $this->argument("<migration-name>", "Nome da migration")->option("-t,--table [table]", "Nome da tabela");
    }

    public function execute(string $migrationName, string $table) {
        $io = $this->app()->io();

        $io->bgCyan("Criando arquivo de migration...", true);

        $timestamp = date('Y_m_d_His');
        $filename = $timestamp . '_' . $migrationName . '.php';
        $path = __DIR__ . '/../migration/' . $filename;

        $template = <<<PHP

        <?php

        return new class {
            public function up(\PDO \$pdo) {
                \$pdo->exec("CREATE TABLE IF NOT EXISTS $table ()");
            }

            public function down(PDO \$pdo) {
                \$pdo->exec("DROP TABLE IF EXISTS $table ()");
            }
        }
        PHP;

        file_put_contents($path, $template);

        $io->ok("Migration criada com sucesso!");
    }
}