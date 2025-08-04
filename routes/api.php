<?php

/**
 * workspace application routes
 */
$app->get('/{userId}/list', '\Budgetcontrol\Workspace\Controller\WorkspaceController:list');
$app->get('/{userId}/by-user/list', '\Budgetcontrol\Workspace\Controller\WorkspaceController:listByUser');
$app->get('/{userId}/last', '\Budgetcontrol\Workspace\Controller\WorkspaceController:last');
$app->get('/{userId}/{wsId}', '\Budgetcontrol\Workspace\Controller\WorkspaceController:get');
$app->post('/{userId}/add', '\Budgetcontrol\Workspace\Controller\WorkspaceController:add');
$app->put('/{userId}/update/{wsId}', '\Budgetcontrol\Workspace\Controller\WorkspaceController:update');
$app->delete('/{wsId}/delete', '\Budgetcontrol\Workspace\Controller\WorkspaceController:delete');
$app->patch('/{userId}/{wsId}/activate', '\Budgetcontrol\Workspace\Controller\WorkspaceController:activate');
$app->post('/{userId}/{wsId}/share', '\Budgetcontrol\Workspace\Controller\WorkspaceController:share');
$app->delete('/{userId}/{wsId}/unshare/{userUuid}', '\Budgetcontrol\Workspace\Controller\WorkspaceController:unShare');

$app->get('/monitor', '\Budgetcontrol\Workspace\Controller\Controller:monitor');