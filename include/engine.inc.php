<?php
    header("Content-Type: text/html; charset=UTF-8");
    ini_set("display_errors", 1);
    ini_set("error_reporting",E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_NOTICE);
    include_once "/web/gomting002/www2.toonypaper.com/include/path.info.php";
    include_once __DIR_PATH__."/include/session.info.php";
    include_once __DIR_PATH__."/include/mysql.info.php";
    include_once __DIR_PATH__."/include/mysql.class.php";
    include_once __DIR_PATH__."/include/lib.class.php";
    include_once __DIR_PATH__."/include/paging.class.php";
    include_once __DIR_PATH__."/include/modeling.class.php";
    include_once __DIR_PATH__."/include/mailSender.class.php";
    include_once __DIR_PATH__."/include/fileUploader.class.php";
?>