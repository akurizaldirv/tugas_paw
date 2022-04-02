<?php 
$conn = mysqli_connect("localhost", "root", "", "sportdigi");

function query($query){
	global $conn;
	$result = mysqli_query($conn,$query);
	$rows = [];
	//var_dump($result);
	while ($row = mysqli_fetch_assoc($result)){
		$rows[] = $row;
	}
	return $rows;
}

function hitung_baris($tabel) {
	global $conn;
	$query = "SELECT * FROM $tabel";
	$result = mysqli_query($conn, $query);
	return mysqli_num_rows($result);
}

function tamba($data){
	global $conn;
	$nama =  htmlspecialchars($data["nama"]);
	$deskripsi =  htmlspecialchars($data["deskripsi"]);

	//querry insert data
	$query = "INSERT INTO kategori (id, namakategori, deskripsi) VALUES ('', '$nama', '$deskripsi')"; 

	$deleted = query("SELECT * FROM kategori WHERE id = $id")[0];

	$namakategori = $deleted['namakategori'];
	$deskripsi = $deleted['deskripsi'];
	$tanggalpost = $deleted['tanggalpost'];

	mysqli_query($conn, "INSERT INTO tempkategori VALUES ('', '$namakategori', '$deskripsi', '$tanggalpost')");

	mysqli_query($conn, "DELETE FROM kategori WHERE id = $id");

	return mysqli_affected_rows($conn);
}


function ubah($data){
	global $conn;
	$id = $data["id"];
	$nama =  htmlspecialchars($data["nama"]);
	$deskripsi =  htmlspecialchars($data["deskripsi"]);

	//querry insert data
	$query = "UPDATE kategori SET namakategori = '$nama', deskripsi = '$deskripsi'  WHERE id = $id";
	mysqli_query($conn, $query);

	return mysqli_affected_rows($conn);
}

function hapus($id){
	global $conn;

	$id = $_GET['id'];

	$deleted = query("SELECT * FROM kategori WHERE id = $id")[0];
	$namakategori = $deleted['namakategori'];
	$deskripsi = $deleted['deskripsi'];
	$tanggal = $deleted['tanggalpost'];

	mysqli_query($conn, "INSERT INTO tempkategori VALUES ('', '$namakategori', '$deskripsi', '$tanggal')");

	mysqli_query($conn, "DELETE FROM kategori WHERE id = $id");

	return mysqli_affected_rows($conn);
}

function cari($keyword){
	$query = "SELECT * FROM postingan WHERE judul LIKE '%$keyword%' OR isi LIKE '%$keyword%";
	return query($query);
}


function delTempKategori($id){
	global $conn;

	mysqli_query($conn, "DELETE FROM tempkategori WHERE id = $id");

	return mysqli_affected_rows($conn);
}

function ubahpas($data){
	global $conn;
	$oldpas = $data["passwordlama"];
	$newpas = $data["password"];
	$newpas2 = $data["password2"];

	$result = mysqli_query($conn, "SELECT * FROM user WHERE id = 'admin'	");
	$row = mysqli_fetch_assoc($result);
	
	if (password_verify($oldpas, $row['password'])) {
		if ($newpas != $newpas2) {
			echo "<script>
				alert('konfirmasi password tidak Sesuai!')
				</script>
				";
			return false;
		}
	} else {
		echo "<script>
			alert('recent password tidak sesuai!')
			</script>
			";
		return false;
	}
	
	
	// if ($newpas != $newpas2) {
	// 		echo "<script>
	// 			alert('konfirmasi password tidak sesuai!')
	// 			</script>
	// 			";
	// 		return false;
	// 	}

	$newpas = password_hash($newpas, PASSWORD_DEFAULT);

	mysqli_query($conn, "UPDATE user SET password = '$newpas' WHERE id = 'admin'");

	return mysqli_affected_rows($conn);
	
}


function restoreTempKategori($id){
	global $conn;

	$id = $_GET['id'];

	$deleted = query("SELECT * FROM tempkategori WHERE id = $id")[0];

	$namakategori = $deleted['namakategori'];
	$deskripsi = $deleted['deskripsi'];
	$tanggalpost = $deleted['tanggalpost'];

	mysqli_query($conn, "INSERT INTO kategori VALUES ('', '$namakategori', '$deskripsi', '$tanggalpost')");

	mysqli_query($conn, "DELETE FROM tempkategori WHERE id = $id");

	return mysqli_affected_rows($conn);
}


