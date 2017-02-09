<?php
$pageTitle = 'Потребител';
include dirname(__FILE__) . '/includes/header.php';

$query = mysqli_query($con, 'SELECT user_id FROM users WHERE user_id ='.(int)$_GET['user_id']);
$rez = mysqli_num_rows($query);
if ($rez!=1) {
	echo "Няма такъв потребител";
}else{

$q = mysqli_query($con, 'SELECT username , b.book_id, message, message_date, book_title 
						 FROM `users` u 
						 LEFT JOIN messages m 
						   ON u.user_id = m.user_id 
						 LEFT JOIN books b 
						   ON m.book_id = b.book_id 
						 WHERE u.user_id = '.(int)$_GET['user_id']);
$result = [];
while ($row = mysqli_fetch_assoc($q)) {
	$result[] = $row;
}
echo '<h3>Здравей, '. current($result)['username'] .' тук са всичките ти коментари: </h3>';
//var_dump($result);

?>
<form method="POST">
	<table border="1">
	<tr><td>Книга</td>
	    <td>Коментар</td>
	    <td>Дата</td>
	</tr>
	<?php 

foreach ($result as $key ) {
	echo '<tr><td><a href="index.php?book_id=' . $key['book_id'] . '">'.$key['book_title'].'</a></td>
	          <td>'.$key['message'].'</td>
	          <td>'.$key['message_date'].'</td>
	      </tr>';
}
	?>

	</table>
</form>


<?php
}

include dirname(__FILE__) . '/includes/footer.php';
?>