<?php
    session_start();
    require_once("../include/db.php");
    include_once('../include/mail.php');

    if(isset($_SESSION['logged_in'])){
        header("location: /");
        exit();
    }

    $key = isset($_GET['key']) ? $_GET['key'] : '000000000000000000000000';
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $key = isset($_POST['key']) ? $_POST['key'] : '000000000000000000000000';
    }
    $user = $collection_user->findOne([
        'active' => 1,
        'registered' => 1,
        'register_key' => $key
    ]);


    if ($_SERVER["REQUEST_METHOD"] == "POST" && $user == NULL) {
        $email = isset($_POST['email']) ? $_POST['email'] : NULL;
        $user = $collection_user->findOne([
            'active' => 1,
            'registered' => 1,
            'email' => $email
        ]);
        if ($user != NULL) {
            $register_key = $user['register_key'];

            $link = "https://turbib.qwd.no/reset_password.php?key=$register_key";

            $to      = $email;
            $subject = 'Reset passord';
            $message = "Hei!\r\n\r\nFølg denne linken for å gjenoprette ditt passord til din bruker på NTNUI Swing og folkedans sitt turbibliotek.\r\nDersom du ikke har bedt om gjennoprettelse av passord, ta kontakt med webansvarlig.\r\n\r\n$link\r\n\r\nmvh\r\nNTNUI Swing og Folkedans\r\nturbiblioteket";
            $headers = array(
                'From' => 'Turbiblioteket <turbiblioteket@gmail.com>',
                'Reply-To' => 'turbiblioteket@gmail.com',
                'X-Mailer' => 'PHP/' . phpversion()
            );

            $mail->addAddress($to);

            $mail->Subject = $subject;
	    $mail->Body    = $message;

	    $success = $mail->send();
            // $success = mail($to, $subject, $message, $headers);
        }

        $_SESSION['reset:status'] = 'success';
        $_SESSION['reset:status_msg'] = 'Email er sendt dersom brukeren eksisterer';

        header("location: reset_password.php");
        die();
    }

    if($_SERVER["REQUEST_METHOD"] == "POST" && $user != NULL) {
        $password_1 = isset($_POST['new_passwd_1']) ? $_POST['new_passwd_1'] : NULL;
        $password_2 = isset($_POST['new_passwd_2']) ? $_POST['new_passwd_2'] : NULL;

        if ($password_1 != $password_2) {
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
                    'password' => password_hash($password_1, PASSWORD_DEFAULT),
                    'register_key' => $register_key
                    ]
                ]
            );

            $_SESSION['status'] = 'success';
            $_SESSION['status_msg'] = 'Passord endret!';
        }
        header('Location: login.php');
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
  <title>Turbibliotek :: Reset password</title>

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
        <div class="col-md-2"></div>
        <div class="col-md-8" style="text-align: center;">
            <h3>NTNUI Swing og Folkedans - Turbiblioteket</h3>
            <p>Vekommen til NTNUI Swing og Folkedans sitt turbibliotek.</p>
            <?php
                if ($user == NULL) {
                    echo '<p>Start gjenoppretting av passordet ved å fylle inn mailadressen
                    du brukte under registrering.<br />Du vil deretter motta en mail med
                    videre instruksjoner</p>';
                }
            ?>
        </div>
        <div class="col-md-2"></div>

    </div>
    <div class="row">
        <div class="col-12">
            <?php
                if (isset($_SESSION['reset:status']) && $_SESSION['reset:status'] == 'success') {
                    echo "<div style='padding-top: 2em;margin:auto;text-align:center;color: darkgreen'>" . $_SESSION['reset:status_msg'] . "</div>";
                    unset($_SESSION['reset:status']);
                    unset($_SESSION['reset:status_msg']);
                } else {
                    if ($user == NULL) {
                        include('../snippets/reset_password_prompt.php');
                    } else {
                        include('../snippets/reset_password_form.php');
                    }
                }
            ?>
            <div style="height: 1em;"></div>
            <div style="text-align: center; margin: auto;"><a href=login.php>login</a></div>
        </div>
    </div>
    </div>
</body>
</html>
