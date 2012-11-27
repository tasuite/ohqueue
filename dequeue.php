<?php
  $user_comp_id = $_SERVER['PHP_AUTH_USER'];
  //$user_comp_id = "hwc2d";

  require_once('dbconnect.php');

  $db = DbUtil::loginConnection();
  $stmt = $db -> stmt_init();

  if($stmt -> prepare('DELETE FROM active_queue WHERE comp_id = ?') or die (mysqli_error($db))) {
	$stmt -> bind_param("s", $user_comp_id);
	$stmt -> execute();
	$db -> commit();
  }

  $stmt -> close();
  $db -> close();
?>