function addPost($data){
	global $conn;
	$judul =  $data["judul"];
	$isi =  $data["ckeditor"];
	$info_kategori = ($data["info_kategori"]);

	// upload gambar
	$gambar = upload();
	if (!$gambar){
		return false;
	}

	//querry insert data
	$query = "INSERT INTO postingan (id, judul, isi, info_kategori, gambar) VALUES ('', '$judul', '$isi', '$info_kategori', '$gambar')";
	mysqli_query($conn, $query);


	return mysqli_affected_rows($conn);
}



function uplod(){
	$namafile = $_FILES['gambar']['name'];
	$ukuranfile = $_FILES['gambar']['size'];
	$error = $_FILES['gambar']['error'];
	$tmpname = $_FILES['gambar']['tmp_name'];	

	// cek apaakadah  ada gambar yg di upload
	if ($error === 4){
		echo "<script>
				alert('pilih gambar terlebih dahulu');
				</script>
				";
	}
	// cek apakah yang di uypload gambar
	$ekstensigambarvalid = ['jpg','jpeg','png'];
	$ekstensigambar = explode('.', $namafile);
	$ekstensigambar = strtolower(end($ekstensigambar));
	if (!in_array($ekstensigambar, $ekstensigambarvalid)){
		echo "<script>
				alert('yang anda upload bukan gambar');
				</script>
				";
		return false;
	}

	// cek ukuran gambar 
	if ($ukuranfile > 1000000){
		echo "<script>
				alert('ukuran gambar terlalu besar');
				</script>
				";
		return false;
	}
	// generate nama file
	$namafilebaru = uniqid();
	$namafilebaru .=  '.';
	$namafilebaru .= $ekstensigambar;
	// lolos pengecekan semua
	move_uploaded_file($tmpname, 'img_post/' . $namafilebaru);

	return $namafilebaru;


}





function delPos($id){
	global $conn;

	$id = $_GET['id'];

	$deleted = query("SELECT * FROM postingan WHERE id = $id")[0];
	$judul = $deleted['judul'];
	$isi = $deleted['isi'];
	$info_kategori = $deleted['info_kategori'];
	$gambar = $deleted['gambar'];
	$waktu = $deleted['waktu'];

	mysqli_query($conn, "INSERT INTO temppost VALUES ('', '$judul', '$isi', '$info_kategori', '$gambar', '$waktu')");

	mysqli_query($conn, "DELETE FROM postingan WHERE id = $id");

	return mysqli_affected_rows($conn);
}


function editPost($data){
	global $conn;

	$id = $data["id"];
	$judul =  htmlspecialchars($data["judul"]);
	$isi =  htmlspecialchars($data["isi"]);
	$info_kategori = htmlspecialchars($data["info_kategori"]);
	
	$gambar = upload();
	if (!$gambar){
		return false;
	}

	//querry insert data
	$query = "UPDATE postingan SET judul = '$judul', isi = '$isi', info_kategori = '$info_kategori', gambar = '$gambar'  WHERE id = $id";
	mysqli_query($conn, $query);

	return mysqli_affected_rows($conn);
}


function delTempPos($id){
	global $conn;

	mysqli_query($conn, "DELETE FROM temppost WHERE id = $id");

	return mysqli_affected_rows($conn);
}


function restoreTempPos($id){
	global $conn;

	$id = $_GET['id'];

	$deleted = query("SELECT * FROM temppost WHERE id = $id")[0];

	$judul = $deleted['judul'];
	$isi = $deleted['isi'];
	$info_kategori = $deleted['info_kategori'];
	$gambar = $deleted['gambar'];
	$waktu = $deleted['waktu'];

	mysqli_query($conn, "INSERT INTO postingan VALUES ('', '$judul', '$isi', '$info_kategori', '$gambar', '$waktu')");

	mysqli_query($conn, "DELETE FROM temppost WHERE id = $id");

	return mysqli_affected_rows($conn);
}


