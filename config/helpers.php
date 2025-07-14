<?php
// config/helpers.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists('escape')) {
    function escape($data) {
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('showMessage')) {
    function showMessage() {
        if (isset($_SESSION['message'])) {
            $message = $_SESSION['message'];
            $type = $message['type'] === 'error' ? 'alert-error' : 'alert-success';
            
            echo '<div class="alert '.$type.'">';
            echo $message['text'];
            echo '<span class="close" onclick="this.parentElement.style.display=\'none\'">&times;</span>';
            echo '</div>';
            
            unset($_SESSION['message']);
        }
    }
}

if (!function_exists('urlencode')) {
    function urlencode($data) {
        return urlencode($data);
    }
}