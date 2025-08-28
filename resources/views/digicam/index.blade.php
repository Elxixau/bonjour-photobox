
<style>
.preview-img { position: relative; }
.download-btn { position: absolute; top: 2px; right: 2px; background: black; color: white; padding: 0.2rem 0.4rem; font-size: 0.75rem; border-radius: 4px; cursor: pointer; }
#timer { width: 60px; height: 60px; font-size: 1.5rem; line-height: 60px; text-align: center; border-radius: 50%; border: 3px solid white; background: rgba(0,0,0,0.5); color: white; margin: auto; display: none;}
</style>

<h1 id="info" class="text-3xl text-white font-black font-serif mb-8 text-center">
   0/{{ $layout }} Foto
</h1>

<div class="max-w-6xl mx-auto flex flex-col md:flex-row gap-8 p-4">
    <!-- Timer / Countdown -->
    <div class="flex-1 max-w-md mx-auto text-center">
        <div id="timer">3</div>
    </div>

    <!-- Preview Section -->
    <div class="flex-1 max-w-md mx-auto flex flex-col">
        <h2 class="text-xl font-semibold mb-4 text-white text-center">Preview Foto</h2>
        <div id="previewContainer" class="grid grid-cols-2 gap-4 p-2"></div>
    </div>
</div>

<div class="max-w-6xl mx-auto mt-6 text-center space-x-4">
    <button id="captureBtn" class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg shadow-black shadow-[4px_4px_0_0] hover:shadow-[6px_6px_0_0] transition duration-300">
        Ambil Foto
    </button>
    <button id="reset" class="px-6 py-3 bg-gray-400 text-white font-semibold rounded-lg shadow-black shadow-[4px_4px_0_0] hover:shadow-[6px_6px_0_0] transition duration-300 hidden">Capture Ulang</button>
    <button id="nextBtn" class="px-6 py-3 bg-white text-black font-semibold rounded-lg shadow-black shadow-[4px_4px_0_0] hover:shadow-[6px_6px_0_0] transition duration-300 hidden">Selanjutnya</button>
</div>

<script>
const layout = {{ $layout }};
let fotoCount = 0;
const timerEl = document.getElementById('timer');
const previewContainer = document.getElementById('previewContainer');
const info = document.getElementById('info');
const nextBtn = document.getElementById('nextBtn');
const resetBtn = document.getElementById('reset');
const captureBtn = document.getElementById('captureBtn');

// Countdown sebelum capture
function startCountdown(seconds, callback) {
    timerEl.textContent = seconds;
    timerEl.style.display = 'block';
    const interval = setInterval(() => {
        seconds--;
        timerEl.textContent = seconds;
        if(seconds <= 0) {
            clearInterval(interval);
            timerEl.style.display = 'none';
            callback();
        }
    }, 1000);
}

// Tunggu file tersedia di folder Session1
async function waitForPreview(filename, retries = 10, delay = 300){
    for(let i=0; i<retries; i++){
        try {
            const res = await fetch(`/preview/${filename}`, { method: 'HEAD' });
            if(res.ok) return true;
        } catch(e){}
        await new Promise(r=>setTimeout(r, delay));
    }
    return false;
}

// Capture foto manual
async function capturePhoto() {
    try {
        // Trigger iGicam
        await fetch('http://localhost:5513/?CMD=Capture');

        fotoCount++;
        const filename = `preview${fotoCount}.jpg`;

        const ok = await waitForPreview(filename, 10, 300);
        if(!ok) return alert('Foto gagal diambil / preview tidak tersedia.');

        const img = document.createElement('div');
        img.className = 'preview-img';

        const imgEl = document.createElement('img');
        imgEl.src = `/preview/${filename}?ts=${Date.now()}`;
        imgEl.className = 'w-full h-auto rounded-lg border border-white';
        img.appendChild(imgEl);

        const btn = document.createElement('button');
        btn.innerText = 'Download';
        btn.className = 'download-btn';
        btn.addEventListener('click', ()=> {
            const a = document.createElement('a');
            a.href = imgEl.src;
            a.download = filename;
            a.click();
        });
        img.appendChild(btn);

        previewContainer.appendChild(img);
        info.textContent = `${fotoCount}/${layout} Foto`;

        if(fotoCount >= layout){
            nextBtn.classList.remove('hidden');
            resetBtn.classList.remove('hidden');
            captureBtn.disabled = true;
        }

    } catch(err){
        console.error(err);
        alert('Gagal capture foto.');
    }
}

// Tombol capture
captureBtn.addEventListener('click', () => {
    if(fotoCount >= layout) return;
    startCountdown(3, capturePhoto);
});

// Tombol reset
resetBtn.addEventListener('click', () => {
    previewContainer.innerHTML = '';
    fotoCount = 0;
    info.textContent = `0/${layout} Foto`;
    nextBtn.classList.add('hidden');
    resetBtn.classList.add('hidden');
    captureBtn.disabled = false;
});

// Tombol selanjutnya
nextBtn.addEventListener('click', () => {
    alert('Semua foto selesai! Bisa lanjut ke proses berikutnya.');
});
</script>