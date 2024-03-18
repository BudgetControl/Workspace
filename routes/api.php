<?php

/**
 * workspace application routes
 */
$app->get('/{userId}/list', '\Budgetcontrol\Workspace\Controller\WorkspaceController:list');
$app->get('/{userId}/last', '\Budgetcontrol\Workspace\Controller\WorkspaceController:last');
$app->get('/{userId}/{wsId}', '\Budgetcontrol\Workspace\Controller\WorkspaceController:get');
$app->post('/{userId}/add', '\Budgetcontrol\Workspace\Controller\WorkspaceController:add');
$app->put('/{userId}/update/{wsId}', '\Budgetcontrol\Workspace\Controller\WorkspaceController:update');

