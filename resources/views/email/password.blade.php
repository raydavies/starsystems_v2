<html>
<body>
<p>Hi there! You recently requested to reset your password. Please click the link below to do so. For security reasons, this link will only be valid for the next hour.</p>
<p><a href="{{ route('password-reset-form-token', ['token' => $token]) }}">{{ route('password-reset-form-token', ['token' => $token]) }}</a></p>
<p>If you didn't request to reset your password, please contact our support team.</p>
</body>
</html>
