<?php
session_start(); // Assurez-vous de démarrer la session

// Connexion à la base de données
try {
    $pdo = new PDO('mysql:host=localhost;dbname=projet2', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Vérification si l'utilisateur est connecté et que les informations sont dans la session
if (!isset($_SESSION['student_id'])) {
    die("Erreur : aucun étudiant sélectionné. Veuillez vous reconnecter.");
}

// Récupération des informations depuis la session
$id_etudiant = $_SESSION['student_id'];
$student_name = $_SESSION['student_name'];

// Vérification si le mode est édition
$edit_mode = isset($_GET['edit']);

// Récupérer les données de l'étudiant avec le token
$stmt = $pdo->prepare("SELECT * FROM student WHERE id = ?");
$stmt->execute([$id_etudiant]);
$etudiant = $stmt->fetch(PDO::FETCH_ASSOC); // Vérifier si l'étudiant existe

if (!$etudiant) {
    die("Étudiant introuvable !");
}

// Récupérer les informations de la candidature de l'étudiant
$stmt = $pdo->prepare("SELECT * FROM candidature WHERE ID_etud = ?");
$stmt->execute([$id_etudiant]);
$candidature = $stmt->fetch(PDO::FETCH_ASSOC); // Vérifier si la candidature existe

if (!$candidature) {
    die("Candidature introuvable !");
}

// Récupérer toutes les filières de l'étudiant
$stmt = $pdo->prepare("SELECT * FROM filiere WHERE student_id = ?");
$stmt->execute([$id_etudiant]);
$filieres = $stmt->fetchAll(PDO::FETCH_ASSOC); // Récupérer toutes les filières

if (!$filieres) {
    die("Aucune filière trouvée !");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Informations étudiant</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #007BFF;
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
            display: block;
            margin-top: 15px;
        }
        input[type="text"],
        input[type="email"],
        input[type="date"],
        input[type="number"],
        select {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }
        input[type="radio"] {
            margin-right: 10px;
        }
        input[type="file"] {
            margin-top: 8px;
        }
        .btn {
    display: inline-block;
    padding: 10px 20px;
    background-color: #007BFF; /* Couleur de fond */
    color: white; /* Texte blanc */
    text-decoration: none; /* Supprimer le soulignement */
    border-radius: 5px; /* Coins arrondis */
    cursor: pointer; /* Curseur en forme de main */
    margin-top: 10px;
    text-align: center;
    }

    .btn-primary {
        background-color: #007BFF;
    }

    .btn-success {
        background-color: #28a745;
    }
        .file-label {
            margin-top: 15px;
            font-weight: bold;
        }
        .info-group {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Informations de l'étudiant</h2>
        <form method="POST" action="update_student.php" enctype="multipart/form-data">
            <div class="info-group">
                <label>Nom :</label>
                <input type="text" name="nom" value="<?= htmlspecialchars($etudiant['nom']) ?>" <?= $edit_mode ? '' : 'readonly' ?>>
            </div>

            <div class="info-group">
                <label>Prénom :</label>
                <input type="text" name="prenom" value="<?= htmlspecialchars($etudiant['prenom']) ?>" <?= $edit_mode ? '' : 'readonly' ?>>
            </div>

            <div class="info-group">
                <label>Email :</label>
                <input type="email" name="email" value="<?= htmlspecialchars($etudiant['email']) ?>" readonly>
            </div>

            <div class="info-group">
                <label>Date de Naissance :</label>
                <input type="date" name="birthday" value="<?= htmlspecialchars($etudiant['birthday']) ?>" <?= $edit_mode ? '' : 'readonly' ?>>
            </div>

            <div class="info-group">
                <label>CNE :</label>
                <input type="text" name="cne" value="<?= htmlspecialchars($candidature['CNE']) ?>" <?= $edit_mode ? '' : 'readonly' ?>>
            </div>

            <div class="info-group">
                <label>CIN :</label>
                <input type="text" name="cin" value="<?= htmlspecialchars($candidature['CIN']) ?>" <?= $edit_mode ? '' : 'readonly' ?>>
            </div>

            <div class="info-group">
                <label>Ville :</label>
                <input type="text" name="ville" value="<?= htmlspecialchars($candidature['ville']) ?>" <?= $edit_mode ? '' : 'readonly' ?>>
            </div>

            <div class="info-group">
                <label>Filières :</label>
                <?php foreach ($filieres as $filiere): ?>
                    <input type="text" name="filieres[]" value="<?= htmlspecialchars($filiere['nom_fil']) ?>" <?= $edit_mode ? '' : 'readonly' ?>>
                <?php endforeach; ?>
            </div>
          
            <div class="buttons">
        <a href="telech_pdf.php?token=<?php echo $etudiant['verification_token']; ?>" class="btn btn-primary">Télécharger le PDF</a>
        <a href="send_pdf_email.php?token=<?php echo $etudiant['verification_token']; ?>" class="btn btn-success">Envoyer par Email</a>
    </div>
        
        </form>
    </div>
</body>
</html>
