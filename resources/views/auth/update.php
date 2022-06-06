<?php
if ($checker !== null) { ?>
    <p style="color: red;"><?= $checker ?></p>

<?php }
$user = $user[0];

?>


<div>

    <form method="post">
        <input type="hidden" name="check" value="ok">
        <input type="hidden" name="id" value="<?= $user['id'] ?>" >
        <input type="hidden" name="password" value="<?= $user['password']?>">
        <?php
        foreach($user as $key => $value){
            if($key == 'firstname' ||
                $key == 'lastname' ||
                $key == 'email'){
                ?>
                <div>
                    <label for="<?= $key ?>"><?= $key ?></label>
                    <input <?= ($key == 'email')? 'type="email"':'type="text"'?> name="<?= $key ?>" value="<?= $value ?>" id="<?= $key ?>">
                </div>
                <?php
            } 
        }
        ?>
        <label for="">Role</label>
        <select name="id_role" id="<?= $key ?>">
            <?php
                foreach($role as $roleUser){
                    ?>
                    <option value="<?= $roleUser['id_roles'] ?>" <?= ($user['id_role'] === $roleUser['id_roles']) ? 'selected' : '' ; ?>><?= $roleUser['nom_role'] ?></option>
                    <?php
                }
            ?>
        </select>
        <input type="submit" value="Sauvegarder">
    </form>


</div>