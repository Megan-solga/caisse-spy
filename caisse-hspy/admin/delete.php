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

if (!empty($_POST)) {
    $id = checkInput($_POST['id']);
    $db = Database::connect();
    $statement = $db->prepare("DELETE FROM items WHERE id = ?");
    $statement->execute(array($id));
    Database::disconnect();
    header("Location: index.php");
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
            <h1><strong>Supprimer un item</strong></h1>
            <br>
            <form class="form" action="delete.php" role="form" method="post">
                <br>
                <input type="hidden" name="id" value="<?php echo $id; ?>" />
                <p class="alert alert-warning">Etes vous sur de vouloir supprimer ?</p>
                <div class="form-actions">
                    <button type="submit" class="btn btn-warning">Oui</button>
                    <a class="btn btn-secondary" href="index.php">Non</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>