<?php
function getInitials($name) {
    $words = explode(' ', trim($name));
    $initials = '';
    
    if(isset($words[0])) $initials .= strtoupper(substr($words[0], 0, 1)); // First name
    if(isset($words[1])) $initials .= strtoupper(substr($words[1], 0, 1)); // Last name
    
    return $initials ?: 'U'; // Return 'U' if no name
}