<?php

if($checkDelete !== null){ ?>
    <p><?= $checkDelete ?></p>
<?php }

?>

<p>Bonjour <?= $_SESSION['firstname'] ?></p>
<?php 

echo '<p>Vous êtes connectés en tant que administrateur du site</p>';
echo '<p>Liste des utilisateurs :</p>';
echo '<a href="'.route('CreateUser').'">Ajouter un utilisateur</a>';
foreach ($users as $user) { 
    if($user['affichage'] == 'oui'){
    ?>
    <li>
        <?= $user['firstname'] ?> 
        <?= $user['lastname'] ?> 
        <a href="<?= route('Details')?>?user=<?= $user['id'] ?>">Voir</a> 
        <a href="<?= route('Modifier')?>?user=<?= $user['id'] ?>">Modifer</a>
        <?php if($user['id_role'] != '1'){?><a href="<?= route('Supprimer')?>?user=<?= $user['id'] ?>">Supprimer</a><?php ; } ?>
    </li>
    <?php } }


?>
<a href="<?= route('CreateRole') ?>">Ajouter un rôle</a>
<a href="<?= route('VoirRole') ?>">Voir les rôles</a>