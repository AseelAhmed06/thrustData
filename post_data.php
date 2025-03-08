<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "thrust"; 
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$pwm = $_GET['pwm'] ?? null;
$current = $_GET['current'] ?? null;
$voltage = $_GET['voltage'] ?? null;
$thrust = $_GET['thrust'] ?? null;

if (is_null($pwm) || is_null($current) || is_null($voltage) || is_null($thrust)) {
    die("All parameters (pwm, current, voltage, thrust) are required.");
}

$power = $current * $voltage;
$sqlCheck = "SELECT pwm FROM temporarydata LIMIT 1";
$result = $conn->query($sqlCheck);

$averageCurrent = $averageVoltage = $averageThrust = 0;
$entryCount = 0;

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $existingPwm = $row['pwm'];

    if ($existingPwm != $pwm) {
        $sqlAverage = "SELECT AVG(current) AS avgCurrent, AVG(voltage) AS avgVoltage, AVG(thrust) AS avgThrust FROM temporarydata";
        $avgResult = $conn->query($sqlAverage);
        
        if ($avgResult->num_rows > 0) {
            $averages = $avgResult->fetch_assoc();
            $averageCurrent = $averages['avgCurrent'];
            $averageVoltage = $averages['avgVoltage'];
            $averageThrust = $averages['avgThrust'];
        }

        $sqlDelete = "DELETE FROM temporarydata";
        if ($conn->query($sqlDelete) === TRUE) {
            echo "All previous data deleted.\n";
        } else {
            echo "Error deleting data: " . $conn->error . "\n";
        }
        $sqlInsert = "INSERT INTO currentdata (pwm, current, voltage, thrust) VALUES (?, ?, ?, ?)";
        $stmtInsert = $conn->prepare($sqlInsert);
        $stmtInsert->bind_param("iddd", $existingPwm, $averageCurrent, $averageVoltage, $averageThrust);

        if ($stmtInsert->execute()) {
            echo "New entry added successfully.\n";
        } else {
            echo "Error adding entry: " . $stmtInsert->error . "\n";
        }

        $stmtInsert->close();
        
    }
}

$sqlInsert = "INSERT INTO temporarydata (pwm, current, voltage, thrust) VALUES (?, ?, ?, ?)";
$stmtInsert = $conn->prepare($sqlInsert);
$stmtInsert->bind_param("iddd", $pwm, $current, $voltage, $thrust);

if ($stmtInsert->execute()) {
    echo "New entry added successfully.\n";
} else {
    echo "Error adding entry: " . $stmtInsert->error . "\n";
}

$stmtInsert->close();
$conn->close();
?>
