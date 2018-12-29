console.log('functions.js loaded');

// APP PAGE FUNTCIONS


function log(message){console.log(message);}

// PAGE LOADER

  function getPages(){
    return [
      {
        page: "my-care-plan",
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
        page: "asset-viewer",
        title: "View Asset",
        callback: renderAssetCard,
      }
    ];

  }

  //loadPage();
  function loadPage(page = null){
    if(page != null){ // IF PAGE PASSED DIRECTLY INTO APP INPUTS
      console.log(page);
      var pageUrl = ('?page='+page);
      //history.pushState('Enhome', "EnHome App", pageUrl) ;
      modifyUrl(null,page);
      showPageContent(page);
      return true;
    } else {
      var page = getQueryVariable("page"); // IF PAGE IN URL
      console.log('PAGE '+ page);
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
      //var pageContentHtml = "<div class='container' style='display: block; width: 100%; background: blue'>";
      // CYCLE THROUGH MY ASSET LIST
      for (m = 0; m < myHome.length; m++){
          console.log(myHome[m].assetId);
          var assetCard = renderAssetCard(myHome[m].assetId);
          var newAssetCard = "";
          newAssetCard += "<div class='assetCardContainer'>";
              newAssetCard += assetCard;
              newAssetCard += "<button onclick='deleteAsset("+myHome[m].userAssetId+")'>Delete</button>";
          newAssetCard += "</div>";

          pageContent.innerHTML += newAssetCard;
          document.getElementById('userAssetNameContainer').innerHTML = "<h1>" + myHome[m].userAssetName+ "</h1>";
          document.getElementById('userAssetNameContainer').id = myHome[m].userAssetId;

      }
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


// IMAGE UPLOADER ******************************
  var savedImages = [];
  var imageUrl;

  function addLinkedImage(){
    var imageUrl = document.getElementById('linkedImageAddress');
    var data = {data: {url: imageUrl.value}};
    if(isURL(imageUrl.value)){
      showResult(data);
    } else {
      return false;
    }
  }
  function isURL(str) {
    var pattern = new RegExp('^(https?:\\/\\/)?'+ // protocol
    '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.?)+[a-z]{2,}|'+ // domain name
    '((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
    '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // port and path
    '(\\?[;&a-z\\d%_.~+=-]*)?'+ // query string
    '(\\#[-a-z\\d_]*)?$','i'); // fragment locator
    alert(pattern.test(str));
    return pattern.test(str);
  }

  function addImageUploader(targetDiv = "imageUploadContainer", insertTarget){
    var currentdate = new Date();
    var newObjectID = currentdate.getHours() +currentdate.getMinutes() + currentdate.getSeconds() + Math.random();
    document.getElementById(targetDiv).innerHTML = "    <div id='savedImagesListing'></div> \n\
    <h4>Add Image from Url:</h4> \n\
    <input type='url' name='linkedImageAddress' id='linkedImageAddress' onblur='addLinkedImage()'> \n\
    <div id='imageUploadContainer'></div> \n\
    <h4>Upload Image</h4> \n\
        <input id='file-upload"+newObjectID+"' name='file-upload' type='file' \n\
            accept='.gif,.jpg,.jpeg,.png' > \n\
          <br><input type='hidden'  name='picfile' id='picfile' placeholder='Choose your picture.jpg'> \n\
          <span  name='imageUrl' id='imageUrl'></span> \n\
          <div id='imageUploadingStatus' style='display:none;'>Uploading...</div> \n\
          <br><input type='text'  name='imageTitle' id='imageTitle' placeholder='Image Title'> \n\
          <input type='hidden' name='savedImages' id='savedImages'> \n\
          <textarea id='imageCaption' placeholder='Caption'></textarea> \n\
          <a href='javascript:void(0)' id='saveImageButton' style='display:none; font-weight: bold; font-size: 2em;' onclick='saveImage(\""+insertTarget+"\")'>Save</a> \n\
    ";
    addFileUploader("file-upload"+newObjectID);
  }

  function addFileUploader(elementId){
    window.addEventListener("load", function() {
      document.getElementById(elementId).onchange = function(event) {
        var reader = new FileReader();
        reader.readAsDataURL(event.srcElement.files[0]);
        var me = this;
        reader.onload = function () {
          var fileContent = reader.result;
          console.log(fileContent);
          document.getElementById('picfile').value = fileContent;
          var d = new Date();
          var dir = d.getFullYear() + "/" + (d.getMonth()+1) + "/" + d.getDate();
          var uploadData = {'picfile': fileContent, 'directory': dir};
          post('api/s3upload/',uploadData, showResult);
          document.getElementById('imageUploadingStatus').style.display = 'block';
        }
      }
    });
  }

  function showResult(data){
    document.getElementById('imageUploadingStatus').style.display = 'none';
    imageUrl = data.data.url;
    document.getElementById('imageUrl').innerHTML = imageUrl;
    document.getElementById('saveImageButton').style.display = 'block';
    console.log(data);
  }

  function saveImage(insertTarget = null){
    var newImageUrl = document.getElementById('imageUrl');
    var newImageTitle = document.getElementById('imageTitle');
    var newImageCaption = document.getElementById('imageCaption');
    document.getElementById('saveImageButton').style.dispay = 'none';

    savedImages.push({
      'url': imageUrl, //newImageUrl.innerHTML,
      'title': newImageTitle.value,
      'caption': newImageCaption.value,
      'shortcode': '*[image:'+newImageTitle.value+']*',
    });

    console.log('SAVED IMAGES');
    console.log(savedImages);
    showSavedImages(insertTarget);

    newImageUrl.innerHTML = '';
    newImageCaption.value = '';
  }

  function showSavedImages(insertTarget = null){
      var savedImagesListing = document.getElementById('savedImagesListing');
      var savedImagesStore = document.getElementById('savedImages');
      console.log(savedImages);
      savedImagesStore.value = JSON.stringify(savedImages);
      var savedImagesPage = getQueryVariable('page');
      var insertHtmlString = "";
      if (page == 'add-new-task' || page == 'edit-task'){
        insertHtmlString = "<br><a href='javascript:void(0)' onclick='insertImageAtCursor(\""+savedImages[si].shortcode+"\", \""+insertTarget+"\")'>Insert into Instruction</a> | ";
      }
      //savedImagesListing.innerHTML = JSON.stringify(savedImages);
      savedImagesListing.innerHTML = '<h4>Saved Images</h4>';
      for(si = 0; si < savedImages.length; si++){
        newImageHtml = "<div style='display: block; vertical-align: top;'>\n\
          <div style='display: inline-block; vertical-align: top;'><img src='"+savedImages[si].url+"' style='width: 90px;'></div> \n\
          <div style='display: inline-block;'><b>"+savedImages[si].title+"</b>\n\
          <br><i>"+savedImages[si].caption+"</i>\n\
            "+insertHtmlString+"\n\
            <a href='"+savedImages[si].url+"' target='new'>View</a> \n\
            | <a href='javascript:void(0)' onclick='removeImage("+si+")'>Remove</a></div></div>";
        savedImagesListing.innerHTML += newImageHtml;
      }
  }

  // INSERT IMAGES
  function insertImageAtCursor(myValue, targetField) {
    console.log(myValue);
    myField = document.getElementById(targetField);
      //IE support
      if (document.selection) {
          myField.focus();
          sel = document.selection.createRange();
          sel.text = myValue;
      }
      //MOZILLA and others
      else if (myField.selectionStart || myField.selectionStart == '0') {
          var startPos = myField.selectionStart;
          var endPos = myField.selectionEnd;
          myField.value = myField.value.substring(0, startPos)
              + myValue
              + myField.value.substring(endPos, myField.value.length);
      } else {
          myField.value += myValue;
      }
  }

  function removeImage(si){
    savedImages.splice(si,1);
    showSavedImages();
  }
// END IMAGE UPLOADER ******************************

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
      console.log('RenderCompletedTasks');
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


      function lookupCarePlan(assetId){ // RETURN COMPLETE CARE PLAN AS JSON
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
        assetCardHtml += "Asset Make: <b>" + myAsset.assetMake + "</b>";
        assetCardHtml += ", Model #: <b>" + myAsset.modelNo + "</b>";
        assetCardHtml += ", Model Year: <b>" + myAsset.modelYear + "</b>";
        assetCardHtml += "<p>" + myAsset.assetDetail.description + "</p>";
        //assetCardHtml += "<br>";
        for(ai = 0; ai < myAsset.assetDetail.images.length; ai++){
          assetCardHtml += "<img src='"+myAsset.assetDetail.images[ai].url+"' style='width: 20%;'>";
        }
        //assetCardHtml += "<pre>" + JSON.stringify(myAsset.assetDetail.images) + "</pre>";
        assetCardHtml += "<pre>" + JSON.stringify(myAsset) + "</pre>";

        return assetCardHtml;
    }

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
              console.log(assetList[al].assetDetail);
                assetList[al].assetDetail = JSON.parse(assetList[al].assetDetail);
                assetList[al].assetDetail.images = JSON.parse(assetList[al].assetDetail.images);
                //assetList[al].assetDetail.otherDetails = JSON.parse(assetList[al].assetDetail.otherDetails);
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
        var pageId = getQueryVariable('page_id');
        if(!page){
            var page = getQueryVariable('page');
        }
        var pageUrl = ('?page_id='+pageId+'&page='+page);
        if(params){
          for(p = 0; p < params.length; p++){
              pageUrl += "&" + params[p].title + "=" + params[p].value;
          }
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


//GLOBAL FUNCTIONS

    var logging = true; // TOGGLE TO TURN LOGGING ON AND OFF

    function log(message){
        if (logging){
            console.log(message);
        }
    }

    function getPathname(){
        var path = window.location.pathname;
        //console.log(window.location.pathname);
        var backupCount = ((path.match(/\//g) || []).length);

        if(path.search("/enhome/") >= 0){
            backupCount = backupCount-2;
        } else {
            backupCount = backupCount-1;
        }
        var returnPath = "";
        for(i=0; i < backupCount; i++){
            returnPath += "../";
        }
        //return "https://labs.tssands.com/";
        return returnPath;

    }
    //loginCheck();
    //BUILD STANDARD HEADER



    // RETRIEVE GET PARAMS
    function getQueryVariable(variable) {
        var query = window.location.search.substring(1);
        var vars = query.split("&");
        for (var i=0;i<vars.length;i++) {
                var pair = vars[i].split("=");
                if(pair[0] === variable){return (pair[1].replace(/%20|%2C|[+]|[*+?^${}()|[\]\\]/gi,' ')).replace('+',' ');}
        }
        return(false);
    }

    // GET CALL & CALLBACK
    function get(yourUrl, callback){
      yourUrl = "http://localhost/enhome/" + yourUrl;
        console.log(yourUrl);

        if((yourUrl.indexOf('labs.t') > -1 && yourUrl.indexOf('https') == -1) || yourUrl.indexOf('../') == -1){
            //yourUrl = 'https://labs.tssands.com/' + yourUrl;
            console.log('after');
            console.log(yourUrl);
        } else {
            console.log('raw');
            console.log(yourUrl);
        }
        var xmlHttp = new XMLHttpRequest();
        xmlHttp.onreadystatechange = function() {
            if (xmlHttp.readyState == 4 && xmlHttp.status == 200){
                //console.log(xmlHttp.responseText);
                //console.log('calling');
                callback(JSON.parse(xmlHttp.responseText));
            }
        }
        //alert(yourUrl);
        xmlHttp.open("GET", yourUrl, true); // true for asynchronous
        xmlHttp.send(null);
     }

     // POST DATA & CALLBACK
    function post(yourUrl, postData, callback){
        //console.log(postData);
        postData = JSON.stringify(postData);
        var xmlHttp = new XMLHttpRequest();
        xmlHttp.onreadystatechange = function() {
            if (xmlHttp.readyState == 4 && xmlHttp.status == 200){
                console.log(xmlHttp.responseText);
                callback(JSON.parse(xmlHttp.responseText));
            }
        }
        xmlHttp.open("POST", yourUrl, true); // true for asynchronous
        xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xmlHttp.send(postData);
    }

    //SET COOKIE
    function setCookie(cname, cvalue, exdays) {
        //alert('Welcome');
        var d = new Date();
        d.setTime(d.getTime() + (exdays*24*60*60*1000));
        var expires = "expires="+ d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }

    //GET COOKIE
    function getCookie(cname) {
        var name = cname + "=";
        var decodedCookie = decodeURIComponent(document.cookie);
        var ca = decodedCookie.split(';');
        for(var i = 0; i <ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    }

    // ALERT BAR
    function alertBar(alertText, alertColor){
        //loadingStatus('off');
        if(!document.getElementById('alertBarContainer')){
            addElementToDocument('div', [{attributeType: 'id', attributeValue: 'alertBarContainer'}]);
            document.getElementById('alertBarContainer').innerHTML = '<div class="alert alert-danger" id="alertBar" role="alert">\n\
                <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>\n\
                <span class="sr-only">Error:</span><span id="alertText">Alert text...</span>\n\
                <span id="alertTime" style="font-style: italic; margin-left: 1em;"></span>  \n\
            </div>';
        }
        var alertBar = document.getElementById('alertBar');
        var alertBarContainer = document.getElementById('alertBarContainer');
        var alertTextContainer = document.getElementById('alertText');
        var alertTimeContainer = document.getElementById('alertTime');
        if(alertColor == 'red'){
            alertBar.className = "alert alert-danger";
        } else if(alertColor == 'yellow'){
            alertBar.className = "alert alert-warning";
        } else if(alertColor == 'blue'){
            alertBar.className = "alert alert-info";
        } else if(alertColor == 'green'){
            alertBar.className = "alert alert-success";
        }

        alertTextContainer.innerHTML = alertText;
        alertTimeContainer.innerHTML = moment().format('HH:mm:ss');
        alertBarContainer.style.bottom = '0px';
        setTimeout(function(){document.getElementById('alertBarContainer').style.bottom = '-100px';}, 10000);
    }

    // SET LOADING STATUS WINDOW
    function loadingStatus(action){
        if(document.getElementById('loadingStatusContainer')){
            var loadingStatusContainer = document.getElementById('loadingStatusContainer');
            if(action == "on"){
                //console.log('loading spinner on');
                document.getElementById('loadingStatusContainer').style.display = 'block';
            } else {
                //console.log('loading spinner off');
                document.getElementById('loadingStatusContainer').style.display = 'none';
            }
        } else {
            addElementToDocument('div', [
                {'attributeType': 'id', 'attributeValue': 'loadingStatusContainer'},
                {'attributeType': 'class', 'attributeValue': 'loadingPanel'}
            ]);
            document.getElementById('loadingStatusContainer').innerHTML = "<h1>Loading...</h1><br><center><div class='loader'></div></center>";
            loadingStatus(action);
        }
    }

    // ADD ELEMENT TO DIV
    function addElement(parentElement, elementType, elementAttributes){
            //console.log('creating div');
            var newElement = document.createElement(elementType);
            for(ae = 0; ae < elementAttributes.length; ae++){
                var newAttribute = document.createAttribute(elementAttributes[ae].attributeType);
                newAttribute.value = elementAttributes[ae].attributeValue;
                newElement.setAttributeNode(newAttribute);
            }
            document.getElementById(parentElement).appendChild(newElement);
    }
    // ADD ELEMENT TO END OF DOCUMENT
    function addElementToDocument(elementType, elementAttributes){
            //console.log('creating div');
            var newElement = document.createElement(elementType);
            for(ae = 0; ae < elementAttributes.length; ae++){
                var newAttribute = document.createAttribute(elementAttributes[ae].attributeType);
                newAttribute.value = elementAttributes[ae].attributeValue;
                newElement.setAttributeNode(newAttribute);
            }
            document.body.appendChild(newElement);
    }

    //ENCODE  and DECODE FOR URL

    function queryEncode(qry){
            qry = encodeURI(qry);
            qry = qry.replace(/\(/g, "%28");
            qry = qry.replace(/\)/g, "%29");
            qry = qry.replace(/\*/g, "%2A");
            qry = qry.replace(/\+/g, "%2B");
            qry = qry.replace(/\:/g, "%3A");

            return qry;
    }
    function queryDecode(qry){
            qry = decodeURI(qry);
            qry = qry.replace(/%28/g, "(");
            qry = qry.replace(/%29/g, ")");
            qry = qry.replace(/%2A/g, "*");
            qry = qry.replace(/%2B/g, "+");
            qry = qry.replace(/%3A/g, ":");

            return qry;
    }


    //GET COLOR GRADIENT
    function getGradient(){
        return ["green", "#365CB1", "#365C44", "red", "#005C00", "#36C9D2", 'orange', 'lightgreen', 'lightblue', 'pink', 'darkblue', '#333', '#ccc', 'darkgreen', 'lightgreen', 'purple', '#666'];
    }

    //PRESENT NUMBERS WITH COMMAS
    function numberWithCommas(x) {
        if(x){
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        } else {
            return 0;
        }
    }

    // CONVERT ASSOCIATE JSON TO ARRAY

    function convertJsonToArray(data){
        return Object.keys(data).map(function(k) { return data[k] });
    }

    // RANDOM INT UP TO #
    function getRandomInt(max) {
        return Math.floor(Math.random() * Math.floor(max));
    }

    //PRESENT JSON AS HTML TABLE
    var listDataDownload;
    function CreateTableFromJSON(jsonData, openButton = true, target = "listData") {
        listDataDownload = jsonData;
        // EXTRACT VALUE FOR HTML HEADER.
        var col = [];
        if(openButton){col.push('Open');}
        for (var i = 0; i < jsonData.length; i++) {
            for (var key in jsonData[i]) {
                if (col.indexOf(key) === -1) {
                    col.push(key);
                }
            }
        }

        // CREATE DYNAMIC TABLE.
        var table = document.createElement("table");

        // CREATE HTML TABLE HEADER ROW USING THE EXTRACTED HEADERS ABOVE.

        var tr = table.insertRow(-1);                   // TABLE ROW.

        for (var i = 0; i < col.length; i++) {
            var th = document.createElement("th");      // TABLE HEADER.
            th.innerHTML = col[i];
            tr.appendChild(th);
        }

        // ADD JSON DATA TO THE TABLE AS ROWS.
        for (var i = 0; i < jsonData.length; i++) {

            tr = table.insertRow(-1);

            //var tabCell = tr.insertCell(-1);
            //tabCell.innerHTML = jsonData[i].link;
            for (var j = 0; j < col.length; j++) {
                var tabCell = tr.insertCell(-1);
                tabCell.innerHTML = jsonData[i][col[j]];
            }
        }

        // FINALLY ADD THE NEWLY CREATED TABLE WITH JSON DATA TO A CONTAINER.
        var divContainer = document.getElementById(target);
        divContainer.innerHTML = "";
        // divContainer.innerHTML += '<button onclick="JSONToCSVConvertor(listDataDownload, \'list_data\',\'export\')" class="btn-primary" style="margin: 10px;">Download as CSV</button>';
        divContainer.appendChild(table);
        divContainer.innerHTML += '<button onclick="JSONToCSVConvertor(listDataDownload, \'list_data\',\'export\')" class="btn-primary" style="margin: 10px;">Download as CSV</button>';
    }

    //CHECK IF VARIABLE IS JSON
    function isJson(str) {
        try {
            JSON.parse(str);
        } catch (e) {
            return false;
        }
        return true;
    }

    //CONVERT JSON FILES TO CSV
    function JSONToCSVConvertor(JSONData, ReportTitle, ShowLabel) {
        //console.log(JSONData);
        //alert(JSON.stringify(JSONData));
        //If JSONData is not an object then JSON.parse will parse the JSON string in an Object
        var arrData = typeof JSONData != 'object' ? JSON.parse(JSONData) : JSONData;

        var CSV = '';
        //Set Report title in first row or line

        CSV += ReportTitle + '\r\n\n';

        //This condition will generate the Label/Header
        if (ShowLabel) {
            var row = "";

            //This loop will extract the label from 1st index of on array
            for (var index in arrData[0]) {

                //Now convert each value to string and comma-seprated
                row += index + ',';
            }

            row = row.slice(0, -1);

            //append Label row with line break
            CSV += row + '\r\n';
        }

        //1st loop is to extract each row
        for (var i = 0; i < arrData.length; i++) {
            var row = "";

            //2nd loop will extract each column and convert it in string comma-seprated
            for (var index in arrData[i]) {
                if(arrData[i][index] == null){arrData[i][index] = "";}
                row += '"' + arrData[i][index] + '",';
            }

            row.slice(0, row.length - 1);

            //add a line break after each row
            CSV += row + '\r\n';
        }

        if (CSV == '') {
            alert("Invalid data");
            return;
        }

        //Generate a file name
        var fileName = ("EnHome_");
        //this will remove the blank-spaces from the title and replace it with an underscore
        fileName += ReportTitle.replace(/ /g,"_");

        //Initialize file format you want csv or xls
        var uri = 'data:text/csv;charset=utf-8,' + escape(CSV);

        // Now the little tricky part.
        // you can use either>> window.open(uri);
        // but this will not work in some browsers
        // or you will not get the correct file extension

        //this trick will generate a temp <a /> tag
        var link = document.createElement("a");
        link.href = uri;

        //set the visibility hidden so it will not effect on your web-layout
        link.style = "visibility:hidden";
        link.download = fileName + ".csv";

        //this part will append the anchor tag and remove it after automatic click
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

// CHART.JS FUNCTIONS
    function getDefaultBarChartOptions(){
        return  {
            title:{
                display:false,
                text:""
            },
            tooltips: {
                mode: 'index',
                intersect: false
            },
            legend: {
                labels: {
                    defaultFontSize: 12,
                    fontSize: 12,
                }
            },
            responsive: true,
            scales: {
                xAxes: [{
                    stacked: true,
                    display: true,
                    ticks: {
                        beginAtZero: false,
                    },
                    scaleLabel: {
                        display: false,
                        labelString: 'Silos'
                    }
                }],
                yAxes: [{
                    stacked: true,
                    display: true,
                    ticks: {
                        beginAtZero: false,
                    },
                    scaleLabel: {
                        display: false,
                        labelString: 'Tons'
                    }
                }]
            },
            animation: {
                duration: 1000,
            }
        };
    }
    function getDefaultLineChartOptions(){
        return {
            responsive: true,
            title:{
                display:false,
                text:''
            },
            tooltips: {
                mode: 'index',
                intersect: false,
            },
            legend: {
                labels: {
                    defaultFontSize: 12,
                    fontSize: 12,
                }
            },
            hover: {
                mode: 'nearest',
                intersect: true
            },
            scales: {
                xAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'Month'
                    },
                    ticks:{display: true},
                }],
                yAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'Value'
                    },
                    ticks:{display: true},
                }]
            },
            animation: {
                duration: 1000,
            }
        };
    }
    function getDefaultDoughnutChartOptions(){
        return {
            responsive: true,
            title:{
                display:false,
                text:''
            },
            tooltips: {
                mode: 'index',
                intersect: false,
            },
            legend: {
                labels: {
                    defaultFontSize: 12,
                    fontSize: 12,
                }
            },
            hover: {
                mode: 'nearest',
                intersect: true
            },
            cutoutPercentage: 50,
            animation: {
                animateRotate: true,
                animateScale: true
            }
        };
    }
    function getDefaultPieChartOptions(){
        return {
            responsive: true,
            title:{
                display:false,
                text:''
            },
            tooltips: {
                mode: 'index',
                intersect: false,
            },
            hover: {
                mode: 'nearest',
                intersect: true
            },
            cutoutPercentage: 0,
            animation: {
                animateRotate: true,
                animateScale: true
            }
        };
    }

    // RENDER/UPDATE A NEW CHART
    function newChartJs(chartTitle, chartSubTitle, chartTargetId, chartData, chartType, chartOptions){
        //console.log('called');
        var chartFound = false;
        var chartTarget = document.getElementById(chartTargetId);
        var chartTitleNoSpace = chartTitle.replace(/\s/g, '');
        var chartId = chartTitleNoSpace + "Canvas";
        var defaultBarChartOptions  = getDefaultBarChartOptions();
        var defaultLineChartOptions  = getDefaultLineChartOptions();
        var defaultDoughnutChartOptions  = getDefaultDoughnutChartOptions();
        var defaultPieChartOptions  = getDefaultPieChartOptions();

        //DEFAULTS
            if(chartType == null){chartType = "bar";}
            if(chartOptions == null){
                if(chartType == 'bar' || chartType == 'horizontalBar'){
                    chartOptions = JSON.parse(JSON.stringify(defaultBarChartOptions));
                } else if (chartType == 'line'){
                    chartOptions = JSON.parse(JSON.stringify(defaultLineChartOptions));
                } else if (chartType == 'doughnut'){
                    chartOptions = JSON.parse(JSON.stringify(defaultDoughnutChartOptions));
                } else if (chartType == 'pie'){
                    chartOptions = JSON.parse(JSON.stringify(defaultPieChartOptions));
                }
                /*chartOptions.tooltips = {
                        enabled: false,
                        custom: customTooltips,
                        mode: 'nearest',
                        intersect: true,
                        backgroundColor: 'black',
                        titleFontFamily: 'Arial',
                        titleFontSize: 20,
                        titleFontStyle: 'underline',
                        titleFontColor: 'blue',
                        titleSpacing: 10,
                        titleMarginBottom: 1,
                        bodyFontFamily: 'Arial',
                        bodyFontSize: 20,
                        bodyFontStyle: 'underline',
                        bodyFontColor: 'blue',
                        bodySpacing: 10,
                        footerFontFamily: 'Arial',
                        footerMarginTop: 5,
                        xPadding: 30,
                        yPadding: 5,
                        caretPadding: 20,
                        caretSize: 10,
                        cornerRadius: 30,
                        displayColors: true,
                        borderColor: "green",
                        borderWidth: 2,
                        /*callbacks: {
                            beforeTitle: function(tooltipItem, data){return 'beforeTitle'},
                            title: function(tooltipItem, data){return 'title'},
                            afterTitle: function(tooltipItem, data){return 'afterTitle'},
                            label: function(tooltipItem, data){
                                var index = tooltipItem.index;
                                return (JSON.stringify(tooltipItem))
                            },
                            labelColor: function(tooltipItem, chart) {
                                return {
                                    borderColor: 'rgb(255, 0, 0)',
                                    backgroundColor: 'rgb(255, 0, 0)'
                                }
                            },
                            labelTextColor:function(tooltipItem, chart){
                                return '#543453';
                            }
                        },*/
                    //};
            }
            if(chartData == null){console.log('ERROR: No Chart Data');}

        // CHECK IF EXISTS
            if(document.getElementById(chartTitleNoSpace + "Canvas")){
                chartFound = true;
                chartCanvas = document.getElementById(chartId);
            }

        // IF CHART ALREADY EXISTS
            if (chartFound == false){
                //CREATE DIV
                //console.log('creating div');
                var chartContainer = document.createElement('div');
                var newAttribute = document.createAttribute('id');
                newAttribute.value = chartTitleNoSpace + "Container";
                chartContainer.setAttributeNode(newAttribute);
                var newAttribute = document.createAttribute('class');
                newAttribute.value = "chartJsItem";
                chartContainer.setAttributeNode(newAttribute);
                chartTarget.appendChild(chartContainer);

                chartContainer.innerHTML = "<h2>" + chartTitle + "</h2><h3><i>" + chartSubTitle + "</i></h3>";

                //CREATE CANVAS
                //console.log('creating canvas');
                var chartCanvas = document.createElement('canvas');
                var newAttribute = document.createAttribute('id');
                newAttribute.value = chartId;
                chartCanvas.setAttributeNode(newAttribute);
                chartTarget.appendChild(chartCanvas);
            }

        //RENDER CHART
            //console.log('Rendering Chart');
            //console.log(chartOptions);
            window[chartTitleNoSpace] = new Chart(chartCanvas.getContext("2d"), {type: chartType, options: chartOptions, data: chartData});
            window[chartTitleNoSpace].update();
    }

    function addDoughnutKeyNumber(chartTitle){
        var chartTitleNoSpace = chartTitle.replace(/\s/g, '');
        var container = document.getElementById(chartTitleNoSpace + "Container");
        var chartItem = pendingSqlAsChart.find(function(item, i){
            return (item.chartTitle == chartTitle);
        });
        var keys = Object.keys(chartItem.chartData[0]);
        var keyNumber = chartItem.chartData[0][keys[0]];
        //console.log(keyNumber);

        container.innerHTML += "<span style='position: absolute; top: 50%; left: 30%; font-size: 5em;'>"+keyNumber+"</span>";
    }

    /*var customTooltips = function(tooltip) {
        // Tooltip Element
        var tooltipEl = document.getElementById('chartjs-tooltip');

        if (!tooltipEl) {
                tooltipEl = document.createElement('div');
                tooltipEl.id = 'chartjs-tooltip';
                tooltipEl.innerHTML = "<table></table>"
                this._chart.canvas.parentNode.appendChild(tooltipEl);
        }

        // Hide if no tooltip
        if (tooltip.opacity === 0) {
                tooltipEl.style.opacity = 0;
                return;
        }

        // Set caret Position
        tooltipEl.classList.remove('above', 'below', 'no-transform');
        if (tooltip.yAlign) {
                tooltipEl.classList.add(tooltip.yAlign);
        } else {
                tooltipEl.classList.add('no-transform');
        }

        function getBody(bodyItem) {
                return bodyItem.lines;
        }

        // Set Text
        if (tooltip.body) {
                var titleLines = tooltip.title || [];
                var bodyLines = tooltip.body.map(getBody);

                var innerHtml = '<thead>';

                titleLines.forEach(function(title) {
                        innerHtml += '<tr><th>' + title + '</th></tr>';
                });
                innerHtml += '</thead><tbody>';

                bodyLines.forEach(function(body, i) {
                        var colors = tooltip.labelColors[i];
                        var style = 'background:' + colors.backgroundColor;
                        style += '; border-color:' + colors.borderColor;
                        style += '; border-width: 2px';
                        var span = '<span class="chartjs-tooltip-key" style="' + style + '"></span>';
                        innerHtml += '<tr><td>' + span + body + '</td></tr>';
                });
                innerHtml += '</tbody>';

                var tableRoot = tooltipEl.querySelector('table');
                tableRoot.innerHTML = innerHtml;
        }

        var positionY = this._chart.canvas.offsetTop;
        var positionX = this._chart.canvas.offsetLeft;

        // Display, position, and set styles for font
        tooltipEl.style.opacity = 1;
        tooltipEl.style.left = positionX + tooltip.caretX + 'px';
        tooltipEl.style.top = positionY + tooltip.caretY + 'px';
        tooltipEl.style.fontFamily = tooltip._fontFamily;
        tooltipEl.style.fontSize = tooltip.fontSize;
        tooltipEl.style.fontStyle = tooltip._fontStyle;
        tooltipEl.style.padding = tooltip.yPadding + 'px ' + tooltip.xPadding + 'px';
    };

    function tooltipContent(i, data){
        return {
            label: 'test'
        }
    }*/

    // CREATE A CHART FROM A SQL QUERY
    var pendingSqlAsChart = [];
    function getSqlAsChart(chartTitle, chartSubtitle, chartTarget,  chartQuery, chartType, chartOptions, chartGradient){
        pendingSqlAsChart.push({
            chartTitle: chartTitle,
            chartSubtitle: chartSubtitle,
            chartTarget: chartTarget,
            chartQuery: chartQuery,
            chartType: chartType,
            chartOptions: chartOptions,
            chartData: [],
            chartGradient: chartGradient
        });
        //console.log(getPathname()+"reporter/api/openquery/?key=" + userKey + "&q=" + chartQuery);
        //console.log(chartQuery);
        get(getPathname()+"reporter/api/openquery/?key=" + userKey + "&q=" + chartQuery, renderSqlAsChart);
    }

    function renderSqlAsChart(data){
        var pendingIndex = pendingSqlAsChart.find(function(item, i){
            return (item.chartQuery == data.result.detail.query);
        });
        var i = pendingSqlAsChart.indexOf(pendingIndex);
        pendingSqlAsChart[i].chartData = data.data;
        //console.log(pendingSqlAsChart[i]);
        var sqlData = data.data;
        var chartLabels = [];
        var chartDatasets = [];
        var gradient;
        var columns = (Object.keys(sqlData[0]));

        // SET GRADIENT
        if(pendingIndex.chartGradient){
            console.log('custom gradient');
            console.log(gradient);
            gradient = pendingIndex.chartGradient;
        } else {
            gradient =  getGradient();
        }



        // BAR CHART
        if(pendingIndex.chartType == 'bar' || pendingIndex.chartType == 'horizontalBar'){
            if(pendingIndex.chartOptions == null){pendingIndex.chartOptions = getDefaultBarChartOptions();}

            if(pendingIndex.chartType == 'bar' ){
                pendingIndex.chartOptions.scales.xAxes[0].scaleLabel.display = true;
                pendingIndex.chartOptions.scales.xAxes[0].scaleLabel.labelString = columns[0];
                pendingIndex.chartOptions.scales.yAxes[0].scaleLabel.display = false;
                pendingIndex.chartOptions.scales.yAxes[0].scaleLabel.labelString = "";
            } else if (pendingIndex.chartType == 'horizontalBar'){
                pendingIndex.chartOptions.scales.xAxes[0].scaleLabel.display = false;
                pendingIndex.chartOptions.scales.xAxes[0].scaleLabel.labelString = "";
                pendingIndex.chartOptions.scales.yAxes[0].scaleLabel.display = true;
                pendingIndex.chartOptions.scales.yAxes[0].scaleLabel.labelString = columns[0];
            }

            for(i = 0; i < sqlData.length; i++){
                chartLabels.push(sqlData[i][columns[0]]);
            }
             // BUILD BAR DATASETS
             for(j=1; j < columns.length; j++){ // CYCLE THROUGH COLUMNS
                 var itemKey = columns[j];
                 var datasetData = [];
                 for(i = 0; i < sqlData.length; i++){ // CYCLE THROUGH ROWS
                     datasetData.push(sqlData[i][itemKey]);
                 }
                 chartDatasets.push({
                      label: columns[j],
                      data: datasetData,
                      backgroundColor: gradient[j-1],
                      borderColor: "#fff",
                      borderWidth: 1
                 });
             }

             chartData = {
                  labels: chartLabels,
                  datasets: chartDatasets
              };
        } else if (pendingIndex.chartType == 'line'|| pendingIndex.chartType == 'time' || pendingIndex.chartType == 'time-hour' || pendingIndex.chartType == 'time-day'){
            if(pendingIndex.chartOptions == null){pendingIndex.chartOptions = getDefaultLineChartOptions();}

            pendingIndex.chartOptions.scales.xAxes[0].scaleLabel.display = true;
            pendingIndex.chartOptions.scales.xAxes[0].scaleLabel.labelString = columns[0];
            pendingIndex.chartOptions.scales.yAxes[0].scaleLabel.display = true;
            pendingIndex.chartOptions.scales.yAxes[0].scaleLabel.labelString = columns[1];

            if(pendingIndex.chartType == 'time' || pendingIndex.chartType == 'time-hour' || pendingIndex.chartType == 'time-day'){
                //console.log('TIMELINE');
                if(pendingIndex.chartType == 'time-day'){
                    var timeUnit = 'day';
                    var timeFormat = 'M/D';
                } else {
                    var timeUnit = 'hour';
                    var timeFormat = 'M/D H:mm';
                }
                pendingIndex.chartOptions.scales.xAxes[0].type = "time";
                pendingIndex.chartOptions.scales.xAxes[0].time = {
                    unit: timeUnit,
                    displayFormats: {
                        hour: timeFormat
                }};

                for(tl = 0; tl < sqlData.length; tl++){
                    sqlData[tl][columns[0]] = moment(sqlData[tl][columns[0]]).tz('America/Chicago');
                    //console.log(sqlData[tl][columns[0]]);
                }
                pendingIndex.chartType = 'line';
            }

            // BUILD LINE DATASETS
            for(j=1; j < columns.length; j++){ // CYCLE THROUGH COLUMNS
                var itemKey = columns[j];
                var datasetData = [];
                for(i = 0; i < sqlData.length; i++){ // CYCLE THROUGH ROWS
                    datasetData.push({x: sqlData[i][columns[0]], y: sqlData[i][columns[j]]});
                }

                chartDatasets.push({
                     label: columns[j],
                     data: datasetData,
                     backgroundColor: gradient[j],
                     borderColor: gradient[j],
                     fill: false,
                     borderWidth: 1
                });
            }
            chartData = {
                 datasets: chartDatasets
            };

        } else if (pendingIndex.chartType == 'doughnut' || pendingIndex.chartType == 'pie'){
            if(pendingIndex.chartOptions == null){
                if (pendingIndex.chartType == 'doughnut' ){pendingIndex.chartOptions = getDefaultDoughnutChartOptions();
                } else if (pendingIndex.chartType == 'pie' ){pendingIndex.chartOptions = getDefaultPieChartOptions();}
            }

            // BUILD LINE DATASETS
            for(i=0; i < sqlData.length; i++){ // CYCLE THROUGH COLUMNS
                var datasetData = [];
                for(j = 0; j < columns.length; j++){ // CYCLE THROUGH ROWS
                    datasetData.push(sqlData[i][columns[j]]);
                }

                chartDatasets.push({
                     data: datasetData,
                     backgroundColor: gradient,
                     borderColor: "#fff",
                     borderWidth: 1
                });
            }
            chartData = {
                labels: columns,
                datasets: chartDatasets
            };

        }
        newChartJs(pendingIndex.chartTitle, pendingIndex.chartSubtitle, pendingIndex.chartTarget, chartData, pendingIndex.chartType, pendingIndex.chartOptions);
        if(pendingIndex.chartType == 'doughnut'){
            //addDoughnutKeyNumber(pendingIndex.chartTitle);
        }
     }
    //getSqlAsChart('Line Test', 'POs', 'welcomeChartsContainer', 'SELECT id, measured_weight, weight FROM truck_loads WHERE measured_weight IS NOT NULL', 'line');


// SECURITY

    function successFunction(data){
        console.log('Success!function');
        console.log(data);
    }


//ON LOAD

    // GOOGLE ANALYTICS
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'UA-115578142-1');
