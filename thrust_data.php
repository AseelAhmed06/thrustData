<?php
session_start();

$servername = "localhost";  
$username = "root";        
$password = "";           
$dbname = "thrust";     

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['reset'])) {
    unset($_SESSION['data']);
    $conn->query("TRUNCATE TABLE currentdata");
    $conn->query("TRUNCATE TABLE temporarydata");
    header("Location: thrust_data.php"); 
    exit();
}
if (isset($_POST['save_data'])) {
    $name = $_POST['data_name'];

    foreach ($_POST['table_data'] as $row) {
        $pwm = $row['pwm'];
        $current = $row['current'];
        $voltage = $row['voltage'];
        $power = $row['power'];
        $thrust = $row['thrust'];

        
        $stmt = $conn->prepare("INSERT INTO saved_data (name, pwm, current, voltage, power, thrust) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $name, $pwm, $current, $voltage, $power, $thrust);
        $stmt->execute();
        $stmt->close();
    }

    echo "<script>alert('Data saved successfully!');</script>";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Motor Thrust Display</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .top-cards {
            display: flex;
            justify-content: space-between;
            width: 100%;
            margin-bottom: 20px;
            gap: 20px;
            flex-wrap: wrap;
        }

        .card {
            background: white;
            padding: 40px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            width: 48%;
            max-width: 500px;
        }

        .circle-progress-wrapper {
            position: relative;
            width: 170px;
            height: 170px;
            margin: 0 auto;
            overflow: visible;
        }

        .circle-progress {
            transform: rotate(-90deg);
        }

        .circle-progress circle {
            fill: none;
            stroke-width: 15;
        }

        .circle-progress .background {
            stroke: #ddd;
        }

        .circle-progress .foreground {
            stroke: #4caf50;
            stroke-linecap: round;
            transition: stroke-dasharray 0.3s;
        }

        .throttle-info {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 20px;
            color: #333;
            font-weight: bold;
        }

        .thrust-info {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: center;
        }

        thead {
            background-color: #f2f2f2;
        }

        button {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            background-color: #ff5733;
            color: white;
            border: none;
            border-radius: 5px;
            margin-top: 20px;
        }

        button:hover {
            background-color: #e74c3c;
        }

        .table-container {
            width: 100%;
            max-height: 400px;
            overflow-y: auto;
            margin-top: 20px;
        }
        .table-card {
            background: white;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            width: 100%;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function fetchData() {
            $.ajax({
                url: "fetch_data.php",
                method: "GET",
                dataType: "json",
                success: function(data) {
                    if (data) {
                        $("#pwm-value").text(data.pwm || "N/A");
                        $("#thrust-value").text(data.thrust || "N/A");

                        const throttle = data.throttle || 0;
                        const strokeDashOffset = 440 - (440 * throttle / 100);
                        $(".foreground").attr("stroke-dashoffset", strokeDashOffset);
                        $(".throttle-info").text(`${throttle}%`);
                    
                        let tableRows = "";
                        data.tableData.forEach(row => {
                            const voltage = parseFloat(row.voltage); 
                            const current = parseFloat(row.current); 
                            const power = (voltage * current).toFixed(2);  
                            const formattedVoltage = isNaN(voltage) ? 'N/A' : voltage.toFixed(3);  
                            const formattedCurrent = isNaN(current) ? 'N/A' : current.toFixed(3);  
                            
                            tableRows += `<tr>
                                <td>${row.pwm}</td>
                                <td>${formattedCurrent}</td>  
                                <td>${formattedVoltage}</td> 
                                <td>${power}</td>   
                                <td>${row.thrust}</td>
                            </tr>`;
                        });

                        $("tbody").html(tableRows);

                    }
                }
            });
        }
        function saveData() {
            const dataName = prompt("Enter a name to save this data:");
            if (dataName) {
                const tableData = [];
                $("tbody tr").each(function() {
                    const row = {
                        pwm: $(this).find("td:eq(0)").text(),
                        current: $(this).find("td:eq(1)").text(),
                        voltage: $(this).find("td:eq(2)").text(),
                        power: $(this).find("td:eq(3)").text(),
                        thrust: $(this).find("td:eq(4)").text()
                    };
                    tableData.push(row);
                });

                $.ajax({
                    url: "",
                    method: "POST",
                    data: {
                        save_data: true,
                        data_name: dataName,
                        table_data: tableData
                    },
                    success: function(response) {
                        alert('Data saved successfully!');
                    }
                });
            }
        }

        $(document).ready(function() {
            fetchData();
            setInterval(fetchData, 2000);
        });
    </script>
</head>
<body>
    <div class="container">
        <div class="top-cards">
            <div class="card">
                <div class="circle-progress-wrapper">
                <svg class="circle-progress" width="170" height="170" viewBox="0 0 170 170">
                    <circle class="background" cx="85" cy="85" r="70"></circle>
                    <circle class="foreground" cx="85" cy="85" r="70" stroke-dasharray="440" stroke-dashoffset="440"></circle>
                </svg>

                    <div class="throttle-info">N/A</div>
                </div>
            </div>
            <div class="card">
                <h3>PWM & Thrust</h3>
                <p>PWM: <span id="pwm-value">N/A</span></p>
                <p>Thrust: <span id="thrust-value">N/A</span> Kg</p>
            </div>
        </div>

        <div class="table-card">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>PWM</th>
                            <th>Current (A)</th>
                            <th>Voltage (V)</th>
                            <th>Power (W)</th>
                            <th>Thrust (kg)</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                    </tbody>
                </table>
            </div>
        </div>

        <div class="container">
        
        <button onclick="saveData()">Save Data</button>

        <a href="http://localhost/thrust/temp_data.php"><button>Show Temporary data Table</button></a>
        <a href="?reset=true"><button>Reset Table</button></a>
    </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>
