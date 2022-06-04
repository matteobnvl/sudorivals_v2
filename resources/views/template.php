<?php
$current = getCurrentRoute();
$title = (isset($current['name'])) ? ' - ' . ucfirst($current['name']) : '';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Site web<?= $title ?></title>
    <link rel="icon" href="public/images/favicon.png">
    <link rel="stylesheet" type="text/css" href="resources/css/app.css">
</head>
<body>

<?php require_once __BASEPATH__ . '/resources/views/common/header.php' ?>

<main>

    <?php require_once __BASEPATH__ . '/resources/views/' . $page . '.php' ?>

</main>

<?php require_once __BASEPATH__ . '/resources/views/common/footer.php' ?>

<script type="text/javascript" src="resources/js/app.js"></script>
</body>
</html>