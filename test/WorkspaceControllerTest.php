<?php

namespace Budgetcontrol\Test;

use MLAB\PHPITest\Entity\Json;
use Budgetcontrol\Test\BaseCase;
use MLAB\PHPITest\Assertions\JsonAssert;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Budgetcontrol\Workspace\Controller\WorkspaceController;
use Budgetcontrol\Workspace\Domain\Repository\WorkspaceRepository;

class WorkspaceControllerTest extends BaseCase
{

    public function testList()
    {
        $request = $this->createMock(Request::class);
        $response = $this->createMock(Response::class);
        $arg = ['userId' => 1];

        $result = $this->controller->list($request, $response, $arg);

        $contentArray = json_decode((string) $result->getBody());
        $assertionContent = new JsonAssert(new Json($contentArray));
        $assertionContent->assertJsonStructure(
            file_get_json(__DIR__ . '/assertions/workspace-list.json')
        );

        $this->assertEquals(200, $result->getStatusCode());
    }

    public function testLast()
    {
        $request = $this->createMock(Request::class);
        $response = $this->createMock(Response::class);
        $arg = ['userId' => 1];

        $result = $this->controller->last($request, $response, $arg);

        $contentArray = json_decode((string) $result->getBody());
        $assertionContent = new JsonAssert(new Json($contentArray));
        $assertionContent->assertJsonStructure(
            file_get_json(__DIR__ . '/assertions/last-workspace.json')
        );

        $this->assertEquals(200, $result->getStatusCode());
        $this->assertEquals('application/json', $result->getHeaderLine('Content-Type'));
        $this->assertEquals($contentArray->user->email, 'mario.rossi@email.it');
    }

    public function testGet()
    {
        $request = $this->createMock(Request::class);
        $response = $this->createMock(Response::class);
        $arg = ['userId' => 1, 'wsId' => '4373a9a3-a481-4d5a-b8fe-c0571be7efe3'];

        $result = $this->controller->get($request, $response, $arg);

        $contentArray = json_decode((string) $result->getBody());
        $assertionContent = new JsonAssert(new Json($contentArray));
        $assertionContent->assertJsonStructure(
            file_get_json(__DIR__ . '/assertions/workspace.json')
        );

        $this->assertEquals(200, $result->getStatusCode());
        $this->assertEquals('application/json', $result->getHeaderLine('Content-Type'));
        $this->assertEquals($contentArray->user->email, 'mario.rossi@email.it');
    }

    public function testAdd()
    {
        $request = $this->createMock(Request::class);
        $response = $this->createMock(Response::class);
        $arg = ['userId' => 1];

        $payload = [
            'workspace' => [
                'name' => 'Test Workspace',
                'currency' => 2,
                'payment_type' => 1
            ],
            'wallet' => [
                'name' => 'Test Wallet',
                'balance' => 0,
                'type' => 'bank',
                'color' => '#3dafac',
                'currency' => 2,
                'exclude_from_stats' => 0
            ]
        ];

        $request->method('getParsedBody')->willReturn($payload);

        $result = $this->controller->add($request, $response, $arg);
        $contentArray = json_decode((string) $result->getBody());

        $this->assertEquals(201, $result->getStatusCode());
        $this->assertEquals($contentArray->workspace->name, 'Test Workspace');
    }

    public function testAddWithRelations()
    {
        $request = $this->createMock(Request::class);
        $response = $this->createMock(Response::class);
        $arg = ['userId' => 1];

        $payload = [
            'name' => 'Test Workspace with Relations',
            'description' => 'Test Workspace Description',
            'shareWith' => ['4373a9a3-a481-4d5a-b8fe-c2571be7efe3'],
        ];

        $request->method('getParsedBody')->willReturn($payload);

        $result = $this->controller->add($request, $response, $arg);
        $contentArray = json_decode((string) $result->getBody());

        $this->assertEquals(201, $result->getStatusCode());
        $this->assertEquals($contentArray->workspace->name, 'Test Workspace with Relations');

        $workspaceRepository = new WorkspaceRepository();
        $workspace = $workspaceRepository->getWorkspaceWithUsers($contentArray->workspace->uuid);

        $this->assertEquals($workspace->users->count(), 2);
        foreach ($workspace->users as $user) {
            $this->assertContains($user->email, ['mario.verdi@email.it', 'mario.rossi@email.it']);
        }

        // assert email sended
        $email = file_get_contents(__DIR__ . '/assertions/email.txt');
        $this->assertStringContainsString('This email was sent to mario.verdi@email.it.', $email);
        $this->assertStringContainsString('Check out [Test Workspace with Relations], a workspace that Mario has shared with you. You can find it in your workspace settings and on the sidebar.', $email);
    }

    public function testUpdate()
    {
        $request = $this->createMock(Request::class);
        $response = $this->createMock(Response::class);
        $arg = ['userId' => 1, 'wsId' => '4373a9a3-a481-4d5a-b8fe-c0571be7efe3'];

        $payload = [
            'name' => 'Test Workspace update',
            'description' => 'Test Workspace Description update',
            'currency' => 1,
            'payment_type' => 1,
        ];

        $request->method('getParsedBody')->willReturn($payload);

        $result = $this->controller->update($request, $response, $arg);
        $contentArray = json_decode((string) $result->getBody());

        $this->assertEquals(201, $result->getStatusCode());
        $this->assertEquals($contentArray->name, 'Test Workspace update');
        $this->assertEquals($contentArray->description, 'Test Workspace Description update');
    }

    public function testActivate()
    {
        $request = $this->createMock(Request::class);
        $response = $this->createMock(Response::class);
        $arg = ['userId' => 1, 'wsId' => '4373a9a3-a481-4d5a-b8fe-c0571be7efe3'];

        $result = $this->controller->activate($request, $response, $arg);

        $this->assertEquals(201, $result->getStatusCode());
    }

    // public function testShare()
    // {
    //     $request = $this->createMock(Request::class);
    //     $response = $this->createMock(Response::class);
    //     $arg = ['userId' => 1, 'wsId' => '4373a9a3-a481-4d5a-b8fe-c0571be7efe3'];

    //     $payload = [
    //         'user_to_share' => 'Test Workspace Description update',
    //     ];

    //     $request->method('getParsedBody')->willReturn($payload);

    //     $result = $this->controller->share($request, $response, $arg);
    //     $contentArray = json_decode((string) $result->getBody());

    //     $this->assertEquals(201, $result->getStatusCode());
    // }

    public function testDelete()
    {
        $request = $this->createMock(Request::class);
        $response = $this->createMock(Response::class);
        $arg = ['userId' => 1, 'wsId' => '4373a9a3-a481-4d5a-b8fe-c0571be7efe3'];

        $result = $this->controller->delete($request, $response, $arg);
        $this->assertEquals(201, $result->getStatusCode());
    }
}
