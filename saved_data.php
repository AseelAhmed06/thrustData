<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "thrust";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT DISTINCT name FROM saved_data ORDER BY name ASC";
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Motor Thrust Data</title>
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .switch {
            position: relative;
            display: inline-block;
            width: 34px;
            height: 20px;
        }
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: 0.4s;
            border-radius: 20px;
        }
        .slider:before {
            position: absolute;
            content: "";
            height: 14px;
            width: 14px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: 0.4s;
            border-radius: 50%;
        }
        input:checked + .slider {
            background-color: #2196F3;
        }
        input:checked + .slider:before {
            transform: translateX(14px);
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
</head>
<body>
<div class="container">
    <h1>Thrust Data</h1>
    <div class="table-card">
        <div class="table-container">
            <table>
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Select</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                        echo "<td>
                                <label class='switch'>
                                    <input type='checkbox' class='data-switch' data-name='" . htmlspecialchars($row['name']) . "'>
                                    <span class='slider'></span>
                                </label>
                              </td>";
                        echo "<td>
                                <button class='download-btn' data-name='" . htmlspecialchars($row['name']) . "'>Download</button>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No data found</td></tr>";
                }
                
                ?>
                </tbody>
            </table>
        </div>
    </div>

    <h2>Selected Data</h2>
    <div class="table-card">
        <div class="table-container">
            <table id="data-table">
                <thead>
                <tr>
                    <th>PWM</th>
                    <th>Current</th>
                    <th>Voltage</th>
                    <th>Power</th>
                    <th>Thrust</th>
                </tr>
                </thead>
                <tbody>
                    <tr><td colspan='5'>Select a dataset to view</td></tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
document.querySelectorAll('.data-switch').forEach(switchElement => {
    switchElement.addEventListener('change', function() {
        document.querySelectorAll('.data-switch').forEach(el => {
            if (el !== this) {
                el.checked = false;
            }
        });

        if (this.checked) {
            const name = this.getAttribute('data-name');
            fetchData(name);
        } else {
            document.querySelector('#data-table tbody').innerHTML = '<tr><td colspan="5">Select a dataset to view</td></tr>';
        }
    });
});

document.querySelectorAll('.download-btn').forEach(button => {
    button.addEventListener('click', function() {
        const name = this.getAttribute('data-name');
        window.location.href = 'download_data.php?name=' + encodeURIComponent(name);
    });
});



function fetchData(name) {
    fetch('fetch_data2.php?name=' + encodeURIComponent(name))
        .then(response => response.json())
        .then(data => {
            const tableBody = document.querySelector('#data-table tbody');
            tableBody.innerHTML = '';

            if (data.length > 0) {
                data.forEach(row => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${row.pwm}</td>
                        <td>${row.current}</td>
                        <td>${row.voltage}</td>
                        <td>${row.power}</td>
                        <td>${row.thrust}</td>
                    `;
                    tableBody.appendChild(tr);
                });
            } else {
                tableBody.innerHTML = '<tr><td colspan="5">No data available</td></tr>';
            }
        })
        .catch(error => console.error('Error fetching data:', error));
}
</script>

</body>
</html>

<?php
$conn->close();
?>
