<?php

namespace App\Controllers;

class HomeController extends Controller
{
    protected static $FILES_PER_PAGE = 10;

    public function index($req, $res)
    {
        $last_files = $this->FileController->get_last_files(HomeController::$FILES_PER_PAGE);

        return $this->view->render($res, 'home.twig', [
            'files'  => $last_files,
            'active' => 'home',
            'title'  => 'Последние загруженные файлы:',
        ]);
    }
}
