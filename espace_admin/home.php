<?php include 'header.php'; ?>


<?php
session_start();


echo '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Inclure Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900">
<div class="container mx-auto p-6">
';

echo '<h1 class="text-center text-3xl font-bold text-blue-600">Bienvenue, ' . $_SESSION['nom_chef'] . '</h1>';
echo ' <!-- Bouton de déconnexion avec une petite icône -->
        <form action="logout.php" method="POST">
            <!-- Icône de déconnexion (exemple d\'icône de porte) -->
            <button type="submit" class="focus:outline-none">
                <img src="images_administration/se-deconnecter.png" alt="Déconnexion" class="h-8 w-8">
            </button>
        </form>';
echo '<hr class="my-6 border-t-4 border-blue-500 rounded-lg">';

try {
    $pdo = new PDO('mysql:host=localhost;dbname=projet2', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("<div class='text-red-500 text-center'>Erreur de connexion à la base de données : " . $e->getMessage() . "</div>");
}

$stmt = $pdo->prepare("SELECT * FROM chef_filiere WHERE id= ?");
$stmt->execute([$_SESSION['id']]);
$chef = $stmt->fetch(PDO::FETCH_ASSOC);

if ($chef) {
    $sql = "
        SELECT student.*, candidature.*, filiere.nom_fil, filiere.id_candid, filiere.verified
        FROM student
        INNER JOIN candidature ON student.id = candidature.ID_etud
        INNER JOIN filiere ON candidature.ID = filiere.id_candid 
        WHERE filiere.nom_fil = ?
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$chef['filiere']]);
    $resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo '<div class="bg-white shadow-md rounded-lg">';
    echo '<div class="bg-blue-600 text-white text-center py-4 rounded-t-lg">';
    echo '<h2 class="text-xl font-semibold">Liste des étudiants - Filière : ' . $chef['filiere'] . '</h2>';
    echo '</div>';
    echo '<div class="p-4 overflow-auto">';
    echo '<form method="POST" action="home.php">';
    echo '<table class="table-auto w-full text-left border-collapse">';
echo '<thead>';
echo '<tr class="bg-blue-100 text-blue-800">';
echo '<th class="px-4 py-2 border-b-2 border-blue-200">Nom</th>';
echo '<th class="px-4 py-2 border-b-2 border-blue-200">Prénom</th>';
echo '<th class="px-4 py-2 border-b-2 border-blue-200">CNE</th>';
echo '<th class="px-4 py-2 border-b-2 border-blue-200">Ville</th>';
echo '<th class="px-4 py-2 border-b-2 border-blue-200">Filière choisie</th>';
echo '<th class="px-4 py-2 border-b-2 border-blue-200">Reçu</th>'; // Nouvelle colonne pour le reçu
echo '<th class="px-4 py-2 border-b-2 border-blue-200">Dossier</th>'; // Nouvelle colonne pour le reçu
echo '<th class="px-4 py-2 border-b-2 border-blue-200">Vérification</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';

foreach ($resultats as $row) {
    echo '<tr class="hover:bg-gray-100">';
    echo '<td class="px-4 py-2 border-b border-gray-200">' . $row['nom'] . '</td>';
    echo '<td class="px-4 py-2 border-b border-gray-200">' . $row['prenom'] . '</td>';
    echo '<td class="px-4 py-2 border-b border-gray-200">' . $row['CNE'] . '</td>';
    echo '<td class="px-4 py-2 border-b border-gray-200">' . $row['ville'] . '</td>';
    echo '<td class="px-4 py-2 border-b border-gray-200">' . $row['nom_fil'] . '</td>';

    // Colonne pour le reçu
    if (!empty($row['recu'])) { // Remplacez 'fichier_recu' par le nom exact de la colonne
        echo '<td class="px-4 py-2 border-b border-gray-200 text-center">
            <a href="' .$row['recu'] . '" class="text-blue-600 underline" target="_blank">Télécharger</a>
        </td>';
    } else {
        echo '<td class="px-4 py-2 border-b border-gray-200 text-center text-gray-500">Aucun fichier</td>';
    }
// Colonne pour le reçu
if (!empty($row['recu'])) { // Remplacez 'fichier_recu' par le nom exact de la colonne
    echo '<td class="px-4 py-2 border-b border-gray-200 text-center">
        <a href="' .$row['dossier_candid'] . '" class="text-blue-600 underline" target="_blank">Télécharger</a>
    </td>';
} else {
    echo '<td class="px-4 py-2 border-b border-gray-200 text-center text-gray-500">Aucun Dossier</td>';
}
    echo '<td class="px-4 py-2 border-b border-gray-200 text-center">
        <input type="checkbox" name="verify[' . $row['id_candid'] . ']" value="1" ' . ($row['verified'] ? 'checked' : '') . '>
    </td>';
    echo '</tr>';
}

echo '</tbody>';
echo '</table>';

    
    // Boutons centrés
    echo '<div class="flex justify-center mt-6 gap-4">';
    echo '<button type="submit" class="bg-blue-600 text-white py-2 px-6 rounded-lg hover:bg-blue-700 shadow-md">Mettre à jour la vérification</button>';
    echo '<a href="supprimer_non_verif.php" class="bg-red-600 text-white px-6 py-2 rounded-lg shadow-md hover:bg-red-700 transition">
        Supprimer les utilisateurs non vérifiés
    </a>';
    echo '</div>';

    echo '</form>';
    echo '</div>';
    echo '</div>';
} else {
    echo '<div class="text-center text-red-500">Chef introuvable.</div>';
}

echo '</div>';
echo '</body></html>';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<form action="etape_finale.php" method="post" class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow-lg space-y-6">
    <div>
        <label for="number" class="block text-lg font-semibold text-gray-700">Nombre d'étudiants Souhaité</label>
        <input 
            type="number" 
            name="number" 
            max="20" 
            min="1" 
            class="w-full mt-2 p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none" 
            required>
    </div>
    
    <div class="text-center">
        <input 
            type="submit" 
            name="selectionner" 
            value="Sélectionner" 
            class="w-full py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
    </div>
</form>

</body>
</html>

<?php 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer tous les IDs des étudiants affichés
    $allIds = array_column($resultats, 'id_candid');

    // Initialiser un tableau pour les mises à jour avec l'état de vérification
    $updates = [];

    foreach ($allIds as $id) {
        // Vérifier si l'étudiant est coché ou non
        $verified = isset($_POST['verify'][$id]) ? 1 : 0;
        $updates[] = "WHEN id_candid = $id THEN $verified";
    }

    // Construire et exécuter la requête seulement si des IDs sont présents
    if (!empty($updates)) {
        $cases = implode(' ', $updates);
        $ids = implode(',', $allIds);

        $sql = "
            UPDATE filiere
            SET verified = CASE $cases END
            WHERE id_candid IN ($ids) AND nom_fil = ?
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$chef['filiere']]);
    }

  
}

// Récupérer les données initiales pour l'affichage
$stmt = $pdo->prepare("SELECT * FROM filiere WHERE nom_fil = ?");
$stmt->execute([$chef['filiere']]);
$resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


