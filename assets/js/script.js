// assets/js/script.js

document.addEventListener('DOMContentLoaded', () => {
    // Gestion de la galerie d'images sur la page de détail
    const mainImage = document.querySelector('.detail-espace-section .main-image');
    const thumbnailImages = document.querySelectorAll('.detail-espace-section .thumbnail-image');

    if (mainImage && thumbnailImages.length > 0) {
        thumbnailImages.forEach(thumbnail => {
            thumbnail.addEventListener('click', () => {
                // Change l'image principale
                mainImage.src = thumbnail.src;

                // Retire la classe 'active' de toutes les vignettes
                thumbnailImages.forEach(t => t.classList.remove('active'));
                // Ajoute la classe 'active' à la vignette cliquée
                thumbnail.classList.add('active');
            });
        });

        // Met la première vignette comme active au chargement
        thumbnailImages[0].classList.add('active');
    }

    // Validation de formulaire simple (exemple sur le formulaire de contact)
    const contactForm = document.querySelector('.contact-form');
    if (contactForm) {
        contactForm.addEventListener('submit', (e) => {
            const nom = contactForm.querySelector('#nom').value.trim();
            const email = contactForm.querySelector('#email').value.trim();
            const message = contactForm.querySelector('#message').value.trim();

            if (!nom || !email || !message) {
                alert('Veuillez remplir tous les champs obligatoires (Nom, Email, Message).');
                e.preventDefault(); // Empêche l'envoi du formulaire
                return;
            }

            if (!isValidEmail(email)) {
                alert('Veuillez entrer une adresse email valide.');
                e.preventDefault();
                return;
            }
            // Si tout est bon, le formulaire sera soumis
        });

        function isValidEmail(email) {
            const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(String(email).toLowerCase());
        }
    }
});