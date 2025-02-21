<?php include('header.html'); ?>

<?php
// Récupération du token depuis l'URL
if (!isset($_GET['token'])) {
    die("Erreur : aucun token fourni.");
}
$token = $_GET['token'];

// Connexion à la base de données
try {
    $pdo = new PDO('mysql:host=localhost;dbname=projet2', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Récupération des informations de l'étudiant avec le token
$stmt = $pdo->prepare("SELECT id, prenom, nom, email, birthday FROM student WHERE verification_token = ?");
$stmt->execute([$token]);
$etudiant = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$etudiant) {
    die("Erreur : aucun étudiant trouvé avec ce token.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Continue Registration</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }

    h2 {
        text-align: center;
        padding: 20px;
        background-color: #007BFF;
        color: white;
        margin: 0;
    }

    form {
        max-width: 800px;
        margin: 20px auto;
        padding: 20px;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    label {
        display: block;
        margin: 10px 0 5px;
        font-weight: bold;
    }

    input[type="text"],
    input[type="email"],
    input[type="date"],
    input[type="number"],
    input[type="file"],
    select {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-sizing: border-box;
    }

    input[type="radio"] {
        margin-right: 10px;
    }

    h3 {
        margin-top: 20px;
        color: #007BFF;
    }

    .file-input-group {
        display: flex;
        flex-direction: column;
        margin-bottom: 15px;
    }

    .file-input-group label {
        font-weight: normal;
    }

    .file-input-group input[type="file"] {
        padding: 8px;
    }

    input[type="submit"] {
        background-color: #28a745;
        color: white;
        padding: 12px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
    }

    input[type="submit"]:hover {
        background-color: #218838;
    }

    .radio-group {
        margin-bottom: 15px;
    }

    .radio-group input[type="radio"] {
        margin-right: 10px;
    }

    .level-form {
        display: none;
        margin-top: 20px;
    }

    .checkbox-group label {
        display: block;
        margin-bottom: 5px;
    }

    .checkbox-group input[type="checkbox"] {
        margin-right: 10px;
    }

    .required {
        color: red;
        font-size: 12px;
    }

    @media (max-width: 768px) {
        form {
            padding: 15px;
            margin: 10px;
        }

        input[type="submit"] {
            width: 100%;
        }
    }
</style>

    <script>
        
 // Fonction pour afficher/masquer les formulaires selon le niveau sélectionné
function toggleLevelForm(level) {
    const bac2Form = document.getElementById('bac2_form');
    const bac3Form = document.getElementById('bac3_form');  

    if (bac2Form) bac2Form.style.display = (level === 'bac2') ? 'block' : 'none'; // Vérifier si bac2Form existe
    if (bac3Form) bac3Form.style.display = (level === 'bac3') ? 'block' : 'none'; // Vérifier si bac3Form existe

    // Réinitialiser les champs du niveau opposé
    if (level === 'bac2') {
        // Réinitialiser les champs BAC+3
        if (bac3Form) {
            document.querySelectorAll('#bac3_form input[type="number"]').forEach(input => input.value = '');
            document.querySelectorAll('#bac3_form input[type="checkbox"]:checked').forEach(input => input.checked = false);
            document.querySelectorAll('#bac3_form select').forEach(select => select.value = '');
        }
    } else if (level === 'bac3') {
        // Réinitialiser les champs BAC+2
        if (bac2Form) {
            document.querySelectorAll('#bac2_form input[type="number"]').forEach(input => input.value = '');
            document.querySelectorAll('#bac2_form input[type="checkbox"]:checked').forEach(input => input.checked = false);
            document.querySelectorAll('#bac2_form select').forEach(select => select.value = '');
        }
    }

    // Retirer 'required' des champs dans des formulaires masqués
    const allFields = document.querySelectorAll('input[required], select[required]'); 
    allFields.forEach(field => {
        const closestDiv = field.closest('div'); // Trouver le conteneur le plus proche
        if (closestDiv && closestDiv.style.display === 'none') {  // Vérifier si le champ est dans une div masquée
            field.removeAttribute('required');  // Supprimer l'attribut 'required' des champs masqués
        }
    });
}

// Fonction de validation du formulaire
function validateForm() {
    removeRequiredForHiddenFields(); // Retirer les 'required' des champs masqués avant la soumission

    const level = document.querySelector("input[name='niveau']:checked").value; // Récupérer le niveau sélectionné
    const selectedFiliere = document.querySelectorAll(`#${level}_form input[type='checkbox']:checked`); // Sélectionner les filières du niveau actif

    if (selectedFiliere.length < 1 || selectedFiliere.length > 3) {
        alert("Vous devez sélectionner entre 1 et 3 filières.");
        return false;
    }

    // Validation des champs de notes uniquement pour le niveau visible
    const notes = document.querySelectorAll(`#${level}_form input[name^="note_semestre"]`);
    for (const note of notes) {
        // Vérifier si le champ est visible avant de le valider
        const closestDiv = note.closest('div'); // Trouver le conteneur du champ
        if (closestDiv && closestDiv.style.display !== 'none' && note.value === "") {
            alert("Tous les champs de notes doivent être remplis.");
            note.focus();
            return false;
        }
    }

    // Validation du champ type_diplome_bac3, qui peut être caché
    if (level === 'bac3') {
        const typeDiplomeBac3Select = document.querySelector("select[name='type_diplome_bac3']");
        const closestDiv = typeDiplomeBac3Select ? typeDiplomeBac3Select.closest('div') : null;
        if (closestDiv && closestDiv.style.display !== 'none' && !typeDiplomeBac3Select.value) {
            alert("Le type de diplôme pour BAC+3 doit être sélectionné.");
            return false;
        }
    }

    return true;
}

// Fonction pour retirer les 'required' des champs masqués
function removeRequiredForHiddenFields() {
    const allFields = document.querySelectorAll('input[required], select[required]'); 
    allFields.forEach(field => {
        const closestDiv = field.closest('div'); // Trouver le conteneur le plus proche
        if (closestDiv && closestDiv.style.display === 'none') { // Vérifier si le champ est dans une div masquée
            field.removeAttribute('required');  // Retirer l'attribut 'required' des champs masqués
        }
    });
}



</script>
</head>

<body>
    <h2>Continue Registration</h2>
    <form action="process_continue_registration.php" method="POST" onsubmit="return validateForm();" enctype="multipart/form-data">
        <!-- Informations non modifiables -->
        <label>Nom :</label>
        <input type="text" name="nom" value="<?= htmlspecialchars($etudiant['nom']) ?>" readonly><br>

        <label>Prénom :</label>
        <input type="text" name="prenom" value="<?= htmlspecialchars($etudiant['prenom']) ?>" readonly><br>

        <label>Email :</label>
        <input type="email" name="email" value="<?= htmlspecialchars($etudiant['email']) ?>" readonly><br>

        <label>Date de Naissance :</label>
        <input type="date" name="birthday" value="<?= htmlspecialchars($etudiant['birthday']) ?>" readonly><br>
        

        <!-- nouveaux données -->


    <!-- CNE -->
    <label for="cne">CNE :</label>
    <input type="text" id="cne" name="cne" required><br>

    <!-- CIN -->
    <label for="cin">CIN :</label>
    <input type="text" id="cin" name="cin" required><br>

    <!-- Ville -->
    <label for="ville">Ville :</label>
    <input type="text" id="ville" name="ville" required><br>



        <!-- Ajout d'une photo -->
        
        <h3>Fichiers à télécharger</h3>
        
        <label>Photo de profil :</label>
        <input type="file" name="photo" accept="image/*" required><br>

        <label>Dossier des diplômes obtenus (PDF) :</label>
        <input type="file" name="dossier_diplomes" accept=".pdf" required><br>
        
        <label>Date d'obtention du BAC :</label>
        <input type="number" name="date_bac" min="2021" max="2024" step="1" required><br>
       
        <h3>specialité de BAC:</h3>
    <label>spécialité de BAC :</label>
    <select name="type_diplome_bac" required>
        <option value="" disabled selected>-- Sélectionnez votre specialité --</option>
        <option value="PC">PC</option>
        <option value="SVT">SVT</option>
        <option value="SM">SM</option>
    </select><br>



      <!-- Question : BAC+2 ou BAC+3 -->
      <label>Précisez votre niveau :</label>
<input type="radio" name="niveau" value="bac2" onclick="toggleLevelForm('bac2')" required> BAC+2
<input type="radio" name="niveau" value="bac3" onclick="toggleLevelForm('bac3')" required> BAC+3<br>


    <!-- Formulaire pour BAC+2 -->
<div id="bac2_form" style="display: none;">
    <h3>Type de diplôme BAC+2 :</h3>
    <label>Type de diplôme :</label>
    <select name="type_diplome_bac2" required>
        <option value="" disabled selected>-- Sélectionnez votre diplôme --</option>
        <option value="deust">DEUST</option>
        <option value="deut">DEUT</option>
        <option value="dut">DUT</option>
    </select><br>

    <!-- Champ pour la date d'obtention du BAC+2 -->
    <label>Date d'obtention du BAC+2 :</label>
    <input type="number" name="date_bac2"  min="2021" max="2024" step="1" required><br>

    <h3>Notes des semestres :</h3>
    <label>Semestre 1 :</label>
    <input type="number" name="note_semestre_1" min="6" max="20" step="1" required><br>
    <label>Semestre 2 :</label>
    <input type="number" name="note_semestre_2" min="6" max="20" step="1" required><br>
    <label>Semestre 3 :</label>
    <input type="number" name="note_semestre_3" min="6" max="20" step="1" required><br>
    <label>Semestre 4 :</label>
    <input type="number" name="note_semestre_4" min="6" max="20" step="1" required><br>

    <h3>Filières BAC+2</h3>
    <h4>Licence  :</h4>
    <label><input type="checkbox" name="bac2_filiere[]" value="MA"> Math Appliqué</label><br>
    <label><input type="checkbox" name="bac2_filiere[]" value="IRM"> Informatique et Réseau Multimédia</label><br>
    <label><input type="checkbox" name="bac2_filiere[]" value="GEII"> Genie Electrique et Informatique Industriel</label><br>
    <h4>Cycle d'Ingénieur  :</h4>
    <label><input type="checkbox" name="bac2_filiere[]" value="LSI"> Logiciel et Systèmes Intelligents</label><br>
    <label><input type="checkbox" name="bac2_filiere[]" value="GME"> Genie Electrique et Mathématique</label><br>
    <label><input type="checkbox" name="bac2_filiere[]" value="RSI"> Réseau et Systèmes intelligents</label><br>

</div>

<!-- Formulaire pour BAC+3 -->
<div id="bac3_form" style="display: none;">

<h3>Type de diplôme BAC+2 :</h3>
    <label>Type de diplôme :</label>
    <select name="type_diplome_bac2" required>
        <option value="" disabled selected>-- Sélectionnez votre diplôme --</option>
        <option value="deust">DEUST</option>
        <option value="deut">DEUT</option>
        <option value="dut">DUT</option>
    </select><br>

    <!-- Champ pour la date d'obtention du BAC+2 -->
    <label>Date d'obtention du BAC+2 :</label>
    <input type="number" name="date_bac2"  min="2021" max="2024" step="1" required><br>


    <h3>Type de diplôme BAC+3 :</h3>
    <label>Type de diplôme :</label>
    <select name="type_diplome_bac3" required>
        <option value="" disabled selected>-- Sélectionnez votre diplôme --</option>
        <option value="pc">Physique</option>
        <option value="math">Mathématiques</option>
        <option value="info">Informatique</option>
        <option value="elec">Électrique</option>
    </select><br>

   <!-- Champ pour l'année d'obtention du BAC+3 -->
   <label>Année d'obtention du BAC+3 :</label>
   <input type="number" name="annee_bac3" min="2021" max="2024" step="1"><br>

    <h3>Notes des semestres :</h3>
    <label>Semestre 1 :</label>
    <input type="number" name="note_semestre_1" min="6" max="20" step="1" required><br>
    <label>Semestre 2 :</label>
    <input type="number" name="note_semestre_2" min="6" max="20" step="1" required><br>
    <label>Semestre 3 :</label>
    <input type="number" name="note_semestre_3" min="6" max="20" step="1" required><br>
    <label>Semestre 4 :</label>
    <input type="number" name="note_semestre_4" min="6" max="20" step="1" required><br>
    <label>Semestre 5 :</label>
    <input type="number" name="note_semestre_5" min="6" max="20" step="1" required><br>
    <label>Semestre 6 :</label>
    <input type="number" name="note_semestre_6" min="6" max="20" step="1" required><br>

    <h3>Filières BAC+3</h3>
    <h4>Master (1 à 3 choix) :</h4>
    <label><input type="checkbox" name="bac3_filiere[]" value="ISERT"> Ingénierie des Systèmes Embarqués, Réseaux et Télécommunications</label><br>
    <label><input type="checkbox" name="bac3_filiere[]" value="IPMA"> Ingénierie et Physique des Matériaux Avancés</label><br>
    <label><input type="checkbox" name="bac3_filiere[]" value="GCBI"> Génie Civil et bâtiments intelligents</label><br>
    <h4>Cycle d'Ingénieur (1 à 3 choix) :</h4>
    <label><input type="checkbox" name="bac3_filiere[]" value="LSI"> Logiciel et Systèmes Intelligents</label><br>
    <label><input type="checkbox" name="bac3_filiere[]" value="GME"> Genie Electrique et Mathématique</label><br>
    <label><input type="checkbox" name="bac3_filiere[]" value="RSI"> Réseau et Systèmes intelligents</label><br>
</div>

        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
        <input type="submit" value="submit">
    </form>
</body>
</html>
