<?php

$bdd = new PDO('mysql:host=localhost;dbname=espace_membre', 'root', 'root');

if (isset($_GET['pseudo'], $_GET['key']) AND !empty($_GET['pseudo']) AND !empty($_GET['key']))
{
    $pseudo = htmlspecialchars(urldecode($_GET['pseudo']));
    $key = intval($_GET['key']);

    $requser = $bdd->prepare("SELECT * FROM membres WHERE pseudo = ? AND confirmkey = ?");
    $requser->execute(array($pseudo, $key));
    $userexist = $requser->rowCount();

    if ($userexist == 1)
    {
        $user = $requser->fetch();
        if ($user['confirme'] == 0)
        {
            $updateuser = $bdd->prepare("UPDATE membres SET confirme = 1 WHERE pseudo = ? AND confirmkey = ?");
            $updateuser->execute(array($pseudo, $key));
            echo "Votre compte à bien été confirmé !";
        }
        else
        {
            echo "Votre compte à déjà été confirmé !";
        }
    }
    else
    {
        echo "L'utilisateur n'existe pas !";
    }
}

?>