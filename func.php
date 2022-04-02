<?php 
$conn = mysqli_connect("ejuwelen.com", "ejuwelen_user1", "user1user1", "ejuwelen_kasir");

function query($query){
	global $conn;
	$result = mysqli_query($conn,$query);
	$rows = [];
	while ($row = mysqli_fetch_assoc($result)){
		$rows[] = $row;
	}
	return $rows;
}


?>