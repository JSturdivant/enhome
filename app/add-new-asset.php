<?php

//print_r( $_POST);
    if($_POST['assetAction'] == 'new'){
        addAssetToLibrary($_POST);
    } /* elseif($_POST['selectedAction'] == 'editAsset'){
        addAssetToLibrary($_POST);
        replaceAssetInLibrary($_POST);
    } elseif($_POST['selectedAction'] == 'addTask'){
        addTaskToLibrary($_POST);
    }*/
?>
<form method='post'>
  <input type='hidden' name='assetId' id='assetId'>
  <input type='hidden' name='assetAction' id='assetAction' value='new'>
  <h3>Branch:</h3>
  <select name='selectedBranchId' id='selectedBranchId'>
      <?php
          $branches = queryStmtToArray('SELECT parent_id, id, name FROM enhome.branches WHERE branches.deleted_at IS NULL');
          foreach ($branches as $b){
              echo "<option value='".$b['id']."'>".$b['parent_id']." - ".$b['id']." - ".$b['name']."</option>";
          }
      ?>
  </select>
  <h3>Asset Make:</h3>
  <select name='selectAssetMakeId' id='selectAssetMakeId'>
      <?php
          $makes = queryStmtToArray('SELECT id, name FROM enhome.asset_makes WHERE asset_makes.deleted_at IS NULL');
          foreach ($makes as $b){
              echo "<option value='".$b['id']."'>".$b['name']."</option>";
          }
      ?>
  </select>
  <h3>Asset Name:</h3>
  <input type='text' name='assetName' id='assetName'>
  <h3>Model No.:</h3>
  <input type='text' name='assetModelNo' id='assetModelNo'>
  <h3>Model Year(s):</h3>
  <input type='text' name='assetModelYear' id='assetModelYear'>
    <!-- IMAGE UPLOADER -->
      <div id='imageUploadContainer'></div>
      <script>
        addImageUploader("imageUploadContainer", "assetDescription");
      </script>
    <!-- END IMAGE UPLOADER -->
  <h3>Asset Description:</h3>
  <textarea name='assetDescription' id='assetDescription'></textarea>

  <hr>
  <input type='submit' value='Save'>

</form>
