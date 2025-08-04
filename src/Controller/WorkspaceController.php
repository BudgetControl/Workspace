<?php

namespace Budgetcontrol\Workspace\Controller;

use Budgetcontrol\Library\Entity\Entry;
use Throwable;
use Budgetcontrol\Library\Model\Currency;
use Budgetcontrol\Workspace\Domain\Model\User;
use Budgetcontrol\Library\Model\WorkspaceSettings;
use Budgetcontrol\Workspace\Domain\Model\Workspace as WorkspaceModel;
use Psr\Http\Message\ResponseInterface as Response;
use Budgetcontrol\Workspace\Service\WorkspaceService;
use Budgetcontrol\Library\ValueObject\WorkspaceSetting;
use Budgetcontrol\Workspace\ValueObjects\Wallet;
use Budgetcontrol\Workspace\ValueObjects\Workspace;
use Psr\Http\Message\ServerRequestInterface as Request;
use Budgetcontrol\Library\Entity\Wallet as EntityWallet;

/**
 * Class WorkspaceController
 * 
 * This class is responsible for handling workspace-related operations.
 * It contains methods for creating, updating, and deleting workspaces.
 */
class WorkspaceController
{

    const DEFAULT_CURRENCY = 2;

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
        if (WorkspaceModel::where('uuid', $arg['wsId'])->count() == 0) {
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

            if (empty($params['workspace'])) {
                return response(["error" => "Missing workspace or wallet parameters"], 400);
            }

            $randomColor = '#' . substr(md5(rand()), 0, 6);
            $wallet = new Wallet(
                $params['wallet']['name'] ?? 'Default Wallet',
                $params['wallet']['balance'] ?? 0,
                EntityWallet::from($params['wallet']['type'] ?? 'cache'),
                $params['wallet']['color'] ?? $randomColor,
                $params['wallet']['currency'] ?? self::DEFAULT_CURRENCY,
                $params['wallet']['exclude_from_stats'] ?? false
            );

            $workspace = new Workspace(
                $params['workspace']['name'],
                $params['workspace']['currency'],
                $params['workspace']['payment_type']
            );

            $toInsert = WorkspaceService::createNewWorkspace($workspace, $wallet, $userId);
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
        if (WorkspaceModel::where('uuid', $arg['wsId'])->count() == 0) {
            return response(["error" => "No workspaces found"], 404);
        }

        $params = $request->getParsedBody();

        try {
            $requestBody = $request->getParsedBody();
            $workspace = WorkspaceModel::where('uuid', $arg['wsId'])->first();
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
                $settings->workspace_id = $workspace->id;
            }

            $settings->data = $workspaceSettings;
            $settings->save();

            $toUpdate = WorkspaceModel::byUuid($arg['wsId'])->first();
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
        if (WorkspaceModel::where('uuid', $wsId)->count() == 0) {
            return response(["error" => "No workspaces found"], 404);
        }

        WorkspaceService::activateWorkspace($wsId, $userId);
        return response([], 201);
    }

    /**
     * Shares a workspace with specified users or entities.
     *
     * @param Request $request  The HTTP request object containing share details.
     * @param Response $response The HTTP response object for returning results.
     * @param mixed $arg        Additional arguments, such as workspace identifier.
     *
     * @return Response         The HTTP response after processing the share action.
     */
    public function share(Request $request, Response $response, $arg): Response
    {
        $wsId = $arg['wsId'];
        $params = $request->getParsedBody();

        $userToShare = $params['user_to_share'];

        //check if is an email or uuid
        if (filter_var($userToShare, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $userToShare);
        } else {
            $user = User::where('uuid', $userToShare);
        }

        if ($user->count() == 0) {
            return response(["error" => "No user found"], 404);
        }

        WorkspaceService::shareWorkspace($wsId, $user->first());
        return response([], 201);
    }

    /**
     * Handles the unsharing of a workspace.
     *
     * @param Request $request The HTTP request object.
     * @param Response $response The HTTP response object.
     * @param mixed $arg Additional arguments, typically route parameters.
     * @return Response The HTTP response after processing the unshare action.
     */
    public function unShare(Request $request, Response $response, $arg): Response
    {
        $wsId = $arg['wsId'];
        $userUuid = $arg['userUuid'];

        $user = User::where('uuid', $userUuid);

        if ($user->count() == 0) {
            return response(["error" => "No user found"], 404);
        }

        WorkspaceService::unShareWorkspace($wsId, $user->first());
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
        $workspace = WorkspaceModel::where('uuid', $wsId)->first();
        if (empty($workspace)) {
            return response(["error" => "No workspaces found"], 404);
        }

        $workspace->delete();
        return response([], 201);
    }
}
