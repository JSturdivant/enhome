<?php include_once("../../functions.php"); getHeader("enHome App Library Curator");?>


<form method="POST">
    <h1>Action:</h1>
    <select name='selectedAction' id='selectedAction' onchange='showContainer()'>
        <option></option>
        <option value='addAsset' >Add Asset</option>
        <option value='addTask'>Add Task</option>
    </select>
    <div class='col-6-lg' id='addAssetContainer' style='display: none'>
        <h1>Add Asset</h1>
        <h3>Branch:</h3>
        <select name='selectedBranchId' id='selectedBranchId'>
            <?php
                $branches = queryStmtToArray('SELECT parent_id, id, name FROM enhome.branches');
                foreach ($branches as $b){
                    echo "<option value='".$b['id']."'>".$b['parent_id']." - ".$b['id']." - ".$b['name']."</option>";
                }
            ?>
        </select>
        <h3>Asset Name:</h3>
        <input type='text' name='assetName' id='assetName'>
        <h3>Asset Description:</h3>
        <textarea name='assetDescription' id='assetDescription'></textarea>
        <h3>Asset Image Address:</h3>
        <input type='text' name='assetImageAddress' id='assetImageAddress'>
    </div>
    <div class='col-6-lg' id='addTaskContainer' style='display: none'>
        <h1>Add Task</h1>
        <h3>Asset:</h3>
        <select name='selectedAssetId' id='selectedAssetId'>
            <?php
                $branches = queryStmtToArray('SELECT branch_id, id, name FROM enhome.assets');
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
                $branches = queryStmtToArray('SELECT id, name FROM enhome.task_types');
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
    </div>
    <input type='submit'>

</form>
<script>
    function showContainer(){
        var selectedAction = document.getElementById('selectedAction').value;
        document.getElementById("addAssetContainer").style.display="none";
        document.getElementById("addTaskContainer").style.display="none";
        if(selectedAction == 'addAsset'){
            document.getElementById("addAssetContainer").style.display="block";
        } else if(selectedAction == 'addTask'){
            document.getElementById("addTaskContainer").style.display="block";
        }
    }
</script>

<?php
    if($_POST['selectedAction'] == 'addAsset'){
        addAssetToLibrary($_POST);
    } elseif($_POST['selectedAction'] == 'addTask'){
        addTaskToLibrary($_POST);
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
                `detail`,
                `added_at`,
                `updated_at`
            )
            VALUES
            (
                '".$data['selectedBranchId']."',
                '".$data['assetName']."',
                '".$data['detail']."',
                now(),
                now()
            );";
        echo $insertStmt;
        $insert = $dbh->exec($insertStmt);
    }


 ?>
