<h1><b>Thrust Data Webpage</b></h1>
<br><br>
<h3><b>Overview</b></h3>
<br>
This project is a web-based interface for displaying and managing motor thrust data received from an ESP32. It collects, displays, and stores PWM, current, voltage, power, and thrust data in a database. Users can view real-time data, save it for later analysis, and reset tables as needed.

<b>Features</b>
<br>
Real-time Data Fetching: Uses AJAX to fetch data dynamically from fetch_data.php.

Data Visualization: Displays PWM and thrust values in real-time.

Circular Progress Indicator: Represents throttle percentage.

Data Storage: Allows users to save session data with a custom name.

Table Management: Provides options to reset tables and view temporary data.
<br><br>
<b>Technologies Used</b>
<br>
Frontend: HTML, CSS, JavaScript, jQuery

Backend: PHP, MySQL

Database: MySQL (Tables: currentdata, temporarydata, saved_data)
<br><br>
<b>Setup Instructions</b>
<br>
Clone or Download the Repository

Setup MySQL Database:

Create a database named thrust.

Create necessary tables (currentdata, temporarydata, saved_data). Refer sql_commands.

Modify Database Connection:

Edit $servername, $username, $password, and $dbname in thrust_data.php to match your MySQL configuration.

Start Apache & MySQL:

Use XAMPP or WAMP for local testing.
<br><br>
<b>Access the Webpage:</b?
<br>
Open http://localhost/thrust/thrust_data.php in a browser.
<br><br>
<b>API Endpoints</b>

fetch_data.php: Retrieves real-time data.

thrust_data.php:

Handles data storage when save_data is triggered.

Resets tables when reset=true is passed as a URL parameter.

Usage Instructions

Monitor Data: View live thrust and PWM updates.

Save Data: Click Save Data, enter a name, and store the session data.

Reset Data: Click Reset Table to clear temporary data.

View Temporary Data: Click Show Temporary Data Table to access stored temporary records.



Author

Developed by Aseel.
