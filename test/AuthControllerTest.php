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

    public function testListByUser()
    {
        $controller = new WorkspaceController();
        $request = $this->createMock(Request::class);
        $response = $this->createMock(Response::class);
        $arg = ['userId' => 1];

        $result = $controller->listByUser($request, $response, $arg);

        $this->assertInstanceOf(Response::class, $result);
    }

    public function testLast()
    {
        $controller = new WorkspaceController();
        $request = $this->createMock(Request::class);
        $response = $this->createMock(Response::class);
        $arg = ['userId' => 1];

        $result = $controller->last($request, $response, $arg);

        $this->assertInstanceOf(Response::class, $result);
    }

    public function testGet()
    {
        $controller = new WorkspaceController();
        $request = $this->createMock(Request::class);
        $response = $this->createMock(Response::class);
        $arg = ['userId' => 1, 'wsId' => 'abc123'];

        $result = $controller->get($request, $response, $arg);

        $this->assertInstanceOf(Response::class, $result);
    }

    public function testAdd()
    {
        $controller = new WorkspaceController();
        $request = $this->createMock(Request::class);
        $response = $this->createMock(Response::class);
        $arg = ['userId' => 1];
        $request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn(['name' => 'Test Workspace', 'description' => 'Test Description']);

        $result = $controller->add($request, $response, $arg);

        $this->assertInstanceOf(Response::class, $result);
    }

    public function testUpdate()
    {
        $controller = new WorkspaceController();
        $request = $this->createMock(Request::class);
        $response = $this->createMock(Response::class);
        $arg = ['userId' => 1, 'wsId' => 'abc123'];
        $request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn(['name' => 'Updated Workspace', 'description' => 'Updated Description']);

        $result = $controller->update($request, $response, $arg);

        $this->assertInstanceOf(Response::class, $result);
    }

    public function testActivate()
    {
        $controller = new WorkspaceController();
        $request = $this->createMock(Request::class);
        $response = $this->createMock(Response::class);
        $arg = ['userId' => 1, 'wsId' => 'abc123'];

        $result = $controller->activate($request, $response, $arg);

        $this->assertInstanceOf(Response::class, $result);
    }

    public function testShare()
    {
        $controller = new WorkspaceController();
        $request = $this->createMock(Request::class);
        $response = $this->createMock(Response::class);
        $arg = ['wsId' => 'abc123'];
        $request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn(['user_to_share' => 'user123']);

        $result = $controller->share($request, $response, $arg);

        $this->assertInstanceOf(Response::class, $result);
    }
}