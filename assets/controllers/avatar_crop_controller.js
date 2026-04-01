import { Controller } from '@hotwired/stimulus';
import '@cropper/elements';
import { Modal } from 'bootstrap';

/**
 * Gère le recadrage de l'avatar avant upload.
 * - Ouvre un modal Cropper.js v2 dès qu'un fichier est sélectionné.
 * - Sur confirmation : exporte la sélection en 80×80 WebP, l'injecte dans
 *   le file input via DataTransfer et met à jour la prévisualisation.
 * - Sur annulation / fermeture : réinitialise le file input.
 */
export default class extends Controller {
    static targets = ['fileInput', 'cropperImage', 'cropperSelection', 'preview', 'previewPlaceholder'];

    connect() {
        this._confirmed = false;
        this._modal = new Modal(this.element.querySelector('#avatar-crop-modal'));

        this.element.querySelector('#avatar-crop-modal')
            .addEventListener('hidden.bs.modal', () => {
                if (!this._confirmed) {
                    this.fileInputTarget.value = '';
                }
                this._confirmed = false;
            });
    }

    fileChanged(event) {
        const file = event.target.files[0];
        if (!file || !file.type.startsWith('image/')) return;

        const reader = new FileReader();
        reader.onload = (e) => {
            this.cropperImageTarget.setAttribute('src', e.target.result);
            this._modal.show();
        };
        reader.readAsDataURL(file);
    }

    async confirm() {
        const canvas = await this.cropperSelectionTarget.$toCanvas({ width: 80, height: 80 });

        canvas.toBlob((blob) => {
            // Remplacer le fichier dans le input par l'image recadrée 80×80
            const croppedFile = new File([blob], 'avatar.webp', { type: 'image/webp' });
            const dt = new DataTransfer();
            dt.items.add(croppedFile);
            this.fileInputTarget.files = dt.files;

            // Mettre à jour la prévisualisation
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
