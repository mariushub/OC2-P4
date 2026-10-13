<?php

/**
 * Gère l'envoi d'images par les membres (photo de profil, couverture de livre).
 * L'envoi de fichiers est un point sensible : on vérifie la taille et le vrai type du fichier,
 * et on choisit nous-même son nom pour qu'il soit impossible d'envoyer un script (.php) sur le serveur.
 */
class ImageUploader
{
    private const MAX_SIZE = 2 * 1024 * 1024; // 2 Mo

    // Types d'images acceptés, avec l'extension qu'on donnera au fichier.
    private const ALLOWED_TYPES = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    /**
     * Enregistre l'image envoyée dans le champ $fieldName du formulaire.
     * @param string $fieldName : le nom du champ <input type="file">.
     * @param string $folder : le dossier de destination (ex : AVATAR_PATH).
     * @throws Exception : si le fichier n'est pas une image valide.
     * @return string|null : le nom du fichier enregistré, ou null si aucun fichier n'a été choisi.
     */
    public static function upload(string $fieldName, string $folder): ?string
    {
        $file = $_FILES[$fieldName] ?? null;

        if ($file === null || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE) {
            throw new Exception("L'image ne doit pas dépasser 2 Mo.");
        }
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("L'envoi de l'image a échoué, veuillez réessayer.");
        }
        if ($file['size'] > self::MAX_SIZE) {
            throw new Exception("L'image ne doit pas dépasser 2 Mo.");
        }

        // On lit le vrai type du fichier dans son contenu, et pas dans son nom (un "virus.php" renommé
        // en "photo.jpg" serait sinon accepté).
        $mimeType = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        if (!isset(self::ALLOWED_TYPES[$mimeType])) {
            throw new Exception("Le fichier doit être une image JPG, PNG ou WEBP.");
        }

        // Nom aléatoire + extension choisie par nous : pas de nom piégé, et pas de risque d'écraser un autre fichier.
        $fileName = bin2hex(random_bytes(16)) . '.' . self::ALLOWED_TYPES[$mimeType];

        if (!move_uploaded_file($file['tmp_name'], $folder . $fileName)) {
            throw new Exception("Impossible d'enregistrer l'image.");
        }
        return $fileName;
    }
}
