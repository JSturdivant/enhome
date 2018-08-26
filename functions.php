<?php
    error_reporting(1);
 
    echo "<script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.13.0/moment.min.js'></script>";
    echo "<script src='https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js'></script>";
    
    include_once('secure/credentials.php');

// USER AUTHENTICATION W/ USER SPICE  
    require_once 'users/init.php';
    require_once 'users/includes/header.php';

    //echo  getPathname();
    
    function getHeader($pageTitle){
       //$userInfo = $user->data();
        $menuHtmlString = "";
        $filePath = getPathname();
        $menuItems = array(
            array(
                'title'=> 'EnHome', 
                'html' => '<li><a href="'.$filePath.'">EnHome</a></li>'
            ),
            array(
                'title'=> 'App' , 
                'html' => '<li class="dropdown"><a href="'.$filePath.'app" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">App <span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li role="separator" class="divider"></li>
                    <li><a href="'.$filePath.'app">App</a></li>
                    <li role="separator" class="divider"></li><li class="dropdown-header">Other Reports</li>
                    <li><a href="'.$filePath.'reporter/explorer">Analytics Explorer</a></li>
                </ul></li>'
                )
            );
          
          for($j=0; $j < count($menuItems); $j++){
              //print_r($menuItems[$j]['html']);
                $menuHtmlString = $menuHtmlString.$menuItems[$j]['html'];
            }
            
           //$userData = $user->data();
        
            $loginLogout = "
                        <li><p style='color: #bbb; padding-top: 1.2em;'>Logged in as <span id='navBarUserName'>".$user->email."</span></p></li>
                        <li><a href='".$filePath."users/logout.php' >Logout</a></li>";
            
        //$userDataJson = print_r(json_encode($userData));
         $menuHtml = "<nav class='navbar navbar-inverse' style='border-radius: 0px;'>
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

// LOAD LIBRARIE
    require 'assets/twilio-php-master/Twilio/autoload.php';
    
// DB FUNCTIONS    
    // CONNECT TO MySQL DB SERVER
    function db_connect () {   
        $dbCreds = getDbCreds();
        $dbh = new PDO("mysql:host=".$dbCreds['host'],$dbCreds['username'],$dbCreds['password']);
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
    function queryStmtToArray($query_stmt, $db = 'prod'){
        $prod_db_connect = db_connect();
        if($db != 'prod'){
            $prod_db_connect = db_connect();
        }
            //echo $query_stmt;
        $query_results = $prod_db_connect->query($query_stmt);
        if($query_results){
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
        } else {return false;}
    } 

    // CONVERT A SQL QUERY STATEMENT TO PHP ARRAY WITH KEYS    
    function queryStmtToArrayLABS($query_stmt){
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
    
    // CHECK USERNAME AND PW AGAINST SANDDRIVE
    function sanddriveLoginCheck($username, $password){
             //https://github.com/revelrylabs/TSS/issues/889#issuecomment-368975581
             //The endpoint is /external_auth, and when it is deployed to staging, it can be tested like this:
             //curl -d "email=<your email>&password=<your password>" -i -X POST http://tssweb.stage.tssands.com/external_auth

             //$username = "admin@tssands.com";
             //$password = "admin";

            $mode = getEnvironment();
            if($mode == 'prod'){
                $url = 'http://sanddrive.tssands.com/external_auth';
            } else {
                $url = 'http://sanddrive.tssands.com/external_auth';
            }
            //$mode = 'prod';
            
             $fields = array(
                'email' => urlencode($username),
                'password' => urlencode($password)
             );
             
             //print_r($fields);

             //url-ify the data for the POST
             foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
             rtrim($fields_string, '&');

             //open connection
             $ch = curl_init();

             //set the url, number of POST vars, POST data
             curl_setopt($ch,CURLOPT_URL, $url);
             curl_setopt($ch, CURLOPT_HEADER, 1);
             //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
             curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
             curl_setopt($ch,CURLOPT_TIMEOUT,10);

             curl_setopt($ch,CURLOPT_POST, count($fields));
             curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
             //execute post
             $result = curl_exec($ch);
             $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

             //close connection
             curl_close($ch);

             return $httpcode;
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
        
        
// SatScada SILO FUNCTIONS
    function updateSiloTagHistory(){
        logEvent('updateSiloTagHistory()', 'called');
        $dbh = db_connect();
        $data_array = array();
        //GET LIST OF DEVICES
        $devices = getSatScada('devices');
        
        // CYCLE THROUGH ALL DEVICES
        foreach ($devices['device'] as $D){
            $device = $D['deviceNum'];
            $lat = $D['lat'];
            $lng = $D['lng'];
            $silo = returnSiloSet($device);
           //echo $device;
           logEvent('updateSiloTagHistory()', 'Device #'.$device);
            // GET LAST TIMESTAMP FOR DEVICE
            $check_stmt = "select ifnull(max(timestamp), '2018-01-01 00:00:00') as timestamp FROM silo_history WHERE device = $device";
            $check = $dbh->query($check_stmt);
            
            $insert_stmt = "INSERT INTO silo_locations (device, silo_set, timestamp, lat, lng, inserted_at) VALUES ('".$device."', '".$silo."', '".date('Y-m-d H:i:s')."', '".$lat."', '".$lng."', '".date('Y-m-d H:i:s')."')";
            $dbh->exec($insert_stmt);
            
            foreach ($check AS $C){
                //print_r($C);
                // GET PROPER START TIME
                $C['timestamp'] =  date('Y-m-d\TH:i:s',strtotime($C['timestamp']) + 30);
                
                // GET DATA SINCE START TIME
                $data_array[] = getSiloTagHistory($device, date('Y-m-d\TH:i:s', strtotime($C['timestamp'])), date('Y-m-d\TH:i:s'), $interpolate = '4h');
                //sleep(1);
            }
        }
        logEvent('updateSiloTagHistory()', 'completed');
        
        return $data_array;
    }
    
    function insertSiloTagHistoryNode($node){
        $dbh = db_connect();
        $keys = array_keys($node);
        for($i = 1; $i < count($node); $i++){
            if($node['Timestamp'] != "" && $node[$keys[$i]] != "" && is_null($node[$keys[$i]]) === false){
                //print_r( $node);
                //echo "<BR>".$insert_stmt."<BR>";
                $insert_stmt = "INSERT INTO silo_history (device, tagId, description, timestamp, siloSet, unixtime, value, value_float, inserted_at) VALUES ('".$node['device']."', '', '".$keys[$i]."', '".$node['Timestamp']."', '".$node['siloSet']."', '".$node['unixTime']."', '".$node[$keys[$i]]."', '".$node[$keys[$i]]."', now())";
                $dbh->exec($insert_stmt);
            }
        }
    }
    
    function getSiloTagHistory($device, $start, $end, $interpolate = '4h'){
        logEvent('getSiloTagHistory()', 'called');

        $tags = '';
        $date = new DateTime();
        $date->setTimezone(new DateTimeZone('America/Chicago'));
        $end = $date->format('c');
        $date = new DateTime($start);
        $date->setTimezone(new DateTimeZone('America/Chicago'));
        $start = $date->format('c');

        //GET TAG DATA HISTORY
        
            //$interpolate = '5m';
            //sleep(rand(0,10));
            $url = "tagdata/export/$device?start=$start&end=$end";
            $data_array = getSatScada($url, null, 'text/plain'); // GET LINK TO CSV
            $csv =  getSatScada($data_array, null, 'text/plain'); // GET CSV DATA
            //print_r($csv);
       //PARSE CSV INTO ARRAY
            $lines = explode( "\r\n", $csv );
            //print_r($lines);
            $data_array = array();
            $headers = explode(",",$lines[1]);
            unset($lines[0]);
            unset($lines[1]);
            foreach($lines as $R){
                $newNode = array();
                $newNode['device'] = $device;
                $newNode['siloSet'] = returnSiloSet($device);
                $cells = explode( ",", $R );
                for ($c=0; $c < count($headers); $c++){
                    $newNode[$headers[$c]] = $cells[$c];
                }
                $data_array[] = $newNode;
                insertSiloTagHistoryNode($newNode);
            }
       //FORMAT TIME 
            for($i = 0; $i < count($data_array); $i++){
                $data_array[$i]['unixTime'] = date('U', strtotime($data_array[$i]['Timestamp']));
                $data_array[$i]['Timestamp'] = date('Y-m-d H:i:s', strtotime($data_array[$i]['Timestamp']));
            }     
            
        //PURGE EMPTY DATA
            for($t = 0; $t < count($data_array); $t++){
                if($data_array[$t]['unixTime'] == "0"){unset($data_array[$t]);}
            }
            
        logEvent('getSiloTagHistory()', 'completed');
        return $data_array;
    }
    
    function getBentekSiloSets(){
        // SHOUDL ALIGN WITH DATA IN 'silo_sets' TABLE IN LABS DB
        return queryStmtToArrayLABS("select id, name as 'siloSet', device, MobileID, SNR from silo_sets;");
        /*
        return array(
            array('siloSet' => 'SC-0014', 'device' => 1, 'serialNum' => "01353241SKY10BA", 'imei' => '356144040576411'),
            array('siloSet' => 'SC-0017', 'device' => 2, 'serialNum' => "01353289SKYD1AA", 'imei' => '356144040607992'),
            array('siloSet' => 'SC-0019', 'device' => 3, 'serialNum' => "01353242SKY94BF", 'imei' => '356144040595510'),
            array('siloSet' => 'SC-0018', 'device' => 4, 'serialNum' => "01353237SKY00A6", 'imei' => ''),
            array('siloSet' => 'SC-0021', 'device' => 5, 'serialNum' => "01353233SKYF092", 'imei' => '356144040783942'),
            array('siloSet' => 'SC-0022', 'device' => 6, 'serialNum' => "01353250SKYB4E7", 'imei' => '356144040782530'),
            array('siloSet' => 'SC-0023', 'device' => 9, 'serialNum' => "01353287SKYC9A0", 'imei' => ''),
        );*/
    }
    
    function returnSiloSet($device){
        $pairs = getBentekSiloSets();
        foreach ($pairs as $P){
            if ($P['device'] == $device){return $P['siloSet'];}
        }
        return false;
    }
    function returnDevice($silo){
        $pairs = getBentekSiloSets();
        foreach ($pairs as $P){
            if ($P['siloSet'] == $silo){return $P['device'];}
        }
        return false;
    }
    
    function getSatScada($endpoint,$postData = null, $accept = 'application/json'){
        logEvent('getSatScada()', 'called '.$endpoint);
        $base_url = "https://satscada.com/";
        $username = "tssllc";
        $password = "tss3329!";
        $payloadName = array();
        $additionalHeaders = array("Accept: $accept");
        //echo '<br>'.$base_url.$endpoint;
        $process = curl_init($base_url.$endpoint);
        //curl_setopt($process, CURLOPT_HTTPHEADER, array('Content-Type: application/xml', $additionalHeaders));
        curl_setopt($process, CURLOPT_HTTPHEADER, array("Accept: $accept"));
        curl_setopt($process, CURLOPT_HEADER, 0);
        curl_setopt($process, CURLOPT_USERPWD, $username . ":" . $password);
        curl_setopt($process, CURLOPT_TIMEOUT, 30);
        //curl_setopt($process, CURLOPT_POST, 1);
        //curl_setopt($process, CURLOPT_POSTFIELDS, $payloadName);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
        $return = curl_exec($process);
        curl_close($process);
        //print_r($return);
        logEvent('getSatScada()', 'completed '.$endpoint);
        if($accept == 'application/json'){
            return json_decode($return, true);
        } else {
            return $return;
        }
        
    }
// GEOFORCE FUNCTIONS    
 function getGeoforceData(){
    logEvent('getGeoforceData()', 'called ');
    $dbh = db_connect();
    $geoforcekey = "oldas3J2RLALPTJJqTrV_w3xtSAH03lUaBh__Oqa8O0";
    $secret = "MMavs-6onGtQXGzrhRj73LwXFoo9eh_cN2KBS2Dcjgo";
    $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJrZXkiOiJvbGRhczNKMlJMQUxQVEpKcVRyVl93M3h0U0FIMDNsVWFCaF9fT3FhOE8wIn0.1t6uvr645gLmpHzp5E0bInM2xQBnXwwHDr1vCsZCdZk";

    $fromDate = date('c',date('U') - (60*60*25));
    $toDate = date('c');


    $url = "https://readings.geoforce.net/readings?from=$fromDate"."Z&token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJrZXkiOiJvbGRhczNKMlJMQUxQVEpKcVRyVl93M3h0U0FIMDNsVWFCaF9fT3FhOE8wIn0.1t6uvr645gLmpHzp5E0bInM2xQBnXwwHDr1vCsZCdZk";


    //GET DEVICE LIST

        $data_array = json_decode(file_get_contents($url),true);

        $devices = array();

        // DEDUPLICATE DATA
        for($i = 0; $i < count($data_array['readings']); $i++){
            $item = $data_array['readings'][$i];
            //print_r($item);
            $ESN = $item['esn'];

            // CHECK FOR DUPLICATE ENTRIES
                $check = queryStmtToArrayLABS("SELECT id FROM geoforce_locations WHERE timestamp = '".date('Y-m-d H:i:s',strtotime($item['timestamp']))."' AND esn = '".$item['esn']."'");

            // IF NO DUPS, INSERT NEW RECORD
            if (count($check) == 0){
                $insert_stmt = "INSERT INTO geoforce_locations (timestamp, esn, asset_name, asset_number, stopped,  lat, lng, inserted_at) VALUES ('".date('Y-m-d H:i:s',strtotime($item['timestamp']))."', '".$item['esn']."', '".$item['asset']['name']."', '".$item['asset']['number']."','".$item['status']['stopped']."', '".$item['position']['coordinates'][1]."', '".$item['position']['coordinates'][0]."', now())";
                $dbh->exec($insert_stmt);
            }

            $found = false;
            for($j = 0; $j < count($devices); $j++){
                if($devices[$j]['esn'] == $ESN){
                    $found = true;
                    //CHECK FOR LATER TIME
                    if(strtotime($devices[$j]['timestamp']) < strtotime($item['timestamp'])){ // UPDATE
                        $devices[$j]['timestamp']=$item['timestamp'];
                    }
                }
            }

            if($found == false){
                $devices[] = $item;
            }

        }
        logEvent('getGeoforceData()', 'completed ');
        return $devices;
    }
    
// TWILIO TXT TOOL 
    //SEND TEXT FUNCTION
    function txtDriver($number, $message){
        logEvent('txtDriver()', $number.': '.$message);
    // Send an SMS using Twilio's REST API and PHP

        $sid = "AC95f14bc054a463f5206ad1a53026a3b3"; // Your Account SID from www.twilio.com/console
        $token = "6bf104403e81f3c0ce32a96251db3ad8"; // Your Auth Token from www.twilio.com/console
        $from = '14694163088';
        $client = new Twilio\Rest\Client($sid, $token);
        $message = $client->messages->create(
          $number, // Text this number
          array(
            'from' => $from, // From a valid Twilio number
            'body' => $message
          )
        );

        return array(
            'result' => $message->sid,
            'to'=> $number,
            'from' => $from,
            'message' => $message,
            'time' => date('Y-m-d H:i:s')
        );
    }
    
// GOOGLE MAPS FUNCTIONS
    function getGoogleDirections($start_location, $end_location){
        logEvent('getGoogleDirections()', 'called');
        //echo $start_location, $end_location;
        $directions_text = "https://maps.googleapis.com/maps/api/directions/json?origin=".$start_location
            ."&destination=".$end_location
            //. "&departure_time=1343641500"
            ."&alternatives=true"
            //."&departure_time=".(max(strtotime($_GET['search_time']),date())+3600*6)
            ."&alternatives=false"
            . "&key=AIzaSyAAyd3d2GnSnvI0tvKXCRXwzIyRQEyZqEs";//AIzaSyDhyd85T3GXlOaXrq4KVXL-HXRLrotpu0Q";
        //echo $directions_text.'<br>';
        //echo $directions_text;
        $directions_json = file_get_contents($directions_text);
        $directions_file = json_decode($directions_json, true);
        logEvent('getGoogleDirections()', 'completed');
        return $directions_file;
    }
    
    function getGoogleDirectionsETA($start_location, $end_location){
        $directions = getGoogleDirections($start_location, $end_location);
        //print_r($directions['routes'][0]['legs'][0]);
        return array('seconds' => $directions['routes'][0]['legs'][0]['duration']['value'], 'meters' => $directions['routes'][0]['legs'][0]['distance']['value']);
    }
   
// LIVE SANDDRIVE DATA
    function getCurrentLoadLocations($well){
        logEvent('getCurrentLoadLocations()', 'called');
        $dbh = db_connect();
        $query = "select truck_loads.id, stages.well_id, stage_id, driver_id, users.first_name, users.last_name, 
            users.company_name, users.truck_number, sand_type_id, sand_types.name as sand, 
            truck_loads.measured_weight, locations.recorded_at as last_seen, 
            locations.lat as current_lat, locations.lng as current_lng, 
            St_X(wells.geom) as well_lat, St_Y(wells.geom) as well_lng,  St_X(staging_pads.geom) as staging_lat, St_Y(staging_pads.geom) as staging_lng,
            truck_load_events.status, truck_loads.dispatch_notes 
            FROM truck_loads
            LEFT JOIN (
                    SELECT user_id, recorded_at, max(St_X(geom)) as lat,max(St_Y(geom)) as lng 
                    FROM user_locations 
                    WHERE recorded_at > (now() - interval '6 hours') AND (user_id, recorded_at) IN (SELECT user_id, max(recorded_at) FROM user_locations GROUP BY user_id)
                    GROUP BY user_id, recorded_at
                    ORDER BY recorded_at desc
            ) AS locations on truck_loads.driver_id = locations.user_id
            LEFT JOIN stages ON truck_loads.stage_id = stages.id
            LEFT JOIN sand_types ON truck_loads.sand_type_id = sand_types.id
            LEFT JOIN wells ON stages.well_id = wells.id 
            LEFT JOIN users on truck_loads.driver_id = users.id
            LEFT JOIN locations as staging_pads ON wells.staging_area_id = staging_pads.id
            LEFT JOIN (
                SELECT truck_load_id, array_agg(status) as status FROM truck_load_events WHERE deleted_at IS NULL GROUP BY truck_load_id
            ) as truck_load_events ON truck_loads.id = truck_load_events.truck_load_id
            WHERE driver_id IS NOT NULL AND
            truck_load_events.status IS NOT NULL AND
            wells.id = $well AND
            truck_loads.id NOT IN (select truck_load_id FROM truck_load_events WHERE status IN ('unloaded', 'rerouted')) ";
        //$data = $dbh->query($query);
        $data_array = queryStmtToArray($query);
        
        logEvent('getCurrentLoadLocations()', 'completed');
        return $data_array;
    }
    
// SENDGRID
    function sendEmail($fromName = 'TSS', $fromEmail = 'noreply@tssands.com', $toEmail, $subject, $body, $attachmentUrl = null, $attachmentFilename = 'attachment'){
        require("assets/sendgrid-php/sendgrid-php.php");
        $myKey = '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';
    
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
    
    
// SCHEDULED TASKS
    function scheduledEmails(){
        logEvent('scheduledEmails()', 'called');
        $dbh = db_connect();
        $dbh_prod = db_connect();
        $return_array = array('executed' => array());
        
        $query_stmt = 'SELECT email_updates.id, title, description,`interval`, query, function, function_params, csv, concat("[",group_concat(\'"\',email_updates_subscriptions.email,\'"\'),"]") as subscribers '
                . 'FROM email_updates '
                . 'LEFT JOIN email_updates_subscriptions ON email_updates.id = email_updates_subscriptions.update_id '
                . 'WHERE email_updates.deleted_at IS NULL AND email_updates_subscriptions.removed_at IS NULL GROUP BY email_updates.id';

        $schedules = queryStmtToArrayLABS($query_stmt);
        for($i = 0; $i < count($schedules); $i++){
            $schedules[$i]['subscribers'] = json_decode($schedules[$i]['subscribers'], false);
        }
        
        foreach($schedules as $S){
            // CHECK INTERVAL AGAINST LAST RUN
            $check = $dbh->query("select max(run_at) as run_at FROM scheduled_task_log WHERE result = 'Success' AND title = '".$S['title']."' ");
            $lastRun = 0;
            foreach($check as $C){$lastRun = $C[0];}

            if((date('U', strtotime($lastRun)) < date('U') - $S['interval']*(60*60))){ //IF INTERVAL SINCE LAST RUN < DESIGN
                $return_array['executed'][] = $S['title'];
                // IF ITS A SIMPLE QUERY UPDATE
                if($S['query']){
                    $S['description'] = $S['description'].arrayToHtmlTable(queryStmtToArray($S['query']));   
                } elseif($S['function']){
                    $S['description'] = $S['function']($S['function_params']);
                }
                
                // CYCLE THROUGH EACH TO ADDRESS 
                foreach($S['subscribers'] as $T){
                    //$return_array[] = sendEmail('noreply@tssands.com', 'noreply@tssands.com', $T, $S['title'], $S['description'], $S['attachmentUrl'], $S['attachmentName']);
                }
                
                // LOG SCHEDULED TASK RUN
                $dbh->exec("INSERT INTO scheduled_task_log (title, identifier, run_at, result, detail) VALUES ('".$S['title']."','".$S['identifier']."', '".date('Y-m-d H:i:s')."', 'Success', '$send')");
            }           
        }
        logEvent('scheduledEmails()', 'completed');
        return $return_array;
    }
    
    function driverEtasByWell(){
        logEvent('driverEtasByWell()', 'called');
        $body = "";
        $query_stmt = "select well_id, well ,  max(last_unload) as last_unload FROM (
                select stages.id, stage_number, well_id, wells.name as well, max(completed_loads.recorded_at) as last_unload, count(distinct loads.id) as total_loads, count(distinct completed_loads.id) as completed_loads, count(distinct loads.id) = count(distinct completed_loads.id) as stage_complete
                FROM stages
                LEFT JOIN wells on stages.well_id = wells.id
                LEFT JOIN truck_loads as loads ON loads.stage_id = stages.id
                LEFT JOIN (
                        SELECT truck_loads.id, truck_loads.stage_id, truck_load_events.recorded_at FROM truck_loads LEFT JOIN truck_load_events ON truck_load_events.truck_load_id = truck_loads.id WHERE status = 'unloaded'
                ) as completed_loads on completed_loads.stage_id = stages.id
                GROUP BY stages.id, wells.name
            ) as data WHERE last_unload > (now() - interval '3 days') GROUP BY well, well_id";

        $wells=(queryStmtToArray($query_stmt));
        for($w = 0; $w < count($wells) ; $w++){
            $locations = getCurrentLoadLocations($wells[$w]['well_id']);
            $printedLocations = array();
            for($i = 0; $i < count($locations); $i++){
                $locations[$i]['latestStatus'] = getLatestStatus($locations[$i]['status']);
                //if ($locations[$i]['latestStatus'] != '' && $locations[$i]['latestStatus'] != 'dispatch' && $locations[$i]['latestStatus'] != 'job_requested'){ // TEST IF ITEM QUALIFIES TO BE INCLUDED IN EMAIL (LOADED TO AT-WELL-SITE) 
                if ($locations[$i]['latestStatus'] == 'loaded_event' || $locations[$i]['latestStatus'] == 'at_staging_area' || $locations[$i]['latestStatus'] == 'called_to_well_site' || $locations[$i]['latestStatus'] == 'at_well_site'){ // TEST IF ITEM QUALIFIES TO BE INCLUDED IN EMAIL (LOADED TO AT-WELL-SITE) 
                    $locations[$i]['Sand'] = $locations[$i]['sand'];
                    unset($locations[$i]['sand']);
                    $locations[$i]['Carrier'] = $locations[$i]['company_name'];
                    unset($locations[$i]['company_name']);
                    $locations[$i]['Driver Name'] = $locations[$i]['first_name']." ".$locations[$i]['last_name'];
                    unset($locations[$i]['first_name']);
                    unset($locations[$i]['last_name']);
                    $locations[$i]['Truck #'] = $locations[$i]['truck_number'];
                    unset($locations[$i]['truck_number']);
                    $locations[$i]['Latest Status'] = $locations[$i]['latestStatus'];
                    unset($locations[$i]['latestStatus']);
                    $locations[$i]['Measured Weight'] = $locations[$i]['measured_weight'];
                    unset($locations[$i]['measured_weight']);
                    //$locations[$i]['eta'] = '--';
                    $locations[$i]['ETA Date'] = null;
                    $locations[$i]['ETA Time'] = null;
                    $locations[$i]['T+Hours'] = null;
                    $locations[$i]['Minutes'] = 9999999; // DEFAULT TO VERY LONG ETA
                    $locations[$i]['Dispatch Notes'] = $locations[$i]['dispatch_notes'];
                    unset($locations[$i]['dispatch_notes']);
                    if(strpos($locations[$i]['status'], 'at_well_site')){$locations[$i]['Minutes'] = 0;}  // IF ALREADY AT THE WELL, ETA IS 0
                                        //$locations[$i]['Latest Status'] = getLatestStatus($locations[$i]['status']);
                    if((strpos($locations[$i]['status'], 'Xat_pull_point') > 0 || strpos($locations[$i]['status'], 'loaded_event') > 0) && strpos($locations[$i]['status'], 'at_well_site') == false && $locations[$i]['current_lat'] && $locations[$i]['current_lng'] && $locations[$i]['well_lat'] && $locations[$i]['well_lng']){
                        $seconds = getGoogleDirectionsETA(($locations[$i]['current_lat'].",".$locations[$i]['current_lng']), ($locations[$i]['well_lat'].",".$locations[$i]['well_lng']))['seconds'];
                        $eta = date('U',($seconds + date('U')));
                        $dt = new DateTime('@'.$eta);
                        $dt->setTimeZone(new DateTimeZone('America/Chicago'));
                        $eta = $dt->format('F j, Y, g:i a');
                        //$locations[$i]['eta'] = $eta;
                        $locations[$i]['ETA Date'] = date('m/d/Y', strtotime($eta));
                        $locations[$i]['ETA Time'] = date('H:i', strtotime($eta));
                        $locations[$i]['T+Hours'] = "T+".date('H:i', $seconds);
                        $locations[$i]['Minutes'] = round($seconds/60,0); // SET TRUE ETA FOR ALL AT-PULL-POINT AND BEYOND TRUCKS
                    }
                    //array_multisort($locations[$i]['eta']);
                    $locations[$i]['Current Location'] = "<a href='https://www.google.com/maps/@".$locations[$i]['current_lat'].",".$locations[$i]['current_lng'].",12z'>Current Location</a>";
                
                   $printedLocations[] =  $locations[$i];  // ADD QUALIFYING LOCATIONS TO PRINT LIST
                }
            }
            usort($printedLocations, "cmpEta");
            //usort($locations, "cmpSand");
            for($i = 0; $i < count($printedLocations); $i++){
                unset($printedLocations[$i]['sand_type_id']);
                unset($printedLocations[$i]['stage_id']);
                unset($printedLocations[$i]['driver_id']);
                unset($printedLocations[$i]['current_lat']);
                unset($printedLocations[$i]['current_lng']);
                unset($printedLocations[$i]['well_lat']);
                unset($printedLocations[$i]['well_lng']);
                unset($printedLocations[$i]['staging_lat']);
                unset($printedLocations[$i]['staging_lng']);
                unset($printedLocations[$i]['status']);
                unset($printedLocations[$i]['well_id']);
                unset($printedLocations[$i]['last_seen']);
                unset($printedLocations[$i]['Minutes']);

            }
            $body = $body."<h3>".$wells[$w]['well']."</h3>".arrayToHtmlTable($printedLocations);
        }
        logEvent('driverEtasByWell()', 'completed');
        return $body;
    }
    
    
    function cmpEta($a, $b)
    {
        if($a['Minutes'] == null){$a['Minutes'] = 0;}
        if($b['Minutes'] == null){$b['Minutes'] = 0;}
        return $a['Minutes'] < $b['Minutes'];
    }
    function cmpSand($a, $b)
    {
        return strcmp($a['Sand'] , $b['Sand']);
    }
    
    function getLatestStatus($status_string){
        if(strpos($status_string, 'unloaded')>0){return 'unloaded';}
        elseif(strpos($status_string, 'at_well_site')>0){return 'at_well_site';}
        elseif(strpos($status_string, 'called_to_well_site')>0){return 'called_to_well_site';}
        elseif(strpos($status_string, 'at_staging_area')>0){return 'at_staging_area';}
        elseif(strpos($status_string, 'loaded_event')>0){return 'loaded_event';}
        elseif(strpos($status_string, 'at_pull_point')>0){return 'at_pull_point';}
        elseif(strpos($status_string, 'dispatch')>0){return 'dispatch';}
        elseif(strpos($status_string, 'job_requested')>0){return 'job_requested';}
        return false;
    }
    
    function updateDriverEtasForAllWells(){
        logEvent('updateDriverEtasForAllWells()', 'called');
        $wells = queryStmtToArray("SELECT id FROM wells WHERE closed = false ORDER BY random() limit 10");
        $data_array = array();
        foreach ($wells as $W){
            logEvent('updateDriverEtasForAllWells()', 'Well #'.$W['id']);
            $data_array[] = file_get_contents('http://tsslabsreporter3-env.ivmn3kmyrq.us-east-1.elasticbeanstalk.com/general/api/getloadetas/?key=testkey&well='.$W['id']);
        }
        logEvent('driverEtasByWell()', 'completed');
        return $data_array;
    }
    
    
    
// PERISCOPE DATA
// 


/**
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED.
 * IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */
/**
 * Class to create embeded URLs to use with periscopedata.com
 *
 * @link https://doc.periscopedata.com/docv2/embed-api
 * @license https://opensource.org/licenses/MIT
 */
    class EmbedUrl
    {
        /**
         *
         * @var string
         */
        protected $apiKey;
        /**
         *
         * @var array
         */
        protected $options;
        const PATH = '/api/embedded_dashboard';
        const URL = 'https://www.periscopedata.com';
        public function __construct($apiKey, array $options = [])
        {
            $this->apiKey = $apiKey;
            $this->options = $options;
        }
        public function getSignature()
        {
            return hash_hmac('sha256', self::PATH . '?data=' . $this->getEncodedData(), $this->apiKey);
        }
        public function getEncodedData()
        {
            return urlencode(json_encode($this->options));
        }
        public function getLink()
        {
            return sprintf(self::URL . self::PATH . '?data=%s&signature=%s', $this->getEncodedData(), $this->getSignature());
        }
    }
//LOAD FUNCTIONS';
    //$prod_db_connect = db_connect();

    echo "<script src='".getPathname()."functions.js'></script>";
?>