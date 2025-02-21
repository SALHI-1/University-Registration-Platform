<?php
require __DIR__ . '/vendor/autoload.php'; // Charger autoload de Composer

use Dompdf\Dompdf; // Importer Dompdf

// Connexion à la base de données
try {
    $pdo = new PDO('mysql:host=localhost;dbname=projet2', 'root', ''); // Connexion avec PDO
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Activer le mode d'erreur
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage()); // Gestion des erreurs
}

// Vérification et récupération de l'ID de l'étudiant via le token
$token = $_GET['token'] ?? null; // Récupérer le token passé dans l'URL
if (!$token) {
    die("Token manquant !");
}

// Récupérer les données de l'étudiant avec le token
$stmt = $pdo->prepare("SELECT * FROM student WHERE verification_token = ?");
$stmt->execute([$token]);
$student = $stmt->fetch(PDO::FETCH_ASSOC); // Vérifier si l'étudiant existe

if (!$student) {
    die("Étudiant introuvable !");
}

// Récupérer les informations de la candidature de l'étudiant
$id = $student['id'];
$stmt = $pdo->prepare("SELECT * FROM candidature WHERE ID_etud = ?");
$stmt->execute([$id]);
$candidature = $stmt->fetch(PDO::FETCH_ASSOC); // Vérifier si la candidature existe

if (!$candidature) {
    die("Candidature introuvable !");
}

// Récupérer toutes les filières de l'étudiant
$stmt = $pdo->prepare("SELECT * FROM filiere WHERE id_candid = ?");
$stmt->execute([$candidature['ID']]);
$filieres = $stmt->fetchAll(PDO::FETCH_ASSOC); // Récupérer toutes les filières

if (!$filieres) {
    die("Aucune filière trouvée !");
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
        
        /* Style pour l'en-tête */
        .header {
            background-color: #004080;
            padding: 20px;
            color: white;
            text-align: center;
            border-radius: 10px;
        }
        .header img {
            height: 60px;
            border-radius: 50%;
        }
        .header h1 {
            font-size: 30px;
            margin: 10px 0;
            font-weight: bold;
        }
        .header p {
            font-size: 18px;
            margin: 0;
        }
        h2{
            text-align: center;
        }

        /* Style du tableau */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        td { text-align: center; }
        
        ul { list-style-type: none; padding-left: 0; }
        li { margin-bottom: 5px; }
        /* Ajout de la photo de l'étudiant */
        .student-photo {
            float: right;
            margin-top: -80px; /* Décalage pour placer l'image plus près du titre */
            margin-left: 20px;
            border-radius: 50%;
            width: 120px; /* Ajustez la taille selon besoin */
            height: 120px;
        }
    </style>
</head>

<body>

    <!-- En-tête avec logo et nom de l'université -->
    <div class='header'>
        <h1>Faculté des Sciences et Techniques</h1>
        <p>Tanger</p>
    </div>
<!-- Photo de l'étudiant à droite -->
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
    <tr>
        <th>Notes</th>
        <td>
            <ul>";
            // Récupérer les notes
            $notes = [
                $candidature['note_s1'],
                $candidature['note_s2'],
                $candidature['note_s3'],
                $candidature['note_s4'],
                $candidature['note_s5'],
                $candidature['note_s6']
            ];
            foreach ($notes as $index => $note) {
                if ($note != 0) { // Vérifier si la note n'est pas égale à 0
                    $html .= "<li>Note S" . ($index + 1) . ": $note</li>";
                }
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

// Générer le contenu du PDF
$pdf_output = $dompdf->output();

// Récupérer les informations nécessaires
$nom = strtoupper($student['nom']);  // Convertir en majuscules pour le nom
$prenom = ucfirst(strtolower($student['prenom']));  // Première lettre en majuscule, le reste en minuscule

// Définir le chemin pour sauvegarder le fichier
$file_path = 'uploads_recu/recu_' .$nom.''.$prenom.''. $student['id'] . '.pdf';

// Construire le nom du fichier
$nom_fichier = $nom . '_' . $prenom . '_INSCRIPTIONfstt.pdf';

// Sauvegarder le fichier sur le serveur
file_put_contents($file_path, $pdf_output);

// Vérifier si une ligne existe pour cet étudiant dans la table 'candidature' (en utilisant la clé étrangère id_etudiant)
$stmt = $pdo->prepare("SELECT COUNT(*) FROM candidature WHERE id_etud = :id_etud");
$stmt->execute(['id_etud' => $student['id']]);
$record_exists = $stmt->fetchColumn() > 0;

if ($record_exists) {
    // Si l'entrée existe, on met à jour le champ 'recu' de la table 'candidature'
    $stmt = $pdo->prepare("UPDATE candidature SET recu = :file_path WHERE id_etud = :id_etud");
    $stmt->execute(['file_path' => $file_path, 'id_etud' => $student['id']]);
} else {
    // Si aucune entrée n'existe, ajouter une nouvelle ligne dans la table 'candidature'
    $stmt = $pdo->prepare("INSERT INTO candidature (id_etud, recu) VALUES (:id_etud, :file_path)");
    $stmt->execute(['id_etud' => $student['id'], 'file_path' => $file_path]);
}

// Assurez-vous d'envoyer un bon type MIME pour les fichiers PDF
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $nom_fichier . '"');
header('Content-Transfer-Encoding: binary');
header('Content-Length: ' . strlen($pdf_output)); // Spécifiez la longueur du contenu

// Envoyer le fichier pour le téléchargement
echo $pdf_output;

exit;
?>
