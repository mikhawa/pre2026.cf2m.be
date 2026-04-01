import { Controller } from '@hotwired/stimulus';
import { Modal } from 'bootstrap';

/**
 * Cropper avatar maison — canvas natif, pas de librairie externe.
 *
 * Flux :
 *  1. Sélection fichier → lecture DataURL → stockage en _pendingSrc → ouverture modal
 *  2. shown.bs.modal → chargement image → initialisation crop box centrée
 *  3. Drag/resize de la crop box (carrée) via souris et touch
 *  4. Confirmation → drawImage sur canvas 80×80 → Blob WebP → DataTransfer → file input
 */
export default class extends Controller {
    static targets = ['fileInput', 'cropCanvas', 'preview', 'previewPlaceholder'];

    connect() {
        this._confirmed  = false;
        this._pendingSrc = null;
        this._img        = new Image();
        this._dragging   = false;
        this._resizing   = false;

        const modalEl = this.element.querySelector('#avatar-crop-modal');
        this._modal   = new Modal(modalEl);

        modalEl.addEventListener('shown.bs.modal', () => {
            if (!this._pendingSrc) return;
            this._img.onload = () => this._initCrop();
            this._img.src    = this._pendingSrc;
            this._pendingSrc = null;
        });

        modalEl.addEventListener('hidden.bs.modal', () => {
            if (!this._confirmed) this.fileInputTarget.value = '';
            this._confirmed = false;
        });
    }

    // ── Sélection du fichier ───────────────────────────────────────────────

    fileChanged(event) {
        const file = event.target.files[0];
        if (!file || !file.type.startsWith('image/')) return;
        const reader = new FileReader();
        reader.onload = (e) => {
            this._pendingSrc = e.target.result;
            this._modal.show();
        };
        reader.readAsDataURL(file);
    }

    // ── Initialisation du canvas ───────────────────────────────────────────

    _initCrop() {
        const canvas = this.cropCanvasTarget;
        const W = canvas.parentElement.offsetWidth;
        const H = canvas.parentElement.offsetHeight;
        canvas.width  = W;
        canvas.height = H;
        this._ctx = canvas.getContext('2d');

        // Adapter l'image dans le canvas (letterbox)
        const r = Math.min(W / this._img.naturalWidth, H / this._img.naturalHeight);
        this._imgW  = this._img.naturalWidth  * r;
        this._imgH  = this._img.naturalHeight * r;
        this._imgX  = (W - this._imgW) / 2;
        this._imgY  = (H - this._imgH) / 2;
        this._scale = r;

        // Crop box initiale : 80 % du côté le plus court, centrée
        const s = Math.min(this._imgW, this._imgH) * 0.8;
        this._cropSize = s;
        this._cropX    = this._imgX + (this._imgW - s) / 2;
        this._cropY    = this._imgY + (this._imgH - s) / 2;
        this._draw();
    }

    // ── Dessin ────────────────────────────────────────────────────────────

    _draw() {
        const canvas = this.cropCanvasTarget;
        const ctx = this._ctx;
        const W = canvas.width, H = canvas.height;
        const x = this._cropX, y = this._cropY, s = this._cropSize;

        ctx.clearRect(0, 0, W, H);

        // Image source
        ctx.drawImage(this._img, this._imgX, this._imgY, this._imgW, this._imgH);

        // Assombrissement hors sélection (4 rectangles)
        ctx.fillStyle = 'rgba(0,0,0,0.60)';
        ctx.fillRect(0, 0, W, y);
        ctx.fillRect(0, y + s, W, H - y - s);
        ctx.fillRect(0, y, x, s);
        ctx.fillRect(x + s, y, W - x - s, s);

        // Bordure de sélection
        ctx.strokeStyle = 'rgba(0,180,216,0.90)';
        ctx.lineWidth = 2;
        ctx.strokeRect(x, y, s, s);

        // Grille des tiers
        ctx.strokeStyle = 'rgba(255,255,255,0.22)';
        ctx.lineWidth = 1;
        ctx.beginPath();
        for (let i = 1; i < 3; i++) {
            ctx.moveTo(x + s * i / 3, y);     ctx.lineTo(x + s * i / 3, y + s);
            ctx.moveTo(x,             y + s * i / 3); ctx.lineTo(x + s, y + s * i / 3);
        }
        ctx.stroke();

        // Poignées de coin
        const hs = 6;
        ctx.fillStyle = 'rgba(0,180,216,1)';
        [[x, y], [x + s, y], [x, y + s], [x + s, y + s]].forEach(([hx, hy]) => {
            ctx.fillRect(hx - hs, hy - hs, hs * 2, hs * 2);
        });
    }

