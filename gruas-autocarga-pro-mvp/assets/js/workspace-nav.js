/* Navegación común del área privada: funciona en escritorio y móvil. */
(function () {
    const header = document.querySelector('.topbar-premium');
    const nav = header?.querySelector('nav');
    if (!header || !nav) return;

    const isAdmin = document.body.classList.contains('workspace-admin') || document.body.textContent.includes('Administrador');
    const links = [
        ['dashboard.php', 'Panel'],
        ['servicios_admin.php', isAdmin ? 'Servicios' : 'Mis servicios'],
        ['gastos.php', isAdmin ? 'Gastos' : 'Mis gastos'],
        ['calendario.php', isAdmin ? 'Calendario' : 'Mi calendario']
    ];
    if (isAdmin) links.push(['reportes.php', 'Reportes']);
    links.push(['perfil.php', 'Mi perfil'], ['logout.php', 'Cerrar sesión']);

    // Reemplazamos los enlaces de cada pantalla por una navegación uniforme.
    nav.id = 'workspace-nav';
    nav.innerHTML = links.map(([href, label]) => `<a href="${href}">${label}</a>`).join('');

    let toggle = header.querySelector('.menu-toggle');
    if (!toggle) {
        toggle = document.createElement('button');
        toggle.className = 'menu-toggle';
        toggle.type = 'button';
        toggle.setAttribute('aria-label', 'Abrir menú');
        toggle.setAttribute('aria-expanded', 'false');
        toggle.textContent = '☰';
        header.querySelector('.brand-logo')?.after(toggle);
    }

    const close = () => {
        nav.classList.remove('is-open');
        toggle.setAttribute('aria-expanded', 'false');
    };
    toggle.addEventListener('click', event => {
        event.stopPropagation();
        const open = nav.classList.toggle('is-open');
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
    nav.querySelectorAll('a').forEach(link => link.addEventListener('click', close));
    document.addEventListener('click', event => {
        if (!header.contains(event.target)) close();
    });
})();
