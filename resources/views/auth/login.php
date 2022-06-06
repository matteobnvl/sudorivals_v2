<?php if ($checker !== null) { ?>

    <p style="color: red;"><?= $checker ?></p>

<?php } 
$connexion = False;
if(!empty($_GET)){ 
    if($_GET['connexion'] == 'admin'){
        $connexion = True;
    }
}


?>

<div>
    <p>Connexion utilisateur : <form method="get"><input type="submit" name="connexion" value="admin" ></form></p>
    
</div>

<form method="post">
    <input type="hidden" name="check" value="ok">
    <label>
        <span>Email</span>
        <input type="email" name="email" value="<?php if($connexion == True){echo 'admin@framework.fr';} ?>">
    </label>
    <label>
        <span>Mot de passe</span>
        <input type="password" name="password" value="<?php if($connexion == True){echo 'admin';} ?>">
    </label>
    <button type="reset">Cancel</button>
    <button type="submit">Validate</button>
</form>