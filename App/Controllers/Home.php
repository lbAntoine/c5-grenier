<?php

namespace App\Controllers;

use App\Models\Articles;
use \Core\View;
use Exception;

/**
 * Home controller
 */
class Home extends \Core\Controller
{

    /**
     * AFFICHE LA PAGE D'ACCUEIL
     *
     * @return void
     * @throws \Exception
     */
    public function indexAction()
    {

        View::renderTemplate('Home/index.html', []);
    }
}
