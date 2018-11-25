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

  <!-- IMAGE UP:OADER -->
  <h3>Images:</h3>
    <div id='savedImagesListing'></div>
    <h4>Add New Image</h4>
    <input id="file-upload" name='file-upload' type="file"
        accept=".gif,.jpg,.jpeg,.png">
      <br><input type='hidden'  name='picfile' id='picfile' placeholder='Choose your picture.jpg'>
      <span  name='imageUrl' id='imageUrl'></span>
      <div id='imageUploadingStatus' style='display:none;'>Uploading...</div>
      <input type='text'  name='imageTitle' id='imageTitle' placeholder='Image Title'>
      <input type='hidden' name='savedImages' id='savedImages'>
      <textarea id='imageCaption' placeholder='Caption'></textarea>
      <a href='javascript:void(0)' id='saveImageButton' style='display:none; font-weight: bold; font-size: 2em;' onclick='saveImage()'>Save</a>

  <h3>Steps:</h3>
      <textarea name='taskSteps' id='taskSteps'></textarea>
<script>

// INSERT IMAGES
function insertImageAtCursor(myValue) {
  console.log(myValue);
  myField = document.getElementById('taskSteps');
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

// IMAGE UPLOADER
var savedImages = [];
var imageUrl;

window.addEventListener("load", function() {
  document.getElementById("file-upload").onchange = function(event) {
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
}});

function showResult(data){
  document.getElementById('imageUploadingStatus').style.display = 'none';
  imageUrl = data.data.url;
  document.getElementById('imageUrl').innerHTML = imageUrl;
  document.getElementById('saveImageButton').style.display = 'block';
  console.log(data);
}

function saveImage(){
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

  showSavedImages();

  newImageUrl.innerHTML = '';
  newImageCaption.value = '';
}

function showSavedImages(){
    var savedImagesListing = document.getElementById('savedImagesListing');
    var savedImagesStore = document.getElementById('savedImages');
    console.log(savedImages);
    savedImagesStore.value = JSON.stringify(savedImages);
    //savedImagesListing.innerHTML = JSON.stringify(savedImages);
    savedImagesListing.innerHTML = '<h4>Saved Images</h4>';
    for(si = 0; si < savedImages.length; si++){
      newImageHtml = "<div style='display: block; vertical-align: top;'><div style='display: inline-block; vertical-align: top;'><img src='"+savedImages[si].url+"' style='width: 90px;'></div><div style='display: inline-block;'><b>"+savedImages[si].title+"</b><br><i>"+savedImages[si].caption+"</i><br><a href='javascript:void(0)' onclick='insertImageAtCursor(\""+savedImages[si].shortcode+"\")'>Insert into Instruction</a> | <a href='"+savedImages[si].url+"' target='new'>View</a> | <a href='javascript:void(0)' onclick='removeImage("+si+")'>Remove</a></div></div>";
      savedImagesListing.innerHTML += newImageHtml;
    }
}

function removeImage(si){
  savedImages.splice(si,1);

  showSavedImages();
}

</script>

  <input type='submit' value='Save'>
</form>
<?php

?>
