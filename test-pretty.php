<?php

// session must be started
if (session_id() <= "") {
	session_start();
}

// for form fields
$firstn = "";
$lastn = "";
$clickedPos = "";
$msg = "";

require_once "checkSpam.php";

// check form data (script step)
if (isset($_POST["firstn"])) {
	$firstn = $_POST["firstn"];
	$lastn = $_POST["lastn"];
	// image clicked position:
	$clickedPos = explode("x", $_POST["clickedPos"]);	
}

// if form data was entered
if ( $firstn > " " && $lastn > " " ) {
	// check if image was clicked	
	if ( $clickedPos <= " " ) {
		// user's data will be displayed with new oneclick		
		$msg = "Are you human? I don't think so?";		
	} else {
		// get image clicked position		
		$x = $clickedPos[0];
		$y = $clickedPos[1];
		// get session variables (center of circles)		
		$rx = $_SESSION["oneclick_x"];
		$ry = $_SESSION["oneclick_y"];
		// clear session variables		
		unset($_SESSION["oneclick_x"]);
		unset($_SESSION["oneclick_y"]);

		// check if clicked position was correct. Center rectangle's diagonals are 10px. We give 2px more for lapse: 10 / 2 + 2 = 7		
		if ( ! ( ($x > $rx - 7) && ($y > $ry - 7) && ($x < $rx + 7) && ($y < $ry + 7) ) ) {
			// user's data will be displayed with new oneclick			
			// clicked position was incorrect...			
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
		<title>nyasoftware - 1 Click Pretty</title>
		<style>
			form {
				width: 220px;
				border: 1px solid #000;
				padding: 20px;
			}
			
			.msg {
				color: red;
				font-weight: bold;
			}
			
			.info {
				margin-top: 20px;				
				background: #e5e5e5;
				font-size: 14px;
				color: blue;
			}
			
			.oneclickimg {
				width: 220px;
				height: 80px;
				margin-top: 20px;
				border: none;
				outline: none;				
			}

			.checkmark {
				position: relative;
				top: -1000px;
				left: -2000px;
				width : 30px;
				height : 24px;
				border: none;				
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
			<form name="myform" id="myform" action="test-pretty.php" method="POST">
				<div>
					<h2>1 Click Pretty</h2>
					<h3>Test (with js)</h3>
				</div>
				<div class="msg"><?php echo $msg ?></div>
				<div>First Name: <input type="text" name="firstn" id="firstn" value=<?php echo $firstn ?> ></div>
				<div>Last Name: <input type="text" name="lastn" id="lastn" value=<?php echo $lastn ?> ></div>
				<div class="info">After entering all data, please, click the center of circles and than click the Submit.</div>
				<div class="oneclick">
					<div class="oneclickimg"><img src="oneclick.php" id="oneclickimg" /></div>
					<div class="checkmark"><img src="images/check-white.png" /></div>
					<input type="hidden" name="clickedPos" id="clickedPos" value="">					
				</div>			
				<div class="buttons">
					<input type="reset" name="btn_reset" id="btn_reset" value="Reset">&nbsp;&nbsp;
					<a href="#noref" class="button"><input type="button" value="Cancel"></a>&nbsp;&nbsp;
					<input type="submit" name="btn_submit" id="btn_submit" value="Submit">
				</div>				
			</form>
		</div>
	</body>
	<script src="js/jquery-1.11.2.min.js" type="text/javascript"></script>
	<script type="text/javascript">
	$(function() {
		$('#oneclickimg').click(function(e) {
			// get the image clicked position
			var parentPosition = getPosition(e.currentTarget);
			var xPosition = e.clientX - parentPosition.x;
			var yPosition = e.clientY - parentPosition.y;
			// put clicked position into the form hidden field
			$('#clickedPos').val(xPosition+'x'+yPosition);
			// display check.png image at the clicked position of image
			var xpos = xPosition -6; // 6 is check mark bottom position at the check.png
			var ypos = Math.abs(yPosition - 100); // -100 is oneclickimg's height. If you add bottom margin to oneclickimg, you must add that value to this.
			$('.checkmark').css('top', '-'+ypos+'px');
			$('.checkmark').css('left', xpos+'px');
			return false; // prevent default action
		});
		 
		function getPosition(element) {
			var xPosition = 0;
			var yPosition = 0;
			  
			while (element) {
				xPosition += (element.offsetLeft - element.scrollLeft + element.clientLeft);
				yPosition += (element.offsetTop - element.scrollTop + element.clientTop);
				element = element.offsetParent;
			}
			return { x: xPosition, y: yPosition };
		}				

	});
	</script>	
</html>
