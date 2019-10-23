<?php
namespace Matcha\Controller;

use Matcha\Core\Controller;

class ErrorController extends Controller
{
    public function index() {
        return ($this->render("index"));
    }
}
