<?php
require_once 'init.php';
check_permission('manage_members');

$stmt = $pdo->query("SELECT m.*, r.name as role_name FROM members m LEFT JOIN roles r ON m.role_id = r.id ORDER BY m.name");
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=members.csv');

$output = fopen('php://output', 'w');

fputcsv($output, ['Name', 'Member ID', 'Role', 'Phone', 'Email', 'Birthday', 'Gender']);

foreach ($members as $member) {
    fputcsv($output, [
        $member['name'],
        $member['member_id'] ?? '',
        $member['role_name'] ?: 'Member',
        $member['phone'],
        $member['email'],
        $member['birthday'],
        $member['gender']
    ]);
}

fclose($output);
exit;
?>