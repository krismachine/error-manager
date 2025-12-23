<?php
// Error Management - Admin Panel
session_start();

// Password (change to your own!)
$ADMIN_PASSWORD = 'password';

// Login
if (isset($_POST['login'])) {
    if ($_POST['password'] === $ADMIN_PASSWORD) {
        $_SESSION['logged_in'] = true;
    } else {
        $error = 'Invalid password';
    }
}

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit;
}

// Mark as completed
if (isset($_POST['complete']) && isset($_SESSION['logged_in'])) {
    $id = $_POST['complete'];
    $file = "data/$id.md";
    
    if (file_exists($file)) {
        $content = file_get_contents($file);
        
        // Add completion date
        $content = str_replace(
            "status: open",
            "status: completed\ncompleted: " . date('Y-m-d H:i:s'),
            $content
        );
        
        // Move to completed
        file_put_contents("completed/$id.md", $content);
        unlink($file);
    }
}

// Reopen report
if (isset($_POST['reopen']) && isset($_SESSION['logged_in'])) {
    $id = $_POST['reopen'];
    $file = "completed/$id.md";
    
    if (file_exists($file)) {
        $content = file_get_contents($file);
        
        // Remove completion date
        $content = preg_replace('/completed: .*\n/', '', $content);
        $content = str_replace("status: completed", "status: open", $content);
        
        // Move to data
        file_put_contents("data/$id.md", $content);
        unlink($file);
    }
}

// Parse .md file function
function parseMarkdown($file) {
    $content = file_get_contents($file);
    
    // Extract frontmatter
    preg_match('/^---\n(.*?)\n---\n(.*)$/s', $content, $matches);
    
    $frontmatter = [];
    if (isset($matches[1])) {
        $lines = explode("\n", $matches[1]);
        foreach ($lines as $line) {
            if (strpos($line, ':') !== false) {
                list($key, $value) = explode(':', $line, 2);
                $frontmatter[trim($key)] = trim($value);
            }
        }
    }
    
    $body = isset($matches[2]) ? trim($matches[2]) : '';
    
    return [
        'meta' => $frontmatter,
        'body' => $body
    ];
}

// Get reports
function getReports($folder) {
    $reports = [];
    $files = glob("$folder/*.md");
    
    foreach ($files as $file) {
        $data = parseMarkdown($file);
        $data['filename'] = basename($file, '.md');
        $reports[] = $data;
    }
    
    // Sort by newest
    usort($reports, function($a, $b) {
        return strcmp($b['meta']['created'] ?? '', $a['meta']['created'] ?? '');
    });
    
    return $reports;
}

$openReports = isset($_SESSION['logged_in']) ? getReports('data') : [];
$completedReports = isset($_SESSION['logged_in']) ? getReports('completed') : [];

