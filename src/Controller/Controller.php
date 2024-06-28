<?php
namespace Budgetcontrol\Workspace\Controller;

use PDO;
use PDOException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Controller {

    public function monitor(Request $request, Response $response)
    {
        return response([
            'success' => true,
            'message' => 'Workspace service is up and running'
        ]);
    }
}