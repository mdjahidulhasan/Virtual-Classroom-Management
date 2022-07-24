 <?php
	class Post
	{
		private $user;
		private $user_obj;
		private $con;
		private $code;
		public $fileDestination;

		public function __construct($con, $user, $code)
		{
			$this->con = $con;
			$this->user = $user;
			$this->code = $code;
			$this->user_obj = new User($con, $code, $user);
		}

		public function submitPost($body, $fileName, $fileDestination,$user_to)
		{
			$body = strip_tags($body); //removes html tags 
			$body = mysqli_real_escape_string($this->con, $body);
			$check_empty = preg_replace('/\s+/', '', $body); //Deletes all spaces 

			if ($check_empty != "" && $fileName == "") {
				//Get username
				$added_by = $this->user_obj->getUsername();
				//If user is on own class room, user_to is 'none'
				if($added_by == $user_to) {
					$user_to = 'none';
				}
				//insert post 
				$query = mysqli_query($this->con, "INSERT INTO posts VALUES('', '$check_empty', '$added_by','$this->code', '$user_to', '$date_added', 'no', 'no','','')");
				$returned_id = mysqli_insert_id($this->con);
				}
			if ($fileName != ""){  //only assignment
				//Get username
				$added_by = $this->user_obj->getUsername();
				//If user is on own class room, user_to is 'none'
				if($added_by == $user_to) {
					$user_to = 'none';
				}
				//Get course Code
				$course_code = $this->user_obj->getCourseCode();
				//insert post
				$query = mysqli_query($this->con, "INSERT  INTO posts VALUES('', '$body', '$added_by','$this->code', '$user_to', '$date_added', 'no', 'no','$fileName','$fileDestination')");
				$returned_id = mysqli_insert_id($this->con);
			}
		}

		public function submitEditPost($edited_body, $id)
		{
			//edited post
			$edited_body = strip_tags($edited_body); //removes html tags
			$edited_body = mysqli_real_escape_string($this->con, $edited_body);
			$check_empty = preg_replace('/\s+/', '', $edited_body); //Deletes all spaces

			if ($check_empty != "") {
				$query = mysqli_query($this->con, "UPDATE posts SET body = '$edited_body ', edited ='Yes' WHERE id='$id'");
			}
		}

		public function loadPosts()
		{
			$userLoggedIn = $this->user_obj->getUsername();
			$str = ""; //String to return 
			$data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE courseCode='$this->code' AND files ='none'   ORDER BY id DESC");
			if (mysqli_num_rows($data_query) > 0) {
				while ($row = mysqli_fetch_array($data_query)) {
					$id = $row['id'];
					$body = $row['body'];
					$added_by = $row['added_by'];
					$date_time = $row['date_added'];
					$edited = $row['edited'];
					//Prepare user_to string so it can be included even if not posted to a user
					if ($row['user_to'] == "none") {
						$user_to = "";
					} else {
						$user_to_obj = new User($this->con,$this->code ,$row['user_to']);
						$user_to_name = $user_to_obj->getFirstAndLastName();
						$user_to = "to <a href='" . $row['user_to'] . "'>" . $user_to_name . "</a>";
					}
					//Check if user who posted, has their account closed
					$added_by_obj = new User($this->con,$this->code ,$added_by);
					if ($added_by_obj->isClosed()) {
						continue;
					}
					$editPost_button = "";
					if ($userLoggedIn == $added_by) {
						$deletePost_button = "<a class='delete_button' href='form_handlers_delete_post.php?post_id=$id&amp;classCode=$this->code'><input id='delete_post_btn' type='button' value='Delete'></a>";
						$editPost_button = "<a href='classes_room_tchr.php?post_id=$id&amp;classCode=$this->code'><input  class='edit_post_btn' type='button' name='edit' value='Edit'></a>";
					} else {
						$deletePost_button = "";
					}
					$user_logged_obj = new User($this->con, $this->code, $userLoggedIn);
					if ($user_logged_obj->isStudent($added_by)) {
						$user_details_query = mysqli_query($this->con, "SELECT first_name, last_name, profilePic FROM users WHERE username='$added_by'");
						$user_row = mysqli_fetch_array($user_details_query);
						$first_name = $user_row['first_name'];
						$last_name = $user_row['last_name'];
						$profilePic = $user_row['profilePic'];
?>

<script>
    function toggle<?php echo $id; ?>() {
        var element = document.getElementById("toggleComment<?php echo $id; ?>");
        if (element.style.display == "block")
            element.style.display = "none";
        else
            element.style.display = "block";
    }
</script>

 <?php
	$comments_check = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id='$id'");
	$comments_check_num = mysqli_num_rows($comments_check);
	$checkEdit = "";
	if ($edited == "Yes") {
		$checkEdit = "Edited";
	}
	$str .= "<div class='status_post' >
				<div class='posted_by' style='color:#ACACAC;'>  
					<b> $first_name $last_name </b>
					$deletePost_button $editPost_button
					</div>
					<div id='post_body'>
						<p>$body</p>
					</div>
					<div class='commentOption' onClick='javascript:toggle$id()'> 
						Comments($comments_check_num)<span class='edited-det'>$checkEdit  </span> 
					</div>
				</div>
				<div class='post_comment' id='toggleComment$id' style='display:none;'>
					<iframe src='comment_frame.php?post_id=$id' id='comment_iframe' frameborder='0' ></iframe>
			</div>
			<hr>";
}
?>

<script>
    $(".delete_button").click(function() {
        return confirm("Are you sure you want to delete this post?"); // this js is not working
    });
</script>

<?php
		} //End of the while loop
		echo $str;
	}
}
public function loadFiles()
{
	$userLoggedIn = $this->user_obj->getUsername();
	$str = ""; //String to return
	$data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE courseCode='$this->code' AND files !='none'   ORDER BY id DESC");
	if (mysqli_num_rows($data_query) > 0) {

		while ($row = mysqli_fetch_array($data_query)) {
			$id = $row['id'];
			$body = $row['body'];
			$added_by = $row['added_by'];
			$date_time = $row['date_added'];
			$file = $row['files'];
			$path = $row['fileDestination'];

			$user_to_obj = new User($this->con,$this->code,  $row['user_to']);
			$user_to_name = $user_to_obj->getFirstAndLastName();
			$user_to = "to <a href='" . $row['user_to'] . "'>" . $user_to_name . "</a>";
			//Check if user who posted, has their account closed
			$added_by_obj = new User($this->con,$this->code, $added_by);
			if ($added_by_obj->isClosed()) {
				continue;
			}
			//edit and delete option
			if ($userLoggedIn == $added_by) {
				$deletePost_button = "<a class='delete_button' href='form_handlers_delete_post.php?postid=$id&amp;classCode=$this->code'><input id='delete_post_btn' type='button' value='Delete'></a>";

				$editPost_button = "<a href='classes_room_tchr.php?post_id=$id&amp;classCode=$this->code'><input  class='edit_post_btn' type='button'  value='Edit'></a>";
				
				$deleteComment_button ="<a class='delete_button' href='form_handlers_delete_post.php?post_id=$id&amp;classCode=$this->code'><input type='button' value='X'></a>";
			} else {
				$deletePost_button = "";
				$editPost_button = "";
			}
			$user_logged_obj = new User($this->con, $this->code, $userLoggedIn);
			if ($user_logged_obj->isStudent($added_by)) {

				$user_details_query = mysqli_query($this->con, "SELECT first_name, last_name, profilePic FROM users WHERE username='$added_by'");
				$user_row = mysqli_fetch_array($user_details_query);
				$first_name = $user_row['first_name'];
				$last_name = $user_row['last_name'];
                $profilePic = $user_row['profilePic'];
				?>

<script>
    function toggle<?php echo $id; ?>() {
        var element = document.getElementById("toggleComment<?php echo $id; ?>");

        if (element.style.display == "block")
            element.style.display = "none";
        else
            element.style.display = "block";
    }
</script>

<?php
	$comments_check = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id='$id'");
	$comments_check_num = mysqli_num_rows($comments_check);

	$fileExt = explode('.', $file);
	$fileActualExt = end($fileExt);
	$allowed  = array('jpg', 'jpeg', 'png');
	$fileDiv = "";

	if (in_array($fileActualExt, $allowed)) {
		$fileDiv = "<div id='postedFile'>
			<img src='$path' onclick='window.open(this.src)' title='Click Here To View Full Screen'>
				</div>";
	}

	$res = str_replace("uploads/", "", $path);
	$downloadFile = "<div id='downloadFile'>
							<a href ='form_handlers_download_files.php?download=$path' title='Click Here To Download'>$res</a>
						</div>";

	$str .= "<div class='status_post' >
				<div class='posted_by' style='color:#ACACAC;'>
					<b> $first_name $last_name </b>
					$deletePost_button 
				</div>
				<div id='post_body'>
					<div class='tooltip'></div>
						<p>$body</p>
						$fileDiv
						$downloadFile
					</div>
				<div class='commentOption' onClick='javascript:toggle$id()'>
					Comments($comments_check_num)&nbsp;&nbsp;&nbsp;
				</div>
			</div>
			<div class='post_comment' id='toggleComment$id' style='display:none;'>
				<iframe src='comment_frame.php?post_id=$id' id='comment_iframe' frameborder='0' ></iframe>
			</div>
			<hr>";
			}
		} //End of the while loop
	echo $str;
	}
}
public function checkTeachingClass(){
	$checkTeaching = false;
	$data_query = mysqli_query($this->con, "SELECT * FROM createclass where username='$this->user' ORDER BY id DESC");
	if (mysqli_num_rows($data_query) > 0) {
	   $checkTeaching = true;
	}
	return $checkTeaching; 
}
public function loadTeachingClasses()
{
	$this->checkTeaching = true;
	$str = ""; //String to return 
	$data_query = mysqli_query($this->con, "SELECT * FROM createclass where username='$this->user' ORDER BY id DESC");
	$userLoggedIn = $this->user_obj->getUsername();

	if (mysqli_num_rows($data_query) > 0) {
		while ($row = mysqli_fetch_array($data_query)) {
			$id = $row['id'];
			$className = $row['className'];
			$section = $row['section'];
			$subject = $row['subject'];
			$code = $row['courseCode'];
			$added_by = $row['username'];

			if ($userLoggedIn == $added_by) {
				$delete_teachingClass = "<a href='form_handlers_delete_post.php?createClass_id=$id&courseCode=$code'><input type='button' id='delete_class_btn' value='Remove'></a>";
			} else {
				$delete_teachingClass = "";
			}

			$str .= "<div class='classBox'>				
						<a href = 'classes_room_tchr.php?classCode=$code'> <h3>$className </h3></a> 
						Section: $section
						<br>
						$subject
						<br>
						<p> $delete_teachingClass </p>
				    </div> ";
		}
		echo $str;
	}
}
public function checkEnrolledClass(){
	$checkEnrolled = false;
	$data_query = mysqli_query($this->con, "SELECT * FROM createclass where student_array LIKE'%$this->user%' ORDER BY id DESC");
	if (mysqli_num_rows($data_query) > 0) {
		$checkEnrolled = true;
	}
	return $checkEnrolled; 
}
public function loadEnrolledClasses()
{
	$str = ""; //String to return 
	$data_query = mysqli_query($this->con, "SELECT * FROM createclass where student_array LIKE'%$this->user%' ORDER BY id DESC");
	if (mysqli_num_rows($data_query) > 0) {
		while ($row = mysqli_fetch_array($data_query)) {
			$className = $row['className'];
			$section = $row['section'];
			$subject = $row['subject'];
			$code = $row['courseCode'];

			$delete_EnrolledClass = "<a href='form_handlers_delete_post.php?Enrolled_Student=$this->user&amp;classCode=$code'><input type='button' id='delete_class_btn' value='Leave'></a>";
			$str .= "<div class='EnrolledclassBox'>
					   <a href = 'classes_room_stu.php?classCode=$code'> <h3>$className </h3></a>
					   Section: $section
					   <br>
					   $subject
					   <br>
					   <p> $delete_EnrolledClass </p>
					   </a>
				</div> ";
		}
		echo $str;
	}
}
}
?> 