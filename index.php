<html>
<link rel="stylesheet" type="text/css" href="style.css" />
<link rel="shortcut icon" href="bus.ico"/>
<body>


<div class="up_title"><div class="title">
<h1>Tragitto del bus</h1>
</div></div>

<img src="bus.jpg" class="image_bus">

<div class="split_left">

<?php
    session_start();
    if($_SERVER['SERVER_PORT'] !== 443 &&
        (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off')) {
        header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        exit;
    }

    session_unset();
    session_destroy();
    
    $indirizzi=array();
    $dbhost = 'localhost';
    $username = 'root';
    $password = '';
    
    $conn = mysqli_connect($dbhost, $username, $password, 'demo_db');
    if(mysqli_connect_errno()){
        echo "Connessione fallita: ".mysqli_connect_error();
        exit();
    }

    if($ris = mysqli_query($conn, "SELECT User, Indirizzo_I, Indirizzo_F, N_passeggeri FROM users")){
        $j=0;
        for($i=0;$i<mysqli_num_rows($ris);$i++){
            $riga = mysqli_fetch_array($ris, MYSQLI_BOTH);
            if($riga["N_passeggeri"] != 0)
                array_push($indirizzi, $riga["Indirizzo_I"], $riga["Indirizzo_F"]);
        }
    }
    ?>

    <div class="text_bus">

    <?php

    if(mysqli_num_rows($ris) == 0){
        echo "Ancora nessun viaggio pretonato: tutti i posti sono liberi!<br>";
    }

    $indirizzi = array_unique($indirizzi);
    sort($indirizzi);

    echo "<table id='bus_table'>";

    echo "<tr>
        <th>Partenza</th>
        <th>Destinazione</th>
        <th>Posti prenotati</th></tr>";

    for($i=0;$i<count($indirizzi);$i++){
        if($i+1!=count($indirizzi)){
            echo "<tr><td>".$indirizzi[$i]."</td><td>".$indirizzi[$i+1]."</td>";
            $ris = mysqli_query($conn, "SELECT User, N_passeggeri FROM users 
                WHERE ((Indirizzo_I = '$indirizzi[$i]') OR
                ('$indirizzi[$i]' BETWEEN Indirizzo_I AND Indirizzo_F)
                AND Indirizzo_F != '$indirizzi[$i]')");
            $n = 0;
            for($j=0;$j<mysqli_num_rows($ris);$j++){
                $riga = mysqli_fetch_array($ris, MYSQLI_BOTH);
                if($riga["N_passeggeri"] != 0)
                    $n+=$riga["N_passeggeri"];
            }
            if($j==0)
                echo "<td>Bus vuoto</td>";
            if($n!=0) echo "<td>".$n."</td>";
            mysqli_free_result($ris);
            echo "</tr>";
        }
    }

    echo "</table>";

    mysqli_close($conn);
?>
    </div>

</div>
<div class="split_right">
<div class="btn-group">
<form action="login.php">
    <input type="submit" value="Login" class="button">
</form>
<form action="registration.html">
    <input type="submit" value="Registrati" class="button">
</form>
</div>
</div>

<div class="foot">
    <div class="foot_text">&copy Copyright Alessandro Napoletano 2018</div>
</div>
</body>
</html>