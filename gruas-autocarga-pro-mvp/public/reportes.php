<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/bootstrap.php';
require_role('administrador');
$pdo = db();
$period = in_array($_GET['period'] ?? 'month', ['week', 'month'], true) ? (string) $_GET['period'] : 'month';
$anchor = preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) ($_GET['date'] ?? '')) ? new DateTimeImmutable((string) $_GET['date']) : new DateTimeImmutable('today');
$start = $period === 'week' ? $anchor->modify('monday this week') : $anchor->modify('first day of this month');
$end = $period === 'week' ? $start->modify('+7 days') : $start->modify('+1 month');
$employeeId = (int) ($_GET['empleado_id'] ?? 0);
$employees = $pdo->query("SELECT id, nombre FROM usuarios WHERE rol = 'empleado' ORDER BY nombre")->fetchAll();
$employeeFilter = $employeeId > 0 ? ' AND s.empleado_id = :employee_id' : '';
$expenseEmployeeFilter = $employeeId > 0 ? ' AND g.empleado_id = :employee_id' : '';
$params = ['start' => $start->format('Y-m-d 00:00:00'), 'end' => $end->format('Y-m-d 00:00:00')] + ($employeeId > 0 ? ['employee_id' => $employeeId] : []);
$serviceStmt = $pdo->prepare("SELECT s.*, u.nombre AS empleado FROM servicios s LEFT JOIN usuarios u ON u.id = s.empleado_id WHERE COALESCE(s.fecha_programada, s.created_at) >= :start AND COALESCE(s.fecha_programada, s.created_at) < :end{$employeeFilter} ORDER BY COALESCE(s.fecha_programada, s.created_at)");
$serviceStmt->execute($params);
$services = $serviceStmt->fetchAll();
$expenseStmt = $pdo->prepare("SELECT g.*, u.nombre AS empleado FROM gastos g LEFT JOIN usuarios u ON u.id = g.empleado_id WHERE g.fecha >= :start AND g.fecha < :end{$expenseEmployeeFilter} ORDER BY g.fecha");
$expenseStmt->execute($params);
$expenses = $expenseStmt->fetchAll();
$income = array_sum(array_map(static fn(array $row): float => (float) ($row['valor_final'] ?? 0), $services));
$expenseTotal = array_sum(array_map(static fn(array $row): float => (float) ($row['valor'] ?? 0), $expenses));
$periodLabel = $period === 'week' ? 'Semana del ' . $start->format('d/m/Y') . ' al ' . $end->modify('-1 day')->format('d/m/Y') : 'Mes de ' . $start->format('m/Y');
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reportes | Grúas 365</title>
    <link rel="stylesheet" href="../assets/css/app.css?v=20261003">
</head>

<body class="workspace-admin">
    <header class="topbar topbar-premium"><a class="brand brand-logo" href="dashboard.php"><span class="brand-mark"><img
                    src="../assets/img-gruas365-hq.png" alt="Logo Grúas 365"></span><span
                class="brand-copy"><strong>GRÚAS</strong><b>365</b><small>ASISTENCIA VIAL</small></span></a>
        <nav><a href="dashboard.php">Panel</a><a href="usuarios.php">Usuarios</a><a
                href="calendario.php">Calendario</a><a class="nav-login" href="logout.php">Cerrar sesión</a></nav>
    </header>
    <main class="section admin-page report-page">
        <div class="dashboard-header">
            <div>
                <p class="eyebrow">Visión financiera</p>
                <h1>Reportes <?= $period === 'week' ? 'semanales' : 'mensuales' ?></h1>
                <p><?= e($periodLabel) ?> · Consulta ingresos, gastos y actividad detallada.</p>
            </div>
            <form class="report-filter" method="get"><label>Tipo de reporte<select name="period">
                        <option value="month" <?= $period === 'month' ? 'selected' : '' ?>>Mensual</option>
                        <option value="week" <?= $period === 'week' ? 'selected' : '' ?>>Semanal</option>
                    </select></label><label>Fecha de referencia<input type="date" name="date"
                        value="<?= e($anchor->format('Y-m-d')) ?>"></label><label>Empleado<select name="empleado_id">
                        <option value="0">Todos los empleados</option><?php foreach ($employees as $employee): ?>
                            <option value="<?= e($employee['id']) ?>" <?= $employeeId === (int) $employee['id'] ? 'selected' : '' ?>><?= e($employee['nombre']) ?></option><?php endforeach; ?>
                    </select></label><button class="btn btn-primary" type="submit">Generar reporte</button></form>
        </div>
        <div class="expense-summary">
            <article class="summary-card"><small>Servicios realizados</small><strong><?= count($services) ?></strong>
            </article>
            <article class="summary-card">
                <small>Ingresos</small><strong>$<?= number_format($income, 0, ',', '.') ?></strong></article>
            <article class="summary-card">
                <small>Gastos</small><strong>$<?= number_format($expenseTotal, 0, ',', '.') ?></strong></article>
            <article class="summary-card"><small>Resultado
                    neto</small><strong>$<?= number_format($income - $expenseTotal, 0, ',', '.') ?></strong></article>
        </div>
        <div class="report-detail-grid">
            <section class="table-card">
                <div class="table-heading">
                    <div>
                        <p class="eyebrow">Ingresos</p>
                        <h2>Servicios del periodo</h2>
                    </div><span><?= count($services) ?> registros</span>
                </div>
                <div class="table-scroll">
                    <table>
                        <thead>
                            <tr>
                                <th>Fecha y hora</th>
                                <th>Empleado</th>
                                <th>Vehículo</th>
                                <th>Estado</th>
                                <th>Valor</th>
                            </tr>
                        </thead>
                        <tbody><?php foreach ($services as $service): ?>
                                <tr>
                                    <td><?= e($service['fecha_programada'] ?: $service['created_at']) ?></td>
                                    <td><?= e($service['empleado'] ?: 'Sin asignar') ?></td>
                                    <td><?= e(ucfirst((string) $service['tipo_vehiculo'])) ?></td>
                                    <td><?= e(ucfirst((string) $service['estado'])) ?></td>
                                    <td>$<?= number_format((float) ($service['valor_final'] ?? 0), 0, ',', '.') ?></td>
                                </tr><?php endforeach; ?><?php if (!$services): ?>
                                <tr>
                                    <td colspan="5">No hay servicios en este periodo.</td>
                                </tr><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
            <section class="table-card">
                <div class="table-heading">
                    <div>
                        <p class="eyebrow">Egresos</p>
                        <h2>Gastos del periodo</h2>
                    </div><span><?= count($expenses) ?> registros</span>
                </div>
                <div class="table-scroll">
                    <table>
                        <thead>
                            <tr>
                                <th>Fecha y hora</th>
                                <th>Empleado</th>
                                <th>Tipo</th>
                                <th>Descripción</th>
                                <th>Valor</th>
                            </tr>
                        </thead>
                        <tbody><?php foreach ($expenses as $expense): ?>
                                <tr>
                                    <td><?= e($expense['fecha']) ?></td>
                                    <td><?= e($expense['empleado'] ?: 'Sin asignar') ?></td>
                                    <td><?= e(ucfirst((string) $expense['tipo'])) ?></td>
                                    <td><?= e($expense['descripcion'] ?: '—') ?></td>
                                    <td>$<?= number_format((float) $expense['valor'], 0, ',', '.') ?></td>
                                </tr><?php endforeach; ?><?php if (!$expenses): ?>
                                <tr>
                                    <td colspan="5">No hay gastos en este periodo.</td>
                                </tr><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>
</body>

</html>