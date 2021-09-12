<?php
    require_once('../include/session.php');
    include_once('../include/db.php');


    if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] == FALSE) {
        http_response_code(404);
        die();
    }
    $id = isset($_GET['user_id']) ? $_GET['user_id'] : '000000000000000000000000';
    try {
        $id = new MongoDB\BSON\ObjectId("$id");
    } catch (\Throwable $th) {
        $id = new MongoDB\BSON\ObjectId('000000000000000000000000');
    }

    if ($id == $_SESSION['user_id']) {
        header('Location: admin.php');  // cant change its own status
        die();
    }

    $user = $collection_user->findOne(['_id' => $id]);
    if ($user['active']) {
        $collection_user->updateOne(["_id" => $id], ['$set' => ['active' => 0]]);
    } else {
        $collection_user->updateOne(["_id" => $id], ['$set' => ['active' => 1]]);
    }
    header('Location: admin.php');
    die();
?>