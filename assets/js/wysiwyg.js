/**
 * Éditeur WYSIWYG simple et léger
 * Transforme les textarea en éditeurs avec formatage de base
 */

export class SimpleWysiwyg {
    constructor(textarea) {
        this.textarea = textarea;
        this.editor = null;
        this.toolbar = null;
        this.init();
    }    init() {
        // Vérifier si déjà initialisé
        if (this.textarea.hasAttribute('data-wysiwyg-initialized')) {
            return;
        }

        // Marquer comme initialisé
        this.textarea.setAttribute('data-wysiwyg-initialized', 'true');

        // Créer le conteneur principal
        const container = document.createElement('div');
        container.className = 'wysiwyg-container';

        // Insérer le conteneur avant le textarea
        this.textarea.parentNode.insertBefore(container, this.textarea);

        // Créer la barre d'outils
        this.createToolbar();
        container.appendChild(this.toolbar);

        // Créer l'éditeur
        this.createEditor();
        container.appendChild(this.editor);        // Masquer le textarea original en ajoutant une classe CSS
        this.textarea.classList.add('wysiwyg-hidden');
        this.textarea.style.display = 'none';
        container.appendChild(this.textarea);

        // Synchroniser les contenus
        this.syncFromTextarea();
        this.bindEvents();
    }    createToolbar() {
        this.toolbar = document.createElement('div');
        this.toolbar.className = 'wysiwyg-toolbar';

        const buttons = [
            { command: 'bold', icon: 'fas fa-bold', title: 'Gras (Ctrl+B)' },
            { command: 'italic', icon: 'fas fa-italic', title: 'Italique (Ctrl+I)' },
            { command: 'underline', icon: 'fas fa-underline', title: 'Souligné (Ctrl+U)' },
            { type: 'separator' },
            { command: 'insertUnorderedList', icon: 'fas fa-list-ul', title: 'Liste à puces' },
            { command: 'insertOrderedList', icon: 'fas fa-list-ol', title: 'Liste numérotée' },
            { type: 'separator' },
            { command: 'justifyLeft', icon: 'fas fa-align-left', title: 'Aligner à gauche' },
            { command: 'justifyCenter', icon: 'fas fa-align-center', title: 'Centrer' },
            { command: 'justifyRight', icon: 'fas fa-align-right', title: 'Aligner à droite' },
            { type: 'separator' },
            { command: 'createLink', icon: 'fas fa-link', title: 'Insérer un lien' },
            { command: 'unlink', icon: 'fas fa-unlink', title: 'Supprimer le lien' }
        ];

        buttons.forEach(button => {
            if (button.type === 'separator') {
                const separator = document.createElement('span');
                separator.className = 'wysiwyg-separator';
                this.toolbar.appendChild(separator);
            } else {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'wysiwyg-btn';
                btn.innerHTML = `<i class="${button.icon}"></i>`;
                btn.title = button.title;
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.execCommand(button.command);
                });
                this.toolbar.appendChild(btn);
            }
        });

        // Ajouter un bouton d'aide
        this.addHelpButton();
    }

    addHelpButton() {
        const helpBtn = document.createElement('button');
        helpBtn.type = 'button';
        helpBtn.className = 'wysiwyg-btn wysiwyg-help-btn';
        helpBtn.innerHTML = '<i class="fas fa-question-circle"></i>';
        helpBtn.title = 'Aide et raccourcis clavier';
        helpBtn.style.marginLeft = 'auto';

        helpBtn.addEventListener('click', (e) => {
            e.preventDefault();
            this.showHelp();
        });

        this.toolbar.appendChild(helpBtn);
    }

    showHelp() {
        const helpContent = `
            <strong>Raccourcis clavier :</strong><br>
            • Ctrl+B : Gras<br>
            • Ctrl+I : Italique<br>
            • Ctrl+U : Souligné<br><br>
            <strong>Fonctionnalités :</strong><br>
            • Utilisez les boutons pour formater le texte<br>
            • Sélectionnez du texte puis cliquez sur un bouton<br>
            • Le contenu est automatiquement sauvegardé
        `;

        // Créer une popup simple
        const popup = document.createElement('div');
        popup.className = 'wysiwyg-help-popup';
        popup.innerHTML = `
            <div class="wysiwyg-help-content">
                <h4>Guide de l'éditeur</h4>
                ${helpContent}
                <button class="wysiwyg-help-close">Fermer</button>
            </div>
        `;

        // Ajouter au document
        document.body.appendChild(popup);

        // Fermer au clic
        popup.querySelector('.wysiwyg-help-close').addEventListener('click', () => {
            document.body.removeChild(popup);
        });

        popup.addEventListener('click', (e) => {
            if (e.target === popup) {
                document.body.removeChild(popup);
            }
        });
    }

    createEditor() {
        this.editor = document.createElement('div');
        this.editor.className = 'wysiwyg-editor';
        this.editor.contentEditable = true;
        this.editor.setAttribute('data-placeholder', 'Saisissez votre contenu ici...');
    }

    execCommand(command) {
        if (command === 'createLink') {
            const url = prompt('Entrez l\'URL du lien:');
            if (url) {
                document.execCommand(command, false, url);
            }
        } else {
            document.execCommand(command, false, null);
        }
        this.editor.focus();
        this.syncToTextarea();
    }

    syncFromTextarea() {
        // Convertir le contenu du textarea vers l'éditeur
        const content = this.textarea.value;
        this.editor.innerHTML = content || '';
    }

    syncToTextarea() {
        // Synchroniser le contenu de l'éditeur vers le textarea
        this.textarea.value = this.editor.innerHTML;
    }

    bindEvents() {
        // Synchroniser quand le contenu change
        this.editor.addEventListener('input', () => {
            this.syncToTextarea();
        });

        // Gérer les raccourcis clavier
        this.editor.addEventListener('keydown', (e) => {
            if (e.ctrlKey || e.metaKey) {
                switch (e.key) {
                    case 'b':
                        e.preventDefault();
                        this.execCommand('bold');
                        break;
                    case 'i':
                        e.preventDefault();
                        this.execCommand('italic');
                        break;
                    case 'u':
                        e.preventDefault();
                        this.execCommand('underline');
                        break;
                }
            }
        });

        // Mettre à jour l'état des boutons
        this.editor.addEventListener('selectionchange', () => {
            this.updateToolbarState();
        });

        // Synchronisation avant envoi du formulaire
        const form = this.textarea.closest('form');
        if (form) {
            form.addEventListener('submit', () => {
                this.syncToTextarea();
            });
        }
    }

    updateToolbarState() {
        const buttons = this.toolbar.querySelectorAll('.wysiwyg-btn');
        const commands = ['bold', 'italic', 'underline', 'insertUnorderedList', 'insertOrderedList'];

        buttons.forEach((btn, index) => {
            if (index < commands.length) {
                const isActive = document.queryCommandState(commands[index]);
                btn.classList.toggle('active', isActive);
            }
        });
    }
}

