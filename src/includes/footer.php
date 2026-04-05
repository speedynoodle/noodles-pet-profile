        </div><!-- /.container -->
    </main>

    <footer class="site-footer">
        <div class="container">
            <p>Made with ❤️ for Jack-Jack &amp; Nagi &nbsp;🐕🐕</p>
            <p class="footer-admin-link">
                <?php if (isAdminLoggedIn()): ?>
                    <a href="/admin/">Admin Dashboard</a> &nbsp;·&nbsp;
                    <a href="/admin/logout.php">Log out</a>
                <?php else: ?>
                    <a href="/admin/login.php">Admin</a>
                <?php endif; ?>
            </p>
        </div>
    </footer>
    <script src="/assets/js/app.js"></script>
</body>
</html>
