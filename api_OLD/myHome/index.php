<?php include_once("../../../functions.php");?>

<?php
  $dbh = db_connect();
  $returnArray = array();
  $action = getRequiredParameter('action');

  $returnArray['action'] = $action;
  //$action = $action;
//echo $action;
  if($action == 'addAsset'){
    $assetId = getRequiredParameter('assetId');
    $insertStmt = "INSERT INTO enhome.user_assets (user_id, username, asset_id, installed_at, added_at, updated_at) VALUES (".$_SESSION['userdata']->id.", '".$_SESSION['userdata']->username."',".$assetId.", now(), now(), now())";
    //echo $insertStmt;
    $returnArray['SQL stmt'] = $insertStmt;
    $dbh->exec($insertStmt);
    //echo 'done';
  }
  returnArrayAsJSON($returnArray);
  //print_r($returnArray);
  //print_r(json_encode($returnArray), JSON_PRETTY_PRINT);
 ?>
