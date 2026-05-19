<?php

$name = "John Doe";
$age = 30;
$isAdmin = true;

echo "Name: " . $name . "\n";

if ($isAdmin) {
    echo "User is an admin.\n";
} else {
    echo "User is not an admin.\n";
}

$skill=['PHP', 'JavaScript', 'Python'];
foreach ($skill as $s) {
    echo "<li>" . $s . "</li>\n";
}

?>