<?php session_start(); ?>
<?php
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<?php require_once dirname(__DIR__) . '/config.php'; ?>
<?php include dirname(__DIR__) . '/includes/header.php'; ?>

<div class="container mt-5">
    <div class="text-center">
        <h2>Loan Application</h2>
        <p>Take the next step towards your financial goals.</p>
    </div>

    <?php
    if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])) {
        echo '<div class="alert alert-danger" role="alert">';
        foreach ($_SESSION['errors'] as $error) {
            echo '<p>' . $error . '</p>';
        }
        echo '</div>';
        unset($_SESSION['errors']);
    }
    ?>

    <form action="<?php echo BASE_URL; ?>actions/submit_application.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <!-- Step 1: Eligibility Check -->
        <fieldset class="mb-4">
            <legend>Step 1: Eligibility Check</legend>
            <p>Please select your category to see tailored rates.</p>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="hubCategory" id="juniorHub" value="junior" required <?php if (isset($_SESSION['form_data']['hubCategory']) && $_SESSION['form_data']['hubCategory'] == 'junior') echo 'checked'; ?>>
                <label class="form-check-label" for="juniorHub">
                    <strong>Junior Hub (Ages 13-17):</strong> Requires a parent/guardian co-signer.
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="hubCategory" id="nextGenHub" value="nextgen" <?php if (isset($_SESSION['form_data']['hubCategory']) && $_SESSION['form_data']['hubCategory'] == 'nextgen') echo 'checked'; ?>>
                <label class="form-check-label" for="nextGenHub">
                    <strong>NextGen Hub (Ages 18-30):</strong> Focused on education and entrepreneurship.
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="hubCategory" id="legacyHub" value="legacy" <?php if (isset($_SESSION['form_data']['hubCategory']) && $_SESSION['form_data']['hubCategory'] == 'legacy') echo 'checked'; ?>>
                <label class="form-check-label" for="legacyHub">
                    <strong>Legacy Hub (Ages 31+):</strong> Focused on asset acquisition and stability.
                </label>
            </div>
        </fieldset>

        <!-- Step 2: The Application Form -->
        <fieldset>
            <legend>Step 2: Application Details</legend>

            <!-- Personal Information -->
            <div class="mb-3">
                <label for="fullName" class="form-label">Full Name (as it appears on ID)</label>
                <input type="text" class="form-control" id="fullName" name="fullName" required value="<?php echo isset($_SESSION['form_data']['fullName']) ? $_SESSION['form_data']['fullName'] : ''; ?>">
            </div>
            <div class="mb-3">
                <label for="membershipNumber" class="form-label">Cooperative Membership Number</label>
                <input type="text" class="form-control" id="membershipNumber" name="membershipNumber" required value="<?php echo isset($_SESSION['form_data']['membershipNumber']) ? $_SESSION['form_data']['membershipNumber'] : ''; ?>">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" required value="<?php echo isset($_SESSION['form_data']['email']) ? $_SESSION['form_data']['email'] : ''; ?>">
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Phone Number</label>
                <input type="tel" class="form-control" id="phone" name="phone" required value="<?php echo isset($_SESSION['form_data']['phone']) ? $_SESSION['form_data']['phone'] : ''; ?>">
            </div>

            <!-- Loan Details -->
            <div class="mb-3">
                <label for="loanPurpose" class="form-label">Loan Purpose</label>
                <select class="form-select" id="loanPurpose" name="loanPurpose" required>
                    <option selected disabled value="">Choose...</option>
                    <option <?php if (isset($_SESSION['form_data']['loanPurpose']) && $_SESSION['form_data']['loanPurpose'] == 'School fees') echo 'selected'; ?>>School fees</option>
                    <option <?php if (isset($_SESSION['form_data']['loanPurpose']) && $_SESSION['form_data']['loanPurpose'] == 'Laptop/Tools') echo 'selected'; ?>>Laptop/Tools</option>
                    <option <?php if (isset($_SESSION['form_data']['loanPurpose']) && $_SESSION['form_data']['loanPurpose'] == 'Small Business') echo 'selected'; ?>>Small Business</option>
                    <option <?php if (isset($_SESSION['form_data']['loanPurpose']) && $_SESSION['form_data']['loanPurpose'] == 'Home Improvement') echo 'selected'; ?>>Home Improvement</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="loanAmount" class="form-label">Amount Requested ($)</label>
                <input type="number" class="form-control" id="loanAmount" name="loanAmount" required value="<?php echo isset($_SESSION['form_data']['loanAmount']) ? $_SESSION['form_data']['loanAmount'] : ''; ?>">
            </div>
             <div class="mb-3">
                <label class="form-label">Repayment Duration</label>
                <p class="form-control-plaintext">10 months</p>
            </div>

            <!-- Financial Standing -->
            <div class="mb-3">
                <label for="monthlyIncome" class="form-label">Monthly Income/Allowance ($)</label>
                <input type="number" class="form-control" id="monthlyIncome" name="monthlyIncome" required value="<?php echo isset($_SESSION['form_data']['monthlyIncome']) ? $_SESSION['form_data']['monthlyIncome'] : ''; ?>">
            </div>
            <div class="mb-3">
                <label for="existingSavings" class="form-label">Existing Savings in the Hub ($)</label>
                <input type="number" class="form-control" id="existingSavings" name="existingSavings" required value="<?php echo isset($_SESSION['form_data']['existingSavings']) ? $_SESSION['form_data']['existingSavings'] : ''; ?>">
            </div>

            <!-- Guarantor Information -->
            <h5>Guarantor 1</h5>
            <div class="mb-3">
                <label for="guarantor1Name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="guarantor1Name" name="guarantor1Name" required value="<?php echo isset($_SESSION['form_data']['guarantor1Name']) ? $_SESSION['form_data']['guarantor1Name'] : ''; ?>">
            </div>
             <div class="mb-3">
                <label for="guarantor1MemberId" class="form-label">Membership Number</label>
                <input type="text" class="form-control" id="guarantor1MemberId" name="guarantor1MemberId" required value="<?php echo isset($_SESSION['form_data']['guarantor1MemberId']) ? $_SESSION['form_data']['guarantor1MemberId'] : ''; ?>">
            </div>

            <h5>Guarantor 2</h5>
            <div class="mb-3">
                <label for="guarantor2Name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="guarantor2Name" name="guarantor2Name" required value="<?php echo isset($_SESSION['form_data']['guarantor2Name']) ? $_SESSION['form_data']['guarantor2Name'] : ''; ?>">
            </div>
             <div class="mb-3">
                <label for="guarantor2MemberId" class="form-label">Membership Number</label>
                <input type="text" class="form-control" id="guarantor2MemberId" name="guarantor2MemberId" required value="<?php echo isset($_SESSION['form_data']['guarantor2MemberId']) ? $_SESSION['form_data']['guarantor2MemberId'] : ''; ?>">
            </div>

        </fieldset>

        <!-- Call to Action -->
        <div class="text-center mt-4">
            <button type="submit" class="btn btn-primary">Apply Now</button>
            <a href="#" class="btn btn-secondary">Speak to a Financial Mentor</a>
        </div>
    </form>
</div>

<?php
    unset($_SESSION['form_data']);
    include dirname(__DIR__) . '/includes/footer.php';
?>
