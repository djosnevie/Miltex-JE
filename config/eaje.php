<?php

return [
    /*
    |--------------------------------------------------------------------------
    | En-tête du bloc de facture dans les journaux DEF
    |--------------------------------------------------------------------------
    | Cette chaîne est utilisée par le JournalParserService pour identifier
    | le début de chaque nouveau bloc (ticket) dans le fichier journal.
    | Elle correspond à la raison sociale imprimée dans le reçu.
    | Peut être modifiée si les fichiers viennent d'une autre entreprise.
    */
    'block_header' => env('EAJE_BLOCK_HEADER', 'M I L T E X   S A R L'),

    /*
    |--------------------------------------------------------------------------
    | Répertoire de stockage des fichiers journaux
    |--------------------------------------------------------------------------
    | Chemin relatif dans le disque "local" (storage/app) où les fichiers
    | JE importés sont conservés après traitement.
    */
    'journals_path' => env('EAJE_JOURNALS_PATH', 'journals'),

    /*
    |--------------------------------------------------------------------------
    | Taux de TVA appliqué en RDC
    |--------------------------------------------------------------------------
    */
    'tva_rate' => 0.16,

    /*
    |--------------------------------------------------------------------------
    | Devise par défaut
    |--------------------------------------------------------------------------
    */
    'default_currency' => 'CDF',

    /*
    |--------------------------------------------------------------------------
    | Seuil de tolérance pour les incohérences arithmétiques (en CDF)
    |--------------------------------------------------------------------------
    */
    'calc_tolerance' => 1.00,
];
