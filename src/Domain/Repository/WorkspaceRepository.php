<?php
declare(strict_types=1);

namespace Budgetcontrol\Workspace\Domain\Repository;

use Budgetcontrol\Library\Model\Workspace;
use Budgetcontrol\Library\Model\WorkspaceSettings;
use Budgetcontrol\Library\ValueObject\WorkspaceSetting;

class WorkspaceRepository
{
    /**
     * Retrieves a Workspace entity by its UUID.
     *
     * @param string $uuid The UUID of the workspace to retrieve.
     * @return Workspace|null The Workspace entity if found, or null if not found.
     */
    public function getWorkspaceByUuid(string $uuid): ?Workspace
    {
        $workspace = Workspace::where('uuid', $uuid)->first();

        if (empty($workspace)) {
            return null;
        }

        return $workspace;
    }

    /**
     * Retrieves the settings for a given workspace.
     *
     * @param Workspace $workspace The workspace for which to retrieve the settings.
     * @return WorkspaceSettings|null The settings of the workspace, or null if not found.
     */
    public function getWorkspaceSettigs(Workspace $workspace): ?WorkspaceSettings
    {
        $settings = WorkspaceSettings::where('workspace_id', $workspace->id)->first();

        if (empty($settings)) {
            return null;
        }

        return $settings;

    }

 
    /**
     * Retrieves a workspace along with its associated users by the given UUID.
     *
     * @param string $uuid The UUID of the workspace to retrieve.
     * @return Workspace|null The workspace with its users, or null if not found.
     */
    public function getWorkspaceWithUsers(string $uuid): ?Workspace
    {
        $workspace = Workspace::with('users')->where('uuid', $uuid)->first();

        if (empty($workspace)) {
            return null;
        }

        return $workspace;
    }
}