import { Controller } from '@hotwired/stimulus';
import suneditor from 'suneditor';
import plugins from 'suneditor/src/plugins';
// Ce module CSS est mappé par Symfony à un data-URI JS qui injecte le <link>
import 'suneditor/dist/css/suneditor.min.css';

export default class extends Controller {
    static values = {
        uploadUrl: { type: String, default: '/admin/media/upload' },
        height:    { type: String, default: '450' },
    };

    connect() {
        try {
            // Certains plugins nécessitent des URL externes (imageGallery → galerie, math → KaTeX)
            // On les exclut pour éviter les erreurs d'initialisation
            const safePlugins = Object.assign({}, plugins);
            delete safePlugins.imageGallery;
            delete safePlugins.math;
            delete safePlugins.template;

            this.editor = suneditor.create(this.element, {
                plugins: safePlugins,
                height: this.heightValue,
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

                imageUploadUrl: this.uploadUrlValue,
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
                    { text: '1',   value: 1   },
                    { text: '1.2', value: 1.2 },
                    { text: '1.5', value: 1.5 },
                    { text: '1.8', value: 1.8 },
                    { text: '2',   value: 2   },
                    { text: '3',   value: 3   },
                ],

                tableMerge: true,
                charCounter: true,
                charCounterLabel: 'Caractères',
                showPathLabel: true,

                lang: this._frLang(),
            });

            // Sync textarea à chaque modification
            this.editor.onChange = (contents) => {
                this.element.value = contents;
            };

            // Sync avant soumission du formulaire
            const form = this.element.closest('form');
            if (form) {
                this._onSubmit = () => { this.element.value = this.editor.getContents(); };
                form.addEventListener('submit', this._onSubmit);
            }
        } catch (err) {
            console.error('[SunEditor] Erreur initialisation :', err);
        }
    }

    disconnect() {
        const form = this.element.closest('form');
        if (form && this._onSubmit) {
            form.removeEventListener('submit', this._onSubmit);
        }
        if (this.editor) {
            this.editor.destroy();
            this.editor = null;
        }
    }

    _frLang() {
        return {
            code: 'fr',
            toolbar: {
                default: 'Par défaut', save: 'Sauvegarder', font: 'Police',
                formats: 'Formats', fontSize: 'Taille', bold: 'Gras',
                underline: 'Souligné', italic: 'Italique', strike: 'Barré',
                subscript: 'Indice', superscript: 'Exposant',
                removeFormat: 'Effacer le format', fontColor: 'Couleur de police',
                hiliteColor: 'Surbrillance', indent: 'Indenter', outdent: 'Désindenter',
                align: 'Aligner', alignLeft: 'Gauche', alignRight: 'Droite',
                alignCenter: 'Centrer', alignJustify: 'Justifier',
                list: 'Liste', orderList: 'Ordonnée', unorderList: 'Non ordonnée',
                horizontalRule: 'Ligne horizontale', hr_solid: 'Solide',
                hr_dotted: 'Pointillé', hr_dashed: 'Tiret',
                table: 'Tableau', link: 'Lien', image: 'Image',
                video: 'Vidéo', audio: 'Audio',
                fullScreen: 'Plein écran', showBlocks: 'Blocs', codeView: 'Code HTML',
                undo: 'Annuler', redo: 'Rétablir', preview: 'Aperçu', print: 'Imprimer',
                tag_p: 'Paragraphe', tag_div: 'Normal (DIV)', tag_h: 'Titre',
                tag_blockquote: 'Citation', tag_pre: 'Code',
                template: 'Modèle', lineHeight: 'Interligne',
                paragraphStyle: 'Style paragraphe', textStyle: 'Style texte',
                imageGallery: 'Galerie', dir_ltr: 'Gauche → Droite', dir_rtl: 'Droite → Gauche',
            },
            dialogBox: {
                linkBox: {
                    title: 'Insérer un lien', url: 'URL du lien',
                    text: 'Texte à afficher', newWindowCheck: 'Nouvelle fenêtre',
                    downloadLinkCheck: 'Lien de téléchargement', bookmark: 'Signet',
                },
                imageBox: {
                    title: 'Insérer une image', file: 'Fichier',
                    url: "URL de l'image", altText: 'Texte alternatif',
                },
                videoBox: { title: 'Insérer une vidéo', file: 'Fichier', url: 'URL (YouTube, Vimeo…)' },
                audioBox: { title: 'Insérer un audio', file: 'Fichier', url: 'URL audio' },
                browser: { tags: 'Tags', search: 'Rechercher' },
                caption: 'Description', close: 'Fermer',
                submitButton: 'Valider', revertButton: 'Réinitialiser',
                proportion: 'Proportions', basic: 'Basique',
                left: 'Gauche', right: 'Droite', center: 'Centre',
                width: 'Largeur', height: 'Hauteur', size: 'Taille', ratio: 'Ratio',
            },
            controller: {
                edit: 'Modifier', unlink: 'Délier', remove: 'Supprimer',
                insertRowAbove: 'Ligne au-dessus', insertRowBelow: 'Ligne en-dessous',
                deleteRow: 'Supprimer ligne', insertColumnBefore: 'Colonne avant',
                insertColumnAfter: 'Colonne après', deleteColumn: 'Supprimer colonne',
                fixedColumnWidth: 'Largeur fixe',
                resize100: '100%', resize75: '75%', resize50: '50%', resize25: '25%',
                mirrorHorizontal: 'Miroir H', mirrorVertical: 'Miroir V',
                rotateLeft: 'Rotation gauche', rotateRight: 'Rotation droite',
                maxSize: 'Taille max', minSize: 'Taille min',
                tableHeader: 'En-tête tableau', mergeCells: 'Fusionner',
                splitCells: 'Diviser', HorizontalSplit: 'Div. horizontale',
                VerticalSplit: 'Div. verticale',
            },
            menu: {
                spaced: 'Espacé', bordered: 'Bordé', neon: 'Néon',
                translucent: 'Translucide', shadow: 'Ombre', code: 'Code',
            },
        };
    }
}