    // ── Utilitaires souris / touch ────────────────────────────────────────

    _pos(e) {
        const r  = this.cropCanvasTarget.getBoundingClientRect();
        const sx = this.cropCanvasTarget.width  / r.width;
        const sy = this.cropCanvasTarget.height / r.height;
        return { x: (e.clientX - r.left) * sx, y: (e.clientY - r.top) * sy };
    }

    _hitCorner(pos) {
        const { _cropX: x, _cropY: y, _cropSize: s } = this;
        const ht = 18;
        return [
            { n: 'se', cx: x + s, cy: y + s },
            { n: 'nw', cx: x,     cy: y     },
            { n: 'ne', cx: x + s, cy: y     },
            { n: 'sw', cx: x,     cy: y + s },
        ].find(c => Math.abs(pos.x - c.cx) < ht && Math.abs(pos.y - c.cy) < ht) ?? null;
    }

    // ── Interactions drag & resize ────────────────────────────────────────

    dragStart(event) {
        event.preventDefault();
        const pos    = this._pos(event);
        const corner = this._hitCorner(pos);
        if (corner) {
            this._resizing     = true;
            this._resizeCorner = corner.n;
        } else if (
            pos.x > this._cropX && pos.x < this._cropX + this._cropSize &&
            pos.y > this._cropY && pos.y < this._cropY + this._cropSize
        ) {
            this._dragging = true;
            this._dragOX   = pos.x - this._cropX;
            this._dragOY   = pos.y - this._cropY;
        }
    }

    dragMove(event) {
        if (!this._dragging && !this._resizing) return;
        event.preventDefault();
        const pos = this._pos(event);
        const { _imgX: ix, _imgY: iy, _imgW: iw, _imgH: ih } = this;
        const minS = 40;

        if (this._dragging) {
            this._cropX = Math.max(ix, Math.min(ix + iw - this._cropSize, pos.x - this._dragOX));
            this._cropY = Math.max(iy, Math.min(iy + ih - this._cropSize, pos.y - this._dragOY));
        } else {
            const { _cropX: cx, _cropY: cy, _cropSize: cs } = this;
            let newSize, nx = cx, ny = cy;
            const c = this._resizeCorner;

            if (c === 'se') {
                newSize = Math.max(minS, Math.min(pos.x - cx, pos.y - cy, ix + iw - cx, iy + ih - cy));
            } else if (c === 'nw') {
                const ax = cx + cs, ay = cy + cs;
                newSize = Math.max(minS, Math.min(ax - pos.x, ay - pos.y, ax - ix, ay - iy));
                nx = ax - newSize; ny = ay - newSize;
            } else if (c === 'ne') {
                const ay = cy + cs;
                newSize = Math.max(minS, Math.min(pos.x - cx, ay - pos.y, ix + iw - cx, ay - iy));
                ny = ay - newSize;
            } else if (c === 'sw') {
                const ax = cx + cs;
                newSize = Math.max(minS, Math.min(ax - pos.x, pos.y - cy, ax - ix, iy + ih - cy));
                nx = ax - newSize;
            }
            this._cropX = nx; this._cropY = ny; this._cropSize = newSize;
        }
        this._draw();
    }

    dragEnd()                { this._dragging = false; this._resizing = false; }
    touchStart(event)        { event.preventDefault(); this.dragStart(event.touches[0]); }
    touchMove(event)         { event.preventDefault(); this.dragMove(event.touches[0]); }

    // ── Confirmation ──────────────────────────────────────────────────────

    confirm() {
        // Recalcule les coordonnées dans l'image naturelle
        const sx = (this._cropX - this._imgX) / this._scale;
        const sy = (this._cropY - this._imgY) / this._scale;
        const ss = this._cropSize / this._scale;

        const out = document.createElement('canvas');
        out.width = out.height = 80;
        out.getContext('2d').drawImage(this._img, sx, sy, ss, ss, 0, 0, 80, 80);

        out.toBlob((blob) => {
            const f  = new File([blob], 'avatar.webp', { type: 'image/webp' });
            const dt = new DataTransfer();
            dt.items.add(f);
            this.fileInputTarget.files = dt.files;

            const url = URL.createObjectURL(blob);
            if (this.hasPreviewTarget) {
                this.previewTarget.src = url;
            } else if (this.hasPreviewPlaceholderTarget) {
                const img = document.createElement('img');
                img.className = 'cf2m-avatar-img';
                img.alt = '';
                img.setAttribute('data-avatar-crop-target', 'preview');
                img.src = url;
                this.previewPlaceholderTarget.replaceWith(img);
            }

            this._confirmed = true;
            this._modal.hide();
        }, 'image/webp', 0.92);
    }
}
