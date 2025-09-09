<!DOCTYPE html>
<html>
<head>
    <title>Match Test</title>
</head>
<body>
    <h1>Match Test</h1>
    <pre id="output"></pre>

    <script>
        fetch("/match/job/123")
            .then(res => res.json())
            .then(data => {
                document.getElementById("output").innerText = JSON.stringify(data, null, 2);
            });
    </script>
</body>
</html>
