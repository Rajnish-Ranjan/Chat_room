

 <script type="text/javascript" src="jquery-1.10.2.min.js"></script>
 <style type="text/css">
 	.button{
    background: blue; 
    display: table;
    color: #fff;
    padding:0.6%;
     }



	input[type="file"] {
    	display: block;
	}
	.refresh{
		background: #aaff77;
		border-radius: 20%;
	}
 </style>
 <script type="text/javascript">
// To print the chat history
    function printDiv(divName) {
         var printContents = document.getElementById(divName).innerHTML;
         var originalContents = document.body.innerHTML;
         document.body.innerHTML = printContents;
         window.print();
         document.body.innerHTML = originalContents;
    }

</script>
<?php
	//creating new session
		
	session_start();
		$ms="";
		$msg="";
if(isset($_POST['user-name'])){
		$user=$_POST['user-name'];
		$use="";
		// Checking username from Stored username in two of the text files to make sure only two users can 
		// chat at one time

		// $user1 will contain the name of previously live first user
		// $user2 will contain the name of previously live second user
		$use1 = fopen("user1.txt", "r") or die("Unable to fetch users data");
		// $user1 is written inside file user1.txt
		$user1=fgets($use1);
		$use2 = fopen("user2.txt", "r") or die("Unable to fetch users data");
		// $user2 is written inside file user2.txt
		$user2=fgets($use2);
		// Entered user-name should not be empty
		
		// We have to show a warning message if the user's name matches with previously live user for security purpose
		// Still the will be permitted
		if($user1==$user){
			echo "warning: <b>Already logged in</b>";
			$_SESSION['username']="1";
		}
		else if($user2==$user){
			$_SESSION['username']="2";
		}
		// if there will be no live 1st user previously it would be set to null
		else if($user1=="null"){
			// So the new user will be the 1st user
			// Thus file user1.txt will be updated with new user
			// session username value will be set to 2 to recognise the session as of 2nd user
			$_SESSION['username']="1";
			$ms="1";
			fclose($use1);
			fclose($use2);
			$myfile = fopen("user1.txt", "w") or die("Unable to fetch users data");
			fwrite($myfile, $user);
			fclose($myfile);
		}
		// if there will be no live 1st user previously it would be set to null
		else if($user2=="null"){
			// So the new user will be the 2st user
			// Thus file user2.txt will be updated with new user
			// session username value will be set to 2 to recognise the session as of 2nd user
			$_SESSION['username']="2";
			fclose($use1);
			fclose($use2);
			$myfile = fopen("user2.txt", "w") or die("Unable to fetch users data");
			fwrite($myfile, $user);
			fclose($myfile);
		}
		else{
			// Third user access will be denied and the control will be transferred to login page
			header('Location: index.php');
		}
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Chat Room</title>
</head>
<body style="margin-left: 10%;margin-right: 10%;background-image: url('bg.jpg');background-repeat: no-repeat;background-size: 100% 100%;">
	<div style="background-color: #ffff44;">
<?php

// The function is created to publish the User's name in the chat room
function User(){
	$ms=$_SESSION['username'];
	// If the user is the first user
    if($ms=="1"){
    	$myfile1 = fopen("user1.txt", "r") or die("Unable to fetch users data");
    	$myfile2 = fopen("user2.txt", "r") or die("Unable to fetch users data");
    }
    else if($ms=="2"){
    	$myfile1 = fopen("user2.txt", "r") or die("Unable to fetch users data");
    	$myfile2 = fopen("user1.txt", "r") or die("Unable to fetch users data");
    }
    $ms=fgets($myfile1);
	// Print 1st user name in the html page
	echo "<b><u>From:<span style='font-size:200%;padding-right:8%;font-variant: small-caps;'> ".$ms."</span></u></b>";
	$ms=fgets($myfile2);
	// Print 2nd user name in the html page
	echo "<b><u>To:<span style='font-size:200%;color:#888888;font-variant: small-caps;'> ".$ms."</span></u></b>";
	$ms="";
	fclose($myfile1);
	fclose($myfile2);
}

User();

// function made to logout
function logout(){
	$ms=$_SESSION['username'];
    if($ms=="1"){
    	$myfile = fopen("user1.txt", "w") or die("Unable to fetch users data");
    	//If user 1 is logging out 1st live user will be updated to null
		fwrite($myfile, "null");
		fclose($myfile);
    }
    if($ms=="2"){
    	$myfile = fopen("user2.txt", "w") or die("Unable to fetch users data");
    	//If user 2 is logging out 2nd live user will be updated to null
		fwrite($myfile, "null");
		fclose($myfile);
    }
    // The turn of the user which can send message is stored in a file called userno.txt
    // If any of the user logout the turn will be neutral (anyone can start chat) and set to zero
    $file = fopen("userno.txt", "w") or die("Unable to fetch users data");
	fwrite($file, "0");
	fclose($file);
	// session will be destroyed
    session_destroy();
    header('Location: index.php');
    return;
}
// If the logout button is tapped the function logout() will run
if (isset($_POST['button'])) {
	logout();
}
// below is the html code inside php echo function to publish logout button

echo '
<form name="form1" method="post" action="" style="text-align:right;padding-right:5%;">
    <label id="refresh">
      <input type="submit" style="background-color:ffffdd;color:#0000bb;border-radius:40%;" name="button" value=" logout ">
    </label>
    <br><br>
</form>';

?>
</div>
<?php
echo '
<form method="post" action="" id="form">
	<button type="submit" name="order">Refresh chat
	
	</button>
	
</form>

';
?>
<!--
	In new div
	Here is a Box with vertical scroll where chat messages info is displayed
	-->
<div style="height: 25em;background-color: #ffffff;border-radius: 3%;overflow:scroll;overflow-x:hidden;overflow-y:scroll;" id="output"'>
<?php 



function Sender(){
	$file = fopen("userno.txt", "w") or die("Unable to fetch users data");
	// The turn is updated with the current user to make sure that the current user will not post message just next time
	fwrite($file, $_SESSION['username']);
	fclose($file);

	// getting the name of the sender either from user1.txt or from user2.txt a/c to session variable
	if($_SESSION['username']=="1"){
		$file1 = fopen("user1.txt", "r") or die("Unable to fetch users data");	
		$sender=fgets($file1);
		fclose($file1);
	}
	else{
		$file1 = fopen("user2.txt", "r") or die("Unable to fetch users data");
		$sender=fgets($file1);
		fclose($file1);
	}
	return $sender;
}


function getTime(){
	date_default_timezone_set("Asia/Kolkata");
	$date = date('Y-m-d H:i:s');
	return $date;
}

function getMessage($chat){
	if (isset($chat)) {
		$message=$chat;
		// Appending chat message in $data variable
		return $message;
	}
	return "";
}

// Function to get the html code either to post image or to post the error if any constraint is not satisfied
function getImage($fileActualExt, $allowed,$fileError,$fileSize,$fileTmp , $target){
	// To check if the file extension is allowed or not
	// by matching it with elements of $allowed array
	if (in_array($fileActualExt, $allowed)) {
		// Checking If there is any error in image
		if($fileError===0){
			// Checking the size of image whether it's less than 204800 Bytes/200 KB
			if($fileSize <=204800){
				// Checking if there is no error in copying/uploading the image file to the folder images

				if (move_uploaded_file($fileTmp , $target)) {
					// The image-name with path is added in 'src' attribute of 'img' tag to be displayed in html
					// and appended in $data var

     				return "<br><img style='margin-left:2%' src='".$target."' height='100' width='100'><br>";
    			}else{
    				//Error message will be added in $data if there is an error
    				return "<i>Failed to upload image</i><br>";
    			}
			}
			else{
				//Error message will be added in $data if size is greater than 200 KB
				return "<i>image size shouldn't exceed 200 KB</i><br>";
			}
		}
		else{
			//Error message will be added in $data if there is error in uploading
			return "<i>There was an error uploading image</i><br>";
		}
	}
	else{
		if($fileActualExt!=""){
			//If Extension is not empty and doesn't match with array then file is not an image
			$fileActualExt="";
			return "<i>The file is not an image</i><br>";
		}	
	}	

}



// varibles to be used later
$sess=$_SESSION['username'];
$message="";
$sender="";
$data="";
$turn ="";
$bool=0;
$msg="";
// checking whether any new message is posted by any user

if (isset($_POST['upload'])) {


$file = fopen("userno.txt", "r") or die("Unable to fetch users data");
// turn variable contains the user's no. whose turn has ended
// It's stored in the file userno.txt
$turn=fgets($file);
fclose($file);
if (!($turn==$_SESSION['username'])||$turn=="0"){
	$temp="";
	// A new message is going to be stored in $data variable
	$data="<div style='background-color:#ddffdd;margin-bottom:0.1em;padding-left: 3%;margin-right:5%;border-radius:0.4em;border-style: dotted;border-width:0.05em' ><b>".Sender();
	$data=$data." <span style='color:#888888;'> ".getTime()." </span></b><br>";
	$chat = $_POST['chat'];
	$data= $data.getMessage($chat)."<br>";
	unset($_POST['chat']);
	if (isset($_FILES['image'])) {
			
    	$image = $_FILES['image']['name'];
    	// image file directory
    	// Basename function gives the name of image/file removing path directories
    	// The image has to be copied to images folder
    	// Image name with path images/file  is stored in $target variable
    	$target = "images/".basename($image);
		$file_name=$_FILES['image']['name'];
		$fileSize = $_FILES['image']['size'];
		$fileError = $_FILES['image']['error'];

		// Explode function divide any string by a character/string like '.'
		// Here it's used to get extension of the image
		$fileExt = explode('.', $file_name);
		// We convert all characters of extension to lowerCase
		$fileTmp = $_FILES['image']['tmp_name'];
		$fileActualExt= strtolower(end($fileExt));
		// Extension which are allowed to be uploaded is stored in $allowed array
		$allowed = array('jpg','jpeg','png','tiff','bmp');
		$temp = getImage($fileActualExt, $allowed,$fileError,$fileSize,$fileTmp , $target);
		$data = $data.$temp."</div><br>";

	}

	// All attributes which should be there for a single message are here in $data variable
	// Now it's added in file chatdat.txt

	$file2 = fopen("chatdata.txt", "a") or die("Unable to fetch users data");
	fwrite($file2, $data);
	$data="";
	fclose($file2);
}
else{
	$bool=1;
}
}
unset($_POST['upload']);
echo "<br>";
// Since all chat are stored in chatdata.txt 
// It's posted in the box after opening file in read mode
	$file = fopen("chatdata.txt", "r") or die("Unable to fetch users data");
	echo fgets($file);
	fclose($file);




	?>  

<script type="text/javascript">
	//This javascript function make sure that user sees the latest chat history
	var chatHistory = document.getElementById("output");
chatHistory.scrollTop = chatHistory.scrollHeight;
</script>

	<script type="text/javascript">
		
// Function to continously check the some has submitted any new chat
setInterval(function(){
	$.ajax({
			url:'chat.php',
			data:{ajaxget:true},
			method:'post',
			success:function(data){
				$('#output').html(data);
			}
	})
},1000);

	</script><br>
	</div><br>
	<?php
echo "<div style='margin-left:0%;' >";
//Remember we have set $bool=0 if The current user doesn't have it's turn
if($bool){
	echo "<span style='color:#ff0000;'>It's not your turn </span>";
}

// HTML form for chat inputs
echo '
<form method="post" action="" enctype="multipart/form-data" autocomplete="off">
	<input type="hidden" name="size" value="1000000"><table><tr><th>
	<input type="text" placeholder="ENTER MESSAGE HERE" style="line-height: 2;font-size: 20;" size="80" name="chat" autocomplete="off" autofocus/></th><th>
      
      <label class="button">Drop an Image
      <input type="file" name="image" id="file" autocomplete="off">
      </label></th></tr></table>
      <button type="submit" class="button" name="upload" autocomplete="off">POST</button>
</form>';
// If the button 'clear chat' is tapped the chat history will be deleted
if (isset($_POST['clear'])) {
	// Since chat history is stored in file chatdata.txt If chatdata.txt will be cleared chat will be cleared
	$file = fopen("chatdata.txt", "w") or die("Unable to fetch users data");
	fwrite($file, "");
	fclose($file);
}

echo '<table><tr><th>';
// TO clear chat history completely
echo '<form method="post" id="check">
		<button type="submit" name="clear">clear chat</button>
		</form>
';


 ?>

</td><td>
	<!--
		Here is the button to print the chat history in pdf

		It call javascript function printDiv() to print the box

		The function printDiv in defined in top of the page
	-->
<form method="post" action="print.php" id="check" target="_blank">
		<input type="button" onclick="printDiv('output')" value="Print Chat" />
		</form>
		
</td></tr></table>
</div>

</body>
</html>
