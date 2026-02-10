<?php
ob_start(); // Démarre la mise en tampon de sortie
require 'admin/database.php';
$db = Database::connect();

// ⚠️ Exécuter uniquement si la requête est POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Crée une commande "en cours" si aucune n'est active
    $checkOrder = $db->query("SELECT * FROM orders WHERE statut = 'en cours' LIMIT 1");
    $currentOrder = $checkOrder->fetch();

    if (!$currentOrder) {
        $stmt = $db->prepare("INSERT INTO orders (order_date, total_price, statut) VALUES (NOW(), 0, 'en cours')");
        $stmt->execute();
        $orderId = $db->lastInsertId();
    } else {
        $orderId = $currentOrder['order_id'];
    }

    foreach ($_POST as $key => $value) {
        if (strpos($key, 'qty_') === 0 && intval($value) > 0) {
            $itemId = str_replace('qty_', '', $key);
            $quantity = intval($value);

            // Récupère le prix unitaire
            $stmt = $db->prepare("SELECT price FROM items WHERE id = ?");
            $stmt->execute([$itemId]);
            $item = $stmt->fetch();

            // Enregistre dans order_items
            $insertStmt = $db->prepare("
                INSERT INTO order_items (order_id, item_id, quantity, price_unit)
                VALUES (?, ?, ?, ?)
            ");
            $insertStmt->execute([$orderId, $itemId, $quantity, $item['price']]);

            // Met à jour le stock
            $updateStock = $db->prepare("UPDATE items SET stock = stock - ? WHERE id = ?");
            $updateStock->execute([$quantity, $itemId]);

            // Met à jour le total de la commande
            $updateTotal = $db->prepare("UPDATE orders SET total_price = total_price + ? WHERE order_id = ?");
            $updateTotal->execute([$quantity * $item['price'], $orderId]);
        }
    }

    // ✅ Redirection pour éviter que le POST se répète au refresh
    header('Location: index.php');
    


    exit;
}
                $checkOrder = $db->query("SELECT * FROM orders WHERE statut = 'en cours' LIMIT 1");
                $currentOrder = $checkOrder->fetch();
                $orderItems = [];

                if ($currentOrder) {
                    $orderId = $currentOrder['order_id'];
                    $stmt = $db->prepare("
        SELECT oi.*, i.item_name
        FROM order_items oi
        JOIN items i ON oi.item_id = i.id
        WHERE oi.order_id = ?
    ");
                    $stmt->execute([$orderId]);
                    $orderItems = $stmt->fetchAll();
                }

                Database::disconnect();
?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <title>Bar brocante</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image" href="images/logo.jpg">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="container">
        <div class="en-tete">
            <img class="logo" src="images/logo.jpg" alt="">

        </div>
        <h1>Boissons</h1>
    </div>

    <div class="container parent">

        <div class="container col-md-9">
            <?php

            $db = Database::connect();
            $statement = $db->query('SELECT * FROM items');
            $items = $statement->fetchAll();

            ?>
            <div class="row">
                <?php foreach ($items as $item): ?>
                    <div class="col-sm-6 col-md-4 col-lg-2 mb-4">
                        <div class="card text-center h-100">
                            <div class="card-header"><?= htmlspecialchars($item['item_name']) ?></div>
                            <div class="card-body">
                                <form action="index.php" method="post">
                                    <div class="mb-3">
                                        <label for="<?= 'qty_' . $item['id'] ?>" class="form-label">Quantité</label>
                                        <input type="number" class="form-control" id="<?= 'qty_' . $item['id'] ?>" name="<?= 'qty_' . $item['id'] ?>" min="0" value="0">
                                    </div>
                                    <button type="submit" class="btn btn-primary">Ajouter</button>
                                </form>
                            </div>
                            <div class="card-footer text-muted">stock : <?= $item['stock'] ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div> <!--ferme row-->
        </div>

        <div class="container col-md-3">
            <div class="card">
                <div class="card-header">
                    Récapitulatif
                </div>
                <?php if ($currentOrder): ?>
                    <div class="card-body">
                        <?php foreach ($orderItems as $item): ?>
                            <p>
                                <?= htmlspecialchars($item['item_name']) ?> :
                                <?= intval($item['quantity']) ?> ×
                                <?= number_format($item['price_unit'], 2) ?> € =
                                <strong><?= number_format($item['quantity'] * $item['price_unit'], 2) ?> €</strong>
                            </p>
                        <?php endforeach; ?>
                    </div>
                    <div class="card-footer text-body-secondary">
                        Total : <strong><?= number_format($currentOrder['total_price'], 2) ?> €</strong>
                        <br>
                        <form method="post" action="valider_commande.php">
                            <button class="btn btn-success" name="valider" value="<?= $orderId ?>">Valider</button>
                            <button class="btn btn-danger" name="annuler" value="<?= $orderId ?>">Annuler</button>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="card-body">
                        <p>Aucune commande en cours</p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="card" style="margin-top: 20px;">
                <div class="card-header">
                    Calculette
                </div>
                <div class="card-body">

                </div>
                <div class="card-footer text-body-secondary">
                    À rendre: 0 €
                    <br>
                    <form action="">
                        <button class="btn btn-success">Valider</button>
                        <button class="btn btn-danger">Annuler</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


</body>

</html>

<?php
ob_end_flush(); // Envoie le contenu tamponné et termine la mise en tampon de sortie
?>