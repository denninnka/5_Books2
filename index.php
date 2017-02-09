<?php
$pageTitle = 'Библиотека';
include dirname(__FILE__) . '/includes/header.php';

$sortingB = -1;
$sortingA = -1;
if (!isset($_GET['author_id']) && !isset($_GET['book_id'])) {
    ?>
    <div><h2>Библиотека</h2></div>
    <div><a href="newbook.php">Нова книга</a>&nbsp;&nbsp;&nbsp;<a href="newauthor.php">Нов автор</a></div><div><br/></div>
    <form method="GET">
    <?php
echo 'Книги:&nbsp;&nbsp; <select name="sortBynameBook">
        <option value="-1">Без сортиране</option>';
    foreach ($sorting as $key => $value) {
        echo '<option value="' . $key . '"' . ($key == $_GET['sortBynameBook'] ? 'selected' : '') . '>' . $value . '</option>';
    }
    echo '</select>
        <input type="submit" name="sortBooks" value="Сортирай" /><br/><br/>
        Автори: <select name="sortBynameAuthor">
        <option value="-1">Без сортиране</option>';
    foreach ($sorting as $k => $v) {
        echo '<option value="' . $k . '"' . ($k == $_GET['sortBynameAuthor'] ? 'selected' : '') . '>' . $v . '</option>';
    }
    echo '</select>
        <input type="submit" name="sortAuthors" value="Сортирай" /><br/><br/>
        Kнига: <input type="text" name="book_title" />
        <input type="submit" name="search" value="Търси">';
    ?>
    <div><br/></div>
    </form>
    <?php

    if (isset($_GET['sortBynameBook']) && isset($sorting[$_GET['sortBynameBook']])) {
        $sortingB = $_GET['sortBynameBook'];
    }
    if (isset($_GET['sortBynameAuthor']) && isset($sorting[$_GET['sortBynameAuthor']])) {
        $sortingA = $_GET['sortBynameAuthor'];
    }
    if (isset($_GET['search'])) {
        $book_title = trim($_GET['book_title']);
        $book_title = mysqli_real_escape_string($con, $book_title);
    }
}
$query_String = 'SELECT b.*, a.*, m. message_id, m.message, COUNT(m.message_id) as messages_count 
                 FROM books_authors as ba
                 INNER JOIN books as b 
                 ON b.book_id = ba.book_id
                         ' . (isset($_GET['author_id']) ? ' 
                 INNER JOIN books_authors as bba 
                 ON bba.book_id = ba.book_id' : '') . '
                 INNER JOIN authors as a 
                 ON ' . (isset($_GET['author_id']) ? 'bba' : 'ba') . '.author_id = a.author_id
                 LEFT JOIN messages m
                 ON m.book_id = b.book_id
                 WHERE 1 = 1
                         ' . (!empty($book_title) ? ' AND  book_title LIKE "% ' . $book_title . ' %" ' : '') . '
                         ' . (isset($_GET['book_id']) ? ' AND  b.book_id  = ' . (int) $_GET['book_id'] : '') . '
                         ' . (isset($_GET['author_id']) ? ' AND  ba.author_id  = ' . (int) $_GET['author_id'] : '') . '
                 GROUP BY b.book_id, a.author_id
                 ORDER BY b.book_title ' . $sortingB . ', a.author_name ' . $sortingA;

$q = mysqli_query($con, $query_String);
if (mysqli_error($con)) {
    echo "Грешка" . mysqli_error($con);
}
$result = [];
while ($row = mysqli_fetch_assoc($q)) {
    $result[$row['book_id']]['book_title']                 = $row['book_title'];
    $result[$row['book_id']]['messages_count']                 = $row['messages_count'];
    $result[$row['book_id']]['authors'][$row['author_id']] = $row['author_name'];
}

