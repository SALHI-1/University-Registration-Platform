<?php
// Démarrer la session
session_start();

// Vérifier si une session existe
if (isset($_SESSION)) {
    // Supprimer toutes les variables de session
    session_unset();

    // Détruire la session
    session_destroy();

    // Rediriger l'utilisateur vers la page de connexion ou d'accueil
    header("Location: login.php");
    exit();
} else {

    // Si aucune session n'existe, rediriger directement
    header("Location: login.php");
    exit();
}
?>
