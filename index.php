<?php

$name_from_browser = $_GET['fullname'] ?? '';
$email_from_browser = $_GET['email'] ?? '';
$message_from_browser = $_GET['message'] ?? '';

// Declare constants for Supabase API
$URL = 'https://kwtikusztagedqiibznw.supabase.co';
$KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Imt3dGlrdXN6dGFnZWRxaWliem53Iiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzkxNjkwODYsImV4cCI6MjA5NDc0NTA4Nn0.TPpfIr1vQYopL5fCXoxV4UPbvGsR7CaorqZSEzVZS18';

// Insert a new record into the 'contacts' table
$data = json_encode([
    'name' => $name_from_browser,
    'email' => $email_from_browser,
    'message' => $message_from_browser
]);

$ch = curl_init("$URL/contacts");
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $data,
    CURLOPT_HTTPHEADER => [
        'apikey: ' . $KEY,
        'Content-Type: application/json',
        'Authorization: Bearer ' . $KEY
    ],
    CURLOPT_RETURNTRANSFER => true
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($http_code !== 201) {
    die("Error inserting record into Supabase.");
}

curl_close($ch);

?>