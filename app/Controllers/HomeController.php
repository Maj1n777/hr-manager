<?php

require_once __DIR__ . '/../Models/AppInfo.php';

class HomeController
{
    public function index(): void
    {
        $model = new AppInfo();
        $data = $model->getInfo();

        require __DIR__ . '/../Views/home.php';
    }
}