function addKomen($data, $idComment){
	global $conn;
	$Nama =  htmlspecialchars($data["Nama"]);
	$Email =  htmlspecialchars($data["Email"]);
	$Comment = htmlspecialchars($data["Comment"]);
	$Post= htmlspecialchars($idComment);




	//querry insert data
	$query = "INSERT INTO komentar (No, Nama, Email, Comment,Post) VALUES ('', '$Nama', '$Email', '$Comment','$idComment')";
	mysqli_query($conn, $query);


	return mysqli_affected_rows($conn);
}
function delComm($id){
	global $conn;

	mysqli_query($conn, "DELETE FROM komentar WHERE No = $id");

	return mysqli_affected_rows($conn);
}







///////////////////////////////////// SPORTDIGI COMPANY ////////////////////////////////////////
///////////////////////////////////// SPORTDIGI COMPANY ////////////////////////////////////////
///////////////////////////////////// SPORTDIGI COMPANY ////////////////////////////////////////
///////////////////////////////////// SPORTDIGI COMPANY ////////////////////////////////////////
///////////////////////////////////// SPORTDIGI COMPANY ////////////////////////////////////////
///////////////////////////////////// SPORTDIGI COMPANY ////////////////////////////////////////
///////////////////////////////////// SPORTDIGI COMPANY ////////////////////////////////////////
///////////////////////////////////// SPORTDIGI COMPANY ////////////////////////////////////////
///////////////////////////////////// SPORTDIGI COMPANY ////////////////////////////////////////
///////////////////////////////////// SPORTDIGI COMPANY ////////////////////////////////////////
///////////////////////////////////// SPORTDIGI COMPANY ////////////////////////////////////////
///////////////////////////////////// SPORTDIGI COMPANY ////////////////////////////////////////
///////////////////////////////////// SPORTDIGI COMPANY ////////////////////////////////////////
///////////////////////////////////// SPORTDIGI COMPANY ////////////////////////////////////////
///////////////////////////////////// SPORTDIGI COMPANY ////////////////////////////////////////
///////////////////////////////////// SPORTDIGI COMPANY ////////////////////////////////////////
///////////////////////////////////// SPORTDIGI COMPANY ////////////////////////////////////////
///////////////////////////////////// SPORTDIGI COMPANY ////////////////////////////////////////
///////////////////////////////////// SPORTDIGI COMPANY ////////////////////////////////////////
///////////////////////////////////// SPORTDIGI COMPANY ////////////////////////////////////////
///////////////////////////////////// SPORTDIGI COMPANY ////////////////////////////////////////
///////////////////////////////////// SPORTDIGI COMPANY ////////////////////////////////////////
///////////////////////////////////// SPORTDIGI COMPANY ////////////////////////////////////////
///////////////////////////////////// SPORTDIGI COMPANY ////////////////////////////////////////
///////////////////////////////////// SPORTDIGI COMPANY ////////////////////////////////////////
///////////////////////////////////// SPORTDIGI COMPANY ////////////////////////////////////////
///////////////////////////////////// SPORTDIGI COMPANY ////////////////////////////////////////
///////////////////////////////////// SPORTDIGI COMPANY ////////////////////////////////////////











function delTempPost($id){
	global $conn;

	mysqli_query($conn, "DELETE FROM temppost WHERE id = $id");
	mysqli_query($conn, "DELETE FROM beritapenulis WHERE id = $id");

	return mysqli_affected_rows($conn);
}

function restoreTempPost($id){
	global $conn;
	$deleted = query("SELECT * FROM temppost WHERE id = $id")[0];
	$info_kategori = $deleted['info_kategori'];
	$judul = $deleted['judul'];
	$isi = $deleted['isi'];
	$waktu = $deleted['waktu'];
	$penulis = $deleted['penulis'];
	$views = $deleted['views'];
	$gambar = $deleted['gambar'];

	if ($info_kategori == "") {
		mysqli_query($conn, "INSERT INTO beritaadmin VALUES ('$id', '$judul', '$isi', '$waktu', '$gambar', '', '$penulis', '', '')");
		mysqli_query($conn, "DELETE FROM temppost WHERE id = '$id'");
	}else{
		mysqli_query($conn, "INSERT INTO postingan VALUES ('$id', '$judul', '$isi', '$info_kategori', '$gambar','$waktu', '', '$penulis')");
		mysqli_query($conn, "DELETE FROM temppost WHERE id = '$id'");
	}

	return mysqli_affected_rows($conn);
}

