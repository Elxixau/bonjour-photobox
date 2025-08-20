<div>
    <h2 class="text-xl font-bold mb-4">Sesi Foto - Layout {{ $layout }}</h2>
    <video id="video" autoplay class="border"></video>
    <canvas id="canvas" style="display:none;"></canvas>
    <button wire:click="capture" id="captureBtn"
        class="mt-4 px-4 py-2 bg-blue-500 text-white rounded">
        Ambil Foto
    </button>

    <div class="grid grid-cols-{{ $layout == 4 ? 2 : 3 }} gap-2 mt-4">
        @foreach($photos as $photo)
            <img src="{{ $photo }}" class="border" />
        @endforeach
    </div>

    <script>
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const captureBtn = document.getElementById('captureBtn');

        navigator.mediaDevices.getUserMedia({ video: true })
            .then(stream => video.srcObject = stream)
            .catch(err => console.error("Camera error:", err));

        captureBtn.addEventListener('click', () => {
            const context = canvas.getContext('2d');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            const imageData = canvas.toDataURL('image/png');
            @this.savePhoto(imageData);
        });
    </script>
</div>
