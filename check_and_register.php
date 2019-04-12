<?php
    session_start();

    if(!$_POST["part"]){
        $home_page = "first_page.php";
        header("Location: ".$home_page);
    }
?>

<html>
<link rel="stylesheet" type="text/css" href="style.css" />
<link rel="shortcut icon" href="bus.ico"/>
<body>

<?php
    $nome = $_SESSION['email'];

    $partenza = $_POST["part"];
    if($partenza == "other")
        $partenza = $_POST["part2"];
    $destinazione = $_POST["dest"];
    if($destinazione == "other")
        $destinazione = $_POST["dest2"];
    $npasseggeri = $_POST["npass"];

    $dbhost = 'localhost';
    $username = 'root';
    $password = '';

    $conn = mysqli_connect($dbhost, $username, $password, 'demo_db');
    if(mysqli_connect_errno()){
        echo "Connessione fallita: ".mysqli_connect_error();
        exit();
    }

    $ris = mysqli_query($conn, "SELECT time FROM users WHERE User = '$nome'");
    $riga = mysqli_fetch_array($ris, MYSQLI_BOTH);

    $currentTime = strtotime(date('Y-m-d H:i:s'));
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
    echo "<div class='up_title'><div class='title'><h1>Prenotazione viaggio</h1></div></div>
        <img src='bus.jpg' class='image_bus'>";

        echo "<div class='split_left'><div class='bad_text'>";

        echo "Prenotazione da parte di $nome: da $partenza a $destinazione con $npasseggeri passeggero/i";
    
    mysqli_query($conn, "UPDATE users SET time = CURRENT_TIME WHERE User = '$nome'");

    function checkn($partenza, $destinazione, $nome, $npasseggeri, $conn){

        $ris = mysqli_query($conn, "SELECT User, Indirizzo_I, Indirizzo_F
            FROM users WHERE (NOT (Indirizzo_F <= '$partenza')
            AND NOT (Indirizzo_I >= '$destinazione')) AND User != '$nome'");

        if(mysqli_num_rows($ris) == 0){
            return true;
        }
        else{
            $indirizzi = array();

            for($i=0;$i<mysqli_num_rows($ris);$i++){
                $riga = mysqli_fetch_array($ris, MYSQLI_BOTH);
                array_push($indirizzi, $riga["Indirizzo_I"], $riga["Indirizzo_F"]);
            }

            $indirizzi = array_unique($indirizzi);
            sort($indirizzi);

            for($j=0;$j<count($indirizzi)-1;$j++){
                $ris = mysqli_query($conn, "SELECT User, Indirizzo_I, Indirizzo_F, N_passeggeri
                    FROM users WHERE (NOT (Indirizzo_F <= '$partenza')
                    AND NOT (Indirizzo_I >= '$destinazione')) AND User != '$nome'");
                $n = $npasseggeri;
                for($i=0;$i<mysqli_num_rows($ris);$i++){
                    $riga = mysqli_fetch_array($ris, MYSQLI_BOTH);
                    if(($riga["Indirizzo_I"] < $indirizzi[$j+1])
                        && ($riga["Indirizzo_F"] > $indirizzi[$j])){
                        $n+=$riga["N_passeggeri"];
                    }
                }
                if($n>$_SESSION["MAXP"]) return false;
            }
        }
        return true;
    }

    if(checkn($partenza, $destinazione, $nome, $npasseggeri, $conn)){
        $ris = mysqli_query($conn, "UPDATE users SET Indirizzo_I = '$partenza', Indirizzo_F = 
            '$destinazione', N_passeggeri = '$npasseggeri' WHERE User = '$nome'");
        echo "<br><br>Prenotazione avvenuta con successo<br><br>";
        echo "</div></div>";
        ?>

        <div class="split_right">
        <div class="btn-group">

        <form method="post" action="personal_page.php">
            <button type="submit" name="submit" class="button">Personal page</button>
        </form>

        </div></div>

        <?php
    }
    else{
        echo "<br><br>Prenotazione non completata: il bus è pieno<br>
            Provare con meno passeggeri o cambiare tratta<br><br>";
        echo "</div></div>";
        ?>

        <div class="split_right">
        <div class="btn-group">

        <form method="post" action="personal_page.php">
            <button type="submit" name="submit" class="button">Personal page</button>
        </form>

        <form method="post" action="new_trip.php">
            <button type="submit" name="submit" class="button">Cambia prenotazione</button>
        </form>

        <form method="post" action="first_page.php">
            <button type="submit" name="submit" class="button">Logout</button>
        </form>

    </div></div>

        <?php
    }

}
?>

<div class="foot">
    <div class="foot_text">&copy Copyright Alessandro Napoletano 2018</div>
</div>

</body>
</html>