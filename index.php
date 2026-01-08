<?php
// ==========================================
// CONFIGURATION
// ==========================================
$password = '12345'; // <--- CHANGE THIS PASSWORD
$site_title = "My Private Blog";
// ==========================================

session_start();

// Handle Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}

// Handle Login Submission
if (isset($_POST['pass'])) {
    if ($_POST['pass'] === $password) {
        $_SESSION['logged_in'] = true;
    } else {
        $error = "Wrong password";
    }
}

// Check if logged in
$is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $site_title; ?></title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; line-height: 1.6; color: #333; background: #f9f9f9; }
        .login-box { max-width: 300px; margin: 100px auto; padding: 30px; background: white; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); text-align: center; }
        input[type="password"] { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { background: #0070f3; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; width: 100%; font-size: 16px; }
        button:hover { background: #0051a2; }
        
        /* Blog Post Styles */
        header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #eaeaea; margin-bottom: 30px; padding-bottom: 10px; }
        h1 { margin: 0; font-size: 1.8rem; }
        a.logout { text-decoration: none; color: #cc0000; font-size: 0.9rem; border: 1px solid #cc0000; padding: 5px 10px; border-radius: 4px; }
        
        /* Markdown Content */
        article img { max-width: 100%; border-radius: 5px; }
        article blockquote { border-left: 4px solid #0070f3; margin: 0; padding-left: 15px; color: #555; background: #eef6fc; padding: 10px; }
        article h1, article h2 { border-bottom: 1px solid #eee; padding-bottom: 5px; margin-top: 40px; }
        article code { background: #eee; padding: 2px 5px; border-radius: 3px; }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
</head>
<body>

<?php if (!$is_logged_in): ?>
    <div class="login-box">
        <h2>Protected Blog</h2>
        <form method="POST">
            <input type="password" name="pass" placeholder="Enter Password" required autofocus>
            <button type="submit">Unlock</button>
        </form>
        <?php if (isset($error)) echo "<p style='color:red; font-size:0.9rem'>$error</p>"; ?>
    </div>

<?php else: ?>
    <header>
        <h1><?php echo $site_title; ?></h1>
        <a href="?logout" class="logout">Logout</a>
    </header>

    <main>
        <article id="content">
            </article>
    </main>

    <script id="raw-markdown" type="text/plain">
<?php
    if (file_exists('posts.md')) {
        echo htmlspecialchars(file_get_contents('posts.md'));
    } else {
        echo "# No posts yet.\nCreate a file named posts.md to start writing.";
    }
?>
    </script>

    <script>
        // Convert Markdown to HTML immediately
        const markdown = document.getElementById('raw-markdown').textContent;
        document.getElementById('content').innerHTML = marked.parse(markdown);
    </script>
<?php endif; ?>

</body>
</html>
