<?php
$GLOBALS['userInfo'] = get_currentuserinfo(); // ACQUIRE WORDPRESS USER INFO

  include_once('functions.php');
  echo "<script>var userData = ".json_encode($GLOBALS['userInfo'])."; var userToken = '".encryptToken(json_encode(array('time'=>strtotime(date('Y-m-d H:i:s')),'id'=>$GLOBALS['userInfo']->id)))."';</script>";


  echo "<script>
    var userInfo = ".json_encode(get_currentuserinfo()->data, JSON_PRETTY_PRINT).";
    var userToken = '".encryptToken(json_encode(array('time'=>strtotime(date('Y-m-d H:i:s')),'id'=>$GLOBALS['userInfo']->ID)))."'
    </script>";

    renderPage($page);    // PASS "page" VARIABLE IN 'PHP SNIPPET' ON WORDRESS

?>
