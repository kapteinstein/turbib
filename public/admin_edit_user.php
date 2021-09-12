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

    $title = "Turbibliotek :: Admin";
    include('../snippets/head.php');
?>

<body>
    <div class="container">
    <?php include('../snippets/header.php'); ?>
    <div class="row">
        <div class="col-1"></div>
        <div class="col-10 gray-table">
            <form action="" method="post" autocomplete="off">

            </form>
            <table>
            <tr>
                <th>email</th>
                <th>navn</th>
                <th>admin</th>
                <th>oprettet</th>
                <th>sist login</th>
                <th>verktøy</th>
            </tr>
            <?php
            $users = $collection_user->find([]);
            foreach ($users as $user) {
                echo '<tr>';
                echo '<td>'.$user['email'].'</td>';
                echo '<td>'.$user['name'].'</td>';
                if ($user['is_admin'] == 1) {
                    echo '<td><i>ja</i></td>';
                } else {
                    echo '<td></td>';
                }
                echo '<td>'.date("Y-M-d", $user['_id']->getTimestamp()).'</td>';
                echo '<td>'.$user['last_login']->toDateTime()->format('Y-M-d').'</td>';

                echo '<td><small>';
                echo '[<a href=admin/disable_user.php?user_id='.$user['_id'].'>disable</a>] ';
                echo '[<a href=admin/edit_user.php?user_id='.$user['_id'].'>edit</a>] ';
                echo '[<a href=admin/toggle_admin.php?user_id='.$user['_id'].'>toggle admin</a>]';
                echo '<small></td>';
                echo '</tr>';
            }
            ?>
            </table>
        </div>
        <div class="col-1"></div>
    </div>
    </div>
</body>
