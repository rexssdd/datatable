<?php
require 'db.php';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=users.xls");

$stmt = $pdo->query("SELECT full_name,email,address,age,dob,contact_number FROM users");

echo "Full Name\tEmail\tAddress\tAge\tDOB\tContact\n";

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $row['full_name']."\t".
         $row['email']."\t".
         $row['address']."\t".
         $row['age']."\t".
         $row['dob']."\t".
         $row['contact_number']."\n";
}
exit;