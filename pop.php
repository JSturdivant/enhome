<?php
  //error_reporting(0);
  include_once('functions.php');
  $dbh = db_connect();
?>


<form method='GET'>

  <h1>Pitch Scenario</h1>
  <label for='throws'>Pitcher Throws</label><select name='throws'>
    <option value='L'>Left</option>
    <option value='R'>Right</option>
  </select>
  <br><label for='hits'>Batter Hits</label><select name='hits'>
    <option value='L'>Left</option>
    <option value='R'>Right</option>
  </select>
  <br><label for='at_bat_number'>At Bat Number</label><input type='number' name='at_bat_number' value=<?php echo $_GET['at_bat_number']; ?>>
  <br><label for='pitch_number'>Pitch Number</label><input type='number' name='pitch_number' value=<?php echo $_GET['pitch_number']; ?>>
  <br><label for='outs'>Outs</label><input type='number' name='outs' value=<?php echo $_GET['outs']; ?>>
  <br><label for='balls'>Balls</label><input type='number' name='balls' value=<?php echo $_GET['balls']; ?>>
  <br><label for='strikes'>Strikes</label><input type='number' name='strikes' value=<?php echo $_GET['strikes']; ?>>


  <h3>Pitch n-1</h3>
  <label for='m1_pitch_type'>Pitch Type</label><select name='m1_pitch_type'>
    <?php
      $pitch_type_list = queryStmtToArray("SELECT abbrev, type from pop.REF_pitch_types ORDER BY type ASC;");
      foreach($pitch_type_list as $P){
        $selected = "";
        IF($_GET['m1_pitch_type'] == $P['abbrev']){$selected = 'SELECTED';}
        echo "<option value='".$P['abbrev']."' ".$selected.">".$P['type']."</option>";
      }
    ?>
  </select>

  <?php printPitchGridInputTable('m1');?>
  <br><label for='m1_x_zone'>X Zone</label><input type='number' name='m1_x_zone' value=<?php echo $_GET['m1_x_zone']; ?>>
  <br><label for='m1_z_zone'>Z Zone</label><input type='number' name='m1_z_zone' value=<?php echo $_GET['m1_z_zone']; ?>>
  <br><label for='m1_speed'>Speed</label><input type='number' name='m1_speed' value=<?php echo $_GET['m1_speed']; ?>>
  <br><label for='m1_spin_rate'>Spin Rate</label><input type='number' name='m1_spin_rate' value=<?php echo $_GET['m1_spin_rate']; ?>>
  <br><input type='checkbox' name='m1_swing' <?php if($_GET['m1_swing'] == 'on'){echo 'CHECKED';} ?>><label for='m1_swing'>Swing</label>
  <br><input type='checkbox' name='m1_contact' <?php if($_GET['m1_contact'] == 'on'){echo 'CHECKED';} ?>><label for='m1_contact'>Contact</label>
  <br><label for='m1_outcome_value'>Outcome Value</label><input type='number' name='m1_outcome_value' step=0.001 value=<?php echo $_GET['m1_outcome_value']; ?>>

  <h3>Pitch n-2</h3>
    <label for='m2_pitch_type'>Pitch Type</label><select name='m2_pitch_type'>
      <?php
        foreach($pitch_type_list as $P){
          $selected = "";
          IF($_GET['m2_pitch_type'] == $P['abbrev']){$selected = 'SELECTED';}
          echo "<option value='".$P['abbrev']."' ".$selected.">".$P['type']."</option>";
        }
      ?>
    </select>

    <?php printPitchGridInputTable('m2'); ?>
    <br><label for='m2_x_zone'>X Zone</label><input type='number' name='m2_x_zone' value=<?php echo $_GET['m2_x_zone']; ?>>
    <br><label for='m2_z_zone'>Z Zone</label><input type='number' name='m2_z_zone' value=<?php echo $_GET['m2_z_zone']; ?>>
    <br><label for='m2_speed'>Speed</label><input type='number' name='m2_speed' value=<?php echo $_GET['m2_speed']; ?>>
    <br><label for='m2_spin_rate'>Spin Rate</label><input type='number' name='m2_spin_rate' value=<?php echo $_GET['m2_spin_rate']; ?>>
    <br><input type='checkbox' name='m2_swing' <?php if($_GET['m2_swing'] == 'on'){echo 'CHECKED';} ?>><label for='m2_swing'>Swing</label>
    <br><input type='checkbox' name='m2_contact' <?php if($_GET['m2_contact'] == 'on'){echo 'CHECKED';} ?>><label for='m2_contact'>Contact</label>
    <br><label for='m2_outcome_value'>Outcome Value</label><input type='number' name='m2_outcome_value' step=0.001 value=<?php echo $_GET['m2_outcome_value']; ?>>

  <br><input type='submit'>
