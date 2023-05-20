<?php

namespace App\Controllers;

use App\Models\Articles;
use \Core\View;
use Exception;

/**
 * Admin controller
 */
class Admin extends \Core\Controller
{

    /**
     * AFFICHE LA PAGE DE STATISTIQUES
     *
     * @return void
     * @throws \Exception
     */
    public function statsAction()
    {

        View::renderTemplate('Admin/stats.html', []);
    }
}
