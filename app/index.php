<style>
    .assetCard {
        background-color: #fafafa;
        border-radius: 20px;
        border: 1px solid #eee;
        box-shadow: 3px 3px 3px #fff;
        padding: 10px;
        margin: 10px;
        overflow-x: hidden;
        text-overflow: ellipsis;
        -webkit-transition: 0.5s ease-out;
        -moz-transition: 0.5s ease-out;
        -o-transition: 0.5s ease-out;
        transition: 0.5s ease-out;
    }

    .assetCard:hover {
        background-color: #fafafa;
        border: 1px solid #ccc;
        box-shadow: 3px 3px 3px #ccc;
    }

    .assetPage {
        background-color: #fff;
        border-radius: 20px;
        border: 1px solid #eee;
        box-shadow: 3px 3px 3px #fff;
        padding: 10px;
        margin: 10px;
        overflow-x: hidden;
        text-overflow: ellipsis;
        -webkit-transition: 0.5s ease-out;
        -moz-transition: 0.5s ease-out;
        -o-transition: 0.5s ease-out;
        transition: 0.5s ease-out;
    }

</style>
<script>
     var userData = <?php print_r(json_encode($GLOBALS['userInfo']));?>;
     //log(userData.email);
    // test

</script>

<div id="mainContent" class="container" ></div>
<script>

    var backupbranchId = 0;
    var assetTree = getAssetTree();
    var assetList = getAssetList();
    var myHome = getMyHome();
    var completedTasks = [];
    var pageContent ;
    var pages = [
      {
        page: "myCarePlan",
        title: "My Care Plan",
        callback: renderConsolidatedCarePlan,
      },
      {
        page: "my-home",
        title: "My Assets",
        callback: renderMyHome,
      },
      {
        page: "add-assets",
        title: "Add Assets",
        callback: renderAssetTree,
      },
      {
        page: "assetViewer",
        title: "View Asset",
        callback: renderAssetCard,
      }
    ];
    function log(message){console.log(message);}

    // PAGE LOADER

      loadPage();
      function loadPage(page = null){
        if(page != null){ // IF PAGE PASSED DIRECTLY INTO APP INPUTS
          console.log(page);
          var pageUrl = ('?page='+page);
          history.pushState('Enhome', "EnHome App", pageUrl) ;
          showPageContent(page);
          return true;
        } else {
          var page = getQueryVariable("page"); // IF PAGE IN URL
          if(page == false){page = pages[0].page;} // IF NO PAGE GIVEN IN URL
          loadPage(page);
        }
      }

      // RENDER THE CONTENTS OF EACH PAGE
      function showPageContent(page){
        console.log(page);
        console.log(pages);
        if(page == false){
          page = pages[0].page;
        }
        var mainContent = document.getElementById('mainContent');
        mainContent.innerHTML = "";

        for(p = 0; p < pages.length; p++){
          if(pages[p].page == page){
            mainContent.innerHTML = '<div class="row" id="'+page+'Container" style="display: block;">\n\
              <div class="col-lg-12">\n\
                <h2>'+pages[p].title+'</h2>\n\
                <div id="'+page+'Content"></div>\n\
              </div>\n\
            </div>';
            pageContent = document.getElementById(page+"Content");
            pages[p].callback();
            return true;
          }
        }
      }

    // PAGE CONTENT RENDERING

      function renderAssetTree(branchId = 0){ // RENDER A BRANCH OF THE ASSET TREE IN THE ASSET TREE VIEWER
        // BUILD BRANCH BREADCRUMB PATH FOR NAVIGATING ASSET TREE
            var branchPath = "";
            var parentBranchId = branchId;
            if(parentBranchId > 0){
                while(parentBranchId > 0){
                    var parentBranchDetail = getParentBranch(parentBranchId);
                    branchPath = "<a href='javascript:void(0)' onclick='renderAssetTree(" + parentBranchDetail.branchId + ")'>" + parentBranchDetail.branchName + '</a> >' + branchPath;
                    parentBranchId = parentBranchDetail.parentBranchId;
                }
            }
            branchPath = "<a href='javascript:void(0)' onclick='renderAssetTree(0)'>Home</a> >" + branchPath;

        // SET PARENT BRANCH ID FOR ASSET TREE RENDERING
            if(getParentBranch(branchId)){
                parentBranchId = getParentBranch(branchId).parentBranchId;
            } else {
                parentBranchId = 0;
            }

        // RENDER ASSET TREE
            pageContent.innerHTML = branchPath + '<br><span onclick="renderAssetTree(' + parentBranchId + ')">\n\
                </span><div id="assetTreeNavigatorContainer"></div>';
                // <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>BACK\n\

              var assetTreeNavigatorContainer = document.getElementById("assetTreeNavigatorContainer");
              var branch = buildAssetTreeBranch(branchId);
              var listHtml;

              // BUILD HTML STRING FOR BRANCH
              listHtml = "<ul>";
              // CYCLE THROUGH EACH SUB-BRANCH ON THE BRANCH
                  for(b = 0; b < branch.length; b++){

                      if(branch[b].type == "cat"){
                          listHtml += "<li onclick='renderAssetTree(" + branch[b].branchId + ")'><span class='glyphicon glyphicon-chevron-right' aria-hidden='true'></span>" + branch[b].branchName + "</li>";
                      } else if(branch[b].type == "asset"){
                          listHtml += "<li onclick='openAsset(" + branch[b].assetId + ")'><span class='glyphicon glyphicon-plus-sign' aria-hidden='true'></span>" + branch[b].assetName + "</li>";
                      }
                  }
              listHtml += "</ul>";
              assetTreeNavigatorContainer.innerHTML = listHtml;

          return true;
      }


      function getMyHome(data = null){
          if(data){
              myHome = data.data;
              console.log('MY HOME LOADED');
              console.log(myHome);
          } else {
              get("api/?token="+userToken+"&action=getMyHome", getMyHome);
          }
      }

      function renderMyHome(data = null){ // RENDER THE LIST OF MY ASSETS IN THE MY ASSET LIST
          if(data){
              console.log('myhome loaded');
              myHome = data.data;
          } else {
              console.log('no myhome data');
              get("api/?token="+userToken+"&action=getMyHome", renderMyHome);
              return false;
            }

          var myAsset;
          console.log(myHome);
          var pageContentHtml = "<div class='container'>";
          // CYCLE THROUGH MY ASSET LIST
          for (m = 0; m < myHome.length; m++){
              console.log(myHome[m].assetId);
              var assetCard = renderAssetCard(myHome[m].assetId);
              //alert(assetCard);
              var newAssetCard = "";
              newAssetCard += "<div class='col-sm-4'>";
                  newAssetCard += assetCard;
                  newAssetCard += "<button onclick='deleteAsset("+myHome[m].userAssetId+")'>Delete</button>";
              newAssetCard += "</div>";

              pageContent.innerHTML += newAssetCard;
              document.getElementById('userAssetNameContainer').innerHTML = "<h1>" + myHome[m].userAssetName+ "</h1>";
              document.getElementById('userAssetNameContainer').id = myHome[m].userAssetId;
              //pageContentHtml += assetCard;
              //pageContentHtml += "<button onclick='deleteAsset("+myHome[m].userAssetId+")'>Delete</button>";
              //pageContentHtml += "<tr><td>" + (myHome[m].assetName) + "(" + (myHome[m].assetId) + ")</td>\n\
              //<td>"+(myHome[m].userAssetName)+"</td>\n\
              //<td>"+(myHome[m].dateAdded)+"</td>\n\
              //<td><button onclick='deleteAsset("+myHome[m].userAssetId+")'>Delete</button></td></tr>";
          }
          //pageContentHtml += "</table>";

          //pageContent.innerHTML = pageContentHtml;
      }

      function renderConsolidatedCarePlan(data = null){
          if(data){
              log(data);
              var consolidatedCarePlanContainer = pageContent;
              var carePlanTasks = data.data;
              var carePlanTasksHtml = "";
              log("CARE PLAN");
              log(carePlanTasks);
              carePlanTasksHtml = "<table><tr>\n\
                <th></th>\n\
                <th>Asset</th>\n\
                <th>Asset Name</th>\n\
                <th>Task</th>\n\
                <th>Frequency Days</th>\n\
                <th>Last Completed</th>\n\
                <th>Next Date</th>\n\
                <th>Description</th>\n\
              </tr>";

            //alert(carePlanTasks);
              for (cpt = 0; cpt < carePlanTasks.length; cpt++){
                  carePlanTasksHtml += "<tr>\n\
                      <td><button onclick='completeTask("+carePlanTasks[cpt].taskId+","+carePlanTasks[cpt].assetId+")')>Task Done</button></td>\n\
                      <td>" + carePlanTasks[cpt].assetName + "</td>\n\
                      <td>" + carePlanTasks[cpt].userAssetName + "</td>\n\
                      <td>" + carePlanTasks[cpt].taskName + "</td>\n\
                      <td>" + carePlanTasks[cpt].frequencyDays + "</td>\n\
                      <td>" + carePlanTasks[cpt].lastCompletedAt + "</td>\n\
                      <td>" + carePlanTasks[cpt].nextDueDate + "</td>\n\
                      <td>" + carePlanTasks[cpt].description + "</td>\n\
                      </tr>";
                  //carePlanTasksHtml += "<tr><td>" + JSON.stringify(carePlanTasks[cpt]) + "</td></tr>";
              }
              carePlanTasksHtml += "</table>";
              consolidatedCarePlanContainer.innerHTML = carePlanTasksHtml;
          } else {
              log("api/?token="+userToken+"&action=getCarePlan");
              get("api/?token="+userToken+"&action=getCarePlan", renderConsolidatedCarePlan);
          }
      }

    // TASK HANDLING
        function doTask(taskId){
            var confirmation = confirm("Are you sure?");
            if(confirmation){
                completedTasks.push({
                    "taskId": taskId,
                    "completedAt": moment()
                });
                alertBar(taskId + " task done!", "green");
                renderMyData();
            }
        }

        function renderCompletedTasks(){ // SHOW COMPLETED TASKS IN TASK LIST
          showPageContent('carePlanContainer');
            for(ct = 0; ct < completedTasks.length; ct++){
                document.getElementById("taskHistory"+completedTasks[ct].taskId).innerHTML += JSON.stringify(completedTasks[ct]);
            }
        }


    // CARE PLAN HANDLING


        function compileConsolidatedCarePlan(){
            var consolidatedCarePlan = [];

            for (ma = 0; ma < myHome.length; ma++){
                log("Care Plan " + myHome[ma].userAssetId);
                var assetCarePlan = lookupCarePlan(myHome[ma].assetId);
                log("ASSET CARE PLAN");
                log(assetCarePlan);
                if(assetCarePlan.tasks.length>0){
                    for (mat = 0; mat < assetCarePlan.tasks.length; mat++){
                        log("ASSET CARE PLAN");
                        log(assetCarePlan);
                        consolidatedCarePlan.push({
                            "assetId": myHome[ma].assetId,
                            "userAssetId": myHome[ma].userAssetId,
                            "assetName": lookupAsset(myHome[ma].assetId).assetName,
                            "taskId": assetCarePlan.tasks[mat].taskId,
                            "taskName": assetCarePlan.tasks[mat].taskName,
                            "frequencyDays": assetCarePlan.tasks[mat].frequencyDays,
                            "description": assetCarePlan.tasks[mat].description,
                            "importance": assetCarePlan.tasks[mat].importance,
                            "lastCompletedAt": assetCarePlan.tasks[mat].lastCompletedAt,
                            "nextDueDate": assetCarePlan.tasks[mat].nextDueDate,
                        });
                    }
                }
            }

            consolidatedCarePlan.sort(function(a, b) {
                return (a.frequencyDays) - (b.frequencyDays);
            });
            console.log(consolidatedCarePlan);
            return consolidatedCarePlan;
        }

        function renderAssetCarePlan(assetId){ // WRITE HTML STRING FOR A SINGLE ASSET CARE PLAN
          //showPageContent('carePlanContainer');
            var asset = lookupAsset(assetId);
            console.log('Render Asset Care Plan');
            console.log(assetId);
            var carePlan = lookupCarePlan(assetId);
            console.log(carePlan);
            var carePlanHtml = "<table>\n\
              <tr><th>Task Name</th><th>Description</th><th>Importance</th><th>Frequency (Days)</th><td>Task ID</th></tr>";

            for(cp = 0; cp < carePlan.tasks.length; cp++){
                carePlanHtml += "<tr><td>" + carePlan.tasks[cp].taskName + "</td> \n\
                    <td>"+ carePlan.tasks[cp].description + "</td>\n\
                    <td>"+ carePlan.tasks[cp].importance + "</td> \n\
                    <td>"+ carePlan.tasks[cp].frequencyDays + "</td> \n\
                    <td>"+ carePlan.tasks[cp].taskId + ")'</td> \n\
                </tr>"
            }

            carePlanHtml += "</table>";
            return carePlanHtml;
        }


    // COMPLETED TASKS
        function getCompletedTasks(userId){

        }

    // ASSET HANDLING
        //document.body.innerHTML += renderAssetCard(1);
        function renderAssetCard(assetId){ // RETURN THE HTML FOR A CARD DISPLAYING THE ASSET
            var myAsset = lookupAsset(assetId);
            console.log("Render Asset Card");
            console.log(myAsset);

            var assetCardHtml = "<div class='assetCard'>";
            assetCardHtml += "<span id='userAssetNameContainer'></span>";
            assetCardHtml += "<h2>" + myAsset.assetName + "</h2>";
            assetCardHtml += "<h4>" + myAsset.assetDetail.description + "</h4>";
            var otherAssetDetails  = myAsset.assetDetail.otherDetails;
            for (i = 0; i < otherAssetDetails.length; i++){
                detail = otherAssetDetails[i];
                if(detail.type == 'img'){
                    assetCardHtml += "<img src='" + detail.value + "'>'";
                }
            }
            //assetCardHtml += JSON.stringify(myAsset);
            /*for (var key in otherAssetDetails) {
              if (otherAssetDetails.hasOwnProperty(key)) {
                console.log(key + ": " + otherAssetDetails[key]);
              }
            }*/
            assetCardHtml += "</div>";
            return assetCardHtml;
        }

        //document.body.innerHTML += renderAssetCard(1);
        function renderAssetPage(assetId){ // RETURN THE HTML FOR A CARD DISPLAYING THE ASSET
            var myAsset = lookupAsset(assetId);
            console.log("Render Asset Card");
            console.log(myAsset);

            var assetCardHtml = "<div class='assetPage'>";
            assetCardHtml += "<span id='userAssetNameContainer'></span>";
            assetCardHtml += "<h2>" + myAsset.assetName + "</h2>";
            assetCardHtml += "<h4>" + myAsset.assetDetail.description + "</h4>";
            var otherAssetDetails  = myAsset.assetDetail.otherDetails;
            for (i = 0; i < otherAssetDetails.length; i++){
                detail = otherAssetDetails[i];
                if(detail.type == 'img'){
                    assetCardHtml += "<img src='" + detail.value + "'>'";
                }
            }
            assetCardHtml += "<span>" + JSON.stringify(myAsset) + "</span>";
            assetCardHtml += "<span id='taskListContainer' ></span>";
            assetCardHtml += "</div>";
            //alert(assetCardHtml);
            return assetCardHtml;
        }
        function renderAssetTasks(data = null){
            if(whatIsIt(data) == "Object"){
                var tasks = data.data;

                document.getElementById('taskListContainer').innerHTML = "<h3>Tasks</h3>";
                for(i = 0; i < tasks.length; i++){
                    document.getElementById('taskListContainer').innerHTML += "<li>#"+tasks[i].taskId + " " + tasks[i].taskName;
                    document.getElementById('taskListContainer').innerHTML += " Importance: "+tasks[i].importance;
                    document.getElementById('taskListContainer').innerHTML += " Description: "+tasks[i].description;
                    document.getElementById('taskListContainer').innerHTML += " Frequency Days: "+tasks[i].frequencyDays;
                }
                document.getElementById('taskListContainer').innerHTML += "<pre>" + JSON.stringify(tasks) + "</pre>";
            } else {
                data = {'assetId': data};
                get("api/?token="+userToken+"&action=getAssetTasks&data=" + JSON.stringify(data), renderAssetTasks);
            }
        }


        function whatIsIt(object) {
            if (object === null) {
                return "null";
            }
            else if (object === undefined) {
                return "undefined";
            }
            else if (object.constructor === "test".constructor) {
                return "String";
            }
            else if (object.constructor === [].constructor) {
                return "Array";
            }
            else if (object.constructor === {}.constructor) {
                return "Object";
            }
            else {
                return "don't know";
            }
        }

        function lookupAsset(assetId){ // LOOKUP AN ASSET FROM THE ASSET LIST
            for(al = 0; al < assetList.length; al++){
                if(assetList[al].assetId == assetId){
                    assetList[al].assetDetail = JSON.parse(assetList[al].assetDetail);
                    return assetList[al];
                }
            }
        }

        function addAsset(assetId){ // ADD AN ASSET TO MY LIST
            var found = false;

            for (f = 0; f < myHome.length; f++){
                if (myHome[f] == assetId){found = true;}
            }

            if(found == false){
                var newAssetName = document.getElementById('newAssetName').value;
                var newAssetInstallationDate = document.getElementById('newAssetInstallationDate').value;
                var data = {'assetId': assetId, 'newAssetName': newAssetName, 'newAssetInstallationDate': newAssetInstallationDate };
                get("api/?token="+userToken+"&action=addAsset&data=" + JSON.stringify(data), successFunction);

            } else {
                alertBar("Asset already on your list!", "blue")
                loadPage('myHome');
            }
            //renderMyData();
        }

        function deleteAsset(assignedAssetId){
            var data = {'assignedAssetId': assignedAssetId};
            get("api/?token="+userToken+"&action=deleteAsset&data=" + JSON.stringify(data), successFunction);

        }

        function completeTask(completedTaskId, userAssetId){
            var data = {'completedTaskId': completedTaskId, 'userAssetId': userAssetId};
            get("api/?token="+userToken+"&action=completeTask&data=" + JSON.stringify(data), successFunction);
        }

        function modifyUrl(params, page = null){
            if(!page){
                var page = getQueryVariable('page');
            }
            var pageUrl = ('?page='+page);
            for(p = 0; p < params.length; p++){
                pageUrl += "&" + params[p].title + "=" + params[p].value;
            }
            history.pushState('Enhome', "EnHome App", pageUrl) ;
        }

        function openAsset(assetId){ // RENDER AN ASSET IN THE ASSET VIEWER
            // UPDATE URL
                modifyUrl([{'title': 'assetId', 'value': assetId}]);

            pageContent.innerHTML = renderAssetPage(assetId);
            renderAssetTasks(assetId);
            pageContent.innerHTML += "<label for='newAssetName'>Asset Name: </label><input type='text' id='newAssetName' name='newAssetName'>";
            pageContent.innerHTML += "<label for='newAssetInstallationDate'>Installation Date: </label><input type='date' id='newAssetInstallationDate' name='newAssetInstallationDate'>";
            pageContent.innerHTML += "<button onclick='addAsset("+assetId+")'>Add to my list</button>";
        }

        function getAssetList(){ // RETURN COMPLETE ASSET LIST AS JSON
            return <?php  getAssetList();  ?>;
        }



        function successFunction(data){
            alertBar(data.response.message, data.response.color);
            console.log('LOADING');
            console.log(getQueryVariable('page'));
            loadPage(getQueryVariable('page'));
        }

    // ASSET TREE NAVIGATION

        function buildAssetTreeBranch(branchId, subBranches=false){ // BUILD LIST OF CATEGORIES IN A CATEGORY NAVIGATION BRANCH
            var branch = [];
           //console.log(assetTree);
            for(var i = 0; i < assetTree.length; i++){
                if(assetTree[i].parentBranchId == branchId){
                    log(assetTree[i].branchName);
                    var newBranch = assetTree[i];
                    if(subBranches){
                        newBranch.branches = buildAssetTreeBranch(newBranch.branchId, subBranches);
                    }
                    newBranch.type = "cat";
                    branch.push(newBranch);
                }
            }

            for(var i = 0; i < assetList.length; i++){
                if(assetList[i].parentBranchId == branchId){
                    log(assetList[i].assetName);
                    var newBranch = assetList[i];
                    newBranch.type = "asset";
                    branch.push(newBranch);
                }
            }
            return branch;
        }
        function getParentBranch(branchId){
            console.log(assetTree);
             for(var i = 0; i < assetTree.length; i++){
                 if(assetTree[i].branchId == branchId){
                     return assetTree[i];
                 }
             }
        }

        function backupTree(){  // BACKUP IN THE ASSSET TREE NAVIGATOR
            console.log(backupbranchId);
            renderAssetTree(backupbranchId);
            //setBackupbranchId()
        }

        function setBackupbranchId(branchId){ // SET BACK UP ID VARIABLE FOR NAVIGATION
            backupbranchId = branchId;
        }



  function getAssetTree(){ // RETURN COMPLETE ASSET TREE AS JSON
      return <?php getAssetTree(); ?>;
  }

  function lookupCarePlan(assetId){ // RETURN COMPLETE CARE PLAN AS JSON
      var carePlans = <?php print_r(json_encode(getTaskLibrary())); ?>;
      for (cp = 0; cp < carePlans.length; cp++){
          if(carePlans[cp].assetId == assetId){
              carePlans[cp].tasks.sort(function(a, b) {
                  return (a.frequencyDays) - (b.frequencyDays);
              });
              log("care plan found");
            return carePlans[cp];
          }
      }

      return {"assetId": null, "tasks":[
              {"taskId": null, "type": null, "taskName": null, "frequencyDays": null, "importance": null, "description": null},
          ]};

  }

