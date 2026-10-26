import './bootstrap';

// Dengarkan siaran antrian dari Reverb
if (window.Echo) {
    window.Echo.channel('antrian').listen('.antrian.updated', (e) => {
        document
            .querySelectorAll(`[data-sedang-dilayani="${e.id_jadwal}"]`)
            .forEach((el) => {
                el.textContent = e.nomor ?? '-';

                // kedip singkat supaya perubahan terlihat
                el.classList.add('animate-pulse');
                setTimeout(() => el.classList.remove('animate-pulse'), 1200);
            });
    });
}