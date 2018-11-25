<?php
// APP PAGE FUNCTIONS

  function getSecrets($key){
    //echo 'test1';
    //print_r(file_get_contents('http://localhost/enhome/secrets.json'));
    $secrets = json_decode(file_get_contents('http://localhost/secrets.json'),true);
    //print_r($secrets);
    //  echo 'test2';
    return $secrets[$key];
  }

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
    $queryStmt = "SELECT
      branches.id as branchId, parent_id as parentBranchId, branches.name as branchName
      FROM enhome.branches WHERE deleted_at IS NULL";
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
        LEFT JOIN enhome.asset_tasks ON asset_tasks.task_id = tasks.id
        WHERE asset_tasks.deleted_at IS NULL AND asset_tasks.asset_id IN (SELECT asset_id FROM enhome.user_assets WHERE user_id = $userId AND tasks.deleted_at IS NULL)
        GROUP BY tasks.id
      ) as t1;";
      //echo $queryStmt;
    $taskData = queryStmtToArray($queryStmt);
    if(count($taskData) > 0){
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
    }
    return $tasks;
  }

// START LOAD WORDPRESS PAGE ********************************************
  function renderPage($pageName){
    if($pageName == "diagnostic"){
      echo getPageLocation();
      displayUserInfo();
    } else {
      if(!$pageName){
        $pageName = 'my-home';
      }
      //$pageBody = file_get_contents("app/$pageName.php");
      include_once("app/$pageName.php");
      $pageBody = "<script>".file_get_contents('functions.js')."</script>";
      print_r($pageBody);
      //include_once('app/index.php');
      echo '
      <div id="mainContent" class="container" ></div>
      <script>
          var pages = getPages();
          var myHome = getMyHome();
          var completedTasks = [];
          var pageContent ;
          var backupbranchId = 0;
      </script>';
      echo "<script>modifyUrl([], '".$pageName."');loadPage();</script>";
      //echo "<script>loadPage('$pageName');</script>";
    }
  }

  function loadStylesheet(){
    echo '<style>';
    print file_get_contents('app/stylesheet.css');
    echo '</style>';
  }

  function loadLibraries(){
   // BOOTSTRAP 4
    /*echo '<style>';
    print file_get_contents('https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css');
    echo '</style>';*/


      echo '<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
          <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>';

      echo '<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
          <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>';

      echo "<script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.13.0/moment.min.js'></script>";
      echo "<script src='https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js'></script>";

  }

// END LOAD WORDPRESS PAGE ********************************************
// *************************************************************************
// START PAGE SPECIFIC FUNCTIONS ********************************************

// END PAGE SPECIFIC FUNCTIONS ********************************************
// *************************************************************************




function getPageLocation(){
    return $_SERVER['SERVER_NAME'].__DIR__.$_SERVER['PHP_SELF'];
}

function detectEnvironment(){
      if(strpos($_SERVER['SERVER_NAME'],"localhost") >= 0 ){
          return 'localhost';
      }
}

function displayUserInfo(){
    $current_user = $GLOBALS['userInfo'];
    echo '<pre>';
    print_r($current_user);
    echo '</pre>';
    echo 'Username: ' . $current_user->user_login . "\n";
    echo 'User email: ' . $current_user->user_email . "\n";
    echo 'User level: ' . $current_user->user_level . "\n";
    echo 'User first name: ' . $current_user->user_firstname . "\n";
    echo 'User last name: ' . $current_user->user_lastname . "\n";
    echo 'User display name: ' . $current_user->display_name . "\n";
    echo 'User ID: ' . $current_user->ID . "\n";
  }

