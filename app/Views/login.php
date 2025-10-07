<!DOCTYPE html>
<html>
<head>
    <title>Login - Announcement System</title>
    <link rel="stylesheet" href="<?= base_url('css/shared.css') ?>">
    <style>
        main { display: flex; align-items: center; justify-content: center; }
        .login-container { max-width: 400px; width: 100%; background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 16px rgba(0,0,0,0.1); }
        .login-container h2 { margin-bottom: 24px; text-align: center; color: #2d3748; }
    </style>
</head>
<body>
    <header>
        <h1>Announcement System</h1>
    </header>
    <main>
        <div class="login-container">
            <h2>Login</h2>
            <form method="post" action="/login">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
            </form>
        </div>
    </main>
    <footer>
        <p>&copy; 2025 Announcement System</p>
    </footer>
</body>
</html>