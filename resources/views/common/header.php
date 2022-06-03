<header>
    <nav>
        <ul>
            <li>
                <a href="<?= route('Accueil') ?>">Accueil</a>
            </li>
            <li>
                <a href="<?= route('Contact') ?>">Contact</a>
            </li>
            <li>
                <a href="<?= route('A propos') ?>">A propos</a>
            </li>
            <?php if (isset($_SESSION['id'])) { ?>
                <li>
                    <a href="<?= route('Tableau de bord') ?>">Tableau de bord</a>
                </li>
                <li>
                    <a href="<?= route('Deconnexion') ?>">Se déconnecter</a>
                </li>
            <?php } else { ?>
                <li>
                    <a href="<?= route('Connexion') ?>">Se connecter</a>
                </li>
            <?php } ?>
        </ul>
    </nav>
</header>