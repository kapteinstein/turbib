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

    $user = $collection_user->findOne(['_id' => $id]);

    if ($user['_id'] == $_SESSION['user_id']) {
        header('Location: admin.php');  // cant change its own status
        die();
    }

    $new_status = ($user['is_admin'] == 1) ? 0 : 1;
    $collection_user->updateOne(["_id" => $id], ['$set' => ['is_admin' => $new_status]]);

    header('Location: admin.php');
    die();
?>