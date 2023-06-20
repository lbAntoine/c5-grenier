<?php

namespace App\Controllers;

use App\Models\Articles;
use \Core\View;
use Exception;

/**
 * Reglements controller
 */
class Reglements extends \Core\Controller
{

    /**
     * AFFICHE LA PAGE DES CONDITIONS GENERALES D'UTILISATION
     *
     * @return void
     * @throws \Exception
     */
    public function conditionsAction()
    {

        View::renderTemplate('Reglements/conditions.html.twig', []);
    }

    /**
     * AFFICHE LA PAGE DE LA POLITIQUE DE CONFIDENTIALITE
     *
     * @return void
     * @throws \Exception
     */
    public function confidentAction()
    {

        View::renderTemplate('Reglements/confident.html.twig', []);
    }

    /**
     * AFFICHE LA PAGE DE LA POLITIQUE RELATIVE AUX COOKIES
     *
     * @return void
     * @throws \Exception
     */
    public function cookiesAction()
    {

        View::renderTemplate('Reglements/cookies.html.twig', []);
    }
}
