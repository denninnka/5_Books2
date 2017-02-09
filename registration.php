<?php
$pageTitle='Регистрация';
include dirname(__FILE__).'/includes/header.php';
if (isset($_POST['back'])) {
	header('Location: ./login.php');
	exit;
}
if (isset($_POST['adduser'])) {
	$username = trim($_POST['username']);
	$password = trim($_POST['password']);
	$repassword = trim($_POST['repassword']);
	$error = [];
	if (mb_strlen($username) < 3 || mb_strlen($username) > 50) {
		$error[] = "<p>Името не трябва да е по късо от 3 символа и не може да е по-дълго от 50 символа</p>";
	}
	if (mb_strlen($password) !== 6) {
		$error[] = "<p>Паролата трябва да съдържа 6 символа</p>";
	}
	if ($password !== $repassword) {
		$error[] = "<p>Паролата е грешна</p>";
	}
	$username = mysqli_real_escape_string($con, $username);
	$q = 'SELECT username FROM users WHERE username = "' . $username .'"';
	if ($result = mysqli_query($con, $q)) {
		if (mysqli_error($con)) {
		echo "Грешка";
		exit;
		}
		$rowcount = mysqli_num_rows($result);
		if ($rowcount != 0) {
			$error[] = "<p>Съществува потребител с такова име</p>";
		}
	}
	$password = mysqli_real_escape_string($con, $password);
	if (count($error) > 0) {
		foreach ($error as $value) {
			echo '<p>'.$value.'</p>';
		}
	}
	else{
		$query = mysqli_query($con, 'INSERT INTO users (username, password) VALUES ("' . $username . '" , "' . $password . '")');
		if (mysqli_error($con)) {
			echo "Грешка";
			exit;
		}
		if (mysqli_query($con, $q)) {
			echo '<p>Вашата регистрация е успешна можете да влезете в системата от <a href="login.php">тук</a></p>';
		}
	}
}
 
?>

<h2>Регистрирай се в библиотека БИБЛИЯ</h2>
<form method="POST">
	<table>
		<tr><td>Потребителско име:</td><td> <input type="text" name="username"/></td></tr>
		<tr><td>Парола: </td><td><input type="text" name="password"></td></tr>
		<tr><td>Повтори парола: </td><td><input type="text" name="repassword"></td></tr>
		<tr><td><input type="submit" name="back" value="Начало" /></td><td><input type="submit" name="adduser" value="Регистрация" /></td></p>
	</table>
</form>

<?php
include dirname(__FILE__).'/includes/footer.php';
?>
