<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "thrust";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$query = "SELECT pwm, current, voltage, thrust FROM currentdata ORDER BY id DESC LIMIT 1";
$result = $conn->query($query);

$response = [
    "pwm" => null,
    "thrust" => null,
    "throttle" => null,
    "tableData" => []
];

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $response["pwm"] = $row["pwm"];
    $response["thrust"] = $row["thrust"];
    $response["throttle"] = ($row["pwm"] -1000) / 10; 
}

$tableQuery = "SELECT pwm, current, voltage,  thrust FROM currentdata ORDER BY id DESC LIMIT 10";
$tableResult = $conn->query($tableQuery);

if ($tableResult && $tableResult->num_rows > 0) {
    while ($tableRow = $tableResult->fetch_assoc()) {
        $response["tableData"][] = $tableRow;
    }
}

echo json_encode($response);

$conn->close();
?>
