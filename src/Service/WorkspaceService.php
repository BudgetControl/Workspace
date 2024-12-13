<?php

namespace Budgetcontrol\Workspace\Service;

use Budgetcontrol\Library\Entity\Wallet as EntityWallet;
use Budgetcontrol\Library\Model\Currency;
use Illuminate\Support\Facades\Log;
use Budgetcontrol\Workspace\Facade\Mail;
use Illuminate\Database\Capsule\Manager as Capsule;
use Budgetcontrol\Workspace\Domain\Entity\Workspace;
use BudgetcontrolLibs\Mailer\View\ShareWorkspaceView;
use Budgetcontrol\Workspace\Exceptions\WorkspaceException;
use Budgetcontrol\Library\Model\Workspace as ModelWorkspace;
use Budgetcontrol\Library\Model\User;
use Budgetcontrol\Library\Model\Wallet;
use Budgetcontrol\Library\ValueObject\WorkspaceSetting;
use Budgetcontrol\Workspace\Domain\Model\WorkspaceSettings;
use Budgetcontrol\Workspace\Domain\Repository\WorkspaceRepository;

/**
 * Represents a service for managing workspaces.
 */
class WorkspaceService
{
    private Workspace $workspace;
    private int $userId;
    protected WorkspaceRepository $repository;

    const CONFIGURATION = 'app_configurations';
    const DEFAULT_CURRENCY = 2;
    const DEFAULT_PAYMENT_TYPE = 1;

    public function __construct(int $userId, string $uuid = null)
    {   
        $this->userId = $userId;
        $this->repository = new WorkspaceRepository();

        if(empty($uuid)) {
            $this->workspace = self::getLastWorkspace($userId);
        }else{

            $workspace = $this->repository->getWorkspaceWithUsers($uuid);
            $workspaceSettings = $this->repository->getWorkspaceSettigs($workspace);
            $user = User::find($userId);

            $this->workspace = new Workspace(
                $workspace,
                $workspaceSettings,
                $user
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
        Log::info("Create new Wallet entry");

        $wallet = new Wallet();
        $wallet->uuid = $uuid;
        $wallet->name = EntityWallet::cache->value;
        $randomColor = '#' . substr(md5(rand()), 0, 6);
        $wallet->color = $randomColor;
        $wallet->type = EntityWallet::cache->value;
        $wallet->balance = 0;
        $wallet->installement_value = 0;
        $wallet->currency = self::DEFAULT_CURRENCY;
        $wallet->exclude_from_stats = 0;
        $wallet->workspace_id = $wsId;
        $wallet->save();

        $defaultCurrency = Currency::where('id',self::DEFAULT_CURRENCY)->first();
        $workspaceSettings = WorkspaceSetting::create($defaultCurrency, self::DEFAULT_PAYMENT_TYPE);

        // 3) setup default settings
        Log::info("Set up default settings");
        $configurations = self::CONFIGURATION;
        $wsSettings = new WorkspaceSettings();
        $wsSettings->setting = $configurations;
        $wsSettings->data = $workspaceSettings;

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
        SELECT DISTINCT w.uuid, w.name, w.updated_at 
        FROM workspaces AS w
        LEFT JOIN workspaces_users_mm AS ws ON ws.workspace_id = w.id
        WHERE ws.user_id = $userId AND w.deleted_at IS NULL
        ORDER BY w.updated_at DESC;
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

    /**
     * Share the workspace with the specified users.
     *
     * @param array $usersToShare An array of users to share the workspace with.
     * @return void
     */
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
                $emailView = new ShareWorkspaceView();
                $emailView->setUserEmail($userFound->email);
                $emailView->setUserName($userFound->name);
                $emailView->setWorkspaceName($this->workspace->getWorkspace()->name);
                $emailView->setUserFrom($this->workspace->getUser()->name);
                $subject = $this->workspace->getUser()->name." has just shared his Wallet (".$this->workspace->getWorkspace()->name.") with you. Access Budget Control to view it";
                
                Mail::send($userFound->email, $subject, $emailView);

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
