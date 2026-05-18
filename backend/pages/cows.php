<?php
// /farm-management/backend/pages/cows.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header('Location: /farm-management/h3j5n8q1');
    exit();
}

require_once __DIR__ . '/../config/database.php';

$conn = getDatabase();

$user_id = (int) $_SESSION['user_id'];

$farm_stmt = $conn->prepare("
    SELECT id
    FROM farms
    WHERE user_id = ?
    LIMIT 1
");

$farm_stmt->bind_param("i", $user_id);
$farm_stmt->execute();

$farm_result = $farm_stmt->get_result();
$farm = $farm_result->fetch_assoc();

if (!$farm) {
    die('No farm found for this user.');
}

$farm_id = (int) $farm['id'];

$farm_stmt->close();

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

    header('Location: /farm-management/v4b7n1m8');
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
                notes
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $insert_stmt->bind_param(
            "iisssssdss",
            $user_id,
            $farm_id,
            $tag,
            $name,
            $breed,
            $gender,
            $dob_value,
            $weight,
            $status,
            $notes
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
                notes = ?
            WHERE id = ?
            AND user_id = ?
        ");

        $update_stmt->bind_param(
            "sssssdssii",
            $tag,
            $name,
            $breed,
            $gender,
            $dob_value,
            $weight,
            $status,
            $notes,
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