<?php
namespace Budgetcontrol\Workspace\Controller;

use Budgetcontrol\Workspace\Domain\Model\Workspace;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Budgetcontrol\Workspace\Service\WorkspaceService;
use Throwable;

/**
 * Class WorkspaceController
 * 
 * This class is responsible for handling workspace-related operations.
 * It contains methods for creating, updating, and deleting workspaces.
 */
class WorkspaceController {

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

        if(empty($service)){
            return response(["error" => "No workspaces found"],404);
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

        if(empty($service)){
            return response(["error" => "No workspaces found"],404);
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
        if(Workspace::where('uuid',$arg['wsId'])->count() == 0){
            return response(["error" => "No workspaces found"],404);
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
            $wsDescription = $params['description'];
            $toInsert = WorkspaceService::createNewWorkspace($wsName, $wsDescription, $userId);
        }catch(Throwable $e){
            return response(["error" => $e->getMessage()],500);
        }

        return response($toInsert->toArray(),201);
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
        if( Workspace::where('uuid',$arg['wsId'])->count() == 0){
            return response(["error" => "No workspaces found"],404);
        }

        try {
            $requestBody = $request->getParsedBody();
            Workspace::where('uuid',$arg['wsId'])->first()
            ->update(
                $requestBody
            );

            $toUpdate = Workspace::byUuid($arg['wsId'])->first();
        }catch(Throwable $e){
            return response(["error" => $e->getMessage()],500);
        }

        return response($toUpdate->toArray(),201);
    }
}