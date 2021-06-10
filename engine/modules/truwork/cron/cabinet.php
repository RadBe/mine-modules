<?php

include_once '_cron.php';

/* @var \App\Core\Game\Permissions\PermissionsManager $permissionManager */
$permissionManager = $app->make(\App\Core\Game\Permissions\PermissionsManager::class);
$userGroupsModel = \App\Cabinet\Models\UserGroupsModel::getInstance();
$userGroups = $userGroupsModel->getExpiredGroups();
\App\Core\Support\AttachRelationEntity::make($userGroups, $app->make(\App\Core\Models\UserModel::class), 'user_id');
\App\Core\Support\AttachRelationEntity::make($userGroups, $app->make(\App\Core\Models\ServersModel::class), 'server_id');

foreach ($userGroups as $userGroup)
{
    $permissionManager->getPermissions($userGroup->_server_id)->removeGroup($userGroup->_user_id, $userGroup->group_name);
    $userGroupsModel->delete($userGroup);
}
if (!empty($userGroups)) {
    dispatch(new \App\Cabinet\Events\GroupsDeleteEvent($userGroups));
}
print 'ok';
