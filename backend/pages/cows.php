<?php
// /backend/pages/cows.php

// Global authentication protection
require_once __DIR__ . '/../middleware/Protector.php';

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/FarmContext.php';

$conn = getDatabase();

$user_id = (int) $_SESSION['user_id'];

// Active farm via central FarmContext (single source of truth)
$farm_id = FarmContext::currentFarmIdOrFail();

const ALLOWED_BREEDS = [
    'Holstein',
    'Jersey',
    'Friesian',
    'Ayrshire',
    'Guernsey',
    'Hereford',
    'Angus',
    'Simmental',
    'Other'
];

const ALLOWED_GENDERS = [
    'Female',
    'Male'
];

const ALLOWED_STATUSES = [
    'Active',
    'Dry',
    'Pregnant',
    'Sick',
    'Sold',
    'Deceased'
];

function clean(string $value): string
{
    return htmlspecialchars(strip_tags(trim($value)));
}

function cowRedirect(string $type, string $message): void
{
    $_SESSION['cow_' . $type] = $message;

    header('Location: /v4b7n1m88c9e3970c5d8e3f6fa7f7dd9ed3160b37b');
    exit();
}

$cows = [];

$cows_query = "
    SELECT
        id,
        ear_tag AS tag_number,
        cow_name AS name,
        breed,
        gender,
        date_of_birth AS birth_date,
        weight_kg,
        status,
        notes,
        image_path,
        created_at
    FROM cows
    WHERE user_id = ?
    ORDER BY created_at DESC
";

$cows_stmt = $conn->prepare($cows_query);

$cows_stmt->bind_param("i", $user_id);

$cows_stmt->execute();

$cows_result = $cows_stmt->get_result();

while ($row = $cows_result->fetch_assoc()) {
    $cows[] = $row;
}

$cows_stmt->close();

$total_cows = count($cows);

$status_counts = [
    'Active'   => 0,
    'Dry'      => 0,
    'Pregnant' => 0,
    'Sick'     => 0
];

