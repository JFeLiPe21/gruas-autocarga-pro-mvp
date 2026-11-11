<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/bootstrap.php';
require_role('empleado', 'administrador');
$user = current_user();
$isAdmin = $user['role'] === 'administrador';
$pdo = db();

// Crear o actualizar servicios. El empleado siempre queda asociado a su propia cuenta.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_or_fail();
    $action = (string)($_POST['action'] ?? 'create');
    if ($action === 'status') {
        $id = (int)($_POST['id'] ?? 0);
        $status = (string)($_POST['estado'] ?? 'pendiente');
        $allowed = ['pendiente', 'programado', 'realizado', 'cancelado'];
        if ($id > 0 && in_array($status, $allowed, true)) {
            $sql = $isAdmin ? 'UPDATE servicios SET estado = :estado, fecha_realizado = IF(:estado2 = "realizado", NOW(), fecha_realizado) WHERE id = :id' : 'UPDATE servicios SET estado = :estado, fecha_realizado = IF(:estado2 = "realizado", NOW(), fecha_realizado) WHERE id = :id AND empleado_id = :employee_id';
            $params = ['estado' => $status, 'estado2' => $status, 'id' => $id] + ($isAdmin ? [] : ['employee_id' => $user['id']]);
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
        }
        redirect('servicios_admin.php');
    }

    $tipo = (string)($_POST['tipo_vehiculo'] ?? 'carro');
    $origen = trim((string)($_POST['origen'] ?? ''));
    $destino = trim((string)($_POST['destino'] ?? ''));
    $modelo = trim((string)($_POST['modelo_vehiculo'] ?? ''));
    $fecha = trim((string)($_POST['fecha_programada'] ?? ''));
    $hora = trim((string)($_POST['hora_servicio'] ?? ''));
    if (!$isAdmin && $fecha !== '') {
        $fecha .= ' ' . ($hora !== '' ? $hora : '00:00') . ':00';
    }
    $fecha = str_replace('T', ' ', $fecha);
    $valor = (float)($_POST['valor_final'] ?? 0);
    $employeeId = $isAdmin ? (int)($_POST['empleado_id'] ?? 0) : $user['id'];
    // La base exige origen y destino; para el registro rápido del empleado usamos etiquetas internas.
    if (!$isAdmin) {
        $origen = $origen !== '' ? $origen : 'Servicio registrado por empleado';
        $destino = $destino !== '' ? $destino : 'Destino no especificado';
    }
    if ($origen === '' || $destino === '' || !in_array($tipo, ['moto', 'carro', 'camioneta'], true) || $employeeId <= 0) {
        flash('error', 'Completa origen, destino, vehículo y empleado responsable.');
        redirect('servicios_admin.php');
    }
    $initialStatus = $isAdmin ? 'programado' : 'realizado';
    $stmt = $pdo->prepare('INSERT INTO servicios (empleado_id, tipo_vehiculo, modelo_vehiculo, origen, destino, fecha_programada, fecha_realizado, valor_final, estado) VALUES (:empleado_id, :tipo, :modelo, :origen, :destino, :fecha, :realizado, :valor, :estado)');
    $stmt->execute(['empleado_id' => $employeeId, 'tipo' => $tipo, 'modelo' => $modelo ?: null, 'origen' => $origen, 'destino' => $destino, 'fecha' => $fecha ?: null, 'realizado' => $isAdmin ? null : ($fecha ?: date('Y-m-d H:i:s')), 'valor' => $valor > 0 ? $valor : null, 'estado' => $initialStatus]);
    flash('success', 'Servicio registrado correctamente.');
    redirect('servicios_admin.php');
}

