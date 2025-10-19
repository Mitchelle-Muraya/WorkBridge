<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>New Contact Message</title>
</head>
<body style="font-family: Arial, sans-serif; background:#f9f9f9; padding:20px;">
  <div style="max-width:600px; margin:auto; background:white; padding:20px; border-radius:8px;">
    <h2 style="color:#007bff;">New Contact Message from WorkBridge</h2>
    <p><strong>Name:</strong> {{ $data['name'] }}</p>
    <p><strong>Email:</strong> {{ $data['email'] }}</p>
    <p><strong>Message:</strong></p>
    <p style="white-space: pre-line;">{{ $data['message'] }}</p>
    <hr>
    <p style="font-size:12px; color:#888;">This message was sent via the WorkBridge contact form.</p>
  </div>
</body>
</html>
