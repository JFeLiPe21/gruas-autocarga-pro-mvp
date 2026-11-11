<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/bootstrap.php';
// Página informativa independiente: aquí crecerá la historia, equipo y cobertura de la empresa.
$pageTitle = 'Conócenos | Grúas 365';
?>
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
        <nav id="main-nav"><a href="index.php">Inicio</a><a class="active" href="conocenos.php">Conócenos</a><a
                href="servicios.php">Servicios</a><a href="cotizar.php">Cotizar</a><a href="flota.php">Flota</a><a
                class="nav-login" href="login.php">Iniciar sesión</a></nav>
    </header>
    <main>
        <section class="page-hero">
            <p class="eyebrow">Nuestra historia</p>
            <h1>La experiencia que<br><em>te acompaña.</em></h1>
            <p>Más de 6 años convirtiendo momentos de estrés en soluciones seguras y oportunas.</p>
        </section>
        <section class="section content-split">
            <div>
                <p class="eyebrow">Quiénes somos</p>
                <h2>Una empresa cercana, profesional y siempre disponible.</h2>
            </div>
            <div class="content-copy">
                <p>En Grúas 365 nos especializamos en asistencia y remolque vehicular. Nuestro propósito es responder
                    con rapidez, cuidar tu vehículo y comunicarte cada paso del servicio.</p>
                <p>Trabajamos con procesos claros y una flota seleccionada para atender motos, carros y vehículos de
                    mayor capacidad.</p>
            </div>
        </section>
        <section class="section values-section">
            <div class="section-heading centered">
                <p class="eyebrow">Nuestros valores</p>
                <h2>Así hacemos las cosas</h2>
            </div>
            <div class="value-grid">
                <article class="value-card"><span class="value-icon">↗</span>
                    <h3>Rapidez</h3>
                    <p>Respuesta ágil desde el primer contacto.</p>
                </article>
                <article class="value-card"><span class="value-icon">◇</span>
                    <h3>Seguridad</h3>
                    <p>Procedimientos cuidadosos para proteger tu vehículo.</p>
                </article>
                <article class="value-card"><span class="value-icon">⌖</span>
                    <h3>Confianza</h3>
                    <p>Información clara y acompañamiento permanente.</p>
                </article>
                <article class="value-card"><span class="value-icon">◷</span>
                    <h3>Disponibilidad</h3>
                    <p>Atención 24 horas, todos los días.</p>
                </article>
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