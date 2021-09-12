<?php
include_once("../include/session.php");
include_once("../include/db.php");

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
    $notes = isset($_POST['notes']) ? $_POST['notes'] : $tur['notes'];
    $collection->updateOne(
        [
            "_id" => $id
        ],
        [
            '$set' => [
                'notes' => $notes
            ]
        ]
    );
    $collection_logs->insertOne([
        'user_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id']),
        'tur_id' => $id,
        'type' => 'notater endret'
    ]);
    header("Location: /?id=". $id);
    die();
}

?>

<?php
  $title = "Turbibliotek :: comment";
  include('../snippets/head.php');
?>
<body>

<div class="container">
    <?php include('../snippets/header.php'); ?>
    <div class="row">
        <div class="col-2"></div>
        <div class="col-8">
            <?php echo '<h3 style="text-align: center;">Kommentarer<br /><small>'.$tur['num_id'].' - '.$tur["name"].'</small></h3>'; ?>

            <form id="comment_form" action="" method="POST">
                <textarea name="notes" form="comment_form" class="textarea_comment input_edit"><?php echo $tur['notes'];?></textarea>
                <input style="float: right;" type="submit" name="submit" value="done" />
            </form>
        </div>
        <div class="col-2"></div>
    </div>
</div>

<div style="height: 100px;"></div>


</body>
</html>
