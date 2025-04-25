<!DOCTYPE html>
<html>
<head>
    <title>SAML Login</title>
</head>
<body>
    <h1>Login as B2B User</h1>
    <form method="POST" action="{{ route('saml.login') }}">
        @csrf
        <input type="email" name="email" placeholder="Email" required>
        <button type="submit">Login</button>
    </form>
</body>
</html>
