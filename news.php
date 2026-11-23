<?php
require_once 'config/database.php';
$pageTitle = 'News & Updates';
trackVisit('news');
?>
<?php require 'includes/header.php'; ?>

<section class="page-banner">
    <div class="max-w-7xl mx-auto px-4 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-2">News & Updates</h1>
        <p class="text-gray-300 text-lg">Latest news and announcements from PTI</p>
    </div>
</section>

<section class="py-16">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid md:grid-cols-3 gap-6">
            <?php
            $news = $pdo->query("SELECT * FROM news WHERE status='published' ORDER BY created_at DESC");
            if ($news->rowCount() == 0):
            ?>
            <div class="col-span-full text-center py-12">
                <i class="fas fa-newspaper text-5xl text-gray-300 mb-4"></i>
                <p class="text-gray-500">No news articles yet.</p>
            </div>
            <?php endif; ?>
            <?php while ($item = $news->fetch()): ?>
            <div class="card">
                <div class="bg-dark-primary h-48 flex items-center justify-center text-white"<?= $item['image'] ? ' style="background:url(' . $item['image'] . ') center/cover no-repeat"' : '' ?>>
                    <?php if (!$item['image']): ?><i class="fas fa-newspaper text-5xl opacity-30"></i><?php endif; ?>
                </div>
                <div class="p-6">
                    <p class="text-xs text-gray-400 mb-2"><i class="far fa-calendar mr-1"></i> <?= date('F d, Y', strtotime($item['created_at'])) ?></p>
                    <h3 class="font-semibold text-lg mb-2"><?= $item['title'] ?></h3>
                    <p class="text-sm text-gray-600 mb-4"><?= substr(strip_tags($item['content']), 0, 150) ?>...</p>
                    <a href="news-details.php?slug=<?= $item['slug'] ?>" class="text-primary font-semibold text-sm hover:text-gold transition">Read More <i class="fas fa-arrow-right ml-1"></i></a>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<?php require 'includes/footer.php'; ?>