// CURATION FUNCTIONS


  function replaceAssetInLibrary($data){
      $dbh = db_connect();

      $updateStmt = "UPDATE `enhome`.`assets` SET `deleted_at`= now() WHERE `id`='".$_POST['assetId']."';";
      //echo $updateStmt;
      $insert = $dbh->exec($updateStmt);
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
      //echo $insertStmt;
      $insert = $dbh->exec($insertStmt);
  }

  function mysqlPrep($string){
    // LINE BREAKS
      $string = str_replace("\n", '\\n', $string);
      $string = str_replace("\r", '', $string);

    // QUOTES
      $string = str_replace("'", '"', $string);

      //$string = urlencode($string);
;
    return $string;
  }

  function addTaskToLibrary($data){
      $dbh = db_connect();


      $descriptionJson = json_encode(array(
        'intro' => mysqlPrep($data['taskIntro']),
        'tools' => mysqlPrep($data['taskTools']),
        'bom' => mysqlPrep($data['taskMaterials']),
        'images' => mysqlPrep($data['savedImages']),
        'steps' => mysqlPrep($data['taskSteps']),
      ));

      $insertStmt = "INSERT INTO `enhome`.`tasks`
      (
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
          '".$data['taskName']."',
          '".$data['selectedTaskTypeId']."',
          '".$data['selectedTaskImportance']."',
          '".$descriptionJson."',
          '".$data['selectedFrequencyDays']."',
          now(),
          now()
      );";
      //echo $insertStmt;
    $insert = $dbh->exec($insertStmt);

    // GET TASK ID FOR NEW TASK
      $selectStmt = "SELECT id FROM tasks WHERE name = '".$data['taskName']."' AND description = '".$descriptionJson."' AND added_at > date_sub(now(), interval 2 minute)";
      $taskIdData = $dbh->query($selectStmt);
      foreach($taskIdData as $T){
        $taskId = $T[0];
      }

    // INSERT ALL ASSOCIATED ASSETS FOR TASK
    $associatedAssets = json_decode(str_replace('\"', '"', $data['associatedAssetsList']), true); //json_decode($data['associatedAssetsList']);
    for ($aa = 0; $aa < count($associatedAssets); $aa++){
      $insertStmt2 = "INSERT INTO `enhome`.`asset_tasks`
      (
          `asset_id`,
          `task_id`,
          `added_at`
      )
      VALUES
          (
          '".$associatedAssets[$aa]['id']."',
          '".$taskId."',
          now()
      );";
      //echo $insertStmt;
    $insert = $dbh->exec($insertStmt2);
    }
  }

    function replaceTaskInLibrary($data){
        $dbh = db_connect();
        $updateStmt = "UPDATE `enhome`.`tasks` SET `deleted_at`= now() WHERE `id`='".$_POST['taskId']."';";
        //echo $updateStmt;
        $insert = $dbh->exec($updateStmt);
    }


// PRE 11/10/2018 *****************************

// LOAD LIBRARIES
    require 'assets/twilio-php-master/Twilio/autoload.php';

    // OBSOLETE


    function getPathname(){
       /* $abs_us_root=$_SERVER['DOCUMENT_ROOT'];

        $self_path=explode("/", $_SERVER['PHP_SELF']);
        $self_path_length=count($self_path);
        $file_found=FALSE;

        for($i = 1; $i < $self_path_length; $i++){
                array_splice($self_path, $self_path_length-$i, $i);
                $us_url_root=implode("/",$self_path)."/";

                if (file_exists($abs_us_root.$us_url_root.'z_us_root.php')){
                        $file_found=TRUE;
                        break;
                }else{
                        $file_found=FALSE;
                }
        }
       */
        if(substr(dirname(__FILE__),0,3) == "/Li"){
            $filePath = "http://localhost/enhome/";
        } else {
            $filePath = "http://www.enho.me/";
        }


        return $filePath;
    }

//logEvent('functions page', 'loaded');
//date_default_timezone_set('America/Chicago');

