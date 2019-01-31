<?php 
require(__DIR__.'/../vendor/autoload.php');
$app = (new \fabriziocaldarelli\vuelive\demo\customApp\VueLoader());
?>

<html>

<head>

<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>

<?= $app->getCssContentsAsString() ?>

</head>

<body>

    <?= $app->getHtmlContentsAsString() ?>

</body>

</html>

<?= $app->getJsContentsAsString() ?>
