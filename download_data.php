<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "thrust";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['name'])) {
    $name = $_GET['name'];

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $name . '_data.csv"');

    $output = fopen("php://output", "w");

    // Add column headers
    fputcsv($output, ['PWM', 'Current', 'Voltage', 'Power', 'Thrust']);

    // Fetch data from the database
    $sql = "SELECT pwm, current, voltage, power, thrust FROM saved_data WHERE name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }

    fclose($output);
}

$conn->close();
?>
