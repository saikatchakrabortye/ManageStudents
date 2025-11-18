<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/***** _Controller suffix is fixed and MY part can be renamed. But must follow codeignitor3 convention */
class MY_Controller extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->library('upload');
        $this->load->helper('url');
        $this->checkLogin();
        
    }
    
    protected function checkLogin() {
        // Check user is loggedIn and has userId in session
        if (!$this->session->userdata('loggedIn') || !$this->session->userdata('userId')) {
            redirect('Login');
        }
    }
    
    protected function validate($formType) {
        $this->load->library('form_validation');
        
        // Load my validation rules config
        $this->config->load('validationRules', TRUE);
        
        $rules = $this->config->item($formType, 'validationRules');
        
        if (!$rules) {
            return ['error' => 'Validation rules not found for: ' . $formType];
        }
        
        $this->form_validation->set_rules($rules);
        
        if ($this->form_validation->run() == FALSE) {
            return ['error' => $this->form_validation->error_array()];
        }
        
        return ['success' => true, 'data' => $this->input->post()];
    }

    protected function validateFile($field_name = 'profile_pic', $max_size = 5242880) {
    if (empty($_FILES[$field_name]['name']) || $_FILES[$field_name]['error'] !== UPLOAD_ERR_OK) {
        return ['error' => 'No file uploaded or upload error'];
    }

    $uploadedFile = $_FILES[$field_name];
    
    // Security validations
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp']; // File extension check
    
    // File extension validation
    $file_extension = strtolower(pathinfo($uploadedFile['name'], PATHINFO_EXTENSION));
    if (!in_array($file_extension, $allowed_extensions)) {
        return ['error' => 'Invalid file extension'];
    }

    // MIME type validation
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $uploadedFile['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime_type, $allowed_types)) {
        return ['error' => 'Invalid file type. Only JPG, PNG, GIF, WEBP allowed'];
    }

    // File size validation
    if ($uploadedFile['size'] > $max_size) {
        return ['error' => 'File size exceeds maximum limit'];
    }

    // Generate safe filename (system generated + original extension)
    $safe_filename = uniqid() . '.' . $file_extension;

    return [
        'success' => true, 
        'file_data' => $uploadedFile, 
        'mime_type' => $mime_type,
        'safe_filename' => $safe_filename // Filename sanitization
    ];
}

protected function compressAndSaveImage($source_path, $mime_type, $destination_path, $quality = 75) {
    try {
        switch ($mime_type) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($source_path);
                // EXIF data stripping - happens automatically when recreating JPEG
                imagejpeg($image, $destination_path, $quality);
                break;
            case 'image/png':
                $image = imagecreatefrompng($source_path);
                imagealphablending($image, false);
                imagesavealpha($image, true);
                imagepng($image, $destination_path, 9);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($source_path);
                imagegif($image, $destination_path);
                break;
            case 'image/webp':
                $image = imagecreatefromstring(file_get_contents($source_path));
                imagewebp($image, $destination_path, $quality);
                break;
            default:
                return false;
        }

        if (isset($image)) {
            imagedestroy($image);
        }
        return file_exists($destination_path);
    } catch (Exception $e) {
        log_message('error', 'Image compression failed: ' . $e->getMessage());
        return false;
    }
}
}