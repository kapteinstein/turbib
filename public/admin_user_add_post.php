<?php
    require_once('../include/session.php');
    include_once('../include/db.php');
    include_once('../include/mail.php');


    if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] == FALSE) {
        http_response_code(404);
        die();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = isset($_POST['new_user_email']) ? $_POST['new_user_email'] : NULL;
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['admin:register:status'] = 'error';
            $_SESSION['admin:register:status_msg'] = 'invalid';
            header("location: admin.php");
            die();
        }

        $count = $collection_user->count([
            'email' => $email
        ]);

        if ($count != 0) {
            $_SESSION['admin:register:status'] = 'error';
            $_SESSION['admin:register:status_msg'] = 'duplicate';
            header("location: admin.php");
            die();
        }

        $register_key = sha1($email.strval(time()));

        $collection_user->insertOne([
            'email' => $email,
            'name' => NULL,
            'password' => NULL,
            'is_admin' => 0,
            'active' => 0,
            'registered' => 0,
            'failed_login_count' => 0,
            'last_login' => NULL,
            'register_key' => $register_key
        ]);


        $link = "https://turbib.qwd.no/register.php?key=$register_key";

        $to      = $email;
        $subject = 'Registrering';
        $message = "Hei!\r\n\r\nDu har blitt invitert til NTNUI Swing og folkedans sitt turbibliotek. Vennligst benytt linken nedenfor til registrering.\r\nIgnorer denne mailen dersom du har mottatt denne ved en feiltagelse.\r\n\r\n$link\r\n\r\nmvh\r\nNTNUI Swing og Folkedans\r\nturbiblioteket";
        $headers = array(
            'From' => 'Turbiblioteket <turbiblioteket@gmail.com>',
            'Reply-To' => 'turbiblioteket@gmail.com',
            'X-Mailer' => 'PHP/' . phpversion()
        );

	$mail->addAddress($to);

        $mail->Subject = $subject;
        $mail->Body    = $message;

        $success = $mail->send();
        //$success = mail($to, $subject, $message, $headers);
        if (!$success) {
            $_SESSION['admin:register:status'] = 'error';
            $_SESSION['admin:register:status_msg'] = error_get_last()['message'];
        } else {
            $_SESSION['admin:register:status'] = 'success';
            $_SESSION['admin:register:status_msg'] = 'suksess';
        }

        header("location: admin.php");
        die();
    }
    die();
?>