function delPost($id){
	global $conn;

	// $id = $_GET['id'];

	$deleted = query("SELECT * FROM postingan WHERE id = $id")[0];
	$judul = $deleted['judul'];
	$isi = $deleted['isi'];
	$info_kategori = $deleted['info_kategori'];
	$gambar = $deleted['gambar'];
	$waktu = $deleted['waktu'];
	$views = $deleted['views'];
	$penulis = $deleted['penulis'];

	mysqli_query($conn, "INSERT INTO temppost VALUES ($id, '$judul', '$isi', '$info_kategori', '$gambar', '$waktu', '$views', '$penulis')");

	mysqli_query($conn, "DELETE FROM postingan WHERE id = $id");

	mysqli_query($conn, "UPDATE beritapenulis SET status='Dropped' WHERE id = $id");

	return mysqli_affected_rows($conn);
}

function delComment($id){
	global $conn;

	mysqli_query($conn, "DELETE FROM komentar WHERE No = $id");

	return mysqli_affected_rows($conn);
}

function uploadBerita($id){
	global $conn;

	// $id = $_GET['id'];
	// var_dump($id);

	$data = query("SELECT * FROM beritaadmin WHERE id = '$id'")[0];
	$judul = $data['judul'];
	$isi = $data['isi'];
	$info_kategori = $data['info_kategori'];
	$gambar = $data['gambar'];
	$waktu = $data['waktu'];
	$penulis = $data['penulis'];	

	// var_dump($data);

	mysqli_query($conn, "INSERT INTO postingan (id, judul, isi, info_kategori, gambar, penulis) VALUES ('$id', '$judul', '$isi', '$info_kategori', '$gambar', '$penulis')");
	mysqli_query($conn, "UPDATE beritapenulis SET status = 'Uploaded' WHERE id = $id");
	mysqli_query($conn, "DELETE FROM beritaadmin WHERE id = $id");
	//mysqli_query($conn, "INSERT INTO postingan VALUES ('', '$judul', '$isi', '$info_kategori', '$gambar', '', '', '$penulis')");

	//mysqli_query($conn, "DELETE FROM postingan WHERE id = $id");

	return mysqli_affected_rows($conn);
}


function tambahKomentar($data, $id){
	global $conn;
	$Nama =  htmlspecialchars($data["Nama"]);
	$Email =  htmlspecialchars($data["Email"]);
	$Comment = htmlspecialchars($data["Comment"]);
	$penulis = htmlspecialchars($data['penulis']);
	// $Post= htmlspecialchars($idComment);




	//querry insert data
	$query = "INSERT INTO komentar (No, Nama, Email, Comment, Post, penulis) VALUES ('', '$Nama', '$Email', '$Comment','$id', '$penulis')";
	mysqli_query($conn, $query);


	return mysqli_affected_rows($conn);
}

// function secure($data){
// 	global $conn;
// 	$id = htmlspecialchars($data['id']);
// 	$uname = strtolower(stripcslashes($data["new-uname"]));
// 	$pass = mysqli_real_escape_string($conn, $data["new-pass"]);
// 	$pass2 = mysqli_real_escape_string($conn, $data["conf-pass"]);


// 	// cek usernmae udh ada
// 	$result =mysqli_query($conn, "SELECT username FROM user WHERE username = '$uname'");
// 	if (mysqli_fetch_assoc($result)){
// 		echo "<script>
// 		alert('username sudah terdaftar')
// 		</script>";
// 		return false;
// 	}


// 	// cek konfirmasi password
// 	if ($pass !== $pass2){
// 		echo "<script>
// 		alert('konfirmasi password tidak sesuai!')
// 		</script>
// 		";
// 		return false;
// 	}

// 	// enkripsi password
// 	$pass = password_hash($pass, PASSWORD_DEFAULT);


// 	// tambahkan user baru ke database
// 	mysqli_query($conn, "UPDATE user SET username = '$uname', password = '$pass' WHERE id = $id");

// 	return mysqli_affected_rows($conn);
// }

