Click here to reset your password: <a href="{{ $link = url('password/reset', ['token' => $token]).'?email='.urlencode($user->getEmailForPasswordReset()) }}"> {{ $link }} </a>
