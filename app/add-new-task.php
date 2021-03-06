<?php

//print_r( $_POST);
    if($_POST['taskAction'] == 'new'){
        addTaskToLibrary($_POST);
    }
?>
<form method='post'>
  <input type='hidden' name='taskId' id='taskId'>
  <input type='hidden' name='taskAction' id='taskAction' value='new'>
  <h3>Link to Asset:</h3>
  <select name='selectedAssetId' id='selectedAssetId'>
      <?php
          $branches = queryStmtToArray('SELECT branch_id, id, name FROM enhome.assets WHERE assets.deleted_at IS NULL');
          foreach ($branches as $b){
              echo "<option value='".$b['id']."' assetName='".$b['name']."'>".$b['branch_id']." - ".$b['id']." - ".$b['name']."</option>";
          }
      ?>
  </select>
  <a href='javascript:void(0)' onclick='addAssetToList()'>Add</a>
  <script>
    var associatedAssetList = [];
    function addAssetToList(){
      var selectedAsset = document.getElementById('selectedAssetId');
      console.log(selectedAsset.value);
      associatedAssetList.push({'id': selectedAsset.value, 'name': selectedAsset.options[selectedAsset.selectedIndex].text});
      console.log(associatedAssetList);
      document.getElementById('associatedAssetsList').value = JSON.stringify(associatedAssetList);
      listAssociatedAssets();
    }
    function listAssociatedAssets(){
      var associatedAssetListContainer = document.getElementById('associatedAssetsListShow');
      associatedAssetListContainer.innerHTML = '';
      for(i = 0; i < associatedAssetList.length; i++){
        associatedAssetListContainer.innerHTML = associatedAssetListContainer.innerHTML + '<li>' + associatedAssetList[i].name + '</li>';
      }
    }
  </script>
  <input type='hidden' name='associatedAssetsList' id='associatedAssetsList'>
  <br><b>Associated Assets</b>
  <div id='associatedAssetsListShow'><i>None</i></div>
  <h3>Task Name:</h3>
  <input type='text' name='taskName' id='taskName'>
  <h3>Task Types:</h3>
  <select name='selectedTaskTypeId' id='selectedTaskTypeId'>
      <?php
          $branches = queryStmtToArray('SELECT id, name FROM enhome.task_types WHERE task_types.deleted_at IS NULL');
          foreach ($branches as $b){
              echo "<option value='".$b['id']."'>".$b['name']."</option>";
          }
      ?>
  </select>
  <h3>Importance:</h3>
  <select name='selectedTaskImportance' id='selectedTaskImportance'>
      <option value=1>High</option>
      <option value=2>Medium</option>
      <option value=3>Low</option>
  </select>
  <h3>Frequency:</h3>
  <input type='number' name='selectedFrequencyDays' id='selectedFrequencyDays' min=1 step=1 value=90> Days
  <h3>Introduction:</h3>
  <textarea name='taskIntro' id='taskIntro'></textarea>
  <h3>Tools:</h3>
  <textarea name='taskTools' id='taskTools'></textarea>
  <h3>Materials:</h3>
  <textarea name='taskMaterials' id='taskMaterials'></textarea>

  <!-- IMAGE UPLOADER -->
  <h3>Images:</h3>
      <div id='imageUploadContainer'></div>
  <h3>Steps:</h3>
      <textarea name='taskSteps' id='taskSteps'></textarea>
<script>
  addImageUploader("imageUploadContainer", "taskSteps");
</script>
<br>
  <input type='submit' value='Save'>
</form>
