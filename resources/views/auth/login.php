<?php if ($checker !== null) { ?>

    <p style="color: red;"><?= $checker ?></p>

<?php } ?>

<form method="post">
    <input type="hidden" name="check" value="ok">
    <label>
        <span>Email</span>
        <input type="email" name="email">
    </label>
    <label>
        <span>Mot de passe</span>
        <input type="password" name="password">
    </label>
    <button type="reset">Cancel</button>
    <button type="submit">Validate</button>
</form>