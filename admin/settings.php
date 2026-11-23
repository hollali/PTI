<?php
require_once __DIR__ . '/../config/database.php';
requireLogin();
$title = 'Settings';

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $keys = ['site_title', 'site_tagline', 'site_email', 'site_phone', 'site_address', 'footer_text', 'site_favicon', 'social_facebook', 'social_twitter', 'social_instagram', 'social_linkedin', 'maintenance_mode', 'home_stats', 'about_leadership', 'about_mission', 'about_vision', 'about_values', 'why_choose_pti', 'working_hours'];
    foreach ($keys as $key) {
        $value = $_POST[$key] ?? '';
        $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?")->execute([$value, $key]);
    }
    logActivity('update_settings', 'Updated website settings');
    $msg = 'Settings saved successfully.';
}

$settings = [];
$stmt = $pdo->query("SELECT * FROM settings");
while ($row = $stmt->fetch()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

require_once 'includes/header.php';
?>
<?php if ($msg): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $msg ?></div><?php endif; ?>

<form method="POST">
    <div class="grid-2">
        <div class="card">
            <div class="card-header">General Settings</div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">Site Title</label>
                    <input type="text" name="site_title" class="form-input" value="<?= $settings['site_title'] ?? '' ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Site Tagline</label>
                    <input type="text" name="site_tagline" class="form-input" value="<?= $settings['site_tagline'] ?? '' ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Site Email</label>
                    <input type="email" name="site_email" class="form-input" value="<?= $settings['site_email'] ?? '' ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Site Phone</label>
                    <input type="text" name="site_phone" class="form-input" value="<?= $settings['site_phone'] ?? '' ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Site Address</label>
                    <input type="text" name="site_address" class="form-input" value="<?= $settings['site_address'] ?? '' ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Footer Text</label>
                    <input type="text" name="footer_text" class="form-input" value="<?= $settings['footer_text'] ?? '' ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Favicon URL</label>
                    <input type="text" name="site_favicon" class="form-input" value="<?= $settings['site_favicon'] ?? '' ?>" placeholder="assets/logos/logo.png">
                    <div class="form-hint">Path to your favicon image (PNG, ICO, or SVG)</div>
                </div>
            </div>
        </div>

        <div>
            <div class="card mb-4">
                <div class="card-header">Social Media Links</div>
                <div class="card-body">
                    <?php
                    $socials = ['facebook', 'twitter', 'instagram', 'linkedin'];
                    foreach ($socials as $s):
                    ?>
                    <div class="form-group">
                        <label class="form-label"><i class="fab fa-<?= $s ?>"></i> <?= ucfirst($s) ?></label>
                        <input type="text" name="social_<?= $s ?>" class="form-input" value="<?= $settings['social_' . $s] ?? '' ?>">
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="card">
                <div class="card-header">Maintenance</div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Maintenance Mode</label>
                        <select name="maintenance_mode" class="form-select">
                            <option value="0" <?= ($settings['maintenance_mode']??'0')=='0'?'selected':'' ?>>Disabled</option>
                            <option value="1" <?= ($settings['maintenance_mode']??'0')=='1'?'selected':'' ?>>Enabled</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="grid-2 mt-6">
        <div class="card">
            <div class="card-header">Homepage Stats <span class="form-hint">(one per line: Number|Label)</span></div>
            <div class="card-body">
                <div class="form-group">
                    <textarea name="home_stats" class="form-textarea font-mono" rows="5"><?= $settings['home_stats'] ?? '' ?></textarea>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">About - Mission / Vision / Values</div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">Mission</label>
                    <textarea name="about_mission" class="form-textarea" rows="2"><?= $settings['about_mission'] ?? '' ?></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Vision</label>
                    <textarea name="about_vision" class="form-textarea" rows="2"><?= $settings['about_vision'] ?? '' ?></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Values</label>
                    <textarea name="about_values" class="form-textarea" rows="2"><?= $settings['about_values'] ?? '' ?></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Working Hours</label>
                    <input type="text" name="working_hours" class="form-input" value="<?= $settings['working_hours'] ?? '' ?>">
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">About - Leadership Team <span class="form-hint">(one per line: Name|Role|icon-class)</span></div>
            <div class="card-body">
                <div class="form-group">
                    <textarea name="about_leadership" class="form-textarea font-mono" rows="6"><?= $settings['about_leadership'] ?? '' ?></textarea>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">Programs - Why Choose PTI <span class="form-hint">(one per line: icon-class|Title|Description)</span></div>
            <div class="card-body">
                <div class="form-group">
                    <textarea name="why_choose_pti" class="form-textarea font-mono" rows="6"><?= $settings['why_choose_pti'] ?? '' ?></textarea>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-6">
        <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> Save All Settings</button>
    </div>
</form>
<?php require_once 'includes/footer.php'; ?>
