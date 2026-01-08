    </div>
    <div class="mobile-footer d-md-none">
        <a href="members.php" class="footer-icon"><i class="fas fa-users"></i><span>Users</span></a>
        <a href="media.php" class="footer-icon"><i class="fas fa-video"></i><span>Media</span></a>
        <a href="dashboard.php" class="footer-icon"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
        <a href="finance.php" class="footer-icon"><i class="fas fa-wallet"></i><span>Finance</span></a>
        <a href="attendance.php" class="footer-icon"><i class="fas fa-user-check"></i><span>Attendance</span></a>
    </div>
    <!-- Summernote requires jQuery and Popper.js, which is in Bootstrap's bundle -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <script>
        document.getElementById("menu-toggle").addEventListener("click", function(e) {
            e.preventDefault();
            document.getElementById("wrapper").classList.toggle("toggled");
        });
    </script>
</body>
</html>