$tab = $_GET['tab'] ?? 'open';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Error Management</title>
    <style>
        body {
            font-family: Monaco, monospace;
            background: #1a1816;
            color: #e8e6e3;
            padding: 40px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        h1 {
            color: #d97757;
            margin-bottom: 10px;
        }
        
        .subtitle {
            color: #6b6864;
            margin-bottom: 40px;
        }
        
        /* Login Form */
        .login-form {
            max-width: 400px;
            margin: 100px auto;
        }
        
        .login-form input {
            width: 100%;
            padding: 10px;
            background: #2d2b28;
            border: 1px solid #3d3935;
            color: #e8e6e3;
            font-family: Monaco, monospace;
            box-sizing: border-box;
            margin-bottom: 15px;
        }
        
        .login-form button {
            background: #d97757;
            color: #1a1816;
            border: none;
            padding: 12px 30px;
            cursor: pointer;
            font-family: Monaco, monospace;
            font-weight: bold;
        }
        
        .error {
            color: #d97757;
            margin-bottom: 15px;
        }
        
        /* Tabs */
        .tabs {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
            border-bottom: 2px solid #3d3935;
        }
        
        .tab {
            padding: 10px 20px;
            cursor: pointer;
            text-decoration: none;
            color: #6b6864;
            border-bottom: 2px solid transparent;
            margin-bottom: -2px;
        }
        
        .tab.active {
            color: #d97757;
            border-bottom-color: #d97757;
        }
        
        /* Reports */
        .report {
            background: #2d2b28;
            border-left: 4px solid #d97757;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 15px;
        }
        
        .report-meta {
            flex: 1;
        }
        
        .report-id {
            color: #6b6864;
            font-size: 12px;
        }
        
        .report-type {
            color: #d97757;
            font-weight: bold;
            margin: 5px 0;
        }
        
        .report-date {
            color: #6b6864;
            font-size: 12px;
        }
        
        .report-url {
            color: #6b6864;
            margin-bottom: 10px;
            word-break: break-all;
        }
        
        .report-url a {
            color: #d97757;
            text-decoration: none;
        }
        
        .report-body {
            background: #1a1816;
            padding: 15px;
            border: 1px solid #3d3935;
            margin-top: 10px;
            line-height: 1.6;
        }
        
        .report-actions {
            margin-top: 15px;
        }
        
        .report-actions button {
            background: #d97757;
            color: #1a1816;
            border: none;
            padding: 8px 20px;
            cursor: pointer;
            font-family: Monaco, monospace;
            font-size: 12px;
        }
        
        .report-actions button:hover {
            background: #e89070;
        }
        
        .completed-report {
            opacity: 0.7;
            border-left-color: #4a7a4a;
        }
        
        .completed-report .report-type {
            color: #6b6864;
        }
        
        .stats {
            display: flex;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .stat {
            background: #2d2b28;
            padding: 15px 25px;
            border-left: 4px solid #d97757;
        }
        
        .stat-number {
            font-size: 32px;
            color: #d97757;
            font-weight: bold;
        }
        
        .stat-label {
            color: #6b6864;
            font-size: 12px;
        }
        
        .logout {
            text-align: right;
            margin-bottom: 20px;
        }
        
        .logout a {
            color: #6b6864;
            text-decoration: none;
            font-size: 12px;
        }
        
        .empty {
            text-align: center;
            padding: 60px;
            color: #6b6864;
        }
    </style>
</head>
<body>

<?php if (!isset($_SESSION['logged_in'])): ?>
    
    <div class="login-form">
        <h1>Admin Panel</h1>
        <div class="subtitle">Error Management System</div>
        
        <?php if (isset($error)): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <input type="password" 
                   name="password" 
                   placeholder="Password" 
                   required 
                   autofocus>
            <button type="submit" name="login">Login</button>
        </form>
    </div>

<?php else: ?>
    
    <div class="logout">
        <a href="index.php">Index</a>
        <a href="?logout">Logout</a>
    </div>
    
    <h1>Error Management System</h1>
    <div class="subtitle">Admin Panel</div>
    
    <div class="stats">
        <div class="stat">
            <div class="stat-number"><?= count($openReports) ?></div>
            <div class="stat-label">OPEN</div>
        </div>
        <div class="stat">
            <div class="stat-number"><?= count($completedReports) ?></div>
            <div class="stat-label">COMPLETED</div>
        </div>
    </div>
    
    <div class="tabs">
        <a href="?tab=open" class="tab <?= $tab === 'open' ? 'active' : '' ?>">
            Open (<?= count($openReports) ?>)
        </a>
        <a href="?tab=completed" class="tab <?= $tab === 'completed' ? 'active' : '' ?>">
            Completed (<?= count($completedReports) ?>)
        </a>
    </div>
    
    <?php if ($tab === 'open'): ?>
        
        <?php if (empty($openReports)): ?>
            <div class="empty">No open reports ✓</div>
        <?php else: ?>
            <?php foreach ($openReports as $report): ?>
                <div class="report">
                    <div class="report-header">
                        <div class="report-meta">
                            <div class="report-id">#<?= $report['filename'] ?></div>
                            <div class="report-type"><?= htmlspecialchars($report['meta']['type'] ?? '') ?></div>
                            <div class="report-date">
                                Reported: <?= htmlspecialchars($report['meta']['created'] ?? '') ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="report-url">
                        URL: <a href="<?= htmlspecialchars($report['meta']['url'] ?? '') ?>" target="_blank">
                            <?= htmlspecialchars($report['meta']['url'] ?? '') ?>
                        </a>
                    </div>
                    
                    <?php if (!empty($report['meta']['link'])): ?>
                    <div class="report-url">
                        Link: <a href="<?= htmlspecialchars($report['meta']['link']) ?>" target="_blank">
                            <?= htmlspecialchars($report['meta']['link']) ?>
                        </a>
                    </div>
                    <?php endif; ?>
                    
                    <div class="report-body">
                        <?= nl2br(htmlspecialchars($report['body'])) ?>
                    </div>
                    
                    <div class="report-actions">
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="complete" value="<?= $report['filename'] ?>">
                            <button type="submit">✓ Mark as completed</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        
    <?php else: ?>
        
        <?php if (empty($completedReports)): ?>
            <div class="empty">No completed reports</div>
        <?php else: ?>
            <?php foreach ($completedReports as $report): ?>
                <div class="report completed-report">
                    <div class="report-header">
                        <div class="report-meta">
                            <div class="report-id">#<?= $report['filename'] ?></div>
                            <div class="report-type">✓ <?= htmlspecialchars($report['meta']['type'] ?? '') ?></div>
                            <div class="report-date">
                                Reported: <?= htmlspecialchars($report['meta']['created'] ?? '') ?><br>
                                Completed: <?= htmlspecialchars($report['meta']['completed'] ?? '') ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="report-url">
                        URL: <a href="<?= htmlspecialchars($report['meta']['url'] ?? '') ?>" target="_blank">
                            <?= htmlspecialchars($report['meta']['url'] ?? '') ?>
                        </a>
                    </div>
                    
                    <?php if (!empty($report['meta']['link'])): ?>
                    <div class="report-url">
                        Link: <a href="<?= htmlspecialchars($report['meta']['link']) ?>" target="_blank">
                            <?= htmlspecialchars($report['meta']['link']) ?>
                        </a>
                    </div>
                    <?php endif; ?>
                    
                    <div class="report-body">
                        <?= nl2br(htmlspecialchars($report['body'])) ?>
                    </div>
                    
                    <div class="report-actions">
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="reopen" value="<?= $report['filename'] ?>">
                            <button type="submit">↺ Reopen</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        
    <?php endif; ?>

<?php endif; ?>

</body>
</html>