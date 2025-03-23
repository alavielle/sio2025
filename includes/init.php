<?php

// fuseau horaire
date_default_timezone_set('Europe/Paris');

// langue locale - dépend du système d'exploitation
setlocale(LC_ALL, 'fr_FR.utf8', 'fra.utf8');

// Nom et ouverture de session
session_name('MYPROJECT'); // nom par defaut : PHPSESSID
session_start();

// Connexion BDD en localhost
$pdo = new PDO(
    'mysql:host=localhost; charset=utf8;dbname=sio',
    'root',
    '',
    array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING, // On se met en ERRMODE_SILENT en mode production
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    )
);



// Inclusion des fonctions du site
require_once('functions.php');

// Constantes du site
define('URL', '/sio2025/');