$employees = $pdo->query("SELECT id, nombre FROM usuarios WHERE rol = 'empleado' AND estado = 'activo' ORDER BY nombre")->fetchAll();
$where = $isAdmin ? '' : 'WHERE s.empleado_id = :employee_id';
$stmt = $pdo->prepare("SELECT s.*, u.nombre AS empleado FROM servicios s LEFT JOIN usuarios u ON u.id = s.empleado_id {$where} ORDER BY COALESCE(s.fecha_programada, s.created_at) DESC LIMIT 100");
$stmt->execute($isAdmin ? [] : ['employee_id' => $user['id']]);
$services = $stmt->fetchAll();
?>
<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Servicios | Grúas 365</title><link rel="stylesheet" href="../assets/css/app.css?v=20261003"></head>
<body class="workspace-<?= $isAdmin ? 'admin' : 'employee' ?>"><header class="topbar topbar-premium"><a class="brand brand-logo" href="dashboard.php"><span class="brand-mark"><img src="../assets/img-gruas365-hq.png" alt="Logo Grúas 365"></span><span class="brand-copy"><strong>GRÚAS</strong><b>365</b><small>ASISTENCIA VIAL</small></span></a><nav><a href="dashboard.php">Panel</a><a href="calendario.php">Calendario</a><a class="nav-login" href="logout.php">Cerrar sesión</a></nav></header>
<main class="section admin-page"><div class="dashboard-header"><div><p class="eyebrow">Operación</p><h1><?= $isAdmin ? 'Todos los servicios' : 'Mis servicios' ?></h1><p>Registra, agenda y actualiza el estado de cada atención.</p></div><a class="btn btn-outline" href="dashboard.php">← Panel</a></div>
<?php if ($message = flash('success')): ?><div class="notice success"><?= e($message) ?></div><?php endif; ?><?php if ($message = flash('error')): ?><div class="notice error"><?= e($message) ?></div><?php endif; ?>
<section class="quote-box service-form-box"><p class="eyebrow">Nuevo registro</p><h2><?= $isAdmin ? 'Agendar servicio' : 'Registrar servicio realizado' ?></h2><?php if ($isAdmin): ?><p class="form-help">El administrador asigna la ruta, el empleado y el valor del servicio.</p><?php else: ?><p class="form-help">Solo necesitas indicar cuándo trabajaste, qué vehículo atendiste y cuánto cobraste.</p><?php endif; ?><form method="post" class="service-form"><?= csrf_field() ?><input type="hidden" name="action" value="create"><label>Tipo de vehículo<select name="tipo_vehiculo"><option value="moto">Moto</option><option value="carro" selected>Carro</option><option value="camioneta">Camioneta</option></select></label><label>Modelo / vehículo<input name="modelo_vehiculo" placeholder="Ej. Kia Picanto"></label><?php if ($isAdmin): ?><label>Origen<input name="origen" required placeholder="Punto A"></label><label>Destino<input name="destino" required placeholder="Punto B"></label><label>Fecha programada<input type="datetime-local" name="fecha_programada"></label><?php else: ?><label>Fecha<input type="date" name="fecha_programada" value="<?= e(date('Y-m-d')) ?>" required></label><label>Hora<input type="time" name="hora_servicio" value="<?= e(date('H:i')) ?>" required></label><?php endif; ?><label>Valor del servicio<input type="number" min="0" step="1000" name="valor_final" placeholder="0" required></label><?php if ($isAdmin): ?><label>Empleado responsable<select name="empleado_id" required><option value="">Selecciona un empleado</option><?php foreach ($employees as $employee): ?><option value="<?= e($employee['id']) ?>"><?= e($employee['nombre']) ?></option><?php endforeach; ?></select></label><?php endif; ?><button class="btn btn-primary" type="submit">Guardar servicio</button></form></section>
<section class="table-card"><div class="table-heading"><h2>Agenda reciente</h2><span><?= count($services) ?> registros</span></div><div class="table-scroll"><table><thead><tr><th>Fecha</th><th>Ruta</th><th>Vehículo</th><th><?= $isAdmin ? 'Empleado' : 'Estado' ?></th><th>Valor</th><th>Estado</th><th></th></tr></thead><tbody><?php foreach ($services as $service): ?><tr><td><?= e($service['fecha_programada'] ?: $service['created_at']) ?></td><td><?= e($service['origen']) ?> → <?= e($service['destino']) ?></td><td><?= e(ucfirst($service['tipo_vehiculo'])) ?></td><td><?= e($isAdmin ? ($service['empleado'] ?: 'Sin asignar') : ucfirst($service['estado'])) ?></td><td>$<?= number_format((float)($service['valor_final'] ?? 0), 0, ',', '.') ?></td><td><span class="status-pill status-<?= e($service['estado']) ?>"><?= e(ucfirst($service['estado'])) ?></span></td><td><form method="post" class="inline-form"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="status"><input type="hidden" name="id" value="<?= e($service['id']) ?>"><select name="estado" onchange="this.form.submit()"><option value="pendiente" <?= $service['estado']==='pendiente'?'selected':'' ?>>Pendiente</option><option value="programado" <?= $service['estado']==='programado'?'selected':'' ?>>Programado</option><option value="realizado" <?= $service['estado']==='realizado'?'selected':'' ?>>Realizado</option><option value="cancelado" <?= $service['estado']==='cancelado'?'selected':'' ?>>Cancelado</option></select></form></td></tr><?php endforeach; ?><?php if (!$services): ?><tr><td colspan="7">Todavía no hay servicios registrados.</td></tr><?php endif; ?></tbody></table></div></section></main></body></html>
