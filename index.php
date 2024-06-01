<!DOCTYPE html>
<html>
<head>
    <title>Generate App Token</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #edf5fc;
            padding: 20px;
            margin: 0;
        }

        .container {
            max-width: 400px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="submit"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 3px;
            margin-bottom: 10px;
            font-size: 14px;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: #fff;
            cursor: pointer;
        }

        .error {
            color: red;
            margin-top: 10px;
        }

        .token-box {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px;
            background-color: #f2f2f2;
            border: 1px solid #ccc;
            border-radius: 3px;
            margin-bottom: 10px;
            overflow-wrap: break-word;
            cursor: pointer;
        }

        .password-input {
            display: flex;
            align-items: center;
            width: 100%;
        }

        .password-input input[type="text"] {
            flex: 1;
            border: none;
            background-color: transparent;
            padding: 0;
        }

        .copy-icon {
            margin-left: 10px;
            cursor: pointer;
        }

        .copy-icon:hover {
            color: #4CAF50;
        }

        .copy-success {
            display: flex;
            align-items: center;
            margin-top: 5px;
            color: #4CAF50;
            font-size: 12px;
        }

        .copy-success svg {
            width: 16px;
            height: 16px;
            margin-right: 5px;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            color: #888;
            font-size: 12px;
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
            font-size: 14px;
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
                <li><a href="logs.php">Logs</a></li>
            </ul>
        </div>

        <h1>Generate App Token</h1>
        <form method="POST">
            <label for="channelName">Channel Name:</label>
            <input type="text" name="channelName" id="channelName" required>

            <label for="uid">UID:</label>
            <input type="text" name="uid" id="uid" required>

            <label for="expireTime">Expire Time (in seconds):</label>
            <input type="text" name="expireTime" id="expireTime" required>

            <input type="submit" name="generateToken" value="Generate App Token">
        </form>

        <?php
        define('APP_ID', 'b8ed574d5d35409293d2552a97ce5dfa');
        define('APP_CERTIFICATE', '1b2a1cde3c43400296e5a10aea567c4a');

        if (isset($_POST['generateToken'])) {
            $channelName = $_POST['channelName'];
            $uid = $_POST['uid'];
            $expireTimeInSeconds = $_POST['expireTime'];

            include("src/AccessToken2.php");

            try {
                // Create the access token
                $accessToken = new AccessToken2(APP_ID, APP_CERTIFICATE, $expireTimeInSeconds);

                // Grant RTC privileges
                $serviceRtc = new ServiceRtc($channelName, $uid);
                $serviceRtc->addPrivilege($serviceRtc::PRIVILEGE_JOIN_CHANNEL, $expireTimeInSeconds);
                $accessToken->addService($serviceRtc);

                // Grant RTM privileges
                $serviceRtm = new ServiceRtm(strval($uid));
                $serviceRtm->addPrivilege($serviceRtm::PRIVILEGE_LOGIN, $expireTimeInSeconds);
                $accessToken->addService($serviceRtm);

                // Grant chat privileges
                $serviceChat = new ServiceChat(strval($uid));
                $serviceChat->addPrivilege($serviceChat::PRIVILEGE_USER, $expireTimeInSeconds);
                $accessToken->addService($serviceChat);

                // Build the token
                $token = $accessToken->build();
        ?>

        <div class="token-box" onclick="copyToken('<?php echo $token; ?>')">
            <div class="password-input">
                <input type="text" value="<?php echo $token; ?>" disabled>
                <span class="copy-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 8h6M9 12h6m-6 4h6m-6-8h6m-12 0l7.956-8.033A1 1 0 0 1 12 4.164V18a2 2 0 0 1-2 2H7.5a.5.5 0 0 1-.5-.5V4.165A1 1 0 0 1 11.044 3L4 11v6.5a.5.5 0 0 0 .5.5H10"/>
                    </svg>
                </span>
            </div>
        </div>

        <div id="copySuccess" class="copy-success" style="display: none;">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="9 11 12 14 22 4"></polyline>
                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-7"></path>
                <rect x="9" y="2" width="13" height="20" rx="2" ry="2"></rect>
            </svg>
            <span>Token Copied!</span>
        </div>

        <?php
            } catch (Exception $e) {
                // Display any errors
                echo '<p class="error">Error: ' . $e->getMessage() . '</p>';
            }
        }
        ?>
    </div>

    <script>
        function copyToken(token) {
            const tempInput = document.createElement('input');
            tempInput.value = token;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand('copy');
            document.body.removeChild(tempInput);

            const copySuccess = document.getElementById('copySuccess');
            copySuccess.style.display = 'flex';
            setTimeout(function() {
                copySuccess.style.display = 'none';
            }, 2000);
        }
    </script>
    <div class="footer">
        <p>&copy; <?php echo date("Y"); ?> Wawe poker face. All rights reserved.</p>
    </div>
</body>
</html>