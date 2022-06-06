<?php

if(isset($_GET['user'])){?>
    <form method="POST">
        <p>Voulez-vous vraiment supprimer l'utlisateur <?= $user[0]['firstname'] ?> <?= $user[0]['lastname'] ?> ?</p>
        <input type="submit" name="supprimer" value="oui" id="oui">
        <input type="submit" name="supprimer" value="non" id="non">
    </form>
<?php }
else{
    redirect('Tableau de bord');
}
?>
