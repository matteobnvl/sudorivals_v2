<?php if ($checker !== null) { ?>

<p style="color: red;"><?= $checker ?></p>

<?php } ?>

<div>
    <h2>Ajouter un utilisateur</h2>
    <form method="post">
        <input type="hidden" name="check" value="ok">
        <input type="text" name="firstname" placeholder="Nom">
        <input type="text" name="lastname" placeholder="Prénom">
        <input type="email" name="email" placeholder="email">
        <input type="password" name="password" placeholder="mot de passe">
        <input type="password" name="password2" placeholder="répéter mot de passe">
        <select name="id_role">
            <option value="1">Administrateur</option>
            <option value="2" selected>Utilisateur</option>
        </select>
        <input type="submit" value="Créer">
    </form>
</div>