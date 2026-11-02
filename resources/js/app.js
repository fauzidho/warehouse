import './bootstrap';

// Lazy-load Firebase HANYA di halaman Cadangan Data
// Halaman lain tidak perlu memuat 600KB Firebase SDK sama sekali
// Ini adalah optimasi performa utama yang mengurangi beban halaman secara dramatis
if (document.body.dataset.page === 'firebase' || document.querySelector('[data-firebase-page]')) {
    import('./firebase').then(({ default: FirebaseSync }) => {
        window.FirebaseSync = FirebaseSync;
    });
}
