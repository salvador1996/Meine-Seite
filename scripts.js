/* scripts.js */
document.getElementById('cv').addEventListener('change', function(event) {
    const file = event.target.files[0];
    const formData = new FormData();
    formData.append('cv', file);

    fetch('upload_cv.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('nom').value = data.nom;
            document.getElementById('prenom').value = data.prenom;
            document.getElementById('date-naissance').value = data.date_naissance;
            document.getElementById('email').value = data.email;
            document.getElementById('telephone').value = data.telephone;
            document.getElementById('nationalite').value = data.nationalite;
        }
    });
});
