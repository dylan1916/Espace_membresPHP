<?php
include_once('fonctions_mail.php');

$bdd = new PDO('mysql:host=localhost;dbname=espace_membre', 'root', 'root');

if (isset($_POST['forminscription']))
{
    $pseudo = htmlspecialchars($_POST['pseudo']);
    $mail = htmlspecialchars($_POST['mail']);
    $mail2 = htmlspecialchars($_POST['mail2']);
    $mdp = sha1($_POST['mdp']);
    $mdp2 = sha1($_POST['mdp2']);

    if (!empty($_POST['pseudo']) AND !empty($_POST['pseudo']) AND !empty($_POST['mail']) AND !empty($_POST['mail2']) AND !empty($_POST['mdp']) AND !empty($_POST['mdp2']))
    { 
       $pseudolength = strlen($pseudo);
        if ($pseudolength <= 255)
        {
            if ($mail == $mail2)
            {
                if (filter_var($mail, FILTER_VALIDATE_EMAIL))
                {
                    $reqmail = $bdd->prepare("SELECT * FROM membres WHERE mail = ?");
                    $reqmail->execute(array($mail));
                    $mailexist =  $reqmail->rowCount();
                    if ($mailexist == 0)
                    {
                        if ($mdp == $mdp2)
                        {         
                            $longueurKey = 15;
                            $key = "";
                            for ($i = 1; $i < $longueurKey; $i++)
                            {
                                $key .= mt_rand(0, 9);
                            }

                            $insertmbr = $bdd->prepare("INSERT INTO membres(pseudo, mail, motdepasse, confirmkey, confirme) VALUES (?, ?, ?, ?, ?)");
                            $insertmbr->execute(array($pseudo, $mail, $mdp, $key, 0));
                            
                            $subject = 'Confirmation de compte';
                            $exp = 'elietordjman98@gmail.com';
                            $message = '
                            <html>
                                <body>
                                    <div align="center">
                                    <a href="http://localhost:8888/Espace_membres/confirmation.php?pseudo='.urlencode($pseudo).'&key='.$key.'">Confirmez votre compte !</a>
                                    </div>
                                </body>
                            </html>
                            ';

                            sendmail($subject , $message, $exp);

                            $erreur = "Votre compte à bien été crée ! <a href=\"connexion.php\">Me connecter</a>";
                            // FAIRE ICI LE LOCATION POUR REDIRIGER SUR UNE PAGE QUAND LE COMPTE A ETE CREE
                        }
                        else
                        {
                            ?>
                                <script>
                                function myFunction() {
                                alert("Vos mots de passes ne correspondent pas !");
                                }
                                </script>
                            <?php
                        }
                    }
                    else
                    {
                        ?>
                            <script>
                            function myFunction() {
                            alert("Adresse mail déjà utilisée !");
                            }
                            </script>
                        <?php
                    }
                }
                else
                {
                    ?>
                        <script>
                        function myFunction() {
                        alert("Votre adresse mail n'est pas valide !");
                        }
                        </script>
                    <?php
                }
            }
            else
            {
                ?>
                    <script>
                    function myFunction() {
                    alert("Vos adresses mail ne correspondent pas !");
                    }
                    </script>
                <?php
            }
        }
        else
        {
            ?>
                <script>
                function myFunction() {
                alert("Votre pseudo ne doit pas dépasser 255 caractères !");
                }
                </script>
            <?php
        }
    }
    else
    {
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
        <h2>Inscription</h2>
        <br/><br/>
        <form method="POST" action="">
            <label for="pseudo">Pseudo :</label>
            <input type="text" placeholder="Votre pseudo" id="pseudo" name="pseudo" value="<?php if(isset($pseudo)) { echo "$pseudo"; } ?>">
            <br/>
            <br/> 
            <label for="mail">Mail :</label>
            <input type="email" placeholder="Votre mail" id="mail" name="mail" value="<?php if(isset($mail)) { echo "$mail"; } ?>">
            <br/>
            <br/> 
            <label for="mail2">Confirmation du mail :</label>
            <input type="email" placeholder="Confirmer votre mail" id="mail2" name="mail2" value="<?php if(isset($mail2)) { echo "$mail2"; } ?>">
            <br/>
            <br/> 
            <label for="mdp">Mot de passe :</label>
            <input type="password" placeholder="Votre mot de passe" id="mdp" name="mdp">
            <br/>
            <br/> 
            <label for="mdp2">Confirmation du mot de passe :</label>
            <input type="password" placeholder="Confirmer votre mot de passe" id="mdp2" name="mdp2">
            <br/>
            <br/> 
            <input type="submit" name="forminscription" value="Je m'inscris" onclick="myFunction()">
        </form>
    </div>
</body>
</html>