// Fonction d'initialisation globale
export function initWysiwygEditors() {
    try {
        // Chercher tous les textarea avec la classe wysiwyg
        const textareas = document.querySelectorAll('textarea.wysiwyg, textarea[data-wysiwyg="true"]');        textareas.forEach(textarea => {
            // Vérifier si ce n'est pas déjà initialisé
            if (!textarea.hasAttribute('data-wysiwyg-initialized')) {
                new SimpleWysiwyg(textarea);
            }
        });        // Auto-détection pour les formulaires d'admin (excluant les rencontres)
        const adminContentTextareas = document.querySelectorAll(
            'textarea[name*="content"]'
        );

        adminContentTextareas.forEach(textarea => {
            // Vérifier si ce n'est pas déjà initialisé
            if (!textarea.parentNode.querySelector('.wysiwyg-container')) {
                new SimpleWysiwyg(textarea);
            }
        });

        console.log('✅ Éditeurs WYSIWYG initialisés avec succès');
    } catch (error) {
        console.error('❌ Erreur lors de l\'initialisation des éditeurs WYSIWYG:', error);
    }
}

// Initialisation automatique
document.addEventListener('DOMContentLoaded', initWysiwygEditors);

// Support pour Turbo (navigation SPA)
document.addEventListener('turbo:load', initWysiwygEditors);

// Export global pour compatibilité
if (typeof window !== 'undefined') {
    window.SimpleWysiwyg = SimpleWysiwyg;
    window.initWysiwygEditors = initWysiwygEditors;
}
