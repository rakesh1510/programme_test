<!DOCTYPE html>
<?php
session_start();
//if (isset($_SESSION['username'])) {
//    header('Location: Pro_Lesson.php');
//}
if (isset($_POST['password'])) {
    if (empty($_POST['pwd'])) {
        echo " password are empty";
    } if (empty($_POST['repwd'])) {
        echo " re type password are empty";
    } if ($_POST['pwd'] != $_POST['repwd']) {
        echo " password not match wiith Retype password are empty";
    } else {
        echo "<script type='text\javascript'>document.getElementById(myform).submit()<\script>";
        echo "<script>document.getElementById('myForm').action = regvalidate.php<\script>";
    }
}
?>
<html>
    <head>
        <link rel="stylesheet" href="insert.css">
        <title>PHP insertion</title>
        <link href="css/insert.css" rel="stylesheet">
    </head>
    <body>
        <div class="maindiv">
            <!--HTML Form -->
            <div class="form_div">
                <div class="title">
                    <h2>Insert Data In Database Using PHP.</h2>
                </div>
                <form action="" method="post" name="myform">
                    <!-- Method can be set as POST for hiding values in URL-->
                    <h2>Form</h2>
                    <label>Name:</label>
                    <input class="input" name="name" type="text" value="">
                    <label>Password:</label>
                    <input class="input" name="pwd" type="text" value="">
                    <label>Re-type Password:</label>
                    <input class="input" name="repwd" type="text" value="">
                    <label>Email:</label>
                    <input class="input" name="email" type="text" value="">
                    <label>Contact:</label>
                    <input class="input" name="contact" type="text" value="">
                    <label>Address:</label>
                    <textarea cols="25" name="address" rows="5"></textarea><br>
                    <input class="submit" name="submit" type="submit" value="Insert">
                </form>
            </div>
        </div>
    </body>
</html>
