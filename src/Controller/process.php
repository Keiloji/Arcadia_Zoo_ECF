<?php
include 'includes/security.php'; // Inclure le fichier de sécurité

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Filtrer et valider l'entrée utilisateur
    $command = filter_input_user($_POST['command']);
    
    // Exécuter la commande de manière sécurisée
    $result = execute_command($command);
    
    // Afficher le résultat
    echo "Résultat de la commande : " . htmlspecialchars($result);
}
?>
