<?php
require_once 'config/database.php';
$pageTitle = 'Contact Us';
trackVisit('contact');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $subject = sanitize($_POST['subject'] ?? '');
    $message = sanitize($_POST['message'] ?? '');
    if ($name && $email && $message) {
        $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $subject, $message]);
        header('Location: contact.php?sent=1');
        exit;
    } else {
        header('Location: contact.php?error=1');
        exit;
    }
}
?>
<?php require 'includes/header.php'; ?>

<section class="page-banner">
    <div class="max-w-7xl mx-auto px-4 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-2">Contact Us</h1>
        <p class="text-gray-300 text-lg">Get in touch with the Parliamentary Training Institute</p>
    </div>
</section>

<section class="py-16 md:py-20">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid md:grid-cols-2 gap-12">
            <div>
                <h2 class="section-title">Send Us a Message</h2>
                <?php if (isset($_GET['sent'])): ?>
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center gap-3">
                    <i class="fas fa-check-circle text-green-500"></i>
                    <span>Thank you for your message. We will get back to you shortly.</span>
                </div>
                <?php endif; ?>
                <?php if (isset($_GET['error'])): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center gap-3">
                    <i class="fas fa-exclamation-circle text-red-500"></i>
                    <span>Please fill in all required fields.</span>
                </div>
                <?php endif; ?>
                <form method="POST" class="space-y-5">
                    <div class="grid md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Full Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary outline-none transition text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary outline-none transition text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Subject</label>
                            <input type="text" name="subject" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary outline-none transition text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Message <span class="text-red-500">*</span></label>
                        <textarea name="message" rows="5" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary outline-none transition text-sm resize-none"></textarea>
                    </div>
                    <button type="submit" class="btn-primary">Send Message <i class="fas fa-paper-plane ml-2"></i></button>
                </form>
            </div>
            <div>
                <h2 class="section-title">Get in Touch</h2>
                <div class="space-y-6">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-primary text-white rounded-xl flex items-center justify-center text-lg flex-shrink-0"><i class="fas fa-map-marker-alt"></i></div>
                        <div><h4 class="font-semibold text-dark-primary">Our Location</h4><p class="text-sm text-gray-600 mt-1"><?= getSetting('site_address') ?></p></div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-primary text-white rounded-xl flex items-center justify-center text-lg flex-shrink-0"><i class="fas fa-phone-alt"></i></div>
                        <div><h4 class="font-semibold text-dark-primary">Phone</h4><p class="text-sm text-gray-600 mt-1"><?= getSetting('site_phone') ?></p></div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-primary text-white rounded-xl flex items-center justify-center text-lg flex-shrink-0"><i class="fas fa-envelope"></i></div>
                        <div><h4 class="font-semibold text-dark-primary">Email</h4><p class="text-sm text-gray-600 mt-1"><?= getSetting('site_email') ?></p></div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-primary text-white rounded-xl flex items-center justify-center text-lg flex-shrink-0"><i class="fas fa-clock"></i></div>
                        <div><h4 class="font-semibold text-dark-primary">Working Hours</h4><p class="text-sm text-gray-600 mt-1"><?= getSetting('working_hours') ?: 'Mon-Fri: 8:00 AM - 5:00 PM' ?></p></div>
                    </div>
                </div>
                <div class="mt-8 p-4 md:p-6 bg-gray-50 rounded-xl">
                    <h4 class="font-semibold text-dark-primary mb-3">Follow Us</h4>
                    <div class="flex gap-3">
                        <a href="#" class="w-10 h-10 bg-primary text-white rounded-xl flex items-center justify-center hover:bg-primary-dark transition"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="w-10 h-10 bg-primary text-white rounded-xl flex items-center justify-center hover:bg-primary-dark transition"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="w-10 h-10 bg-primary text-white rounded-xl flex items-center justify-center hover:bg-primary-dark transition"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="w-10 h-10 bg-primary text-white rounded-xl flex items-center justify-center hover:bg-primary-dark transition"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require 'includes/footer.php'; ?>
