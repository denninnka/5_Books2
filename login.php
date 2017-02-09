<?php
$pageTitle = 'Влез';
include dirname(__FILE__) . '/includes/header.php';
if (isset($_POST['reg'])) {
    header('Location: ./registration.php');
    exit;
}
if (isset($_POST['vhod'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $username = mysqli_real_escape_string($con, $username);
    $password = mysqli_real_escape_string($con, $password);
    $error    = [];
    $q        = 'SELECT * FROM users
		  WHERE username = "' . $username . '" AND password = "' . $password . '"';
    if ($result = mysqli_query($con, $q)) {
        $res = $result->fetch_assoc();
        //var_dump($res);
        $rowcount = mysqli_num_rows($result);
        if ($rowcount != 0) {
            $_SESSION['isLogged'] = true;            
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $res['user_id'];
            
            //var_dump($_SESSION['user_id']);
            header('Location: index.php');
            exit;
        } else {
            echo "Грешно потребителско име или парола";
        }
    }
}

?>
<h1>Добре дошли в нашата библиотека БИБЛИЯ</h1>
<form method="POST">
<table>
<tr><td>Потребителско име:</td><td> <input type="text" name="username"/></td></tr>
<tr><td>Парола: </td><td><input type="text" name="password"></td></tr>
<tr><td></td><td><input type="submit" name="vhod" value="Влез" /></td></p>
</table>
<p>Ако не сте регистриран може да се регистрирате тук <input type="submit" name="reg" value="Регистрация" /></p>
</form>

<?php

?>


<?php
include dirname(__FILE__) . '/includes/footer.php';
?>
