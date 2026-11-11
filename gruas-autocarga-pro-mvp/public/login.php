<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_or_fail();

    $identifier = trim((string)($_POST['identifier'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    if ($identifier === '' || $password === '') {
        flash('error', 'Completa usuario/correo y contraseña.');
        redirect('login.php');
    }

    $pdo = db();
    $stmt = $pdo->prepare(
        'SELECT id, nombre, email, password_hash, rol, estado
         FROM usuarios
         WHERE (email = :email OR usuario = :usuario)
           AND estado = "activo"
         LIMIT 1'
    );
    $stmt->execute(['email' => $identifier, 'usuario' => $identifier]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        flash('error', 'Credenciales inválidas.');
        redirect('login.php');
    }

    session_regenerate_id(true);
    $_SESSION['user_id'] = (int)$user['id'];
    $_SESSION['role'] = $user['rol'];
    $_SESSION['name'] = $user['nombre'];
    $_SESSION['last_activity'] = time();

    redirect('dashboard.php');
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sesión | Grúas 365</title>
    <link rel="stylesheet" href="../assets/css/app.css?v=20261003">
</head>
<body class="center-page">
<main class="login-card">
    <p class="eyebrow">Inicio de sesión · Grúas 365</p>
    <h1>Iniciar sesión</h1>
    <?php if ($msg = flash('error')): ?><div class="alert"><?= e($msg) ?></div><?php endif; ?>
    <form method="post">
        <?= csrf_field() ?>
        <label>Usuario o correo
            <input name="identifier" required autocomplete="username">
        </label>
        <label>Contraseña
            <input type="password" name="password" required autocomplete="current-password">
        </label>
        <button class="btn" type="submit">Iniciar sesión</button>
    </form>
    <a href="index.php">← Volver al inicio</a>
</main>
</body>
</html>
