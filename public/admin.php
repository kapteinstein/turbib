<?php
    require_once('../include/session.php');
    include_once('../include/db.php');

    $user = $collection_user->findOne(["_id" => new MongoDB\BSON\ObjectID($_SESSION['user_id'])]);
    if($_SERVER["REQUEST_METHOD"] == "POST") {

        $gammelt_passord = isset($_POST['old_passwd']) ? $_POST['old_passwd'] : "";
        $new_password_1 = isset($_POST['new_passwd_1']) ? $_POST['new_passwd_1'] : NULL;
        $new_password_2 = isset($_POST['new_passwd_2']) ? $_POST['new_passwd_2'] : NULL;

        $user = $collection_user->findOne(['_id' => new MongoDB\BSON\ObjectID($_SESSION['user_id'])]);

        if (password_verify($gammelt_passord, $user['password']) == FALSE) {
            $_SESSION['status'] = 'error';
            $_SESSION['status_msg'] = 'Feil gammelt passord';
        } else if ($new_password_1 == $new_password_2 && $new_password_1 != NULL) {
            $collection_user->updateOne(
                ['_id' => new MongoDB\BSON\ObjectID($_SESSION['user_id'])],
                ['$set' => [
                    'password' => password_hash($new_password_1, PASSWORD_DEFAULT)
                    ]
                ]
            );

            $_SESSION['status'] = 'success';
            $_SESSION['status_msg'] = 'Oppdatert!';
        } else if ($new_password_1 != $new_password_2) {
            $_SESSION['status'] = 'error';
            $_SESSION['status_msg'] = 'Passordene er ulike';
        }

        header('Location: admin.php');
        die();
    }

    $title = "Turbibliotek :: Admin";
    include('../snippets/head.php');
?>

<body>
    <div class="container">
    <?php include('../snippets/header.php'); ?>
    <?php
        if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == TRUE) {
            include('../snippets/admin/user_table.php');
            echo '<div style="height: 2em;"></div>';
            include('../snippets/admin/user_add.php');
            echo '<div style="height: 2em;"></div>';
        }
    ?>

    <div class="row">
        <div class="col-12">
            <?php $user = $collection_user->findOne(["_id" => new MongoDB\BSON\ObjectID($_SESSION['user_id'])]); ?>
            <h3>Endre egen brukerinfo</h3>
            <form action='' method='post' autocomplete='off'>
                <input autocomplete='off' name='hidden' type='text' style='display:none;' />
                <table style="padding: 0.5em; border: 1px solid #e7e7e7;">
                    <tr>
                        <td>Bruker opprettet:</td>
                        <td><?php echo date("Y-M-d", $user['_id']->getTimestamp()); ?></td>
                    </tr>
                    <tr>
                        <td>Navn:</td>
                        <td><?php echo $user['name']; ?></td>
                    </tr>
                    <tr>
                        <td>Email:</td>
                        <td><input style="width: 32ch" autocomplete="disabled" type='email' name='username' placeholder='email' value=<?php echo '"'.$user['email'].'"'; ?> readonly></td>
                    </tr>
                    <tr>
                        <td>Gammelt passord:</td>
                        <td><input style="width: 32ch" value="" type='password' name='old_passwd' placeholder='gammelt passord' /></td>
                    </tr>
                    <tr>
                        <td>Nytt passord:</td>
                        <td><input style="width: 32ch" value="" type='password' name='new_passwd_1' placeholder='nytt passord' /></td>
                    </tr>
                    <tr>
                        <td>Gjenta nytt passord:</td>
                        <td><input style="width: 32ch" type='password' name='new_passwd_2' placeholder='nytt passord' /></td>
                    </tr>
                    <tr>
                        <td>

                        <?php
                            if(isset($_SESSION['status']) && $_SESSION['status'] == 'error') {
                                echo '<span style="color: red;">'.$_SESSION['status_msg'].'</span>';
                                unset($_SESSION['status']);
                                unset($_SESSION['status_msg']);
                            }
                            if(isset($_SESSION['status']) && $_SESSION['status'] == 'success') {
                                echo '<span style="color: darkgreen;">'.$_SESSION['status_msg'].'</span>';
                                unset($_SESSION['status']);
                                unset($_SESSION['status_msg']);
                            }
                        ?>

                        </td>
                        <td style="float: right"><input type='submit' name='submit' value='Update' /></td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
    </div>

    <div style="height: 100px;"></div>

</body>
</html>
