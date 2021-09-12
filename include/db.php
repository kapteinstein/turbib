<?php
    require_once("../vendor/autoload.php");
    try {
        // maa fikse passwd auth her
        $mongodb = (new MongoDB\Client)->turbib;
        $collection = $mongodb->tur;
        $collection_meta = $mongodb->meta;
        $collection_user = $mongodb->user;
        $collection_logs = $mongodb->log;

    } catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
