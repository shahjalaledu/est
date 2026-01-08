<?php
// ==========================================
// CONFIGURATION
// ==========================================
$password = '12345'; // <--- Set your Master Password here
$site_name = "My Tech Blog";
$author_name = "Admin"; 
// ==========================================

session_start();
require_once 'Parsedown.php';

// --- LOGOUT ---
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}

// --- LOGIN HANDLING ---
if (isset($_POST['pass'])) {
    if ($_POST['pass'] === $password) {
        $_SESSION['logged_in'] = true;
        // IMPORTANT: We redirect to '?id=article'. 
        // This tricks Chrome into thinking it's a new page, enabling the "Listen" button.
        header("Location: index.php?id=article"); 
        exit;
    }
}

$is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;

// If user tries to access the article without login, redirect to home
if (isset($_GET['id']) && !$is_logged_in) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    
    <?php if ($is_logged_in): 
        // --- READ MODE ---
        $raw_markdown = file_exists('posts.md') ? file_get_contents('posts.md') : "# Welcome\nPlease edit posts.md";
        
        // Extract Title from the first line (# Title)
        $page_title = $site_name;
        if (preg_match('/^# (.*)$/m', $raw_markdown, $matches)) {
            $page_title = trim($matches[1]);
        }
        
        // Render Markdown to HTML
        $Parsedown = new Parsedown();
        $html_content = $Parsedown->text($raw_markdown);
        $clean_desc = mb_substr(strip_tags($html_content), 0, 160) . "...";
        $word_count = str_word_count(strip_tags($html_content));
    ?>
    
    <title><?php echo $page_title; ?></title>
    <meta name="description" content="<?php echo $clean_desc; ?>" />
    
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="article" />
    <meta property="og:title" content="<?php echo $page_title; ?>" />
    <meta property="og:description" content="<?php echo $clean_desc; ?>" />
    <meta property="og:url" content="http://<?php echo $_SERVER['HTTP_HOST']; ?>/index.php" />
    <meta property="article:author" content="<?php echo $author_name; ?>" />
    
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@graph": [
        {
          "@type": "WebPage",
          "name": "<?php echo addslashes($page_title); ?>",
          "url": "http://<?php echo $_SERVER['HTTP_HOST']; ?>/index.php?id=article"
        },
        {
          "@type": "BlogPosting",
          "headline": "<?php echo addslashes($page_title); ?>",
          "author": {
            "@type": "Person",
            "name": "<?php echo $author_name; ?>"
          },
          "datePublished": "<?php echo date("Y-m-d"); ?>",
          "articleBody": "<?php echo addslashes(str_replace(["\r", "\n"], " ", strip_tags($html_content))); ?>"
        }
      ]
    }
    </script>

    <?php else: ?>
    <title>Login Required</title>
    <meta name="robots" content="noindex" />
    <?php endif; ?>

    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif; line-height: 1.6; color: #333; background: #f0f2f5; margin: 0; padding: 0; }
        
        /* Container */
        .site-content { max-width: 800px; margin: 0 auto; background: #fff; min-height: 100vh; padding: 20px 40px; }
        
        /* Login Styles */
        .login-screen { display: flex; height: 100vh; justify-content: center; align-items: center; background: #2c3e50; }
        .login-box { background: white; padding: 40px; border-radius: 10px; text-align: center; box-shadow: 0 10px 25px rgba(0,0,0,0.2); }
        input[type="password"] { padding: 15px; width: 250px; border: 2px solid #eee; border-radius: 5px; font-size: 16px; outline: none; }
        input[type="password"]:focus { border-color: #3498db; }
        button { margin-top: 15px; padding: 12px 30px; background: #3498db; color: white; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; }
        
        /* Content Styles */
        img { max-width: 100%; height: auto; display: block; margin: 20px 0; border-radius: 5px; }
        h1 { font-size: 2.5rem; margin-top: 20px; color: #2c3e50; line-height: 1.2; }
        h2 { font-size: 1.8rem; margin-top: 40px; color: #34495e; }
        p { margin-bottom: 20px; font-size: 18px; color: #222; }
        blockquote { border-left: 5px solid #3498db; margin: 30px 0; padding: 10px 20px; background: #f1f9ff; color: #555; }
        
        /* Header */
        .top-bar { border-bottom: 1px solid #eee; padding-bottom: 20px; margin-bottom: 30px; display: flex; justify-content: space-between; }
        .logout { color: #e74c3c; text-decoration: none; font-weight: bold; border: 1px solid #e74c3c; padding: 5px 15px; border-radius: 20px; }
    </style>
</head>
<body>

<?php if (!$is_logged_in): ?>
    
    <div class="login-screen">
        <div class="login-box">
            <h2 style="margin-top:0">Protected Blog</h2>
            <form method="POST">
                <input type="password" name="pass" placeholder="Enter Password" required>
                <br>
                <button type="submit">Read Articles</button>
            </form>
        </div>
    </div>

<?php else: ?>

    <div class="site-content">
        <header class="top-bar">
            <span><?php echo $site_name; ?></span>
            <a href="?logout" class="logout">Log Out</a>
        </header>

        <main>
            <article class="post">
                <?php 
                    // This outputs the clean HTML directly from the server
                    echo $html_content; 
                ?>
            </article>
        </main>
        
        <?php if ($word_count < 300): ?>
            <div style="background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin-top: 50px; font-size: 0.9em;">
                <strong>Note:</strong> Your article is only <?php echo $word_count; ?> words. 
                Google Chrome <u>requires</u> about 300-500 words to enable the "Listen to this page" button. 
                Please write more text to test the audio feature.
            </div>
        <?php endif; ?>

        <footer style="margin-top: 60px; color: #999; font-size: 0.8rem; text-align: center;">
            &copy; <?php echo date("Y"); ?> <?php echo $site_name; ?>
        </footer>
    </div>

<?php endif; ?>

</body>
</html>
