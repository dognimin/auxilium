<?php
session_start();
session_unset();
session_destroy();
$json = array(
    'success' => true
);
echo json_encode($json,1);
?>
