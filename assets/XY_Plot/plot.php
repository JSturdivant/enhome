<?php 

// Load the least-square regression class. 
require_once('XY_Plot.php' ); 

  //----------------------------------------------------------- 
  $imageWidth     = 600; 
  $imageHeight    = 200; 

  $leftMargin        = 10; 
  $rightMargin       = 35; 

  $topMargin         = 8; 
  $bottomMargin      = 20; 

  // Verticle scale 
  $Y_MajorScale   = 0.1; 
  $Y_MinorScale   = $Y_MajorScale / 5; 
  $X_MajorScale   = 0.1; 
  $X_MinorScale   = $X_MajorScale / 5; 
  //----------------------------------------------------------- 

  // Create image 
  $image = @imageCreate( $imageWidth , $imageHeight ) 
      or die( "Cannot Initialize new GD image stream" ); 

  //--------------------------------- 
  // Create basic color map 
  //--------------------------------- 
  $colorMap = array(); 

  //$colorMap[ "Background" ] = imageColorAllocate( $image , 0xDB , 0xB3 , 0xE6 ); 
  $colorMap[ "Background" ] = imageColorAllocate( $image , 255, 255, 255 ); 

  // Create a standard color palette 
  $colorMap[ "Black"       ] = imagecolorallocate( $image,   0,   0,   0 ); 
  $colorMap[ "Red"         ] = imagecolorallocate( $image, 192,   0,   0 ); 
  $colorMap[ "Green"       ] = imagecolorallocate( $image,   0, 192,   0 ); 
  $colorMap[ "Blue"        ] = imagecolorallocate( $image,   0,   0, 192 ); 
  $colorMap[ "Brown"       ] = imagecolorallocate( $image,  48,  48,   0 ); 
  $colorMap[ "Cyan"        ] = imagecolorallocate( $image,   0, 192, 192 ); 
  $colorMap[ "Purple"      ] = imagecolorallocate( $image, 192,   0, 192 ); 
  $colorMap[ "LightGray"   ] = imagecolorallocate( $image, 192, 192, 192 ); 

  $colorMap[ "DarkGray"    ] = imagecolorallocate( $image,  48,  48,  48 ); 
  $colorMap[ "LightRed"    ] = imagecolorallocate( $image, 255,   0,   0 ); 
  $colorMap[ "LightGreen"  ] = imagecolorallocate( $image,   0, 255,   0 ); 
  $colorMap[ "LightBlue"   ] = imagecolorallocate( $image,   0,   0, 255 ); 
  $colorMap[ "Yellow"      ] = imagecolorallocate( $image, 255, 255,   0 ); 
  $colorMap[ "LightCyan"   ] = imagecolorallocate( $image,   0, 255, 255 ); 
  $colorMap[ "LightPurple" ] = imagecolorallocate( $image, 255,   0, 255 ); 
  $colorMap[ "White"       ] = imagecolorallocate( $image, 255, 255, 255 ); 

  $colorMap[ "Gray10"      ] = imagecolorallocate( $image ,  26 ,  26 ,  26 ); 
  $colorMap[ "Gray20"      ] = imagecolorallocate( $image ,  51 ,  51 ,  51 ); 
  $colorMap[ "Gray30"      ] = imagecolorallocate( $image ,  77 ,  77 ,  77 ); 
  $colorMap[ "Gray40"      ] = imagecolorallocate( $image , 102 , 102 , 102 ); 
  $colorMap[ "Gray50"      ] = imagecolorallocate( $image , 128 , 128 , 128 ); 
  $colorMap[ "Gray60"      ] = imagecolorallocate( $image , 154 , 154 , 154 ); 
  $colorMap[ "Gray70"      ] = imagecolorallocate( $image , 180 , 180 , 180 ); 
  $colorMap[ "Gray80"      ] = imagecolorallocate( $image , 205 , 205 , 205 ); 
  $colorMap[ "Gray90"      ] = imagecolorallocate( $image , 230 , 230 , 230 ); 

  // New plot 
  $plot = new XY_Plot( $image ); 

  // Setup boundaries 
  $plot->sizeWindow 
  ( 
    $leftMargin, 
    $topMargin, 
    $imageWidth - $rightMargin, 
    $imageHeight - $bottomMargin 
  ); 

  // Draw border around graph area. 
  imageRectangle 
  ( 
    $image, 
    $leftMargin, 
    $topMargin, 
    $imageWidth - $rightMargin, 
    $imageHeight - $bottomMargin, 
    $colorMap[ "LightGray" ] 
  ); 

