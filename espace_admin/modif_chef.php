<?php include 'header.php'; ?>

<?php 

try {
    // Connexion à la base de données avec PDO
    $pdo = new PDO('mysql:host=localhost;dbname=projet2', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // En cas d'erreur de connexion
    die("<div class='alert alert-danger'>Erreur de connexion à la base de données : " . htmlspecialchars($e->getMessage()) . "</div>");
}

// Vérifier si l'ID est passé dans l'URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $chef_id = $_GET['id'];

    // Récupérer les informations du chef à partir de la base de données
    $stmt = $pdo->prepare("SELECT * FROM chef_filiere WHERE id = ?");
    $stmt->execute([$chef_id]);
    $chef = $stmt->fetch(PDO::FETCH_ASSOC);

    // Si aucune donnée n'est trouvée
    if (!$chef) {
        die("<div class='alert alert-warning'>Chef introuvable.</div>");
    }
} else {
    die("<div class='alert alert-warning'>Aucun ID valide fourni.</div>");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Chef</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .form-container {
    margin: auto;
    margin-top: 5%;
    padding: 30px;
    max-width: 600px; /* Augmenter la largeur maximale */
    width: 90%; /* Largeur adaptative pour les petits écrans */
    background: #fff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    border-radius: 8px;
}
h1 {
    text-align: center; /* Centrer horizontalement */
    margin-bottom: 20px; /* Ajouter de l'espace en dessous */
    font-size: 28px; /* Ajuster la taille si nécessaire */
    color: #343a40; /* Couleur optionnelle pour un aspect professionnel */
}
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <form action="" method="POST">
                <h1>Modifier </h1>
                <!-- Nom -->
                <div class="mb-3">
                    <label for="nom" class="form-label">Nom :</label>
                    <input type="text" id="nom" name="nom" class="form-control" value="<?php echo htmlspecialchars($chef['nom_chef']); ?>" required>
                </div>
                <!-- Prénom -->
                <div class="mb-3">
                    <label for="prenom" class="form-label">Prénom :</label>
                    <input type="text" id="prenom" name="prenom" class="form-control" value="<?php echo htmlspecialchars($chef['prenom_chef']); ?>" required>
                </div>
                <!-- Code -->
                <div class="mb-3">
                    <label for="code" class="form-label">Code :</label>
                    <input type="text" id="code" name="code" class="form-control" value="<?php echo htmlspecialchars($chef['code_verif']); ?>" required>
                </div>
                <!-- Password -->
                <div class="mb-3">
                    <label for="password" class="form-label">Mot de passe :</label>
                    <input type="password" id="password" name="password" class="form-control" value="<?php echo htmlspecialchars($chef['password']); ?>" required>
                </div>
                <!-- Filière -->
                <div class="mb-3">
                    <label for="nom_fil" class="form-label">Filière :</label>
                    <input type="text" id="nom_fil" name="nom_fil" class="form-control" value="<?php echo htmlspecialchars($chef['filiere']); ?>" required>
                </div>
                <!-- Bouton Enregistrer -->
                <div class="d-grid">
                    <button type="submit" name="enregistrer" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Vérifier si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $code = $_POST['code'];
    $password = $_POST['password'];
    $filiere = $_POST['nom_fil'];

    // Mettre à jour les informations du chef
    $sql = "UPDATE chef_filiere 
            SET nom_chef = :nom, prenom_chef = :prenom, code_verif = :code, password = :password, filiere = :filiere 
            WHERE id = :id";
    $stmt = $pdo->prepare($sql);

    // Exécuter la requête
    $stmt->execute([
        ':nom' => $nom,
        ':prenom' => $prenom,
        ':code' => $code,
        ':password' => $password,
        ':filiere' => $filiere,
        ':id' => $chef_id
    ]);


    echo "<div class='alert alert-success'>Les informations ont été mises à jour avec succès.</div>";
    header('Location: gerer_chefs.php');
                exit();
}
?>
