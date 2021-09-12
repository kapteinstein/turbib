<?php
    session_start();

    if(!isset($_SESSION['logged_in'])){
        header("location: /login.php?location=" . urlencode($_SERVER['REQUEST_URI']));
        die();
    }

    if($_SESSION['last_activity'] < time()-$_SESSION['expire_time']) { //have we expired?
        //redirect to logout.php
        header('Location: /logout.php');
        die();
    } else{ //if we haven't expired:
        $_SESSION['last_activity'] = time(); //this was the moment of last activity.
    }
