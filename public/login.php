<?php
    session_start();
    require_once("../include/db.php");

    if(isset($_SESSION['logged_in'])){
        header("location: /");
        exit();
    }

    if($_SERVER["REQUEST_METHOD"] == "POST") {
        $redirect = '/';
        if($_POST['location'] != '') {
            $redirect = $_POST['location'];
        }
        $email = isset($_POST['username']) ? $_POST['username'] : "";  // username
        $password = isset($_POST['password']) ? $_POST['password'] : "";


        $user = $collection_user->findOne([
            'email' => $email
        ]);

        //echo "<pre>"; print_r($user); print_r($email); print_r($password); echo "</pre>";
        if($user != NULL && password_verify($password, $user['password']) && $user['active'] == TRUE) {
            $_SESSION['logged_in'] = true; //set you've logged in
            $_SESSION['last_activity'] = time(); //your last activity was now, having logged in.
            $_SESSION['expire_time'] = 1*60*60; //expire time in seconds: 1 hour
            $_SESSION['user_id'] = $user['_id'];

            if (isset($user['is_admin']) && $user['is_admin'] == 1) {
                $_SESSION['is_admin'] = true;
            } else {
                $_SESSION['is_admin'] = false;
            }

            $collection_user->updateOne(
                [
                    '_id' => new MongoDB\BSON\ObjectId($user['_id'])
                ],
                [
                    '$set' => [
                        'last_login' => new MongoDB\BSON\UTCDateTime()
                    ]
                ]
            );

            header("location: ".$redirect);
            exit();
        } else {
            $_SESSION['status'] = 'error';
            $_SESSION['status_msg'] = 'invalid';
        }
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
  <link rel="icon" type="image/png" href="/static/img/favicon-32x32.png" sizes="32x32" />
  <link rel="icon" type="image/png" href="/static/img/favicon-16x16.png" sizes="16x16" />
  <meta name="description" content="NTNUI Swing og Folkedans - Turbiblioteket">
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
    a { color: #004070; }
    a:hover { color: #B44444; }
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
        <div class="col-md-4"></div>
        <div class="col-md-4">
            <form action="" method="POST">
                <?php
                echo '<input type="hidden" name="location" value="';
                if(isset($_GET['location'])) {
                    echo htmlspecialchars($_GET['location']);
                }
                echo '" /><br />';
                ?>
                <table style="padding: 0.5em; margin: auto; margin-top: 1em; border: 1px dotted #333;">
                    <tr>
                        <td>Email:</td>
                        <td><input style="width: 100%" type="email" name="username" placeholder="email"></td>
                    </tr>
                    <tr>
                        <td>Passord:</td>
                        <td><input style="width: 100%" type="password" name="password" placeholder="passord"></td>
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
                        <td style="text-align: right">
                            <input type="submit" name="submit" value="login">
                        </td>
                    </tr>
                </table>
            </form>
            <div style="height: 1em;"></div>
            <div style="text-align: center; margin: auto;"><a href=reset_password.php>glemt passord</a></div>
        </div>
        <div class="col-md-4"></div>
    </div>
    </div>

    <div style="height: 100px;"></div>

</body>
</html>
