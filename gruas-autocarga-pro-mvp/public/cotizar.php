<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/bootstrap.php';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cotizar | Grúas 365</title>
    <link rel="stylesheet" href="../assets/css/app.css?v=20261003">
</head>
<body>
<header class="topbar topbar-premium">
    <a class="brand brand-logo" href="index.php" aria-label="Grúas 365, ir al inicio"><span class="brand-mark"><img src="../assets/img-gruas365-hq.png" alt="Logo Grúas 365"></span><span class="brand-copy"><strong>GRÚAS</strong><b>365</b><small>ASISTENCIA VIAL</small></span></a>
    <span class="nav-promise"><span class="status-dot"></span> Atención 24/7</span>
    <button class="menu-toggle" type="button" aria-label="Abrir menú" aria-expanded="false">☰</button>
    <nav id="main-nav"><a href="index.php">Inicio</a><a href="conocenos.php">Conócenos</a><a href="servicios.php">Servicios</a><a class="active" href="cotizar.php">Cotizar</a><a href="flota.php">Flota</a><a class="nav-login" href="login.php">Iniciar sesión</a></nav>
</header>

<main class="quote-page">
    <section class="quote-box">
        <p class="eyebrow">Cotización</p>
        <h1>Calcula una tarifa estimada</h1>
        <p>En esta primera versión dejamos el mapa y el cálculo por distancia preparados para la siguiente fase.</p>

        <!-- Ruta relativa: funciona también cuando el proyecto vive dentro de XAMPP/htdocs. -->
        <form action="cotizar.php" method="post" id="quote-form">
            <label>Origen
                <input name="origin" required placeholder="Ej. Calle 80 con Av. Boyacá">
            </label>
            <label>Destino
                <input name="destination" required placeholder="Ej. Portal de Suba">
            </label>
            <label>Tipo de vehículo
                <select name="vehicle_type" required>
                    <option value="moto">Moto</option>
                    <option value="carro">Carro</option>
                    <option value="camioneta">Camioneta</option>
                </select>
            </label>
            <label>Modelo / año (opcional)
                <input name="vehicle_model" placeholder="Ej. Kia Picanto 2020">
            </label>
            <button class="btn" type="submit">Continuar</button>
        </form>
    </section>

    <section class="map-placeholder">
        <div>
            <span>MAPA</span>
            <p>Aquí conectaremos el proveedor de mapas/rutas en la siguiente fase.</p>
        </div>
    </section>
</main>
<footer class="site-footer"><div><div class="brand-copy"><strong>GRÚAS</strong><b>365</b><small>ASISTENCIA VIAL</small></div><p>Asistencia vehicular confiable, todos los días.</p></div><div><h3>Explora</h3><a href="index.php">Inicio</a><a href="conocenos.php">Conócenos</a><a href="servicios.php">Servicios</a><a href="flota.php">Flota</a></div><div><h3>Atención</h3><p>Disponible 24 horas, 7 días a la semana.</p><a href="tel:+573001234567">☎ +57 322 4989899</a></div><div><h3>Acción rápida</h3><a href="login.php">Iniciar sesión</a></div><div class="footer-bottom"><p>© <?= date('Y') ?> Grúas 365.</p><p>Diseñado para moverte con tranquilidad.</p></div></footer>
<script>const menu=document.querySelector('.menu-toggle'),nav=document.querySelector('#main-nav');menu?.addEventListener('click',()=>{const open=nav.classList.toggle('is-open');menu.setAttribute('aria-expanded',open)});</script>
</body>
</html>
