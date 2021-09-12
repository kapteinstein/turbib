<?php
    include_once('../include/session.php');
    include_once('../include/db.php');

    if($_SERVER["REQUEST_METHOD"] == "GET") {
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
            header("Location: /");
            die();
        }

        $collection->deleteOne(["_id" => $id]);
        $collection_logs->insertOne([
            'user_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id']),
            'tur_id' => $id,
            'type' => 'tur slettet'
        ]);
    }

header("Location: /");