function secureuser($data){
	global $conn;
	$id = $data['id'];
	$oldPass = strtolower(stripcslashes($data["passwordlama"]));
	$pass = mysqli_real_escape_string($conn, $data["password"]);
	$pass2 = mysqli_real_escape_string($conn, $data["password2"]);

	$result = mysqli_query($conn, "SELECT * FROM user WHERE id = '$id'");
	$row = mysqli_fetch_assoc($result);

	// $oldPass = password_hash($oldPass, PASSWORD_DEFAULT);
	// var_dump($data); 
	// var_dump($id);
	// var_dump($oldPass);
	// var_dump($result);
	// var_dump($row['password']);
	
	if (password_verify($oldPass, $row['password'])) {
		if ($pass != $pass2) {
			echo "<script>
				alert('Konfirmasi Password Salah')
				</script>
				";
			return false;
		}
	} else {
		echo "<script>
			alert('Password Lama tidak Sesuai')
			</script>
			";
		return false;
	}
	


	// cek usernmae udh ada
	// $result =mysqli_query($conn, "SELECT username FROM user WHERE username = '$uname'");
	// if (mysqli_fetch_assoc($result)){
	// 	echo "<script>
	// 	alert('username sudah terdaftar')
	// 	</script>";
	// 	return false;
	// }


	// // cek konfirmasi password
	// if ($pass !== $pass2){
	// 	echo "<script>
	// 	alert('konfirmasi password tidak sesuai!')
	// 	</script>
	// 	";
	// 	return false;
	// }

	// enkripsi password
	$pass = password_hash($pass, PASSWORD_DEFAULT);


	// tambahkan user baru ke database
	mysqli_query($conn, "UPDATE user SET password = '$pass' WHERE id = $id");

	return mysqli_affected_rows($conn);
}

function ubahuser($data){
	global $conn;
	$nama = htmlspecialchars($data["nama"]);
	$tgl =  htmlspecialchars($data["tgl"]);
	$nohp =  htmlspecialchars($data["nohp"]);
	$email =  htmlspecialchars($data["email"]);
	$username =  htmlspecialchars($data["username"]);

	// var_dump($tgl);

	//querry insert data
	$query = "UPDATE user SET  
	nama = '$nama', 
	tanggal = '$tgl',
	nohp = '$nohp',
	email = '$email'  
	WHERE username = '$username'";
	mysqli_query($conn, $query);

	return mysqli_affected_rows($conn);
}
// Penulis //

function tulisBerita($data){
	global $conn;
	$username = $data["username"];
	$judul =  $data["judul"];
	$isi =  $data["ckeditor"];
	//$gambar = $data["gambar"];

	// upload gambar
	$gambar = upload();
	if (!$gambar){
		return false;
	}

	//querry insert data
	$query = "INSERT INTO beritapenulis (id, judul, isi, gambar, penulis) VALUES ('', '$judul', '$isi', '$gambar', '$username')";
	mysqli_query($conn, $query);


	return mysqli_affected_rows($conn);
}

function upload(){
	$namafile = $_FILES['gambar']['name'];
	$ukuranfile = $_FILES['gambar']['size'];
	$error = $_FILES['gambar']['error'];
	$tmpname = $_FILES['gambar']['tmp_name'];	

	// cek apaakadah  ada gambar yg di upload
	if ($error === 4){
		echo "<script>
				alert('pilih gambar terlebih dahulu');
				</script>
				";
	}
	// cek apakah yang di uypload gambar
	$ekstensigambarvalid = ['jpg','jpeg','png'];
	$ekstensigambar = explode('.', $namafile);
	$ekstensigambar = strtolower(end($ekstensigambar));
	if (!in_array($ekstensigambar, $ekstensigambarvalid)){
		echo "<script>
				alert('yang anda upload bukan gambar');
				</script>
				";
		return false;
	}

	// cek ukuran gambar 
	if ($ukuranfile > 1000000){
		echo "<script>
				alert('ukuran gambar terlalu besar');
				</script>
				";
		return false;
	}
	// generate nama file
	$namafilebaru = uniqid();
	$namafilebaru .=  '.';
	$namafilebaru .= $ekstensigambar;
	// lolos pengecekan semua
	move_uploaded_file($tmpname, 'img_post/' . $namafilebaru);

	return $namafilebaru;
}

