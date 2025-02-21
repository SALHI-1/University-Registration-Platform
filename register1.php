
<?php
session_start(); // Démarrer la session
include('header.html');

// Inclure PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Connexion à la base de données
try {
    $pdo = new PDO('mysql:host=localhost;dbname=projet2', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST["submit"])) {
        $nom = htmlspecialchars($_POST['nom']);
        $prenom = htmlspecialchars($_POST['prenom']);
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $birthday = $_POST['birthday'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // Validation des mots de passe
        if ($password != $confirm_password) {
            $_SESSION['message'] = ['type' => 'danger', 'text' => "Les mots de passe ne correspondent pas."];
        } else {
            // Vérification si l'email existe déjà
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM student WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetchColumn() > 0) {
                $_SESSION['message'] = ['type' => 'danger', 'text' => "Cet email est déjà enregistré."];
            } else {

                // Génération du token de vérification
                $verification_token = bin2hex(random_bytes(16));

                // Insertion des données dans la base de données
                try {
                    $stmt = $pdo->prepare("INSERT INTO student (nom, prenom, email, birthday, password, verification_token) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$nom, $prenom, $email, $birthday, $password, $verification_token]);

                    // Envoi de l'email de vérification
                    try {
                        $mail = new PHPMailer(true);
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com';
                        $mail->SMTPAuth = true;
                        $mail->Username = 'mohammedsalhisam@gmail.com';
                        $mail->Password = 'pyds wzgx xvgx dkiu';
                        $mail->SMTPSecure = 'ssl';
                        $mail->Port = 465;

                        $mail->setFrom('mohammedsalhisam@gmail.com', 'Université');
                        $mail->addAddress($email);

                        $verification_link = "http://localhost/formular2/verification.php?token=$verification_token";
                        $mail->isHTML(true);
                        $mail->Subject = 'Vérification de votre inscription';
                        $mail->Body = "
                            <h2>Bonjour $prenom $nom,</h2>
                            <p>Merci pour votre inscription. Veuillez cliquer sur le lien ci-dessous pour vérifier votre email :</p>
                            <a href='$verification_link'>Confirmer mon email</a>
                        ";

                        $mail->send();
                        $_SESSION['message'] = ['type' => 'success', 'text' => "Un email de vérification a été envoyé. Veuillez vérifier votre boîte de réception."];
                    } catch (Exception $e) {
                        $_SESSION['message'] = ['type' => 'danger', 'text' => "Erreur lors de l'envoi de l'email : {$mail->ErrorInfo}"];
                    }
                } catch (PDOException $e) {
                    $_SESSION['message'] = ['type' => 'danger', 'text' => "Erreur lors de l'insertion dans la base de données : " . $e->getMessage()];
                }
            }
        }
        // Redirection pour éviter la resoumission du formulaire
        header('Location: register1.php');
        exit();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white text-center">
                    <h3>Register</h3>
                </div>
                <div class="card-body">
                    <form action="register1.php" method="POST">
                             <!-- Affichage du message -->
                    <?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-<?= $_SESSION['message']['type'] ?> mt-3 shadow-sm alert-dismissible fade show" role="alert" style="border-radius: 8px;">
        <div class="d-flex align-items-center">
            <div>
                <?php if ($_SESSION['message']['type'] === 'success'): ?>
                    <i class="bi bi-check-circle-fill text-success me-2" style="font-size: 1.5rem;"></i>
                <?php elseif ($_SESSION['message']['type'] === 'danger'): ?>
                    <i class="bi bi-exclamation-circle-fill text-danger me-2" style="font-size: 1.5rem;"></i>
                <?php endif; ?>
            </div>
            <div>
                <?= htmlspecialchars($_SESSION['message']['text']) ?>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['message']); // Supprimer le message après affichage ?>
<?php endif; ?>
                        <div class="mb-3">
                            <label for="prenom" class="form-label">First Name:</label>
                            <input type="text" name="prenom" id="prenom" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="nom" class="form-label">Last Name:</label>
                            <input type="text" name="nom" id="nom" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email:</label>
                            <input type="email" name="email" id="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                        <label for="birthday" class="form-label">Birthday:</label>
    <input  type="date" name="birthday" id="birthday" class="form-control"  max="2024-11-25">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password:</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Password Confirmation:</label>
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <p class="text-muted">Click on submit, so we can send you an E-mail verification.</p>
                        </div>
                        <div class="d-grid">
                            <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>

               


                        </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

