<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User List</title>
</head>
<body>
    <h1>User List</h1>
    <table border="1">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <!-- Tambahkan kolom lain jika diperlukan -->
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
            <tr>
                <td>{{ $user->nama }}</td>
                <td>{{ $user->email }}</td>
                <!-- Tampilkan data lain sesuai dengan kebutuhan -->
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
