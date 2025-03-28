document.addEventListener('DOMContentLoaded', function() {
    const addToCartButtons = document.querySelectorAll('.card button[data-offre-id]');

    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            const offreId = this.getAttribute('data-offre-id');
            console.log('Offre ajoutée au panier avec l\'ID:', offreId);

            fetch('/panier/ajouter/' + offreId, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Offre ajoutée au panier !');
                    // Ici, vous pourriez mettre à jour l'affichage du panier dans la barre de navigation
                } else {
                    alert('Erreur lors de l\'ajout au panier.');
                    console.error(data.message);
                }
            })
            .catch(error => {
                console.error('Erreur réseau lors de l\'ajout au panier:', error);
                alert('Erreur réseau lors de l\'ajout au panier.');
            });
        });
    });
});