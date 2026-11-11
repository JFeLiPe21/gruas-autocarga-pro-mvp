<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/bootstrap.php';
require_role('administrador');

// Este módulo demuestra el control real de roles: solo administradores pueden crear cuentas.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_or_fail();
    $action = (string) ($_POST['action'] ?? 'create');
    if ($action === 'toggle') {
        $id = (int) ($_POST['id'] ?? 0);
        // Nunca se desactiva la propia cuenta para evitar dejar el sistema sin acceso.
        if ($id > 0 && $id !== (int) $_SESSION['user_id']) {
            $stmt = db()->prepare('UPDATE usuarios SET estado = IF(estado = "activo", "inactivo", "activo") WHERE id = :id');
            $stmt->execute(['id' => $id]);
        }
        redirect('usuarios.php');
    }
    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0 && $id !== (int) $_SESSION['user_id']) {
            $countStmt = db()->prepare('SELECT (SELECT COUNT(*) FROM servicios WHERE empleado_id = :id1) + (SELECT COUNT(*) FROM gastos WHERE empleado_id = :id2) AS total');
            $countStmt->execute(['id1' => $id, 'id2' => $id]);
            $hasHistory = (int) $countStmt->fetchColumn() > 0;
            if ($hasHistory) {
                $stmt = db()->prepare('UPDATE usuarios SET estado = "inactivo" WHERE id = :id');
                $stmt->execute(['id' => $id]);
                flash('error', 'El usuario tiene historial y fue desactivado para conservar sus registros.');
            } else {
                $stmt = db()->prepare('DELETE FROM usuarios WHERE id = :id');
                $stmt->execute(['id' => $id]);
                flash('success', 'Usuario eliminado correctamente.');
            }
        }
        redirect('usuarios.php');
    }
    $nombre = trim((string) ($_POST['nombre'] ?? ''));
    $usuario = trim((string) ($_POST['usuario'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $rol = (string) ($_POST['rol'] ?? 'empleado');

    if ($nombre === '' || $usuario === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8 || !in_array($rol, ['empleado', 'administrador'], true)) {
        flash('error', 'Completa los datos correctamente. La contraseña debe tener mínimo 8 caracteres.');
        redirect('usuarios.php');
    }

    try {
        $stmt = db()->prepare('INSERT INTO usuarios (nombre, usuario, email, password_hash, rol) VALUES (:nombre, :usuario, :email, :hash, :rol)');
        $stmt->execute(['nombre' => $nombre, 'usuario' => $usuario, 'email' => $email, 'hash' => password_hash($password, PASSWORD_DEFAULT), 'rol' => $rol]);
        flash('success', 'Usuario creado correctamente.');
    } catch (PDOException $exception) {
        flash('error', 'No se pudo crear: el usuario o correo pueden estar registrados.');
    }
    redirect('usuarios.php');
}

$users = db()->query('SELECT id, nombre, usuario, email, rol, estado, created_at FROM usuarios ORDER BY created_at DESC')->fetchAll();
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Usuarios | Grúas 365</title>
    <link rel="stylesheet" href="../assets/css/app.css?v=20261003">
</head>

<body class="workspace-admin">
    <header class="topbar topbar-premium"><a class="brand brand-logo" href="dashboard.php"><span class="brand-mark"><img
                    src="../assets/img-gruas365-hq.png" alt="Logo Grúas 365"></span><span
                class="brand-copy"><strong>GRÚAS</strong><b>365</b><small>ASISTENCIA VIAL</small></span></a>
        <nav><a href="dashboard.php">Panel</a><a class="nav-login" href="logout.php">Cerrar sesión</a></nav>
    </header>
    <main class="section admin-page">
        <div class="dashboard-header">
            <div>
                <p class="eyebrow">Administración</p>
                <h1>Usuarios y roles</h1>
                <p>Crea cuentas internas y define si serán empleados o administradores.</p>
            </div><a class="btn btn-outline" href="dashboard.php">← Volver al panel</a>
        </div>
        <?php if ($message = flash('success')): ?>
            <div class="notice success"><?= e($message) ?></div><?php endif; ?><?php if ($message = flash('error')): ?>
            <div class="notice error"><?= e($message) ?></div><?php endif; ?>
        <div class="admin-layout">
            <section class="quote-box user-create-card">
                <p class="eyebrow">Nueva cuenta</p>
                <h2>Registrar usuario</h2>
                <p class="form-help">Elige si la cuenta será empleado o administrador. Los permisos se aplican
                    automáticamente al iniciar sesión.</p>
                <form method="post"><?= csrf_field() ?><label>Nombre completo<input name="nombre"
                            required></label><label>Usuario<input name="usuario" required></label><label>Correo
                        electrónico<input type="email" name="email" required></label><label>Contraseña temporal<input
                            type="password" name="password" minlength="8" required></label><label>Rol<select name="rol">
                            <option value="empleado">Empleado</option>
                            <option value="administrador">Administrador</option>
                        </select></label><button class="btn btn-primary" type="submit">Crear usuario</button></form>
            </section>
            <section class="user-list">
                <div class="table-heading">
                    <div>
                        <p class="eyebrow">Directorio interno</p>
                        <h2>Usuarios registrados</h2>
                    </div><span><?= count($users) ?> cuentas</span>
                </div><?php foreach ($users as $item): ?>
                    <article>
                        <div><strong><?= e($item['nombre']) ?></strong><small><?= e($item['email']) ?> ·
                                <?= e($item['usuario']) ?></small></div>
                        <div class="user-actions"><span
                                class="badge-rol <?= $item['rol'] === 'administrador' ? 'badge-admin' : 'badge-empleado' ?>"><?= e(ucfirst($item['rol'])) ?></span><span
                                class="state-dot <?= $item['estado'] === 'activo' ? 'state-active' : 'state-inactive' ?>"><?= e(ucfirst($item['estado'])) ?></span><?php if ($item['id'] !== (int) $_SESSION['user_id']): ?>
                                <form method="post" class="inline-form"><?= csrf_field() ?><input type="hidden" name="action"
                                        value="toggle"><input type="hidden" name="id" value="<?= e($item['id']) ?>"><button
                                        class="text-button"
                                        type="submit"><?= $item['estado'] === 'activo' ? 'Desactivar' : 'Activar' ?></button>
                                </form>
                                <form method="post" class="inline-form"><?= csrf_field() ?><input type="hidden" name="action"
                                        value="delete"><input type="hidden" name="id" value="<?= e($item['id']) ?>"><button
                                        class="text-button text-danger" type="submit">Eliminar</button></form><?php endif; ?>
                        </div>
                    </article><?php endforeach; ?>
            </section>
        </div>
    </main>
    <script>document.querySelectorAll('input,select').forEach(field => field.addEventListener('input', () => field.classList.remove('invalid')));</script>
</body>

</html>
