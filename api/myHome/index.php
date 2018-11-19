<?php include_once("../../../functions.php");?>

<?php
  $dbh = db_connect();
  $returnArray = array();
  $action = getRequiredParameter('action');
  $userId = $GLOBALS['userId'];

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
  }  elseif($action == 'getTaskLibrary'){
      $returnArray['data'] = getTaskLibrary();
  }  elseif($action == 'getAssetList'){
      $returnArray['data'] = getAssetList();
  }
  returnArrayAsJSON($returnArray);
  //print_r($returnArray);
  //print_r(json_encode($returnArray), JSON_PRETTY_PRINT);


    function getTaskLibrary(){
      $userId = $GLOBALS['userId'];
      $tasks = array();
      $queryStmt = "SELECT
        taskId, assetId, taskName, type, importance, description, frequencyDays, lastCompletedAt,
        	DATE_ADD(lastCompletedAt, interval frequencyDays day) as nextDueDate
        FROM (
          SELECT
          	tasks.id as taskId, tasks.asset_id as assetId,  tasks.name as taskName, task_types.name as type,
          	importance, description, frequency_days as frequencyDays ,
              max(task_completion.completed_at) as lastCompletedAt
          FROM enhome.tasks
          LEFT JOIN enhome.task_types ON tasks.type_id = task_types.id
          LEFT JOIN enhome.task_completion ON task_completion.task_id = tasks.id AND task_completion.user_id = $userId
          WHERE tasks.asset_id IN (SELECT asset_id FROM enhome.user_assets WHERE user_id = $userId AND tasks.deleted_at IS NULL)
          GROUP BY tasks.id
        ) as t1;";
        //echo $queryStmt;
      $taskData = queryStmtToArray($queryStmt);
      foreach ($taskData as $T){ // CYCLE THROUGH ALL TASKS
        $found = false;
        for($i = 0; $i < count($tasks); $i++){ // CYCLE THROUGH ALL FORMATTED ASSET OBJECTS
          if($tasks[$i]['assetId'] == $T['assetId']){
            $found = true; // OBJECT FOR ASSET IS FOUND
            $tasks[$i]['tasks'][] = array( // INSERT NEW TASK INTO PRE-EXISTING ASSET
              'taskId' => $T['taskId'],
              'type' => $T['type'],
              'taskName' => $T['taskName'],
              'frequencyDays' => $T['frequencyDays'],
              'lastCompletedAt' => $T['lastCompletedAt'],
              'nextDueDate' => $T['nextDueDate'],
              'importance' => $T['importance'],
              'description' => $T['description'],
            );
          }
        }

        // IF ASSET OBJECT NOT FOUND
        $tasks[] = array(
          'assetId' => $T['assetId'],
          'tasks' => array()
        );
      }
      return $tasks;
    }
 ?>
