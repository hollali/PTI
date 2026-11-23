<?php
require_once 'config/database.php';
$pageTitle = 'Home';
trackVisit('home');
?>
<?php require 'includes/header.php'; ?>

<!-- Hero -->
<?php
$banners = $pdo->query("SELECT * FROM banners WHERE status='active' ORDER BY sort_order");
$hasBanners = $banners->rowCount() > 0;
if (!$hasBanners):
?>
<section class="hero-section">
    <div class="grid-bg"></div>
    <div class="content max-w-7xl mx-auto px-4 sm:px-6">
        <div class="max-w-3xl py-16 md:py-0">
            <div class="reveal visible">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-white/10 rounded-full border border-white/10 text-white/70 text-xs font-medium tracking-wider uppercase mb-5">
                    <span class="w-1.5 h-1.5 bg-white rounded-full"></span>
                    Official Institute of the Parliament of Ghana
                </div>
            </div>
            <h1 class="text-4xl sm:text-5xl md:text-6xl font-extrabold text-white leading-[1.1] mb-4 reveal visible reveal-d1">
                Parliamentary<br>
                <span style="color:var(--gold)">Training Institute</span>
            </h1>
            <p class="text-base sm:text-lg text-white/60 leading-relaxed max-w-xl mb-7 reveal visible reveal-d2">
                Building the capacity of Parliamentarians and parliamentary staff for effective democratic governance and legislative excellence.
            </p>
            <div class="flex flex-wrap gap-3 reveal visible reveal-d3">
                <a href="programs.php" class="inline-flex items-center gap-2 px-7 py-3 bg-white text-primary font-semibold text-sm rounded-lg hover:bg-gray-100 shadow-lg hover:shadow-xl transition-all duration-200 w-full sm:w-auto">
                    <i class="fas fa-graduation-cap"></i>
                    Explore Programs
                </a>
                <a href="about.php" class="btn-outline-light">
                    <i class="fas fa-info-circle"></i>
                    Learn More
                </a>
            </div>
        </div>
    </div>
    <div class="hero-wave">
        <svg viewBox="0 0 1440 80" preserveAspectRatio="none" style="width:100%;height:60px;">
            <path d="M0,40 C360,80 1080,0 1440,40 L1440,80 L0,80 Z" fill="#F9FAFB"/>
        </svg>
    </div>
</section>
<?php else: ?>
<section class="hero-section hero-carousel" id="heroCarousel">
    <div class="grid-bg"></div>
    <?php $i = 0; while ($b = $banners->fetch()): ?>
    <div class="hero-slide<?= $i === 0 ? ' active' : '' ?>" style="<?= $b['image_url'] ? 'background-image:url(' . $b['image_url'] . ');background-size:cover;background-position:center' : '' ?>">
        <div class="content max-w-7xl mx-auto px-4 sm:px-6">
            <div class="max-w-3xl py-16 md:py-0">
                <div class="reveal visible">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-white/10 rounded-full border border-white/10 text-white/70 text-xs font-medium tracking-wider uppercase mb-5">
                        <span class="w-1.5 h-1.5 bg-white rounded-full"></span>
                        Official Institute of the Parliament of Ghana
                    </div>
                </div>
                <h1 class="text-4xl sm:text-5xl md:text-6xl font-extrabold text-white leading-[1.1] mb-4">
                    <?= nl2br($b['title']) ?>
                </h1>
                <?php if ($b['subtitle']): ?>
                <p class="text-base sm:text-lg text-white/60 leading-relaxed max-w-xl mb-7">
                    <?= $b['subtitle'] ?>
                </p>
                <?php endif; ?>
                <div class="flex flex-wrap gap-3">
                    <a href="<?= $b['link'] ?: 'programs.php' ?>" class="inline-flex items-center gap-2 px-7 py-3 bg-white text-primary font-semibold text-sm rounded-lg hover:bg-gray-100 shadow-lg hover:shadow-xl transition-all duration-200 w-full sm:w-auto">
                        <i class="fas fa-graduation-cap"></i>
                        <?= $b['btn_text'] ?: 'Explore' ?>
                    </a>
                    <a href="about.php" class="btn-outline-light">
                        <i class="fas fa-info-circle"></i>
                        Learn More
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php $i++; endwhile; ?>
    <div class="hero-dots">
        <?php for ($d = 0; $d < $i; $d++): ?>
        <span class="dot<?= $d === 0 ? ' active' : '' ?>" data-index="<?= $d ?>"></span>
        <?php endfor; ?>
    </div>
    <div class="hero-wave">
        <svg viewBox="0 0 1440 80" preserveAspectRatio="none" style="width:100%;height:60px;">
            <path d="M0,40 C360,80 1080,0 1440,40 L1440,80 L0,80 Z" fill="#F9FAFB"/>
        </svg>
    </div>
