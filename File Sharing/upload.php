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
    </style>
</head>
<body background="bgfit.jpg" background-position: 50% 50%; background-repeat:no-repeat;>
    <form action="homepage.html" method="post" enctype="multipart/form-data">
    <input type="submit" value="Home" name="submit">
</form> 
<?php

if($_SERVER['REQUEST_METHOD'] === 'POST')
{
	header('Content-Type: application/json');
    if($_FILES["upload"]["error"] > 0)
    {
    	$response = array(
    		"code" => -1,
    		"message" => $_FILES["upload"]["error"]
    	);
    	echo '"<span style="color:#AFA;text-align:center;">Error! File Not Uploaded</span>';
    	exit(0);
    }
    else
    {
    	$CURRENT_URL = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    	$last_slash = strrpos($CURRENT_URL, "/");
		if($last_slash > 0)
		{
			$SERVER_BASE_URL = substr($CURRENT_URL, 0, $last_slash) . "/";
		}
    	$UPLOAD_DIRECTORY = "curlUploads/";
    	if(!file_exists($UPLOAD_DIRECTORY))
    	{
    		mkdir($UPLOAD_DIRECTORY, 0777, true);
    	}
    	
    	$SERVER_UPLOAD_ENDPOINT = "upload.php";
        $SERVER_LIST_ENDPOINT = "list.php";
        
    	$from_server = ($_POST["source"] == "server");
    	$modtime = $_POST["modtime"];
        $filename = $_FILES["upload"]["name"];
        $filesize = $_FILES["upload"]["size"];
        $tmpfile = $_FILES["upload"]["tmp_name"];
        $filemd5 = md5_file($tmpfile);
        $timestamp = filemtime($tmpfile);
        if($modtime != "")
        {
        	// we got modification time from the client
        	$timestamp = $modtime;
        	touch($tmpfile, $timestamp);
        }
        
        // determine if we need to save this file
        $save_file = false;
        $new_file_path = $UPLOAD_DIRECTORY . $filename;
        if(!$from_server || !file_exists($new_file_path))
        {
        	// new file
        	$save_file = true;
        }
        
        // some files that we won't accept
        if($filename == "_peers.json")
        {
        	$save_file = false;
        	$response = array(
    			"code" => -1,
    			"message" => "Illegal file name: " . $filename
    		);
    		echo '"<span style="color:#AFA;text-align:center;">Error! File Not Uploaded! Illegal File Name</span>';
    		exit(0);
        }
        
        if($save_file)
        {
        	// save the file
        	move_uploaded_file($tmpfile, $new_file_path);
        	
        	/*
        	$pid = pcntl_fork();
        	if($pid == -1)
        	{
        		// error forking
        		
        	} else if(
        	*/
        	
        	// if this was from a user, we need to alert other servers
        	if(!$from_server)
        	{
        		// iterate through _peers.list and upload the file to each server
        		$peers_string = file_get_contents("_peers.json");
        		$peers = json_decode($peers_string, true);
        		foreach($peers as $peer)
        		{
        			$url = $peer["upload"];
        			$data['upload'] = "@".$new_file_path;
        			$data['source'] = "server";
        			$data['modtime'] = $timestamp;
        			$data['submit'] = "submit";
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
					curl_setopt($ch, CURLOPT_POST, true);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $data); 
					$response = curl_exec($ch);
        		}
        	}
        }
        
        // show success message
        $response = array(
        	"code" => 0,
        	"file" => array(
        		"filename" => $filename,
        		"url" => $SERVER_BASE_URL . $new_file_path,
        		"timestamp" => $timestamp,
        		"md5sum" => $filemd5,
        		"size" => $filesize
        	)
        );
        $jsonstring = json_encode($response);
		$jsonstring = str_replace("\\/", "/", $jsonstring); // undo backslash escape
        echo '"<span style="color:#AFA;text-align:center;">File Uploaded</span>';
    }
}
else
{
?>
<div>
    <form action="viewAll.html" method="post" enctype="multipart/form-data">
      <fieldset>
    <legend><h2>View Files Uploaded From All Server</h2></legend>
    <input type="submit" value="View Broadcasted Files" name="submit">
       </fieldset>
</form>  
</div>

<?php
}

// launch the sync.php script
exec("php -f sync.php >> /dev/null &");

?>

</body>
</html>
