<?php
require_once 'config/database.php';
$pageTitle = 'Page Not Found';
?>
<?php require 'includes/header.php'; ?>

<section class="min-h-[60vh] flex items-center justify-center py-20">
    <div class="text-center">
        <h1 class="text-6xl sm:text-8xl font-extrabold text-primary mb-4">404</h1>
        <h2 class="text-xl sm:text-2xl font-bold text-dark-primary mb-2">Page Not Found</h2>
        <p class="text-gray-500 mb-8">The page you are looking for might have been removed or is temporarily unavailable.</p>
        <a href="index.php" class="btn-primary">Back to Home</a>
    </div>
</section>

<?php require 'includes/footer.php'; ?>
