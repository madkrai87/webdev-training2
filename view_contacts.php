<?php

require_once __DIR__ . '/supabase_config.php';

$URL = SUPABASE_URL . '/rest/v1/contacts?select=*';
$SERVICE_ROLE_KEY = SUPABASE_SERVICE_ROLE_KEY;

if (empty($SERVICE_ROLE_KEY)) {
    die('Supabase service role key not configured. Set SUPABASE_SERVICE_ROLE_KEY.');
}

$ch = curl_init($URL);
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
    die("Error fetching contacts from Supabase. HTTP $http_code. Response: $error_message");
}

$contacts = json_decode($response, true);
if (!is_array($contacts)) {
    die('Unexpected response format from Supabase.');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Records</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="data-page">
        <div class="page-header">
            <h1>Contact Records</h1>
            <p>Data loaded from Supabase and displayed in a responsive table.</p>
        </div>

        <nav class="page-nav">
            <ul>
                <li><a href="index.html">Home</a></li>
                <li><a href="contact.html">Contact</a></li>
                <li><a href="view_contacts.php">View Contacts</a></li>
            </ul>
        </nav>

        <div class="table-card">
            <?php if (count($contacts) === 0): ?>
                <p class="empty-state">No contact records found.</p>
            <?php else: ?>
                <div class="table-meta">Showing <?php echo count($contacts); ?> contact<?php echo count($contacts) === 1 ? '' : 's'; ?></div>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Message</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($contacts as $index => $record): ?>
                                    <?php $recordId = htmlspecialchars($record['id'] ?? ''); ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><?php echo $recordId; ?></td>
                                    <td><?php echo htmlspecialchars($record['name'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($record['email'] ?? ''); ?></td>
                                    <td><?php echo nl2br(htmlspecialchars($record['message'] ?? '')); ?></td>
                                    <td class="actions-cell">
                                        <div class="action-buttons">
                                            <a class="action-button edit" href="edit_contact.php?id=<?php echo $recordId; ?>">Edit</a>
                                            <form class="action-form" method="POST" action="delete_contact.php" onsubmit="return confirm('Delete this record?');">
                                                <input type="hidden" name="id" value="<?php echo $recordId; ?>">
                                                <button type="submit" class="action-button delete">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
