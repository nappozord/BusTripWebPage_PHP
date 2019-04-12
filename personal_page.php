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

    $indirizzi = array();
    $dbhost = 'localhost';
    $username = 'root';
    $password = '';
    $flag = 0;

    if(isset($_SESSION["email"])){
        $nome = $_SESSION["email"];
        $pass = $_SESSION["pass"];
    }
    else{
        
        $nome = $_POST["email"];
        $pass = $_POST["pass"]; 
        $_SESSION["email"] = $nome;
        $_SESSION["pass"] = $pass;
    }

    $_SESSION["MAXP"] = 4;

    $conn = mysqli_connect($dbhost, $username, $password, 'demo_db');
    if(mysqli_connect_errno()){
        echo "Connessione fallita: ".mysqli_connect_error();
        exit();
    }

    if($ris = mysqli_query($conn, "SELECT Password FROM users WHERE User = '$nome'")){
        $riga = mysqli_fetch_array($ris, MYSQLI_BOTH);
        if($riga["Password"] != $pass) $flag = 1;
    }

    if(isset($_SESSION["log"]) && $_SESSION["log"] == 1){
        mysqli_query($conn, "UPDATE users SET time = CURRENT_TIME WHERE User = '$nome'");
        unset($_SESSION["log"]);
    }

    if($flag == 1){
        echo '<div class="up_title"><div class="title"><h1>Accesso negato</h1></div></div>
            <img src="bus.jpg" class="image_bus">';

        session_unset();
        session_destroy();

        ?>
        <div class="split_left">
        <div class="bad_text">
            Email o password errate, riprovare
        </div></div>
        <div class="split_right">
        <div class="btn-group">

        <form action="login.php">
            <input type="submit" value="Login" class="button">
        </form>

        <form action="registration.html">
            <input type="submit" value="Registrati" class="button">
        </form>

        <form method="post" action="first_page.php">
            <button type="submit" name="submit" class="button">Home Page</button>
        </form>
        </div></div>

        <?php
    }
    else{

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


    mysqli_query($conn, "UPDATE users SET time = CURRENT_TIME WHERE User = '$nome'");



    echo '<div class="up_title"><div class="title"><h1>Benvenuto '.$nome.'</h1></div></div>
        <img src="bus.jpg" class="image_bus">';


    if($ris = mysqli_query($conn, "SELECT User, Indirizzo_I, Indirizzo_F, N_passeggeri FROM users")){
        $j=0;
        for($i=0;$i<mysqli_num_rows($ris);$i++){
            $riga = mysqli_fetch_array($ris, MYSQLI_BOTH);
            if($riga["N_passeggeri"] != 0)
                array_push($indirizzi, $riga["Indirizzo_I"], $riga["Indirizzo_F"]);
                if($riga["User"] == $nome){
                    $user_I = $riga["Indirizzo_I"];
                    $user_F = $riga["Indirizzo_F"];
                }
        }
        mysqli_free_result($ris);
    }
   ?>
    <div class="split_left">

    <div class="text_bus">

    <?php

    $indirizzi = array_unique($indirizzi);
    sort($indirizzi);

    echo "<table id='bus_table'>";

    echo "<tr>
        <th>Partenza</th>
        <th>Destinazione</th>
        <th>Passeggeri</th>
        <th>Posti prenotati</th></tr>";

    for($i=0;$i<count($indirizzi);$i++){
        if($i+1!=count($indirizzi)){
            if($indirizzi[$i] == $user_I){
                echo "<td><span style='color:b30000;'>".$indirizzi[$i]."</span></td>";
            }
            else echo "<td>".$indirizzi[$i]."</td>";
            if($indirizzi[$i+1] == $user_F){
                echo "<td><span style='color:b30000;'>".$indirizzi[$i+1]."</span></td>";
            }
            else echo "<td>".$indirizzi[$i+1]."</td>";
            $ris = mysqli_query($conn, "SELECT User, N_passeggeri FROM users 
                WHERE ((Indirizzo_I = '$indirizzi[$i]') OR
                ('$indirizzi[$i]' BETWEEN Indirizzo_I AND Indirizzo_F)
                AND Indirizzo_F != '$indirizzi[$i]')");
            for($j=0;$j<mysqli_num_rows($ris);$j++){
                $riga = mysqli_fetch_array($ris, MYSQLI_BOTH);
                if($riga["N_passeggeri"] != 0)
                    echo "<td>".$riga["User"]."</td><td>".$riga["N_passeggeri"]."</td>";
                if($j+1 != mysqli_num_rows($ris)){
                    echo "</tr><tr><td></td><td></td>";
                }
            }
            if($j==0)
                echo "<td>Bus vuoto</td><td>0</td>";
            mysqli_free_result($ris);
            echo "</tr>";
        }
    }

    echo "</table>";

    mysqli_close($conn);
    if($user_I == "")
        echo "<div class='bad_text'>Non hai ancora prenotato nessun posto, che aspetti?<br><br></div>"

?>
    </div>
</div>
<div class="split_right">
<div class="btn-group">

<form method="post" action="new_trip.php">
    <button type="submit" name="submit" class="button">Prenota viaggio</button>
</form>

<script>
    function are_you_sure(){
        if(confirm("Sicuro di voler cancellare il viaggio?")){
            return true;
        }
        return false;
    }
</script>

<form method="post" action="cancel_trip.php" onsubmit="return are_you_sure();">
    <button type="submit" name="submit" class="button">Cancella viaggio</button>
</form>

<form method="post" action="first_page.php">
    <button type="submit" name="submit" class="button">Logout</button>
</form>

</div></div>

<?php
}}
?>

<div class="foot">
    <div class="foot_text">&copy Copyright Alessandro Napoletano 2018</div>
</div>

</body>
</html>