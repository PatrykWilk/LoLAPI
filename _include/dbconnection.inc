<?php
	//  replace mysqllogin and mysqlpassword with your own
	$conn = mysqli_connect("localhost", "21607065", "password", "db2_21607065");

	if (mysqli_connect_errno()) {
		echo "Failed to connect to MySQL: " . mysqli_connect_error();
		die();
	}

?>