<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/bootstrap.php';
require_role('empleado', 'administrador');
$user = current_user();
$isAdmin = $user['role'] === 'administrador';
$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_or_fail();
    $employeeId = $isAdmin ? (int) ($_POST['empleado_id'] ?? 0) : $user['id'];
    $type = (string) ($_POST['tipo'] ?? 'otro');
    $value = (float) ($_POST['valor'] ?? 0);
    $date = (string) ($_POST['fecha'] ?? date('Y-m-d\TH:i'));
    $description = trim((string) ($_POST['descripcion'] ?? ''));
    if ($employeeId <= 0 || $value < 0 || !in_array($type, ['gasolina', 'peaje', 'reparacion', 'otro'], true)) {
        flash('error', 'Completa empleado, tipo y un valor válido (no negativo) para el gasto.');
        redirect('gastos.php');
    }
    $stmt = $pdo->prepare('INSERT INTO gastos (empleado_id, tipo, valor, fecha, descripcion) VALUES (:empleado_id, :tipo, :valor, :fecha, :descripcion)');
    $stmt->execute(['empleado_id' => $employeeId, 'tipo' => $type, 'valor' => $value, 'fecha' => str_replace('T', ' ', $date) . (strlen($date) === 16 ? ':00' : ''), 'descripcion' => $description ?: null]);
    flash('success', 'Gasto registrado correctamente.');
    redirect('gastos.php');
}

$employees = $pdo->query("SELECT id, nombre FROM usuarios WHERE rol = 'empleado' AND estado = 'activo' ORDER BY nombre")->fetchAll();
$where = $isAdmin ? '' : 'WHERE g.empleado_id = :employee_id';
$stmt = $pdo->prepare("SELECT g.*, u.nombre AS empleado FROM gastos g LEFT JOIN usuarios u ON u.id = g.empleado_id {$where} ORDER BY g.fecha DESC LIMIT 100");
$stmt->execute($isAdmin ? [] : ['employee_id' => $user['id']]);
$expenses = $stmt->fetchAll();
$total = array_sum(array_map(static fn(array $item): float => (float) $item['valor'], $expenses));
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gastos | Grúas 365</title>
    <link rel="stylesheet" href="../assets/css/app.css?v=20261003">
</head>

<body class="workspace-<?= $isAdmin ? 'admin' : 'employee' ?>">
    <header class="topbar topbar-premium"><a class="brand brand-logo" href="dashboard.php"><span class="brand-mark"><img
                    src="../assets/img-gruas365-hq.png" alt="Logo Grúas 365"></span><span
                class="brand-copy"><strong>GRÚAS</strong><b>365</b><small>ASISTENCIA VIAL</small></span></a>
        <nav><a href="dashboard.php">Panel</a><a href="servicios_admin.php">Servicios</a><a
                href="calendario.php">Calendario</a><a class="nav-login" href="logout.php">Cerrar sesión</a></nav>
    </header>
    <main class="section admin-page">
        <div class="dashboard-header">
            <div>
                <p class="eyebrow">Control financiero</p>
                <h1><?= $isAdmin ? 'Gastos de la operación' : 'Mis gastos' ?></h1>
                <p>Registra gasolina, peajes, reparaciones y otros costos.</p>
            </div><a class="btn btn-outline" href="dashboard.php">← Panel</a>
        </div>
        <?php if ($message = flash('success')): ?>
            <div class="notice success"><?= e($message) ?></div><?php endif; ?><?php if ($message = flash('error')): ?>
            <div class="notice error"><?= e($message) ?></div><?php endif; ?>
        <section class="quote-box">
            <p class="eyebrow">Nuevo gasto</p>
            <form method="post" class="service-form"><?= csrf_field() ?><label>Tipo<select name="tipo">
                        <option value="gasolina">Gasolina</option>
                        <option value="peaje">Peaje</option>
                        <option value="reparacion">Reparación</option>
                        <option value="otro">Otro</option>
                    </select></label><label>Valor<input type="number" name="valor" min="0" step="1"
                        required></label><label>Fecha<input type="datetime-local" name="fecha"
                        value="<?= e(date('Y-m-d\TH:i')) ?>" required></label><label>Descripción<input
                        name="descripcion"
                        placeholder="Detalle opcional"></label><?php if ($isAdmin): ?><label>Empleado<select
                            name="empleado_id" required>
                            <option value="">Selecciona un empleado</option><?php foreach ($employees as $employee): ?>
                                <option value="<?= e($employee['id']) ?>"><?= e($employee['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select></label><?php endif; ?><button class="btn btn-primary" type="submit">Guardar gasto</button>
            </form>
        </section>
        <div class="expense-summary">
            <article class="summary-card"><small>Registros visibles</small><strong><?= count($expenses) ?></strong>
            </article>
            <article class="summary-card"><small>Total
                    visible</small><strong>$<?= number_format($total, 0, ',', '.') ?></strong></article>
            <article class="summary-card"><small>Ámbito</small><strong><?= $isAdmin ? 'Global' : 'Personal' ?></strong>
            </article>
        </div>
        <section class="table-card">
            <div class="table-heading">
                <h2>Gastos recientes</h2>
            </div>
            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Empleado</th>
                            <th>Tipo</th>
                            <th>Valor</th>
                            <th>Descripción</th>
                        </tr>
                    </thead>
                    <tbody><?php foreach ($expenses as $expense): ?>
                            <tr>
                                <td><?= e($expense['fecha']) ?></td>
                                <td><?= e($expense['empleado'] ?: 'Sin asignar') ?></td>
                                <td><?= e(ucfirst($expense['tipo'])) ?></td>
                                <td>$<?= number_format((float) $expense['valor'], 0, ',', '.') ?></td>
                                <td><?= e($expense['descripcion'] ?: '—') ?></td>
                            </tr><?php endforeach; ?><?php if (!$expenses): ?>
                            <tr>
                                <td colspan="5">Todavía no hay gastos registrados.</td>
                            </tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>

</html>