function editBerita($data){
	global $conn;

	$id = $data["id"];
	$judul =  htmlspecialchars($data["judul"]);
	$isi =  htmlspecialchars($data["ckeditor"]);
	
	$gambar = upload();
	if (!$gambar){
		return false;
	}

	//querry insert data
	$query = "UPDATE beritapenulis SET judul = '$judul', isi = '$isi', gambar = '$gambar'  WHERE id = $id";
	mysqli_query($conn, $query);

	return mysqli_affected_rows($conn);
}

function delBerita($id){
	global $conn;

	//$id = $_GET['id'];

	$deleted = query("SELECT * FROM beritapenulis WHERE id = $id")[0];
	// var_dump($deleted);

	if ($deleted["status"] == "published") {
		$judul = $deleted["judul"];
		$isi = $deleted["isi"];
		$gambar = $deleted["gambar"];
		$waktu = $deleted["waktu"];
		$status = $deleted["status"];
		// var_dump("ASD");
		mysqli_query($conn, "INSERT INTO tempberita VALUES ('', '$judul', '$isi', '$waktu', '$gambar', '$status')");

		mysqli_query($conn, "DELETE FROM beritapenulis WHERE id = $id");
	} else {
		mysqli_query($conn, "DELETE FROM beritapenulis WHERE id = $id");
	}
	

	return mysqli_affected_rows($conn);
}

function toEditor($id){
	global $conn;

	//$id = $_GET['id'];

	$data = query("SELECT * FROM beritapenulis WHERE id = $id")[0];
	//var_dump($data);

	$judul = $data["judul"];
	$isi = $data["isi"];
	$gambar = $data["gambar"];
	$waktu = $data["waktu"];
	$penulis = $data["penulis"];
	// var_dump("ASD");
	mysqli_query($conn, "INSERT INTO beritaeditor (id, judul, isi, waktu, gambar, penulis) VALUES ('$id', '$judul', '$isi', '$waktu', '$gambar', '$penulis')");
	
	mysqli_query($conn, "UPDATE beritapenulis SET status = 'Proposed' WHERE id = $id");

	return mysqli_affected_rows($conn);
}
function toAdmin($id){
	global $conn;

	//$id = $_GET['id'];

	$data = query("SELECT * FROM beritaeditor WHERE id = $id")[0];
	//var_dump($data);
	$id = $data["id"];
	$judul = $data["judul"];
	$isi = $data["isi"];
	$gambar = $data["gambar"];
	$penulis = $data['penulis'];
	// var_dump($data);
	//mysqli_query($conn, "INSERT INTO beritaeditor (id, judul, isi, waktu, gambar) VALUES ('$id', '$judul', '$isi', '$waktu', '$gambar')");
	
	mysqli_query($conn, "INSERT INTO beritaadmin (id, judul, isi, gambar, penulis) VALUES ('$id', '$judul', '$isi', '$gambar', '$penulis')");
	mysqli_query($conn, "DELETE FROM beritaeditor WHERE id = $id");
	//mysqli_query($conn, "UPDATE beritapenulis SET status = 'Uploaded' WHERE id = $id");

	return mysqli_affected_rows($conn);
}
function Reject($id){
	global $conn;

	//$id = $_GET['id'];

	$data = query("SELECT * FROM beritapenulis WHERE id = $id")[0];
	//var_dump($data);
	$id = $data["id"];
	$judul = $data["judul"];
	$isi = $data["ckeditor"];
	$catatan = $data["catatan"];
	var_dump($data);
	//mysqli_query($conn, "INSERT INTO beritaeditor (id, judul, isi, waktu, gambar) VALUES ('$id', '$judul', '$isi', '$waktu', '$gambar')");
	
	mysqli_query($conn, "UPDATE beritapenulis SET judul = '$judul', isi = '$isi', catatan = '$catatan', status = 'Revisi' WHERE id = $id");
	mysqli_query($conn, "DELETE FROM beritaeditor WHERE id = $id");

	return mysqli_affected_rows($conn);
}

