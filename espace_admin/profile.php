<?php 
try {
    // Connexion à la base de données avec PDO
    $pdo = new PDO('mysql:host=localhost;dbname=projet2', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // En cas d'erreur de connexion
    die("<div class='alert alert-danger'>Erreur de connexion à la base de données : " . htmlspecialchars($e->getMessage()) . "</div>");
}

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
            SET nom_chef = :nom, prenom_chef = :prenom, code_verif = :code, password = :password 
            WHERE role ='admin'";
    $stmt = $pdo->prepare($sql);

    // Exécuter la requête
    $stmt->execute([
        ':nom' => $nom,
        ':prenom' => $prenom,
        ':code' => $code,
        ':password' => $password,
    ]);

    // Rediriger vers la page d'accueil après la mise à jour
    header('Location: profile.php');
    exit();
}

// Récupérer les informations du chef à partir de la base de données
$stmt = $pdo->prepare("SELECT * FROM chef_filiere WHERE role = 'admin' ");
$stmt->execute();
$chef = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Chef</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Arial', sans-serif;
            color: #343a40;
        }
        .form-container {
            margin: auto;
            margin-top: 5%;
            padding: 30px;
            max-width: 600px; /* Augmenter la largeur maximale */
            width: 90%; /* Largeur adaptative pour les petits écrans */
            background: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        h1 {
            text-align: center;
            margin: 30px;
            font-size: 30px;
            color: #007bff;
            font-weight: 600;
        }
        h2 {
            text-align: center;
            color: #6c757d;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .form-label {
            font-weight: 500;
        }
        .form-control {
            border-radius: 8px;
            box-shadow: none;
            border: 1px solid #ced4da;
            transition: all 0.3s ease-in-out;
        }
        .form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.25rem rgba(38, 143, 255, 0.25);
        }
        .btn-primary, .btn-back {
            border-radius: 8px;
            padding: 12px 20px;
            font-size: 16px;
            transition: all 0.3s ease-in-out;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .btn-back {
            background-color: #28a745;
            border: none;
            color: white;
        }
        .btn-back:hover {
            background-color: #218838;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            font-size: 16px;
        }
    </style>
</head>
<body>

<h1>Bienvenue, <?php echo $chef['nom_chef'] .' '.$chef['prenom_chef']; ?></h1>

<div class="container">
    <div class="form-container">
        <form action="profile.php" method="POST">
            <h2>Profil </h2>
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
                <input type="text" readonly id="nom_fil" name="nom_fil" class="form-control" value="<?php echo htmlspecialchars($chef['filiere']); ?>" required>
            </div>
            <!-- Boutons -->
            <div class="d-grid gap-2">
                <button type="submit" name="enregistrer" class="btn btn-primary">Enregistrer</button>
                <a href="home_admin.php" class="btn btn-back">Retour au Home</a>
            </div>
        </form>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
