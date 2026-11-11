<?php
declare(strict_types=1);

/*
 * Ejecuta este archivo SOLO de forma local y después elimínalo de producción.
 * Genera el hash con password_hash(), nunca guardes la contraseña directamente.
 */
require_once __DIR__ . '/../src/bootstrap.php';

$nombre = 'Administrador';
$usuario = 'admin';
$email = 'admin@local.test';
$password = 'CambiarEstaClave!123';

$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = db()->prepare(
    'INSERT INTO usuarios (nombre, usuario, email, password_hash, rol)
     VALUES (:nombre, :usuario, :email, :hash, "administrador")'
);

$stmt->execute([
    'nombre' => $nombre,
    'usuario' => $usuario,
    'email' => $email,
    'hash' => $hash,
]);

echo "Administrador creado. Cambia/elimina este script antes de producción.\n";
