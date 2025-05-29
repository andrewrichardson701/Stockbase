<form method="POST" action="/two-factor-challenge">
    @csrf
    <input type="text" name="code" placeholder="Enter your 2FA code" />
    <button type="submit">Verify</button>
</form>