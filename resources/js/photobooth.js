import Upscaler from 'upscaler';
import x2 from '@upscalerjs/esrgan-medium/2x';

// --- Inisialisasi Upscaler pakai CPU ---
const upscaler = new Upscaler({
  model: x2,         // gunakan model langsung, jangan new x2()
  preferWebGL: false // pakai CPU, tidak pakai WebGL
});

document.addEventListener('DOMContentLoaded', () => {
    const video = document.getElementById('video');
    const timerEl = document.getElementById('timer');
    const info = document.getElementById('info');
    const resetBtn = document.getElementById('reset');
    const nextBtn = document.getElementById('nextBtn');
    const previewContainer = document.getElementById('previewContainer');
    const globalTimerEl = document.getElementById('globalTimer');
    const orientationBtn = document.getElementById('orientationBtn');
    const orientationIcon = document.getElementById('orientationIcon');

    const durasi = window.PHOTOBOOTH.durasi;
    const totalPhotos = window.PHOTOBOOTH.layout;
    const orderId = window.PHOTOBOOTH.orderId;
    const totalWaktu = window.PHOTOBOOTH.totalWaktu;
    let isPortrait = window.PHOTOBOOTH.orientasi === 'portrait';
    let isMirror = true;
    let capturedImages = Array(totalPhotos).fill(null);

    let countdown = durasi;
    let timerInterval;
    let globalTimerInterval;
    let stream, track, imageCapture;

    // ---------------- Helpers ----------------
    function formatTime(sec) {
        const m = String(Math.floor(sec / 60)).padStart(2, '0');
        const s = String(sec % 60).padStart(2, '0');
        return `${m}:${s}`;
    }

    function loadImageFromBlob(blob) {
        return new Promise((resolve, reject) => {
            const url = URL.createObjectURL(blob);
            const img = new Image();
            img.onload = () => { URL.revokeObjectURL(url); resolve(img); };
            img.onerror = reject;
            img.src = url;
        });
    }

    async function takeSnapshot() {
        try {
            if (video.readyState < 2) await new Promise(r => video.onloadedmetadata = r);

            // Ambil frame video ke canvas
            let srcCanvas = document.createElement('canvas');
            let ctx = srcCanvas.getContext('2d');

            if (imageCapture && imageCapture.takePhoto) {
                try {
                    const blob = await imageCapture.takePhoto();
                    const img = await loadImageFromBlob(blob);
                    srcCanvas.width = img.width;
                    srcCanvas.height = img.height;
                    ctx.drawImage(img, 0, 0);
                } catch {
                    srcCanvas.width = video.videoWidth;
                    srcCanvas.height = video.videoHeight;
                    ctx.drawImage(video, 0, 0, srcCanvas.width, srcCanvas.height);
                }
            } else {
                srcCanvas.width = video.videoWidth;
                srcCanvas.height = video.videoHeight;
                ctx.drawImage(video, 0, 0, srcCanvas.width, srcCanvas.height);
            }

            // Resize untuk CPU (opsional)
            const maxWidth = 1920;
            const maxHeight = 1080;
            let scale = Math.min(maxWidth / srcCanvas.width, maxHeight / srcCanvas.height, 1);
            const tempCanvas = document.createElement('canvas');
            tempCanvas.width = srcCanvas.width * scale;
            tempCanvas.height = srcCanvas.height * scale;
            tempCanvas.getContext('2d').drawImage(srcCanvas, 0, 0, tempCanvas.width, tempCanvas.height);

            // Upscale
            const upscaledCanvas = await upscaler.upscale(tempCanvas);

            // Convert ke base64
            const blob = await new Promise(resolve => upscaledCanvas.toBlob(resolve, 'image/jpeg', 1.0));
            const reader = new FileReader();
            reader.readAsDataURL(blob);
            reader.onloadend = async () => {
                const base64data = reader.result;

                const res = await fetch("/upload-photo", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ order_id: orderId, image: base64data })
                });
                const data = await res.json();
                if (data.success && data.url) {
                    const emptyIndex = capturedImages.findIndex(i => !i);
                    if (emptyIndex !== -1) capturedImages[emptyIndex] = data.url;
                    renderPreview();
                }
            };
        } catch (err) {
            console.error(err);
        }
    }

    function updateVideoTransform() {
        video.style.transformOrigin = 'center center';
        let rotateDeg = isPortrait ? 90 : 0;
        let scaleX = (isMirror && !isPortrait) ? -1 : 1;
        let scaleY = (isMirror && isPortrait) ? -1 : 1;
        video.style.transform = `rotate(${rotateDeg}deg) scale(${scaleX}, ${scaleY})`;
    }

    function renderPreview() {
        previewContainer.innerHTML = '';
        for (let i = 0; i < totalPhotos; i++) {
            const box = document.createElement('div');
            box.className = `relative flex items-center justify-center bg-gray-100 rounded-xl shadow-lg aspect-[3/4] border-2 border-dashed border-gray-300`;
            if (capturedImages[i]) {
                const img = document.createElement('img');
                img.src = capturedImages[i];
                img.className = "w-full h-full object-cover rounded-xl shadow-lg";
                box.appendChild(img);
            } else {
                const numberCircle = document.createElement('div');
                numberCircle.className = "w-16 h-16 flex items-center justify-center rounded-full border-4 border-gray-400 text-gray-500 text-2xl font-bold";
                numberCircle.textContent = i + 1;
                box.appendChild(numberCircle);
            }
            previewContainer.appendChild(box);
        }
        const taken = capturedImages.filter(i => i).length;
        info.textContent = `Foto: ${taken}/${totalPhotos} Foto`;
        resetBtn.classList.toggle('hidden', !capturedImages.some(i => i));
        nextBtn.classList.toggle('hidden', !capturedImages.some(i => i));
    }

    async function startCamera() {
        try {
            stream = await navigator.mediaDevices.getUserMedia({
                video: { width: { ideal: 1920 }, height: { ideal: 1080 }, frameRate: { ideal: 30 } },
                audio: false
            });
            video.srcObject = stream;
            track = stream.getVideoTracks()[0];
            try { imageCapture = new ImageCapture(track); } catch { imageCapture = null; }
            updateVideoTransform();
        } catch (err) {
            console.error(err);
            info.textContent = "Gagal mengakses kamera";
        }
    }

    function startGlobalTimer() {
        let remaining = totalWaktu;
        function tick() {
            globalTimerEl.textContent = formatTime(remaining);
            if (remaining <= 0) clearInterval(globalTimerInterval);
            remaining--;
        }
        tick();
        globalTimerInterval = setInterval(tick, 1000);
    }

    function startAutoCapture() {
        let photoIndex = 0;
        function captureLoop() {
            if (photoIndex >= totalPhotos) return;
            countdown = durasi;
            timerEl.textContent = countdown;
            clearInterval(timerInterval);
            timerInterval = setInterval(() => {
                countdown--;
                timerEl.textContent = countdown;
                if (countdown <= 0) {
                    clearInterval(timerInterval);
                    takeSnapshot().then(() => {
                        photoIndex++;
                        captureLoop();
                    });
                }
            }, 1000);
        }
        captureLoop();
    }

    // ---------------- Event ----------------
    orientationBtn.addEventListener('click', () => {
        isPortrait = !isPortrait;
        updateVideoTransform();
        orientationIcon.style.transform = isPortrait ? 'rotate(0deg)' : 'rotate(90deg)';
    });

    nextBtn.addEventListener('click', () => {
        if(!capturedImages.some(i => i)) return alert("Belum ada foto yang diambil!");
        window.location.href = `/filter/${window.PHOTOBOOTH.orderCode}`;
    });

    // ---------------- Start ----------------
    (async () => {
        await Swal.fire({
            title: 'Siap untuk sesi foto?',
            html: `<p>Foto akan diambil otomatis dalam <strong>${durasi}</strong> detik.</p>`,
            confirmButtonText: 'Mulai', cancelButtonText: 'Batal', showCancelButton: true
        });
        startCamera();
        renderPreview();
        startGlobalTimer();
        startAutoCapture();
    })();

});
