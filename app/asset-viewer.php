<?php

  if($_GET['userAssetId']){
      // GET ASSET INFORMATION
        $userAssetId = $_GET['userAssetId'];
        renderAssetInformation(getAssetIdFromUserAssetId($userAssetId));
      // GET CARE PLAN INFORMATION


  } elseif($_GET['assetId']){
    renderAssetInformation($_GET['assetId']);

  } else {
    echo '<i>Please identify which asset to display</i>';
  }

  function renderAssetInformation($assetId){
    $assetInformation = getAssetInformation($assetId);
    echo '<pre>';
    print_r($assetInformation);
    echo '</pre>';
  }

  function getAssetIdFromUserAssetId($userAssetId){
    $qryStmt = "SELECT asset_id FROM user_assets WHERE user_assets.id = $userAssetId;";
    //echo $qryStmt;
    $assetId = queryStmtToArray($qryStmt)[0]['asset_id'];
    return $assetId;
  }

  function getAssetInformation($assetId){
    $qryStmt = "SELECT branch_id, assets.name, asset_makes.name as asset_make, model_no, model_year, detail
      FROM assets
      LEFT JOIN asset_makes ON assets.make_id = asset_makes.id
      WHERE assets.id = $assetId;";
      //echo $qryStmt;
    $assetData = queryStmtToArray($qryStmt);
    return $assetData;
  }
?>
