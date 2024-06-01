<?php
$logFilePath = "token_log.json";
$logLimit = 1000;

function addToLog($token, $channelName, $userLocation)
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
        "timestamp" => date("Y-m-d H:i:s")
    ];
    $logData[] = $newEntry;

    // Trim the log to the specified limit
    if (count($logData) > $logLimit) {
        $logData = array_slice($logData, -1 * $logLimit);
    }

    // Write the updated log file
    file_put_contents($logFilePath, json_encode($logData));
}

function getUserLocation($ipAddress)
{
    // Use an IP geolocation API or database to get approximate user location
    // Replace the API_URL with the actual API endpoint or database URL
    $apiUrl = "http://api.example.com/geolocation?ip=" . $ipAddress;
    $locationData = @file_get_contents($apiUrl);
    if ($locationData) {
        $locationJson = json_decode($locationData, true);
        if ($locationJson && isset($locationJson['country'])) {
            return $locationJson['country'];
        }
    }
    return "Unknown";
}

$appID = "970CA35de60c44645bbae8a215061b33";
$appCertificate = "5CFd2fd1755d40ecb72977518be15d3b";
$expireTimeInSeconds = 600;

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['channelName']) && isset($_GET['uid'])) {
    $channelName = $_GET['channelName'];
    $uid = $_GET['uid'];

    // Perform IP geolocation
    $userLocation = getUserLocation($_SERVER['REMOTE_ADDR']);

    include("src/AccessToken2.php");

    $accessToken = new AccessToken2($appID, $appCertificate, $expireTimeInSeconds);

    // grant rtc privileges
    $serviceRtc = new ServiceRtc($channelName, $uid);
    $serviceRtc->addPrivilege($serviceRtc::PRIVILEGE_JOIN_CHANNEL, $expireTimeInSeconds);
    $accessToken->addService($serviceRtc);

    // grant rtm privileges
    $serviceRtm = new ServiceRtm($uid);
    $serviceRtm->addPrivilege($serviceRtm::PRIVILEGE_LOGIN, $expireTimeInSeconds);
    $accessToken->addService($serviceRtm);

    // grant chat privileges
    $serviceChat = new ServiceChat($uid);
    $serviceChat->addPrivilege($serviceChat::PRIVILEGE_USER, $expireTimeInSeconds);
    $accessToken->addService($serviceChat);

    $token = $accessToken->build();

    // Add token to log
    addToLog($token, $channelName, $userLocation);

    // Return the token as JSON response
    header('Content-Type: application/json');
    echo json_encode(['token' => $token]);
} else {
    // Return error message
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Missing parameters']);
}
?>