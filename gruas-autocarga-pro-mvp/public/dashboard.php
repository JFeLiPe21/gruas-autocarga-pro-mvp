<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/bootstrap.php';
require_login();

// current_user() obtiene la sesión y su rol desde la base de datos.
$user = current_user();
$isAdmin = $user['role'] === 'administrador';
$pdo = db();
$today = date('Y-m-d');
$monthStart = date('Y-m-01');
$scopeSql = $isAdmin ? '' : ' AND empleado_id = :employee_id';
$scopeParams = $isAdmin ? [] : ['employee_id' => $user['id']];
$todayStmt = $pdo->prepare("SELECT COUNT(*) FROM servicios WHERE DATE(COALESCE(fecha_programada, created_at)) = :today{$scopeSql}");
$todayStmt->execute(['today' => $today] + $scopeParams);
$todayServices = (int) $todayStmt->fetchColumn();
$monthStmt = $pdo->prepare("SELECT COALESCE(SUM(valor_final), 0) FROM servicios WHERE estado = 'realizado' AND DATE(COALESCE(fecha_realizado, created_at)) >= :month_start{$scopeSql}");
$monthStmt->execute(['month_start' => $monthStart] + $scopeParams);
$monthIncome = (float) $monthStmt->fetchColumn();
$pending = (int) $pdo->query("SELECT COUNT(*) FROM solicitudes_cotizacion WHERE estado = 'nueva'")->fetchColumn();
$availableTrucks = (int) $pdo->query("SELECT COUNT(*) FROM gruas WHERE estado = 'disponible'")->fetchColumn();
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel | Grúas 365</title>
    <link rel="stylesheet" href="../assets/css/app.css?v=20261003">
</head>

<body class="workspace-<?= $isAdmin ? 'admin' : 'employee' ?>">
    <header class="topbar topbar-premium"><a class="brand brand-logo" href="index.php"><span class="brand-mark"><img
                    src="../assets/img-gruas365-hq.png" alt="Logo Grúas 365"></span><span
                class="brand-copy"><strong>GRÚAS</strong><b>365</b><small>ASISTENCIA VIAL</small></span></a><button class="menu-toggle" type="button" aria-label="Abrir menú" aria-expanded="false">☰</button>
        <nav id="workspace-nav"><?php if ($isAdmin): ?><a href="servicios_admin.php">Servicios</a><a href="gastos.php">Gastos</a><a href="calendario.php">Calendario</a><a href="reportes.php">Reportes</a><?php else: ?><a href="servicios_admin.php">Mis servicios</a><a href="gastos.php">Mis gastos</a><a href="calendario.php">Mi calendario</a><?php endif; ?><a href="perfil.php">Mi perfil</a><a href="index.php">Ver sitio público</a><a class="nav-login"
                href="logout.php">Cerrar sesión</a></nav>
    </header>
    <main class="section dashboard-shell">
        <div class="dashboard-header">
            <div>
                <p class="eyebrow">Panel de operaciones</p>
                <h1>Hola, <?= e($user['name']) ?></h1>
                <p>Sesión activa como <span
                        class="badge-rol <?= $isAdmin ? 'badge-admin' : 'badge-empleado' ?>"><?= e(ucfirst($user['role'])) ?></span>
                </p>
            </div><a class="btn btn-primary" href="servicios_admin.php">Registrar servicio →</a>
        </div>
        <!-- Resumen inicial: estos indicadores se conectarán a consultas reales en el siguiente módulo. -->
        <section class="dashboard-grid">
            <article class="dashboard-card">
                <p>Servicios hoy</p><span
                    class="metric"><?= $todayServices ?></span><small><?= $isAdmin ? 'Toda la operación' : 'Asignados a ti' ?></small>
            </article>
            <article class="dashboard-card">
                <p>Solicitudes nuevas</p><span class="metric"><?= $pending ?></span><small>Por revisar</small>
            </article>
            <article class="dashboard-card">
                <p>Grúas disponibles</p><span class="metric"><?= $availableTrucks ?></span><small>En la flota</small>
            </article>
            <article class="dashboard-card">
                <p><?= $isAdmin ? 'Ingresos del mes' : 'Tus ingresos del mes' ?></p><span
                    class="metric">$<?= number_format($monthIncome, 0, ',', '.') ?></span><small>Servicios
                    realizados</small>
            </article>
        </section>
        <?php if ($isAdmin): ?>
            <!-- Solo administradores ven la gestión de usuarios, tarifas y reportes globales. -->
            <section class="role-section">
                <p class="eyebrow">Permisos de administrador</p>
                <h2>Control global del negocio</h2>
                <p>Administra usuarios, revisa la operación completa y consulta resultados filtrados por empleado.</p>
                <div class="role-actions"><a class="btn btn-primary" href="usuarios.php">Gestionar usuarios</a><a
                        class="btn btn-outline" href="servicios_admin.php">Servicios</a><a class="btn btn-outline"
                        href="gastos.php">Gastos</a><a class="btn btn-outline" href="calendario.php">Calendario</a><a
                        class="btn btn-outline" href="reportes.php">Reportes</a></div>
            </section>
        <?php else: ?>
            <!-- Empleados ven solo las tareas operativas de su jornada. -->
            <section class="role-section">
                <p class="eyebrow">Permisos de empleado</p>
                <h2>Tu jornada operativa</h2>
                <p>Registra servicios, gastos y consulta tu agenda y resumen mensual.</p>
                <div class="role-actions"><a class="btn btn-primary" href="servicios_admin.php">Mis servicios</a><a
                        class="btn btn-outline" href="gastos.php">Registrar gasto</a><a class="btn btn-outline"
                        href="calendario.php">Mi calendario</a></div>
            </section>
        <?php endif; ?>
    </main>
    <footer class="site-footer">
        <div>
            <div class="brand-copy"><strong>GRÚAS</strong><b>365</b><small>ASISTENCIA VIAL</small></div>
            <p>Panel seguro para el equipo Grúas 365.</p>
        </div>
        <div>
            <h3>Sesión</h3>
            <p><?= e($user['name']) ?><br><?= e(ucfirst($user['role'])) ?></p>
        </div>
        <div>
            <h3>Ayuda</h3><a href="index.php">Volver al sitio público</a><a href="logout.php">Cerrar sesión</a>
        </div>
        <div class="footer-bottom">
            <p>© <?= date('Y') ?> Grúas 365.</p>
            <p>Acceso protegido.</p>
        </div>
    </footer>
</body>

</html>
