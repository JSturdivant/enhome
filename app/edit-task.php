<?php

//print_r( $_POST);
    if($_POST['taskAction'] == 'edit'){
        addTaskToLibrary($_POST);
        replaceTaskInLibrary($_POST);
    }
?>
<h3>Select Task to Edit:</h3>

<select name='selectTaskId' id='selectTaskId' onchange='updateTaskToEdit()' >
    <?php
        $assets = queryStmtToArray('SELECT tasks.id, asset_tasks.asset_id, name FROM enhome.tasks LEFT JOIN asset_tasks ON asset_tasks.task_id = tasks.id WHERE tasks.deleted_at IS NULL');
        foreach ($assets as $b){
            echo "<option value='".$b['id']."'>".$b['name']."</option>";
        }
    ?>
</select>
<hr>
<?php
  include_once('add-new-task.php');
 ?>

<script>
    function updateTaskToEdit(data){
        if(data){
            console.log('GET return data');
            console.log( data.data);
            var taskDetail = data.data[0];

            //taskDetail.detail = JSON.parse(taskDetail.detail);
            if(taskDetail.description){
              taskDetail.description = JSON.parse(taskDetail.description);
              if(taskDetail.description.images){
              taskDetail.savedImages = JSON.parse(taskDetail.description.images);
                savedImages = taskDetail.savedImages;
                showSavedImages();
              }
            }

            taskDetail.assetId = JSON.parse(taskDetail.assetId);

            // CYCLE THROUGH ALL ASSIGNED ASSETS
            var assetOptions = document.getElementById('selectedAssetId').options;
            console.log(assetOptions.length);
            console.log(assetOptions[1].value);
            associatedAssetList = [];
            if(taskDetail.assetId){
              for(aa = 0; aa < taskDetail.assetId.length; aa++){
                // CYCLE THROUGH ALL ASSET OPTIONS
                for(ao = 0; ao < assetOptions.length; ao++){
                  if(assetOptions[ao].value == taskDetail.assetId[aa]){
                    associatedAssetList.push({'id': assetOptions[ao].value, 'name': assetOptions[ao].text});

                  }

                }

              }
            }

            listAssociatedAssets();

            document.getElementById('taskAction').value = 'edit';
            document.getElementById('taskId').value = taskDetail.id;
            document.getElementById('selectedAssetId').value = taskDetail.assetId;
            document.getElementById('taskName').value = taskDetail.taskName;
            document.getElementById('selectedTaskTypeId').value = taskDetail.TaskTypeId;
            document.getElementById('selectedTaskImportance').value = taskDetail.TaskImportance;
            document.getElementById('selectedFrequencyDays').value = taskDetail.FrequencyDays;
            document.getElementById('taskIntro').value = taskDetail.description.intro;
            document.getElementById('taskTools').value = taskDetail.description.tools;
            document.getElementById('taskMaterials').value = taskDetail.description.bom;
            document.getElementById('taskSteps').value = taskDetail.description.steps;
            //document.getElementById('taskDescription').value = taskDetail.description;
        } else {
            var queryStmt = "select tasks.id, concat('[',group_concat(asset_tasks.asset_id),']') as assetId, name as taskName, type_id as TaskTypeId, importance as TaskImportance, description, frequency_days as FrequencyDays, tasks.added_at, tasks.updated_at FROM tasks LEFT JOIN asset_tasks ON asset_tasks.task_id = tasks.id WHERE tasks.deleted_at IS NULL AND tasks.id = " + document.getElementById('selectTaskId').value;
                console.log('../api/query/?q=' + queryStmt);
                //return true;
            get('api/query/?q=' + queryStmt, updateTaskToEdit);
        }
    }
</script>
