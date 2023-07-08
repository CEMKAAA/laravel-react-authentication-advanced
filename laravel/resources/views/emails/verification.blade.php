<html>
<head>
    <title>Email Verification</title>
</head>
<body>
    <h2>Email Verification</h2>
    <p>Please click the following link to verify your email:</p>
    <a href="{{ url('http://localhost:3000/verify/'.$token) }}">Verify Email</a>
</body>
</html>
