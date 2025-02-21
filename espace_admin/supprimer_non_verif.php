<?php 
session_start();
try {
    $pdo = new PDO('mysql:host=localhost;dbname=projet2', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("<div class='alert alert-danger'>Erreur de connexion à la base de données : " . $e->getMessage() . "</div>");
}
$stmt = $pdo->prepare("SELECT * FROM chef_filiere WHERE id= ?");
$stmt->execute([$_SESSION['id']]);
$chef = $stmt->fetch(PDO::FETCH_ASSOC);

$query = "
DELETE FROM filiere
WHERE verified = 0 AND nom_fil = ?
";

// Exécuter la requête en passant un tableau
$stmt = $pdo->prepare($query);
$stmt->execute([$chef['filiere']]); // Remarquez les crochets autour de $chef['filiere']

 echo "Les étudiants avec une candidature non vérifiée ont été supprimés.";
 header('Location: home.php');
 exit();
?>