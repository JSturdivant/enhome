<?php

  if($_GET['userAssetId']){
    echo "<script>modifyUrl([{'title': 'userAssetId', 'value': ".$_GET['userAssetId']."}]);</script>";
      // GET ASSET INFORMATION
        $userAssetId = $_GET['userAssetId'];
        $userAssetInformation = getUserAssetInformationFromUserAssetId($userAssetId);
        renderAssetInformation($userAssetInformation['asset_id'],$userAssetInformation);
        renderAssetCarePlan(getAssetCarePlan($userAssetId));
      // GET CARE PLAN INFORMATION

  } elseif($_GET['assetId']){
    echo "<script>modifyUrl([{'title': 'assetId', 'value': ".$_GET['assetId']."}]);</script>";
    renderAssetInformation($_GET['assetId']);

    echo "<label for='newAssetName'>Asset Name: </label><input type='text' id='newAssetName' name='newAssetName'>";
    echo "<label for='newAssetInstallationDate'>Installation Date: </label><input type='date' id='newAssetInstallationDate' name='newAssetInstallationDate'>";
    echo "<button onclick='addAsset(".$_GET['assetId'].")'>Add to my list</button>";

  } else {
    echo '<i>Please identify which asset to display</i>';
  }
  function renderAssetCarePlan($carePlan){

    echo "<table><tr>
      <th></th>
      <th>Task</th>
      <th>Frequency Days</th>
      <th>Last Completed</th>
      <th>Next Date</th>
      <th>Intro</th>
      <th>Tools</th>
      <th>BOM</th>
      <th>Steps</th>
      <th>Images</th>
    </tr>";

    //alert(carePlanTasks);
    for ($cpt = 0; $cpt < count($carePlan); $cpt++){
      $carePlan[$cpt]['description'] = json_decode($carePlan[$cpt]['description'], true);
      echo "<tr>
            <td><button onclick='completeTask(".$carePlan[$cpt]['taskId'].",".$carePlan[$cpt]['assetId'].")'>Task Done</button></td>
            <td>" . $carePlan[$cpt]['taskName'] . "</td>
            <td>" . $carePlan[$cpt]['frequencyDays'] . "</td>
            <td>" . $carePlan[$cpt]['lastCompletedAt'] . "</td>
            <td>" . $carePlan[$cpt]['nextDueDate'] . "</td>
            <td>" . $carePlan[$cpt]['description']['intro'] . "</td>
            <td>" . $carePlan[$cpt]['description']['tools'] . "</td>
            <td>" . $carePlan[$cpt]['description']['bom'] . "</td>
            <td>" . $carePlan[$cpt]['description']['steps'] . "</td>
            <td>" . $carePlan[$cpt]['description']['images'] . "</td>
            </tr>";
        //carePlanTasksHtml += "<tr><td>" + JSON.stringify(carePlanTasks[cpt]) + "</td></tr>";
    }
    echo "</table>";

    echo '<pre>';
    print_r($carePlan);
    echo '</pre>';


  }

  function getAssetCarePlan($userAssetId = null, $assetId = null){
    if($userAssetId){
      $assetId = getUserAssetInformationFromUserAssetId($userAssetId)['asset_id'];
      $queryStmt = "SELECT
        taskId, assetId, taskName, type, importance, description, frequencyDays, lastCompletedAt,
          DATE_ADD(lastCompletedAt, interval frequencyDays day) as nextDueDate
        FROM (
          SELECT
            tasks.id as taskId, $assetId as assetId, $userAssetId as userAssetId,  tasks.name as taskName, task_types.name as type,
            importance, description, frequency_days as frequencyDays ,
              max(task_completion.completed_at) as lastCompletedAt
          FROM enhome.tasks
          LEFT JOIN enhome.task_types ON tasks.type_id = task_types.id
          LEFT JOIN enhome.task_completion ON task_completion.task_id = tasks.id AND task_completion.user_asset_id = $userAssetId
          LEFT JOIN enhome.asset_tasks ON asset_tasks.task_id = tasks.id
          WHERE asset_tasks.deleted_at IS NULL AND asset_tasks.asset_id = $assetId
          GROUP BY tasks.id
        ) as t1;";
        //echo $queryStmt;
        $assetCarePlan = queryStmtToArray($queryStmt);


    } else {

    }

    return $assetCarePlan;

  }

  function renderAssetInformation($assetId, $userAssetInformation){
    $assetInformation = getAssetInformation($assetId)[0];
    $assetInformation['detail'] = json_decode($assetInformation['detail'], true);
    $assetInformation['detail']['images'] = json_decode($assetInformation['detail']['images'], true);

    echo '<pre>';
    print_r($userAssetInformation);
    print_r($assetInformation);
    echo '</pre>';
  }

  function getUserAssetInformationFromUserAssetId($userAssetId){
    $qryStmt = "SELECT asset_id, user_asset_name FROM user_assets WHERE user_assets.id = $userAssetId;";
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

<script>
// UPDATE URL
  //  modifyUrl([{'title': 'assetId', 'value': assetId}]);

    //?page_id=143&page=asset-viewer&userAssetId=84

//pageContent.innerHTML = renderAssetPage(assetId);
//renderAssetTasks(assetId);
//pageContent.innerHTML += "<label for='newAssetName'>Asset Name: </label><input type='text' id='newAssetName' name='newAssetName'>";
//pageContent.innerHTML += "<label for='newAssetInstallationDate'>Installation Date: </label><input type='date' id='newAssetInstallationDate' name='newAssetInstallationDate'>";
//pageContent.innerHTML += "<button onclick='addAsset("+assetId+")'>Add to my list</button>";
</script>
