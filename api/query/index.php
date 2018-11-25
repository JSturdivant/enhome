<?php
    $_SESSION['loadHeader'] = false;
    include_once('../../functions.php');

    $returnData = array(
        'data' => queryStmtToArray($_GET['q'])
    );

    returnArrayAsJSON($returnData);

?>
