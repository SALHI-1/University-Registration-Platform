
<?php include 'header.php'; ?>

<?php

try {
    $pdo = new PDO('mysql:host=localhost;dbname=projet2', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("<div class='text-red-500 text-center'>Erreur de connexion à la base de données : " . $e->getMessage() . "</div>");
}

$sql="SELECT id,nom_chef,prenom_chef,code_verif,password,filiere 
      FROM chef_filiere WHERE role='chef'  ";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$chefs = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Chefs de Filière</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">
       <!-- Barre de navigation -->
       <nav class="bg-blue-600 shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <div class="flex-shrink-0">
                    <a href="" class="text-white font-bold text-lg">Admin Dashboard</a>
                </div>
                <!-- Menu -->
                <div class="hidden md:flex space-x-4">
                    <a href="profile.php" class="text-white hover:bg-blue-700 px-3 py-2 rounded">Mon Compte</a>
                    <a href="home_admin.php" class="text-white hover:bg-blue-700 px-3 py-2 rounded">Statistiques</a>
                    <a href="gerer_chefs.php" class="text-white hover:bg-blue-700 px-3 py-2 rounded">Liste des Chefs</a>
                    <a href="logout.php" class="text-white hover:bg-blue-700 px-3 py-2 rounded">se déconnecter</a>
                </div>
               
            </div>
        </div>
       
    </nav>
    <div class="container mx-auto my-8 p-4 bg-white rounded shadow-lg">
        <h1 class="text-2xl font-bold text-center text-gray-700 mb-6">Liste des Chefs de Filière</h1>

        <table class="table-auto w-full border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-200 text-gray-700">
                    <th class="border border-gray-300 px-4 py-2">Nom</th>
                    <th class="border border-gray-300 px-4 py-2">Prénom</th>
                    <th class="border border-gray-300 px-4 py-2">Code</th>
                    <th class="border border-gray-300 px-4 py-2">Password</th>
                    <th class="border border-gray-300 px-4 py-2">Filière</th>
                    <th class="border border-gray-300 px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($chefs as $chef): ?>
                    <tr class="text-gray-600 hover:bg-gray-100">
                        <td class="border border-gray-300 px-4 py-2 text-center"><?php echo $chef['nom_chef']; ?></td>
                        <td class="border border-gray-300 px-4 py-2 text-center"><?php echo $chef['prenom_chef']; ?></td>
                        <td class="border border-gray-300 px-4 py-2 text-center"><?php echo $chef['code_verif']; ?></td>
                        <td class="border border-gray-300 px-4 py-2 text-center"><?php echo $chef['password']; ?></td>
                        <td class="border border-gray-300 px-4 py-2 text-center"><?php echo $chef['filiere']; ?></td>
                        <td class="border border-gray-300 px-4 py-2 text-center"> <a href="supp_chef.php?delet=<?php echo $chef['id']; ?>"
                            class="text-red-500 hover:text-red-700 font-bold  mx-3">
                            Supprimer</a>
                            <a href="modif_chef.php?id=<?php echo $chef['id']; ?>" 
                            class="text-blue-500 hover:text-blue-700 font-bold  mx-3">
                            Modifier
                            </a>
                            </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if (empty($chefs)): ?>
            <p class="text-red-500 text-center mt-4">Aucun chef trouvé.</p>
        <?php endif; ?>
    </div>
</body>
</html>
