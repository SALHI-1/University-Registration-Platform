
<?php
// Connexion à la base de données
try {
    $pdo = new PDO('mysql:host=localhost;dbname=projet2', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Vérifier si un token est fourni
$message = "";
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Rechercher l'utilisateur avec ce token
    $stmt = $pdo->prepare("SELECT nom, prenom, email, birthday FROM student WHERE verification_token = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // L'utilisateur a été trouvé, mettre à jour l'attribut "verifie"
        $updateStmt = $pdo->prepare("UPDATE student SET verifie = 1 WHERE verification_token = ?");
        $updateStmt->execute([$token]);

        // Message de succès
        $message = "<div class='success'>
                        <h1>✅ Email Vérifié</h1>
                        <p>Bonjour <strong>" . htmlspecialchars($user['prenom']) . " " . htmlspecialchars($user['nom']) . "</strong>, votre email a été vérifié avec succès !</p>
                        <a href='continue_registration.php?token=" . htmlspecialchars($token) . "' class='btn'>Continuer l'enregistrement</a>
                    </div>";
    } else {
        // Message d'erreur pour un token invalide
        $message = "<div class='error'>
                        <h1>❌ Erreur</h1>
                        <p>Token invalide ou utilisateur introuvable.</p>
                    </div>";
    }
} else {
    // Message d'erreur pour un token manquant
    $message = "<div class='warning'>
                    <h1>⚠️ Attention</h1>
                    <p>Token manquant. Veuillez vérifier le lien fourni dans votre email.</p>
                </div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification</title>
    <!-- Lien CSS -->
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f9fafb;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
        }

        .container {
            max-width: 600px;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        p {
            font-size: 16px;
            margin-bottom: 15px;
            line-height: 1.5;
        }

        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 25px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            font-size: 16px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .success {
            color: #21618C;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 20px;
            border-radius: 10px;
        }

        .error {
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 20px;
            border-radius: 10px;
        }

        .warning {
            color: #856404;
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            padding: 20px;
            border-radius: 10px;
        }

        a {
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Affichage du message -->
        <?= $message ?>
    </div>
</body>
</html>
