<?php
session_start();
require __DIR__ . '/../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Connexion à la base de données avec PDO
        $pdo = new PDO('mysql:host=localhost;dbname=projet2', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Erreur de connexion à la base de données : " . $e->getMessage());
    }

    // Récupérer le nom du chef de filière depuis la session
    $id = $_SESSION['id'];

    // Requête pour récupérer les informations du chef de filière
    try {
        $stmt = $pdo->prepare("SELECT * FROM chef_filiere WHERE id = ?");
        $stmt->execute([$id]);
        $chef = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$chef) {
            die("Aucun chef de filière trouvé pour ce nom.");
        }

        $filiere = $chef['filiere'];
        $nombre_etudiants = $_POST['number']; // Nombre désiré d'étudiants
    } catch (PDOException $e) {
        die("Erreur lors de la récupération des données du chef de filière : " . $e->getMessage());
    }

    // Requête principale pour récupérer les étudiants
    $query = "
    SELECT 
        s.nom, 
        s.prenom, 
        c.CNE, 
        c.CIN, 
        CASE 
            WHEN c.note_s5 IS NOT NULL AND c.note_s6 IS NOT NULL THEN 
                (c.note_s1 + c.note_s2 + c.note_s3 + c.note_s4 + c.note_s5 + c.note_s6) / 6
            WHEN c.note_s5 IS NULL AND c.note_s6 IS NULL THEN 
                (c.note_s1 + c.note_s2 + c.note_s3 + c.note_s4) / 4
            ELSE NULL
        END AS moyenne
    FROM 
        student s
    INNER JOIN 
        candidature c ON s.id = c.id_etud
    INNER JOIN 
        filiere f ON c.ID = f.id_candid
    WHERE 
        f.verified = 1 -- Candidatures vérifiées
        AND f.nom_fil = :nom_filiere -- Nom de la filière spécifique
    ORDER BY 
        moyenne DESC
    LIMIT :nombre_etudiants
";

    

    try {
        $stmt = $pdo->prepare($query);

        // Lier les paramètres
        $stmt->bindParam(':nom_filiere', $filiere, PDO::PARAM_STR);
        $stmt->bindValue(':nombre_etudiants', (int)$nombre_etudiants, PDO::PARAM_INT);

        $stmt->execute();

        // Récupérer les résultats
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($result) {
            // Création d'un fichier Excel
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Ajouter les en-têtes
            $sheet->setCellValue('A1', 'Nom');
            $sheet->setCellValue('B1', 'Prénom');
            $sheet->setCellValue('C1', 'CNE');
            $sheet->setCellValue('D1', 'CIN');
            $sheet->setCellValue('E1', 'Moyenne');

            // Remplir les données
            $rowIndex = 2; // Commence à la ligne 2 (après les en-têtes)
            foreach ($result as $row) {
                $sheet->setCellValue('A' . $rowIndex, $row['nom']);
                $sheet->setCellValue('B' . $rowIndex, $row['prenom']);
                $sheet->setCellValue('C' . $rowIndex, $row['CNE']);
                $sheet->setCellValue('D' . $rowIndex, $row['CIN']);
                $sheet->setCellValue('E' . $rowIndex, $row['moyenne']);
                $rowIndex++;
            }

            // Générer le fichier Excel
            $writer = new Xlsx($spreadsheet);

            // En-têtes HTTP pour le téléchargement
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="etudiants.xlsx"');
            header('Cache-Control: max-age=0');

            // Envoyer le fichier au navigateur
            $writer->save('php://output');
            exit;
        } else {
            echo "Aucun étudiant ne correspond aux critères.";
        }
    } catch (PDOException $e) {
        die("Erreur lors de l'exécution de la requête : " . $e->getMessage());
    }
} else {
    echo "Requête invalide.";
}
?>
