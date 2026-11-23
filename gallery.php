<?php
require_once 'config/database.php';

$page = $pdo->prepare("SELECT * FROM pages WHERE slug = ? AND status = 'published'");
$page->execute(['gallery']);
$galleryPage = $page->fetch();

$pageTitle = $galleryPage ? $galleryPage['title'] : 'Gallery';
trackVisit('gallery');

$categories = $pdo->query("SELECT DISTINCT category FROM gallery WHERE category IS NOT NULL AND category != '' ORDER BY category");
$categories = $categories->fetchAll(PDO::FETCH_COLUMN);

$images = $pdo->query("SELECT * FROM gallery ORDER BY sort_order ASC, created_at DESC");
$images = $images->fetchAll();
?>
<?php require 'includes/header.php'; ?>

<section class="page-banner">
    <div class="max-w-7xl mx-auto px-4 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-2"><?= htmlspecialchars($galleryPage['title'] ?? 'Photo Gallery') ?></h1>
        <p class="text-gray-300 text-lg"><?= htmlspecialchars($galleryPage['meta_description'] ?? 'Moments from our training programs and events') ?></p>
    </div>
</section>

<section class="py-16">
    <div class="max-w-7xl mx-auto px-4">
        <?php if (empty($images)): ?>
        <div class="text-center py-20">
            <div class="w-24 h-24 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-images text-4xl text-gray-300"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-700 mb-2">No Images Yet</h3>
            <p class="text-gray-500">Gallery images will appear here once added.</p>
        </div>
        <?php else: ?>

        <?php if (!empty($categories)): ?>
        <div class="flex flex-wrap justify-center gap-2 mb-10" id="filterTabs">
            <button class="filter-btn active px-5 py-2 rounded-full text-sm font-medium transition" data-filter="all">All</button>
            <?php foreach ($categories as $cat): ?>
            <button class="filter-btn px-5 py-2 rounded-full text-sm font-medium transition" data-filter="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></button>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4" id="galleryGrid">
            <?php foreach ($images as $img): ?>
            <div class="gallery-item card group relative overflow-hidden rounded-xl <?= $img['category'] ? 'cat-'.preg_replace('/[^a-zA-Z0-9]/', '_', $img['category']) : '' ?>" data-category="<?= htmlspecialchars($img['category'] ?? '') ?>">
                <?php if ($img['image_url']): ?>
                <div class="w-full h-48 md:h-56 lg:h-64 bg-cover bg-center transition-transform duration-500 group-hover:scale-110" style="background-image:url(<?= $img['image_url'] ?>)"></div>
                <?php else: ?>
                <div class="w-full h-48 md:h-56 lg:h-64 bg-gray-100 flex items-center justify-center">
                    <i class="fas fa-image text-5xl text-gray-300"></i>
                </div>
                <?php endif; ?>
                <div class="absolute inset-0 bg-gradient-to-t from-dark-primary/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-all duration-300 flex flex-col justify-end p-5 cursor-pointer" onclick="openLightbox(<?= $img['id'] ?>)">
                    <?php if ($img['category']): ?>
                    <span class="text-xs font-medium text-white/70 bg-white/15 backdrop-blur-sm px-2.5 py-1 rounded-full inline-block w-fit mb-2"><?= htmlspecialchars($img['category']) ?></span>
                    <?php endif; ?>
                    <?php if ($img['title']): ?>
                    <h3 class="text-white font-semibold text-sm leading-snug"><?= htmlspecialchars($img['title']) ?></h3>
                    <?php endif; ?>
                    <div class="mt-2 flex items-center gap-1.5 text-white/60 text-xs">
                        <i class="fas fa-expand"></i>
                        <span>View</span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<div id="lightbox" class="fixed inset-0 z-[3000] hidden">
    <div class="absolute inset-0 bg-black/90 backdrop-blur-sm" onclick="closeLightbox()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <button onclick="closeLightbox()" class="absolute top-4 right-4 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition z-10">
            <i class="fas fa-times text-xl"></i>
        </button>
        <button onclick="prevImage()" class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition z-10">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button onclick="nextImage()" class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition z-10">
            <i class="fas fa-chevron-right"></i>
        </button>
        <div class="max-w-5xl max-h-[85vh] w-full text-center" id="lightboxContent">
            <img id="lightboxImg" src="" alt="" class="max-w-full max-h-[75vh] mx-auto rounded-lg shadow-2xl object-contain">
            <div class="mt-4 text-white" id="lightboxInfo">
                <h3 id="lightboxTitle" class="text-lg font-semibold"></h3>
                <p id="lightboxCategory" class="text-sm text-white/60 mt-1"></p>
            </div>
        </div>
    </div>
</div>

<style>
.filter-btn { background: #f3f4f6; color: #6b7280; cursor: pointer; border: none; }
.filter-btn:hover { background: #e5e7eb; color: #374151; }
.filter-btn.active { background: var(--primary); color: #fff; }
.gallery-item { transition: transform 0.3s ease, box-shadow 0.3s ease; }
.gallery-item:hover { transform: translateY(-2px); box-shadow: 0 12px 32px rgba(0,0,0,0.1); }
</style>

<script>
var galleryImages = <?= json_encode($images) ?>;
var currentIndex = 0;

function openLightbox(id) {
    var idx = galleryImages.findIndex(function(img) { return img.id == id; });
    if (idx === -1) return;
    currentIndex = idx;
    showLightboxImage();
    document.getElementById('lightbox').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    document.getElementById('lightbox').classList.add('hidden');
    document.body.style.overflow = '';
}

function showLightboxImage() {
    var img = galleryImages[currentIndex];
    var el = document.getElementById('lightboxImg');
    el.src = img.image_url || '';
    el.alt = img.title || 'Gallery image';
    document.getElementById('lightboxTitle').textContent = img.title || '';
    document.getElementById('lightboxCategory').textContent = img.category ? 'Category: ' + img.category : '';
}

function prevImage() {
    currentIndex = (currentIndex - 1 + galleryImages.length) % galleryImages.length;
    showLightboxImage();
}

function nextImage() {
    currentIndex = (currentIndex + 1) % galleryImages.length;
    showLightboxImage();
}

document.addEventListener('keydown', function(e) {
    if (!document.getElementById('lightbox').classList.contains('hidden')) {
        if (e.key === 'Escape') closeLightbox();
        if (e.key === 'ArrowLeft') prevImage();
        if (e.key === 'ArrowRight') nextImage();
    }
});

document.querySelectorAll('.filter-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.filter-btn').forEach(function(b) { b.classList.remove('active'); });
        this.classList.add('active');
        var filter = this.dataset.filter;
        document.querySelectorAll('.gallery-item').forEach(function(item) {
            if (filter === 'all' || item.dataset.category === filter) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    });
});
</script>

<?php require 'includes/footer.php'; ?>
