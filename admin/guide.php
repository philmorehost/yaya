<?php
require_once 'init.php';
// This page should be accessible to all logged-in admin users.
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="main-content">
    <div class="container-fluid">
        <h2 class="mb-4">Admin Guide</h2>
        <p class="mb-4">This guide provides instructions on how to manage the various sections of the website using the admin panel. Click on any section below to expand it and view the details.</p>

        <div class="accordion" id="adminGuideAccordion">

            <!-- Dashboard Section -->
            <div class="accordion-item bg-dark">
                <h2 class="accordion-header" id="headingDashboard">
                    <button class="accordion-button bg-secondary text-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDashboard" aria-expanded="true" aria-controls="collapseDashboard">
                        Dashboard Overview
                    </button>
                </h2>
                <div id="collapseDashboard" class="accordion-collapse collapse show" aria-labelledby="headingDashboard" data-bs-parent="#adminGuideAccordion">
                    <div class="accordion-body text-light">
                        The <strong>Dashboard</strong> is the first page you see after logging in. It provides a quick overview of your website's activity, including:
                        <ul>
                            <li>High-level statistics in summary cards.</li>
                            <li>Tables showing recent members, media uploads, and announcements.</li>
                        </ul>
                        It's your central hub for monitoring the latest updates.
                    </div>
                </div>
            </div>

            <!-- Members Section -->
            <div class="accordion-item bg-dark">
                <h2 class="accordion-header" id="headingMembers">
                    <button class="accordion-button collapsed bg-secondary text-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapseMembers" aria-expanded="false" aria-controls="collapseMembers">
                        Managing Members
                    </button>
                </h2>
                <div id="collapseMembers" class="accordion-collapse collapse" aria-labelledby="headingMembers" data-bs-parent="#adminGuideAccordion">
                    <div class="accordion-body text-light">
                        The <strong>Members</strong> page allows you to manage all user accounts. From here you can:
                        <ul>
                            <li><strong>Add a Member:</strong> Click the "Add Member" button and fill in the form. A default password ('password') is set, and the member can change it later. A unique Member ID is generated automatically.</li>
                            <li><strong>Edit a Member:</strong> Click the "Edit" button next to a member's name to update their personal information or assign them a specific role.</li>
                            <li><strong>Import/Export:</strong> Use the "Export to CSV" button to download a list of all members. Use the "Import from CSV" button to bulk-upload new members. The CSV must have the correct column order.</li>
                            <li><strong>Login as Member:</strong> The "Login as Member" button allows a Super Admin to impersonate a member to see the site from their perspective or troubleshoot issues. A bar will appear at the top allowing you to switch back to your admin account.</li>
                            <li><strong>Delete a Member:</strong> Click the "Delete" button to permanently remove a member. This action cannot be undone.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Roles & Permissions Section -->
            <div class="accordion-item bg-dark">
                <h2 class="accordion-header" id="headingRoles">
                    <button class="accordion-button collapsed bg-secondary text-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapseRoles" aria-expanded="false" aria-controls="collapseRoles">
                        Roles & Permissions
                    </button>
                </h2>
                <div id="collapseRoles" class="accordion-collapse collapse" aria-labelledby="headingRoles" data-bs-parent="#adminGuideAccordion">
                    <div class="accordion-body text-light">
                        This section allows you to control what different types of users can do in the admin panel.
                        <ul>
                            <li><strong>Manage Roles:</strong> On the <strong>Roles</strong> page, you can create new roles (e.g., "Pastor", "Accountant", "Media Team").</li>
                            <li><strong>Edit Permissions:</strong> After creating a role, click "Edit Permissions" to grant access to specific pages. For example, you can give the "Accountant" role access to the Finance pages while restricting them from managing members.</li>
                            <li><strong>Assigning Roles:</strong> To assign a role to a user, go to the <strong>Members</strong> page, edit a member, and select the desired role from the dropdown menu.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Departments Section -->
            <div class="accordion-item bg-dark">
                <h2 class="accordion-header" id="headingDepts">
                    <button class="accordion-button collapsed bg-secondary text-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDepts" aria-expanded="false" aria-controls="collapseDepts">
                        Managing Departments
                    </button>
                </h2>
                <div id="collapseDepts" class="accordion-collapse collapse" aria-labelledby="headingDepts" data-bs-parent="#adminGuideAccordion">
                    <div class="accordion-body text-light">
                        You can organize members into departments (e.g., "Choir", "Ushering").
                        <ul>
                            <li><strong>Create Departments:</strong> Go to the <strong>Departments</strong> page to add, edit, or delete departments.</li>
                            <li><strong>Assign Heads of Department:</strong> When creating or editing a department, you can assign a member to be the Department Head. Only members with a specific role (not the default 'Member' role) can be assigned as heads.</li>
                            <li><strong>Manage Applications:</strong> Members can apply to join departments from their dashboard. As an admin, you can view all applications on the <strong>Department Applications</strong> page and choose to approve or reject them.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Content Management Section -->
            <div class="accordion-item bg-dark">
                <h2 class="accordion-header" id="headingContent">
                    <button class="accordion-button collapsed bg-secondary text-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapseContent" aria-expanded="false" aria-controls="collapseContent">
                        Content Management (Media, Announcements, Events)
                    </button>
                </h2>
                <div id="collapseContent" class="accordion-collapse collapse" aria-labelledby="headingContent" data-bs-parent="#adminGuideAccordion">
                    <div class="accordion-body text-light">
                        These pages control the content visible to your members and the public.
                        <ul>
                            <li><strong>Media:</strong> Manage video/audio sermons. You can add new media, including titles, descriptions, and embed links (e.g., from YouTube).</li>
                            <li><strong>Announcements:</strong> Create and manage announcements that appear on the member dashboard and homepage.</li>
                            <li><strong>Events:</strong> Keep your members informed about upcoming events.</li>
                            <li><strong>Rich Text Editor:</strong> All content pages use a powerful text editor that allows for formatting, links, and direct image uploads. You can drag and drop images into the editor to upload them.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Financial Management Section -->
            <div class="accordion-item bg-dark">
                <h2 class="accordion-header" id="headingFinance">
                    <button class="accordion-button collapsed bg-secondary text-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFinance" aria-expanded="false" aria-controls="collapseFinance">
                        Financial Management
                    </button>
                </h2>
                <div id="collapseFinance" class="accordion-collapse collapse" aria-labelledby="headingFinance" data-bs-parent="#adminGuideAccordion">
                    <div class="accordion-body text-light">
                        The finance section helps you track giving and generate reports.
                        <ul>
                            <li><strong>Finance Page:</strong> View a detailed list of all financial transactions (offerings, tithes, etc.).</li>
                            <li><strong>Financial Report:</strong> Generate and view summary reports for different giving types over specific date ranges.</li>
                            <li><strong>Giving Accounts:</strong> Manage the bank account details that are displayed to members on the "Give" page. You can add, edit, or remove accounts here.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- System Settings Section -->
            <div class="accordion-item bg-dark">
                <h2 class="accordion-header" id="headingSettings">
                    <button class="accordion-button collapsed bg-secondary text-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSettings" aria-expanded="false" aria-controls="collapseSettings">
                        System Settings
                    </button>
                </h2>
                <div id="collapseSettings" class="accordion-collapse collapse" aria-labelledby="headingSettings" data-bs-parent="#adminGuideAccordion">
                    <div class="accordion-body text-light">
                        Configure core website functionality from these pages.
                        <ul>
                            <li><strong>Homepage Settings:</strong> Control the content displayed on the public-facing homepage, such as the main welcome message and video background.</li>
                            <li><strong>SMTP Settings:</strong> Set up your email server credentials here. This is required for the website to send emails, such as for password resets.</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

<?php
require_once '../includes/footer.php';
?>
