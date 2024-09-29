<?php

use Symfony\Component\Dotenv\Dotenv;

// Chargement de l'autoloader de Composer
require dirname(__DIR__).'/vendor/autoload.php';

// Vérification de la méthode bootEnv pour initialiser les variables d'environnement
if (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}


if ($_SERVER['APP_DEBUG']) {
    umask(0000);
}
