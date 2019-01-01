<?php

  $userAssetId = $_GET['userAssetId'];
  $taskId = $_GET['taskId'];
  if($userAssetId && $taskId){
      // GET TASK INFORMATION
        echo '<hr><h2>Task Information</h2>';
        renderTaskInformation(getTaskInformation($taskId,$userAssetId));

      // GET TASK HISTORY
        echo '<hr><h2>Task History</h2>';
        renderTaskHistory(getTaskHistory($taskId, $userAssetId));

      // GET ASSET INFORMATION
        $userAssetInformation = getUserAssetInformationFromUserAssetId($userAssetId);
        renderAssetInformation($userAssetInformation['asset_id'],$userAssetInformation);

  } elseif($taskId){

    // GET TASK INFORMATION
      echo '<hr><h2>Task Information</h2>';
      renderTaskInformation(getTaskInformation($taskId,null));


  } else {
    echo '<i>Please identify which asset to display</i>';
  }

  function getTaskInformation($taskId, $userAssetId = null){
    $qryStmt = "SELECT tasks.id, tasks.name as taskName, task_types.name as taskType, description, importance, frequency_days as frequencyDays
      FROM tasks
      LEFT JOIN task_types ON tasks.type_id = task_types.id
      WHERE tasks.id = $taskId";
      //echo $qryStmt;
      $taskInformation = queryStmtToArray($qryStmt)[0];
      $taskInformation['description'] = json_decode($taskInformation['description'], true);
      $taskInformation['description']['images'] = json_decode($taskInformation['description']['images'], true);

      return $taskInformation;
  }

  function renderTaskInformation($taskInformation){

    echo '<pre>';
    print_r($taskInformation);
    echo '</pre>';
  }

  function getTaskHistory ($taskId, $userAssetId){
    $qryStmt = "SELECT tasks.id, tasks.name as taskName,
      	task_completion.completed_at,
      	task_completion.started_at,
      	task_completion.user_task_notes,
      	frequency_days as frequencyDays
      FROM tasks
      LEFT JOIN task_completion ON tasks.id = task_completion.task_id
      WHERE task_completion.user_asset_id = $userAssetId AND tasks.id = $taskId";
      //echo $qryStmt;
      $taskHistory = queryStmtToArray($qryStmt);

      return $taskHistory;
  }

  function renderTaskHistory($taskHistory){
      echo '<pre>';
      print_r($taskHistory);
      echo '</pre>';

  }

  // OLD BELOW, DELETE ****************************************


  function renderAssetInformation($assetId, $userAssetInformation){
    //echo $assetId;
    $assetInformation = getAssetInformation($assetId)[0];
    $assetInformation['detail'] = json_decode($assetInformation['detail'], true);
    $assetInformation['detail']['images'] = json_decode($assetInformation['detail']['images'], true);
    $images = $assetInformation['detail']['images'];

    if($userAssetInformation){echo '<h2>'.$userAssetInformation['user_asset_name'].'</h2>';}

    echo '<h2><i>'.$assetInformation['name'].'</i></h2>';

    echo '<table>';
    if($userAssetInformation){
      echo '<tr><th>Installed:</th><td>'.$userAssetInformation['installed_at'].'</td></tr>
      <tr><th>Updated:</th><td>'.$userAssetInformation['updated_at'].'</td></tr>';
    }
    echo '<tr><th>Make:</th><td>'.$assetInformation['asset_make'].'</td></tr>
      <tr><th>Model #:</th><td>'.$assetInformation['model_no'].'</td></tr>
      <tr><th>Model Year:</th><td>'.$assetInformation['model_year'].'</td></tr>
      <tr><th>Description:</th><td>'.$assetInformation['detail']['description'].'</td></tr>';

    if($userAssetInformation){echo '<tr><th>My Notes:</th><td>'.$userAssetInformation['user_asset_notes'].'</td></tr>';}

    echo '</table>';

      echo '<table class="invisble">';
      for($i = 0; $i < count($images); $i++){
        echo "<tr><td class='invisble'><b>".$images[$i]['title'].': '.$images[$i]['description']."</b><br><img src='".$images[$i]['url']."' alt='".$images[$i]['title'].': '.$images[$i]['description']."' class='thumbnail-large expandable'></td>";
      }
      echo '</table>';

    /*
    echo '<pre>';
    print_r($userAssetInformation);
    print_r($assetInformation);
    echo '</pre>';
    */
  }

  function getUserAssetInformationFromUserAssetId($userAssetId){
    $qryStmt = "SELECT asset_id, user_asset_name, user_asset_notes, installed_at, updated_at FROM user_assets WHERE user_assets.id = $userAssetId;";
    //echo $qryStmt;
    $userAssetInformation = queryStmtToArray($qryStmt)[0];
    return $userAssetInformation;
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
