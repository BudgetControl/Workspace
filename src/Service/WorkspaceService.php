<?php

namespace Budgetcontrol\Workspace\Service;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Capsule\Manager as Capsule;
use Budgetcontrol\Workspace\Domain\Model\User;
use Budgetcontrol\Workspace\Domain\Model\WorkspaceSettings;
use Budgetcontrol\Workspace\Domain\Entity\Workspace;
use Budgetcontrol\Workspace\Exceptions\WorkspaceException;
use Budgetcontrol\Workspace\Domain\Model\Workspace as ModelWorkspace;

/**
 * Represents a service for managing workspaces.
 */
class WorkspaceService
{
    private Workspace $workspace;

    CONST CONFIGURATION = 'app_configurations';

    public function __construct(int $userId, string $uuid = null)
    {
        if(empty($uuid)) {
            $this->workspace = self::getLastWorkspace($userId);
        }else{
            $this->workspace = new Workspace(
                ModelWorkspace::where('uuid', $uuid)->first(),
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
        Log::info("Create new Account entry");
        Capsule::statement('
            INSERT INTO accounts
            (uuid,date_time,name,color,type,balance,installementValue,currency,exclude_from_stats,workspace_id)
            VALUES
            ("' . $uuid . '","' . $dateTIme . '","Cash","#C6C6C6","Cash",0,0,"EUR",0,'.$wsId.')
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
        SELECT workspaces.id as wsid FROM budgetV2.workspaces
        inner join workspaces_users as ws on ws.workspace_id = workspaces.id
        left join users on ws.workspace_id = users.id
        where workspace_id = $userId
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
        SELECT w.uuid, w.name, w.updated_at FROM budgetV2.workspaces as w
        inner join workspaces_users_mm as ws on ws.workspace_id = w.id
        where ws.user_id = $userId and w.deleted_at is null
        order by w.updated_at desc;
        ");

        return $ws;
    }
}
