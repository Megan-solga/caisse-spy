<?php
// session_start();
// if (!isset($_SESSION['user_id'])) {
//     header('Location: connexion.php');
//     exit;
// }
?>
<!DOCTYPE html>
<html>

<head>
    <title>Bar brocante</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image" href="../images/logo.jpg">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js"></script>
    <link href='https://fonts.googleapis.com/css?family=Holtwood+One+SC' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <div class="d-flex justify-content-end p-3">
        <a href="logout.php" class="btn btn-outline-danger">
            <span class="bi-box-arrow-right"></span> Déconnexion
        </a>
    </div>
    <div class="container">
        <div class="en-tete">
            <img class="logo" src="../images/logo.jpg" alt="">
            <h1>Administration</h1>
        </div>
    </div>
    <div class="container admin">
        <div class="row">
            <h1><strong>Liste des items </strong><a href="insert.php" class="btn btn-success btn-lg"><span
                        class="bi-plus"></span> Ajouter</a></h1>
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Prix</th>
                        <th>Catégorie</th>
                        <th>Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    require 'database.php';
                    $db = Database::connect(); // la connexion est stockée dans la variable $db
                    $statement = $db->query('SELECT items.id, items.item_name, items.stock, items.price, categories.category_name AS category FROM items LEFT JOIN categories ON items.category_id = categories.id ORDER BY items.id DESC'); // On récupère tous les items de la base de données
                    while ($item = $statement->fetch()) { // On affiche chaque item
                        echo '<tr>';
                        echo '<td>' . $item['item_name'] . '</td>';
                        echo '<td>' . number_format((float)$item['price'], 2, '.', '') . ' €</td>';
                        echo '<td>' . $item['category'] . '</td>';
                        echo '<td>' . $item['stock'] . '</td>';
                        echo '<td width=340>';
                        echo '<a class="btn btn-primary" href="update.php?id=' . $item['id'] . '"><span class="bi-pencil"></span> Modifier</a> ';
                        echo '<a class="btn btn-danger" href="delete.php?id=' . $item['id'] . '"><span class="bi-x"></span> Supprimer</a>';
                        echo '</td>';
                        echo '</tr>';
                    }
                    Database::disconnect(); // On se déconnecte de la base de données                     
                    ?>


                    <!-- tableau basé sur cette version statique -->
                    <!-- <tr>
                        <td>Item 1</td>
                        <td>Description 1</td>
                        <td>Prix 1</td>
                        <td>Catégorie 1</td>
                        <td width=340>
                            <a class="btn btn-secondary" href="view.php?id=1"><span class="bi-eye"></span> Voir</a>
                            <a class="btn btn-primary" href="update.php?id=1"><span class="bi-pencil"></span>
                                Modifier</a>
                            <a class="btn btn-danger" href="delete.php?id=1"><span class="bi-x"></span> Supprimer</a>
                        </td>
                    </tr>
                    <tr>
                        <td>Item 2</td>
                        <td>Description 2</td>
                        <td>Prix 2</td>
                        <td>Catégorie 2</td>
                        <td width=340>
                            <a class="btn btn-secondary" href="view.php?id=2"><span class="bi-eye"></span> Voir</a>
                            <a class="btn btn-primary" href="update.php?id=2"><span class="bi-pencil"></span> Modifier</a>
                            <a class="btn btn-danger" href="delete.php?id=2"><span class="bi-x"></span> Supprimer</a>
                        </td>
                    </tr> -->


                </tbody>
            </table>
        </div>
    </div>
</body>

</html>