<?php
class HomeController
{
    public function index(): void
    {
        view('home', 'index', ['judul' => 'Beranda']);
    }

    public function dashboard(): void
    {
        $user = require_login();
        view('home', 'dashboard', ['judul' => 'Dashboard', 'user' => $user]);
    }
}