foreach ($cows as $cow) {
    $status = $cow['status'];

    if (isset($status_counts[$status])) {
        $status_counts[$status]++;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = clean($_POST['action'] ?? '');

    if ($action === 'add_cow') {

        $tag = clean($_POST['tag_number'] ?? '');

        $name = clean($_POST['name'] ?? '');

        $breed = clean($_POST['breed'] ?? '');

        $gender = clean($_POST['gender'] ?? '');

        $dob = clean($_POST['birth_date'] ?? '');

        $weight = is_numeric($_POST['weight_kg'] ?? '')
            ? (float) $_POST['weight_kg']
            : null;

        $status = clean($_POST['status'] ?? 'Active');

        $notes = clean($_POST['notes'] ?? '');

        file_put_contents(__DIR__ . '/../../logs/upload_debug.log', date('c') . " UPDATE FILES: " . print_r($_FILES, true) . "
", FILE_APPEND);
	$image_path = null;
        if (isset($_FILES['cow_image']) && $_FILES['cow_image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/../../public/uploads/cows/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $ext = pathinfo($_FILES['cow_image']['name'], PATHINFO_EXTENSION);
            $filename = 'cow_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
            $destination = $upload_dir . $filename;
            if (move_uploaded_file($_FILES['cow_image']['tmp_name'], $destination)) {
                $image_path = '/public/uploads/cows/' . $filename;
            }
        }

        if (!$tag) {
            cowRedirect('error', 'Tag number is required.');
        }

        if (!in_array($breed, ALLOWED_BREEDS)) {
            cowRedirect('error', 'Invalid breed selected.');
        }

        if (!in_array($gender, ALLOWED_GENDERS)) {
            cowRedirect('error', 'Invalid gender selected.');
        }

        if (!in_array($status, ALLOWED_STATUSES)) {
            cowRedirect('error', 'Invalid status selected.');
        }

        if ($dob && !strtotime($dob)) {
            cowRedirect('error', 'Invalid birth date.');
        }
        $check_stmt = $conn->prepare("
            SELECT id
            FROM cows
            WHERE ear_tag = ?
            AND user_id = ?
            LIMIT 1
        ");

        $check_stmt->bind_param("si", $tag, $user_id);

        $check_stmt->execute();

        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            cowRedirect('error', "Tag #{$tag} already exists.");
        }

        $check_stmt->close();

        $dob_value = $dob ?: null;

        $insert_stmt = $conn->prepare("
            INSERT INTO cows (
                user_id,
                farm_id,
                ear_tag,
                cow_name,
                breed,
                gender,
                date_of_birth,
                weight_kg,
                status,
                notes,
                image_path
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $insert_stmt->bind_param(
            "iisssssdsss",
            $user_id,
            $farm_id,
            $tag,
            $name,
            $breed,
            $gender,
            $dob_value,
            $weight,
            $status,
            $notes,
            $image_path
        );

        $insert_stmt->execute();

        $insert_stmt->close();

        cowRedirect('success', "Cow #{$tag} added successfully!");
    }

    if ($action === 'update_cow') {

        $cow_id = (int) ($_POST['cow_id'] ?? 0);

        $tag = clean($_POST['tag_number'] ?? '');

        $name = clean($_POST['name'] ?? '');

        $breed = clean($_POST['breed'] ?? '');

        $gender = clean($_POST['gender'] ?? '');

        $dob = clean($_POST['birth_date'] ?? '');

        $weight = is_numeric($_POST['weight_kg'] ?? '')
            ? (float) $_POST['weight_kg']
            : null;

        $status = clean($_POST['status'] ?? 'Active');

        $notes = clean($_POST['notes'] ?? '');

        file_put_contents(__DIR__ . '/../../logs/upload_debug.log', date('c') . " UPDATE FILES: " . print_r($_FILES, true) . "
", FILE_APPEND);
	$image_path = null;
        if (isset($_FILES['cow_image']) && $_FILES['cow_image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/../../public/uploads/cows/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $ext = pathinfo($_FILES['cow_image']['name'], PATHINFO_EXTENSION);
            $filename = 'cow_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
            $destination = $upload_dir . $filename;
            if (move_uploaded_file($_FILES['cow_image']['tmp_name'], $destination)) {
                $image_path = '/public/uploads/cows/' . $filename;
            }
        }

        if (!$cow_id) {
            cowRedirect('error', 'Invalid cow ID.');
        }

        if (!$tag) {
            cowRedirect('error', 'Tag number is required.');
        }

        if (!in_array($breed, ALLOWED_BREEDS)) {
            cowRedirect('error', 'Invalid breed selected.');
        }

        if (!in_array($gender, ALLOWED_GENDERS)) {
            cowRedirect('error', 'Invalid gender selected.');
        }

        if (!in_array($status, ALLOWED_STATUSES)) {
            cowRedirect('error', 'Invalid status selected.');
        }

        $dob_value = $dob ?: null;

        $update_stmt = $conn->prepare("
            UPDATE cows
            SET
                ear_tag = ?,
                cow_name = ?,
                breed = ?,
                gender = ?,
                date_of_birth = ?,
                weight_kg = ?,
                status = ?,
                notes = ?,
                image_path = COALESCE(?, image_path)
            WHERE id = ?
            AND user_id = ?
        ");

        $update_stmt->bind_param(
            "sssssdsssii",
            $tag,
            $name,
            $breed,
            $gender,
            $dob_value,
            $weight,
            $status,
            $notes,
            $image_path,
            $cow_id,
            $user_id
        );

        $update_stmt->execute();

        $update_stmt->close();

        cowRedirect('success', "Cow #{$tag} updated successfully!");
    }

    if ($action === 'delete_cow') {

        $cow_id = (int) ($_POST['cow_id'] ?? 0);

        if (!$cow_id) {
            cowRedirect('error', 'Invalid cow ID.');
        }

        $delete_stmt = $conn->prepare("
            DELETE FROM cows
            WHERE id = ?
            AND user_id = ?
        ");

        $delete_stmt->bind_param("ii", $cow_id, $user_id);

        $delete_stmt->execute();

        $delete_stmt->close();

        cowRedirect('success', 'Cow removed successfully.');
    }
}

$conn->close();
?>