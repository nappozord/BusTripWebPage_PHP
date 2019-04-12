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

    echo "<div class='up_title'><div class='title'><h1>Prenota il tuo viaggio!</h1></div></div>
        <img src='bus.jpg' class='image_bus'>";
    mysqli_query($conn, "UPDATE users SET time = CURRENT_TIME WHERE User = '$nome'");

    $ris = mysqli_query($conn, "SELECT N_passeggeri FROM users WHERE User = '$nome'");
    $riga = mysqli_fetch_array($ris, MYSQLI_BOTH);

    echo '<div class="split_left">';

    if($riga["N_passeggeri"] != 0){
        echo "<div class='bad_text'>Prenotazione già effettuata<br>Se si vuole cambiare il percorso di viaggio
            è necessario eliminare la precedente prenotazione<br><br></div></div>";
    ?>

    <script>
    function are_you_sure(){
        if(confirm("Sicuro di voler cancellare il viaggio?")){
            return true;
        }
        return false;
    }
    </script>

        <div class="split_right">
        <div class="btn-group">


        <form method="post" action="personal_page.php">
            <button type="submit" name="submit" class="button">Pagina personale</button>
        </form>

        <form method="post" action="cancel_trip.php" onsubmit="return are_you_sure();">
            <button type="submit" name="submit" class="button">Cancella viaggio</button>
        </form>

        </div></div>

    <?php
    }
    else{

        $indirizzi=array();

        if($ris = mysqli_query($conn, "SELECT ID, User, Password, Indirizzo_I, Indirizzo_F, N_passeggeri FROM users")){
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

        $indirizzi = array_unique($indirizzi);

        sort($indirizzi);

        echo '<div class="new_trip_text">';

        echo "Scegliere gli indirizzi di partenza e destinazione tra quelli già proposti 
            dagli utenti o inserirne uno nuovo, specificare per quante persone<br><br></div>";

        ?>

        <script>

            function altro_p(){
                sel.value=def.value;
            }

            function altro_d(){
                sel2.value=def2.value;
            }

            function altro_p_select(){
                part.value="";
            }

            function altro_d_select(){
                dest.value="";
            }

            function check(){
                if(part.value!=""){
                    if(dest.value!=""){
                        var n = part.value.localeCompare(dest.value);
                        if(n == -1)
                            return true;
                        else{
                            alert("La partenza non può avvenire prima o essere uguale alla destinazione");
                            return false;
                        } 
                    }
                    else{
                        if(sel2.value == "other"){
                            alert("Selezionare un indirizzo di destinazione");
                            return false;
                        }
                        var n = part.value.localeCompare(sel2.value);
                        if(n == -1)
                                return true;
                        else{
                            alert("La partenza non può avvenire prima o essere uguale alla destinazione");
                            return false;
                        }
                    }
                }
                else{
                    if(sel.value == "other"){
                        alert("Selezionare un indirizzo di partenza");
                        return false;
                    }
                    if(dest.value!=""){
                        var n = sel.value.localeCompare(dest.value);
                        if(n == -1)
                            return true;
                        else{
                            alert("La partenza non può avvenire prima o essere uguale alla destinazione");
                            return false;
                        }
                    }
                    else{
                        if(sel2.value == "other"){
                            alert("Selezionare un indirizzo di destinazione");
                            return false;
                        }
                        var n = sel.value.localeCompare(sel2.value);
                        if(n == -1)
                            return true;
                        else{
                            alert("La partenza non può avvenire prima o essere uguale alla destinazione");
                            return false;
                        }
                    }
                }
            }

        </script>

        <form method="post" action="check_and_register.php" onsubmit="return check();">
        PARTENZA<br><br>
        <select id="sel" name="part" onchange="altro_p_select()">
            <option value="other" id="def">Altro</option>
            <?php for($i=0;$i<count($indirizzi);$i++): ;?>
            <option value="<?php echo $indirizzi[$i]; ?>"><?php echo $indirizzi[$i];?></option>
            <?php endfor; ?>
        </select>
        
        <?php

        echo "  OR  ";
        echo "<input type='text' id='part' name='part2' onkeypress='altro_p()' autocomplete='off'><br><br>";
        echo "DESTINAZIONE<br><br>";

        ?>

        <select id="sel2" name="dest" onchange="altro_d_select()">
            <option value="other" id="def2">Altro</option>
            <?php for($i=0;$i<count($indirizzi);$i++): ;?>
            <option value="<?php echo $indirizzi[$i]; ?>"><?php echo $indirizzi[$i];?></option>
            <?php endfor; ?>
        </select>
        
        <?php

        echo "  OR  ";
        echo "<input type='text' id='dest' name='dest2' onkeypress='altro_d()' autocomplete='off'><br><br>";
        echo " N. passeggeri<br><br>";
        echo "<input type='number' min='1' max='4' id='npass' name='npass' value='1'><br><br>";

        ?>

        <div class="button_group">
        <button type="submit" class="button3">Prenota</button>
        <button type="reset" class="button3">Reset</button>
        </div>
        </div>
        </form>

        <div class="split_right">
        <div class="btn-group">

        <form method="post" action="personal_page.php">
            <button type="submit" name="submit" class="button">Personal Page</button>
        </form>

        </div></div>

        <?php
    }
    }
?>
</div>

<div class="foot">
    <div class="foot_text">&copy Copyright Alessandro Napoletano 2018</div>

</body>
</html>