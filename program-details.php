<?php
require_once 'config/database.php';
$slug = $_GET['slug'] ?? '';
$stmt = $pdo->prepare("SELECT * FROM programs WHERE slug = ? AND status='active'");
$stmt->execute([$slug]);
$prog = $stmt->fetch();
if (!$prog) { header('Location: programs.php'); exit; }
$pageTitle = $prog['title'];
trackVisit('program/' . $slug);
?>
<?php require 'includes/header.php'; ?>

<section class="page-banner">
    <div class="max-w-7xl mx-auto px-4 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-2"><?= $prog['title'] ?></h1>
        <p class="text-gray-300 text-lg"><?= $prog['duration'] ?></p>
    </div>
</section>

<section class="py-16">
    <div class="max-w-4xl mx-auto px-4">
        <div class="card p-8 card-p-8">
            <?php if ($prog['image']): ?>
            <div class="rounded-lg overflow-hidden mb-6 -mx-8 -mt-8">
                <img src="<?= $prog['image'] ?>" alt="<?= $prog['title'] ?>" class="w-full h-64 object-cover">
            </div>
            <?php endif; ?>
            <div class="flex flex-wrap gap-2 mb-6">
                <span class="bg-primary/10 text-primary text-sm px-3 py-1 rounded"><?= $prog['category'] ?></span>
                <span class="bg-gold/20 text-dark text-sm px-3 py-1 rounded"><i class="far fa-clock mr-1"></i> <?= $prog['duration'] ?></span>
            </div>
            <?php if ($prog['fee'] > 0): ?>
            <p class="text-2xl font-bold text-primary mb-6">GHS <?= number_format($prog['fee'], 2) ?></p>
            <?php endif; ?>
            <div class="prose max-w-none text-gray-600 leading-relaxed">
                <?= $prog['content'] ?: '<p>' . $prog['description'] . '</p>' ?>
            </div>
            <div class="mt-8 pt-6 border-t border-gray-200">
                <a href="contact.php" class="btn-primary text-sm">Enroll Now <i class="fas fa-arrow-right ml-2"></i></a>
            </div>
        </div>
    </div>
</section>

<?php require 'includes/footer.php'; ?>
