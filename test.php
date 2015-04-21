<?php

// session must be started
if (session_id() <= "") {
	session_start();
}

// for form fields
$firstn = "";
$lastn = "";
$msg = "";

require_once "checkSpam.php";

// check form data (script step)
if (isset($_POST["firstn"])) {
	$firstn = $_POST["firstn"];
	$lastn = $_POST["lastn"];
}

// if form data was entered
if ( $firstn > " " && $lastn > " " ) {
	// check if image submit button was clicked
	if ( !isset($_POST["oneclickimg_x"]) ) {
		// user's data will be displayed with new oneclick
		$msg = "Are you human? I don't think so?";		
	} else {
		// get image submit button clicked position
		$x = $_POST["oneclickimg_x"];
		$y = $_POST["oneclickimg_y"];
		// get session variables (center of circles)
		$rx = $_SESSION["oneclick_x"];
		$ry = $_SESSION["oneclick_y"];
		// clear session variables
		unset($_SESSION["oneclick_x"]);
		unset($_SESSION["oneclick_y"]);
	
		// check if clicked position was correct. Center dimentions are 5px * 5px. We give 2px more for lapse: 5 + 2 = 7
		if ( ! ( ($x > $rx - 7) && ($y > $ry - 7) && ($x < $rx + 7) && ($y < $ry + 7) ) ) {
			// clicked position was incorrect...
			// but user's data will be displayed with new oneclick			
			$msg = "Are you human? I don't think so?";		
		} else {
			check_spam();			
			// everything was correct...
			// insert member info to db...
			$msg = "Your info was recorded...";	
			// entered data clearing			
			$firstn = "";
			$lastn = "";
		}
	}
} else if ( $firstn <= " " && $lastn <= " "  )  {
	// no data was entered but submit button was clicked. There is nothing to do... Continue...
} else if ( $firstn <= " " || $lastn <= " " )  {
	// all necessary data wasn't entered.
	$msg = "Please enter all data.";
}

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset='UTF-8'>	
		<title>nyasoftware - 1 Click</title>
		<style>
			form {
				width: 220px;
				border: 1px solid #000;
				padding: 20px;
			}
			
			input[type="image"] {
				background: #e5e5e5;
				outline: none;
			}
			
			.msg {
				color: red;
				font-weight: bold;
			}
			
			.info {
				background: #e5e5e5;
				font-size: 14px;
				color: blue;
			}
			
			.button {
				text-decoration: none;
			}
			
			.buttons {
				text-align: left;
			}
		</style>
	</head>
	<body>
		<div class="container">
			<form name="myform" id="myform" action="test.php" method="POST">
				<h2>1 Click</h2>
				<h3>Test (without js)</h3>
				<p class="msg"><?php echo $msg ?></p>
				<p>First Name: <input type="text" name="firstn" id="firstn" value=<?php echo $firstn ?> ></p>
				<p>Last Name: <input type="text" name="lastn" id="lastn" value=<?php echo $lastn ?> ></p>
				<p class="info">After entering all data, please, click the center of circles.</p>
				<p><input type="image" src="oneclick.php" alt="Submit" name="oneclickimg" id="oneclickimg"></p>			
				<p class="buttons">
					<input type="reset" name="btn_reset" id="btn_reset" value="Reset">&nbsp;&nbsp;
					<a href="#noref" class="button"><input type="button" value="Cancel"></a>&nbsp;&nbsp;
					<input type="submit" name="btn_submit" id="btn_submit" value="Submit">
				</p>				
			</form>
		</div>
	</body>
</html>
