<?php
include_once('fonctions_mail.php');
session_start();
$bdd = new PDO('mysql:host=localhost;dbname=espace_membre', 'root', 'root');

if (isset($_GET['section']))
{
    $section = htmlspecialchars($_GET['section']);
}
else
{
    $section = "";
}

if (isset($_POST['recup_submit'], $_POST['recup_mail']))
{
    if (!empty($_POST['recup_mail']))
    {
        $recup_mail = htmlspecialchars($_POST['recup_mail']);
        if (filter_var($recup_mail, FILTER_VALIDATE_EMAIL))
        {
            $mailexist = $bdd->prepare('SELECT id, pseudo FROM membres WHERE mail = ?');
            $mailexist->execute(array($recup_mail));
            $mailexist_count = $mailexist->rowCount();
            if ($mailexist_count == 1)
            {
                $pseudo = $mailexist->fetch();
                $pseudo = $pseudo['pseudo'];
                $_SESSION['recup_mail'] = $recup_mail;
                $recup_code = "";
                
                for ($i=0; $i < 8 ; $i++)
                { 
                    $recup_code .= mt_rand(0, 9);
                }

                $mail_recup_exist = $bdd->prepare("SELECT id FROM recuperation WHERE mail = ?");
                $mail_recup_exist->execute(array($recup_mail));
                $mail_recup_exist = $mail_recup_exist->rowCount();

                if ($mail_recup_exist == 1)
                {
                    $recup_insert = $bdd->prepare("UPDATE recuperation SET code = ? WHERE mail = ?");
                    $recup_insert->execute(array($recup_code, $recup_mail));
                }
                else
                {
                    $recup_insert = $bdd->prepare("INSERT INTO recuperation(mail, code, confirme) VALUES (?, ?, ?)");
                    $recup_insert->execute(array($recup_mail, $recup_code, 0));
                }

                $subject = 'Recuperation de mot de passe';
                $exp = 'elietordjman98@gmail.com';
                // Pour $exp modifier par la suite par $recup_mail
                $message = '
                <html>
                    <body>
                        <div align="center">
                        Bonjour <b>'.$pseudo.'</b><br/>
                        Voici votre code de récupération : <b>'.$recup_code.'</b><br/><br/>
                        Puis cliquer <a href="http://localhost:8888/Espace_membres/change_mdp.php?section=code">ici</a>
                        </div>
                    </body>
                </html>
                ';

                sendmail($subject , $message, $exp);

            }
            else
            {
                $error = "Cette adresse mail n'est pas enregistrée";
            }
        }
        else
        {
            $error = "Adresse mail invalide";
        }
    }
    else
    {
        $error = "Veuillez entrer votre adresse mail !";
    }
}


if (isset($_POST['verif_submit'], $_POST['verif_code']))
{
    if (!empty($_POST['verif_code']))
    {
        $verif_code = htmlspecialchars($_POST['verif_code']);
        $verif_req = $bdd->prepare("SELECT id FROM recuperation WHERE mail = ? AND code = ?");
        $verif_req->execute(array($_SESSION['recup_mail'], $verif_code));
        $verif_req = $verif_req->rowCount();

        if ($verif_req == 1)
        {
            $up_req = $bdd->prepare("UPDATE recuperation SET confirme = 1 WHERE mail = ?");
            $up_req->execute(array($_SESSION['recup_mail']));
            header("Location: http://localhost:8888/Espace_membres/change_mdp.php?section=changemdp");
        }
        else
        {
            $error = "Code invalide";
        }

    }
    else
    {
        $error = "Veuillez entrer votre code de confirmation";
    }
}


if (isset($_POST['change_submit']))
{
    if (isset($_POST['change_mdp'], $_POST['change_mdpc']))
    {
        $verif_confirme = $bdd->prepare("SELECT confirme FROM recuperation WHERE mail = ?");
        $verif_confirme->execute(array($_SESSION['recup_mail']));
        $verif_confirme = $verif_confirme->fetch();
        $verif_confirme = $verif_confirme['confirme'];
        
        if ($verif_confirme == 1)
        {
            $mdp = htmlspecialchars($_POST['change_mdp']);
            $mdpc = htmlspecialchars($_POST['change_mdpc']);

            if (!empty($mdp) AND !empty($mdpc))
            {
                if ($mdp == $mdpc)
                {
                    $mdp = sha1($mdp);

                    $ins_mdp = $bdd->prepare("UPDATE membres SET motdepasse = ? WHERE mail = ?");
                    $ins_mdp->execute(array($mdp, $_SESSION['recup_mail']));
                    $del_req = $bdd->prepare("DELETE FROM recuperation WHERE mail = ?");
                    $del_req->execute(array($_SESSION['recup_mail']));
                    header("Location: http://localhost:8888/Espace_membres/connexion.php");
                }
                else
                {
                    $error = "Vos mots de passes ne correspondent pas";            
                }
            }
            else
            {
                $error = "Veuillez remplir tous les champs";
            }
        }
        else
        {
            $error = "Veuillez valider votre code de vérification qui vous a été envoyé par mail";
        }
        
    }
    else
    {
        $error = "Veuillez remplir tous les champs";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <center><h3>Refaire son mot de passe</h3></center>
    <br/>
    <center>

    <?php if ($section == 'code') { ?>
        Récupération de mot de passe pour <?= $_SESSION['recup_mail'] ?>
        <br/><br/>
        <form action="" method="post">
            <input type="number" name="verif_code" placeholder="Code de vérification"><br/><br/>
            <input type="submit" value="Valider" name="verif_submit">
            <?php  if (isset($error)) echo $error ?>
        </form>
    
    <?php } else if ($section == 'changemdp') { ?>
        Nouveau mot de passe pour <?= $_SESSION['recup_mail'] ?>
        <br/><br/>
        <form action="" method="post">
            <input type="password" name="change_mdp" placeholder="Nouveau mot de passe"><br/><br/>
            <input type="password" name="change_mdpc" placeholder="Confirmation du mot de passe"><br/><br/>
            <input type="submit" value="Valider" name="change_submit">
            <?php  if (isset($error)) echo $error ?>
        </form>

    <?php } else { ?>
        <form action="" method="post">
            <input type="email" name="recup_mail" placeholder="Votre adresse email"><br/><br/>
            <input type="submit" value="Valider" name="recup_submit">
            <?php  if (isset($error)) echo $error ?>
        </form>
    <?php } ?>
    </center>
</body>
</html>