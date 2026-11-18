<?php
function app_config(string $key)
{
    static $c = null;
    if ($c === null) {
        $c = require BASE_PATH . '/config/app.php';
    }
    return $c[$key] ?? null;
}

function e($v): string
{
    return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
}

function url(string $route = ''): string
{
    return 'index.php' . ($route !== '' ? '?r=' . $route : '');
}

function redirect(string $route): void
{
    header('Location: ' . url($route));
    exit;
}

function is_post(): bool
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

function view(string $module, string $name, array $data = []): void
{
    extract($data, EXTR_SKIP);
    require BASE_PATH . '/core/layout/header.php';
    require BASE_PATH . "/modules/$module/views/$name.php";
    require BASE_PATH . '/core/layout/footer.php';
}

function flash_set(string $tipe, string $teks): void
{
    $_SESSION['flash'][] = ['tipe' => $tipe, 'teks' => $teks];
}

function flash_get(): array
{
    $f = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $f;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function csrf_verify(): void
{
    $ok = isset($_POST['csrf'], $_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $_POST['csrf']);
    if (!$ok) {
        http_response_code(419);
        exit('Sesi formulir tidak valid. Muat ulang halaman lalu coba lagi.');
    }
}

function auth_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function require_login(): array
{
    $u = auth_user();
    if (!$u) {
        redirect('auth/login');
    }
    return $u;
}

// 0812-3456-7890 / +62 812 3456 7890 / 812... -> 6281234567890
function normalize_phone(string $raw): ?string
{
    $d = preg_replace('/\D+/', '', $raw);
    if ($d === '') {
        return null;
    }
    if (str_starts_with($d, '0')) {
        $d = '62' . substr($d, 1);
    } elseif (str_starts_with($d, '8')) {
        $d = '62' . $d;
    }
    return preg_match('/^62\d{8,12}$/', $d) ? $d : null;
}
