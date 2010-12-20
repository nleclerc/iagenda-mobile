======
<?php
header("content-type:text/plain");
include_once 'inc/ioUtil.php';

?>

* <?= isLoggedIn() ?>

* <?= login("","") ?>

* <?= isLoggedIn() ?>

-EOF