<p>Bienvenue sur l'accueil</p>
<p>La liste des utilisateurs est :</p>
<ul>
    <?php foreach ($users as $user) { ?>
        <li><?= $user['firstname'] ?> <?= $user['lastname'] ?></li>
    <?php } ?>
</ul>