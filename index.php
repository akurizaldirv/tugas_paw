<?php 
	require 'func.php';

	if (isset($_POST['submit'])) {
		$sorting_to = $_POST['sorting_to'];
		$sorting_by = $_POST['sorting_by'];

		$data = query("SELECT * FROM menu ORDER BY $sorting_by $sorting_to");
	} else {
		$data = query('SELECT * FROM menu');		
	}


 ?>

<!DOCTYPE html>
<html>
<head>
	<title>MENU MAKANAN</title>
</head>
<body>
	<h1>Menu Makanan</h1>
	<form method="post" action="">
		<label for="sorting_by">Sorting by : </label>
		<select name="sorting_by">
			<option value="idmenu">ID Menu</option>
			<option value="nama_menu">Nama Menu</option>
			<option value="harga">Harga</option>
		</select>
		<select name="sorting_to">
			<option value="ASC">Ascending</option>
			<option value="DESC">Descending</option>
		</select>
		<input type="submit" name="submit" value="submit">
	</form>
	<br>
	<table border="2">
		<tr>
			<th>No.</th>
			<th>Nama Makanan</th>
			<th>Harga</th>
		</tr>

		<?php $i = 1 ?>
		<?php foreach ($data as $row) : ?>
		<tr>
			<td><?php echo $i ?></td>
			<td><?php echo $row['nama_menu']; ?></td>
			<td><?php echo $row['harga'] ?></td>
		</tr>
		<?php $i++ ?>
		<?php endforeach; ?>
	</table>
</body>
</html>