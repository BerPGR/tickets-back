
<?php

return new class {
public function up(PDO $pdo) {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS tickets (
                id INT AUTO_INCREMENT PRIMARY KEY,
                owner_id INT NOT NULL,
                user_id INT NOT NULL,
                title VARCHAR(255),
                description TEXT,
                due_date DATE,
                priority ENUM('ALTA', 'MEDIA', 'BAIXA'),
                status ENUM('Aguardando', 'Em Execução', 'Revisão', 'Finalizado'),
                client_id INT NOT NULL,
                team_id INT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                CONSTRAINT fk_owner_ticket FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE,
                CONSTRAINT fk_user_ticket FOREIGN KEY (user_id) REFERENCES users(id),
                CONSTRAINT fk_client_ticket FOREIGN KEY (client_id) REFERENCES clients(id),
                CONSTRAINT fk_team_ticket FOREIGN KEY (team_id) REFERENCES teams(id)
            )
        ");
    }

    public function down(PDO $pdo) {
        $pdo->exec("DROP TABLE IF EXISTS tickets");
    }
};