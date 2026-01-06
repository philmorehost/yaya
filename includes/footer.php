    </div>
    <div class="mobile-footer d-lg-none">
        <a href="members.php" class="footer-icon">
            <i class="fas fa-users"></i>
            <span>Members</span>
        </a>
        <a href="media.php" class="footer-icon">
            <i class="fas fa-video"></i>
            <span>Media</span>
        </a>
        <a href="dashboard.php" class="footer-icon">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
        <a href="finance.php" class="footer-icon">
            <i class="fas fa-wallet"></i>
            <span>Finance</span>
        </a>
        <a href="attendance.php" class="footer-icon">
            <i class="fas fa-check-circle"></i>
            <span>Attendance</span>
        </a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById("menu-toggle").addEventListener("click", function(e) {
            e.preventDefault();
            document.getElementById("wrapper").classList.toggle("toggled");
        });
    </script>
</body>
</html>
