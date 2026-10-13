<?php

/**
 * Système d'autoload.
 * A chaque fois que PHP a besoin d'une classe qu'il ne connaît pas encore, il appelle cette fonction.
 * Elle cherche dans les dossiers services, models, controllers et views un fichier qui porte
 * le nom de la classe (ex : la classe BookManager est dans models/BookManager.php) et l'inclut.
 * C'est pour cela qu'il faut toujours respecter la règle : 1 classe = 1 fichier du même nom.
 */
spl_autoload_register(function($className) {
    $folders = ['services', 'models', 'controllers', 'views'];

    foreach ($folders as $folder) {
        $file = $folder . '/' . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});
