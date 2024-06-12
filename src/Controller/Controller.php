<?php
namespace Budgetcontrol\Workspace\Controller;

class Controller {

    public function monitor($request, $response, $args) {
        return $response->withJson(['status' => 'ok']);
    }
    
}