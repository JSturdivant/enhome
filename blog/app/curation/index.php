<?php include_once("../../functions.php"); getHeader("enHome App Library Curator");
print_r( $_POST);
    if($_POST['selectedAction'] == 'addAsset'){
        addAssetToLibrary($_POST);
    } elseif($_POST['selectedAction'] == 'editAsset'){
        addAssetToLibrary($_POST);
        replaceAssetInLibrary($_POST);
    } elseif($_POST['selectedAction'] == 'addTask'){
        addTaskToLibrary($_POST);
    }
?>



    <h1>Action:</h1>
    <select name='selectedActionDropdown' id='selectedActionDropdown' onchange='showContainer()'>
        <option></option>
        <option value='addAsset' >Add Asset</option>
        <option value='editAsset' >Edit Asset</option>
        <option value='addTask'>Add Task</option>
    </select>

    <div class='col-6-lg' id='editAssetContainer' style='display: none'>
        <h3>Select Asset to Edit:</h3>

        <select name='selectAssetId' id='selectAssetId' onchange='updateAssetToEdit()' >
            <?php
                $assets = queryStmtToArray('SELECT id, name FROM enhome.assets WHERE assets.deleted_at IS NULL');
                foreach ($assets as $b){
                    echo "<option value='".$b['id']."'>".$b['name']."</option>";
                }
            ?>
        </select>
        <script>
            function updateAssetToEdit(data){
                if(data){
                    console.log('GET return data');
                    console.log( data.data);
                    assetDetail = data.data[0];

                    assetDetail.detail = JSON.parse(assetDetail.detail);
                    document.getElementById('assetId').value = assetDetail.assetId;
                    document.getElementById('assetName').value = assetDetail.assetName;
                    document.getElementById('selectedBranchId').value = assetDetail.branchId;
                    document.getElementById('selectAssetMakeId').value = assetDetail.makeId;
                    document.getElementById('assetModelNo').value = assetDetail.modelNo;
                    document.getElementById('assetModelYear').value = assetDetail.modelYear;
                    document.getElementById('assetDescription').value = assetDetail.detail.description;
                    document.getElementById('assetImageAddress').value = assetDetail.detail.otherDetails[0].value;
                } else {
                    var queryStmt = "SELECT assets.id as assetId, branch_id as branchId, assets.name as assetName, make_id as makeId, asset_makes.name as asset_make, model_no as modelNo, model_year as modelYear, detail, assets.added_at, assets.updated_at, assets.deleted_at \n\
                        FROM enhome.assets LEFT JOIN asset_makes ON assets.make_id = asset_makes.id \n\
                        WHERE assets.id = " + document.getElementById('selectAssetId').value;
                        console.log('../api/query/?q=' + queryStmt);
                        //return true;
                    get('../api/query/?q=' + queryStmt, updateAssetToEdit);
                }
            }
        </script>

    </div>

    <form method="POST">
        <input type='hidden' id='selectedAction' name='selectedAction' value=''>

    <div class='col-6-lg' id='addAssetContainer' style='display: none'>
        <h1>Add/Edit Asset</h1>
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
        <input type='hidden' name='assetId' id='assetId'>
        <h3>Asset Name:</h3>
        <input type='text' name='assetName' id='assetName'>
        <h3>Model No.:</h3>
        <input type='text' name='assetModelNo' id='assetModelNo'>
        <h3>Model Year(s):</h3>
        <input type='text' name='assetModelYear' id='assetModelYear'>
        <h3>Asset Description:</h3>
        <textarea name='assetDescription' id='assetDescription'></textarea>
        <h3>Asset Image Address:</h3>
        <input type='text' name='assetImageAddress' id='assetImageAddress'>
        <input type='submit'>
    </div>
    <div class='col-6-lg' id='addTaskContainer' style='display: none'>
        <h1>Add Task</h1>
        <h3>Asset:</h3>
        <select name='selectedAssetId' id='selectedAssetId'>
            <?php
                $branches = queryStmtToArray('SELECT branch_id, id, name FROM enhome.assets WHERE assets.deleted_at IS NULL');
                foreach ($branches as $b){
                    echo "<option value='".$b['id']."'>".$b['branch_id']." - ".$b['id']." - ".$b['name']."</option>";
                }
            ?>
        </select>
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
        <input type='number' name='selectedFrequencyDays' id='selectedFrequencyDays'> Days
        <h3>Description & Steps:</h3>
        <textarea name='taskDescription' id='taskDescription'></textarea>
        <input type='submit'>
    </div>

