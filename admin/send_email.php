<?php
require_once 'init.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../includes/phpmailer/src/Exception.php';
require '../includes/phpmailer/src/PHPMailer.php';
require '../includes/phpmailer/src/SMTP.php';

if (!check_permission('send_email')) {
    $_SESSION['error_message'] = 'You do not have permission to access this page.';
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once '../includes/csrf_check.php';

    $recipient_type = $_POST['recipient_type'];
    $subject = $_POST['subject'];
    $body = $_POST['body'];
    $emails = [];

    if ($recipient_type == 'all_members') {
        $stmt = $pdo->query("SELECT email FROM members WHERE email IS NOT NULL AND email != ''");
        $emails = $stmt->fetchAll(PDO::FETCH_COLUMN);
    } elseif ($recipient_type == 'single_member') {
        $member_id = $_POST['member_id'];
        $stmt = $pdo->prepare("SELECT email FROM members WHERE id = ? AND email IS NOT NULL AND email != ''");
        $stmt->execute([$member_id]);
        $email = $stmt->fetchColumn();
        if ($email) {
            $emails[] = $email;
        }
    } elseif ($recipient_type == 'all_departments') {
        $stmt = $pdo->query("SELECT DISTINCT m.email FROM members m JOIN department_members dm ON m.id = dm.member_id WHERE m.email IS NOT NULL AND m.email != ''");
        $emails = $stmt->fetchAll(PDO::FETCH_COLUMN);
    } elseif ($recipient_type == 'single_department') {
        $department_id = $_POST['department_id'];
        $stmt = $pdo->prepare("SELECT m.email FROM members m JOIN department_members dm ON m.id = dm.member_id WHERE dm.department_id = ? AND m.email IS NOT NULL AND m.email != ''");
        $stmt->execute([$department_id]);
        $emails = $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    if (empty($emails)) {
        $_SESSION['error_message'] = 'No recipients found for the selected option.';
    } else {
        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host       = get_setting('smtp_host');
            $mail->SMTPAuth   = true;
            $mail->Username   = get_setting('smtp_user');
            $mail->Password   = get_setting('smtp_pass');
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = get_setting('smtp_port');

            //Recipients
            $from_email = get_setting('smtp_from_email');
            $from_name = get_setting('smtp_from_name');
            $mail->setFrom($from_email, $from_name);

            if (count($emails) > 1) {
                // For bulk emails, add recipients as BCC to protect privacy
                $mail->addAddress($from_email, $from_name); // Send the email to the sender itself
                foreach ($emails as $email) {
                    $mail->addBCC($email);
                }
            } else {
                // For single emails, add the recipient directly
                $mail->addAddress($emails[0]);
            }

            //Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            $mail->send();
            $_SESSION['success_message'] = 'Email has been sent successfully.';
        } catch (Exception $e) {
            $_SESSION['error_message'] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
    header('Location: send_email.php');
    exit;
}

require_once '../includes/header.php';
require_once '../includes/sidebar.php';

// Fetch members and departments for filtering
$members = $pdo->query("SELECT id, name, email FROM members ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
$departments = $pdo->query("SELECT id, name FROM departments ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="main-content">
    <div class="container-fluid">
        <h2 class="mb-4">Send Email</h2>

        <?php if(isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
        <?php endif; ?>
        <?php if(isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form method="post" action="send_email.php">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="mb-3">
                        <label for="recipient_type" class="form-label">Recipient</label>
                        <select class="form-select" id="recipient_type" name="recipient_type">
                            <option value="all_members">All Members</option>
                            <option value="single_member">Single Member</option>
                            <option value="all_departments">All Departments</option>
                            <option value="single_department">Single Department</option>
                        </select>
                    </div>

                    <div class="mb-3" id="single_member_select" style="display: none;">
                        <label for="member_id" class="form-label">Select Member</label>
                        <select class="form-select" id="member_id" name="member_id">
                            <?php foreach ($members as $member): ?>
                                <option value="<?php echo $member['id']; ?>"><?php echo htmlspecialchars($member['name']); ?> (<?php echo htmlspecialchars($member['email']); ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3" id="single_department_select" style="display: none;">
                        <label for="department_id" class="form-label">Select Department</label>
                        <select class="form-select" id="department_id" name="department_id">
                            <?php foreach ($departments as $department): ?>
                                <option value="<?php echo $department['id']; ?>"><?php echo htmlspecialchars($department['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="subject" class="form-label">Subject</label>
                        <input type="text" class="form-control" id="subject" name="subject" required>
                    </div>

                    <div class="mb-3">
                        <label for="body" class="form-label">Body</label>
                        <textarea class="form-control" id="body" name="body" rows="10"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Send Email</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
<script>
    CKEDITOR.replace('body');

    document.getElementById('recipient_type').addEventListener('change', function() {
        var singleMemberSelect = document.getElementById('single_member_select');
        var singleDepartmentSelect = document.getElementById('single_department_select');
        if (this.value === 'single_member') {
            singleMemberSelect.style.display = 'block';
            singleDepartmentSelect.style.display = 'none';
        } else if (this.value === 'single_department') {
            singleMemberSelect.style.display = 'none';
            singleDepartmentSelect.style.display = 'block';
        } else {
            singleMemberSelect.style.display = 'none';
            singleDepartmentSelect.style.display = 'none';
        }
    });
</script>

<?php require_once '../includes/footer.php'; ?>
