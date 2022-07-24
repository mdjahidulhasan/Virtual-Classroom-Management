<?php 
    include("header.php");
    require 'config.php' ;
    require 'form_handlers_create_joinclass.php';
?>

<script>
    $(document).ready(function(){
        $("#second").show();
    });
</script>

<div class="bg">
    <div class="wrapper">
        <div class="creatClass_box">  	 
            <div id="second">
                <div class="joinClass_header">
                    <h3>Join class</h3>
                </div>
                <form action="join_class.php" method="POST">
                    <input type="text" name="code" placeholder="Class code" autocomplete="off" value = "">
                    <br>
                    <button class="cancel_button" ><a href="index.php">Cancel</a></button>
                    <button  type="sumbit" name="joinClass_button" id="create_class_button">Join</button>
                    <!-- <br>
                    <br>
                    <a href="#" id="createClass" class="createClass">Want to create a new Class? Click here!</a> -->
                    </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>