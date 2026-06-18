
<?php

return new class {
    public function up(\PDO $pdo) {
        $pdo->exec("ALTER TABLE tickets MODIFY COLUMN status ENUM('Aguardando','Executando','Revisão','Finalizado') DEFAULT 'Aguardando'");
    }
};