<?php
session_start();
echo "Session test OK<br>";
echo "Loggedin: " . (isset($_SESSION['loggedin']) ? 'Yes' : 'No') . "<br>";
echo "Role: " . (isset($_SESSION['role']) ? $_SESSION['role'] : 'Not set') . "<br>";
echo "Time: " . date('Y-m-d H:i:s') . "<br>";
?>
