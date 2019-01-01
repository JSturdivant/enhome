<?php

  if($_GET['userAssetId']){
      // GET ASSET INFORMATION
        $userAssetId = $_GET['userAssetId'];
        $userAssetInformation = getUserAssetInformationFromUserAssetId($userAssetId);
        renderAssetInformation($userAssetInformation['asset_id'],$userAssetInformation);

      // GET CARE PLAN INFORMATION
        echo '<hr><h2>Care Plan</h2>';
        renderAssetCarePlan(getAssetCarePlan($userAssetId, null));

      // GET CARE HISTORY
        echo '<hr><h2>Care History</h2>';
        renderAssetCareHistory(getAssetCareHistory($userAssetId));


  } elseif($_GET['assetId']){
      echo "<label for='newAssetName'>Asset Name: </label><input type='text' id='newAssetName' name='newAssetName'>";
      echo "<label for='newAssetInstallationDate'>Installation Date: </label><input type='date' id='newAssetInstallationDate' name='newAssetInstallationDate'>";
      echo "<button onclick='addAsset(".$_GET['assetId'].")'>Add to my list</button>";
    // GET ASSET INFORMATION
      $assetId = $_GET['assetId'];
      renderAssetInformation($assetId,null);

    // GET CARE PLAN INFORMATION
      echo '<hr><h2>Care Plan</h2>';
      renderAssetCarePlan(getAssetCarePlan(null, $assetId));
      /*
    echo "<script>modifyUrl([{'title': 'assetId', 'value': ".$_GET['assetId']."}]);</script>";
    renderAssetInformation($_GET['assetId']);
    */


  } else {
    echo '<i>Please identify which asset to display</i>';
  }

  function getAssetCareHistory($userAssetId){
    $qryStmt = "SELECT
	       task_completion.id, task_completion.user_id as userId, users.user_login as user,
         task_completion.user_asset_id as userAssetId, task_types.name as task_type,
         task_completion.task_id as taskId, tasks.name as task, tasks.description as task_description,
         task_completion.started_at, task_completion.completed_at, task_completion.user_task_notes,
         task_completion.inserted_at
      FROM task_completion
      	LEFT JOIN tasks ON task_completion.task_id = tasks.id
          LEFT JOIN wordpress.wp_users as users on users.id = task_completion.user_id
          LEFT JOIN task_types ON tasks.type_id = task_types.id
      WHERE user_asset_id = $userAssetId
      ORDER BY task_completion.completed_at DESC";
      //echo $qryStmt;
      $careHistory = queryStmtToArray($qryStmt);

      return $careHistory;
  }

  function renderAssetCareHistory($careHistory){

    echo '<table><tr>
      <th>Task</th>
      <th>Notes</th>
      <th>Started</th>
      <th>Completed</th>
      <th>User</th>
    </tr>';

      for($c = 0; $c < count($careHistory); $c++){
        echo '<tr>';
        echo '<td>'.$careHistory[$c]['task'].'</td>';
        echo '<td>'.$careHistory[$c]['user_task_notes'].'</td>';
        echo '<td>'.$careHistory[$c]['started_at'].'</td>';
        echo '<td>'.$careHistory[$c]['completed_at'].'</td>';
        echo '<td>'.$careHistory[$c]['user'].'</td>';
        echo '</tr>';
      }

    echo '</table>';

    /*
    echo '<pre>';
    print_r($careHistory);
    echo'</pre>';
    */
  }

  function renderAssetCarePlan($carePlan){
    $generic = false;
    if(!$carePlan[0]['userAssetId']){
      $generic = true;
    }

    echo "<table><tr>
      <th></th>
      <th>Task</th>
      <th>Frequency Days</th>";
      if(!$generic){
      echo "<th>Last Completed</th>
        <th>Next Date</th>";
      }
      echo "<th>Intro</th>
      <th>Tools</th>
      <th>BOM</th>
      <th>Steps</th>
      <th>Images</th>
    </tr>";

    //alert(carePlanTasks);
    for ($cpt = 0; $cpt < count($carePlan); $cpt++){
      $carePlan[$cpt]['description'] = json_decode($carePlan[$cpt]['description'], true);
      echo "<tr>
            <td><button onclick='completeTask(".$carePlan[$cpt]['taskId'].",".$carePlan[$cpt]['userAssetId'].")'>Task Done</button></td>
            <td>" . $carePlan[$cpt]['taskName'] . "</td>
            <td>" . $carePlan[$cpt]['frequencyDays'] . "</td>";
            if(!$generic){
              echo "<td>" . $carePlan[$cpt]['lastCompletedAt'] . "</td>
              <td>" . $carePlan[$cpt]['nextDueDate'] . "</td>";
            }
            echo "<td>" . $carePlan[$cpt]['description']['intro'] . "</td>
            <td>" . $carePlan[$cpt]['description']['tools'] . "</td>
            <td>" . $carePlan[$cpt]['description']['bom'] . "</td>
            <td>" . $carePlan[$cpt]['description']['steps'] . "</td>
            <td>" . $carePlan[$cpt]['description']['images'] . "</td>
            </tr>";
    }
    echo "</table>";

    /*
    echo '<pre>';
    print_r($carePlan);
    echo '</pre>';
    */

  }

  function getAssetCarePlan($userAssetId = null, $assetId = null){
    if($userAssetId){
      $assetId = getUserAssetInformationFromUserAssetId($userAssetId)['asset_id'];

      $queryStmt = "SELECT
      	user_assets.id as userAssetId, user_id as userId, user_assets.asset_id as assetId,
      	tasks.id as taskId, tasks.name as taskName, tasks.description as description, tasks.importance,
          task_types.name as type, tasks.frequency_days as frequencyDays,
          last_completion.last_completion as lastCompletedAt, DATE_ADD(last_completion.last_completion, interval tasks.frequency_days day) as nextDueDate
      FROM user_assets
      LEFT JOIN asset_tasks ON user_assets.asset_id = asset_tasks.asset_id
      LEFT JOIN tasks ON asset_tasks.task_id = tasks.id
      LEFT JOIN task_types ON tasks.type_id = task_types.id
      LEFT JOIN (
      	SELECT  user_asset_id, task_id, max(completed_at) as last_completion FROM task_completion GROUP BY user_asset_id, task_id
      ) as last_completion on user_assets.id = last_completion.user_asset_id and tasks.id = last_completion.task_id
      WHERE user_assets.id = $userAssetId
      ORDER BY DATE_ADD(last_completion.last_completion, interval tasks.frequency_days day) ASC";
      //echo $queryStmt;
      $assetCarePlan = queryStmtToArray($queryStmt);


    } else {

      $queryStmt = "SELECT
        null as userAssetId, null as userId,
        asset_tasks.asset_id as assetId,
      	tasks.id as taskId, tasks.name as taskName, tasks.description as description, tasks.importance,
        task_types.name as type, tasks.frequency_days as frequencyDays
      FROM asset_tasks
      LEFT JOIN tasks ON asset_tasks.task_id = tasks.id
      LEFT JOIN task_types ON tasks.type_id = task_types.id
      WHERE asset_tasks.asset_id = $assetId
      ORDER BY tasks.frequency_days ASC";
      //echo $queryStmt;
      $assetCarePlan = queryStmtToArray($queryStmt);
    }

    return $assetCarePlan;

  }

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