</script>

</body>
</html>

<?php

  function getUserAssets(){
    $userData = $GLOBALS['userInfo'];
    $list = array();
    $assets = queryStmtToArray("SELECT
            user_assets.id as userAssetId,
            user_assets.asset_id as assetId,
            user_assets.user_asset_name as userAssetName,
            assets.name as assetName,
            user_assets.added_at as dateAdded
        FROM enhome.user_assets
        LEFT JOIN enhome.assets ON assets.id = user_assets.asset_id
        WHERE user_id =".$userData->ID." AND user_assets.deleted_at IS NULL;");
    foreach($assets as $A){
      $list[] = $A;
    }
    print_r(json_encode($list));
  }

  function getAssetTree(){
    $queryStmt = "SELECT branches.id as branchId, parent_id as parentBranchId, branches.name as branchName FROM enhome.branches WHERE deleted_at IS NULL";
    PrintQueryStmtAsJson($queryStmt);
  }

  function getAssetList(){
    $queryStmt = "SELECT branch_id as parentBranchId, id as assetId, name as assetName, detail as assetDetail from enhome.assets WHERE deleted_at IS NULL;";
    PrintQueryStmtAsJson($queryStmt);
  }

  function getTaskLibrary(){
    $userId = $GLOBALS['userInfo']->ID;
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
