<?php
session_start();
session_destroy();
header("Location: /Proyecto3/index.php");
exit;
