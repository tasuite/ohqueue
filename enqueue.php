<?php
	$user_comp_id = $_SERVER['PHP_AUTH_USER'];
	//$user_comp_id =  "mst3k";
	$loc = $_GET['loc'];

	require_once("dbconnect.php");
	$db = DbUtil::loginConnection();
	$stmt = $db -> stmt_init();
	
	
	if($stmt -> prepare("INSERT INTO active_queue (`comp_id`, `location`) VALUES (?, ?)") or die(mysqli_error($db))) {
		$stmt -> bind_param("ss", $user_comp_id, $loc);
		$stmt -> execute();
	}
	
	if($stmt -> prepare("SELECT COUNT(*) FROM active_queue") or die(mysqli_error($db))) {
		$stmt -> execute();
		$stmt -> bind_result($position);
		$stmt -> fetch();
	}
	
	echo "<p>Your spot in the queue: <strong> ".$position."</strong></p>";
	echo "<p>You are at location: <strong>".$loc."</strong></p>";


?>