</form>
<script>

  function populatePitchZone(x,z,pitch_number){
    var zones;
    if(pitch_number == 'm1'){
      zones = {
        'x' : 'm1_x_zone',
        'z' : 'm1_z_zone',
      };
    } else if(pitch_number == 'm2'){
      zones = {
        'x' : 'm2_x_zone',
        'z' : 'm2_z_zone',
      };
    }

    var x_zone = document.getElementsByName(zones.x)[0];
    var z_zone = document.getElementsByName(zones.z)[0];
    x_zone.value = x;
    z_zone.value = z;
  }
  //populatePitchZone(9,9,'m1');

</script>

<?php

$setup_data = $_GET;
//print_r($setup_data);

if($setup_data['m1_swing'] == 'on'){$setup_data['m1_swing'] = 1;} else {$setup_data['m1_swing'] = 0;}
if($setup_data['m1_contact'] == 'on'){$setup_data['m1_contact'] = 1;} else {$setup_data['m1_contact'] = 0;}
if($setup_data['m2_swing'] == 'on'){$setup_data['m2_swing'] = 1;} else {$setup_data['m2_swing'] = 0;}
if($setup_data['m2_contact'] == 'on'){$setup_data['m2_contact'] = 1;} else {$setup_data['m2_contact'] = 0;}


//$test = queryStmtToArray('select * from pop.REF_pitch_types');

//print_r($test);

$pitch_setup = array(
  'scenario' => array(
    'pitcher_id' => '1',
    'hitter_id' => '2',
    'at_bat_number' => $setup_data['at_bat_number'],
    'pitch_number' => $setup_data['pitch_number'],
    'throws' => $setup_data['throws'],
    'hits' => $setup_data['hits'],
    'outs' => $setup_data['outs'],
    'runners' => 1,
    'balls' => $setup_data['balls'],
    'strikes' => $setup_data['strikes'],
  ),
  'm1' => array(
    'pitch_type' => $setup_data['m1_pitch_type'],
    'plate_x_zone' => $setup_data['m1_x_zone'],
    'plate_z_zone' => $setup_data['m1_z_zone'],
    'speed' => $setup_data['m1_speed'],
    'release_spin_rate' => $setup_data['m1_spin_rate'],
    'pfx_x' => 0.1,
    'pfx_z' => 0.1,
    'swing' => $setup_data['m1_swing'],
    'contact' => $setup_data['m1_contact'],
    'pitch_outcome_value' => $setup_data['m1_outcome_value'],
  ),
  'm2' => array(
    'pitch_type' => $setup_data['m2_pitch_type'],
    'plate_x_zone' => $setup_data['m2_x_zone'],
    'plate_z_zone' => $setup_data['m2_z_zone'],
    'speed' => $setup_data['m2_speed'],
    'release_spin_rate' => $setup_data['m2_spin_rate'],
    'pfx_x' => 0.1,
    'pfx_z' => 0.1,
    'swing' => $setup_data['m2_swing'],
    'contact' => $setup_data['m2_contact'],
    'pitch_outcome_value' => $setup_data['m2_outcome_value'],
  ),
);

$context_filter = "p_throws = '".$pitch_setup['scenario']['throws']."'
AND stand = '".$pitch_setup['scenario']['hits']."'";

