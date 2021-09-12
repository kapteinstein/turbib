<?php
    include_once('../include/session.php');
    include_once('../include/db.php');

    $id = isset($_GET["id"]) ? $_GET["id"] : NULL;

    try {
        $id = new MongoDB\BSON\ObjectId("$id");
    } catch (\Throwable $th) {
        header("Location: /");
        die();
    }



    $tur = $collection->findOne(["_id" => $id]);

    if ($tur != NULL) {

        $query = [
            "_id" => $id
        ];
        if ($tur['dato_undervist'] == NULL) {
            $options = [
                '$set' => [
                    "dato_undervist" => array(new MongoDB\BSON\UTCDateTime(strtotime("midnight")*1000)),
                    "sist_undervist" => new MongoDB\BSON\UTCDateTime(strtotime("midnight")*1000),
                ]
            ];
        } else {
            $options = [
                '$addToSet' => [
                    "dato_undervist" => new MongoDB\BSON\UTCDateTime(strtotime("midnight")*1000),
                ],
                '$set' => [
                    "sist_undervist" => new MongoDB\BSON\UTCDateTime(strtotime("midnight")*1000),
                ]
            ];
        }
        $count = 0;
        foreach ($tur['dato_undervist'] as $date) {
            $count++;
        }
        $collection->updateOne($query, $options);
        $tur = $collection->findOne(["_id" => $id]);
        $count_new = 0;
        foreach ($tur['dato_undervist'] as $date) {
            $count_new++;
        }
        if ($count != $count_new) {
            $collection_logs->insertOne([
                'user_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id']),
                'tur_id' => $id,
                'type' => 'sist undervist idag'
            ]);
        }
    }
    header("Location: /?id=".$id);
