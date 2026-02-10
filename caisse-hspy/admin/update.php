<?php

// session_start();
// if (!isset($_SESSION['user_id'])) {
//     header('Location: connexion.php');
//     exit;
// }

require 'database.php';

if (!empty($_GET['id'])) {
    $id = checkInput($_GET['id']);
}

$nameError = $priceError = $categoryError = $stockError = $name = $price = $category = $stock = "";

if (!empty($_POST)) { // on a rien soumis donc le post est vide, on a utilisé que le get donc on ne rentre pas là dedans. On y rentre que si on modifie qqch pour le passer en post. à ce moment là on a un tableau $_POST qui contient les données du formulaire
    $name               = checkInput($_POST['name']);
    $price              = checkInput($_POST['price']);
    $category           = checkInput($_POST['category']);
    $stock              = checkInput($_POST['stock']);
    $isSuccess          = true;


    if (empty($name)) {
        $nameError = 'Ce champ ne peut pas être vide';
        $isSuccess = false;
    }
    if (empty($stock)) {
        $stockError = 'Ce champ ne peut pas être vide';
        $isSuccess = false;
    }
    if (empty($price)) {
        $priceError = 'Ce champ ne peut pas être vide';
        $isSuccess = false;
    }
    if (empty($category)) {
        $categoryError = 'Ce champ ne peut pas être vide';
        $isSuccess = false;
    }



    if (($isSuccess)) { // Si le formulaire est valide 
        $db = Database::connect();
        $statement = $db->prepare("UPDATE items set item_name = ?, stock = ?, price = ?, category_id = ? where id = ?");
        $statement->execute(array($name, $stock, $price, $category, $id));

        Database::disconnect();
        header("Location: index.php");
    }
} else { // premier passage dans le formulaire, on récupère les données de l'item à modifier
    $db = Database::connect();

    $statement = $db->prepare("SELECT * FROM items where id = ?");
    $statement->execute(array($id)); // c'est l'id récupéré au debut du fichier avec get
    $item = $statement->fetch();

    $name           = $item['item_name'];
    $price          = $item['price'];
    $category       = $item['category_id'];
    $stock          = $item['stock'];

    Database::disconnect();
}

function checkInput($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Bar brocante</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js"></script>
    <link href='http://fonts.googleapis.com/css?family=Holtwood+One+SC' rel='stylesheet' type='text/css'>
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
            <div class="col-sm-6">
                <h1><strong>Modifier un item</strong></h1>
                <br>
                <form class="form" action="update.php?id=<?php echo $_GET['id']; ?>" role="form" method="post" >
                    <br>
                    <div>
                        <label class="form-label" for="name">Nom:</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Nom" value="<?php echo $name; ?>">
                        <span class="help-inline"><?php echo $nameError; ?></span>
                    </div>
                    <br>
                    <div>
                        <label class="form-label" for="price">Prix: (en €)</label>
                        <input type="number" step="0.50" class="form-control" id="price" name="price" placeholder="Prix" value="<?php echo $price; ?>">
                        <span class="help-inline"><?php echo $priceError; ?></span>
                    </div>
                    <br>
                    <div>
                        <label class="form-label" for="category">Catégorie:</label>
                        <select class="form-control" id="category" name="category">
                            <?php
                            $db = Database::connect();
                            foreach ($db->query('SELECT * FROM categories') as $row) {
                                if ($category == $row['id']) {
                                    echo '<option selected="selected" value="' . $row['id'] . '">' . $row['category_name'] . '</option>';
                                } else {
                                    echo '<option value="' . $row['id'] . '">' . $row['category_name'] . '</option>';
                                }
                            }
                            Database::disconnect();
                            ?>
                        </select>
                        <span class="help-inline"><?php echo $categoryError; ?></span>
                    </div>
                    <br>
                    <div>
                        <label class="form-label" for="stock">Présents en stock :</label>
                        <input type="number" step="1" class="form-control" id="stock" name="stock" placeholder="Stock" value="<?php echo $stock; ?>">
                        <span class="help-inline"><?php echo $stockError; ?></span>
                    </div>
                    <br>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-success"><span class="bi-pencil"></span> Modifier</button>
                        <a class="btn btn-primary" href="index.php"><span class="bi-arrow-left"></span> Retour</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>