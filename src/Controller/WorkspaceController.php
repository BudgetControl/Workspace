<?php

namespace Budgetcontrol\Workspace\Controller;

use Throwable;
use Budgetcontrol\Library\Model\Currency;
use Budgetcontrol\Workspace\Domain\Model\User;
use Budgetcontrol\Library\Model\WorkspaceSettings;
use Budgetcontrol\Workspace\Domain\Model\Workspace;
use Psr\Http\Message\ResponseInterface as Response;
use Budgetcontrol\Workspace\Service\WorkspaceService;
use Budgetcontrol\Library\ValueObject\WorkspaceSetting;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class WorkspaceController
 * 
 * This class is responsible for handling workspace-related operations.
 * It contains methods for creating, updating, and deleting workspaces.
 */
class WorkspaceController
{

    /**
     * List all workspaces.
     *
     * @param Request $request The HTTP request object.
     * @param Response $response The HTTP response object.
     * @param mixed $arg Additional arguments.
     * @return Response The HTTP response object.
     */
    public function list(Request $request, Response $response, $arg): Response
    {
        $userId = $arg['userId'];
        $service = WorkspaceService::getWorkspacesList($userId);

        if (empty($service)) {
            return response(["error" => "No workspaces found"], 404);
        }

        return response($service);
    }

    /**
     * List all workspaces of current user.
     *
     * @param Request $request The HTTP request object.
     * @param Response $response The HTTP response object.
     * @param mixed $arg Additional arguments.
     * @return Response The HTTP response object.
     */
    public function listByUser(Request $request, Response $response, $arg): Response
    {
        $userId = $arg['userId'];
        $service = WorkspaceService::getWorkspacesUserList($userId);

        if (empty($service)) {
            return response(["error" => "No workspaces found"], 404);
        }

        return response($service);
    }

    /**
     * Handle the last request.
     *
     * @param Request $request The HTTP request object.
     * @param Response $response The HTTP response object.
     * @param mixed $arg The argument passed to the controller.
     * @return Response The HTTP response object.
     */
    public function last(Request $request, Response $response, $arg): Response
    {
        $userId = $arg['userId'];
        $service = WorkspaceService::getLastWorkspace($userId);

        if (empty($service)) {
            return response(["error" => "No workspaces found"], 404);
        }

        return response($service->toArray());
    }


    /**
     * Retrieves a resource.
     *
     * @param Request $request The HTTP request object.
     * @param Response $response The HTTP response object.
     * @param mixed $arg The argument passed to the controller.
     * @return Response The HTTP response object.
     */
    public function get(Request $request, Response $response, $arg): Response
    {
        // check if ws exists
        if (Workspace::where('uuid', $arg['wsId'])->count() == 0) {
            return response(["error" => "No workspaces found"], 404);
        }

        $userId = $arg['userId'];
        $workspaceService = new WorkspaceService($userId, $arg['wsId']);
        $workspace = $workspaceService->getWorkspace();

        return response($workspace->toArray());
    }


    /**
     * Add a new workspace.
     *
     * @param Request $request The HTTP request object.
     * @param Response $response The HTTP response object.
     * @param mixed $arg Additional arguments.
     * @return Response The updated HTTP response object.
     */
    public function add(Request $request, Response $response, $arg): Response
    {
        try {
            $userId = $arg['userId'];
            $params = $request->getParsedBody();
            $wsName = $params['name'];
            $wsDescription = $params['description'] ?? '';
            $toInsert = WorkspaceService::createNewWorkspace($wsName, $wsDescription, $userId);
            $wsId = $toInsert->getWorkspace()->uuid;

            $service = new WorkspaceService($arg['userId'], $wsId);
            if(isset($params['shareWith']) && !empty($params['shareWith'])) {
                $service->shareWith($params['shareWith']);
            }
        } catch (Throwable $e) {
            return response(["error" => $e->getMessage()], 500);
        }

        return response($toInsert->toArray(), 201);
    }


    /**
     * Update a workspace.
     *
     * @param Request $request The HTTP request object.
     * @param int $id The ID of the workspace to update.
     * @return Response The HTTP response object.
     */
    public function update(Request $request, Response $response, $arg): Response
    {
        // check if ws exists
        if (Workspace::where('uuid', $arg['wsId'])->count() == 0) {
            return response(["error" => "No workspaces found"], 404);
        }

        $params = $request->getParsedBody();

        try {
            $requestBody = $request->getParsedBody();
            $workspace = Workspace::where('uuid', $arg['wsId'])->first();
            $workspace->name = $requestBody['name'];
            $workspace->description = $requestBody['description'] ?? null;
            $workspace->save();

            $service = new WorkspaceService($arg['userId'], $arg['wsId']);
            if(isset($params['shareWith']) && !empty($params['shareWith'])) {
                $service->shareWith($params['shareWith']);
            }

            // Update workspace setting
            $currency = Currency::find($params['currency']);
            $workspaceSettings = WorkspaceSetting::create($currency, $params['payment_type']);
            $settings = WorkspaceSettings::where('workspace_id', $workspace->id)->first();

            if(empty($settings)) {
                $settings = new WorkspaceSettings();
            }

            $settings->setting = $workspaceSettings;
            $settings->save();

            $toUpdate = Workspace::byUuid($arg['wsId'])->first();
        } catch (Throwable $e) {
            return response(["error" => $e->getMessage()], 500);
        }

        return response($toUpdate->toArray(), 201);
    }

    /**
     * Update the current workspace.
     *
     * @param Request $request The HTTP request object.
     * @param Response $response The HTTP response object.
     * @param mixed $arg Additional arguments.
     * @return Response The updated HTTP response object.
     */
    public function activate(Request $request, Response $response, $arg): Response
    {
        $wsId = $arg['wsId'];
        $userId = $arg['userId'];
        if (Workspace::where('uuid', $wsId)->count() == 0) {
            return response(["error" => "No workspaces found"], 404);
        }

        WorkspaceService::activateWorkspace($wsId, $userId);
        return response([], 201);
    }

    public function share(Request $request, Response $response, $arg): Response
    {
        $wsId = $arg['wsId'];
        $params = $request->getParsedBody();

        $userToShare = $params['user_to_share'];
        $user = $user = User::where('uuid', $userToShare);

        if ($user->count() == 0) {
            return response(["error" => "No user found"], 404);
        }

        WorkspaceService::shareWorkspace($wsId, $user->first());
        return response([], 201);
    }

    /**
     * Deletes a workspace.
     *
     * @param Request $request The HTTP request object.
     * @param Response $response The HTTP response object.
     * @param mixed $arg Additional arguments.
     * @return Response The HTTP response object after deletion.
     */
    public function delete(Request $request, Response $response, $arg): Response
    {
        $wsId = $arg['wsId'];
        $workspace = Workspace::where('uuid', $wsId)->first();
        if (empty($workspace)) {
            return response(["error" => "No workspaces found"], 404);
        }

        $workspace->delete();
        return response([], 201);
    }
}
