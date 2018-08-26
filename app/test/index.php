<html>
    <head>
    <?php  include_once('../../functions.php'); ?>
        
        
    <!-- SANDDRIVE FUNCTIONS --> 
        <script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.13.0/moment.min.js'></script>
        <script src='https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js'></script>
        <script src='../functions.js' ></script>
    </head>
<body onload=''>
    <a href="users/logout.php">Logout</a>
        <script>
             var userData = <?php print_r(json_encode($userData));?>;
             
             log(userData.email);
            
            
            // FACEBOOK SDK https://developers.facebook.com/apps/831655413710934/fb-login/quickstart/
                //<script>
                  window.fbAsyncInit = function() {
                    FB.init({
                      appId      : '831655413710934',
                      cookie     : true,
                      xfbml      : true,
                      version    : 'v3.1'
                    });

                    FB.AppEvents.logPageView();   

                  };

                  (function(d, s, id){
                     var js, fjs = d.getElementsByTagName(s)[0];
                     if (d.getElementById(id)) {return;}
                     js = d.createElement(s); js.id = id;
                     js.src = "https://connect.facebook.net/en_US/sdk.js";
                     fjs.parentNode.insertBefore(js, fjs);
                   }(document, 'script', 'facebook-jssdk'));
               //< /script>


            //Taken from the sample code above, here's some of the code that's run during page load to check a person's login status:
                /*FB.getLoginStatus(function(response) {
                    statusChangeCallback(response);
                });*/


            //The response object that's provided to your callback contains a number of fields:
                /*{
                    status: 'connected',
                    authResponse: {
                        accessToken: '...',
                        expiresIn:'...',
                        signedRequest:'...',
                        userID:'...'
                    }
                }*/


                function checkLoginState() {
                    FB.getLoginStatus(function(response) {
                      statusChangeCallback(response);
                    });
                }
                
                function statusChangeCallback(response){
                    console.log(response);
                }
        </script>


    <div id='mainContent' class="container" >
        <div class="row">
            <div class="col-lg-12">
                <h2>Add an Asset</h2>
                <span onclick="backupTree()"><span class='glyphicon glyphicon-chevron-left' aria-hidden='true'></span>BACK</span>
                <div id="assetTreeNavigatorContainer" onload="renderAssetTree()"></div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <h2>Asset Viewer</h2>
                <div id="assetViewContainer"></div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <h2>My Assets</h2>
                <div id="myAssetsContainer"></div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <h2>My Care Plan</h2>
                <div id="consolidatedCarePlanContainer"></div>
            </div>
        </div>
        
    </div>
    <script>
        
        var backupCatId = 0;
        var assetTree = getAssetTree();
        var assetList = getAssetList();
        var myAssets = [1,3,6];
        var completedTasks = [];
        
        // ON LOAD FUNCTIONS
            renderMyData();
        
        function renderMyData(){
            renderAssetTree();
            renderMyAssets();
            renderConsolidatedCarePlan();
            renderCompletedTasks();
        }
        
        
        // TASK HANDLING
            function doTask(taskId){
                var confirmation = confirm('Are you sure?');
                if(confirmation){
                    completedTasks.push({
                        "taskId": taskId,
                        "completedAt": moment()
                    });
                    alertBar(taskId + " task done!", 'green');
                    renderMyData();
                }
            }
            
            function renderCompletedTasks(){ // SHOW COMPLETED TASKS IN TASK LIST
                for(ct = 0; ct < completedTasks.length; ct++){
                    document.getElementById('taskHistory'+completedTasks[ct].taskId).innerHTML += JSON.stringify(completedTasks[ct]);
                }
                
            }
        
        // CARE PLAN HANDLING
            function renderConsolidatedCarePlan(){
                var consolidatedCarePlanContainer = document.getElementById('consolidatedCarePlanContainer');
                var carePlanTasks = compileConsolidatedCarePlan();
                var carePlanTasksHtml = "";
                
                carePlanTasksHtml = "<table>";
                
                for (cpt = 0; cpt < carePlanTasks.length; cpt++){
                    carePlanTasksHtml += "<tr>\n\
                        <td><button onclick='doTask("+carePlanTasks[cpt].taskId+")'>Task Done</button></td>\n\
                        <td>" + carePlanTasks[cpt].assetName + "</td>\n\
                        <td>" + carePlanTasks[cpt].taskName + "</td>\n\
                        <td>" + carePlanTasks[cpt].frequencyDays + "</td>\n\
                        <td>" + carePlanTasks[cpt].description + "</td>\n\
                        <td><div id='taskHistory"+carePlanTasks[cpt].taskId+"'></div></td>\n\
                        </tr>";
                    //carePlanTasksHtml += "<tr><td>" + JSON.stringify(carePlanTasks[cpt]) + "</td></tr>";
                }
                
                carePlanTasksHtml += "</table>";
                
                consolidatedCarePlanContainer.innerHTML = carePlanTasksHtml;
                
            }
            
            function compileConsolidatedCarePlan(){
                var consolidatedCarePlan = [];
                
                for (ma = 0; ma < myAssets.length; ma++){
                    log("Care Plan " + myAssets[ma]);
                    var assetCarePlan = lookupCarePlan(myAssets[ma]);
                    if(assetCarePlan.tasks.length>0){
                        for (mat = 0; mat < assetCarePlan.tasks.length; mat++){
                            consolidatedCarePlan.push({
                                "assetId": myAssets[ma],
                                "assetName": lookupAsset(myAssets[ma]).assetName,
                                "taskId": assetCarePlan.tasks[mat].taskId,
                                "taskName": assetCarePlan.tasks[mat].taskName,
                                "frequencyDays": assetCarePlan.tasks[mat].frequencyDays,
                                "description": assetCarePlan.tasks[mat].description,
                                "importance": assetCarePlan.tasks[mat].importance,
                            });
                        }
                    }
                }
                
                consolidatedCarePlan.sort(function(a, b) {
                    return (a.frequencyDays) - (b.frequencyDays);
                });
                
                return consolidatedCarePlan;
            }
        
            function renderAssetCarePlan(assetId){ // WRITE HTML STRING FOR A SINGLE ASSET CARE PLAN
                var asset = lookupAsset(assetId);
                var carePlan = lookupCarePlan(assetId);
                var carePlanHtml = "<table>";

                for(cp = 0; cp < carePlan.length; cp++){
                    carePlanHtml += "<tr><td>" + carePlan[cp].taskName + "</td><td>"+ carePlan[cp].frequency + "</td></tr>"
                }

                carePlanHtml += "</table>";
                return carePlanHtml;
            }
            
            function lookupCarePlan(assetId){ // RETURN COMPLETE CARE PLAN AS JSON
                var carePlans = [
                    {"assetId": "1", "tasks":[
                        {"taskId": 1, "type": "task type 1", "taskName": "Task Name 1", "frequencyDays": 33, "importance": "High", "description": "Do these 7 things..."},
                        {"taskId": 2, "type": "task type 2", "taskName": "Task Name 2", "frequencyDays": 65, "importance": "Low", "description": "Do these 2 things..."},
                        {"taskId": 3, "type": "task type 3", "taskName": "Task Name 3", "frequencyDays": 270, "importance": "High", "description": "Do these 7 things..."},
                        {"taskId": 4, "type": "task type 4", "taskName": "Task Name 4", "frequencyDays": 170, "importance": "High", "description": "Do these 7 things..."},
                        {"taskId": 5, "type": "task type 5", "taskName": "Task Name 5", "frequencyDays": 43, "importance": "High", "description": "Do these 7 things..."},
                    ]},
                    {"assetId": "2", "tasks":[
                        {"taskId": 6, "type": "task type 1", "taskName": "Task Name 1", "frequencyDays": 90, "importance": "High", "description": "Do these 7 things..."},
                        {"taskId": 7, "type": "task type 2", "taskName": "Task Name 2", "frequencyDays": 7, "importance": "Low", "description": "Do these 2 things..."},
                        {"taskId": 8, "type": "task type 3", "taskName": "Task Name 3", "frequencyDays": 1, "importance": "High", "description": "Do these 7 things..."},
                        {"taskId": 9, "type": "task type 4", "taskName": "Task Name 4", "frequencyDays": 180, "importance": "High", "description": "Do these 7 things..."},
                        {"taskId": 10, "type": "task type 5", "taskName": "Task Name 5", "frequencyDays": 30, "importance": "High", "description": "Do these 7 things..."},
                    ]},
                    {"assetId": "3", "tasks":[
                        {"taskId": 11, "type": "task type 1", "taskName": "Task Name 1", "frequencyDays": 84, "importance": "High", "description": "Do these 7 things..."},
                        {"taskId": 12, "type": "task type 2", "taskName": "Task Name 2", "frequencyDays": 8, "importance": "Low", "description": "Do these 2 things..."},
                        {"taskId": 13, "type": "task type 3", "taskName": "Task Name 3", "frequencyDays": 2, "importance": "High", "description": "Do these 7 things..."},
                        {"taskId": 14, "type": "task type 4", "taskName": "Task Name 4", "frequencyDays": 180, "importance": "High", "description": "Do these 7 things..."},
                        {"taskId": 15, "type": "task type 5", "taskName": "Task Name 5", "frequencyDays": 28, "importance": "High", "description": "Do these 7 things..."},
                    ]},
                    {"assetId": "4", "tasks":[
                        {"taskId": 16, "type": "task type 1", "taskName": "Task Name 1", "frequencyDays": 92, "importance": "High", "description": "Do these 7 things..."},
                        {"taskId": 17, "type": "task type 2", "taskName": "Task Name 2", "frequencyDays": 7, "importance": "Low", "description": "Do these 2 things..."},
                        {"taskId": 18, "type": "task type 3", "taskName": "Task Name 3", "frequencyDays": 1, "importance": "High", "description": "Do these 7 things..."},
                        {"taskId": 19, "type": "task type 4", "taskName": "Task Name 4", "frequencyDays": 180, "importance": "High", "description": "Do these 7 things..."},
                        {"taskId": 20, "type": "task type 5", "taskName": "Task Name 5", "frequencyDays": 27, "importance": "High", "description": "Do these 7 things..."},
                    ]},
                    {"assetId": "5", "tasks":[
                        {"taskId": 21, "type": "task type 1", "taskName": "Task Name 1", "frequencyDays": 91, "importance": "High", "description": "Do these 7 things..."},
                        {"taskId": 22, "type": "task type 2", "taskName": "Task Name 2", "frequencyDays": 7, "importance": "Low", "description": "Do these 2 things..."},
                        {"taskId": 23, "type": "task type 3", "taskName": "Task Name 3", "frequencyDays": 1, "importance": "High", "description": "Do these 7 things..."},
                        {"taskId": 24, "type": "task type 4", "taskName": "Task Name 4", "frequencyDays": 180, "importance": "High", "description": "Do these 7 things..."},
                        {"taskId": 25, "type": "task type 5", "taskName": "Task Name 5", "frequencyDays": 30, "importance": "High", "description": "Do these 7 things..."},
                    ]},
                    {"assetId": "6", "tasks":[
                        {"taskId": 26, "type": "task type 5", "taskName": "Task Name 5", "frequencyDays": 35, "importance": "High", "description": "Do these 7 things..."},
                        {"taskId": 27, "type": "task type 1", "taskName": "Task Name 1", "frequencyDays": 71, "importance": "High", "description": "Do these 7 things..."},
                        {"taskId": 28, "type": "task type 2", "taskName": "Task Name 2", "frequencyDays": 7, "importance": "Low", "description": "Do these 2 things..."},
                        {"taskId": 29, "type": "task type 3", "taskName": "Task Name 3", "frequencyDays": 1, "importance": "High", "description": "Do these 7 things..."},
                        {"taskId": 30, "type": "task type 4", "taskName": "Task Name 4", "frequencyDays": 180, "importance": "High", "description": "Do these 7 things..."},
                    ]},
                    {"assetId": "7", "tasks":[
                        {"taskId": 31, "type": "task type 4", "taskName": "Task Name 4", "frequencyDays": 177, "importance": "High", "description": "Do these 7 things..."},
                        {"taskId": 32, "type": "task type 1", "taskName": "Task Name 1", "frequencyDays": 81, "importance": "High", "description": "Do these 7 things..."},
                        {"taskId": 33, "type": "task type 2", "taskName": "Task Name 2", "frequencyDays": 6, "importance": "Low", "description": "Do these 2 things..."},
                        {"taskId": 34, "type": "task type 3", "taskName": "Task Name 3", "frequencyDays": 4, "importance": "High", "description": "Do these 7 things..."},
                        {"taskId": 35, "type": "task type 5", "taskName": "Task Name 5", "frequencyDays": 39, "importance": "High", "description": "Do these 7 things..."},
                    ]}
                ];

                for (cp = 0; cp < carePlans.length; cp++){
                    if(carePlans[cp].assetId == assetId){
                        carePlans[cp].tasks.sort(function(a, b) {
                            return (a.frequencyDays) - (b.frequencyDays);
                        });
                        log('care plan found');
                        return carePlans[cp];
                    }
                }

                return {"assetId": null, "tasks":[
                        {"taskId": null, "type": null, "taskName": null, "frequencyDays": null, "importance": null, "description": null},
                    ]};
            }

        // COMPLETED TASKS
            function getCompletedTasks(userId){

            }


        // ASSET HANDLING
            function renderMyAssets(){ // RENDER THE LIST OF MY ASSETS IN THE MY ASSET LIST
                var myAssetsContainer = document.getElementById('myAssetsContainer');
                var myAsset;

                myAssetsContainer.innerHTML = "";

                // CYCLE THROUGH MY ASSET LIST
                for (m = 0; m < myAssets.length; m++){
                    myAssetsContainer.innerHTML += renderAssetCard(myAssets[m])
                }
            }

            function renderAssetCard(assetId){ // RETURN THE HTML FOR A CARD DISPLAYING THE ASSET
                var myAsset = lookupAsset(myAssets[m]);
                return "<h3>" + myAsset.assetName + "</h3>";
            }

            function lookupAsset(assetId){ // LOOKUP AN ASSET FROM THE ASSET LIST
                for(al = 0; al < assetList.length; al++){
                    if(assetList[al].assetId == assetId){
                        return assetList[al];
                    }
                }
            }

            function addAsset(assetId){ // ADD AN ASSET TO MY LIST
                var found = false;

                for (f = 0; f < myAssets.length; f++){
                    if (myAssets[f] == assetId){found = true;}
                }

                if(found == false){
                    myAssets.push(assetId);
                    renderMyAssets();
                } else {
                    alertBar('Asset already on your list!', 'blue')
                }
                renderMyData();
            }

            function openAsset(assetId){ // RENDER AN ASSET IN THE ASSET VIEWER
                var assetViewContainer = document.getElementById('assetViewContainer');

                // CYCLE THROUGH ASSET LIST TO FIND THE ASSET
                for(a = 0; a < assetList.length; a++){
                    if(assetList[a].assetId == assetId){
                        var asset = assetList[a];
                        assetViewContainer.innerHTML = "<h3>" + asset.assetName + "</h3>" + renderAssetCarePlan(assetId) +"<button onclick='addAsset("+asset.assetId+")'>Add to my list</button>";
                    }
                }


            }


            function getAssetList(){ // RETURN COMPLETE ASSET LIST AS JSON
                return [
                    {"parentCatId" : 21, "assetId": "1", "assetName": "Asset 1", "assetDetail" : {"description": "Asset description goes here..."}},
                    {"parentCatId" : 21, "assetId": "2", "assetName": "Asset 2", "assetDetail" : {"description": "Asset description goes here..."}},
                    {"parentCatId" : 21, "assetId": "3", "assetName": "Asset 3", "assetDetail" : {"description": "Asset description goes here..."}},
                    {"parentCatId" : 11, "assetId": "4", "assetName": "Asset 4", "assetDetail" : {"description": "Asset description goes here..."}},
                    {"parentCatId" : 22, "assetId": "5", "assetName": "Asset 5", "assetDetail" : {"description": "Asset description goes here..."}},
                    {"parentCatId" : 23, "assetId": "6", "assetName": "Asset 6", "assetDetail" : {"description": "Asset description goes here..."}},
                    {"parentCatId" : 24, "assetId": "7", "assetName": "Asset 7", "assetDetail" : {"description": "Asset description goes here..."}},

                ]
            }
        

        
        // ASSET TREE NAVIGATION
            function renderAssetTree(catId = 0){ // RENDER A BRANCH OF THE ASSET TREE IN THE ASSET TREE VIEWER
                log(catId);
                var assetTreeNavigatorContainer = document.getElementById('assetTreeNavigatorContainer');
                var branch = buildAssetTreeBranch(catId);
                var listHtml;

                // BUILD HTML STRING FOR BRANCH
                listHtml = "<ul>";
                for(b = 0; b < branch.length; b++){
                    if(branch[b].type == "cat"){
                        listHtml += "<li onclick='renderAssetTree(" + branch[b].catId + "), setBackupCatId("+branch[b].parentCatId+")'><span class='glyphicon glyphicon-chevron-right' aria-hidden='true'></span>" + branch[b].catName + "</li>";
                    } else if(branch[b].type == "asset"){
                        listHtml += "<li onclick='openAsset(" + branch[b].assetId + ")'><span class='glyphicon glyphicon-plus-sign' aria-hidden='true'></span>" + branch[b].assetName + "</li>";
                    }
                }

                listHtml += "</ul>";
                assetTreeNavigatorContainer.innerHTML = listHtml;

                return true;
            }

            function buildAssetTreeBranch(catId, subBranches=false){ // BUILD LIST OF CATEGORIES IN A CATEGORY NAVIGATION BRANCH
                var branch = [];
               //console.log(assetTree);
                for(var i = 0; i < assetTree.length; i++){
                    if(assetTree[i].parentCatId == catId){
                        log(assetTree[i].catName);
                        var newBranch = assetTree[i];
                        if(subBranches){
                            newBranch.branches = buildAssetTreeBranch(newBranch.catId, subBranches);
                        }
                        newBranch.type = "cat";
                        branch.push(newBranch);
                    }
                }

                for(var i = 0; i < assetList.length; i++){
                    if(assetList[i].parentCatId == catId){
                        log(assetList[i].assetName);
                        var newBranch = assetList[i];
                        newBranch.type = "asset";
                        branch.push(newBranch);
                    }
                }

                return branch;
            }

            function backupTree(){  // BACKUP IN THE ASSSET TREE NAVIGATOR
                renderAssetTree(backupCatId);
            }
            
            function setBackupCatId(catId){ // SET BACK UP ID VARIABLE FOR NAVIGATION
                backupCatId = catId;
            }
            
            function getAssetTree(){ // RETURN COMPLETE ASSET TREE AS JSON
                return [
                    {"catId": 1, "parentCatId" : 0, "catName": "Kitchen"},
                    {"catId": 2, "parentCatId" : 0, "catName": "Bathroom"},
                    {"catId": 3, "parentCatId" : 0, "catName": "Garage"}, 
                    {"catId": 4, "parentCatId" : 0, "catName": "Yard"}, 
                    {"catId": 5, "parentCatId" : 0, "catName": "HVAC"}, 
                    {"catId": 6, "parentCatId" : 0, "catName": "Plumbing"}, 
                    {"catId": 7, "parentCatId" : 0, "catName": "Electrical"}, 
                    {"catId": 11, "parentCatId" : 1, "catName": "Refrigerator & Freezer"},
                    {"catId": 12, "parentCatId" : 1, "catName": "Cooktop & Oven"},
                    {"catId": 13, "parentCatId" : 1, "catName": "Sink"}, 
                    {"catId": 14, "parentCatId" : 1, "catName": "Dishwasher"}, 
                    {"catId": 15, "parentCatId" : 1, "catName": "Exhaust"}, 
                    {"catId": 21, "parentCatId" : 11, "catName": "Level 3 Test Category 1"},
                    {"catId": 22, "parentCatId" : 11, "catName": "Level 3 Test Category 2"},
                    {"catId": 23, "parentCatId" : 11, "catName": "Level 3 Test Category 3"}, 
                ];
            }
        
        
    </script>
</body>
</html>
