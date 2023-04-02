<?php 
	function testSmsGroup(){
			if(isset($_POST['save'])){
			$con= dbcon(); 
			$tempArray =array();
			$phoneNumbers=$con->query("SELECT tel FROM  users WHERE user_level='1' ") or die(mysqli_error($con));
			while($contact =$phoneNumbers->fetch_array()){
				array_push($tempArray,$contact['tel']);
			}
			$formatContactArray = Helpers::formatPhoneNumbers($tempArray);
			//send sms to formatted array of numbers
			$message=$_POST['message'];
			Helpers::sendSMS($message,$formatContactArray);
			echo "<script>alert('Your Form has successfully been Submitted . Thank you')</script>";
		}
	}