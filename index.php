<?php
	$user_id = $_SERVER['PHP_AUTH_USER']; //get netbadge 
	//$user_id = 'hwc2d';
	
	//set up database connection
	require_once("dbconnect.php");
	$db = DbUtil::loginConnection();
	$stmt = $db -> stmt_init();


	//get the user's name from user_id
	if($stmt -> prepare('SELECT fname, lname, role FROM roster WHERE comp_id = ?') or die(mysqli_error($db))) {
		$stmt -> bind_param("s", $user_id);
		$stmt -> execute();
		$stmt -> bind_result($user_fname, $user_lname, $user_role);
		$stmt -> fetch();
	}
	
	//get the user's location in the queue
	
	$position = 1;
	if($stmt -> prepare("SELECT comp_id, location FROM active_queue NATURAL JOIN roster ORDER BY enter_ts") or die(mysqli_error($db))) {
		$stmt -> execute();
		$stmt -> bind_result($comp_id, $location);
		while($stmt -> fetch()){
			if($comp_id === $user_id){
				break;
			}else{
				$position++;
			}
		}	
	}
	

?>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>Office Hours</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="Office Horus">
		<meta name="author" content="HunterC">
		
		<!-- Le styles -->
		
		<link href="css/bootstrap.css" rel="stylesheet">
		
		<link href="css/bootstrap-toggle-buttons.css" rel="stylesheet">

		<link href="css/bootstrap-responsive.css" rel="stylesheet">
		
		
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
		<script type="text/javascript" src="js/jquery.toggle.buttons.js"></script>
		<script src="js/jquery.form.js"></script>
		<script src="js/jquery.cookie.js"></script>
		<script type="text/javascript" src="js/bootstrap.min.js"></script>

		<script>
			$(document).ready(function() { 
				// bind 'myForm' and provide a simple callback function 
				$('#enqueue_form').ajaxForm(function(data) {
					$('#your_location').html(data);
					$('#enqueue_form').replaceWith($('#your_location'));
					$('#your_location').fadeIn('fast');
				}); 
			});
			
			function toggle(){
				if($.cookie('status') === null){
					$.cookie('status', 'on');
					document.getElementById('toggle_btn').value = "Turn OFF Queue";
				}else if($.cookie('status') === 'on'){
					$.cookie('status', 'off');
					document.getElementById('toggle_btn').value = "Turn ON Queue";
				}else if($.cookie('status') === 'off'){
					$.cookie('status', 'on');
					document.getElementById('toggle_btn').value = "Turn OFF Queue";
				}
				
				$("#student_table").load("index.php #student_table");
				
			}
		
		</script>
	</head>


	<body style="padding-top: 60px;">
		<div class="navbar navbar-inverse navbar-fixed-top">
			<div class="navbar-inner">
				<div class="container">
					<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
				  		<span class="icon-bar"></span>
				  		<span class="icon-bar"></span>
				  		<span class="icon-bar"></span>
					</a>
					<a style="color: white" class="brand">Office Hours Queue</a>
					<div class="nav-collapse collapse">
				  		<ul class="nav">
				  		</ul>
					</div><!--/.nav-collapse -->
			  	</div>
			</div>
		</div>
		<div class="container">  
		<?php if($user_role == 'instructor' || $user_role == 'ta') { ?> <!-- staff loop-->
			<p>Hello, <?php echo $user_fname; ?>!</p>
			You're a member of the Staff!
			
			<?php
				$cookie_status = "Turn ON Queue";
				if($_COOKIE['status'] === 'on'){
					$cookie_status = "Turn OFF Queue";
				}
			?>
			
			<form>
				<input type="button" class="btn btn-primary" id="toggle_btn" onclick="toggle()" value="<?php echo $cookie_status ?>"/>
			</form>
			
			<div id="student_table">
			<?php
					
						
					
				
					echo $_COOKIE['status'];
					//set up and display the contents of the queue
					$table = '<table class="table">
						<thead>
							<th>Name</th>
							<th>Comp ID</th>
							<th>Location</th>
						</thead>
						<tbody>';
					
					//get users' information
					if($stmt -> prepare("SELECT comp_id, fname, lname, location FROM active_queue NATURAL JOIN roster ORDER BY enter_ts") or die(mysqli_error($db))) {
						$stmt -> execute();
						$stmt -> bind_result($comp_id, $fname, $lname, $location);
						while($stmt -> fetch()){
							$table = $table.'<tr><td>'.$fname.' '.$lname.'</td><td>'.$comp_id.'</td><td>'.$location.'</td><button type="button" class="btn btn-danger">x</button></tr>';
						}
						$table = $table.'</tbody></table>';
					}
					
					echo $table;
				
			?>
			</div>
		
		<?php }else{ ?> <!-- staff else-->
			<p>Hello, <?php echo $user_fname; ?>!</p>
			<?php 
				if($_COOKIE['status'] === 'on'){
					if($comp_id != $user_id) { 
			?>
					
				<p>Join the Queue:</p>
				<form id="enqueue_form" action="enqueue.php" method="get"> 
					Location: <input type="text" name="loc" /> 
					<input type="submit" value="Join" /> 
				</form>
				
			
			<?php } else { ?>
				<p>Your computing id is <?php echo $user_id ?>.</p>
				<p>Your spot in the queue: <strong><?php echo $position ;?></strong></p>
				<p>You are at location: <strong><?php echo $location ?>.</strong></p>
			
				
			<?php } ?>
				<div id="your_location" style="display:none">
						
				</div>
			<?php }else{
				echo "Office Hours have ended. Please consult the calendar to see when they will resume.<br>";
				echo '<iframe src="https://www.google.com/calendar/embed?src=uvacs1110%40gmail.com&ctz=America/New_York" style="border: 0" width="800" height="600" frameborder="0" scrolling="no"></iframe>';
			}
			
	 } 
	 ?> <!-- staff end-->
		</div>
	</body>
</html>