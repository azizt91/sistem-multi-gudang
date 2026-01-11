/**
 * Reusable Barcode Scanner Component
 * Uses html5-qrcode library for barcode scanning
 * 
 * Usage:
 * const scanner = new BarcodeScanner({
 *     containerId: 'reader',
 *     onScanSuccess: (code) => { ... },
 *     onScanError: (error) => { ... },
 *     checkCodeEndpoint: '/items/find-by-code',
 *     csrfToken: window.csrfToken
 * });
 * 
 * scanner.start();
 * scanner.stop();
 * scanner.pause();
 * scanner.resume();
 */

class BarcodeScanner {
    constructor(options) {
        this.containerId = options.containerId || 'reader';
        this.onScanSuccess = options.onScanSuccess || function () { };
        this.onScanError = options.onScanError || function () { };
        this.onCodeExists = options.onCodeExists || function () { };
        this.onCodeAvailable = options.onCodeAvailable || function () { };
        this.checkCodeEndpoint = options.checkCodeEndpoint || null;
        this.csrfToken = options.csrfToken || '';
        this.autoCheckCode = options.autoCheckCode !== false;

        this.html5QrcodeScanner = null;
        this.isScanning = false;
        this.isPaused = false;

        // Supported barcode formats
        this.supportedFormats = [
            Html5QrcodeSupportedFormats.CODE_128,
            Html5QrcodeSupportedFormats.EAN_13,
            Html5QrcodeSupportedFormats.EAN_8,
            Html5QrcodeSupportedFormats.UPC_A,
            Html5QrcodeSupportedFormats.UPC_E,
            Html5QrcodeSupportedFormats.CODE_39,
            Html5QrcodeSupportedFormats.CODE_93,
            Html5QrcodeSupportedFormats.CODABAR,
            Html5QrcodeSupportedFormats.ITF,
            Html5QrcodeSupportedFormats.QR_CODE
        ];
    }

    async start() {
        if (this.isScanning) return;

        try {
            this.html5QrcodeScanner = new Html5Qrcode(this.containerId);

            const config = {
                fps: 10,
                qrbox: { width: 300, height: 150 },
                formatsToSupport: this.supportedFormats,
                experimentalFeatures: {
                    useBarCodeDetectorIfSupported: true
                }
            };

            await this.html5QrcodeScanner.start(
                { facingMode: "environment" },
                config,
                (decodedText, decodedResult) => this.handleScanSuccess(decodedText, decodedResult),
                (errorMessage) => this.handleScanError(errorMessage)
            );

            this.isScanning = true;
            this.isPaused = false;
            return true;
        } catch (err) {
            this.onScanError('Tidak dapat mengakses kamera: ' + err.message);
            return false;
        }
    }

    async stop() {
        if (!this.html5QrcodeScanner || !this.isScanning) return;

        try {
            await this.html5QrcodeScanner.stop();
            this.isScanning = false;
            this.isPaused = false;
            return true;
        } catch (err) {
            console.error('Error stopping scanner:', err);
            return false;
        }
    }

    pause() {
        if (this.html5QrcodeScanner && this.isScanning && !this.isPaused) {
            this.html5QrcodeScanner.pause();
            this.isPaused = true;
        }
    }

    resume() {
        if (this.html5QrcodeScanner && this.isScanning && this.isPaused) {
            this.html5QrcodeScanner.resume();
            this.isPaused = false;
        }
    }

    handleScanSuccess(decodedText, decodedResult) {
        // Play beep sound
        this.playBeep();

        // Pause scanner
        this.pause();

        // Check if code exists (if endpoint provided)
        if (this.autoCheckCode && this.checkCodeEndpoint) {
            this.checkCodeExists(decodedText);
        } else {
            this.onScanSuccess(decodedText, decodedResult);
        }
    }

    handleScanError(errorMessage) {
        // Only report actual errors, not "no barcode found" messages
        if (errorMessage && !errorMessage.includes('No MultiFormat Readers')) {
            this.onScanError(errorMessage);
        }
    }

    async checkCodeExists(code) {
        try {
            const response = await fetch(this.checkCodeEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken
                },
                body: JSON.stringify({ code: code })
            });

            const data = await response.json();

            if (data.success) {
                // Code exists
                this.onCodeExists(code, data.item);
            } else {
                // Code is available
                this.onCodeAvailable(code);
                this.onScanSuccess(code, null);
            }
        } catch (error) {
            // Assume available if check fails
            this.onCodeAvailable(code);
            this.onScanSuccess(code, null);
        }
    }

    playBeep() {
        try {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();

            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);

            oscillator.frequency.value = 1000;
            oscillator.type = 'sine';
            gainNode.gain.value = 0.3;

            oscillator.start();
            setTimeout(() => {
                oscillator.stop();
                audioContext.close();
            }, 100);
        } catch (e) {
            console.log('Audio not supported');
        }
    }

    // Static method to check if camera is available
    static async hasCamera() {
        try {
            const devices = await Html5Qrcode.getCameras();
            return devices && devices.length > 0;
        } catch (err) {
            return false;
        }
    }

    // Static method to get available cameras
    static async getCameras() {
        try {
            return await Html5Qrcode.getCameras();
        } catch (err) {
            return [];
        }
    }

    // Get current scanning state
    getState() {
        return {
            isScanning: this.isScanning,
            isPaused: this.isPaused
        };
    }
}

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = BarcodeScanner;
}