// DB FUNCTIONS
    // CONNECT TO MySQL DB SERVER
    function db_connect () {
        $dbCreds = getSecrets('db');
        $dbh = new PDO("mysql:dbname=enhome;host=".$dbCreds['local']['host'],$dbCreds['local']['username'],$dbCreds['local']['password']);
        $dbh->setAttribute (PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return ($dbh);
    }


    function getEnvironment(){
        if ( $_SERVER['SERVER_NAME'] == "tsslabsreporter-env.us-east-1.elasticbeanstalk.com" ||  $_SERVER['SERVER_NAME'] == "labs.tssands.com"){
            $mode = 'prod';
        } else {
            $mode = 'dev';
        }
        return $mode;
    }

    // CONVERT A QUERY STMT TO A PRINTED JSON OBJECT;
    function PrintQueryStmtAsJson($queryStmt){
      print_r(json_encode(queryStmtToArray($queryStmt)));
    }

//GLOBAL FUNCTIONS
    // GET KEYS FROM SQL QUERY RESULTS
    function getQueryResultKeys($data){
        //echo count($data);
        //print_r($data);
        $fields=array();
        $keys = array();
        //if(is_array($data)){
          $fields = array_keys($data->fetch(PDO::FETCH_ASSOC));
          FOREACH ($fields as $field){
              $keys[] = $field;
          }
        //}
        return $keys;
    }


    // CONVERT A SQL QUERY STATEMENT TO PHP ARRAY WITH KEYS
    function queryStmtToArray($query_stmt){
        $prod_db_connect = db_connect();
        //echo $query_stmt;
        $data_array = array();
        $query_results = $prod_db_connect->query($query_stmt);
        if(count($query_results) > 0){
          $keys = getQueryResultKeys($query_results);
          $query_results->execute();
          foreach ($query_results as $R){
              $new_row = array();
              for($i = 0; $i < count($keys); $i++){
                  $new_row[$keys[$i]] = $R[$i];
              }
              $data_array[] = $new_row;
          }
        }
        return $data_array;
    }

    // GET TIME WITH MICROSECONDS
    function microtime_float()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

    // CONVERT PHP ARRAY TO HTML TABLE
    function arrayToHtmlTable($data,$args=false) {
       // Array to Table Function
       // Copyright (c) 2014, Ink Plant
       // https://inkplant.com/code/array-to-table
       if (!is_array($args)) { $args = array(); }
       foreach (array('class','column_widths','custom_headers','format_functions','nowrap_head','nowrap_body','capitalize_headers') as $key) {
               if (array_key_exists($key,$args)) { $$key = $args[$key]; } else { $$key = false; }
       }
       if ($class) { $class = ' class="'.$class.'"'; } else { $class = ''; }
       if (!is_array($column_widths)) { $column_widths = array(); }

       //get rid of headers row, if it exists (headers should exist as keys)
       if (array_key_exists('headers',$data)) { unset($data['headers']); }

           $t = '<table cellspacing=0 '.$class.'>';
           $i = 0;
           foreach ($data as $row) {
                   $i++;
                   //display headers
                   if ($i == 1) {
                           foreach ($row as $key => $value) {
                                   if (array_key_exists($key,$column_widths)) { $style = ' style="width:'.$column_widths[$key].'px;"'; } else { $style = ''; }
                                   $t .= '<col'.$style.' />';
                           }
                           $t .= '<thead><tr>';
                           foreach ($row as $key => $value) {
                                   if (is_array($custom_headers) && array_key_exists($key,$custom_headers) && ($custom_headers[$key])) { $header = $custom_headers[$key]; }
                                   elseif ($capitalize_headers) { $header = ucwords($key); }
                                   else { $header = $key; }
                                   if ($nowrap_head) { $nowrap = ' nowrap'; } else { $nowrap = ''; }
                                   $t .= '<td style="margin: 0px; border: 1px solid black; border-collapse: collapse; padding: 3px; font-weight: bold;" '.$nowrap.'>'.$header.'</td>';
                           }
                           $t .= '</tr></thead>';
                   }

                   //display values
                   if ($i == 1) { $t .= '<tbody>'; }
                   $t .= '<tr>';
                   foreach ($row as $key => $value) {
                           if (is_array($format_functions) && array_key_exists($key,$format_functions) && ($format_functions[$key])) {
                                   $function = $format_functions[$key];
                                   if (!function_exists($function)) { custom_die('Data format function does not exist: '.htmlspecialchars($function)); }
                                   $value = $function($value);
                           }
                           if ($nowrap_body) { $nowrap = ' nowrap'; } else { $nowrap = ''; }

                           $t .= '<td style="margin: 0px; border: 1px solid black; border-collapse: collapse; padding: 3px;" '.$nowrap.'>'.$value.'</td>';
                   }
                   $t .= '</tr>';
           }
           $t .= '</tbody>';
           $t .= '</table>';
           return $t;
   }

    // KEY ENCRYPT/DECRYPT
        function encryptToken($username){
            $creds = getSecureCreds();
            $token = openssl_encrypt($username, $creds['method'], $creds['key'], 0,$creds['IV']);
            $token = str_replace("=", "Reqs621", $token);
            $token = str_replace("+", "Rplu621", $token);
            $token = str_replace("/", "Rsla621", $token);
            return $token;
        }
        function decryptToken($token){
            $creds = getSecureCreds();
            $method = "AES-256-CFB8";
            $token = str_replace("Reqs621", "=", $token);
            $token = str_replace("Rplu621", "+", $token);
            $token = str_replace("Rsla621", "/", $token);
            $username = openssl_decrypt($token, $creds['method'], $creds['key'], 0,$creds['IV']);
            return $username;
        }
        function getSecureCreds(){
            return array('method'=> "AES-256-CFB8", 'key' => '35gkjjvb908tng34ivn45n2jv', 'IV' => '1234567812345678');
        }

// API FUNCTIONS
    // RETURN PHP ARRAY AS JSON API RESPONSE
    function returnArrayAsJSON($array){
        header('Content-Type: application/json');
        //print_r($array);
        print_r(json_encode($array, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        exit();
    }

    // AUTHNETICATE USER KEY
    function authenticateUser($key){
        error_reporting(0);

        $authenticated = false;
        $username = decryptKey($key);

        $dbh = db_connect();
        $data_array = array('check' => false);
        $checkStmt = "select email, count(id) as count from users where lower(email) = '$username' GROUP BY email";

        //echo $checkStmt;
        $check = $dbh->query($checkStmt);
        //print_r( $check->rowCount());
        foreach ($check as $C){$count = $C['count'];}
        //echo $count;
        if($count > 0 || $key == 'testkey'){
            $authenticated = true;
        }
         if($authenticated == false){
            $return = array(
                'result' => 'failure',
                'reason' => 'bad key'
            );
            returnArrayAsJSON($return);
        }
        return $authenticated;
    }

    // GET REQUIRED PARAMETER
    function getRequiredParameter($param){
        if ($_GET[$param]){
            return $_GET[$param];
        } else {
            $return = array(
                'result' => 'failure',
                'reason' => 'missing required parameter: '.$param
            );
            returnArrayAsJSON($return);
        }
    }
    function postedRequiredParameter($param){
        if ($_POST[$param]){
            return $_POST[$param];
        } else {
            $return = array(
                'result' => 'failure',
                'reason' => 'missing required parameter: '.$param,
                'post' => $_POST,
            );
            returnArrayAsJSON($return);
        }
    }

    // FORMAT RETURN ARRAY
    function formatReturnArray($type, $resultDetail, $data_array, $time_start){

        return array(
            'result' => array(
                'type' => $type,
                'result' => 'success',
                'time' => date('r'),
                'detail' => $resultDetail,
                'record_count' => count($data_array),
                'result_speed' => microtime_float() - $time_start,
            ),
            'data' => $data_array
        );
    }

    // SORT A MULTI-DIMENSIONAL TABLE ARRAY
    function sortTableArray($data, $sorts1, $sort1dir, $sort2=null, $sort2dir=null){
        if(!(in_array($sort1dir,array(SORT_ASC, SORT_DESC)))){
            $sort1dir = SORT_ASC;
        }
        if($sort2){
            if(!(in_array($sort2dir,array(SORT_ASC, SORT_DESC)))){
                $sort2dir = SORT_ASC;
            }
        }
        // Obtain a list of columns
        foreach ($data as $key => $row) {
            $key1[$key]  = $row[$sosrt1];
            if($sort2){$key2[$key] = $row[$sort2];}
        }

        // Sort the data with volume descending, edition ascending
        // Add $data as the last parameter, to sort by the common key
         if($sort2){array_multisort($key1, $sort1dir, $key2, $sort2dir, $data);}
         else {array_multisort($key1, $sort1dir,$key1, $sort1dir, $data);}

         return $data;
    }

    // GET API DATA AND CONVERT TO PHP ARRAY
    function getCustomerApiData($endpointUrl) // DEPRECATED 02-16-2018, REPLACE WITH getReporterApiData(
    {
            //$prefix = "http://localhost/TSS/reporter/api/";
            $prefix = ($_SERVER['SERVER_NAME']);
            if ($prefix == "localhost"){
                $prefix = "http://".$prefix."/TSS";
            }

             $prefix = $prefix.'/reporter/api/';

            $url = $prefix.$endpointUrl;
            //echo $url;
            $response = json_decode(file_get_contents($url), true);

            return $response;
        }
    function getReporterApiData($endpointUrl){
            //$prefix = "http://localhost/TSS/reporter/api/";
            $prefix = ($_SERVER['SERVER_NAME']);
            if ($prefix == "localhost"){
                $prefix = "http://".$prefix."/TSS";
            }

             $prefix = $prefix.'/reporter/api/';

            $url = $prefix.$endpointUrl;
            //echo $url;
            $response = json_decode(file_get_contents($url), true);

            return $response;
        }

    function logEvent($type, $comment){
        $dbh =db_connect();
        $source = url();
        $timestamp = date('Y-m-d H:i:s');
        $stmt = "INSERT INTO `labs-portal`.`log`
            (
            `recorded_at`,
            `source`,
            `type`,
            `comment`,
            `ip_address`)
            VALUES
            ('$timestamp',
            '$source',
            '$type',
            '$comment', '".$_SERVER['REMOTE_ADDR']."' );
            ";
        //echo $stmt;
        //$dbh->exec($stmt);
    }

    function url(){
    if(isset($_SERVER['HTTPS'])){
        $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
    }
    else{
        $protocol = 'http';
    }
    return $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    //return $protocol . "://" . $_SERVER['HTTP_HOST'];
}

// SENDGRID
    function sendEmail($fromName = 'enHome', $fromEmail = 'noreply@enho.me', $toEmail, $subject, $body, $attachmentUrl = null, $attachmentFilename = 'attachment'){
        require("assets/sendgrid-php/sendgrid-php.php");
        $myKey = '{YOUR_API_KEY_HERE}';

        $from = new SendGrid\Email($fromName, $fromEmail);
        $to = new SendGrid\Email($toEmail, $toEmail);
        $content = new SendGrid\Content("text/html", $body);
        $mail = new SendGrid\Mail($from, $subject, $to, $content);

        if($attachmentUrl){
            $file_encoded = base64_encode(file_get_contents($attachmentUrl));
            $attachment = new SendGrid\Attachment();
            $attachment->setContent($file_encoded);
            $attachment->setType("application/text");
            $attachment->setDisposition("attachment");
            $attachment->setFilename($attachmentFilename);
            $mail->addAttachment($attachment);
        }

        $apiKey = getenv($myKey);
        $sg = new \SendGrid($myKey);

        $response = $sg->client->mail()->send()->post($mail);
        //echo $response->statusCode();
        //print_r($response->headers());
        //echo $response->body();
        return $response;
    }
?>
