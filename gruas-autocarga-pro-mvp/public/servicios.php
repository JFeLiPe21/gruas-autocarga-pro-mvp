<?php declare(strict_types=1);
require_once __DIR__ . '/../src/bootstrap.php';
$pageTitle = 'Servicios | Grúas 365'; ?>
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
        <nav id="main-nav"><a href="index.php">Inicio</a><a href="conocenos.php">Conócenos</a><a class="active"
                href="servicios.php">Servicios</a><a href="cotizar.php">Cotizar</a><a href="flota.php">Flota</a><a
                class="nav-login" href="login.php">Iniciar sesión</a></nav>
    </header>
    <main>
        <section class="page-hero">
            <p class="eyebrow">Soluciones 24/7</p>
            <h1>Estamos preparados<br><em>para ayudarte.</em></h1>
            <p>Cuéntanos qué necesitas y asignaremos la atención adecuada.</p>
        </section>
        <section class="section services-page-grid">
            <article class="service-feature"><span class="value-icon">↗</span>
                <h2>Traslado de vehículos</h2>
                <p>Movemos carros, camionetas y motos de forma segura hacia el destino que elijas.</p><a
                    class="btn btn-primary" href="cotizar.php">Cotizar traslado →</a>
            </article>
            <article class="service-feature"><span class="value-icon">◇</span>
                <h2>Asistencia en carretera</h2>
                <p>Apoyo para imprevistos, vehículos inmovilizados y situaciones que requieren respuesta rápida.</p><a
                    class="btn btn-outline" href="tel:+ +57 322 4989899">Llamar ahora</a>
            </article>
            <article class="service-feature"><span class="value-icon">⌖</span>
                <h2>Extracción y parqueaderos</h2>
                <p>Soluciones especializadas para vehículos bloqueados o de difícil acceso.</p><a
                    class="btn btn-outline" href="https://wa.me/message/HQLZ6WGWG73VH1" target="_blank" rel="noopener">Escribir
                    por WhatsApp</a>
            </article>
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
            <p>Disponible 24 horas, 7 días a la semana.</p><a href="tel: +57 322 4989899">☎ +57 322 4989899</a>
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