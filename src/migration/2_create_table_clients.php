<?php

return new class {
    public function up(PDO $pdo) {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS clients (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL UNIQUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }

    public function down(PDO $pdo) {
        $pdo->exec("DROP TABNLE IF EXISTS clients");
    }
};