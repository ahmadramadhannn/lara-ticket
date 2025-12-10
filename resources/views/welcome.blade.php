<html>

<head>
    @vite([])
    <title>home?</title>
</head>

<body>
    <h1>welcome to homepage</h1>

    @foreach ($users as $user)
        <p>{{ $user->name }}</p>
        <p>{{ $user->email }}</p>
    @endforeach
</body>

</html>