//--------------------------------------------------------------------------- 
// 
//--------------------------------------------------------------------------- 
function plotRenderData( $data, $color) 
{ 
  global $plot; 

  $plot->resetData(); 
  foreach ( $data as $point ) 
    $plot->addData( $point[ 0 ], $point[ 1 ] ); 

  $plot->setColor( $color ); 

  $plot->autoScaleX_MinMax( 0 ); 
  $plot->autoScaleY_MinMax( 1 ); 
} 

//--------------------------------------------------------------------------- 
// Custom vertical text-- just return value with dolor sign in front 
//--------------------------------------------------------------------------- 
function textCallback( $Value ) 
{ 
   return number_format( $Value, 1 ); 
} 

//--------------------------------------------------------------------------- 
// 
//--------------------------------------------------------------------------- 
function plotAddScale() 
{ 
  global $plot; 
  global $X_MajorScale; 
  global $X_MinorScale; 
  global $Y_MajorScale; 
  global $Y_MinorScale; 
  global $colorMap; 

  // Setup and draw minor horizontal scale (right to left). 
  $plot->SetX_MinorDivisionScale( $X_MinorScale ); 
  $plot->SetX_MinorDivisionColor( $colorMap[ "Gray90" ] ); 
  $plot->DrawX_MinorDivisions(); 

  // Setup and draw minor vertical scale (top to bottom). 
  $plot->SetY_MinorDivisionScale( $Y_MinorScale ); 
  $plot->SetY_MinorDivisionColor( $colorMap[ "Gray90" ] ); 
  $plot->DrawY_MinorDivisions(); 

  //---------------------------------- 
  // Setup and draw major horizontal scale (right to left). 
  //---------------------------------- 

  // Extend lines 5 pixels past margins for label. 
  $plot->SetX_MajorDivisionExtension( 5 ); 

  // Scale. 
  $plot->SetX_MajorDivisionScale( $X_MajorScale ); 

  // Division lines are blue. 
  $plot->SetX_MajorDivisionColor( $colorMap[ "Gray80" ] ); 

  // Text labels are dark gray. 
  $plot->SetX_MajorDivisionTextColor( $colorMap[ "Black" ] ); 
  $plot->SetX_MajorTextCallback( "textCallback" ); 

  // Draw it. 
  $plot->DrawX_MajorDivisions(); 

  //---------------------------------- 
  // Setup and draw major vertical scale (top to bottom) 
  //---------------------------------- 

  // Extend lines 5 pixels past margins for label. 
  $plot->SetY_MajorDivisionExtension( 5 ); 

  // Scale. 
  $plot->SetY_MajorDivisionScale( $Y_MajorScale ); 

  // Divisions in dark gray. 
  $plot->SetY_MajorDivisionColor( $colorMap[ "Gray80" ] ); 
  $plot->SetY_MajorTextCallback( "textCallback" ); 

  // Lables in dark gray. 
  $plot->SetY_MajorDivisionTextColor( $colorMap[ "Black" ] ); 

  // Draw it. 
  $plot->DrawY_MajorDivisions(); 
} 

function plotRenderRegression( $regression, $coefficients, $min, $max, $color ) 
{ 
  global $plot; 
  global $imageWidth; 
  global $leftMargin; 
  global $rightMargin; 

  $plot->resetData(); 

  $frameWidth = $imageWidth - $leftMargin - $rightMargin; 
  for ( $xIndex = 0; $xIndex < $frameWidth; ++$xIndex ) 
  { 
    $x = $xIndex * ( $max - $min ) / $frameWidth + $min; 
    $y = $regression->interpolate( $coefficients, $x ); 
    $plot->addData( $x, $y ); 
  } 

  $plot->setColor( $color ); 
  $plot->renderWithLines(); 

} 

?>