</section>
<script>
(function(){
    var slides = document.querySelectorAll('#heroCarousel .hero-slide');
    var dots = document.querySelectorAll('#heroCarousel .dot');
    if (!slides.length) return;
    var current = 0, total = slides.length, timer;

    function goTo(idx) {
        slides.forEach(function(s) { s.classList.remove('active'); });
        dots.forEach(function(d) { d.classList.remove('active'); });
        slides[idx].classList.add('active');
        dots[idx].classList.add('active');
        current = idx;
    }

    function next() { goTo((current + 1) % total); }

    function resetTimer() {
        clearInterval(timer);
        if (total > 1) timer = setInterval(next, 6000);
    }

    dots.forEach(function(d) {
        d.addEventListener('click', function() {
            goTo(parseInt(this.dataset.index));
            resetTimer();
        });
    });

    resetTimer();
})();
</script>
<?php endif; ?>

<!-- Stats -->
<section class="py-12 md:py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="card-app grid grid-cols-2 md:grid-cols-4 divide-x divide-y md:divide-y-0 divide-gray-100">
            <?php
            $stats_raw = getSetting('home_stats');
            $stats = [];
            if ($stats_raw) {
                foreach (explode("\n", $stats_raw) as $line) {
                    $parts = explode('|', trim($line), 2);
                    if (count($parts) === 2) $stats[] = ['num' => trim($parts[0]), 'label' => trim($parts[1])];
                }
            }
            if (empty($stats)):
                $stats = [['num' => '15+', 'label' => 'Years of Excellence'], ['num' => '500+', 'label' => 'Parliamentarians Trained'], ['num' => '40+', 'label' => 'Expert Facilitators'], ['num' => '8', 'label' => 'Core Programs']];
            endif;
            foreach ($stats as $i => $s):
            ?>
            <div class="stat-card-app reveal" style="transition-delay: <?= $i * 0.1 ?>s">
                <div class="number"><?= $s['num'] ?></div>
                <div class="label"><?= $s['label'] ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- About -->
<section class="py-16 md:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="grid md:grid-cols-2 gap-12 items-center">
            <div class="reveal">
                <div class="section-tag">About the Institute</div>
                <h2 class="section-title mb-5">Welcome to the <span class="highlight">Parliamentary Training Institute</span></h2>
                <p class="text-gray-500 leading-relaxed mb-6 text-[15px]">
                    The Parliamentary Training Institute (PTI) is the premier institution for parliamentary capacity development in Ghana, established to equip Members of Parliament and parliamentary staff with the skills and knowledge needed for effective legislative service.
                </p>
                <div class="space-y-2.5 mb-7">
                    <div class="flex items-start gap-3">
                        <div class="w-5 h-5 bg-applight rounded flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fas fa-check text-primary text-[10px]"></i>
                        </div>
                        <span class="text-sm text-gray-600">Comprehensive training programs tailored for parliamentary excellence</span>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-5 h-5 bg-applight rounded flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fas fa-check text-primary text-[10px]"></i>
                        </div>
                        <span class="text-sm text-gray-600">Expert facilitators with extensive parliamentary experience</span>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-5 h-5 bg-applight rounded flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fas fa-check text-primary text-[10px]"></i>
                        </div>
                        <span class="text-sm text-gray-600">State-of-the-art learning facilities and resources</span>
                    </div>
                </div>
                <a href="about.php" class="btn-primary text-sm">
                    Learn More About Us
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <div class="reveal reveal-d2">
                <div class="card-app p-8 text-center">
                    <div class="w-16 h-16 bg-applight rounded-xl flex items-center justify-center mx-auto mb-5">
                        <i class="fas fa-landmark text-2xl text-primary"></i>
                    </div>
                    <blockquote class="text-lg italic text-gray-500 leading-relaxed mb-3">
                        "Excellence in Parliamentary Capacity Development"
                    </blockquote>
                    <div class="w-10 h-0.5 bg-gray-200 mx-auto"></div>
                    <p class="text-sm text-gray-400 mt-3">— Our Mission</p>
                </div>
            </div>
        </div>

        <?php
        $page = $pdo->prepare("SELECT * FROM pages WHERE slug = ? AND status = 'published'");
        $page->execute(['home']);
        $content = $page->fetch();
        $defaultContent = 'Welcome to the Parliamentary Training InstituteExcellence in Parliamentary Capacity Development';
        if ($content && trim(strip_tags($content['content'])) !== $defaultContent):
        ?>
        <div class="prose max-w-none mt-14 pt-14 border-t border-gray-100 reveal"><?= $content['content'] ?></div>
        <?php endif; ?>
    </div>
