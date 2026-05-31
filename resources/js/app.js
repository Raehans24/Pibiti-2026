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
const isDark = document.documentElement.classList.contains('dark');

/**
 * Memperbarui tampilan ikon toggle berdasarkan status dark mode.
 * Menampilkan ikon matahari saat dark mode aktif, ikon bulan saat light mode.
 */
function updateIcons() {
    if (iconMoon && iconSun) {
        iconMoon.classList.toggle('hidden', isDark);
        iconSun.classList.toggle('hidden', !isDark);
    }
}

// Jalankan saat halaman dimuat untuk sinkronisasi ikon dengan status tema awal
updateIcons();

if (darkModeToggle) {
    darkModeToggle.addEventListener('click', () => {
        document.documentElement.classList.toggle('dark');
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
        updateIcons();
    });
}