// Pitch minus 1 where statements
  if (($pitch_setup['m1']['plate_x_zone'] && $pitch_setup['m1']['plate_z_zone'] && $pitch_setup['m1']['pitch_type'])){
    $pitch_m1_type_where = "AND pm1_pitch_type = '".$pitch_setup['m1']['pitch_type']."' ";
    $pitch_m1_x_zone_where = "AND pm1_plate_x_zone = ".$pitch_setup['m1']['plate_x_zone']." ";
    $pitch_m1_z_zone_where = "AND pm1_plate_z_zone = ".$pitch_setup['m1']['plate_z_zone']." ";
    $pitch_m1_contact_swing_where = "AND pm1_contact = ".$pitch_setup['m1']['contact']." AND pm1_swing = ".$pitch_setup['m1']['swing']." ";
  }

// Pitch minus 2 where statements
  if (($pitch_setup['m2']['plate_x_zone'] && $pitch_setup['m2']['plate_z_zone'] && $pitch_setup['m2']['pitch_type'])){
    $pitch_m2_type_where = "AND pm2_pitch_type = '".$pitch_setup['m2']['pitch_type']."' ";
    $pitch_m2_x_zone_where = "AND pm2_plate_x_zone = ".$pitch_setup['m2']['plate_x_zone']." ";
    $pitch_m2_z_zone_where = "AND pm2_plate_z_zone = ".$pitch_setup['m2']['plate_z_zone']." ";
    $pitch_m2_contact_where = "AND pm2_contact = ".$pitch_setup['m2']['contact']." ";
    $pitch_m2_contact_swing_where = "AND pm2_contact = ".$pitch_setup['m2']['contact']." AND pm2_swing = ".$pitch_setup['m2']['swing']." ";
  }

// X Location
  $qry_stmt = "SELECT plate_x_zone, count, expected_value, expected_value_sd
    FROM(
    	SELECT
    		plate_x_zone,
    		count(id) as count, avg(pitch_outcome_value) as expected_value, stddev(pitch_outcome_value) as expected_value_sd
    	FROM pop.2_pitch_prior_consolidated_table
    	WHERE
    		".$context_filter."
    		".$pitch_m1_x_zone_where."
    		".$pitch_m2_x_zone_where."
        ".$pitch_m1_contact_swing_where."
        ".$pitch_m2_contact_swing_where."
    	GROUP BY
    		plate_x_zone
    	ORDER BY avg(pitch_outcome_value) DESC
    ) as t1 WHERE count > 10";

  $x_locations = queryStmtToArray($qry_stmt);
  $total_xs = 0;
  for($i = 0; $i < count($x_locations); $i++){
    $total_xs = $total_xs + $x_locations[$i]['count'];
  }
  for($i = 0; $i < count($x_locations); $i++){
    //echo ' X '.$x_locations[$i]['count'] .'/'.$total_xs;
    $x_locations[$i]['probability'] = $x_locations[$i]['count']/$total_xs;
    //echo ' '.$x_locations[$i]['probability'] .' ';
  }


