<?php

require_once __DIR__ . "/../vendor/autoload.php";

$faker = Faker\Factory::create('pt-BR');
$pdo = new PDO("mysql:host=127.0.0.1;dbname=tickets", "root", "Senha123!");

$password = 'secret123';
$hash = password_hash($password, PASSWORD_BCRYPT);
$roles = array('CHEFE', 'GERENTE', 'DESENVOLVEDOR', 'ANALISTA', 'REDATOR', 'COLUNISTA');
$teams = array('Inteligência e imagem', 'Desenvolvimento', 'Coordenação', 'Infra e Suporte', 'Negócios', 'Mídia', 'Relacionamento');
$team_ids = [];
$stmt = $pdo->prepare("INSERT INTO teams (name) VALUES (?)");
foreach($teams as $team) {
    $stmt->execute([$team]);
    $team_ids[] = $pdo->lastInsertId();
}

$pdo->beginTransaction();
$stmt = $pdo->prepare("INSERT INTO clients (name) VALUES (?)");
for($i = 0; $i < 20; $i++) {
    $companyName = $faker->unique()->company;

    $stmt->execute([$companyName]);
}
$pdo->commit();

$sql = "INSERT INTO users (name, email, password_hash, role, team_id) VALUES (?, ?, ?, ?, ?)";
for ($i = 0; $i < 30; $i++) {
    $randTeamId = $team_ids[array_rand($team_ids)];
    $role = $roles[array_rand($roles)];
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $faker->name,
        $faker->unique()->safeEmail,
        $hash,
        $role, 
        $randTeamId
    ]);
}