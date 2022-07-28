<?php

declare(strict_types=1);

namespace App\Tools;

class ErrorsMessages
{
    /******************************
     ********** ROUTING *********** 
     ******************************/
    public const ROUTE_INEXISTANTE = 'Oups, cette adresse est introuvable.';

    /******************************
     ********* CATEGORY *********** 
     ******************************/
    public const CATEGORIE_INEXISTANTE = 'Erreur, aucune catégorie trouvée avec cet identifiant.';

    /******************************
     ********* TODO *********** 
     ******************************/
    public const TODO_INEXISTANT = 'Erreur, aucun todo trouvé avec cet identifiant.';
}
