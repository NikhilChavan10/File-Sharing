<!DOCTYPE html>
<html>
<head>
	<style>
	h2 {color:violet;}

input[type=submit] {padding:5px 5px; background:#ccc; border:0 none;
cursor:pointer;
-webkit-border-radius: 5px;
border-radius: 5px; }
form div {
  padding: 5px; /*default div padding in the form e.g.  0 5px 0*/
  margin: 5px; /*default div padding in the form e.g. 5px 0  0*/
}
label {color: #aaa; font-weight: bold;}
	</style>
</head>
<body background="bgfit.jpg" background-position: 50% 50%; background-repeat:no-repeat;>
	<div>
<form action="homepage.html" method="post" enctype="multipart/form-data">
    <input type="submit" value="Home" name="submit">
</form> 
</div>
<?php
$valid_formats = array("jpg", "png", "gif", "zip", "bmp");
$max_file_size = 1024*1000; //100 kb
$path = "uploads/"; // Upload directory
$count = 0;

if(isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST"){
	// Loop $_FILES to exeicute all files
	foreach ($_FILES['files']['name'] as $f => $name) {     
	    if ($_FILES['files']['error'][$f] == 4) {
	        continue; // Skip file if any error found
	    }	       
	    if ($_FILES['files']['error'][$f] == 0) {	           
	        if ($_FILES['files']['size'][$f] > $max_file_size) {
	            $message[] = "$name is too large!.";
	            continue; // Skip large files
	        }
			elseif( ! in_array(pathinfo($name, PATHINFO_EXTENSION), $valid_formats) ){
				$message[] = "$name is not a valid format";
				continue; // Skip invalid file formats
			}
	        else{ // No error found! Move uploaded files 
	            if(move_uploaded_file($_FILES["files"]["tmp_name"][$f], $path.$name))
		{
	            $count++; // Number of successfully uploaded file			
		}
	        }
	    }
	}
}
if ($count == 0)
{
	echo '"<span style="color:#AFA;text-align:center;">Error! File Not Uploaded</span>';
}
else
{
	echo '"<span style="color:#AFA;text-align:center;">File Uploaded</span>';
}
?>
<div>
 <form action="view.html" method="post" enctype="multipart/form-data">
  <fieldset>
    <legend><h2>View Local Server Files</h2></legend>
    <input type="submit" value="View Files" name="submit">
  </fieldset>
</form>
</div>
</body>
</html>