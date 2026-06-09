<?php

return new class {
    public function up(PDO $pdo)
    {
        $pdo->exec("CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            role ENUM('CHEFE', 'GERENTE', 'DESENVOLVEDOR', 'ANALISTA', 'REDATOR', 'COLUNISTA') NOT NULL,
            team_id INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT fk_team_user FOREIGN KEY (team_id) REFERENCES teams(id)
        )");
    }

    public function down(PDO $pdo) {
        $pdo->exec("DROP TABLE IF EXISTS users");
    }
};