function simpanBerita($data){
	global $conn;

	//$id = $_GET['id'];

	//$data = query("SELECT * FROM beritapenulis WHERE id = $id")[0];
	//var_dump($data);
	$id = $data["id"];
	$judul = $data["judul"];
	$isi = $data["ckeditor"];
	$catatan = $data["catatan"];
	// var_dump("ASD");
	//mysqli_query($conn, "INSERT INTO beritaeditor (id, judul, isi, waktu, gambar) VALUES ('$id', '$judul', '$isi', '$waktu', '$gambar')");
	
	mysqli_query($conn, "UPDATE beritaeditor SET judul = '$judul', isi = '$isi', catatan = '$catatan' WHERE id = $id");
	//mysqli_query($conn, "UPDATE beritapenulis SET catatan = '$catatan' WHERE id = $id");
	return mysqli_affected_rows($conn);
}

function tinjauBerita($data){
	global $conn;

	//$id = $_GET['id'];

	//$data = query("SELECT * FROM beritapenulis WHERE id = $id")[0];
	//var_dump($data);
	$id = $data["id"];
	$judul = $data["judul"];
	$isi = $data["ckeditor"];
	$catatan = $data["catatan"];
	var_dump($data);
	//mysqli_query($conn, "INSERT INTO beritaeditor (id, judul, isi, waktu, gambar) VALUES ('$id', '$judul', '$isi', '$waktu', '$gambar')");
	
	mysqli_query($conn, "UPDATE beritapenulis SET judul = '$judul', isi = '$isi', catatan = '$catatan', status = 'Revisi' WHERE id = $id");
	mysqli_query($conn, "DELETE FROM beritaeditor WHERE id = $id");

	return mysqli_affected_rows($conn);
}

function accBerita($data){
	global $conn;

	//$id = $_GET['id'];

	//$data = query("SELECT * FROM beritapenulis WHERE id = $id")[0];
	//var_dump($data);
	$id = $data["id"];
	$judul = $data["judul"];
	$isi = $data["ckeditor"];
	$gambar = $data["gambar"];
	$penulis = $data['penulis'];
	var_dump($data);
	//mysqli_query($conn, "INSERT INTO beritaeditor (id, judul, isi, waktu, gambar) VALUES ('$id', '$judul', '$isi', '$waktu', '$gambar')");
	
	mysqli_query($conn, "INSERT INTO beritaadmin (id, judul, isi, gambar, penulis) VALUES ('$id', '$judul', '$isi', '$gambar', '$penulis')");
	mysqli_query($conn, "DELETE FROM beritaeditor WHERE id = $id");
	//mysqli_query($conn, "UPDATE beritapenulis SET status = 'Uploaded' WHERE id = $id");

	return mysqli_affected_rows($conn);
}

// function accBerita($data){
// 	global $conn;

// 	//$id = $_GET['id'];

// 	//$data = query("SELECT * FROM beritapenulis WHERE id = $id")[0];
// 	//var_dump($data);
// 	$id = $data["id"];
// 	$judul = $data["judul"];
// 	$isi = $data["ckeditor"];
// 	$gambar = $data["gambar"];
// 	var_dump($data);
// 	//mysqli_query($conn, "INSERT INTO beritaeditor (id, judul, isi, waktu, gambar) VALUES ('$id', '$judul', '$isi', '$waktu', '$gambar')");
	
// 	mysqli_query($conn, "INSERT INTO postingan (id, judul, isi, gambar) VALUES ('$id', '$judul', '$isi', '$gambar')");
// 	mysqli_query($conn, "DELETE FROM beritaeditor WHERE id = $id");
// 	mysqli_query($conn, "UPDATE beritapenulis SET status = 'Uploaded' WHERE id = $id");

// 	return mysqli_affected_rows($conn);
// }


/// Kategori

function tambahKategori($data){
	global $conn;

	var_dump("asdasdasd");

	$nama =  htmlspecialchars($data["nama"]);
	$deskripsi =  htmlspecialchars($data["deskripsi"]);

	//querry insert data 
	mysqli_query($conn, "INSERT INTO kategori VALUES ('', '$nama', '$deskripsi')");


	return mysqli_affected_rows($conn);
}

function editKategori($data){
	global $conn;
	$id = $data["id"];
	$nama =  htmlspecialchars($data["nama"]);
	$deskripsi =  htmlspecialchars($data["deskripsi"]);

	//querry insert data
	$query = "UPDATE kategori SET namakategori = '$nama', deskripsi = '$deskripsi'  WHERE id = $id";
	mysqli_query($conn, $query);

	return mysqli_affected_rows($conn);
}

