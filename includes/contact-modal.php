<div id="contactModal" class="fixed inset-0 z-[2000] hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeContactModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto animate-modal-in">
            <div class="flex items-center justify-between p-5 pb-0">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Send Us a Message</h3>
                    <p class="text-sm text-gray-400 mt-0.5">We'll get back to you shortly.</p>
                </div>
                <button onclick="closeContactModal()" class="w-9 h-9 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-400 hover:text-gray-600 transition flex-shrink-0">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" action="contact.php" class="p-5 space-y-3.5">
                <div class="grid sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" required class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Subject</label>
                    <input type="text" name="subject" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Message <span class="text-red-500">*</span></label>
                    <textarea name="message" rows="4" required class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition text-sm resize-none"></textarea>
                </div>
                <button type="submit" class="w-full bg-primary text-white py-2.5 rounded-lg font-semibold text-sm hover:bg-primary-dark transition flex items-center justify-center gap-2">
                    Send Message
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
        </div>
    </div>
</div>
<style>
    @keyframes modalIn {
        from { opacity: 0; transform: scale(0.95) translateY(8px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }
    .animate-modal-in { animation: modalIn 0.2s ease-out; }
</style>
<script>
    function openContactModal() { document.getElementById('contactModal').classList.remove('hidden'); document.body.style.overflow = 'hidden'; }
    function closeContactModal() { document.getElementById('contactModal').classList.add('hidden'); document.body.style.overflow = ''; }
    document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeContactModal(); });
    document.querySelectorAll('[data-toggle="contact-modal"]').forEach(el => el.addEventListener('click', function(e) { e.preventDefault(); openContactModal(); }));
</script>
