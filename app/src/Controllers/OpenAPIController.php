<?php

namespace App\Controllers;

use Phalcon\Mvc\Controller;

class OpenAPIController extends Controller
{
    public function index()
    {
        $applicationSpecifics = file_get_contents(__DIR__ . '/../../public/openapi.json');
        return json_decode($applicationSpecifics);
    }
}
