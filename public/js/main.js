document.addEventListener('DOMContentLoaded', function () {
    // Funcionalidad menú de hamburguesa
    const mobileBtn = document.getElementById('mobile-btn');
    const mobileMenu = document.getElementById('mobile-menu');

    if (mobileBtn && mobileMenu) {
        mobileBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
            if (!mobileMenu.classList.contains('hidden')) {
                mobileMenu.classList.add('animate-slideDown');
                setTimeout(() => {
                    mobileMenu.classList.remove('animate-slideDown');
                }, 300);
            }
        });

        const mobileLinks = mobileMenu.querySelectorAll('a');
        mobileLinks.forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.add('hidden');
            });
        });
    }

    // Función mejorada para vista previa de imágenes
    function setupImagePreview(config) {
        const {
            fileInputId,
            previewId,
            removeBtnId,
            dropzoneId
        } = config;

        //console.log(`Configurando vista previa para: ${fileInputId}`);

        const fileInput = document.getElementById(fileInputId);
        const preview = document.getElementById(previewId);
        const removeBtn = document.getElementById(removeBtnId);
        const dropzone = dropzoneId ? document.getElementById(dropzoneId) : null;

        // Debug: Verificar elementos
        // console.log('Elementos encontrados:', {
        //     fileInput,
        //     preview,
        //     removeBtn,
        //     dropzone
        // });

        // Verificar si todos los elementos necesarios existen
        if (!fileInput || !preview || !removeBtn) {
            //console.error('Faltan elementos requeridos');
            return false;
        }

        const previewImg = preview.querySelector('img');
        if (!previewImg) {
            //console.error('No se encontró la imagen de preview');
            return false;
        }

        // Configurar evento change para el input de archivo
        fileInput.addEventListener('change', function (e) {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                const validTypes = ['image/jpeg', 'image/png', 'image/webp'];

                if (!validTypes.includes(file.type)) {
                    alert('Por favor, sube una imagen válida (JPEG, PNG o WEBP)');
                    this.value = '';
                    return;
                }

                const reader = new FileReader();

                reader.onload = function (e) {
                    previewImg.src = e.target.result;
                    preview.classList.remove('hidden');
                    if (dropzone) dropzone.classList.add('hidden');
                };

                reader.readAsDataURL(file);
            }
        });

        // Configurar botón de eliminar imagen
        removeBtn.addEventListener('click', function (e) {
            e.preventDefault();
            fileInput.value = '';
            preview.classList.add('hidden');
            if (dropzone) dropzone.classList.remove('hidden');
        });

        return true;
    }

    // Inicializar vista previa para el formulario de devolución
    const devolucionPreview = setupImagePreview({
        fileInputId: 'evidencia',
        previewId: 'preview',
        removeBtnId: 'removeImage',
        dropzoneId: 'dropzone'
    });

    // Inicializar vista previa para el formulario de agregar objetos (si existe)
    const agregarPreview = setupImagePreview({
        fileInputId: 'foto',
        previewId: 'preview',
        removeBtnId: 'removeImage',
        dropzoneId: 'dropzone'
    });
});