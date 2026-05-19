<?php

require_once __DIR__ . '/supabase_config.php';

$URL_BASE = SUPABASE_URL . '/rest/v1/contacts';
$SERVICE_ROLE_KEY = SUPABASE_SERVICE_ROLE_KEY;

if (empty($SERVICE_ROLE_KEY)) {
    die('Supabase service role key not configured. Set SUPABASE_SERVICE_ROLE_KEY.');
}

function redirectToList(string $message = ''): void
{
    $location = 'view_contacts.php';
    if ($message !== '') {
        $location .= '?updated=1';
    }
    header("Location: $location");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = trim((string)($_POST['id'] ?? ''));
    $name = htmlspecialchars(trim($_POST['name'] ?? ''));
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $message = htmlspecialchars(trim($_POST['message'] ?? ''));

    if ($id === '' || empty($name) || empty($email) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die('Invalid form data. Please provide a valid ID, name, email, and message.');
    }

    $payload = json_encode([
        'name' => $name,
        'email' => $email,
        'message' => $message,
    ]);

    $ch = curl_init($URL_BASE . '?id=eq.' . urlencode((string)$id));
    curl_setopt_array($ch, [
        CURLOPT_CUSTOMREQUEST => 'PATCH',
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'apikey: ' . $SERVICE_ROLE_KEY,
            'Authorization: Bearer ' . $SERVICE_ROLE_KEY,
            'Content-Type: application/json',
            'Prefer: return=minimal'
        ]
    ]);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);

    if ($response === false || ($http_code !== 204 && $http_code !== 200)) {
        $error_message = $curl_error ?: trim($response);
        die("Error updating record. HTTP $http_code. Response: $error_message");
    }

    redirectToList('updated');
}

$id = trim((string)($_GET['id'] ?? ''));
if ($id === '') {
    die('Invalid contact ID.');
}

$ch = curl_init($URL_BASE . '?id=eq.' . rawurlencode($id) . '&select=*');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'apikey: ' . $SERVICE_ROLE_KEY,
        'Authorization: Bearer ' . $SERVICE_ROLE_KEY,
        'Content-Type: application/json'
    ]
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

if ($response === false || $http_code !== 200) {
    $error_message = $curl_error ?: trim($response);
    die("Error fetching record. HTTP $http_code. Response: $error_message");
}

$records = json_decode($response, true);
if (!is_array($records) || count($records) === 0) {
    die('Contact record not found.');
}

$record = $records[0];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Contact</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="form-page">
        <nav class="page-nav">
            <ul>
                <li><a href="index.html">Home</a></li>
                <li><a href="contact.html">Contact</a></li>
                <li><a href="view_contacts.php">View Contacts</a></li>
            </ul>
        </nav>

        <div class="form-card">
            <div class="form-header">
                <h1>Edit Contact</h1>
                <p>Update the contact details and save the changes.</p>
            </div>
            <form action="edit_contact.php?id=<?php echo $id; ?>" method="POST">
                <input type="hidden" name="id" value="<?php echo $id; ?>">

                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($record['name'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($record['email'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" rows="6" required><?php echo htmlspecialchars($record['message'] ?? ''); ?></textarea>
                </div>

                <button type="submit">Save Changes</button>
            </form>
        </div>
    </div>
</body>
</html>