if (!$result) {
    echo '<p>В библиотеката няма намерена книга "' . htmlentities($book_title) . '" </p>';
} else {
    if (isset($_GET['book_id'])) {
        ?>
            <div><h3><a href="index.php">Върни се в библиотеката</a></h3></div>
            <div><h2>Книгата "<?=current($result)['book_title'];?>" е написана от авторите:</h2></div>
        <?php
}

    if (isset($_GET['author_id'])) {
        echo '<div><h3><a href="index.php">Върни се в библиотеката</a></h3></div>';
        echo '<div><h2>Авторът ' . current($result)['authors'][$_GET['author_id']] . ' участва в книгите:</h2></div>';
    }

    echo '<table border = "1"><tr><td>Книги</td><td>Автори</td><td>Коментари</td></tr>';
    foreach ($result as $key => $row) {
        echo '<tr><td><a href="index.php?book_id=' . $key . '">' . $row['book_title'] . '</a></td><td>';
        $ar = [];
        foreach ($row['authors'] as $k => $va) {
            $ar[] = '<a href="index.php?author_id=' . $k . '">' . $va . '</a>';
        }
        echo implode(' , ', $ar) . '</td><td>'.$row['messages_count'].'</td></tr>';
    }
    echo '</table><div><br/></div>';
}
if (isset($_GET['book_id'])) {

    ?>

    <div><h3>Напиши коментар относно тази книга</h3></div>
    <form method="POST">
        <div><textarea type="text" name="msg" rows="5" cols="80"></textarea></div>
        <div><p>(коментар до 250 символа)</p></div>
        <div><input type="submit" name="send" value="Изпрати"/></div>
        <div><br/></div>

    <?php

    if ($_SESSION['isLogged'] && $_SESSION['username']) {
        if (isset($_POST['send'])) {
            $msg     = trim($_POST['msg']);
            $msg     = mysqli_real_escape_string($con, $msg);
            $book_id = (int) $_GET['book_id'];
            $user_id = (int) $_SESSION['user_id'];
            $error   = false;
            if (mb_strlen($msg) < 1) {
                echo "Трябва да напишите коментар";
                $error = true;
            }
            if (mb_strlen($msg) > 250) {
                echo "Коментарът ви неможе да е по-дълъг от 250 символа!";
                $error = true;
            }
            if (!$error) {
                $query_Insert = 'INSERT INTO messages (message, message_date, user_id, book_id) VALUES ("' . $msg . '", NOW(), "' . $user_id . '", "' . $book_id . '")';
                $query_Ins    = mysqli_query($con, $query_Insert);
                if (mysqli_error($con)) {
                    echo "Грешка22" . mysqli_error($con);
                } else {
                    echo "<p>Вие коментирахте успешно тази кника!</p>";
                    //header('Location: index.php');
                }
            }
        }
        ?>

        <div><br/></div>
        <div><input type="submit" name="logout" value="Изход"/></div>
    </form>

        <?php

        $query_Msg = mysqli_query($con, 'SELECT message_date, message, book_id, username, users.user_id FROM `messages` LEFT JOIN users ON messages.user_id=users.user_id ORDER BY message_date DESC');
        if (mysqli_error($con)) {
            echo "Грешка404" . mysqli_error($con);
        }

        echo "<div><h3>Коментари</h3></div>
          <form method='POST'>
            <table border='1' width='600'>
                <tr><td width='250'>Дата</td>
                <td width='350'>Съобщение</td>";
        while ($row = $query_Msg->fetch_assoc()) {
            if ($_GET['book_id'] == $row['book_id']) {
                echo '<tr><td width="250"><p>Потребител: <b><a href="user.php?user_id='.$row['user_id'].'">' . $row['username'] . '</a></b><br/>Дата: <b>' . $row['message_date'] . '</b></p></td>
                <td width="350">' . $row['message'] . '</td>';
            }
        }
        echo "</table></form>";

        if (isset($_POST['logout'])) {
            session_destroy();
            header('Location: index.php');
            exit();
        }
    }

    if (!$_SESSION['isLogged']) {
        echo '<div><h3>За да напишете коментар трябва да сте влезнал в системата потребител. Ако нямате регистрация я направете <b><a href="registration.php">тук</a></b> или
        влезте в системата от <a href="login.php">вход</a></h3></div>';
    }
    //
    //var_dump($_SESSION['isLogged']);
    //var_dump($_POST);
    //var_dump($_GET['book_id']);
    //var_dump($_SESSION['user_id']);

}

?>

<?php
include dirname(__FILE__) . '/includes/footer.php';
?>