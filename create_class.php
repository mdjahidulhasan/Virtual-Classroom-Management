<?php 
    include("header.php");
    require 'config.php' ;
    require 'form_handlers_create_joinclass.php';
?>

<script>
    $(document).ready(function(){
        $("#first").show();
    });
</script>

<div class="bg">
    <div class="wrapper">
        <div class="creatClass_box">  		
            <div id="first">
                <div class="creatClass_header">
                    <h3>Create Class</h3>
                </div>
			    <form action="create_class.php" method="POST">
                    <input type="text" name="className" autocomplete="off" placeholder="Class Name/Course Code" value = "">
				   	<br>
				    <input type="text" name="section" autocomplete="off" placeholder="Section" value = "">
				    <br>
				    <input type="text" name="subject" autocomplete="off" placeholder="Subject/Course Title" value = "">
				    <br>
                    <button class="cancel_button"><a href="index.php">Cancel</a></button>
				     <button  type="sumbit" name="createClass_button" id ="create_class_button">Create</button>
	                <!-- <br>
                    <br>
                    <a href="#" id="joinClass" class="joinClass">Want to join a Class? Click Here</a> -->
		     	</form>
            </div>
        </div>          
    </div>
</div>
</body>
</html>