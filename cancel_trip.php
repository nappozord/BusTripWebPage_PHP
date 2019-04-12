<?php
    session_start();

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

    $nome = $_SESSION["email"];

    $dbhost = "localhost";
    $username = "root";
    $password = "";

    $conn = mysqli_connect($dbhost, $username, $password, "demo_db");
    if(mysqli_connect_errno()){
        echo "Connessione fallita: ".mysqli_connect_error();
        exit();
    }

    $ris = mysqli_query($conn, "SELECT time FROM users WHERE User = '$nome'");
    $riga = mysqli_fetch_array($ris, MYSQLI_BOTH);

    $currentTime = strtotime(date("Y-m-d H:i:s"));
    $savedTime  = strtotime($riga["time"]);
    $differenceInSeconds = $currentTime - $savedTime;

    if($differenceInSeconds > 120){
        echo '<div class="up_title"><div class="title"><h1>Accesso negato</h1></div></div>
            <img src="bus.jpg" class="image_bus">';
        echo '<div class="split_left"><div class="bad_text">
            <h1>Sono passati '.$differenceInSeconds.' secondi, devi riloggarti</h1>
            </div></div>';

        ?>
        <div class="split_right">
        <div class="btn-group">

        <form action="login.php">
            <button type="submit" name="submit" class="button">Login</button>
        </form>

        <form method="post" action="first_page.php">
            <button type="submit" name="submit" class="button">Home Page</button>
        </form>
        </div></div>

        <?php
    }

    else{

    echo "<div class='up_title'><div class='title'><h1>Cancellazione viaggio</h1></div></div>
        <img src='bus.jpg' class='image_bus'>";

    mysqli_query($conn, "UPDATE users SET time = CURRENT_TIME WHERE User = '$nome'");

    $ris = mysqli_query($conn, "SELECT Indirizzo_I, Indirizzo_F, N_passeggeri FROM users
        WHERE User = '$nome'");
    $riga = mysqli_fetch_array($ris, MYSQLI_BOTH);

    echo '<div class="split_left">';

    if($riga["N_passeggeri"] == 0){
        echo "<div class='bad_text'>Viaggio non cancellato<br>Nessuna prenotazione effettuata<br></div>";
    }
    else{
        echo "<div class='bad_text'>Prenotazione viaggio da ".$riga["Indirizzo_I"]." a ".$riga["Indirizzo_F"]." cancellata con successo<br></div>";
        $ris = mysqli_query($conn, "UPDATE users SET Indirizzo_I = null, Indirizzo_F = null, 
        N_passeggeri = 0 WHERE User = '$nome'");
    }

    echo "</div>";

?>

<div class="split_right">
<div class="btn-group">

<form method="post" action="personal_page.php">
    <button type="submit" name="submit" class="button">Personal page</button>
</form>

<form method="post" action="new_trip.php">
    <button type="submit" name="submit" class="button">Prenota viaggio</button>
</form>

<form method="post" action="first_page.php">
    <button type="submit" name="submit" class="button">Logout</button>
</form>

</div></div>

<?php } ?>

<div class="foot">
    <div class="foot_text">&copy Copyright Alessandro Napoletano 2018</div>
</div>

</body>
</html>