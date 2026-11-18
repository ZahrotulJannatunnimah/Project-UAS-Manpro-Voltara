<?php
class Router
{
    // Format rute: ?r=modul/aksi  (contoh: ?r=auth/verify-otp)
    public static function dispatch(string $route): void
    {
        $parts  = explode('/', trim($route, '/'));
        $module = $parts[0] ?: 'home';
        $action = $parts[1] ?? 'index';

        if (!preg_match('/^[a-z0-9_]+$/', $module) || !preg_match('/^[a-z0-9-]+$/', $action)) {
            self::notFound();
            return;
        }

        $class = ucfirst($module) . 'Controller';
        $file  = BASE_PATH . "/modules/$module/controllers/$class.php";
        if (!is_file($file)) {
            self::notFound();
            return;
        }
        require_once $file;

        $method = lcfirst(str_replace(' ', '', ucwords(str_replace('-', ' ', $action))));
        if (!class_exists($class) || !method_exists($class, $method)
            || !(new ReflectionMethod($class, $method))->isPublic()) {
            self::notFound();
            return;
        }

        (new $class())->$method();
    }

    public static function notFound(): void
    {
        http_response_code(404);
        view('home', '404', ['judul' => 'Halaman tidak ditemukan']);
    }
}
