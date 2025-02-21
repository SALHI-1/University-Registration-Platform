<?php 
session_start();

try {
    // Connexion à la base de données avec PDO
    $pdo = new PDO('mysql:host=localhost;dbname=projet2', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // En cas d'erreur de connexion
    die("<div class='alert alert-danger'>Erreur de connexion à la base de données : " . htmlspecialchars($e->getMessage()) . "</div>");
}

// Vérifier si le paramètre 'delet' existe dans l'URL
if (isset($_GET['delet'])) {
    $chef_id = $_GET['delet'];

    // Validation de l'ID pour s'assurer qu'il est numérique
    if (is_numeric($chef_id)) {
        try {
            // Préparer la requête SQL pour supprimer l'entrée
            $stmt = $pdo->prepare("DELETE FROM chef_filiere WHERE id = ?");
            $stmt->execute([$chef_id]);

            // Rediriger vers la page 'home.php' après la suppression
            header('Location: gerer_chefs.php');
            exit();
        } catch (PDOException $e) {
            echo "<div class='alert alert-danger'>Erreur lors de la suppression : " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    } else {
        echo "<div class='alert alert-warning'>ID invalide fourni.</div>";
    }
} else {
    echo "<div class='alert alert-warning'>Aucun ID de chef fourni.</div>";
}
?>