function delKategori($id){
	global $conn;

	// $id = $_GET['id'];

	// $deleted = query("SELECT * FROM kategori WHERE id = $id")[0];
	// $namakategori = $deleted['namakategori']; 
	// $deskripsi = $deleted['deskripsi'];
	// $tanggal = $deleted['tanggalpost'];

	// mysqli_query($conn, "INSERT INTO tempkategori VALUES ('', '$namakategori', '$deskripsi', '$tanggal')");
	
	$result = mysqli_query($conn, "SELECT * FROM postingan WHERE info_kategori = '$id'");
	$count =  mysqli_num_rows($result);

	$result2 = mysqli_query($conn, "SELECT * FROM beritaadmin WHERE info_kategori = '$id'");
	$count2 =  mysqli_num_rows($result2);

	$result3 = mysqli_query($conn, "SELECT * FROM temppost WHERE info_kategori = '$id'");
	$count3 =  mysqli_num_rows($result3);

	$rows = [];
	//var_dump($result);
	while ($row = mysqli_fetch_assoc($result)){
		$rows[] = $row;
	}

	$rows2 = [];
	//var_dump($result);
	while ($row2 = mysqli_fetch_assoc($result2)){
		$rows2[] = $row2;
	}
	
	$rows3 = [];
	//var_dump($result);
	while ($row3 = mysqli_fetch_assoc($result3)){
		$rows3[] = $row3;
	}

	// var_dump($rows);
	// var_dump($count);

	// Looping Postingan
	$i = 0;
	while ($i<$count) {
		$a = $rows[$i]['id'];

		$judul = $rows[$i]['judul'];
		$isi = $rows[$i]['isi'];
		$waktu = $rows[$i]['waktu'];
		$penulis = $rows[$i]['penulis'];
		$views = $rows[$i]['views'];
		$gambar = $rows[$i]['gambar'];

		mysqli_query($conn, "INSERT INTO beritaadmin VALUES ($a, '$judul', '$isi', '$waktu', '$gambar', '', '$penulis', '', '')");
		mysqli_query($conn, "DELETE FROM postingan WHERE id = '$a'");
		$i = $i + 1;
	}

	$i2 = 0;
	while ($i2<$count2) {
		$a2 = $rows2[$i2]['id'];

		mysqli_query($conn, "UPDATE beritaadmin SET info_kategori = '' WHERE id = '$a2'");
		$i2 = $i2 + 1;
	}

	$i3 = 0;
	while ($i3<$count3) {
		$a3 = $rows3[$i3]['id'];

		mysqli_query($conn, "UPDATE temppost SET info_kategori = '' WHERE id = '$a3'");
		$i3 = $i3 + 1;
	}

	mysqli_query($conn, "DELETE FROM kategori WHERE id = $id");

	return mysqli_affected_rows($conn);
}

function setKategori($data){
	global $conn;
	$id = $data["id"];
	$ktrg =  htmlspecialchars($data["ktrg"]);

	//querry insert data
	$query = "UPDATE beritaadmin SET info_kategori = '$ktrg'  WHERE id = $id";
	mysqli_query($conn, $query);

	return mysqli_affected_rows($conn);
}

function regristrasi($data){
	global $conn;
	$nama = $data["nama"];
	$email = $data["email"];
	$username = strtolower(stripcslashes($data["username"]));
	$password = mysqli_real_escape_string($conn, $data["password"]);
	$password2 = mysqli_real_escape_string($conn, $data["password2"]);

	// cek usernmae udh ada
	$result =mysqli_query($conn, "SELECT username FROM user WHERE username = '$username'");
	if (mysqli_fetch_assoc($result)){
		echo "<script>
		alert('username sudah terdaftar')
		</script>";
		return false;
	}


	// cek konfirmasi password
	if ($password !== $password2){
		echo "<script>
		alert('konfirmasi password tidak sesuai!')
		</script>
		";
		return false;
	}

	// enkripsi password
	$password = password_hash($password, PASSWORD_DEFAULT);


	// tambahkan user baru ke database
	mysqli_query($conn, "INSERT INTO user VALUES('', '$nama', '','','$username','$email', '$password','Penulis')");

	return mysqli_affected_rows($conn);


}
?>


