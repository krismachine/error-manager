<?php
// Error Management - Report Form

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $url = $_POST['url'] ?? '';
    $type = $_POST['type'] ?? '';
    $link = $_POST['link'] ?? '';
    $description = $_POST['description'] ?? '';
    
    if ($url && $type && $description) {
        // Generate report ID
        $date = date('Y-m-d');
        $time = date('H-i-s');
        $id = $date . '-' . $time;
        
        // File content .md
        $content = "---\n";
        $content .= "id: $id\n";
        $content .= "created: " . date('Y-m-d H:i:s') . "\n";
        $content .= "url: $url\n";
        $content .= "type: $type\n";
        if ($link) {
            $content .= "link: $link\n";
        }
        $content .= "status: open\n";
        $content .= "---\n\n";
        $content .= $description;
        
        // Save to file
        file_put_contents("data/$id.md", $content);
        
        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error Management</title>
    <style>
        body {
            font-family: Monaco, monospace;
            background: #1a1816;
            color: #e8e6e3;
            padding: 40px;
            max-width: 900px;
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
        
        label {
            display: block;
            margin-top: 20px;
            margin-bottom: 5px;
            color: #d97757;
        }
        
        input[type="text"],
        select,
        textarea {
            width: 100%;
            padding: 10px;
            background: #2d2b28;
            border: 1px solid #3d3935;
            color: #e8e6e3;
            font-family: Monaco, monospace;
            box-sizing: border-box;
        }
        
        textarea {
            min-height: 150px;
            resize: vertical;
        }
        
        button {
            background: #d97757;
            color: #1a1816;
            border: none;
            padding: 12px 30px;
            margin-top: 20px;
            cursor: pointer;
            font-family: Monaco, monospace;
            font-size: 14px;
            font-weight: bold;
        }
        
        button:hover {
            background: #e89070;
        }
        
        .success {
            background: #2d4a2d;
            border: 1px solid #4a7a4a;
            padding: 15px;
            margin-bottom: 30px;
            border-radius: 4px;
        }
        
        .admin-link {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #3d3935;
            text-align: center;
        }
        
        .admin-link a {
            color: #6b6864;
            font-size: 12px;
            text-decoration: none;
        }
        
        .admin-link a:hover {
            color: #d97757;
        }
    </style>
</head>
<body>
    <h1>Error Management System</h1>
    <div class="subtitle">yoursite</div>
    
    <?php if (isset($success)): ?>
        <div class="success">
            ✓ Report submitted successfully! Thank you.
        </div>
    <?php endif; ?>
    
    <form method="POST">
        <label for="url">Page URL with error *</label>
        <input type="text" 
               id="url" 
               name="url" 
               placeholder="https://yoursite.com/pagewitherror" 
               required>
        
        <label for="type">Error type *</label>
        <select id="type" name="type" required>
            <option value="">-- Select --</option>
            <option value="Translation error">Translation error</option>
            <option value="Missing image">Missing image</option>
            <option value="Wrong image">Wrong image</option>
            <option value="Missing product">Missing product</option>
            <option value="Wrong description">Wrong description</option>
            <option value="Other">Other</option>
        </select>
        
        <label for="link">Additional link (optional)</label>
        <input type="text" 
               id="link" 
               name="link" 
               placeholder="e.g. link to correct translation, screenshot...">
        
        <label for="description">Problem description *</label>
        <textarea id="description" 
                  name="description" 
                  placeholder="Describe the problem in detail..."
                  required></textarea>
        
        <button type="submit">Submit report</button>
    </form>
    
    <div class="admin-link">
        <a href="admin.php">Admin Panel</a>
    </div>
</body>
</html>