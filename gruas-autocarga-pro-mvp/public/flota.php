<?php declare(strict_types=1);
require_once __DIR__ . '/../src/bootstrap.php';
$pageTitle = 'Flota | Grúas 365';
$fleet = [['01', 'Grúa 4x4', 'Motos', 'Ideal para motos y vehículos bloqueados en parqueaderos.', 'Hasta 500 kg'], ['02', 'Grúa liviana', 'Carros pequeños', 'Para un carro y varias motos en trayectos urbanos.', 'Hasta 2 t'], ['03', 'Grúa mediana', 'Carros', 'Mayor capacidad para carros y motos.', 'Hasta 3.5 t'], ['04', 'Grúa grande', 'Alta capacidad', 'Para vehículos de mayor peso y operaciones exigentes.', 'Según configuración']]; ?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?></title>
    <link rel="stylesheet" href="../assets/css/app.css?v=20261003">
</head>

<body>
    <header class="topbar topbar-premium"><a class="brand brand-logo" href="index.php"><span class="brand-mark"><img
                    src="../assets/img-gruas365-hq.png" alt="Logo Grúas 365"></span><span
                class="brand-copy"><strong>GRÚAS</strong><b>365</b><small>ASISTENCIA VIAL</small></span></a><button
            class="menu-toggle" type="button">☰</button>
        <nav id="main-nav"><a href="index.php">Inicio</a><a href="conocenos.php">Conócenos</a><a
                href="servicios.php">Servicios</a><a href="cotizar.php">Cotizar</a><a class="active"
                href="flota.php">Flota</a><a class="nav-login" href="login.php">Iniciar sesión</a></nav>
    </header>
    <main>
        <section class="page-hero">
            <p class="eyebrow">Nuestra flota</p>
            <h1>La grúa adecuada<br><em>para cada misión.</em></h1>
            <p>Seleccionamos la unidad según tu vehículo, el peso y las condiciones del servicio.</p>
        </section>
        <section class="section fleet-section">
            <div class="fleet-grid"><?php foreach ($fleet as $unit): ?>
                    <article class="fleet-card">
                        <div class="fleet-photo"><img src="../assets/img-gruas365-hq.png"
                                alt="Imagen pendiente de <?= e($unit[1]) ?>"><span>Foto
                                próximamente</span><b><?= e($unit[0]) ?></b></div>
                        <div class="fleet-info">
                            <p class="fleet-type"><?= e($unit[1]) ?></p>
                            <h3><?= e($unit[2]) ?></h3>
                            <p><?= e($unit[3]) ?></p><strong class="capacity">Capacidad: <?= e($unit[4]) ?></strong>
                        </div>
                    </article><?php endforeach; ?>
            </div>
        </section>
    </main>
    <footer class="site-footer">
        <div>
            <div class="brand-copy"><strong>GRÚAS</strong><b>365</b><small>ASISTENCIA VIAL</small></div>
            <p>Asistencia vehicular confiable, todos los días.</p>
        </div>
        <div>
            <h3>Explora</h3><a href="index.php">Inicio</a><a href="conocenos.php">Conócenos</a><a
                href="servicios.php">Servicios</a><a href="flota.php">Flota</a>
        </div>
        <div>
            <h3>Atención</h3>
            <p>Disponible 24 horas, 7 días a la semana.</p><a href="tel:+573001234567">☎ +57  322 4989899</a>
        </div>
        <div>
            <h3>Acción rápida</h3><a href="cotizar.php">Solicitar cotización →</a><a href="login.php">Iniciar sesión</a>
        </div>
        <div class="footer-bottom">
            <p>© <?= date('Y') ?> Grúas 365.</p>
            <p>Diseñado para moverte con tranquilidad.</p>
        </div>
    </footer>
    <script>const m = document.querySelector('.menu-toggle'), n = document.querySelector('#main-nav'); m?.addEventListener('click', () => n.classList.toggle('is-open'));</script>
</body>

</html>