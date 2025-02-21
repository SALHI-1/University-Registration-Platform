<?php
session_start(); // Démarrer la session pour gérer les messages

// Connexion à la base de données
try {
    $pdo = new PDO('mysql:host=localhost;dbname=projet2', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Vérifier que le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $token = htmlspecialchars($_POST['token']);
    $cne = htmlspecialchars(trim($_POST['cne']));
    $cin = htmlspecialchars(trim($_POST['cin']));
    $ville = htmlspecialchars(trim($_POST['ville']));
    $spec_bac = htmlspecialchars(trim($_POST['type_diplome_bac']));
    $date_bac = htmlspecialchars(trim($_POST['date_bac']));
    $spec_bac2 = isset($_POST['type_diplome_bac2']) ? htmlspecialchars(trim($_POST['type_diplome_bac2'])) : null;
    $date_bac2 = isset($_POST['date_bac2']) ? htmlspecialchars(trim($_POST['date_bac2'])) : null;
    $spec_bac3 = isset($_POST['type_diplome_bac3']) ? htmlspecialchars(trim($_POST['type_diplome_bac3'])) : null;
    $date_bac3 = isset($_POST['annee_bac3']) ? htmlspecialchars(trim($_POST['annee_bac3'])) : null;
    $notes = [
        $_POST['note_semestre_1'],
        $_POST['note_semestre_2'],
        $_POST['note_semestre_3'],
        $_POST['note_semestre_4'],
        isset($_POST['note_semestre_5']) ? $_POST['note_semestre_5'] : null,
        isset($_POST['note_semestre_6']) ? $_POST['note_semestre_6'] : null
    ];

    try {
        // Récupérer l'ID de l'étudiant à partir du token
        $stmt = $pdo->prepare("SELECT id FROM student WHERE verification_token = ?");
        $stmt->execute([$token]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$student) {
            throw new Exception("Erreur : étudiant introuvable avec ce token.");
        }

        $student_id = $student['id'];


        // Gestion du fichier photo
        $photo = null;
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $photo = 'uploads/' . basename($_FILES['photo']['name']);
            move_uploaded_file($_FILES['photo']['tmp_name'], $photo);
        }

        // Gestion du fichier des diplômes
        $dossier_candid = null;
        if (isset($_FILES['dossier_diplomes']) && $_FILES['dossier_diplomes']['error'] === UPLOAD_ERR_OK) {
            $dossier_candid = 'uploads/' . basename($_FILES['dossier_diplomes']['name']);
            move_uploaded_file($_FILES['dossier_diplomes']['tmp_name'], $dossier_candid);
        }

        // Insérer les données dans la table candidature
        $stmt = $pdo->prepare("
            INSERT INTO candidature 
            (ID_etud, CNE, CIN, ville, photo, dossier_candid, spec_bac, date_bac, spec_bac2, date_bac2, spec_bac3, date_bac3, note_s1, note_s2, note_s3, note_s4, note_s5, note_s6)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([ 
            $student_id, $cne, $cin, $ville, $photo, $dossier_candid, $spec_bac, $date_bac, 
            $spec_bac2, $date_bac2, $spec_bac3, $date_bac3, 
            $notes[0], $notes[1], $notes[2], $notes[3], $notes[4], $notes[5]
        ]);

     // Récupérer l'ID de la candidature récemment insérée
$candidature_id = $pdo->lastInsertId(); // Assurez-vous d'utiliser cet ID pour la clé étrangère

// Déterminer le niveau d'étude et insérer dans la table filière
$niveau_etude = '';  

// Sélection des filières pour BAC+2 et BAC+3
$bac2_filiere = isset($_POST['bac2_filiere']) ? $_POST['bac2_filiere'] : [];
$bac3_filiere = isset($_POST['bac3_filiere']) ? $_POST['bac3_filiere'] : [];

// Déterminer le niveau d'étude en fonction de la filière
if (!empty($bac2_filiere)) {
    foreach ($bac2_filiere as $filiere) {
        if ($filiere == 'LSI' || $filiere == 'RSI' || $filiere == 'GME') {
            $niveau_etude = 'Cycle';
        } else {
            $niveau_etude = 'Licence';
        }

        // Insérer la filière avec son niveau dans la table filiere
        $filiere_stmt = $pdo->prepare("INSERT INTO filiere (nom_fil, niveau_fil, id_candid) VALUES (?, ?, ?)");
        $filiere_stmt->execute([$filiere, $niveau_etude, $candidature_id]); // Utilisation de $candidature_id
    }
}

if (!empty($bac3_filiere)) {
    foreach ($bac3_filiere as $filiere) {
        if ($filiere == 'LSI' || $filiere == 'RSI' || $filiere == 'GME') {
            $niveau_etude = 'Cycle';
        } else {
            $niveau_etude = 'Master';
        }

        // Insérer la filière avec son niveau dans la table filiere
        $filiere_stmt = $pdo->prepare("INSERT INTO filiere (nom_fil, niveau_fil, id_candid) VALUES (?, ?, ?)");
        $filiere_stmt->execute([$filiere, $niveau_etude, $candidature_id]); // Utilisation de $candidature_id
    }
}

        

        // Stocker le message de succès dans la session
        $_SESSION['success_message'] = "Les données ont été enregistrées avec succès.";
    } catch (Exception $e) {
        // Stocker le message d'erreur dans la session
        $_SESSION['error_message'] = $e->getMessage();
    }

    // Redirection vers la même page
    // header("Location: process_continue_registration.php");
    // exit; // Arrêter l'exécution après la redirection
}

// Affichage des messages (en haut de la page)

if (isset($_SESSION['success_message'])) {
    echo "<div class='container'>";
    echo "<div class='alert alert-success'>";
    echo "<p>" . $_SESSION['success_message'] . "</p>";
    echo "<div class='action-buttons'>";
    echo "<a href='telech_pdf.php?token=$token' class='btn btn-primary'>Télécharger le PDF</a>";
    echo "<a href='send_pdf_email.php?token=$token' class='btn btn-success'>Envoyer le PDF par Email</a>";
    echo "</div>";
    echo "</div>";
    echo "</div>";  // Fin de la div .container
    unset($_SESSION['success_message']);
}

if (isset($_SESSION['error_message'])) {
    echo "<div class='container'>";
    echo "<div class='alert alert-danger'>";
    echo "<p>" . $_SESSION['error_message'] . "</p>";
    echo "</div>";
    echo "</div>";  // Fin de la div .container
    unset($_SESSION['error_message']);
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votre Page</title>
    <style>

   /* CSS pour centrer le contenu */
body {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
    font-family: Arial, sans-serif;
    background-color: #f4f4f4; /* Optionnel : vous pouvez personnaliser l'arrière-plan */
}

.container {
    text-align: center;
}

.alert {
    padding: 15px;
    margin: 20px 0;
    border-radius: 5px;
    font-size: 16px;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.action-buttons {
    margin-top: 10px;
}

.action-buttons .btn {
    display: inline-block;
    margin-right: 10px;
    padding: 10px 15px;
    text-decoration: none;
    color: #fff;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}

.btn-primary {
    background-color: #007bff;
    border: none;
}

.btn-primary:hover {
    background-color: #0056b3;
}

.btn-success {
    background-color: #28a745;
    border: none;
}

.btn-success:hover {
    background-color: #218838;
}

</style>


</head>
<body>
