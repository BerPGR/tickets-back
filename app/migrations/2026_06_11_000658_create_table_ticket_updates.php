
<?php

return new class {
    public function up(PDO $pdo) {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS ticket_updates (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                ticket_id INT NOT NULL,
                user_id INT NOT NULL,
                modified_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                type ENUM('comment', 'update', 'assignment'),
                comment TEXT
            )
        ");
    }

    public function down(PDO $pdo) {
        $pdo->exec("DROP TABLE IF EXISTS ticket_updates");
    }
};