// Z Location
  $qry_stmt = "SELECT plate_z_zone, count, expected_value, expected_value_sd
    FROM(
      SELECT
        plate_z_zone,
        count(id) as count, avg(pitch_outcome_value) as expected_value, stddev(pitch_outcome_value) as expected_value_sd
      FROM pop.2_pitch_prior_consolidated_table
      WHERE
        ".$context_filter."
        ".$pitch_m1_z_zone_where."
        ".$pitch_m2_z_zone_where."
        ".$pitch_m1_contact_swing_where."
        ".$pitch_m2_contact_swing_where."
      GROUP BY
        plate_z_zone
      ORDER BY avg(pitch_outcome_value) DESC
    ) as t1 WHERE count > 10";

    $z_locations = queryStmtToArray($qry_stmt);
    $total_zs = 0;
    for($i = 0; $i < count($z_locations); $i++){
      $total_zs = $total_zs + $z_locations[$i]['count'];
    }
    for($i = 0; $i < count($z_locations); $i++){
      //echo ' Z '.$z_locations[$i]['count'] .'/'.$total_zs;
      $z_locations[$i]['probability'] = $z_locations[$i]['count']/$total_zs;
      //echo ' '.$z_locations[$i]['probability'].' ';
    }

    // OPTIMAL X,Z LOCATION
      $locations_array = array();
      $probability_sum_unadj = 0;
      // CYCLE THROUGH Xs
        for($x = 0; $x < count($x_locations); $x++){
          // CYCLE THROUGH Zs
          $x_location = $x_locations[$x]['plate_x_zone'];
          for($z = 0; $z < count($z_locations); $z++){
            // CYCLE THROUGH Zs
            $z_location = $z_locations[$z]['plate_z_zone'];
            $locations_array[] = array(
              'x_zone' => $x_location,
              'z_zone' => $z_location,
              'expected_outcome' => ($x_locations[$x]['expected_value'] + $z_locations[$z]['expected_value'])/2,
              'probability' => avg(array($x_locations[$x]['probability'] , $z_locations[$z]['probability'])),
            );
            //echo '<br>'.$x.','.$z.' - '.$x_locations[$x]['probability'] . ' & '. $z_locations[$z]['probability'].'='.avg(array($x_locations[$x]['probability'] , $z_locations[$z]['probability']));
            $probability_sum_unadj += avg(array($x_locations[$x]['probability'] , $z_locations[$z]['probability']));
          }
        }
        // ADJUST PROBABILITY AND CALCULATE AT BAT EXPECTED OUTCOME
        $at_bat_woba = 0;
        for($x = 0; $x < count($locations_array); $x++){
            $locations_array[$x]['probability'] = $locations_array[$x]['probability'] / $probability_sum_unadj;
            $at_bat_woba += $locations_array[$x]['probability'] * $locations_array[$x]['expected_outcome'];
        }

        echo '<h3>Expected Pitch wOBA Change</h3>'.$at_bat_woba;


        $grid_template = array(
          '-3' => array(),
          '-2' => array(),
          '-1' => array(),
          '0' => array(),
          '1' => array(),
          '2' => array(),
          '3' => array(),
          '4' => array(),
          '5' => array(),
          '6' => array(),
        );
        $grid_template = array(
          '-3' => $grid_template,
          '-2' => $grid_template,
          '-1' => $grid_template,
          '0' => $grid_template,
          '1' => $grid_template,
          '2' => $grid_template,
          '3' => $grid_template,
          '4' => $grid_template,
          '5' => $grid_template,
          '6' => $grid_template,
        );

        //$outcome_location_grid_raw = array();
        $outcome_location_grid_raw = $grid_template;
        $outcome_location_grid = $grid_template;
        $prediction_location_grid_raw = $grid_template;

        for($l = 0; $l < count($locations_array); $l++){
          $location = $locations_array[$l];
          $outcome_location_grid_raw[$location['x_zone']][$location['z_zone']] = $location['expected_outcome'];
          $prediction_location_grid_raw[$location['x_zone']][$location['z_zone']] = $location['probability'];
        }

        // SMOOTH LOCATION GRID

        for($gx = -1; $gx < count($outcome_location_grid_raw); $gx++){
          for($gz = -1; $gz < count($outcome_location_grid_raw[$gx]); $gz++){
            $surrounding_outcome_values = array(
              $outcome_location_grid_raw[$gx+1][$gz],
              $outcome_location_grid_raw[$gx-1][$gz],
              $outcome_location_grid_raw[$gx][$gz+1],
              $outcome_location_grid_raw[$gx][$gz-1],
            );
            $surrounding_outcome_avg = avg($surrounding_outcome_values);
            $outcome_location_grid[$gx][$gz] = avg(array($surrounding_outcome_avg, $outcome_location_grid_raw[$gx][$gz]));
          }
        }


