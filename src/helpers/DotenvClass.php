<?php

namespace pumast3r\api\helpers;

use Dotenv\Dotenv;


class DotenvClass {
    static function loadDotenv() {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        return $dotenv->load();
    }
}