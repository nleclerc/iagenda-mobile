======
<?php
header("content-type:text/plain");
include_once 'inc/ioUtil.php';

session_start();

?>

* <?= isLoggedIn() ?>

* <?php try {echo login("nicoponk","");} catch (Exception $e) {echo $e->getMessage();} ?>

* <?= isLoggedIn() ?>

* <?= session_id() ?>

-EOF