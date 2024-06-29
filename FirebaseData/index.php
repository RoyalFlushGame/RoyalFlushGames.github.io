<!DOCTYPE html>
<html>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
<head>
  <title>User Search and Delete</title>
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
        max-width: 700px;
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
        display: flex
        flex-direction: column;
    }

    label {
        font-weight: bold;
        margin-bottom: 5px;
    }

    input[type="text"], input[type="email"], input[type="tel"] {
      display: block;
      width: 100%;
      padding: 10px;
      margin-bottom: 10px;
      font-size: 16px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }

    input#firebaseId {
      max-width: 700px;
    }

    input[type="submit"], button {
      padding: 10px 20px;
      font-size: 16px;
      background-color: #4285f4;
      color: #fff;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }

    .user-info {
      margin-bottom: 20px;
      background-color: #fff;
      padding: 20px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }

    .notice {
      margin-top: 20px;
      padding: 10px;
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
      border-radius: 4px;
    }

    .delete-btn {
      background-color: #dc3545;
    }

    .footer {
            margin-top: 20px;
            text-align: center;
            color: #888;
            font-size: 12px;
        }
  </style>
</head>
<body>
  <div class="container">
    <main>

      <?php if (!empty($message)): ?>
        <div class="notice"><?php echo $message; ?></div>
      <?php endif; ?>

      <div class="py-5 text-center">
        <img class="d-block mx-auto mb-4" src="datadelete.png" alt="" width="100" height="100">
        <h2>User data deletion request</h2>
        <p class="lead">Please provide required info to get your data deleted from our server</p>
      </div>
      <?php
      $message = '';
      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $firebaseId = $_POST['firebaseId'];
        $email = $_POST['email'];
        $fbId = $_POST['fbid'];
        $phone = $_POST['phone'];
        $appleId = $_POST['appleId'];

        // Prepare data as an object
        $requestData = array(
          'firebaseId' => $firebaseId,
          'email' => $email,
          'fbId' => $fbId,
          'phone' => $phone,
          'appleId' => $appleId
        );

        // Read existing JSON file content
        $file = 'userdata.json';
        $existingData = file_get_contents($file);

        // Decode existing JSON data
        $jsonData = json_decode($existingData, true);

        // Append new request data to the "requests" array
        $jsonData['requests'][] = $requestData;

        // Convert data to JSON format
        $updatedData = json_encode($jsonData, JSON_PRETTY_PRINT);

        // Store updated data in the JSON file
        file_put_contents($file, $updatedData);

        // Display success message
        $message = "Your data deletion request has been received. Your data will be removed in 24 to 48 hours.";
        
      }
      ?>
      
      <form method="POST">
        <label for="firebaseId">Firebase ID:</label>
        <input type="text" id="firebaseId" name="firebaseId" required>

        <label for="email">Your email (to receive update when deletion is complete):</label>
        <input type="email" id="email" name="email" required>

        <hr class="my-4">

        <label for="fbid">FB ID (optional):</label>
        <input type="text" id="fbid" name="fbid">

        <label for="phone">Phone Number:</label>
        <input type="text" id="phone" name="phone">

        <label for="appleId">Apple ID:</label>
        <input type="text" id="appleId" name="appleId">

        <hr class="my-4">
        
        <div class="form-check">
          <input type="checkbox" class="form-check-input" id="same-address" required>
          <label class="form-check-label" for="same-address">I understand that after deleting my data, I won't be able to retrieve it.</label>
        </div>

        <input type="submit" value="Submit request">
      </form>
      
    </main>
  </div>
  <footer class="my-5 pt-5 text-body-secondary text-center text-small">
    <p class="mb-1"><?php echo date("Y"); ?> Wawe poker face. All rights reserved.</p>
    <ul class="list-inline">
      <li class="list-inline-item"><a href="https://wawepokerface.com/Privacy.html">Privacy</a></li>
      <li class="list-inline-item"><a href="https://wawepokerface.com/Privacy.html">Terms</a></li>
      <li class="list-inline-item"><a href="https://wawepokerface.com/Privacy.html">Support</a></li>
    </ul>
  </footer>
</body>
</html>