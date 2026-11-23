<?php
require_once 'config/database.php';
$pageTitle = 'Programs';
trackVisit('programs');
?>
<?php require 'includes/header.php'; ?>

<section class="page-banner">
    <div class="max-w-7xl mx-auto px-4 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-2">Our Programs</h1>
        <p class="text-gray-300 text-lg">Comprehensive training programs for parliamentary excellence</p>
    </div>
</section>

<section class="py-16 md:py-20">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php
            $progs = $pdo->query("SELECT * FROM programs WHERE status='active' ORDER BY featured DESC, id ASC");
            if ($progs->rowCount() == 0):
            ?>
            <div class="col-span-full text-center py-12">
                <i class="fas fa-book-open text-5xl text-gray-300 mb-4"></i>
                <p class="text-gray-500">No programs available at the moment. Please check back later.</p>
            </div>
            <?php endif; ?>
            <?php while ($prog = $progs->fetch()): ?>
            <div class="card p-6 flex flex-col">
                <?php if ($prog['image']): ?>
                <div class="rounded-lg overflow-hidden mb-3 h-40 -mx-6 -mt-6">
                    <img src="<?= $prog['image'] ?>" alt="<?= $prog['title'] ?>" class="w-full h-full object-cover">
                </div>
                <?php endif; ?>
                <div class="flex items-center gap-2 mb-2">
                    <?php if ($prog['featured']): ?>
                    <span class="bg-gold text-dark text-xs font-bold px-2 py-1 rounded">Featured</span>
                    <?php endif; ?>
                    <span class="bg-primary/10 text-primary text-xs px-2 py-1 rounded"><?= $prog['category'] ?></span>
                </div>
                <h3 class="font-semibold text-lg mb-2 text-dark-primary"><?= $prog['title'] ?></h3>
                <p class="text-sm text-gray-500 mb-2"><i class="far fa-clock mr-1"></i> <?= $prog['duration'] ?></p>
                <p class="text-sm text-gray-600 mb-4 flex-grow"><?= $prog['description'] ?></p>
                <div class="flex items-center justify-between mt-auto pt-4 border-t border-gray-100">
                    <a href="program-details.php?slug=<?= $prog['slug'] ?>" class="text-primary font-semibold text-sm hover:text-gold transition">View Details <i class="fas fa-arrow-right ml-1"></i></a>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 text-center">
        <h2 class="section-title center">Why Choose PTI?</h2>
        <div class="grid md:grid-cols-4 gap-6 mt-8">
            <?php
            $why_raw = getSetting('why_choose_pti');
            $why_items = [];
            if ($why_raw) {
                foreach (explode("\n", $why_raw) as $line) {
                    $parts = explode('|', trim($line), 3);
                    if (count($parts) === 3) $why_items[] = ['icon' => trim($parts[0]), 'title' => trim($parts[1]), 'desc' => trim($parts[2])];
                }
            }
            if (empty($why_items)):
                $why_items = [
                    ['icon' => 'fa-certificate', 'title' => 'Accredited Programs', 'desc' => 'All programs are accredited by the relevant professional bodies.'],
                    ['icon' => 'fa-chalkboard-teacher', 'title' => 'Expert Faculty', 'desc' => 'Learn from experienced parliamentarians and academics.'],
                    ['icon' => 'fa-hands-helping', 'title' => 'Practical Approach', 'desc' => 'Hands-on training with real-world parliamentary scenarios.'],
                    ['icon' => 'fa-globe-africa', 'title' => 'Networking', 'desc' => 'Connect with peers across the legislative sector.'],
                ];
            endif;
            $wi = 0; foreach ($why_items as $w):
            ?>
            <div class="p-6">
                <i class="fas <?= $w['icon'] ?> text-4xl <?= $wi % 2 === 0 ? 'text-gold' : 'text-primary' ?> mb-4"></i>
                <h4 class="font-bold mb-2"><?= $w['title'] ?></h4>
                <p class="text-sm text-gray-500"><?= $w['desc'] ?></p>
            </div>
            <?php $wi++; endforeach; ?>
        </div>
    </div>
</section>

<?php require 'includes/footer.php'; ?>
