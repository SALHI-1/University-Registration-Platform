
<?php 

session_start();
?>
<?php
if (isset($_SESSION['message'])) { 
    $message = $_SESSION['message']; 
    $alertType = $message['type'] === 'success' ? 'success' : 'error'; 
    echo "<div class='alert {$alertType}'>{$message['content']}</div>"; 
    
    // Supprimer le message après l'affichage 
    unset($_SESSION['message']); 
} 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        /* Style global pour les messages */
.alert {
    padding: 15px;
    margin: 10px 0;
    border-radius: 5px;
    font-size: 16px;
    text-align: center;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

/* Message de succès */
.alert.success {
    background-color: #28a745;
    color: white;
}

/* Message d'erreur */
.alert.error {
    background-color: #dc3545;
    color: white;
}

/* Animation d'apparition */
.alert {
    animation: fadeIn 1s ease-in-out;
}

/* Animation de disparition (si vous voulez qu'il disparaisse après quelques secondes) */
@keyframes fadeIn {
    0% { opacity: 0; }
    100% { opacity: 1; }
}

/* Optionnel : disparition automatique après 5 secondes */
.alert.success, .alert.error {
    animation: fadeOut 1s 4s forwards; /* Fait disparaître après 4 secondes */
}

@keyframes fadeOut {
    0% { opacity: 1; }
    100% { opacity: 0; }
}

        body {
            background-color: #f8f9fa; /* Universitaire et professionnel */
            font-family: Arial, sans-serif;
        }

        .form-container {
            margin: auto;
            margin-top: 5%;
            padding: 30px;
            max-width: 400px;
            background: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 8px;
        }

        .form-container h1 {
            font-size: 24px;
            color: #343a40;
            text-align: center;
            margin-bottom: 20px;
        }

        .form-container .form-label {
            font-weight: bold;
        }

        .form-container a {
            color: #007bff;
            text-decoration: none;
        }

        .form-container a:hover {
            text-decoration: underline;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <form action="login.php" method="POST">
                <h1>Login Page</h1>
                <!-- Email Field -->
                <div class="mb-3">
                    <label for="CODE" class="form-label">CODE:</label>
                    <input type="text" id="code" name="code" class="form-control" placeholder="Entrer le code du compte" required>
                </div>
                <!-- Password Field -->
                <div class="mb-3">
                    <label for="password" class="form-label">PASSWORD:</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Entrer le password" required>
                </div>
                <div class="mb-3">
                    <label for="nom_fil" class="form-label">Nom de votre filière:</label>
                    <input type="text" id="nom_fil" name="nom_fil" class="form-control" placeholder="Entrer le Nom de votre filière" required>
                </div>
                <!-- Submit Button -->
                <div class="d-grid">
                    <button type="submit"  name="login" class="btn btn-primary">Login</button>
                </div>
               
            </form>
        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=projet2', 'root', ''); // Connexion avec PDO
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Gestion des erreurs
    } catch (PDOException $e) {
        die("Erreur de connexion à la base de données : " . $e->getMessage());
    }

    // Récupération des données du formulaire
    $code = $_POST['code'];
    $password = $_POST['password'];
    $nom_fil = $_POST['nom_fil'];

    if ($code && $password && $nom_fil) {
        // Préparer la requête pour chercher un chef de filière correspondant
        $stmt = $pdo->prepare("SELECT * FROM chef_filiere WHERE code_verif = ? AND filiere = ?");
        $stmt->execute([$code, $nom_fil]);

        $chef = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($chef) {
            // Vérification des informations pour un admin
            if ($password === "54321" && $code === "54321" && $nom_fil === "admin") {
                // Si c'est un admin, on démarre la session et redirige vers home_admin.php
                $_SESSION['id'] = $chef['id'];
                $_SESSION['nom_chef'] = $chef['nom_chef'] . ' ' . $chef['prenom_chef'];
                header('Location:home_admin.php');
                exit();
            }

            // Vérification pour un chef de filière
            if ($password === $chef['password'] && $code === $chef['code_verif']) {
                // Si c'est un chef de filière, on démarre la session et redirige vers home.php
                $_SESSION['id'] = $chef['id'];
                $_SESSION['nom_chef'] = $chef['nom_chef'] . ' ' . $chef['prenom_chef'];
                header('Location: home.php');
                exit();
            } else {
                // Mot de passe incorrect
                $_SESSION['message'] = ['type' => 'error', 'content' => 'Password ou Code incorrect.'];
                header('Location: login.php');
                exit();
            }
        } else {
            // Aucun utilisateur trouvé
            $_SESSION['message'] = ['type' => 'error', 'content' => 'Admin ou Chef de filière non trouvé.'];
            header('Location: login.php');
            exit();
        }
    }
}
?>
