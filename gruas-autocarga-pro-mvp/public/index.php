<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/bootstrap.php';

// CONFIGURACIÓN RÁPIDA: cambia aquí el número real cuando lo tengas.
$pageTitle = 'Grúas 365 | Asistencia vehicular 24/7';
$phone = '+57 322 4989899';
$whatsappNumber = 'wa.me/message/HQLZ6WGWG73VH1';
$whatsappMessage = rawurlencode('Hola Grúas 365, necesito un servicio de grúa urgentemente.');
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Grúas 365: asistencia y remolque vehicular 24/7.">
    <title><?= e($pageTitle) ?></title>
    <link rel="stylesheet" href="../assets/css/app.css?v=20261003">
</head>

<body class="home-page">
    <!-- Navbar: las rutas son páginas independientes, no anclas dentro del inicio. -->
    <header class="topbar topbar-premium">
        <a class="brand brand-logo" href="index.php" aria-label="Ir al inicio de Grúas 365">
            <span class="brand-mark"><img src="../assets/img-gruas365-hq.png" alt="Logo Grúas 365"></span>
            <span class="brand-copy"><strong>GRÚAS</strong><b>365</b><small>ASISTENCIA VIAL</small></span>
        </a>
        <span class="nav-promise"><span class="status-dot"></span> Atención 24/7</span>
        <button class="menu-toggle" type="button" aria-label="Abrir menú" aria-expanded="false">☰</button>
        <nav id="main-nav">
            <a class="active" href="index.php">Inicio</a>
            <a href="conocenos.php">Conócenos</a>
            <a href="servicios.php">Servicios</a>
            <a href="cotizar.php">Cotizar</a>
            <a href="flota.php">Flota</a>
            <a class="nav-login" href="#acceso-personal">Iniciar sesión</a>
        </nav>
    </header>

    <main>
        <!-- Hero: el inicio tiene una sola misión: impactar y llevar al cliente a contactar. -->
        <section class="hero hero-home hero-premium">
            <div class="hero-particles" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i></div>
            <!-- La marca de fondo ocupa el espacio sin competir con la frase principal. -->
            <div class="hero-brand-backdrop" aria-hidden="true"><img src="../assets/img-gruas365-hq.png" alt=""></div>
            <div class="hero-content">
                <p class="eyebrow"><span class="status-dot"></span> GRÚAS 24 HORAS · RESPUESTA RÁPIDA</p>
                <h1 class="hero-title">Cuando el camino<br><span>se detiene, nosotros llegamos.</span></h1>
                <p class="hero-lead">Asistencia y remolque vehicular confiable, con atención profesional cuando más la
                    necesitas.</p>
                <div class="hero-actions"><a class="btn btn-primary btn-glow" href="cotizar.php">Cotizar mi servicio
                        <span>→</span></a><a class="btn btn-outline btn-call" href="tel:<?= e($phone) ?>">☎ Llamar
                        ahora</a></div>
                <div class="hero-trust"><span>★ ★ ★ ★ ★</span> <strong>Servicio confiable</strong><i>·</i> Más de 6 años
                    de experiencia</div>
            </div>
            <!-- Tarjetas de confianza: sustituyen el espacio vacío por información útil y visual. -->
            <div class="hero-benefits" aria-label="Beneficios de Grúas 365">
                <article><span>◷</span>
                    <h2></h2><div><strong>Atención 24/7</strong><small>Siempre listos para ayudarte</small></div></h2>
                </article>
                <article><span>↗</span>
                    <div><strong>Respuesta rápida</strong><small>Coordinación sin vueltas</small></div>
                </article>
                <article><span>◇</span>
                    <div><strong>Servicio seguro</strong><small>Personal capacitado</small></div>
                </article>
                <article><span>⌖</span>
                    <div><strong>Cobertura urbana</strong><small>Vamos hasta donde estás</small></div>
                </article>
            </div>
            <div class="scroll-cue">DESLIZA PARA CONOCERNOS <span>↓</span></div>
        </section>

        <!-- Banda de accesos: aprovecha el espacio bajo el hero sin cargar la página de información. -->
        <section class="home-links section">
            <a href="conocenos.php"><span>01</span><strong>Conócenos</strong><small>La experiencia detrás del servicio
                    →</small></a>
            <a href="servicios.php"><span>02</span><strong>Servicios</strong><small>Soluciones para cada emergencia
                    →</small></a>
            <a href="flota.php"><span>03</span><strong>Nuestra flota</strong><small>La grúa adecuada para cada misión
                    →</small></a>
        </section>
        <section class="home-cta">
            <p class="eyebrow">¿Necesitas una grúa?</p>
            <h2>Estamos listos para ayudarte.</h2><a class="btn btn-primary" href="cotizar.php">Empezar cotización
                <span>→</span></a>
        </section>
    </main>

    <!-- Footer global: puedes reemplazar los textos de contacto por los datos definitivos. -->
    <footer class="site-footer">
        <div>
            <div class="brand-copy"><strong>GRÚAS</strong><b>365</b><small>ASISTENCIA VIAL</small></div>
            <p>Asistencia vehicular confiable, todos los días.</p>
        </div>
        <div>
            <h3>Explora</h3><a href="index.php">Inicio</a><a href="conocenos.php">Conócenos</a><a
                href="servicios.php">Servicios</a><a href="flota.php">Nuestra flota</a>
        </div>
        <div>
            <h3>Atención</h3>
            <p>Disponible 24 horas, 7 días a la semana.</p><a href="tel:<?= e($phone) ?>">☎ <?= e($phone) ?></a><a
                href="wa.me/message/HQLZ6WGWG73VH1<?= e($whatsappNumber) ?>" target="_blank" rel="noopener">WhatsApp directo</a>
        </div>
        <div>
            <h3>Encuéntranos</h3>
            <p>Bogotá y alrededores.<br>Respuesta rápida y seguimiento personalizado.</p><a href="cotizar.php">Solicitar
                cotización →</a>
        </div>
        <div class="footer-bottom">
            <p>© <?= date('Y') ?> Grúas 365. Todos los derechos reservados.</p>
            <p>Diseñado para moverte con tranquilidad.</p>
        </div>
    </footer>
    <a class="whatsapp-float" href="https://wa.me/message/HQLZ6WGWG73VH1"<?= e($whatsappNumber) ?>?text=<?= e($whatsappMessage) ?>"
        target="_blank" rel="noopener" aria-label="Escribir por WhatsApp">⌕<span>¿Necesitas ayuda?</span></a>

    <!-- Modal: el acceso sigue siendo discreto y profesional para el personal interno. -->
    <div class="modal" id="acceso-personal" role="dialog" aria-modal="true" aria-labelledby="login-title">
        <div class="modal-card"><a class="modal-close" href="index.php" aria-label="Cerrar">×</a>
            <p class="eyebrow">Inicio de sesión</p>
            <h2 id="login-title">Iniciar sesión</h2>
            <p>Ingresa para gestionar servicios, agenda y operaciones.</p>
            <form method="post" action="login.php"><?= csrf_field() ?><label>Usuario o correo<input name="identifier"
                        required autocomplete="username"></label><label>Contraseña<input type="password" name="password"
                        required autocomplete="current-password"></label><button class="btn btn-primary"
                    type="submit">Iniciar sesión</button></form>
        </div>
    </div>

    <!-- Menú móvil y microinteracción de inclinación del logo al mover el cursor. -->
    <script>
        const menu = document.querySelector('.menu-toggle'), nav = document.querySelector('#main-nav');
        menu?.addEventListener('click', () => { const open = nav.classList.toggle('is-open'); menu.setAttribute('aria-expanded', open) });
        nav?.querySelectorAll('a').forEach(link => link.addEventListener('click', () => nav.classList.remove('is-open')));
        const stage = document.querySelector('.hero-brand-backdrop');
        stage?.addEventListener('mousemove', (event) => { const box = stage.getBoundingClientRect(); const x = (event.clientX - box.left) / box.width - .5; const y = (event.clientY - box.top) / box.height - .5; stage.style.setProperty('--tilt-x', `${y * -8}deg`); stage.style.setProperty('--tilt-y', `${x * 8}deg`) });
        stage?.addEventListener('mouseleave', () => { stage.style.setProperty('--tilt-x', '0deg'); stage.style.setProperty('--tilt-y', '0deg') });
    </script>
</body>

</html>