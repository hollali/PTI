<footer class="footer pt-12 pb-0">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8 pb-10">
            <div>
                <h5>About PTI</h5>
                <p class="text-sm leading-relaxed text-white/40 mb-4">The Parliamentary Training Institute (PTI) is dedicated to building the capacity of Members of Parliament and parliamentary staff through high-quality training and professional development programs.</p>
                <div class="flex gap-2">
                    <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
            <div>
                <h5>Quick Links</h5>
                <ul class="space-y-2 text-sm">
                    <?php $stmt = $pdo->query("SELECT * FROM menu_items WHERE parent_id IS NULL AND status='active' ORDER BY sort_order"); while ($item = $stmt->fetch()): ?>
                    <li><a href="<?= $item['link'] ?>" class="hover:pl-1"><?= $item['label'] ?></a></li>
                    <?php endwhile; ?>
                    <li><a href="news.php" class="hover:pl-1">News & Updates</a></li>
                    <li><a href="gallery.php" class="hover:pl-1">Gallery</a></li>
                </ul>
            </div>
            <div>
                <h5>Our Programs</h5>
                <ul class="space-y-2 text-sm">
                    <?php $progs = $pdo->query("SELECT * FROM programs WHERE status='active' LIMIT 5"); while ($p = $progs->fetch()): ?>
                    <li><a href="program-details.php?slug=<?= $p['slug'] ?>" class="hover:pl-1"><?= $p['title'] ?></a></li>
                    <?php endwhile; ?>
                </ul>
            </div>
            <div>
                <h5>Contact Info</h5>
                <div class="space-y-2.5 text-sm">
                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span class="text-white/40"><?= getSetting('site_address') ?></span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <span class="text-white/40"><?= getSetting('site_phone') ?></span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <span class="text-white/40"><?= getSetting('site_email') ?></span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-clock"></i>
                        <span class="text-white/40">Mon-Fri: 8:00 AM - 5:00 PM</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 flex flex-col sm:flex-row items-center justify-between gap-2">
            <div class="text-white/30"><?= getSetting('footer_text') ?></div>
            <div class="flex gap-5 text-sm">
                <a href="#" class="text-white/30 hover:text-white transition">Privacy Policy</a>
                <a href="#" class="text-white/30 hover:text-white transition">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>
<script src="public/js/main.js"></script>
<?php require 'includes/contact-modal.php'; ?>
</body>
</html>
