<?php
    include_once('../include/session.php');
    include_once('../include/db.php');

    include_once('../include/func.php');

    $id = isset($_GET['id']) ? $_GET['id'] : '000000000000000000000000';
    try {
        $id = new MongoDB\BSON\ObjectId("$id");
    } catch (\Throwable $th) {
        $id = new MongoDB\BSON\ObjectId('000000000000000000000000');
    }

    try {
        $tur = $collection->findOne(['_id' => $id]);
    } catch (\Throwable $th) {
        $tur = NULL;
    }

    if ($tur == NULL) {
        header("Location: /?id=". $id);
        die();
    }


    if($_SERVER["REQUEST_METHOD"] == "POST") {
        require('../snippets/add_edit_post.php');
        if ($error == []) {
            $res = $collection->updateOne(['_id' => $tur['_id']], ['$set' => $query]);
            $collection_logs->insertOne([
                'user_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id']),
                'tur_id' => $tur['_id'],
                'type' => 'generell info om tur endret'
            ]);
            header("Location: /?id=" . $tur['_id']);
        }
    }
?>

<?php
  $title = "Turbibliotek :: edit";
  include('../snippets/head.php');
?>
<body>
<div class="container">
    <?php include('../snippets/header.php'); ?>
    <?php include('../snippets/add_edit_form.php'); ?>
</div>

<div style="height: 100px;"></div>

</body>
</html>
