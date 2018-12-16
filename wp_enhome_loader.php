<?php


$GLOBALS['userInfo'] = null;
$GLOBALS['userInfo'] = get_currentuserinfo(); // ACQUIRE WORDPRESS USER INFO

  // LOGIN CHECK
  //echo $requirelogin;
    if($GLOBALS['userInfo']->ID == 0 && $requirelogin == 'yes'){
      //echo 'NOT LOGGED IN';
      echo "<script>window.location.replace('wp-login.php') ;</script>";
    } elseif($requirelogin == 'yes' && $GLOBALS['userInfo']->ID != 0){
      //echo 'LOGGED IN!!';
    } else {
      //echo 'Login not required';
    }

  include_once('functions.php');
  loadStylesheet();
  echo "<script>".file_get_contents('functions.js')."</script>";  // ADD JS FUNCTIONS AT TOP OF PAGE

  loadLibraries(); // LOAD ADDITIONAL LIBRARIES NEEDED

  //echo '<script>console.log('$page');</script>'
  // ENCODE USER INFORMATION AND EMBED IN JS
    echo "<script>
      var userData = ".json_encode($GLOBALS['userInfo'], JSON_PRETTY_PRINT).";
      var userInfo = ".json_encode(get_currentuserinfo()->data, JSON_PRETTY_PRINT).";
      var userToken = '".encryptToken(json_encode(array('time'=>strtotime(date('Y-m-d H:i:s')),'id'=>$GLOBALS['userInfo']->ID)))."'
    </script>";

    echo '<script>
    var assetTree = ';
    getAssetTree();
    echo '; var assetList = ';
    getAssetList();
    echo ';  var carePlans = ';
    print_r(json_encode(getTaskLibrary()));
    echo ';';
    echo '</script>';

    //echo '<h1>'.$page.'</h1>';

    renderPage($page);    // PASS "page" VARIABLE IN 'PHP SNIPPET' ON WORDRESS

?>
