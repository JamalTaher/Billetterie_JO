document.addEventListener('DOMContentLoaded', function() {
    const addToCartButtons = document.querySelectorAll('.card button[data-offre-id][data-evenement-id]');
    const panierCountElement = document.getElementById('panier-count');

    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            const offreId = this.getAttribute('data-offre-id');
            const evenementId = this.getAttribute('data-evenement-id');

            fetch(`/panier/ajouter/${offreId}/${evenementId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                   // alert('Offre ajoutée au panier !');
                    if (panierCountElement) {
                        panierCountElement.textContent = data.panierCount;
                    }
                } else {
                    //alert('Erreur lors de l\'ajout au panier.');
                    console.error(data.message);
                }
            })
            .catch(error => {
                console.error('Erreur réseau lors de l\'ajout au panier:', error);
                //alert('Erreur réseau lors de l\'ajout au panier.');
            });
        });
    });

    
    const removeFromCartButtons = document.querySelectorAll('.supprimer-panier-item');

    removeFromCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            console.log('Bouton Supprimer cliqué !'); 
            const offreId = this.getAttribute('data-offre-id');
            const evenementId = this.getAttribute('data-evenement-id');

            fetch(`/panier/supprimer/${offreId}/${evenementId}`, { 
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                   // alert('Élément supprimé du panier !');
                    
                    window.location.reload();
                    
                } else {
                    alert('Erreur lors de la suppression de l\'élément.');
                    console.error(data.message);
                }
            })
            .catch(error => {
                console.error('Erreur réseau lors de la suppression de l\'élément:', error);
                //alert('Erreur réseau lors de la suppression de l\'élément.');
            });
        });
    });
});