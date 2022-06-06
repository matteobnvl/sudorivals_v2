<?php if ($checker !== null) { ?>

<p style="color: red;"><?= $checker ?></p>

<?php } ?>

<div>
    <form method="post">
        <input type="text" name="nom_role" placeholder="Nom du role">
        <input type="submit" name="check" value="ok">
    </form>
</div>