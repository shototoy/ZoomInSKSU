<!DOCTYPE html>
<html>
<head>
    <title>Login - Announcement System</title>
    <link rel="stylesheet" href="<?= base_url('css/shared.css') ?>">
    <style>
        main { display: flex; align-items: center; justify-content: center; gap: 10vw; padding: 40px; position: relative; left: -10vw; }
        .logo-container { max-width: 400px; }
        .logo-container img { width: 100%; height: auto; border-radius: 12px; }
        .login-container { max-width: 400px; width: 100%; background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 16px rgba(0,0,0,0.1); }
        .login-container h2 { margin-bottom: 24px; text-align: center; color: #2d3748; }
    </style>
</head>
<body>
    <header>
        <h1>ZoomInSKSU</h1>
    </header>
    <main>
        <div class="logo-container">
            <img src="<?= base_url('assets/logo.png') ?>" alt="ZoomInSKSU Logo">
        </div>
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
        <p>&copy; 2025 ZoomInSKSU System, By Esteban and Esteva</p>
    </footer>
</body>
</html>