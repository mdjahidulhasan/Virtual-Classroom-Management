<html>
<head>
    <title></title>
    <link rel="stylesheet" type="text/css" href="css\styling.css">
</head>

<body>
    <div class="comment_wrapper">
    <?php 
		require 'config.php';
		include("classes_user.php");
		include("classes_post.php");
		$userLoggedIn = "";
		if (isset($_SESSION['username'])) {
			$userLoggedIn = $_SESSION['username'];
			$user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$userLoggedIn'");
			$user = mysqli_fetch_array($user_details_query);
		}
		?>

        <script>
            function toggle() {
                var element = document.getElementById("comment_section");

                if (element.style.display == "block")
                    element.style.display = "none";
                else
                    element.style.display = "block";
            }
        </script>

        <?php
		if (isset($_GET['post_id'])) {
			$post_id = $_GET['post_id'];
		}

		$user_query = mysqli_query($con, "SELECT added_by, courseCode, user_to FROM posts WHERE id='$post_id'");
		$row = mysqli_fetch_array($user_query);
		$posted_to = $row['added_by'];
		$courseCode = $row['courseCode'];
		$user_to = $row['user_to'];

		if (isset($_POST['postComment' . $post_id])) {
			$post_body = $_POST['post_body'];
			$post_body = mysqli_escape_string($con, $post_body);
			$date_time_now = date("Y-m-d H:i:s");
			$insert_post = mysqli_query($con, "INSERT INTO comments VALUES ('', '$post_body','$courseCode', '$userLoggedIn', '$posted_to', '$date_time_now', 'no', '$post_id')");
			
			$get_commenters = mysqli_query($con, "SELECT * FROM comments WHERE post_id='$post_id'");
			$notified_users = array();
			
			while($row = mysqli_fetch_array($get_commenters)) {
				if($row['posted_by'] != $posted_to && $row['posted_by'] != $user_to 
					&& $row['posted_by'] != $userLoggedIn && !in_array($row['posted_by'], $notified_users)) {
					array_push($notified_users, $row['posted_by']);
				}
			}
			echo "<p style='text-align: center; margin: 0 0 0.5rem 0;'>Comment Posted! </p>";
		}
		?>

        <form action="comment_frame.php?post_id=<?php echo $post_id; ?>" id="comment_form" name="postComment<?php echo $post_id; ?>" method="POST" autocomplete="off">
            <input type="text" name="post_body" placeholder="Add a comment">
            <input type="submit" name="postComment<?php echo $post_id; ?>" value="Post">
        </form>

        <!-- Load comments -->
        <?php 
		$get_comments = mysqli_query($con, "SELECT * FROM comments WHERE post_id='$post_id' ORDER BY id DESC");
		$count = mysqli_num_rows($get_comments);

		if ($count != 0) {
			while ($comment = mysqli_fetch_array($get_comments)) {
				$id = $comment['id'];
				$courseCode = $comment['courseCode'];
				$comment_body = $comment['post_body'];
				$posted_to = $comment['posted_to'];
				$posted_by = $comment['posted_by'];
				$date_added = $comment['date_added'];
				$removed = $comment['removed'];
				$post_id = $comment['post_id'];
				$user_obj = new User($con,$courseCode, $posted_by);
				?>

				<div class="comment_section">
					<b> <?php echo $user_obj->getFirstAndLastName(); ?> </b>
					<?php echo "<br><p >$comment_body<p>"; ?>
				</div>
				<?php
			}
		}
		else {
			echo "<p style='text-align: center; margin-bottom:4rem;'>No Comments to Show!</p>";
		}
		?>
	</div>
</body>
</html> 