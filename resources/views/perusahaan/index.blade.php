<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HALAMAN PERUSAHAAN</title>
</head>

<body>
    <h1>HALAMAN PERUSAHAAN</h1>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <a class="nav-link" aria-current="page" href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">Logout</a>
    </form>
</body>

</html>