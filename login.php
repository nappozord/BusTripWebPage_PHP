<?php
    session_start();
?>

<html>
<link rel="stylesheet" type="text/css" href="style.css" />
<link rel="shortcut icon" href="bus.ico"/>
<body>


<div class="up_title"><div class="title">
<h1>Esegui il login</h1>
</div></div>

<img src="bus.jpg" class="image_bus">

<?php
    $dbhost = 'localhost';
    $username = 'root';
    $password = '';

    $conn = mysqli_connect($dbhost, $username, $password, 'demo_db');
    if(mysqli_connect_errno()){
        echo "Connessione fallita: ".mysqli_connect_error();
        exit();
    }

?>

    <script>
        function check(){
            var email_regex = /\S+@\S+\.\S+/;
            var pass_regex = /((([a-z])([A-Z]|\d))|(([A-Z]|\d)([a-z])))|((([a-z])([\D\W]+)([A-Z]|\d))|(([A-Z]|\d)([\D\W]+)([a-z])))/;
            if(!email_regex.test(email.value) || !pass_regex.test(pass.value)){
                alert("Email o password non valida! (Assicurati che la password contenga un carattere minuscolo e un carattere maiuscolo o numerico)");
                return false;
            }
            <?php $_SESSION["log"] = 1 ?>
        }
    </script>

    <div class="split_left">

    <div class="wellcome">Benvenuto</div>

    <div class="register">
    <form method="post" action="personal_page.php" onsubmit="return check();">
        <input type="text" id="email" name="email" class="input" placeholder="  Email"><br><br>
        <input type="password" id="pass" name="pass" class="input" placeholder="  Password"><br><br>
        <button type="submit" name="submit" class="button2">Login</button>
    </form>
    </div>

    </div>

    <div class="split_right">
<div class="btn-group">

<form method="post" action="first_page.php">
    <button type="submit" name="submit" class="button">Home Page</button>
</form>

</div></div>

</body>
<div class="foot">
    <div class="foot_text">&copy Copyright Alessandro Napoletano 2018</div>
</div>

</body>
</html>