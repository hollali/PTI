<?php
require_once 'config/database.php';
$slug = $_GET['slug'] ?? '';
$stmt = $pdo->prepare("SELECT * FROM news WHERE slug = ? AND status='published'");
$stmt->execute([$slug]);
$article = $stmt->fetch();
if (!$article) { header('Location: news.php'); exit; }
$pageTitle = $article['title'];
trackVisit('news/' . $slug);
?>
<?php require 'includes/header.php'; ?>

<section class="page-banner">
    <div class="max-w-7xl mx-auto px-4 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-2"><?= $article['title'] ?></h1>
        <p class="text-gray-300 text-lg"><i class="far fa-calendar mr-1"></i> <?= date('F d, Y', strtotime($article['created_at'])) ?></p>
    </div>
</section>

<section class="py-16">
    <div class="max-w-4xl mx-auto px-4">
        <div class="card p-8 card-p-8">
            <?php if ($article['image']): ?>
            <div class="mb-6 rounded-lg overflow-hidden">
                <img src="<?= $article['image'] ?>" alt="<?= $article['title'] ?>" class="w-full h-64 object-cover">
            </div>
            <?php endif; ?>
            <div class="prose max-w-none text-gray-600 leading-relaxed">
                <?= $article['content'] ?>
            </div>
            <div class="mt-8 pt-6 border-t border-gray-200">
                <a href="news.php" class="text-primary hover:text-gold transition font-semibold"><i class="fas fa-arrow-left mr-2"></i> Back to News</a>
            </div>
        </div>
    </div>
</section>

<?php require 'includes/footer.php'; ?>
