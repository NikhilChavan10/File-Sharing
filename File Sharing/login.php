<?php
    
    $host="nikhilchavancom.fatcowmysql.com"; // Host name
    $username="nikhilsc10"; // Mysql username
    $password="nikhilchavan"; // Mysql password
    $db_name="nikhil_10"; // Database name
    $tbl_name="signuptable"; // Table name
    
    // Connect to server and select databse.
    mysql_connect("$host", "$username", "$password")or die("cannot connect");
    mysql_select_db("$db_name")or die("cannot select DB");
    
    // username and password sent from form
//    $myusername="A10001";//$_POST['myusername'];
//    $mypassword="fitness123";//$_POST['mypassword'];
    $myusername=$_POST['myusername'];
    $mypassword=$_POST['mypassword'];
	
    // To protect MySQL injection (more detail about MySQL injection)
    $myusername = stripslashes($myusername);
    $mypassword = stripslashes($mypassword);
    $myusername = mysql_real_escape_string($myusername);
    $mypassword = mysql_real_escape_string($mypassword);
    $sql="SELECT * FROM $tbl_name WHERE username='$myusername' and password='$mypassword'";
    $result=mysql_query($sql);
    
    // Mysql_num_row is counting table row
    $count= mysql_num_rows($result);
    // If result matched $myusername and $mypassword, table row must be 1 row
    if($count==1){
        // Register $myusername, $mypassword and redirect to file "login_success.php"
		        
        session_start();
        $_SESSION['login_user']=$myusername; // Initializing Session	
		//header("location:homepage/index.html");		
		header("location: http://nikhil-chavan.com/Project207/homepage.html");
    }
    else {
header("location: http://nikhil-chavan.com/Project207/login.html");
alert("Incorrect");
    }
	
    ?>