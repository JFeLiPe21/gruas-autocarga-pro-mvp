<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    // Guardamos las sesiones en una carpeta propia del proyecto para evitar
    // errores de permisos de C:\xampp\tmp en instalaciones locales.
    $sessionPath = __DIR__ . '/../storage/sessions';
    if (!is_dir($sessionPath)) {
        @mkdir($sessionPath, 0775, true);
    }
    session_save_path($sessionPath);
    session_set_cookie_params([
        'httponly' => true,
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'samesite' => 'Lax',
    ]);
    session_start();
}

if (!empty($_SESSION['user_id']) && !empty($_SESSION['last_activity'])) {
    $timeout = 30 * 60;
    if (time() - (int)$_SESSION['last_activity'] > $timeout) {
        $_SESSION = [];
        session_destroy();
        session_start();
    } else {
        $_SESSION['last_activity'] = time();
    }
}

function e(string|int|float|null $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function db(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;

    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
        DB_HOST, DB_PORT, DB_NAME
    );

    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $pdo;
}

function redirect(string $path): never {
    header('Location: ' . $path);
    exit;
}

function require_login(): void {
    if (empty($_SESSION['user_id'])) redirect('login.php');
}

// Autorización reutilizable para proteger módulos internos por rol.
function require_role(string ...$roles): void {
    $user = current_user();
    if (!in_array($user['role'], $roles, true)) {
        http_response_code(403);
        exit('No tienes permisos para acceder a este módulo.');
    }
}

function current_user(): array {
    require_login();
    $stmt = db()->prepare('SELECT id, nombre, rol, estado FROM usuarios WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => (int)$_SESSION['user_id']]);
    $record = $stmt->fetch();
    if (!$record || $record['estado'] !== 'activo') {
        $_SESSION = [];
        session_destroy();
        redirect('login.php');
    }
    $_SESSION['name'] = $record['nombre'];
    $_SESSION['role'] = $record['rol'];
    return [
        'id' => (int)$record['id'],
        'name' => (string)$record['nombre'],
        'role' => (string)$record['rol'],
    ];
}

function csrf_token(): string {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string {
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function verify_csrf_or_fail(): void {
    $sent = (string)($_POST['csrf'] ?? '');
    if (!hash_equals((string)($_SESSION['csrf'] ?? ''), $sent)) {
        http_response_code(419);
        exit('Solicitud no válida.');
    }
}

function flash(string $key, ?string $value = null): ?string {
    if ($value !== null) {
        $_SESSION['_flash'][$key] = $value;
        return null;
    }
    $message = $_SESSION['_flash'][$key] ?? null;
    unset($_SESSION['_flash'][$key]);
    return $message;
}

// Inyecta la navegación común en todas las vistas privadas sin duplicar
// cabeceras PHP en cada módulo. El script solo actúa sobre páginas workspace.
ob_start(static function (string $html): string {
    if ((str_contains($html, 'workspace-') || str_contains($html, 'logout.php')) && str_contains($html, '</body>') && !str_contains($html, 'workspace-nav.js')) {
        return str_replace('</body>', '<script src="../assets/js/workspace-nav.js?v=20261003"></script></body>', $html);
    }
    return $html;
});