// PITCH TYPE
  $qry_stmt = "SELECT pitch_type, count, expected_value, expected_value_sd
    FROM(
      SELECT
        pitch_type,
        count(id) as count, round(avg(pitch_outcome_value),3) as expected_value, round(stddev(pitch_outcome_value),3) as expected_value_sd
      FROM pop.2_pitch_prior_consolidated_table
      WHERE
        ".$context_filter."
        ".$pitch_m1_type_where."
        ".$pitch_m2_type_where."
        ".$pitch_m1_contact_swing_where."
        ".$pitch_m2_contact_swing_where."
      GROUP BY
        pitch_type
      ORDER BY avg(pitch_outcome_value) DESC
    ) as t1 WHERE count > 10";

    //echo $qry_stmt;
  $pitch_types = queryStmtToArray($qry_stmt);
  $total_pitches = 0;
  for($p = 0; $p < count($pitch_types); $p++){
    $total_pitches += $pitch_types[$p]['count'];
  }
  for($p = 0; $p < count($pitch_types); $p++){
    $pitch_types[$p]['probability'] = round($pitch_types[$p]['count'] / $total_pitches,2);
  }

  echo '<h1>Pitch Prediction Guide</h1>';
  printPitchLocationGrid($prediction_location_grid_raw, 'percent');

  echo '<h1>Pitch Value Guide</h1>';
  printPitchLocationGrid($outcome_location_grid, 'thousandths');


  // PRINT BEST PITCH TYPE
    echo '<h3>Pitch Type Chart</h3>';
    echo arrayToHtmlTable($pitch_types);


  // PRINT PITCH SETUP
    echo '<h3>Pitch Setup Info</h3>';
    echo '<pre>';
    print_r($pitch_setup);
    echo '</pre>';

    function getColorGradient($positiveValuePercent){
      if($positiveValuePercent){
        $redValue = 255 * (1-$positiveValuePercent);
        $greenValue = 255 * ($positiveValuePercent);

        return "rgb($redValue, $greenValue, 0)";
      } else {
        return "rgb(255,255,255)";
      }
    }

    function printPitchLocationGrid($location_grid, $units = 'thousandths'){
      // SET COLOR SCALE
        $max_value = -99999;
        $min_value = 99999;
        //echo '<pre>';
        //print_r($location_grid);
        //echo '</pre>';
        for($x = -3; $x < 6; $x++){
          for($z = -3; $z < 6; $z++){
            if ($location_grid[$x][$z] < 999999){
              $value = $location_grid[$x][$z];
              if($value > $max_value){$max_value = $value;}
              if($value < $min_value){$min_value = $value;}
            }
          }
        }

      // PRINT PITCH LOCATION CHART
        echo '<h3>Location Chart</h3>';
        echo '<table style="border: 1px solid black;">';
        for($z = 3; $z > -2; $z--){
          echo '<tr><th>'.$z.'</th>';
            for($x = -1; $x < 5; $x++){
              if($location_grid[$x][$z] < 9999999){
                if($units == 'thousandths'){$printValue = round($location_grid[$x][$z],3);
                } elseif($units == 'percent'){$printValue = round($location_grid[$x][$z]*100,0)."%";
                } else { $printValue = $location_grid[$x][$z];
                }
                echo '<td style="border: 1px solid black; background: '.getColorGradient(($location_grid[$x][$z] / ($max_value - $min_value))).'">';
                //echo '<span style="color: rgba(0,150,0,'.(($location_grid[$x][$z]-.05)*10).');">'.round($location_grid[$x][$z],3).'</span>';
                echo '<span style="color: black;">'.$printValue.'</span>';
                echo '</td>';
              } else {
                echo '<td></td>';
              }

            }
          echo '</tr>';

        }
        echo '<tr><th></th>';
        for($x = -1; $x < 5; $x++){
          echo '<th>'.$x.'</th>';
        }
        echo '</tr>';
        echo '</table>';
      }


    function printPitchGridInputTable($pitch_number){
      echo '<table>';
        for($y = 4; $y >= -2; $y--){
          echo '<tr><th>'.$y.'</th>';
          for($x = -1; $x < 4; $x++){
            if($x >=0 && $x <= 2 && $y >= 0 && $y <= 2){
              $shading = 'lightgray';
            } else {
              $shading = 'white';
            }
             echo '<td style="border: 1px solid gray; background: '.$shading.'; text-align: center;"><a href=\'javascript:void(0)\' onclick=\'populatePitchZone('.$x.','.$y.',"'.$pitch_number.'")\'>'.$x.','.$y.'</a></td>';
          }
         echo '</tr>';
      }
      echo '<tr><th></th>';

      for($x = -1; $x < 4; $x++){echo '<th>'.$x.'</th>';}
      echo '</table>';
    }

    function avg($array){
      $total = 0;
      $values = 0;
      for($c = 0; $c < count($array); $c++){
        if($array[$c]){
          $values = $values + 1;
          $total = $total + $array[$c];
        }
      }
      if ($values > 0){
        return ($total / $values);
      }
      return false;
    }
?>
