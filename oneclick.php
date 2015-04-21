<?php
	// session must be started
	if (session_id() <= "") {
		session_start();
	}
	
	// parameters (you may change this values....)
	$width = 220; // background image width
	$height = 80; // background image height
	$bgColor = 'rgb(225, 225, 225)'; // background image color
	$userColor = false; /* 0=red, 1=pink, 2=navy, 3=blue, 4=purple, 5=dark red, 6=violet, 7=dark navy, 8=dark blue, 9=grey, 10=green, 11=yellow  */	
	// end of parameters

	// center of circles
	$x = rand(10, $width-15);
	$y = rand(10, $height-15);
	
	$cnum = rand(4,9); // number of circles
	
	$startColors = array( array(255, 123, 137), array(241, 74, 152), array(127, 118, 237), array(31, 158, 251), array(139, 43, 153), array(137, 28, 38), array(112, 14, 60), array(34, 25, 141), array(7, 53, 86), array(19, 104, 16), array(140, 140, 140), array(129, 115, 4) );
	$endColors = array( array(255, 179, 193), array(255, 174, 252), array(231, 222, 255), array(143, 255, 255), array(255, 175, 255), array(255, 140, 150), array(255, 170, 216), array(194, 185, 255), array(179, 225, 255), array(171, 255, 168), array(200, 200, 200), array(255, 243, 132) );

	// if user doesn't select a special color, select random color
	if ( $userColor == false )
		$selColor = rand(0, 11);
	else
		$selColor = $userColor;
	
	$colorIncr = rand(5, 7); // color swatch increment for each 
	
	// create color swatch
	$sorted_colors = array();	
	if ($cnum == 0 || $cnum % 2 == 0) { // if zero or even
		$r = $startColors[$selColor][0];
		$g = $startColors[$selColor][1];
		$b = $startColors[$selColor][2];	
		for ( $i=0; $i<12; $i++ ) { 
			if ( ( $r + $colorIncr ) < $endColors[$selColor][0] )
				$r = $r + $colorIncr;
			else
				$r = $endColors[$selColor][0];
			
			if ( ( $g + $colorIncr ) < $endColors[$selColor][1] )
				$g = $g + $colorIncr;
			else
				$g = $endColors[$selColor][1];
			
			if ( ( $b + $colorIncr ) < $endColors[$selColor][2] )
				$b = $b + $colorIncr;
			else
				$b = $endColors[$selColor][2];
			
			$color = "rgb(".$r.",".$g.",".$b.")";
			array_push($sorted_colors, $color); 
		} 
	} else { // if odd
		$r = $endColors[$selColor][0];
		$g = $endColors[$selColor][1];
		$b = $endColors[$selColor][2];		
		for ( $i=0; $i<12; $i++ ) { 	
			if ( ( $r - $colorIncr ) > $startColors[$selColor][0] )
				$r = $r - $colorIncr;
			else
				$r = $startColors[$selColor][0];
			
			if ( ( $g - $colorIncr ) > $startColors[$selColor][1] )
				$g = $g - $colorIncr;
			else
				$g = $startColors[$selColor][1];
			
			if ( ( $b - $colorIncr ) > $startColors[$selColor][2] )
				$b = $b - $colorIncr;
			else
				$b = $startColors[$selColor][2];
			
			$color = "rgb(".$r.",".$g.",".$b.")";
			array_push($sorted_colors, $color); 
		}		
	}

	// create background image
    $layer = new Imagick();
    $layer->newImage($width, $height, new ImagickPixel($bgColor));

	// create circles	
    $circle = new ImagickDraw();

	$j = $cnum;
	for ($i=0; $i<$cnum; $i++) {
		$circle->setFillColor($sorted_colors[$i+1]);
		if ($cnum < 7)
			$cw = rand(6, 8); // circles width if numbers of circles < 7
		else
			$cw = rand(3, 5); // circles width if number of circles >= 7	
		$circle->circle($x, $y, $x + $j*$cw, $y + $j*$cw); // set circle parameters
		$layer->drawImage($circle); // draw circle
		$j = $j - 1;		
	}

	// set position of center of circles into session variables	
	$_SESSION["oneclick_x"] = $x;
	$_SESSION["oneclick_y"] = $y;
	
	// create center rectangle with last swatch color	
	if ($cnum+2 > 11)
		$rect = 2;
	else
		$rect = 11;
	$circle->setFillColor($sorted_colors[$rect]); 
	$circle->rectangle($x-6, $y-6, $x + 6, $y + 6);
	$layer->drawImage($circle);

	// display image	
	$layer->setImageFormat("png");		
	header("Content-Type: image/png");
	echo $layer->getImageBlob();		  

?>