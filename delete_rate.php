<?php
include 'inc/db.php';
$id = intval($_POST['id']);
$conn->query("DELETE FROM rates WHERE id=$id");
$conn->close();
