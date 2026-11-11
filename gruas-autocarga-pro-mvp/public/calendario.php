<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/bootstrap.php';
require_role('empleado', 'administrador');

$user = current_user();
$isAdmin = $user['role'] === 'administrador';
$pdo = db();

// El mes se recibe como YYYY-MM para que el filtro sea fácil de editar y compartir.
$month = preg_match('/^\d{4}-\d{2}$/', (string) ($_GET['month'] ?? '')) ? (string) $_GET['month'] : date('Y-m');
$start = new DateTimeImmutable($month . '-01');
$end = $start->modify('+1 month');
$monthNames = [1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril', 5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto', 9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'];
$weekdayNames = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
$selectedEmployee = $isAdmin ? (int) ($_GET['empleado_id'] ?? 0) : $user['id'];
$scope = $selectedEmployee > 0 ? ' AND s.empleado_id = :employee_id' : '';
$params = ['start' => $start->format('Y-m-d 00:00:00'), 'end' => $end->format('Y-m-d 00:00:00')] + ($selectedEmployee > 0 ? ['employee_id' => $selectedEmployee] : []);

// Cargamos servicios y gastos del mes para poder calcular cada tarjeta de día.
$stmt = $pdo->prepare("SELECT s.*, u.nombre AS empleado FROM servicios s LEFT JOIN usuarios u ON u.id = s.empleado_id WHERE COALESCE(s.fecha_programada, s.created_at) >= :start AND COALESCE(s.fecha_programada, s.created_at) < :end{$scope} ORDER BY COALESCE(s.fecha_programada, s.created_at)");
$stmt->execute($params);
$servicesByDay = [];
foreach ($stmt->fetchAll() as $service) {
    $day = substr((string) ($service['fecha_programada'] ?: $service['created_at']), 0, 10);
    $servicesByDay[$day][] = $service;
}
$expenseScope = $selectedEmployee > 0 ? ' AND g.empleado_id = :employee_id' : '';
$expenseStmt = $pdo->prepare("SELECT g.* FROM gastos g WHERE g.fecha >= :start AND g.fecha < :end{$expenseScope} ORDER BY g.fecha");
$expenseStmt->execute($params);
$expensesByDay = [];
foreach ($expenseStmt->fetchAll() as $expense) {
    $day = substr((string) $expense['fecha'], 0, 10);
    $expensesByDay[$day][] = $expense;
}

$daysInMonth = (int) $start->format('t');
$firstWeekday = (int) $start->format('N') - 1; // 0 = lunes.
$monthServices = array_sum(array_map('count', $servicesByDay));
$monthIncome = 0.0;
$monthExpenses = 0.0;
foreach ($servicesByDay as $items)
    foreach ($items as $item)
        $monthIncome += (float) ($item['valor_final'] ?? 0);
foreach ($expensesByDay as $items)
    foreach ($items as $item)
        $monthExpenses += (float) $item['valor'];
$previousMonth = $start->modify('-1 month')->format('Y-m');
$nextMonth = $start->modify('+1 month')->format('Y-m');
$employees = $isAdmin ? $pdo->query("SELECT id, nombre FROM usuarios WHERE rol = 'empleado' AND estado = 'activo' ORDER BY nombre")->fetchAll() : [];
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Calendario | Grúas 365</title>
    <link rel="stylesheet" href="../assets/css/app.css?v=20261003">
</head>

<body class="workspace-<?= $isAdmin ? 'admin' : 'employee' ?>">
    <header class="topbar topbar-premium"><a class="brand brand-logo" href="dashboard.php"><span class="brand-mark"><img
                    src="../assets/img-gruas365-hq.png" alt="Logo Grúas 365"></span><span
                class="brand-copy"><strong>GRÚAS</strong><b>365</b><small>ASISTENCIA VIAL</small></span></a>
        <nav><a href="dashboard.php">Panel</a><a href="servicios_admin.php">Servicios</a><a
                href="gastos.php">Gastos</a><a class="nav-login" href="logout.php">Cerrar sesión</a></nav>
    </header>
    <main class="section calendar-page">
        <div class="calendar-topbar">
            <div>
                <p class="eyebrow">Agenda operativa</p>
                <h1>Calendario de <?= e($monthNames[(int) $start->format('n')]) ?>
                    <span><?= e($start->format('Y')) ?></span></h1>
                <p><?= $isAdmin ? ($selectedEmployee > 0 ? 'Vista filtrada por empleado.' : 'Vista global de la operación.') : 'Tus servicios y gastos del mes.' ?>
                </p>
            </div>
            <div class="calendar-toolbar">
                <form class="employee-filter" method="get"><input type="hidden" name="month"
                        value="<?= e($month) ?>"><?php if ($isAdmin): ?><label>Empleado<select name="empleado_id">
                                <option value="0">Todos los empleados</option><?php foreach ($employees as $employee): ?>
                                    <option value="<?= e($employee['id']) ?>" <?= $selectedEmployee === (int) $employee['id'] ? 'selected' : '' ?>><?= e($employee['nombre']) ?></option><?php endforeach; ?>
                            </select></label><?php endif; ?><button class="btn btn-primary" type="submit">Aplicar
                        filtro</button></form>
                <div class="calendar-actions"><a class="btn btn-outline"
                        href="calendario.php?month=<?= e($previousMonth) ?><?= $selectedEmployee ? '&empleado_id=' . e($selectedEmployee) : '' ?>">←</a><a
                        class="btn btn-outline"
                        href="calendario.php?month=<?= e(date('Y-m')) ?><?= $selectedEmployee ? '&empleado_id=' . e($selectedEmployee) : '' ?>">Hoy</a><a
                        class="btn btn-outline"
                        href="calendario.php?month=<?= e($nextMonth) ?><?= $selectedEmployee ? '&empleado_id=' . e($selectedEmployee) : '' ?>">→</a>
                </div>
            </div>
        </div>
        <div class="calendar-layout">
            <section class="calendar-card">
                <div class="weekday-row">
                    <?php foreach ($weekdayNames as $weekday): ?><span><?= e($weekday) ?></span><?php endforeach; ?>
                </div>
                <div class="calendar-grid calendar-grid-modern">
                    <?php for ($blank = 0; $blank < $firstWeekday; $blank++): ?>
                        <div class="calendar-day calendar-empty"></div><?php endfor; ?>
                    <?php for ($day = 1; $day <= $daysInMonth; $day++):
                        $date = $start->setDate((int) $start->format('Y'), (int) $start->format('m'), $day)->format('Y-m-d');
                        $services = $servicesByDay[$date] ?? [];
                        $expenses = $expensesByDay[$date] ?? [];
                        $income = array_sum(array_map(static fn(array $item): float => (float) ($item['valor_final'] ?? 0), $services));
                        $expenseTotal = array_sum(array_map(static fn(array $item): float => (float) $item['valor'], $expenses));
                        $activity = [];
                        foreach ($services as $item) $activity[] = ['kind' => 'Servicio', 'time' => substr((string) ($item['fecha_programada'] ?: $item['created_at']), 11, 5), 'employee' => $item['empleado'] ?: 'Sin asignar', 'detail' => ucfirst((string) $item['tipo_vehiculo']), 'value' => (float) ($item['valor_final'] ?? 0)];
                        foreach ($expenses as $item) $activity[] = ['kind' => 'Gasto', 'time' => substr((string) $item['fecha'], 11, 5), 'employee' => $item['empleado'] ?? 'Sin asignar', 'detail' => ucfirst((string) $item['tipo']) . ': ' . ((string) $item['descripcion'] ?: 'Sin descripción'), 'value' => (float) $item['valor']]; ?>
                        <article class="calendar-day calendar-day-modern <?= $services || $expenses ? 'has-events' : '' ?>"
                            tabindex="0" data-date="<?= e($date) ?>" data-services="<?= count($services) ?>"
                            data-income="<?= e($income) ?>" data-expenses="<?= e($expenseTotal) ?>" data-activity="<?= e(json_encode($activity, JSON_UNESCAPED_UNICODE)) ?>">
                            <header><strong><?= $day ?></strong><?php if ($services || $expenses): ?><span
                                        class="event-count"><?= count($services) + count($expenses) ?></span><?php endif; ?>
                            </header>
                            <div class="day-preview"><?php foreach (array_slice($services, 0, 2) as $service): ?><span
                                        class="mini-event service-event"><?= e(substr((string) ($service['fecha_programada'] ?: $service['created_at']), 11, 5)) ?>
                                        ·
                                        <?= e(ucfirst($service['tipo_vehiculo'])) ?></span><?php endforeach; ?><?php foreach (array_slice($expenses, 0, 1) as $expense): ?><span
                                        class="mini-event expense-event">Gasto ·
                                        $<?= number_format((float) $expense['valor'], 0, ',', '.') ?></span><?php endforeach; ?><?php if (count($services) + count($expenses) > 3): ?><small>+
                                        más actividad</small><?php endif; ?></div>
                        </article>
                    <?php endfor; ?>
                </div>
            </section>
            <aside class="calendar-summary" id="calendar-summary" data-month-services="<?= e($monthServices) ?>"
                data-month-income="<?= e($monthIncome) ?>" data-month-expenses="<?= e($monthExpenses) ?>">
                <p class="eyebrow">Resumen seleccionado</p>
                <h2 id="summary-title">Mes completo</h2>
                <p class="summary-hint">Selecciona uno o varios días para ver su detalle.</p>
                <div class="summary-stat"><span>Servicios</span><strong
                        id="summary-services"><?= $monthServices ?></strong></div>
                <div class="summary-stat"><span>Ingresos</span><strong
                        id="summary-income">$<?= number_format($monthIncome, 0, ',', '.') ?></strong></div>
                <div class="summary-stat"><span>Gastos</span><strong
                        id="summary-expenses">$<?= number_format($monthExpenses, 0, ',', '.') ?></strong></div>
                <div class="summary-stat summary-net"><span>Resultado</span><strong
                        id="summary-net">$<?= number_format($monthIncome - $monthExpenses, 0, ',', '.') ?></strong>
                </div>
                <button class="btn btn-primary apply-selection" type="button">Aplicar selección</button><button
                    class="btn btn-outline summary-reset" type="button">Ver mes completo</button>
                <div class="calendar-activity" id="calendar-activity"><p class="eyebrow">Detalle de actividad</p><p class="summary-hint">Selecciona días y aplica para ver servicios y gastos.</p></div>
            </aside>
        </div>
    </main>
    <script>
        // La selección es solo visual: los datos ya vienen calculados desde PHP y no requiere otra consulta.
        const summary = document.querySelector('#calendar-summary');
        const title = document.querySelector('#summary-title');
        const selected = new Set();
        const money = value => '$' + Number(value).toLocaleString('es-CO', { maximumFractionDigits: 0 });
        const activityBox = document.querySelector('#calendar-activity');
        function renderActivity(cards) {
            const rows = cards.flatMap(card => (JSON.parse(card.dataset.activity || '[]')).map(item => ({ ...item, date: card.dataset.date })));
            if (!rows.length) { activityBox.innerHTML = '<p class="eyebrow">Detalle de actividad</p><p class="summary-hint">Selecciona días con actividad y aplica para ver el detalle.</p>'; return; }
            activityBox.innerHTML = '<p class="eyebrow">Detalle de actividad</p>' + rows.map(item => '<div class="activity-row"><strong>' + item.date.split('-').reverse().join('/') + ' · ' + item.time + '</strong><span>' + item.kind + ' · ' + item.employee + '</span><small>' + item.detail + ' · ' + money(item.value) + '</small></div>').join('');
        }
        function renderSummary() {
            const cards = [...document.querySelectorAll('.calendar-day-modern')].filter(card => selected.has(card.dataset.date));
            const source = cards.length ? cards : null;
            const services = source ? cards.reduce((sum, card) => sum + Number(card.dataset.services), 0) : Number(summary.dataset.monthServices);
            const income = source ? cards.reduce((sum, card) => sum + Number(card.dataset.income), 0) : Number(summary.dataset.monthIncome);
            const expenses = source ? cards.reduce((sum, card) => sum + Number(card.dataset.expenses), 0) : Number(summary.dataset.monthExpenses);
            title.textContent = source ? (cards.length + ' día' + (cards.length > 1 ? 's' : '') + ' seleccionado' + (cards.length > 1 ? 's' : '')) : 'Mes completo';
            document.querySelector('#summary-services').textContent = services;
            document.querySelector('#summary-income').textContent = money(income);
            document.querySelector('#summary-expenses').textContent = money(expenses);
            document.querySelector('#summary-net').textContent = money(income - expenses);
            renderActivity(source || []);
        }
        document.querySelectorAll('.calendar-day-modern').forEach(card => card.addEventListener('click', () => { if (!card.dataset.date) return; card.classList.toggle('is-selected'); card.classList.contains('is-selected') ? selected.add(card.dataset.date) : selected.delete(card.dataset.date); renderSummary(); }));
        document.querySelector('.apply-selection').addEventListener('click', () => { document.querySelectorAll('.calendar-day-modern').forEach(card => card.classList.toggle('is-applied', selected.has(card.dataset.date))); renderSummary(); });
        document.querySelector('.summary-reset').addEventListener('click', () => { selected.clear(); document.querySelectorAll('.is-selected,.is-applied').forEach(card => card.classList.remove('is-selected', 'is-applied')); renderSummary(); });
    </script>
</body>

</html>
