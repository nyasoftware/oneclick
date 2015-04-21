<?php
function check_spam() {
	require_once("db_connect.php");
	
	$ip = "";
	if ( isset($_SERVER['HTTP_CLIENT_IP']) && ! empty($_SERVER['HTTP_CLIENT_IP'])) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif ( isset($_SERVER['HTTP_X_FORWARDED_FOR']) && ! empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = (isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
	}

	$ip = filter_var($ip, FILTER_VALIDATE_IP);
	$ip = ($ip === false) ? '0.0.0.0' : $ip;

	$start = date('Y-m-d H:i:s');
	$end = date('Y-m-d H:i:s');
	$newdate = strtotime ( '-1 minutes' , strtotime ( $end ) ) ;
	$end = date ( 'Y-m-d H:i:s' , $newdate );
	$sql = "SELECT * FROM oneclick_members WHERE MemberDateTime between '$start' and '$end'";
	$result = mysql_query($sql);
	$num_rows = mysql_num_rows($result);
	
	if ($num_rows < 5) {
		$sql = "SELECT * FROM oneclick_members WHERE MemberIP = '$ip'";
		$result = mysql_query($sql);
		$num_rows = mysql_num_rows($result);
	}
	
	if ($num_rows < 5) {
		$sql = "INSERT INTO oneclick_members(MemberIP) VALUES('$ip')";
		if (!mysql_query($sql)) {
			die($sql.'<br>Insert error: ' . mysql_error());
		}
	} else {
		$sql = "DELETE FROM oneclick_members WHERE MemberDateTime between '$start' and '$end'";
		$result = mysql_query($sql);
		$sql = "DELETE FROM oneclick_members WHERE MemberIP = '$ip'";
		$result = mysql_query($sql);		
		exit;
	}
}
?>