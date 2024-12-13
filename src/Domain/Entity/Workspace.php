<?php
namespace Budgetcontrol\Workspace\Domain\Entity;

use Budgetcontrol\Workspace\Service\Traits\Serializer;
use Budgetcontrol\Library\Model\Workspace as ModelWorkspace;
use Budgetcontrol\Library\Model\User;
use Budgetcontrol\Library\Model\WorkspaceSettings;

 final class Workspace {

    use Serializer;

    private readonly ModelWorkspace $workspace;
    private readonly ?WorkspaceSettings $settings;
    private readonly User $user;
    private readonly string $hash;

    public function __construct(ModelWorkspace $ws, ?WorkspaceSettings $settings, User $user)
    {
        $ws->update(
            ['updated_at' => date('Y-m-d H:i:s', time())]
        );

        $this->workspace = $ws;
        $this->user = $user;
        $this->hash = \Ramsey\Uuid\Uuid::uuid4()->toString();
        $this->settings = $settings;
    }

    /**
     * Get the value of workspace
     *
     * @return ModelWorkspace
     */
    public function getWorkspace(): ModelWorkspace
    {
        return $this->workspace;
    }

    /**
     * Get the value of user
     *
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * Get the value of uuid
     *
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }


    /**
     * Get the value of settings
     * @return ?WorkspaceSettings
     */
    public function getSettings(): ?WorkspaceSettings
    {
        return $this->settings;
    }


    /**
     * Set the value of settings
     * @param WorkspaceSettings $settings
     */
    public function setSettings(WorkspaceSettings $settings): self
    {
        $this->settings = $settings;

        return $this;
    }
 }