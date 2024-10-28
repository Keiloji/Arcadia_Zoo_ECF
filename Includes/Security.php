<?php

// Fonction pour filtrer les entrées utilisateur
function filter_input_user($data) {
    $data = trim($data); // Supprime les espaces en début et fin de chaîne
    $data = stripslashes($data); // Supprime les antislashs
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8'); // Convertit les caractères spéciaux en entités HTML
    return $data;
}

// Fonction pour prévenir les injections de commande
function validate_command($command) {
    // Vérifie que la commande contient uniquement des caractères alphanumériques et des espaces
    return preg_match('/^[a-zA-Z0-9\s]+$/', $command);
}

// Fonction pour sécuriser l'inclusion de fichiers
function secure_file_inclusion($page) {
    $allowed_pages = ['about.php', 'contact.php', 'home.php']; // Liste blanche des fichiers autorisés
    if (in_array($page, $allowed_pages)) {
        include $page;
    } else {
        include '404.php'; // Redirection vers une page 404 si la page n'est pas autorisée
    }
}

// Connexion sécurisée à la base de données avec PDO
function get_db_connection() {
    $dsn = "mysql:host=localhost;dbname=mydb;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Active les exceptions pour faciliter le débogage
    ];

    try {
        return new PDO($dsn, "username", "password", $options);
    } catch (PDOException $e) {
        die("Erreur de connexion à la base de données : " . $e->getMessage());
    }
}

// Fonction pour effectuer des requêtes SQL préparées (prévention des injections SQL)
function execute_secure_query($pdo, $query, $params) {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fonction pour exécuter des commandes shell de manière sécurisée
function execute_command($command) {
    if (validate_command($command)) {
        return shell_exec(escapeshellcmd($command)); // Utilisation d'escapeshellcmd pour sécuriser l'exécution des commandes
    } else {
        return "Commande invalide.";
    }
}

?>
