<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Upload Selfie & Signature - Grow Capital Research</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #004b87;
            --primary-hover: #003b6b;
            --background: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --success: #10b981;
            --error: #ef4444;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--background);
            color: var(--text-main);
            line-height: 1.5;
            padding: 20px 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .wrapper {
            width: 100%;
            max-width: 480px;
            background: var(--card-bg);
            border-radius: 20px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border);
            overflow: hidden;
        }

        .header {
            background-color: var(--primary);
            color: white;
            padding: 24px;
            text-align: center;
        }

        .header h1 {
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .header p {
            font-size: 13px;
            opacity: 0.9;
        }

        .content {
            padding: 24px;
        }

        .alert {
            padding: 16px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 20px;
            text-align: center;
        }

        .alert-success {
            background-color: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--text-main);
        }

        .file-upload-wrapper {
            position: relative;
            width: 100%;
            height: 120px;
            border: 2px dashed var(--border);
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            transition: border-color 0.2s;
            background: #fafafa;
        }

        .file-upload-wrapper:hover {
            border-color: var(--primary);
        }

        .file-upload-wrapper input[type="file"] {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        .upload-icon {
            font-size: 24px;
            color: var(--text-muted);
            margin-bottom: 8px;
        }

        .upload-text {
            font-size: 13px;
            color: var(--text-muted);
            font-weight: 500;
        }

        .preview-container {
            display: none;
            margin-top: 12px;
            text-align: center;
        }

        .preview-image {
            max-width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--primary);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        /* Canvas drawing board */
        .canvas-container {
            width: 100%;
            background-color: #fafafa;
            border: 2px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
            position: relative;
        }

        .canvas-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 12px;
            background: #f1f5f9;
            border-bottom: 1px solid var(--border);
        }

        .canvas-header span {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
        }

        .clear-btn {
            background: none;
            border: none;
            color: var(--error);
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            padding: 4px 8px;
        }

        canvas {
            display: block;
            width: 100%;
            height: 150px;
            touch-action: none;
            cursor: crosshair;
        }

        .btn-submit {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            padding: 14px;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
            margin-top: 8px;
        }

        .btn-submit:hover {
            background-color: var(--primary-hover);
        }

        .footer {
            padding: 16px;
            text-align: center;
            font-size: 11px;
            color: var(--text-muted);
            border-top: 1px solid var(--border);
        }
    </style>
</head>
<body>

    <div class="wrapper">
        <div class="header">
            <h1>Grow Capital Research</h1>
            <p>Verification Document Upload Portal</p>
        </div>

        <div class="content">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form action="/kyc/upload-media/{{ $user->id }}/{{ $token }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                @csrf
                
                <!-- Selfie Section -->
                <div class="form-group">
                    <label class="form-label">1. Capture or Upload Selfie</label>
                    <div class="file-upload-wrapper">
                        <span class="upload-icon">📸</span>
                        <span class="upload-text">Tap to capture or upload photo</span>
                        <input type="file" name="selfie" accept="image/*" capture="user" id="selfieInput">
                    </div>
                    <div class="preview-container" id="previewContainer">
                        <img src="" alt="Selfie Preview" class="preview-image" id="selfiePreview">
                        <p style="font-size:12px; color:var(--success); margin-top:4px; font-weight:600;">Selfie selected successfully!</p>
                    </div>
                </div>

                <!-- Signature Section -->
                <div class="form-group">
                    <label class="form-label">2. Draw Digital Signature</label>
                    <div class="canvas-container">
                        <div class="canvas-header">
                            <span>Draw inside this box</span>
                            <button type="button" class="clear-btn" id="clearBtn">Clear Signature</button>
                        </div>
                        <canvas id="sigCanvas"></canvas>
                    </div>
                    <input type="hidden" name="signature_data" id="sigInput">
                </div>

                <button type="submit" class="btn-submit">Submit Verification Media</button>
            </form>
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} Grow Capital Research &bull; SEBI Reg Analyst
        </div>
    </div>

    <script>
        // Selfie preview logic
        const selfieInput = document.getElementById('selfieInput');
        const selfiePreview = document.getElementById('selfiePreview');
        const previewContainer = document.getElementById('previewContainer');

        selfieInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    selfiePreview.src = event.target.result;
                    previewContainer.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });

        // HTML5 Canvas Drawing logic for Signature
        const canvas = document.getElementById('sigCanvas');
        const ctx = canvas.getContext('2d');
        const clearBtn = document.getElementById('clearBtn');
        const sigInput = document.getElementById('sigInput');
        const uploadForm = document.getElementById('uploadForm');

        // Set dimensions (responsive)
        function resizeCanvas() {
            const rect = canvas.getBoundingClientRect();
            canvas.width = rect.width;
            canvas.height = 150; // Keep height fixed
            // Set pen details
            ctx.lineWidth = 3;
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';
            ctx.strokeStyle = '#0f172a'; // dark line
        }

        window.addEventListener('resize', resizeCanvas);
        resizeCanvas();

        let isDrawing = false;
        let lastX = 0;
        let lastY = 0;

        function getCoordinates(e) {
            const rect = canvas.getBoundingClientRect();
            let clientX, clientY;
            if (e.touches && e.touches.length > 0) {
                clientX = e.touches[0].clientX;
                clientY = e.touches[0].clientY;
            } else {
                clientX = e.clientX;
                clientY = e.clientY;
            }
            return {
                x: clientX - rect.left,
                y: clientY - rect.top
            };
        }

        function startDrawing(e) {
            isDrawing = true;
            const coords = getCoordinates(e);
            lastX = coords.x;
            lastY = coords.y;
            ctx.beginPath();
            ctx.moveTo(lastX, lastY);
        }

        function draw(e) {
            if (!isDrawing) return;
            e.preventDefault(); // Prevent scrolling on mobile while drawing
            const coords = getCoordinates(e);
            ctx.lineTo(coords.x, coords.y);
            ctx.stroke();
            lastX = coords.x;
            lastY = coords.y;
        }

        function stopDrawing() {
            isDrawing = false;
        }

        // Mouse events
        canvas.addEventListener('mousedown', startDrawing);
        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('mouseup', stopDrawing);
        canvas.addEventListener('mouseout', stopDrawing);

        // Touch events (Mobile)
        canvas.addEventListener('touchstart', startDrawing);
        canvas.addEventListener('touchmove', draw);
        canvas.addEventListener('touchend', stopDrawing);

        // Clear canvas
        clearBtn.addEventListener('click', function() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            sigInput.value = '';
        });

        // Form submission: write canvas content to input
        uploadForm.addEventListener('submit', function(e) {
            // Check if user drew anything (we check if canvas is blank)
            const isBlank = isCanvasBlank(canvas);
            if (!isBlank) {
                sigInput.value = canvas.toDataURL('image/png');
            }
        });

        function isCanvasBlank(c) {
            const blank = document.createElement('canvas');
            blank.width = c.width;
            blank.height = c.height;
            return c.toDataURL() === blank.toDataURL();
        }
    </script>
</body>
</html>
