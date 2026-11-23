<?php
require_once 'config/database.php';

$page = $pdo->prepare("SELECT * FROM pages WHERE slug = ? AND status = 'published'");
$page->execute(['about']);
$aboutContent = $page->fetch();

$pageTitle = $aboutContent ? $aboutContent['title'] : 'About Us';
trackVisit('about');
?>
<?php require 'includes/header.php'; ?>

<section class="page-banner">
    <div class="max-w-7xl mx-auto px-4 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-2"><?= htmlspecialchars($aboutContent['title'] ?? 'About Us') ?></h1>
        <p class="text-gray-300 text-lg"><?= htmlspecialchars($aboutContent['meta_description'] ?? 'Learn about the Parliamentary Training Institute') ?></p>
    </div>
</section>

<section class="py-16 md:py-20">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid md:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="section-title">Who We Are</h2>
                <?php if ($aboutContent && trim(strip_tags($aboutContent['content'])) !== 'About the InstituteAbout the InstituteThe Parliamentary Training Institute (PTI) is the premier institution for parliamentary capacity building in Ghana.'): ?>
                <div class="prose max-w-none text-gray-600 leading-relaxed"><?= $aboutContent['content'] ?></div>
                <?php else: ?>
                <p class="text-gray-600 mb-4 leading-relaxed">The Parliamentary Training Institute (PTI) is the capacity development arm of the Parliament of Ghana. Established to provide high-quality training and professional development for Members of Parliament, parliamentary staff, and stakeholders in the legislative process.</p>
                <p class="text-gray-600 mb-6 leading-relaxed">Our mission is to enhance the effectiveness of Parliament through targeted training programs, research, and knowledge sharing. We are committed to fostering excellence in parliamentary practice and democratic governance.</p>
                <div class="grid grid-cols-2 gap-4">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-check-circle text-green-primary text-xl"></i>
                        <span class="text-sm font-medium">Expert Facilitators</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <i class="fas fa-check-circle text-green-primary text-xl"></i>
                        <span class="text-sm font-medium">Modern Curriculum</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <i class="fas fa-check-circle text-green-primary text-xl"></i>
                        <span class="text-sm font-medium">Hands-on Training</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <i class="fas fa-check-circle text-green-primary text-xl"></i>
                        <span class="text-sm font-medium">Certification</span>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <div class="bg-gray-100 rounded-lg h-64 md:h-96 flex items-center justify-center">
                <i class="fas fa-landmark text-8xl text-gray-300"></i>
            </div>
        </div>
    </div>
</section>

<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="section-title center">Our Leadership</h2>
            <p class="text-gray-600">Meet the dedicated team behind the Parliamentary Training Institute</p>
        </div>
        <div class="grid md:grid-cols-3 gap-8">
            <?php
            $leaders_raw = getSetting('about_leadership');
            $leaders = [];
            if ($leaders_raw) {
                foreach (explode("\n", $leaders_raw) as $line) {
                    $parts = explode('|', trim($line), 3);
                    if (count($parts) === 3) $leaders[] = ['name' => trim($parts[0]), 'role' => trim($parts[1]), 'icon' => trim($parts[2])];
                }
            }
            if (empty($leaders)):
                $leaders = [['name' => 'Dr. Emmanuel Asante', 'role' => 'Director', 'icon' => 'fa-user-tie'], ['name' => 'Mrs. Akua Mensah', 'role' => 'Deputy Director', 'icon' => 'fa-user-tie'], ['name' => 'Mr. Kwame Osei', 'role' => 'Head of Programs', 'icon' => 'fa-chalkboard-teacher']];
            endif;
            foreach ($leaders as $l):
            ?>
            <div class="card team-card">
                <div class="bg-gray-100 h-48 md:h-64 flex items-center justify-center">
                    <i class="fas <?= $l['icon'] ?> text-7xl text-gray-300"></i>
                </div>
                <div class="info">
                    <h4><?= $l['name'] ?></h4>
                    <p><?= $l['role'] ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="py-16">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid md:grid-cols-3 gap-8">
            <div class="text-center p-8 card">
                <div class="w-16 h-16 bg-gold/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-eye text-2xl text-gold"></i>
                </div>
                <h3 class="text-xl font-bold mb-3 text-dark-primary">Our Vision</h3>
                <p class="text-gray-600 text-sm"><?= getSetting('about_vision') ?: 'To be a world-class parliamentary training institution that sets the standard for legislative capacity development in Africa.' ?></p>
            </div>
            <div class="text-center p-8 card">
                <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-bullseye text-2xl text-primary"></i>
                </div>
                <h3 class="text-xl font-bold mb-3 text-dark-primary">Our Mission</h3>
                <p class="text-gray-600 text-sm"><?= getSetting('about_mission') ?: 'To enhance parliamentary effectiveness through innovative training, research, and capacity building programs.' ?></p>
            </div>
            <div class="text-center p-8 card">
                <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-star text-2xl text-primary"></i>
                </div>
                <h3 class="text-xl font-bold mb-3 text-dark-primary">Our Values</h3>
                <p class="text-gray-600 text-sm"><?= getSetting('about_values') ?: 'Excellence, Integrity, Inclusiveness, Innovation, and Professionalism in all our undertakings.' ?></p>
            </div>
        </div>
    </div>
</section>

<?php require 'includes/footer.php'; ?>
