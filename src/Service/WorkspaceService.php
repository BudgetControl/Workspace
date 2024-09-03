<?php

namespace Budgetcontrol\Workspace\Service;

use MLAB\SdkMailer\View\Mail as ViewMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Capsule\Manager as Capsule;
use Budgetcontrol\Workspace\Domain\Model\User;
use Budgetcontrol\Workspace\Domain\Model\WorkspaceSettings;
use Budgetcontrol\Workspace\Domain\Entity\Workspace;
use Budgetcontrol\Workspace\Exceptions\WorkspaceException;
use Budgetcontrol\Workspace\Domain\Model\Workspace as ModelWorkspace;
use Budgetcontrol\Workspace\Facade\Mail;

/**
 * Represents a service for managing workspaces.
 */
class WorkspaceService
{
    private Workspace $workspace;
    private int $userId;

    CONST CONFIGURATION = 'app_configurations';

    public function __construct(int $userId, string $uuid = null)
    {   
        $this->userId = $userId;
        if(empty($uuid)) {
            $this->workspace = self::getLastWorkspace($userId);
        }else{
            $this->workspace = new Workspace(
                ModelWorkspace::where('uuid', $uuid)->with('users')->first(),
                WorkspaceSettings::where('workspace_id', ModelWorkspace::where('uuid', $uuid)->first()->id)->first(),
                User::find($userId)
            );
        }
    }

    /**
     * create workspace
     * when user create a new Workspaces he must create a Wallet and setup the default user settings
     */
    public static function createNewWorkspace(string $name, string $wsDescription, int $userId): Workspace
    {
        //check if user id is valid
        if(empty(User::find($userId))) {
            throw new WorkspaceException("No user found", 500);
        }

        // 1) create workspace
        Log::info("Set up default workspace");
        $workspace = new ModelWorkspace();
        $workspace->name = $name;
        $workspace->description = $wsDescription;
        $workspace->user_id = $userId;
        $workspace->uuid = \Ramsey\Uuid\Uuid::uuid4()->toString();
        $workspace->save();

        $workspace->users()->attach(User::find($userId));
        $wsId = $workspace->id;

        if(empty($wsId)) {
            throw new WorkspaceException("No Workspace found", 500);
        }

        // 2) create new wallet
        $uuid = \Ramsey\Uuid\Uuid::uuid4()->toString();
        $dateTIme = date("Y-m-d H:i:s", time());
        Log::info("Create new Wallet entry");
        Capsule::statement('
            INSERT INTO wallets
            (uuid,name,color,type,balance,installementValue,currency,exclude_from_stats,workspace_id)
            VALUES
            ("' . $uuid . '","Cash","#C6C6C6","Cash",0,0,"EUR",0,'.$wsId.')
        ');

        // 3) setup default settings
        Log::info("Set up default settings");
        $configurations = self::CONFIGURATION;
        $wsSettings = new WorkspaceSettings();
        $wsSettings->setting = $configurations;
        $wsSettings->data = json_encode([
            "currency_id" => 1,
            "payment_type_id" => 1
        ]);
        $wsSettings->workspace_id = $wsId;
        $wsSettings->save();

        return new Workspace(
            $workspace,
            WorkspaceSettings::find($wsSettings->id),
            User::find($userId)
        );
    }

    /**
     * retrive WS informations
     */
    public function getWorkspace(): Workspace
    {
        return $this->workspace;
    }

    /**
     * get last used workspace by user ID
     */
    public static function getLastWorkspace(int $userId): Workspace
    {
        $ws = Capsule::select("
        SELECT workspaces.id as wsid FROM workspaces as w
        inner join workspaces_users as ws on ws.workspace_id = workspaces.id
        left join users on ws.workspace_id = users.id
        where workspace_id = $userId and w.user_id = $userId
        order by workspaces.updated_at desc
        limit 1;
        ");

        if(empty($ws)) {
            throw new WorkspaceException("No workspace found", 404);
        }

        $ws = $ws[0];

        return new Workspace(
            ModelWorkspace::find($ws->wsid),
            WorkspaceSettings::where('workspace_id', $ws->wsid)->first(),
            User::find($userId)
        );
    }

    /**
     * get list of workspaces
     */
    public static function getWorkspacesList(int $userId): array
    {
        $ws = Capsule::select("
        SELECT w.uuid, w.name, w.updated_at FROM workspaces as w
        inner join workspaces_users_mm as ws on ws.workspace_id = w.id
        where ws.user_id = $userId and w.deleted_at is null
        order by w.updated_at desc;
        ");

        return $ws;
    }


    /**
     * get list of workspaces
     */
    public static function getWorkspacesUserList(int $userId): array
    {
        $ws = Capsule::select("
        SELECT w.uuid, w.name, w.updated_at FROM workspaces as w
        inner join workspaces_users_mm as ws on ws.workspace_id = w.id
        where ws.user_id = $userId and w.deleted_at is null
        and w.user_id = $userId
        order by w.updated_at desc;
        ");

        return $ws;
    }
    
    /**
     * Sets the current workspace for a user.
     *
     * @param string $wsId The ID of the workspace to set as current.
     * @param int $userId The ID of the user.
     * @return void
     */
    public static function activateWorkspace(string $wsId, int $userId): void
    {
        $workspaces = self::getWorkspacesList($userId);
        foreach($workspaces as $workspace) {
            $current = false;
            if($workspace->uuid == $wsId) {
                $current = true;
            }

            $ws = ModelWorkspace::where('uuid', $wsId)->first();
                $ws->current = $current;
                $ws->save();
        }
    }

    /**
     * Shares a workspace with a user.
     *
     * @param string $wsId The ID of the workspace to be shared.
     * @param User $user The user to share the workspace with.
     * @return void
     */
    public static function shareWorkspace(string $wsId, User $user): void
    {
        $ws = ModelWorkspace::where('uuid', $wsId)->first();
        $ws->users()->attach($user);
    }

    public function shareWith(array $usersToShare): void
    {
        //first remove all relations
        $this->workspace->getWorkspace()->users()->detach();

        //atttach current user
        $this->workspace->getWorkspace()->users()->attach($this->workspace->getUser());

        foreach($usersToShare as $user) {
            $userFound = User::where('uuid', $user['uuid'])->first();
            if(empty($userFound)) {
                Log::error("No user found with id: " . $userFound);
            }

            $this->workspace->getWorkspace()->users()->attach($userFound);

            try{
                $emailView = new ViewMail();
                $emailView->setData([
                    'message' => $this->workspace->getUser()->name." has just shared his Wallet (".$this->workspace->getWorkspace()->name.") with you. Access Budget Control to view it",
                ]);
                Mail::sendMail($user['email'], 'Workspace shared', $emailView);
            } catch (\Throwable $e) {
                Log::error("Error sharing workspace, could not send email: " . $e->getMessage());
            }
        }
        
    }

    public function workspaceRelationsUsers(int $wsId): array
    {
        $currentUserId = $this->userId;
        $users = Capsule::select("
        SELECT users.uuid, users.email, users.name FROM users
        inner join workspaces_users_mm as ws on ws.user_id = users.id
        where ws.workspace_id = $wsId and users.id != $currentUserId;
        ");

        return $users;
    }
}
