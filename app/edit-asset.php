<?php

//print_r( $_POST);
    if($_POST['assetAction'] == 'edit'){
        addAssetToLibrary($_POST);
        replaceAssetInLibrary($_POST);
    }/* elseif($_POST['selectedAction'] == 'editAsset'){
        addAssetToLibrary($_POST);
        replaceAssetInLibrary($_POST);
    } elseif($_POST['selectedAction'] == 'addTask'){
        addTaskToLibrary($_POST);
    }*/
?>
<h3>Select Asset to Edit:</h3>

<select name='selectAssetId' id='selectAssetId' onchange='updateAssetToEdit()' >
    <?php
        $assets = queryStmtToArray('SELECT id, name FROM enhome.assets WHERE assets.deleted_at IS NULL');
        foreach ($assets as $b){
            echo "<option value='".$b['id']."'>".$b['name']."</option>";
        }
    ?>
</select>
<?php
  include_once('add-new-asset.php');
 ?>
<script>
    function updateAssetToEdit(data){
        if(data){
            console.log('GET return data');
            console.log( data.data);
            assetDetail = data.data[0];

            assetDetail.detail = JSON.parse(assetDetail.detail);
            document.getElementById('assetAction').value = 'edit';
            document.getElementById('assetId').value = assetDetail.assetId;
            document.getElementById('assetName').value = assetDetail.assetName;
            document.getElementById('selectedBranchId').value = assetDetail.branchId;
            document.getElementById('selectAssetMakeId').value = assetDetail.makeId;
            document.getElementById('assetModelNo').value = assetDetail.modelNo;
            document.getElementById('assetModelYear').value = assetDetail.modelYear;
            document.getElementById('assetDescription').value = assetDetail.detail.description;
            if(assetDetail.detail.otherDetails){
              document.getElementById('assetImageAddress').value = assetDetail.detail.otherDetails[0].value;
            }
        } else {
            var queryStmt = "SELECT assets.id as assetId, branch_id as branchId, assets.name as assetName, make_id as makeId, asset_makes.name as asset_make, model_no as modelNo, model_year as modelYear, detail, assets.added_at, assets.updated_at, assets.deleted_at \n\
                FROM enhome.assets LEFT JOIN asset_makes ON assets.make_id = asset_makes.id \n\
                WHERE assets.id = " + document.getElementById('selectAssetId').value;
                console.log('../api/query/?q=' + queryStmt);
                //return true;
            get('api/query/?q=' + queryStmt, updateAssetToEdit);
        }
    }
</script>
