<?php
//echo __DIR__;
    header('Content-type: application/json');
    $_SESSION['userSpiceHeaders'] = 'off';
    include_once("../../functions.php");
    $_SESSION['userSpiceHeaders'] = 'on';
//error_reporting(0);
  $dbh = db_connect();
  $returnArray = array();
  $action = getRequiredParameter('action');
  $token = getRequiredParameter('token');
  $tokenData = json_decode(decryptToken($token),1);
  //print_r(($tokenData));
  $_SESSION['userId'] = $tokenData['id'];
  $tokenData['regTime'] = date('Y-m-d H:i:s',$tokenData['time']);
  // DATABASE UPDATE FUNCTIONS

    // CHECK FOR UPDATE REQUESTS
      $action = $_GET['action'];
      //$_GET['data'] = json_encode(array('assetId' => 99));
      $data = json_decode($_GET['data'],1);
      if ($action == 'addAsset'){
        $sqlStmt = addAsset($data['assetId'], $data['newAssetName'], $data['newAssetInstallationDate']);
      } elseif ($action == 'deleteAsset'){
        $sqlStmt = deleteAsset($data['assignedAssetId']);
    } elseif ($action == 'completeTask'){
        $sqlStmt = completeTask($data['completedTaskId'], $data['userAssetId']);
    } elseif ($action == 'getCarePlan'){
        $data = getCarePlan();
    } elseif ($action == 'getMyHome'){
        $data = getMyHome();
    } elseif ($action == 'getAssetTasks'){
        $data = getAssetTasks($data['assetId']);
    }




  $returnArray[] = $sqlStmt;
  $returnArray = array(
      'response' => array(
          'message' => 'Success!',
          'color' => 'green'
      ),
      'tokenData' =>$tokenData,
      'action' => $action,
      'data' => $data,
      'sqlStmt' => $sqlStmt
  );
 returnArrayAsJSON($returnArray);
  //print_r($returnArray);
  //print_r(json_encode($returnArray), JSON_PRETTY_PRINT);

    function getCarePlan(){
        $dbh = db_connect();
        $userId = $_SESSION['userId'];
        $sqlStmt = "SELECT
        	user_assets.id as userAssetId,
            user_assets.asset_id as assetId,
            assets.name as assetName,
            user_assets.user_asset_name as userAssetName,
            tasks.id as taskId,
        		tasks.name as taskName,
                task_types.name as type,
        		tasks.importance,
                tasks.description,
                tasks.frequency_days as frequencyDays ,
        	max(task_completion.completed_at) as lastCompletedAt,
            DATE_ADD(max(task_completion.completed_at), interval frequency_days day) as nextDueDate
        FROM enhome.user_assets
        JOIN enhome.tasks ON tasks.asset_id = user_assets.asset_id
        LEFT JOIN enhome.task_completion ON task_completion.task_id = tasks.id
        LEFT JOIN enhome.task_types ON task_types.id = tasks.type_id
        LEFT JOIN enhome.assets ON user_assets.asset_id = assets.id
        WHERE user_assets.user_id = $userId
        AND user_assets.deleted_at IS NULL
        GROUP BY user_assets.id, tasks.id
        ORDER BY DATE_ADD(max(task_completion.completed_at), interval frequency_days day) ASC";

        return queryStmtToArray($sqlStmt);
    }

    function getAssetTasks($assetId){
        $list = array();
        $sqlStmt = "SELECT
          taskId, assetId, taskName, type, importance, description, frequencyDays, lastCompletedAt,
            DATE_ADD(lastCompletedAt, interval frequencyDays day) as nextDueDate
          FROM (
            SELECT
                tasks.id as taskId, tasks.asset_id as assetId,  tasks.name as taskName, task_types.name as type,
                importance, description, frequency_days as frequencyDays ,
                max(task_completion.completed_at) as lastCompletedAt
            FROM enhome.tasks
            LEFT JOIN enhome.task_types ON tasks.type_id = task_types.id
            LEFT JOIN enhome.task_completion ON task_completion.task_id = tasks.id
            WHERE tasks.asset_id = $assetId
            AND tasks.deleted_at IS NULL
            GROUP BY tasks.id
          ) as t1;";
        //echo $sqlStmt;
        $tasks = queryStmtToArray($sqlStmt);
        //print_r(json_encode($list));
        return $tasks;
    }

    function getMyHome(){
        $list = array();
        $sqlStmt = "SELECT
                user_assets.id as userAssetId,
                user_assets.asset_id as assetId,
                user_assets.user_asset_name as userAssetName,
                assets.name as assetName,
                user_assets.added_at as dateAdded
            FROM enhome.user_assets
            LEFT JOIN enhome.assets ON assets.id = user_assets.asset_id
            WHERE user_id =".$_SESSION['userId']." AND user_assets.deleted_at IS NULL;";
            //echo $sqlStmt;
        $assets = queryStmtToArray($sqlStmt);
        foreach($assets as $A){
          $list[] = $A;
        }
        //print_r(json_encode($list));
        return $assets;
    }

    function completeTask($completedTaskId, $userAssetId){
      $dbh = db_connect();
      $userId = $_SESSION['userId'];
      $sqlStmt = "INSERT INTO `enhome`.`task_completion`
          (
            `user_id`,
            `user_asset_id`,
            `task_id`,
            `started_at`,
            `completed_at`,
            `inserted_at`
          )
          VALUES
          (
            $userId,
            $userAssetId,
            $completedTaskId,
            now(),
            now(),
            now()
          );
      ";
      //echo $sqlStmt;
      $dbh->exec($sqlStmt);
      return $sqlStmt;
    }

      function addAsset($assetId, $assetName, $assetInstallationDate){
        $dbh = db_connect();
        $userId = $_SESSION['userId'];
        $sqlStmt = "INSERT INTO `enhome`.`user_assets`
            (
              `user_id`,
              `asset_id`,
              `user_asset_name`,
              `installed_at`,
              `added_at`,
              `updated_at`
            )
            VALUES
            (
              $userId,
              $assetId,
              '$assetName',
              '$assetInstallationDate',
              now(),
              now()
            );
        ";
        //echo $sqlStmt;
        $dbh->exec($sqlStmt);
        return $sqlStmt;
      }

      function deleteAsset($assignedAssetId){
          $dbh = db_connect();
          $userId = $_SESSION['userId'];
          $sqlStmt = "UPDATE `enhome`.`user_assets` SET deleted_at = now() WHERE id = $assignedAssetId AND user_id = $userId;";
          $dbh->exec($sqlStmt);
          return $sqlStmt;
      }
 ?>
