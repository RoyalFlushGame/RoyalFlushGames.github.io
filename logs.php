<!DOCTYPE html>
<html>
<head>
    <title>Token Logs</title>
    <style>
        /* Additional styling */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            margin-top: 50px;
            font-family: Arial, sans-serif;
        }

        .table-container {
            margin-top: 20px;
        }

        h1 {
            color: #333;
            font-size: 24px;
            margin-bottom: 10px;
        }

        #log-table {
            width: 100%;
            border-collapse: collapse;
        }

        #log-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: left;
            padding: 8px;
        }

        #log-table td, #log-table th {
            padding: 8px;
            border: 1px solid #ddd;
        }

        .top-menu {
            background-color: #f2f2f2;
            padding: 10px;
            margin-bottom: 20px;
        }

        .top-menu ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        .top-menu ul li {
            display: inline-block;
            margin-right: 10px;
        }

        .top-menu ul li a {
            color: #333;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 3px;
        }

        .top-menu ul li a:hover {
            background-color: #ddd;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="top-menu">
            <ul>
                <li><a href="index.php">Home</a></li>
            </ul>
        </div>
        <div class="table-container">
            <h1>Token Logs</h1>
            <table id="log-table">
                <thead>
                    <tr>
                        <th>Index</th>
                        <th>Timestamp</th>
                        <th>Channel/Room Name</th>
                        <th>Firebase ID</th>
                        <th>Location</th>
                        <th>Token</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $logFilePath = "token_log.json";
                    if (file_exists($logFilePath)) {
                        $logData = json_decode(file_get_contents($logFilePath), true);
                        if (is_array($logData)) {
                            // Sort log entries by timestamp in descending order
                            usort($logData, function($a, $b) {
                                $timestampA = strtotime($a['timestamp']);
                                $timestampB = strtotime($b['timestamp']);
                                return $timestampB - $timestampA;
                            });

                            foreach ($logData as $index => $entry) {
                                echo "<tr>";
                                echo "<td>" . ($index + 1) . "</td>";
                                echo "<td>" . date("Y-m-d h:i:s A", strtotime($entry['timestamp'])) . "</td>";
                                echo "<td>" . $entry['channelName'] . "</td>";
                                echo "<td>" . $entry['fireBaseID'] . "</td>";
                                echo "<td>" . $entry['userLocation'] . "</td>";
                                echo "<td style='max-width: 400px; word-wrap: break-word; overflow-wrap: break-word;'>" . $entry['token'] . "</td>";
                                echo "</tr>";
                            }
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>