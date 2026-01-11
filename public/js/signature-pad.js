/**
 * Signature Pad - Digital Signature Drawing Tool
 * Supports canvas drawing and touch events for mobile
 */
class SignaturePad {
    constructor(canvasElement, options = {}) {
        this.canvas = canvasElement;
        this.ctx = this.canvas.getContext('2d');
        this.drawing = false;
        this.lastX = 0;
        this.lastY = 0;
        this.paths = [];
        this.currentPath = [];

        // Options
        this.options = {
            lineWidth: options.lineWidth || 2,
            strokeStyle: options.strokeStyle || '#000000',
            backgroundColor: options.backgroundColor || '#ffffff',
            ...options
        };

        this.init();
    }

    init() {
        // Set canvas size
        this.resizeCanvas();

        // Clear with background
        this.clear();

        // Mouse events
        this.canvas.addEventListener('mousedown', (e) => this.startDrawing(e));
        this.canvas.addEventListener('mousemove', (e) => this.draw(e));
        this.canvas.addEventListener('mouseup', () => this.stopDrawing());
        this.canvas.addEventListener('mouseout', () => this.stopDrawing());

        // Touch events
        this.canvas.addEventListener('touchstart', (e) => this.startDrawing(e), { passive: false });
        this.canvas.addEventListener('touchmove', (e) => this.draw(e), { passive: false });
        this.canvas.addEventListener('touchend', () => this.stopDrawing());

        // Resize handler
        window.addEventListener('resize', () => this.resizeCanvas());
    }

    resizeCanvas() {
        const rect = this.canvas.getBoundingClientRect();
        const dpr = window.devicePixelRatio || 1;

        // Store current image
        const imageData = this.ctx.getImageData(0, 0, this.canvas.width, this.canvas.height);

        this.canvas.width = rect.width * dpr;
        this.canvas.height = rect.height * dpr;

        this.ctx.scale(dpr, dpr);
        this.canvas.style.width = rect.width + 'px';
        this.canvas.style.height = rect.height + 'px';

        // Redraw
        this.redraw();
    }

    getCoordinates(e) {
        const rect = this.canvas.getBoundingClientRect();
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

    startDrawing(e) {
        e.preventDefault();
        this.drawing = true;
        const coords = this.getCoordinates(e);
        this.lastX = coords.x;
        this.lastY = coords.y;
        this.currentPath = [{ x: coords.x, y: coords.y }];
    }

    draw(e) {
        if (!this.drawing) return;
        e.preventDefault();

        const coords = this.getCoordinates(e);

        this.ctx.beginPath();
        this.ctx.strokeStyle = this.options.strokeStyle;
        this.ctx.lineWidth = this.options.lineWidth;
        this.ctx.lineCap = 'round';
        this.ctx.lineJoin = 'round';

        this.ctx.moveTo(this.lastX, this.lastY);
        this.ctx.lineTo(coords.x, coords.y);
        this.ctx.stroke();

        this.currentPath.push({ x: coords.x, y: coords.y });
        this.lastX = coords.x;
        this.lastY = coords.y;
    }

    stopDrawing() {
        if (this.drawing && this.currentPath.length > 0) {
            this.paths.push([...this.currentPath]);
        }
        this.drawing = false;
        this.currentPath = [];
    }

    redraw() {
        this.ctx.fillStyle = this.options.backgroundColor;
        this.ctx.fillRect(0, 0, this.canvas.width, this.canvas.height);

        this.ctx.strokeStyle = this.options.strokeStyle;
        this.ctx.lineWidth = this.options.lineWidth;
        this.ctx.lineCap = 'round';
        this.ctx.lineJoin = 'round';

        for (const path of this.paths) {
            if (path.length < 2) continue;

            this.ctx.beginPath();
            this.ctx.moveTo(path[0].x, path[0].y);

            for (let i = 1; i < path.length; i++) {
                this.ctx.lineTo(path[i].x, path[i].y);
            }
            this.ctx.stroke();
        }
    }

    clear() {
        this.paths = [];
        this.currentPath = [];
        this.ctx.fillStyle = this.options.backgroundColor;
        this.ctx.fillRect(0, 0, this.canvas.width, this.canvas.height);
    }

    undo() {
        if (this.paths.length > 0) {
            this.paths.pop();
            this.redraw();
        }
    }

    isEmpty() {
        return this.paths.length === 0;
    }

    toDataURL(type = 'image/png') {
        return this.canvas.toDataURL(type);
    }

    toBlob(callback, type = 'image/png') {
        // Create a new canvas with proper dimensions for export
        const exportCanvas = document.createElement('canvas');
        const exportCtx = exportCanvas.getContext('2d');

        // Use the display size, not the scaled size
        const rect = this.canvas.getBoundingClientRect();
        exportCanvas.width = rect.width;
        exportCanvas.height = rect.height;

        // Fill with background color
        exportCtx.fillStyle = this.options.backgroundColor;
        exportCtx.fillRect(0, 0, exportCanvas.width, exportCanvas.height);

        // Draw the paths
        exportCtx.strokeStyle = this.options.strokeStyle;
        exportCtx.lineWidth = this.options.lineWidth;
        exportCtx.lineCap = 'round';
        exportCtx.lineJoin = 'round';

        for (const path of this.paths) {
            if (path.length < 2) continue;
            exportCtx.beginPath();
            exportCtx.moveTo(path[0].x, path[0].y);
            for (let i = 1; i < path.length; i++) {
                exportCtx.lineTo(path[i].x, path[i].y);
            }
            exportCtx.stroke();
        }

        exportCanvas.toBlob(callback, type);
    }
}

// Export for use
window.SignaturePad = SignaturePad;
