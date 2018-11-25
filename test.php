<?php
  function getSecrets($key){
    $secrets = json_decode(file_get_contents('secrets.json'),true);
    return $secrets[$key];
  }
  print_r(getSecrets('s3'));

 ?>
