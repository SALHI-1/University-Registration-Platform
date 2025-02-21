
<?php include 'header.php'; ?>

<?php
session_start();
try {
    $pdo = new PDO('mysql:host=localhost;dbname=projet2', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Calcul des statistiques
    $total_inscriptions = $pdo->query("SELECT COUNT(*) FROM candidature")->fetchColumn();

    // Étudiants Bac+2 (où les notes de S6 sont NULL)
    $total_bac2 = $pdo->query("SELECT COUNT(*) FROM candidature WHERE note_s6 IS NULL")->fetchColumn();

    // Étudiants Bac+3 (où les notes de S5 et S6 sont présentes)
    $total_bac3 = $pdo->query("SELECT COUNT(*) FROM candidature WHERE note_s6 IS NOT NULL AND note_s5 IS NOT NULL")->fetchColumn();

    // Données pour le graphe des demandes par niveau en joignant les tables "candidature" et "filiere"
    $licence = $pdo->query("SELECT COUNT(*) 
                             FROM filiere f
                             WHERE f.niveau_fil = 'Licence'")->fetchColumn();
    
    $master = $pdo->query("SELECT COUNT(*) 
                           FROM filiere f 
                           WHERE f.niveau_fil = 'Master'")->fetchColumn();

    $cycle = $pdo->query("SELECT COUNT(*) 
                          FROM filiere f 
                          WHERE f.niveau_fil = 'Cycle'")->fetchColumn();

    // Filieres les plus demandées pour chaque niveau
    // Filières les plus demandées pour Licence
    $licence_filieres = $pdo->query("SELECT f.nom_fil, COUNT(*) as count 
                                     FROM filiere f 
                                     WHERE f.niveau_fil = 'Licence'
                                     GROUP BY f.nom_fil 
                                     ORDER BY count DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    
    // Filières les plus demandées pour Master
    $master_filieres = $pdo->query("SELECT f.nom_fil, COUNT(*) as count 
                                    FROM filiere f 
                                    WHERE f.niveau_fil = 'Master'
                                    GROUP BY f.nom_fil 
                                    ORDER BY count DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
  

  $filiere_dominante = $pdo->query("SELECT nom_fil, COUNT(*) AS demande_count
  FROM filiere
  GROUP BY nom_fil
  ORDER BY demande_count DESC
  LIMIT 1")->fetch(PDO::FETCH_ASSOC);

$total_chefs = $pdo->query("SELECT COUNT(*) FROM chef_filiere WHERE role='chef'")->fetchColumn();

$total_candidature_verifie = $pdo->query("SELECT COUNT(*) FROM filiere WHERE verified=1 ")->fetchColumn();


    // Filières les plus demandées pour Cycle
    $cycle_filieres = $pdo->query("SELECT f.nom_fil, COUNT(*) as count 
                                    FROM filiere f 
                                    WHERE f.niveau_fil = 'Cycle'
                                    GROUP BY f.nom_fil 
                                    ORDER BY count DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques Admin</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
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
    <div class="container mx-auto p-6">
        <h1 class="text-3xl text-blue-600 font-bold hover:text-gray-700 text-center mb-6">Statistiques des Inscriptions</h1>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <div class="bg-green p-6 rounded-lg shadow-md text-center">
                <h2 class="text-xl  font-bold hover:text-blue-500 text-gray-700">Total des Inscriptions</h2>
                <p class="text-4xl font-bold  hover:text-blue-700 text-black-100"><?= $total_inscriptions; ?></p>
            </div>
           


            
            <div class="bg-white p-6 rounded-lg shadow-md text-center">
                <h2 class="text-xl font-bold hover:text-blue-500 text-gray-700">Étudiants Bac+2</h2>
                <p class="text-4xl font-bold  hover:text-blue-700 text-black-100"><?= $total_bac2; ?></p>
            </div>
            <div class="bg-green p-6 rounded-lg shadow-md text-center">
                <h2 class="text-xl font-bold hover:text-blue-500 text-gray-700">Étudiants Bac+3</h2>
                <p class="text-4xl font-bold  hover:text-blue-700 text-black-100"><?= $total_bac3; ?></p>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md text-center">
            <h2 class="text-xl font-bold hover:text-blue-500 text-gray-700">La filière dominante</h2>
            <p class="text-4xl font-bold hover:text-blue-700 text-black-100">
            <?= isset($filiere_dominante['nom_fil']) ? $filiere_dominante['nom_fil'] : "Aucune donnée"; ?>
            </p>
            </div>

            <div class="bg-green p-6 rounded-lg shadow-md text-center">
            <h2 class="text-xl font-bold hover:text-blue-500 text-gray-700">Nombre de Chefs</h2>
            <p class="text-4xl font-bold hover:text-blue-700 text-black-100">
            <?= $total_chefs; ?>
            </p>
           
        
            </div><div class="bg-white p-6 rounded-lg shadow-md text-center">
            <h2 class="text-xl font-bold hover:text-blue-500 text-gray-700">Nombre des demandes vérifiées</h2>
            <p class="text-4xl font-bold hover:text-blue-700 text-black-100">
            <?= $total_candidature_verifie; ?>
            </p>
            </div>

        </div>
        <h2 class="text-2xl text-blue-600 font-bold hover:text-gray-700 text-center mt-10 mb-4">Répartition des Demandes par Niveau</h2>
<div class="flex justify-center">
    <canvas id="niveauChart" style="max-width: 300px; max-height: 300px; width: 100%; height: 100%;"></canvas> <!-- Forcer une taille spécifique -->
</div>
<script>
    const ctx = document.getElementById('niveauChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Licence', 'Master', 'Cycle'],
            datasets: [{
                label: 'Demandes par Niveau',
                data: [<?= $licence; ?>, <?= $master; ?>, <?= $cycle; ?>],
                backgroundColor: ['#4CAF50', '#FF9800', '#2196F3'],
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            }
        }
    });
</script>

</script>


        <h2 class="text-2xl p-6 text-blue-600 hover:text-gray-700 font-bold text-center mt-10 mb-4">Filières les plus demandées pour chaque Niveau</h2>
        
        <div class="flex justify-center gap-6">
            <!-- Graphique pour les Filières de Licence -->
            <div>
                <h3 class="text-xl font-bold text-center mb-4">Filières de Licence</h3>
                <canvas id="licenceChart" width="200" height="200"></canvas>
            </div>

            <!-- Graphique pour les Filières de Master -->
            <div>
                <h3 class="text-xl font-bold text-center mb-4">Filières de Master</h3>
                <canvas id="masterChart" width="200" height="200"></canvas>
            </div>

            <!-- Graphique pour les Filières de Cycle -->
            <div>
                <h3 class="text-xl font-bold text-center mb-4">Filières de Cycle</h3>
                <canvas id="cycleChart" width="200" height="200"></canvas>
            </div>
        </div>

        <script>
            // Filières de Licence
            const licenceCtx = document.getElementById('licenceChart').getContext('2d');
            new Chart(licenceCtx, {
                type: 'doughnut',
                data: {
                    labels: <?= json_encode(array_column($licence_filieres, 'nom_fil')) ?>,
                    datasets: [{
                        label: 'Filières de Licence',
                        data: <?= json_encode(array_column($licence_filieres, 'count')) ?>,
                        backgroundColor: ['#4CAF50', '#FF9800', '#2196F3', '#FF5733', '#900C3F'],
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    }
                }
            });

            // Filières de Master
            const masterCtx = document.getElementById('masterChart').getContext('2d');
            new Chart(masterCtx, {
                type: 'doughnut',
                data: {
                    labels: <?= json_encode(array_column($master_filieres, 'nom_fil')) ?>,
                    datasets: [{
                        label: 'Filières de Master',
                        data: <?= json_encode(array_column($master_filieres, 'count')) ?>,
                        backgroundColor: ['#4CAF50', '#FF9800', '#2196F3', '#FF5733', '#900C3F'],
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    }
                }
            });

            // Filières de Cycle
            const cycleCtx = document.getElementById('cycleChart').getContext('2d');
            new Chart(cycleCtx, {
                type: 'doughnut',
                data: {
                    labels: <?= json_encode(array_column($cycle_filieres, 'nom_fil')) ?>,
                    datasets: [{
                        label: 'Filières de Cycle',
                        data: <?= json_encode(array_column($cycle_filieres, 'count')) ?>,
                        backgroundColor: ['#4CAF50', '#FF9800', '#2196F3', '#FF5733', '#900C3F'],
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    }
                }
            });
        </script>
    </div>
</body>
</html>
