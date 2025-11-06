<?php
    date_default_timezone_set ("Europe/Moscow");
    //include_once 'logWriter.php';

    // establish mySOLi connection & database selection
    $host = getenv('DB_HOST') ?: 'db';
    $user = getenv('DB_USER') ?: 'quiz_user';
    $password = getenv('DB_PASSWORD') ?: 'quiz_password';
    $database = getenv('DB_NAME') ?: 'quiz_db';
    
    $db = new mysqli($host, $user, $password, $database);

    // db query settings
    $db->set_charset('utf8');
    if ($db->connect_errno) die('Could not connect: '.$mysqli->connect_error);

    // make a query to the database
    function db_query ($query) {
        global $db;
        $res=$db->query ($query);
        if (!$res) throw new Exception ($db->error);
        return $res;
    }

    function db_multiQuery ($query) {
        global $db;
        $res=$db->multi_query ($query);
        if (!$res) throw new Exception ($db->error);
        return $res;
    }
