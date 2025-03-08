<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Data Table</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>

    <h2>Live Sensor Data</h2>
    <div id="data-container">Loading data...</div>

    <script>
        function fetchData() {
            $.ajax({
                url: "fetch_data_all.php",
                method: "GET",
                success: function(data) {
                    $("#data-container").html(data);
                }
            });
        }

        // Fetch data initially
        fetchData();

        // Auto-refresh every 2 seconds
        setInterval(fetchData, 2000);
    </script>

</body>
</html>
