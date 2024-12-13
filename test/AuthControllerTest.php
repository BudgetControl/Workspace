<?php
use PHPUnit\Framework\TestCase;
use Budgetcontrol\Workspace\Controller\WorkspaceController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Budgetcontrol\Test\BaseCase;

class WorkspaceControllerTest extends BaseCase
{
    public function testList()
    {
        $controller = new WorkspaceController();
        $request = $this->createMock(Request::class);
        $response = $this->createMock(Response::class);
        $arg = ['userId' => 1];

        $result = $controller->list($request, $response, $arg);

        $this->assertInstanceOf(Response::class, $result);
    }

}