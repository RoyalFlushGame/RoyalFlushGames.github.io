<?php
$logFilePath = "token_log.json";
$logLimit = 1000;

function addToLog($token, $channelName, $userLocation, $fireBaseID)
{
    global $logFilePath, $logLimit;

    // Read the existing log file
    $logData = [];
    if (file_exists($logFilePath)) {
        $logData = json_decode(file_get_contents($logFilePath), true);
    }

    // Add new log entry
    $newEntry = [
        "token" => $token,
        "channelName" => $channelName,
        "userLocation" => $userLocation,
        "timestamp" => date("Y-m-d H:i:s"),
        "fireBaseID" => $fireBaseID
    ];
    $logData[] = $newEntry;

    // Trim the log to the specified limit
    if (count($logData) > $logLimit) {
        $logData = array_slice($logData, -1 * $logLimit);
    }

    // Write the updated log file
    file_put_contents($logFilePath, json_encode($logData));
}

function get_geolocation($apiKey, $ip, $lang = "en", $fields = "*", $excludes = "") {
    $url = "https://api.ipgeolocation.io/ipgeo?apiKey=".$apiKey."&ip=".$ip."&lang=".$lang."&fields=".$fields."&excludes=".$excludes;
    $cURL = curl_init();

    curl_setopt($cURL, CURLOPT_URL, $url);
    curl_setopt($cURL, CURLOPT_HTTPGET, true);
    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($cURL, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Accept: application/json',
        'User-Agent: '.$_SERVER['HTTP_USER_AGENT']
    ));

    return curl_exec($cURL);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['channelName']) && isset($_GET['uid']) && isset($_GET['accessToken'])  && isset($_GET['fireBaseID'])) {
    $channelName = $_GET['channelName'];
    $accessToken = $_GET['accessToken'];
    $fireBaseID = $_GET['fireBaseID'];
    $uid = $_GET['uid'];

    // Perform IP geolocation
    $userIP = $_SERVER['REMOTE_ADDR'];
    $apiKey = "a47f18b79ac34526a867b4ec6f137f37";
    $location = get_geolocation($apiKey, $userIP);
    $decodedLocation = json_decode($location, true);
    $userLocation = $decodedLocation['country_name'];

    // Add token to log
    addToLog($accessToken, $channelName, $userLocation, $fireBaseID);

    // Return the token as JSON response
    header('Content-Type: application/json');
    echo json_encode(['token' => $accessToken]);
} else {
    // Return error message
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Missing parameters']);
}
?>
