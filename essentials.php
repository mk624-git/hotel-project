<?php
function alert($type, $message) {
    $bs_class = ($type == 'success') ? 'alert-success' : 'alert-danger';
    echo <<<ALERT
    <div class="alert $bs_class alert-dismissible fade show custom-alert" role="alert">
        <strong>$message</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    ALERT;
}

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Function to upload image
function uploadImage($file, $target_dir, $allowed_types = ['jpg', 'jpeg', 'png', 'gif']) {
    $target_file = $target_dir . basename($file['name']);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
    if (!in_array($imageFileType, $allowed_types)) {
        return ['error' => 'Sorry, only JPG, JPEG, PNG & GIF files are allowed.'];
    }
    
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        return ['success' => true, 'filename' => basename($file['name'])];
    }
    
    return ['error' => 'Sorry, there was an error uploading your file.'];
}
?>