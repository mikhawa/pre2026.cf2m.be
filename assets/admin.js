/**
 * Point d'entrée admin : initialise SunEditor sur les textareas
 * ayant la classe 'ea-suneditor-field', sur chaque chargement de page.
 */
import suneditor from 'suneditor';
import plugins from 'suneditor/src/plugins';
import 'suneditor/dist/css/suneditor.min.css';

function initSunEditors() {
    document.querySelectorAll('textarea.ea-suneditor-field').forEach((textarea) => {
        // Éviter la double-initialisation
        if (textarea.dataset.suneditorInitialized) {
            return;
        }
        textarea.dataset.suneditorInitialized = 'true';

        const uploadUrl    = textarea.dataset.suneditorUploadUrl || '/admin/media/upload';
        const editorHeight = textarea.dataset.suneditorHeight    || '450';

        const safePlugins = Object.assign({}, plugins);
        delete safePlugins.imageGallery;
        delete safePlugins.math;
        delete safePlugins.template;

        try {
            const editor = suneditor.create(textarea, {
                plugins: safePlugins,
                height: editorHeight,
                width: '100%',
                defaultTag: 'p',

                buttonList: [
                    ['undo', 'redo'],
                    ['font', 'fontSize', 'formatBlock'],
                    ['paragraphStyle', 'blockquote'],
                    ['bold', 'underline', 'italic', 'strike', 'subscript', 'superscript'],
                    ['fontColor', 'hiliteColor', 'textStyle'],
                    ['removeFormat'],
                    '/',
                    ['outdent', 'indent'],
                    ['align', 'horizontalRule', 'list', 'lineHeight'],
                    ['table', 'link', 'image', 'video', 'audio'],
                    ['fullScreen', 'showBlocks', 'codeView'],
                    ['preview', 'print'],
                    ['dir_ltr', 'dir_rtl'],
                ],

                imageUploadUrl: uploadUrl,
                imageUploadHeader: { 'X-Requested-With': 'XMLHttpRequest' },
                imageResizing: true,
                imageMultipleFile: true,
                imageAccept: '.jpg,.jpeg,.png,.gif,.webp',
                videoResizing: true,

                font: [
                    'Arial', 'Calibri', 'Comic Sans MS', 'Courier New', 'Georgia',
                    'Impact', 'Palatino Linotype', 'Segoe UI', 'Tahoma',
                    'Times New Roman', 'Trebuchet MS', 'Verdana',
                ],
                fontSize: [8, 9, 10, 11, 12, 14, 16, 18, 20, 22, 24, 28, 32, 36, 40, 48, 56, 72],
                formats: ['p', 'div', 'blockquote', 'pre', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
                lineHeights: [
                    { text: '1', value: 1 }, { text: '1.2', value: 1.2 },
                    { text: '1.5', value: 1.5 }, { text: '1.8', value: 1.8 },
                    { text: '2', value: 2 }, { text: '3', value: 3 },
                ],

                tableMerge: true,
                charCounter: true,
                charCounterLabel: 'Caractères',
                showPathLabel: true,
            });

            // Sync contenu → textarea à chaque changement
            editor.onChange = (contents) => {
                textarea.value = contents;
            };

            // Sync avant soumission
            const form = textarea.closest('form');
            if (form) {
                form.addEventListener('submit', () => {
                    textarea.value = editor.getContents();
                });
            }
        } catch (err) {
            console.error('[SunEditor] Erreur initialisation :', err, textarea);
        }
    });
}

// Initialisation au chargement initial
document.addEventListener('DOMContentLoaded', initSunEditors);

// Réinitialisation après navigation Turbo (page complète ou frame)
document.addEventListener('turbo:load',        initSunEditors);
document.addEventListener('turbo:frame-load',  initSunEditors);
