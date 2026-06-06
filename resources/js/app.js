// === Menu Toggle (Sidebar Mobile) ===
const menuButton = document.getElementById('menuButton');
const aside = document.getElementById('aside');

if(menuButton && aside){
    menuButton.addEventListener('click',()=> {
        aside.toggleAttribute('hidden');
    });
}

// === Dark Mode Toggle dengan State Persistence ===
const darkModeToggle = document.getElementById('darkModeToggle');
const iconMoon = document.getElementById('iconMoon');
const iconSun = document.getElementById('iconSun');

/**
 * Memperbarui tampilan ikon toggle berdasarkan status dark mode.
 * Menampilkan ikon matahari saat dark mode aktif, ikon bulan saat light mode.
 */
function updateIcons() {
    const isDark = document.documentElement.classList.contains('dark');
    if (iconMoon && iconSun) {
        iconMoon.classList.toggle('hidden', isDark);
        iconSun.classList.toggle('hidden', !isDark);
    }
}

// Jalankan saat halaman dimuat untuk sinkronisasi ikon dengan status tema awal
updateIcons();

if (darkModeToggle) {
    darkModeToggle.addEventListener('click', () => {
        const wasDark = document.documentElement.classList.contains('dark');
        document.documentElement.classList.toggle('dark', !wasDark);
        localStorage.setItem('theme', !wasDark ? 'dark' : 'light');
        updateIcons();
    });
}

// === Note Edit Mode Logic ===
document.addEventListener('DOMContentLoaded', () => {
    const titleDisplay = document.getElementById('title-display-container');
    const titleInputContainer = document.getElementById('title-input-container');
    const titleInput = document.getElementById('title-input');
    
    const contentClicker = document.getElementById('content-text-clicker');
    const contentDisplayContainer = document.getElementById('content-display-container');
    const contentInputContainer = document.getElementById('content-input-container');
    const contentTextarea = document.getElementById('content-textarea');
    
    const editActions = document.getElementById('edit-actions');
    const btnCancel = document.getElementById('btn-cancel-edit');

    // Only run if the edit form exists on the page
    const editForm = document.getElementById('edit-note-form');
    if (!editForm) return;

    function enterEditMode(focusTarget) {
        if (titleDisplay) titleDisplay.classList.add('hidden');
        if (contentDisplayContainer) contentDisplayContainer.classList.add('hidden');
        
        if (titleInputContainer) titleInputContainer.classList.remove('hidden');
        if (contentInputContainer) contentInputContainer.classList.remove('hidden');
        if (editActions) {
            editActions.classList.remove('hidden');
            editActions.classList.add('flex');
        }

        if (focusTarget === 'title' && titleInput) {
            titleInput.focus();
            titleInput.select();
        } else if (focusTarget === 'content' && contentTextarea) {
            contentTextarea.focus();
            const length = contentTextarea.value.length;
            contentTextarea.setSelectionRange(length, length);
        }
    }

    function exitEditMode() {
        if (titleDisplay) titleDisplay.classList.remove('hidden');
        if (contentDisplayContainer) contentDisplayContainer.classList.remove('hidden');
        
        if (titleInputContainer) titleInputContainer.classList.add('hidden');
        if (contentInputContainer) contentInputContainer.classList.add('hidden');
        if (editActions) {
            editActions.classList.add('hidden');
            editActions.classList.remove('flex');
        }
    }

    if (titleDisplay) {
        titleDisplay.addEventListener('click', () => enterEditMode('title'));
    }
    if (contentClicker) {
        contentClicker.addEventListener('click', () => enterEditMode('content'));
    }
    if (btnCancel) {
        btnCancel.addEventListener('click', exitEditMode);
    }

    // Cancel edit mode when clicking outside the form
    document.addEventListener('click', (event) => {
        if (editActions && !editActions.classList.contains('hidden') && editForm && !editForm.contains(event.target)) {
            exitEditMode();
        }
    });
});

// === Loading Overlay Logic ===
document.addEventListener('DOMContentLoaded', () => {
    const generalLoading = document.getElementById('general-loading-overlay');
    const aiLoading = document.getElementById('ai-loading-overlay');

    function showGeneralLoading() {
        if (generalLoading) {
            generalLoading.classList.remove('hidden');
            generalLoading.classList.add('flex');
            // Small delay to allow display:flex to apply before transitioning opacity
            setTimeout(() => {
                generalLoading.classList.remove('opacity-0');
                generalLoading.classList.add('opacity-100');
            }, 10);
        }
    }

    function showAILoading() {
        if (aiLoading) {
            aiLoading.classList.remove('hidden');
            aiLoading.classList.add('flex');
            setTimeout(() => {
                aiLoading.classList.remove('opacity-0');
                aiLoading.classList.add('opacity-100');
            }, 10);
        }
    }

    // Attach to all forms
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', () => {
            // If the form has the class form-ai-action, show AI loading, else general loading
            if (form.classList.contains('form-ai-action')) {
                showAILoading();
            } else {
                // Don't show loading if the form is targeting a new tab (target="_blank")
                if (form.getAttribute('target') !== '_blank') {
                    showGeneralLoading();
                }
            }
        });
    });

    // Also attach to any links/buttons marked explicitly for AI action
    document.querySelectorAll('.btn-ai-action').forEach(btn => {
        btn.addEventListener('click', (e) => {
            // Only trigger if it's not opening in a new tab
            if (btn.getAttribute('target') !== '_blank') {
                showAILoading();
            }
        });
    });
});