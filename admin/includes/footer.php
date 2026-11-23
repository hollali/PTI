    </div>
</div>

<div class="modal-overlay" id="confirmModal">
    <div class="modal-dialog" style="max-width: 400px;">
        <div class="modal-body" style="padding: 32px 26px; text-align: center;">
            <div class="confirm-icon"><i class="fas fa-exclamation-triangle"></i></div>
            <h3 class="confirm-title">Are you sure?</h3>
            <p class="confirm-msg" id="confirmMsg"></p>
            <div class="flex gap-3 justify-center">
                <button onclick="closeConfirm()" class="btn btn-ghost btn-lg">Cancel</button>
                <button id="confirmDeleteBtn" class="btn btn-danger btn-lg">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
var confirmCallback = null;

document.addEventListener('DOMContentLoaded', function() {
    /* Sidebar toggle */
    var sidebar = document.querySelector('.sidebar');
    var toggleBtn = document.getElementById('sidebarToggle');
    if (sidebar && toggleBtn) {
        var saved = localStorage.getItem('sidebarCollapsed');
        if (saved === 'true') sidebar.classList.add('collapsed');
        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
        });
    }

    /* Image preview on file input change */
    document.querySelectorAll('input[type="file"][data-preview]').forEach(function(input) {
        input.addEventListener('change', function() {
            var preview = document.getElementById(this.dataset.preview);
            if (preview && this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) { preview.src = e.target.result; preview.style.display = 'block'; };
                reader.readAsDataURL(this.files[0]);
            }
        });
    });

    var alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(a) {
        setTimeout(function() { a.remove(); }, 5000);
    });

    document.querySelectorAll('.modal-overlay').forEach(function(overlay) {
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) {
                if (overlay.id === 'confirmModal') {
                    closeConfirm();
                } else {
                    closeModal(overlay.id);
                }
            }
        });
    });

    var confirmBtn = document.getElementById('confirmDeleteBtn');
    if (confirmBtn) {
        confirmBtn.addEventListener('click', function() {
            closeConfirm();
            if (typeof confirmCallback === 'function') {
                setTimeout(confirmCallback, 50);
                confirmCallback = null;
            }
        });
    }
});

function openModal(id) {
    var el = document.getElementById(id);
    if (!el) return;
    el.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeModal(id) {
    var el = document.getElementById(id);
    if (!el) return;
    el.classList.remove('active');
    document.body.style.overflow = '';
}

function showConfirm(msg, callback) {
    document.getElementById('confirmMsg').textContent = msg;
    document.getElementById('confirmModal').classList.add('active');
    document.body.style.overflow = 'hidden';
    confirmCallback = callback;
}

function closeConfirm() {
    document.getElementById('confirmModal').classList.remove('active');
    document.body.style.overflow = '';
    confirmCallback = null;
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        var activeModals = document.querySelectorAll('.modal-overlay.active');
        activeModals.forEach(function(el) {
            el.classList.remove('active');
        });
        document.body.style.overflow = '';
        confirmCallback = null;
    }
});
</script>
</body>
</html>
