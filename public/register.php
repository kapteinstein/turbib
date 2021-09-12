<?php
    session_start();
    require_once("../include/db.php");

    if(isset($_SESSION['logged_in'])){
        header("location: /");
        exit();
    }

    $key = isset($_GET['key']) ? $_GET['key'] : NULL;

    $user = $collection_user->findOne([
        'active' => 0,
        'registered' => 0,
        'register_key' => $key
    ]);
    if ($user == NULL) {
        header('Location: /');
        die();
    }

    if($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = isset($_POST['new_name']) ? $_POST['new_name'] : $user['name'];
        $password_1 = isset($_POST['new_passwd_1']) ? $_POST['new_passwd_1'] : NULL;
        $password_2 = isset($_POST['new_passwd_2']) ? $_POST['new_passwd_2'] : NULL;

        if ($name == NULL) {
            $_SESSION['status'] = 'error';
            $_SESSION['status_msg'] = 'Ugyldig navn';
        } else if ($password_1 != $password_2) {
            $_SESSION['status'] = 'error';
            $_SESSION['status_msg'] = 'Passordene er ulike';
        } else if ($password_1 == NULL) {
            $_SESSION['status'] = 'error';
            $_SESSION['status_msg'] = 'Ugyldig passord';
        } else {
            $register_key = sha1($user['email'].strval(time()));
            $collection_user->updateOne(
                ['_id' => new MongoDB\BSON\ObjectID($user['_id'])],
                ['$set' => [
                    'name' => $name,
                    'password' => password_hash($password_1, PASSWORD_DEFAULT),
                    'active' => 1,
                    'registered' => 1,
                    'is_admin' => 0,
                    'failed_login_count' => 0,
                    'last_login' => NULL,
                    'register_key' => $register_key
                    ]
                ]
            );

            $_SESSION['status'] = 'success';
            $_SESSION['status_msg'] = 'Bruker registrert';
        }
        header('Location: register.php?key='.$_GET['key']);
        die();
    }
?>

<!doctype html>
<html lang="en">
<!--
Turbiblioteket til NTNUI Swing og Folkedans.
Nettside laget av Erik Liodden 2019.
-->
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta content="text/html">
  <meta name="description" content="NTNUI Swing og Folkedans - Turbiblioteket">
  <link rel="icon" type="image/png" href="/static/img/favicon-32x32.png" sizes="32x32" />
  <link rel="icon" type="image/png" href="/static/img/favicon-16x16.png" sizes="16x16" />
  <title>Turbibliotek :: Login</title>

  <!-- Add additional CSS in static file -->
  <link rel="stylesheet" href="static/css/bootstrap-grid.min.css">
  <style>
    html, body {
        margin-bottom: 0;
        margin-top: 0;
        height: 100%;
    }
    body {
        color: #333;
        font-family: Georgia, serif;
        font-size: 14px;
        -moz-osx-font-smoothing: grayscale;
        -webkit-font-smoothing: antialiased;
    }
    </style>
</head>

<body>
    <div class="container">
    <div class="row">
        <div class="col-12" style="text-align: center;">
            <h3>NTNUI Swing og Folkedans - Turbiblioteket</h3>
            <p>Vekommen til NTNUI Swing og Folkedans sitt turbibliotek.</p>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <form action="" method="post" autocomplete="disabled">
                <table style="padding: 0.5em; margin: auto; margin-top: 1em; border: 1px dotted #333;">
                    <tr>
                        <td>Fullt navn:</td>
                        <td><input style="width: 32ch" autocomplete="disabled" type='text' name='new_name' placeholder='Fullt navn' /></td>
                    </tr>
                    <tr>
                        <td>Email:</td>
                        <td><input style="width: 32ch" autocomplete="disabled" type='email' name='username' placeholder='Fullt navn' value=<?php echo '"'.$user['email'].'"'; ?> readonly></td>
                    </tr>
                    <tr>
                        <td>Passord:</td>
                        <td><input style="width: 32ch" autocomplete="disabled" type='password' name='new_passwd_1' placeholder='passord' /></td>
                    </tr>
                    <tr>
                        <td>Gjenta passord:</td>
                        <td><input style="width: 32ch" autocomplete="disabled" type='password' name='new_passwd_2' placeholder='passord' /></td>
                    </tr>
                    <tr>
                        <td>
                        <?php
                            if (isset($_SESSION['status']) && $_SESSION['status'] == 'error') {
                                echo "<span style='color: red'>" . $_SESSION['status_msg'] . "</span>";
                                unset($_SESSION['status']);
                                unset($_SESSION['status_msg']);
                            } else if (isset($_SESSION['status']) && $_SESSION['status'] == 'success') {
                                echo "<span style='color: darkgreen'>" . $_SESSION['status_msg'] . "</span>";
                                unset($_SESSION['status']);
                                unset($_SESSION['status_msg']);
                            }
                        ?>
                        </td>
                        <td style="text-align: right"><input type="submit" name="submit" value="registrer"></td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
    </div>
</body>
</html>
