<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "thrust";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch latest data
$sql = "SELECT id, pwm, current, voltage, power, thrust FROM temporarydata ORDER BY id DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table border='1'>
            <tr>
                <th>ID</th>
                <th>PWM</th>
                <th>Current</th>
                <th>Voltage</th>
                <th>Power</th>
                <th>Thrust</th>
            </tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['pwm']}</td>
                <td>{$row['current']}</td>
                <td>{$row['voltage']}</td>
                <td>{$row['power']}</td>
                <td>{$row['thrust']}</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "<p>No data found</p>";
}

$conn->close();
?>
