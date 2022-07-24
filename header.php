<?php 
	require 'config.php' ;
	require 'form_handlers_create_joinclass.php';
	include("classes_user.php");
	include("classes_post.php");

	if(isset($_SESSION['username'])){
		$userLoggedIn  = $_SESSION['username'];
		$userLoggedIn2  = $_SESSION['username'];
		$user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username = '$userLoggedIn'");
		$user_details_query2 = mysqli_query($con, "SELECT * FROM createclass WHERE username = '$userLoggedIn2' ORDER BY id DESC");
		$user = mysqli_fetch_array($user_details_query);
		$user2 = mysqli_fetch_array($user_details_query2);
		$user_role = $user['role'];
	}
	else{
		header("Location:register.php");
	}
 ?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>VC</title>

	<!-- javaScripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="js/createJoinClass.js"></script>
    <script src="js/jquery.jcrop.js"></script>
    <script src="js/jcrop_bits.js"></script>

	<!-- css -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="css\styling.css">
    <link rel="shortcut icon" type="image/png" href="images/graduation.png">
</head>

<body>
<div class="background">

</div>
<div class="top_bar">
	<div class="logo">
		<a href="index.php" style="text-decoration: none"><i class="fa fa-chalkboard"></i>Virtual Classroom</a>
	</div>
	<div class="icon">
		<nav>
			<a href="#">
				<?php echo $user['first_name']; ?> <?php echo $user['last_name']; ?>
			</a>
			<a href="index.php"><i class="fas fa-home"></i>
				<span class="tooltiptext">Home</span>
			</a>
			<?php 
          		if(strcmp($user_role,"Teacher") == 0){
            	echo 
					'<a href="create_class.php">
						<i class="fas fa-plus"></i>
						<span class="tooltiptext">Create Class</span>
					</a>';
          		}
          		else{
            	echo 
                	'<a href="join_class.php">
                		<i class="fas fa-plus"></i>
                		<span class="tooltiptext">Join Class</span>
                	</a>';
          		} 
       		?>
			<a href="logout.php">
				<i class="fas fa-power-off"></i>
				<span class="tooltiptext">Logout</span>
			</a>
		</nav>
	</div>
</div>
	