</section>

<!-- Programs -->
<section class="py-16 md:py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-5 mb-10">
            <div class="reveal">
                <div class="section-tag">Our Programs</div>
                <h2 class="section-title">Flagship <span class="highlight">Training Programs</span></h2>
            </div>
            <a href="programs.php" class="btn-ghost reveal reveal-d2">
                View All Programs <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-5">
            <?php
            $progs = $pdo->query("SELECT * FROM programs WHERE status='active' AND featured=1 ORDER BY id LIMIT 4");
            $i = 0;
            while ($prog = $progs->fetch()):
            ?>
            <div class="card-app program-card-app reveal" style="transition-delay: <?= $i * 0.1 ?>s">
                <?php if ($prog['image']): ?>
                <div class="rounded-lg overflow-hidden mb-4 h-40 -mx-6 -mt-7">
                    <img src="<?= $prog['image'] ?>" alt="<?= $prog['title'] ?>" class="w-full h-full object-cover">
                </div>
                <?php else: ?>
                <div class="icon-wrap"><i class="fas fa-graduation-cap"></i></div>
                <?php endif; ?>
                <h3><?= $prog['title'] ?></h3>
                <div class="duration"><i class="far fa-clock mr-1"></i> <?= $prog['duration'] ?></div>
                <p><?= $prog['description'] ?></p>
                <a href="program-details.php?slug=<?= $prog['slug'] ?>" class="btn-ghost mt-3 text-sm">
                    Learn More <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <?php $i++; endwhile; ?>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-section py-20 md:py-24">
    <div class="grid-bg"></div>
    <div class="content max-w-7xl mx-auto px-4 sm:px-6 text-center">
        <div class="max-w-2xl mx-auto reveal">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-white/10 rounded-full border border-white/10 text-white/70 text-xs font-medium tracking-wider uppercase mb-5">
                <span class="w-1.5 h-1.5 bg-white rounded-full"></span>
                Get Involved
            </div>
            <h2 class="text-3xl md:text-4xl font-extrabold text-white leading-tight mb-3">
                Empowering Democratic Governance
            </h2>
            <p class="text-base text-white/60 max-w-xl mx-auto mb-7 leading-relaxed">
                Join the premier institution dedicated to building parliamentary capacity in Ghana. Equip yourself with the knowledge and skills for effective legislative service.
            </p>
            <div class="flex flex-wrap justify-center gap-3">
                <button onclick="openContactModal()" class="inline-flex items-center gap-2 px-7 py-3 bg-white text-primary font-semibold text-sm rounded-lg hover:bg-gray-100 shadow-lg hover:shadow-xl transition-all duration-200 cursor-pointer w-full sm:w-auto">
                    <i class="fas fa-paper-plane"></i>
                    Get in Touch
                </button>
                <a href="programs.php" class="btn-outline-light">
                    <i class="fas fa-arrow-right"></i>
                    Browse Programs
                </a>
            </div>
        </div>
    </div>
</section>

<!-- News -->
<section class="py-16 md:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-5 mb-10">
            <div class="reveal">
                <div class="section-tag">News & Updates</div>
                <h2 class="section-title">Latest from <span class="highlight">PTI</span></h2>
            </div>
            <a href="news.php" class="btn-ghost reveal reveal-d2">
                View All News <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <div class="grid md:grid-cols-3 gap-5">
            <?php
            $news = $pdo->query("SELECT * FROM news WHERE status='published' ORDER BY created_at DESC LIMIT 3");
            $i = 0;
            while ($item = $news->fetch()):
            ?>
            <div class="card-app news-card-app reveal" style="transition-delay: <?= $i * 0.15 ?>s">
                <div class="img-wrap"<?= $item['image'] ? ' style="background-image:url(' . $item['image'] . ');background-size:cover;background-position:center"' : '' ?>>
                    <?php if (!$item['image']): ?><i class="fas fa-newspaper"></i><?php endif; ?>
                    <div class="date-badge">
                        <i class="far fa-calendar mr-1.5"></i><?= date('M d, Y', strtotime($item['created_at'])) ?>
                    </div>
                </div>
                <div class="body">
                    <h3><?= $item['title'] ?></h3>
                    <p><?= substr(strip_tags($item['content']), 0, 150) ?>...</p>
                    <a href="news-details.php?slug=<?= $item['slug'] ?>" class="btn-ghost text-sm mt-2">
                        Read More <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
            <?php $i++; endwhile; ?>
        </div>
    </div>
</section>

<button id="scrollTop"><i class="fas fa-arrow-up"></i></button>

<?php require 'includes/footer.php'; ?>
