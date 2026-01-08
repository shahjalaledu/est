<?php
// ==========================================
// CONFIGURATION
// ==========================================
$password = '12345'; // <--- CHANGE THIS
$site_name = "My Personal Blog";
// ==========================================

session_start();
require_once 'Parsedown.php'; // Loads the Markdown engine

// 1. Handle Login/Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}
if (isset($_POST['pass'])) {
    if ($_POST['pass'] === $password) {
        $_SESSION['logged_in'] = true;
    }
}
$is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;

// 2. Prepare the Content (Server Side)
$html_content = "";
$raw_markdown = "";
$page_title = $site_name;

if ($is_logged_in) {
    if (file_exists('posts.md')) {
        $raw_markdown = file_get_contents('posts.md');
        
        // Extract the first H1 (# Title) to use as the page title
        if (preg_match('/^# (.*)$/m', $raw_markdown, $matches)) {
            $page_title = trim($matches[1]);
        }
        
        // Convert Markdown to HTML using PHP (Not JS)
        $Parsedown = new Parsedown();
        $html_content = $Parsedown->text($raw_markdown);
    } else {
        $html_content = "<p>Please create posts.md</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    
    <meta name="description" content="<?php echo substr(strip_tags($html_content), 0, 150); ?>...">
    <meta property="og:type" content="article" />
    <meta property="og:title" content="<?php echo $page_title; ?>" />
    
    <?php if ($is_logged_in): ?>
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "BlogPosting",
      "headline": "<?php echo addslashes($page_title); ?>",
      "articleBody": "<?php echo addslashes(strip_tags($html_content)); ?>",
      "author": {
        "@type": "Person",
        "name": "Admin"
      }
    }
    </script>
    <?php endif; ?>

    <style>
        body { font-family: 'Georgia', serif; line-height: 1.8; color: #333; max-width: 800px; margin: 0 auto; padding: 20px; background: #fff; }
        .login-container { max-width: 300px; margin: 100px auto; text-align: center; font-family: sans-serif; }
        input { padding: 10px; width: 100%; margin-bottom: 10px; }
        button { padding: 10px 20px; background: #222; color: #fff; border: none; cursor: pointer; }
        
        /* Article Styling */
        article { font-size: 1.1rem; }
        h1, h2, h3 { font-family: sans-serif; color: #111; margin-top: 1.5em; }
        h1 { font-size: 2.5em; border-bottom: 4px solid #eee; padding-bottom: 10px; }
        blockquote { border-left: 5px solid #0070f3; padding-left: 20px; color: #555; font-style: italic; }
        img { max-width: 100%; height: auto; }
        code { background: #f4f4f4; padding: 2px 5px; font-family: monospace; }
    </style>
</head>
<body>

<?php if (!$is_logged_in): ?>
    <div class="login-container">
        <h2>Private Blog</h2>
        <form method="POST">
            <input type="password" name="pass" placeholder="Password" required>
            <button type="submit">Read</button>
        </form>
    </div>
<?php else: ?>
    
    <main>
        <article>
            <?php echo $html_content; ?>
        </article>
    </main>

    <footer style="margin-top: 50px; border-top: 1px solid #eee; padding-top: 20px; font-family: sans-serif; font-size: 0.8rem; color: #999;">
        <a href="?logout" style="color: red; text-decoration: none;">Logout</a>
    </footer>

<?php endif; ?>

</body>
</html>