</form>

<script>
    function showContainer(){
        var selectedAction = document.getElementById('selectedActionDropdown').value;
        document.getElementById('selectedAction').value = selectedAction;

        console.log('Show Container');
        console.log(selectedAction);
        console.log(document.getElementById('selectedAction').value);

        document.getElementById("addAssetContainer").style.display="none";
        document.getElementById("addTaskContainer").style.display="none";
        if(selectedAction == 'addAsset'){
            document.getElementById("addAssetContainer").style.display="block";
        } else if(selectedAction == 'editAsset'){
            document.getElementById("editAssetContainer").style.display="block";
            document.getElementById("addAssetContainer").style.display="block";
            var editAssetId = get('assetId');
            //populateAssetEdit(editAssetId);
        } else if(selectedAction == 'addTask'){
            document.getElementById("addTaskContainer").style.display="block";
        }
    }

</script>
<script>
    var selectedAction = '<?php IF($_POST){
        echo $_POST['selectedAction'];
    } else {echo 'addTask';}?>';
    var selectedActionContainer = document.getElementById('selectedActionDropdown');
    selectedActionContainer.value = selectedAction;
    showContainer();

</script>
<?php
    function getAssetDetail($assetId){
        $dbh = db_connect();
        $assetDetailQuery = "SELECT
            assets.id, branch_id as branchId, assets.name as assetName, make_id as makeId, asset_makes.name as asset_make, model_no as modelNo, model_year as modelYear, detail, added_at, updated_at, assets.deleted_at
            FROM enhome.assets
            LEFT JOIN asset_makes ON assets.make_id = asset_makes.id WHERE assets.id = $assetId;";
        $assetDetail = queryStmtToArray($assetDetailQuery)[0];
        $assetDetail['detail'] = json_decode($assetDetail['detail']);
        return json_encode($assetDetail);
    }


    function addTaskToLibrary($data){
        $dbh = db_connect();

        $insertStmt = "INSERT INTO `enhome`.`tasks`
        (
            `asset_id`,
            `name`,
            `type_id`,
            `importance`,
            `description`,
            `frequency_days`,
            `added_at`,
            `updated_at`
        )
        VALUES
            (
            '".$data['selectedAssetId']."',
            '".$data['taskName']."',
            '".$data['selectedTaskTypeId']."',
            '".$data['selectedTaskImportance']."',
            '".$data['taskDescription']."',
            '".$data['selectedFrequencyDays']."',
            now(),
            now()
        );";

            echo $insertStmt;

            $insert = $dbh->exec($insertStmt);

    }

        function addAssetToLibrary($data){
            $dbh = db_connect();
            $data['detail'] = array(
                'description' => $_POST['assetDescription'],
                'otherDetails' => array(
                    array(
                        'type' => 'img',
                        'name' => 'Image',
                        'value' => $_POST['assetImageAddress']
                    )
                ),
            );

            $data['detail'] = json_encode($data['detail']);

            $insertStmt = "INSERT INTO `enhome`.`assets`
                (
                    `branch_id`,
                    `name`,
                    `make_id`,
                    `model_no`,
                    `model_year`,
                    `detail`,
                    `added_at`,
                    `updated_at`
                )
                VALUES
                (
                    '".$data['selectedBranchId']."',
                    '".$data['assetName']."',
                    '".$data['selectAssetMakeId']."',
                    '".$data['assetModelNo']."',
                    '".$data['assetModelYear']."',
                    '".$data['detail']."',
                    now(),
                    now()
                );";
            echo $insertStmt;
            $insert = $dbh->exec($insertStmt);
        }

        function replaceAssetInLibrary($data){
            $dbh = db_connect();

            $updateStmt = "UPDATE `enhome`.`assets` SET `deleted_at`= now() WHERE `id`='".$_POST['assetId']."';";
            echo $updateStmt;
            $insert = $dbh->exec($updateStmt);
        }


 ?>
