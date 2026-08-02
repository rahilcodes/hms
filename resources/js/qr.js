// Standalone entry for print/payment pages: renders a scannable QR code
// into every element carrying a data-upi-qr="upi://pay?..." attribute.
import QRCode from 'qrcode';

function renderUpiQrs() {
    document.querySelectorAll('[data-upi-qr]').forEach((el) => {
        if (el.dataset.qrRendered) return;
        const uri = el.getAttribute('data-upi-qr');
        if (!uri) return;
        const size = parseInt(el.dataset.qrSize || '176', 10);
        const canvas = document.createElement('canvas');
        el.appendChild(canvas);
        QRCode.toCanvas(canvas, uri, { width: size, margin: 1 }, (err) => {
            if (err) {
                canvas.remove();
                return;
            }
            el.dataset.qrRendered = '1';
        });
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', renderUpiQrs);
} else {
    renderUpiQrs();
}
