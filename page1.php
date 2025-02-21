<?php include('header.html'); ?>
<?php session_start(); // Démarrer la session
if (isset($_SESSION['message'])) { 
    $message = $_SESSION['message']; 
    $alertType = $message['type'] === 'success' ? 'success' : 'error'; 
    echo "<div class='alert {$alertType}'>{$message['content']}</div>"; 
    
    // Supprimer le message après l'affichage 
    unset($_SESSION['message']); 
} 
?>
<?php 

try {
    $pdo = new PDO('mysql:host=localhost;dbname=projet2', 'root', ''); // Connexion avec PDO
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Gestion des erreurs
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Vérification des informations de connexion
    if ($email && $password) {
        $stmt = $pdo->prepare("SELECT * FROM student WHERE email = ?");
        $stmt->execute([$email]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($student) {
            // Vérifier si le mot de passe est correct
            if ($password == $student['password']) {
                // Si les identifiants sont corrects, démarrer la session
                $_SESSION['student_id'] = $student['id'];
                $_SESSION['student_name'] = $student['prenom'] . ' ' . $student['nom'];
                
                // Rediriger vers le profil ou la page d'accueil
                header('Location: acceuil.php');
                exit();
            } else {
                // Mot de passe incorrect
                $_SESSION['message'] = ['type' => 'error', 'content' => 'Mot de passe incorrect.'];
                header('Location: page1.php');
                exit();
            }
        } else {
            // L'email n'existe pas
            $_SESSION['message'] = ['type' => 'error', 'content' => 'Email non trouvé.'];
            header('Location: page1.php');
            exit();
        }
    } else {
        // Formulaire incomplet
        $_SESSION['message'] = ['type' => 'error', 'content' => 'Veuillez remplir tous les champs.'];
        header('Location: page1.php');
        exit();
    }
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
            <form action="page1.php" method="POST">
                <h1>Login Page</h1>
                <!-- Email Field -->
                <div class="mb-3">
                    <label for="email" class="form-label">EMAIL:</label>
                    <input type="text" id="email" name="email" class="form-control" placeholder="Enter your email" required>
                </div>
                <!-- Password Field -->
                <div class="mb-3">
                    <label for="password" class="form-label">PASSWORD:</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
                </div>
                <!-- Submit Button -->
                <div class="d-grid">
                    <button type="submit"  name="login" class="btn btn-primary">Login</button>
                </div>
                <!-- Links -->
                <div class="text-center mt-3">
                    <a href="pwd.php">Forget Password?</a> or <a href="register1.php">Don't have an account yet?</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
