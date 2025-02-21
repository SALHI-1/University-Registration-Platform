<?php
session_start(); // Démarrer la session

require __DIR__ . '/vendor/autoload.php'; // Charger l'autoload de Composer
use Dompdf\Dompdf; // Importer Dompdf
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Connexion à la base de données
try {
    $pdo = new PDO('mysql:host=localhost;dbname=projet2', 'root', ''); // Connexion avec PDO
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Activer le mode d'erreur
} catch (PDOException $e) {
    $_SESSION['message'] = ['type' => 'error', 'content' => "Erreur de connexion à la base de données : " . $e->getMessage()];
    header('Location: page1.php');
    exit();
}

// Vérification et récupération de l'ID de l'étudiant via le token
$token = $_GET['token'] ?? null; // Récupérer le token passé dans l'URL
if (!$token) {
    $_SESSION['message'] = ['type' => 'error', 'content' => 'Token manquant !'];
    header('Location: page1.php');
    exit();
}

// Récupérer les données de l'étudiant avec le token
$stmt = $pdo->prepare("SELECT * FROM student WHERE verification_token = ?");
$stmt->execute([$token]);
$student = $stmt->fetch(PDO::FETCH_ASSOC); // Vérifier si l'étudiant existe

if (!$student) {
    $_SESSION['message'] = ['type' => 'error', 'content' => 'Étudiant introuvable !'];
    header('Location: page1.php');
    exit();
}

// Récupérer les informations de la candidature de l'étudiant
$id = $student['id'];
$stmt = $pdo->prepare("SELECT * FROM candidature WHERE ID_etud = ?");
$stmt->execute([$id]);
$candidature = $stmt->fetch(PDO::FETCH_ASSOC); // Vérifier si la candidature existe

if (!$candidature) {
    $_SESSION['message'] = ['type' => 'error', 'content' => 'Candidature introuvable !'];
    header('Location: page1.php');
    exit();
}

// Récupérer toutes les filières de l'étudiant
$stmt = $pdo->prepare("SELECT * FROM filiere WHERE student_id = ?");
$stmt->execute([$id]);
$filieres = $stmt->fetchAll(PDO::FETCH_ASSOC); // Récupérer toutes les filières

if (!$filieres) {
    $_SESSION['message'] = ['type' => 'error', 'content' => 'Aucune filière trouvée !'];
    header('Location: page1.php');
    exit();
}

// Générer le contenu HTML pour le PDF
$html = "
<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Fiche de Préinscription</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; margin: 20px; }
        .alert { padding: 20px; margin: 20px 0; border-radius: 5px; text-align: center; }
        .success { background-color: #28a745; color: white; }
        .error { background-color: #dc3545; color: white; }
        .alert a { color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; background-color: #007bff; }
        .alert a:hover { background-color: #0056b3; }
        .header { background-color: #004080; padding: 20px; color: white; text-align: center; border-radius: 10px; }
        .header img { height: 60px; border-radius: 50%; }
        .header h1 { font-size: 30px; margin: 10px 0; font-weight: bold; }
        h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        td { text-align: center; }
        ul { list-style-type: none; padding-left: 0; }
        li { margin-bottom: 5px; }
        .student-photo { float: right; margin-top: -80px; margin-left: 20px; border-radius: 50%; width: 120px; height: 120px; }
    </style>
</head>

<body>

    <!-- En-tête avec logo et nom de l'université -->
    <div class='header'>
        <h1>Faculté des Sciences et Techniques</h1>
        <p>Tanger</p>
    </div>

    <h2>Fiche de Préinscription</h2>

    <table>
        <tr>
            <th>Nom</th>
            <td>{$student['nom']}</td>
        </tr>
        <tr>
            <th>Prénom</th>
            <td>{$student['prenom']}</td>
        </tr>
        <tr>
            <th>Email</th>
            <td>{$student['email']}</td>
        </tr>
        <tr>
            <th>CNE</th>
            <td>{$candidature['CNE']}</td>
        </tr>
        <tr>
            <th>CIN</th>
            <td>{$candidature['CIN']}</td>
        </tr>
        <tr>
            <th>Ville</th>
            <td>{$candidature['ville']}</td>
        </tr>
        <tr>
            <th>Filières</th>
            <td>
                <ul>";
                foreach ($filieres as $filiere) {
                    $html .= "<li>{$filiere['niveau_fil']} : {$filiere['nom_fil']}</li>";
                }
                $html .= "</ul>
            </td>
        </tr>
    </table>
</body>
</html>
";

// Initialisation de Dompdf
$dompdf = new Dompdf();
$dompdf->loadHtml($html); // Charger le contenu HTML
$dompdf->setPaper('A4', 'portrait'); // Définir la taille et l'orientation du papier
$dompdf->render(); // Générer le PDF

// Sauvegarder le PDF dans un fichier temporaire
$output = $dompdf->output();
$temp_pdf_path = __DIR__ . '/uploads/fichier_preinscription_' . $student['nom'] . '_' . $student['prenom'] . '.pdf';
file_put_contents($temp_pdf_path, $output);

// Initialiser PHPMailer pour envoyer l'email
$mail = new PHPMailer(true); // Activer les exceptions
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'mohammedsalhisam@gmail.com'; // Remplacez par votre email
    $mail->Password = 'pyds wzgx xvgx dkiu'; // Remplacez par votre mot de passe d'application
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;

    $mail->setFrom('mohammedsalhisam@gmail.com', 'Université');
    $mail->addAddress($student['email'], $student['prenom'] . ' ' . $student['nom']); // Ajouter l'email de l'étudiant

    // Ajouter le fichier PDF en pièce jointe
    $mail->addAttachment($temp_pdf_path);

    // Contenu de l'email
    $mail->isHTML(true);
    $mail->Subject = 'Fiche de Préinscription';
    $mail->Body    = 'Bonjour ' . $student['prenom'] . ',<br>Veuillez trouver en pièce jointe votre fiche de préinscription.<br>Cordialement,<br>Université Abdelmalek Essaâdi';

    // Envoyer l'email
    $mail->send();

    // Supprimer le fichier PDF temporaire après envoi
    unlink($temp_pdf_path);

    $_SESSION['message'] = ['type' => 'success', 'content' => "L'email avec la fiche de préinscription a été envoyé avec succès."];
    header('Location: page1.php');
    exit();

} catch (Exception $e) {
    $_SESSION['message'] = ['type' => 'error', 'content' => "L'email n'a pas pu être envoyé. Erreur: {$mail->ErrorInfo}"];
    header('Location: page1.php');
    exit();
}
?>
