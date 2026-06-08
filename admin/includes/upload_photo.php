<?php
/*
|--------------------------------------------------------------------------
| SAFE PHOTO UPLOAD HELPER
|--------------------------------------------------------------------------
|
| Validates that an uploaded file is a REAL image with an allowed extension
| and stores it under a server-generated random name in /images. Returns the
| stored filename on success, or null when there is no (valid) upload.
|
| This replaces the previous `move_uploaded_file(..., '../images/'.$_FILES
| ['photo']['name'])` pattern, which let an authenticated user upload
| `shell.php` (remote code execution) or use `../` for path traversal.
|
*/

function save_employee_photo($file){

    // No file / not an actual HTTP upload
    if(empty($file) || empty($file['name']) || empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])){
        return null;
    }

    if(isset($file['error']) && $file['error'] !== UPLOAD_ERR_OK){
        return null;
    }

    // Extension allowlist
    $allowed = array('jpg', 'jpeg', 'png', 'gif', 'webp');
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if(!in_array($ext, $allowed, true)){
        return null;
    }

    // Must really be an image (defeats a script renamed to .jpg)
    $info = @getimagesize($file['tmp_name']);
    if($info === false){
        return null;
    }

    // /images lives at the project root (admin pages reference ../images/)
    $dir = __DIR__ . '/../../images/';
    if(!is_dir($dir)){
        @mkdir($dir, 0755, true);
    }

    // Server-generated random name — never trust the client filename
    $safe = bin2hex(random_bytes(8)) . '.' . $ext;

    if(move_uploaded_file($file['tmp_name'], $dir . $safe)){
        return $safe;
    }

    return null;
}
?>
