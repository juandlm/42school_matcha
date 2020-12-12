<?php

namespace Matcha\Controller;

use Matcha\Core\Controller;

class SetupController extends Controller
{
    public function index()
    {
        if (!file_exists(APP . "config/installed")) {
            file_put_contents(APP . "config/installed", time());
            $this->render("index");
            include_once(APP . "config/setup.php");
            exit;
        } else {
            $this->redirect(URL);
        }
    }
}
