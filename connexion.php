<?php
session_start();

$bdd = new PDO('mysql:host=localhost;dbname=espace_membre', 'root', 'root');


if (isset($_POST['formconnexion']))
{
    $mailconnect = htmlspecialchars($_POST['mailconnect']);
    $mdpconnect = sha1($_POST['mdpconnect']);

    if (!empty($mailconnect) AND !empty($mdpconnect))
    {
        $requser = $bdd->prepare("SELECT * FROM membres WHERE mail = ? AND motdepasse = ? AND confirme = 1");
        $requser->execute(array($mailconnect, $mdpconnect));
        $userexist = $requser->rowCount();
        if ($userexist == 1)
        {
            $userinfo = $requser->fetch();
            $_SESSION['id'] = $userinfo['id'];
            $_SESSION['pseudo'] = $userinfo['pseudo'];
            $_SESSION['mail'] = $userinfo['mail'];
            header("Location: profil.php?id=".$_SESSION['id']);
        }
        else
        {
           // $erreur = "Mauvais mail/à confirmer ou mot de passe !";
            ///////////////////////////////////////////////////////////////////////////////////////////        
                    
                    ?>
                                <script>
                                function myFunction() {
                                alert("Mauvais mail/à confirmer ou mot de passe !");
                                }
                                </script>

                    <?php
//////////////////////////////////////////////////////////////////////////////////////////////////////

        }
    }
    else
    {
       // $erreur = "Tous les champs doivent être complétés !";

        ?>
        <script>
        function myFunction() {
        alert("Tous les champs doivent être complétés !");
        }
        </script>

<?php
    }
} 

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>TUTO espace membres</title>
</head>
<body>
    <div align="center">
        <h2>Connexion</h2>
        <br/><br/>
        <form method="POST" action="">
           <input type="email" name="mailconnect" placeholder="E-mail">
           <input type="password" name="mdpconnect" placeholder="Mot de passe">
           <input type="submit" name="formconnexion" value="Se connecter" onclick="myFunction()">
        <!-- RAJOUTER ONCLICK SUR LES BOUTONS -->
        </form>
        
        <?php
            if (isset($erreur))
            {
                echo '<font color="red">'.$erreur.'</font>';
            }
        ?>

    </div>
</body>
</html>