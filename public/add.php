<?php
    require_once('../include/session.php');
    include_once('../include/db.php');


    if($_SERVER["REQUEST_METHOD"] == "POST") {
        require('../snippets/add_edit_post.php');
        $num_id = $collection->findOne([],["projection" => ["num_id" => 1], "sort" => array("num_id"=>-1)]);
        $next_num_id = $num_id["num_id"] + 1;
        $query["num_id"] = $next_num_id;

        if ($error == []) {
            $res = $collection->insertOne($query);
            $collection_logs->insertOne([
                'user_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id']),
                'tur_id' => $res->getInsertedId(),
                'type' => 'tur opprettet'
            ]);
            header("Location: /?id=" . $res->getInsertedId());
        }
    }




?>

<?php
  $title = "Turbibliotek :: add";
  include('../snippets/head.php');
?>
<div class="container">
    <?php include('../snippets/header.php'); ?>
    <?php include('../snippets/add_edit_form.php'); ?>
</div>
<div style="height: 100px;"></div>

</body>
</html>
