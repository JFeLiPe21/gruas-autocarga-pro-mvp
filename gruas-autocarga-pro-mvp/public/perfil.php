<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/bootstrap.php';
require_role('empleado', 'administrador');
$user = current_user();

// Cambiar contraseña: se valida la clave actual y se guarda únicamente el hash.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_or_fail();
    $current = (string)($_POST['current_password'] ?? '');
    $new = (string)($_POST['new_password'] ?? '');
    $confirm = (string)($_POST['confirm_password'] ?? '');
    $stmt = db()->prepare('SELECT password_hash FROM usuarios WHERE id = :id');
    $stmt->execute(['id' => $user['id']]);
    $record = $stmt->fetch();
    if (!$record || !password_verify($current, $record['password_hash'])) {
        flash('error', 'La contraseña actual no es correcta.');
    } elseif (strlen($new) < 8 || $new !== $confirm) {
        flash('error', 'La nueva contraseña debe tener mínimo 8 caracteres y coincidir en ambos campos.');
    } else {
        $stmt = db()->prepare('UPDATE usuarios SET password_hash = :hash WHERE id = :id');
        $stmt->execute(['hash' => password_hash($new, PASSWORD_DEFAULT), 'id' => $user['id']]);
        flash('success', 'Contraseña actualizada correctamente.');
    }
    redirect('perfil.php');
}
?>
<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Mi perfil | Grúas 365</title><link rel="stylesheet" href="../assets/css/app.css?v=20261003"></head>
<body><header class="topbar topbar-premium"><a class="brand brand-logo" href="dashboard.php"><span class="brand-mark"><img src="../assets/img-gruas365-hq.png" alt="Logo Grúas 365"></span><span class="brand-copy"><strong>GRÚAS</strong><b>365</b><small>ASISTENCIA VIAL</small></span></a><nav><a href="dashboard.php">Panel</a><a class="nav-login" href="logout.php">Cerrar sesión</a></nav></header>
<main class="section profile-page"><div class="dashboard-header"><div><p class="eyebrow">Cuenta interna</p><h1>Mi perfil</h1><p><?= e($user['name']) ?> · <?= e(ucfirst($user['role'])) ?></p></div><a class="btn btn-outline" href="dashboard.php">← Panel</a></div><section class="quote-box profile-card"><p class="eyebrow">Seguridad</p><h2>Cambiar contraseña</h2><?php if ($message = flash('success')): ?><div class="notice success"><?= e($message) ?></div><?php endif; ?><?php if ($message = flash('error')): ?><div class="notice error"><?= e($message) ?></div><?php endif; ?><form method="post"><?= csrf_field() ?><label>Contraseña actual<input type="password" name="current_password" required autocomplete="current-password"></label><label>Nueva contraseña<input type="password" name="new_password" minlength="8" required autocomplete="new-password"></label><label>Repetir nueva contraseña<input type="password" name="confirm_password" minlength="8" required autocomplete="new-password"></label><button class="btn btn-primary" type="submit">Actualizar contraseña</button></form></section></main></body></html>
