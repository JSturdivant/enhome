<?php

//echo $_POST['picfile'];
include_once('../../functions.php');
include_once('../../assets/aws/aws-autoloader.php');

// AUTHENTICATE

// DIRECT INJECTION METHOD
use Aws\S3\S3Client;

function getS3Client(){
  $secrets = getSecrets('s3');
  // Instantiate the S3 client with your AWS credentials
  $client = S3Client::factory(array(
      'credentials' => array(
          'key'    => $secrets['enhomephotos']['key'],
          'secret' => $secrets['enhomephotos']['secret'],
      ),
      'region' => 'us-east-2',
      'version' => '2006-03-01'
  ));

  return $client;
}

$client = getS3Client();

function getS3Buckets(){
  $client = getS3Client();
  // LIST BUCKETS
  $result = $client->listBuckets();

  return $client->listBuckets();
}


function getS3BucketObjects($bucket){
  $client = getS3Client();
  $iterator = $client->getIterator('ListObjects', array(
      'Bucket' => $bucket
  ));

  $data = array();
  foreach ($iterator as $object) {
    $data[] = $object;
  }

  return $data;
}

//print_r(getS3Buckets());
//print_r(getS3BucketObjects('enhomephotos'));


function uploadFile(){
  $client = getS3Client();

  $putdata = file_get_contents("php://input");
  $request = json_decode($putdata);

  $s3FilePath = $request->directory;

  if(substr($s3FilePath,strlen($s3FilePath)-1,1) != "/"){$s3FilePath = $s3FilePath.'/';}

  //$_POST = json_decode($_POST);
  $bucket = 'enhomephotos';

  // Upload an object to Amazon S3
  //print_r($_POST['picfile']);
  $image_parts = explode(";base64,", $request->picfile);
  $image_type_aux = explode("image/", $image_parts[0]);
  $image_type = $image_type_aux[1];
  $image_base64 = $image_parts[1];

  $dateTime = new DateTime();
  $fileName = $dateTime->getTimestamp() . "." . $image_type;

  $result = $client->putObject(array(
      'Bucket' => $bucket,
      'region' => 'us-east-2',
      'Key'    => $s3FilePath.$fileName,
        'ContentType'     => 'image/jpg',
      //'Body'   => 'Hello!'
      'Body'   => base64_decode($image_base64),
      'ACL'  => 'public-read'
  ));


  // Access parts of the result object
  //echo $result['Expiration'] . "\n";
  //echo $result['ServerSideEncryption'] . "\n";
  //echo $result['ETag'] . "\n";
  //echo $result['VersionId'] . "\n";
  //echo $result['RequestId'] . "\n";

  // Get the URL the object can be downloaded from
  //echo $result['ObjectURL'] . "\n";

  return $result['ObjectURL'];
}


$uploadUrl = uploadFile();

//print_r($upload);

$returnArray = array(
    'response' => array(
        'message' => 'Success!',
        'color' => 'green'
    ),
    'data' => array('url' => $uploadUrl),
);
returnArrayAsJSON($returnArray);

?>
