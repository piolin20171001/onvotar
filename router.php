<?php
// router.php
if (strpos($_SERVER['SCRIPT_FILENAME'], '.css') !== false) {
    header('Content-Type: text/css');
}
readfile($_SERVER['SCRIPT_FILENAME']);
?>

