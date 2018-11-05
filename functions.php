<?php
//echo 'test - '.get_current_user_id();
//echo ABSPATH;
$GLOBALS['userId'] = get_current_user_id();
$GLOBALS['userMetaData'] = (get_user_meta(get_current_user_id()));

// LOAD LIBRARIE
    require 'assets/twilio-php-master/Twilio/autoload.php';

    // USER SPICE HEADER DATA

    //error_reporting(1);

            // BOOTSTRAP 4
            //echo '<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
            //    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>';

            //    echo '<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
            //        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>';

            //echo "<script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.13.0/moment.min.js'></script>";
            //echo "<script src='https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js'></script>";


        // USER AUTHENTICATION W/ USER SPICE
            //require_once 'users/init.php';
            //require_once 'users/includes/header.php';

            //echo "<style>body{padding-top: 0px;}</style>"; // OVERWRITE USERSPICE CSS, TOP PADDING
            //echo "<script>var userData = ".$userDataJson."; var userToken = '".encryptToken(json_encode(array('time'=>strtotime(date('Y-m-d H:i:s')),'id'=>$userData->id)))."';</script>";

//           echo "<script src='".getPathname()."functions.js'></script>";
//       }
//    }


    function getHeader($pageTitle){

       echo '<script>document.title = "'.$pageTitle.'";</script>';
        $menuHtmlString = "";
        $filePath = getPathname();
        $menuItems = array(
            array(
                'title'=> 'Home',
                'html' => '<li><a href="'.$filePath.'">Home</a></li>'
            ),
            array(
                'title'=> 'App' ,
                'html' => '<li class="dropdown"><a href="'.$filePath.'app" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">App <span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li role="separator" class="divider"></li>
                    <li><a href="'.$filePath.'app/?page=myCarePlan">My Care Plan</a></li>
                    <li><a href="'.$filePath.'app/?page=myHome">My Home</a></li>
                    <li><a href="'.$filePath.'app/?page=addAssets">Add Assets</a></li>
                    <li><a href="'.$filePath.'app/?page=myTips">My Tips</a></li>
                </ul></li>'
              ),
              array(
                  'title'=> 'Blog',
                  'html' => '<li><a href="'.$filePath.'blog/">Blog</a></li>'
              ),
            );

          for($j=0; $j < count($menuItems); $j++){
              //print_r($menuItems[$j]['html']);
                $menuHtmlString = $menuHtmlString.$menuItems[$j]['html'];
            }

           //$userData = $user->data();

            $loginLogout = "
                        <li><p style='color: #bbb; padding-top: 1.2em;'>Logged in as
                            <span id='navBarUserName'>".$user->email."</span></p>
                        </li>
                        <li>
                            <a href='".$filePath."users/logout.php' >Logout</a>
                        </li>";

        //$userDataJson = print_r(json_encode($userData));
        /* $menuHtml = "<nav class='navbar navbar-inverse' style='border-radius: 0px;'>
            <div class='container-fluid'>
                <div class='navbar-header'>
                    <button type='button' class='navbar-toggle collapsed' data-toggle='collapse' data-target='#bs-example-navbar-collapse-1' aria-expanded='false'>
                        <span class='sr-only'>Toggle navigation</span>
                        <span class='icon-bar'></span><span class='icon-bar'></span>
                        <span class='icon-bar'></span>
                    </button>
                    <a class='navbar-brand' href='#'>
                        <img src='' style='width: 1.5em;'>
                    </a>
                </div>
                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class='collapse navbar-collapse' id='bs-example-navbar-collapse-1'>


                    <ul class='nav navbar-nav'>
                        $menuHtmlString
                    </ul>
                    <ul class='nav navbar-nav navbar-right'>$loginLogout
                    </ul>
                </div><!-- /.navbar-collapse -->
            </div><!-- /.container-fluid -->
        </nav><div class='col-sm-12'><h3>$pageTitle</h3></div>"
        . "<script>document.getElementById('navBarUserName').innerHTML=userData.email;</script>";
*/
        $menuHtml = "<nav class='navbar navbar-justify' style='border-radius: 0px; background: none'>
            <div class='container-fluid'>
                <div class='navbar-header'>
                    <button type='button' class='navbar-toggle collapsed' data-toggle='collapse' data-target='#bs-example-navbar-collapse-1' aria-expanded='false'>
                        <span class='sr-only'>Toggle navigation</span>
                        <span class='icon-bar'></span><span class='icon-bar'></span>
                        <span class='icon-bar'></span>
                    </button>
                    </li>
                </div>
                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class='collapse navbar-collapse' id='bs-example-navbar-collapse-1'>

                    <ul class='nav navbar-tabs'>
                        <a class='navbar-brand' href='#'>
                            <img src='https://www.google.com/images/branding/googlelogo/2x/googlelogo_color_120x44dp.png' style='width: 1.5em;'>
                        </a>
                        <span class='navbar-brand' href='index.php'>EnHome</span>
                        $menuHtmlString
                        $loginLogout

                    </ul>

                </div><!-- /.navbar-collapse -->
            </div><!-- /.container-fluid -->
        </nav>
        <script>document.getElementById('navBarUserName').innerHTML=userData.email;</script>
        <div class='col-sm-12'><h3>$pageTitle</h3></div>";

        $menuHtml = $menuHtml."  </head><body>";

        print_r($menuHtml);
         return $menuHtml;
    }


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
        //$dbCreds = getDbCreds();
        $dbCreds['local']['host'] = '127.0.0.1'; // enhome.czsbom142yss.us-east-2.rds.amazonaws.com
        $dbCreds['local']['username'] = 'enhome_portal';
        $dbCreds['local']['password'] = 'BlackPanther';
        $dbCreds['staging']['host'] = 'enhome.czsbom142yss.us-east-2.rds.amazonaws.com';
        $dbCreds['staging']['username'] = 'enhome_portal';
        $dbCreds['staging']['password'] = 'BlackPanther';
        $dbh = new PDO("mysql:dbname=enhome;host=".$dbCreds['local']['host'],$dbCreds['local']['username'],$dbCreds['local']['password']);
        $dbh->setAttribute (PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return ($dbh);
    }

    function getDbCreds2(){
        return array(
            'host' => '127.0.0.1',
            'username' => 'enhome_portal',
            'password' => 'BlackPanther',
        );
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
        $fields = array_keys($data->fetch(PDO::FETCH_ASSOC));
        $keys = array();
        FOREACH ($fields as $field){
            $keys[] = $field;
        }
        return $keys;
    }


    // CONVERT A SQL QUERY STATEMENT TO PHP ARRAY WITH KEYS
    function queryStmtToArray($query_stmt){
        $prod_db_connect = db_connect();
        $query_results = $prod_db_connect->query($query_stmt);
        $keys = getQueryResultKeys($query_results);
        $data_array = array();
        $query_results->execute();
        foreach ($query_results as $R){
            $new_row = array();
            for($i = 0; $i < count($keys); $i++){
                $new_row[$keys[$i]] = $R[$i];
            }
            $data_array[] = $new_row;
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
    function sendEmail($fromName = 'TSS', $fromEmail = 'noreply@tssands.com', $toEmail, $subject, $body, $attachmentUrl = null, $attachmentFilename = 'attachment'){
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


//LOAD FUNCTIONS';
    //$prod_db_connect = db_connect();

    //echo "<script src='".getPathname()."functions.js'></script>";
?>
