<div>
    <h2>Mes informations</h2>
    <p>Nom : <?= $_SESSION['firstname'] ?></p>
    <p>Prénom : <?= $_SESSION['lastname'] ?></p>
    <p>Email : <?= $_SESSION['email'] ?></p>
    <p>Role : <?= $_SESSION['nom_role'] ?></p>
    <a href="<?= route('Modifier') ?>?user=<?= $_SESSION['id'] ?>">Modifier mes informations</a>
</div>