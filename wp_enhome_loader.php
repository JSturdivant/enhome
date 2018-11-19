<?php
// ADD STYLES
echo '<style>
    .assetCard {
        background-color: #fafafa;
        border-radius: 20px;
        border: 1px solid #eee;
        box-shadow: 3px 3px 3px #fff;
        padding: 10px;
        margin: 10px;
        overflow-x: hidden;
        text-overflow: ellipsis;
        -webkit-transition: 0.5s ease-out;
        -moz-transition: 0.5s ease-out;
        -o-transition: 0.5s ease-out;
        transition: 0.5s ease-out;
    }

    .assetCard:hover {
        background-color: #fafafa;
        border: 1px solid #ccc;
        box-shadow: 3px 3px 3px #ccc;
    }

    .assetPage {
        background-color: #fff;
        border-radius: 20px;
        border: 1px solid #eee;
        box-shadow: 3px 3px 3px #fff;
        padding: 10px;
        margin: 10px;
        overflow-x: hidden;
        text-overflow: ellipsis;
        -webkit-transition: 0.5s ease-out;
        -moz-transition: 0.5s ease-out;
        -o-transition: 0.5s ease-out;
        transition: 0.5s ease-out;
    }

</style>';


$GLOBALS['userInfo'] = get_currentuserinfo(); // ACQUIRE WORDPRESS USER INFO
  include_once('functions.php');
    echo "<script src='functions.js'></script>";
  loadLibraries();
  //loadBootstrap();
  echo "<script>var userData = ".json_encode($GLOBALS['userInfo'])."; var userToken = '".encryptToken(json_encode(array('time'=>strtotime(date('Y-m-d H:i:s')),'id'=>$GLOBALS['userInfo']->id)))."';</script>";

  echo "<script>
    console.log('$page');
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

    echo '<h1>'.$page.'</h1>';
    
    renderPage($page);    // PASS "page" VARIABLE IN 'PHP SNIPPET' ON WORDRESS

?>
