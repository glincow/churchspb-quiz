<?php
header('Content-Type: text/html; charset=utf-8');

include_once "config.php";

// Check connection charset
$result = $db->query("SHOW VARIABLES LIKE 'character_set%'");
echo "<h3>MySQL Connection Charset (from PHP):</h3><pre>";
while ($row = $result->fetch_assoc()) {
    echo $row['Variable_name'] . " = " . $row['Value'] . "\n";
}
echo "</pre>";

// Check data retrieval
$result = $db->query("SELECT name FROM questionnaire WHERE id=1");
$data = $result->fetch_assoc();
echo "<h3>Data from database:</h3>";
echo "<p>Name: " . $data['name'] . "</p>";
?>
