<?php
session_start();

$bdd = new PDO('mysql:host=localhost;dbname=espace_membre', 'root', 'root');

if (isset($_SESSION['id']))
{
    $requser = $bdd->prepare("SELECT * FROM membres WHERE id = ?");
    $requser->execute(array($_SESSION['id']));
    $user = $requser->fetch();
 
    if (isset($_POST['newpseudo']) AND !empty($_POST['newpseudo']) AND $_POST['newpseudo'] != $user['pseudo'])
    {
        $newpseudo = htmlspecialchars($_POST['newpseudo']);
        $insertpseudo = $bdd->prepare("UPDATE membres SET pseudo = ? WHERE id = ?");
        $insertpseudo->execute(array($newpseudo, $_SESSION['id']));
        header("Location: profil.php?id=".$_SESSION['id']);
    }

    if (isset($_POST['newmail']) AND !empty($_POST['newmail']) AND $_POST['newmail'] != $user['mail'])
    {
        $newmail = htmlspecialchars($_POST['newmail']);
        $insertmail = $bdd->prepare("UPDATE membres SET mail = ? WHERE id = ?");
        $insertmail->execute(array($newmail, $_SESSION['id']));
        header("Location: profil.php?id=".$_SESSION['id']);
    }

    if (isset($_POST['newmdp1']) AND !empty($_POST['newmdp1']) AND isset($_POST['newmdp2']) AND !empty($_POST['newmdp2']))
    {
        $mdp1 = sha1($_POST['newmdp1']);
        $mdp2 = sha1($_POST['newmdp2']);

        if ($mdp1 == $mdp2)
        {
            $insertmdp = $bdd->prepare("UPDATE membres SET motdepasse = ? WHERE  id = ?");
            $insertmdp->execute(array($mdp1, $_SESSION['id']));
            header("Location: profil.php?id=".$_SESSION['id']);
        }
        else
        {
            $msg = "Vos deux mots de passes ne correspondent pas !";
        }
    }

    if (isset($_POST['newpseudo']) AND $_POST['newpseudo'] == $user['pseudo'])
    {
        header("Location: profil.php?id=".$_SESSION['id']);
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
        <h2>Edition de mon profil</h2><br/><br/>
        <div align="left">
        <form method="POST" action="">
            <label for="">Pseudo :</label>
            <input type="text" name="newpseudo" placeholder="Pseudo" value="<?php echo $user['pseudo']; ?>"><br/><br/>
            <label for="">E-mail :</label>
            <input type="email" name="newmail" placeholder="Mail" value="<?php echo $user['mail']; ?>"><br/><br/>
            <label for="">Mot de passe :</label>
            <input type="password" name="newmdp1" placeholder="Mot de passe"><br/><br/>
            <label for="">Reconfirmer le mot de passe :</label>
            <input type="password" name="newmdp2" placeholder="Confirmation du mot de passe"><br/><br/>
            <input type="submit" value="Mettre à jour mon profil !">
        </form>
        <?php
            if (isset($msg))
            {
                echo $msg;
            }
        ?>
        </div>
    </div>
</body>
</html>

<?php
}
else
{
header("Location: connexion.php");
}
?>