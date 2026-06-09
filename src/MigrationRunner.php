<?php

class MigrationRunner {
    public function __construct(private PDO $pdo) {
        $this->ensureMigrationsTable();
    }

    private function ensureMigrationsTable() {
        $this->pdo->exec(
            "
            CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255) NOT NULL,
                executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
            "
        );
    }

    public function run(string $migrationsPath) {
        $files = glob($migrationsPath . "/*.php");
        sort($files);

        $ran = $this->getRanMigrations();

        foreach ($files as $file) {
            $name = basename($file);
            if (in_array($name, $ran)) continue;

            $migration = require $file;
            $migration->up($this->pdo);

            $stmt = $this->pdo->prepare("INSERT INTO migrations (migration) VALUES (?)");
            $stmt->execute([$name]);

            echo "Ran: $name\n";
        }
    }

    private function getRanMigrations() {
        return $this->pdo->query("SELECT migration FROM migrations")->fetchAll(PDO::FETCH_COLUMN);
    } 
}