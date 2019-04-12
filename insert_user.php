<?php
    if(!isset($_SESSION["email"]) && !$_POST["email"]){
        $home_page = "first_page.php";
        header("Location: ".$home_page);
    }
?>

<html>
<link rel="stylesheet" type="text/css" href="style.css" />
<link rel="shortcut icon" href="bus.ico"/>
<body>

<?php
    $nome = $_POST['email'];
    $pass = $_POST['pass']; 

    $dbhost = 'localhost';
    $username = 'root';
    $password = '';

    $conn = mysqli_connect($dbhost, $username, $password, 'demo_db');
    if(mysqli_connect_errno()){
        echo "Connessione fallita: ".mysqli_connect_error();
        exit();
    }

    $ris = mysqli_query($conn, "SELECT User FROM users WHERE User = '$nome'");

    if(mysqli_num_rows($ris) == 0){
        if($ris = mysqli_query($conn, "INSERT INTO users (ID, User, Password, time) 
            VALUES (NULL, '$nome', '$pass', CURRENT_TIMESTAMP)")){
                ?>
            <div class="up_title"><div class="title">
            <h1>Nuovo utente registrato</h1>
            </div></div>

            <img src="bus.jpg" class="image_bus">

                <?php

            echo "<div class='split_left'><div class='bad_text'>Benvenuto sul bus, ".$nome."</div></div>";

        }
    }
    else{

        ?>
            <div class="up_title"><div class="title">
            <h1>Utente non registrato</h1>
            </div></div>

            <img src="bus.jpg" class="image_bus">

            <div class='split_left'>
                <div class='bad_text'>
                Email già utilizzata
            </div></div>

        <?php

    }
    mysqli_close($conn);
?>

<div class="split_right">
<div class="btn-group">

<form method="post" action="first_page.php">
    <button type="submit" name="submit" class="button">Home page</button>
</form>

</div></div>

<div class="foot">
    <div class="foot_text">&copy Copyright Alessandro Napoletano 2018</div>
</div>

</body>
</html>