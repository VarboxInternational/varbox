<?php

namespace Varbox\Seed;

use Illuminate\Database\Seeder;
use Varbox\Contracts\PermissionModelContract;

class PermissionsSeeder extends Seeder
{
    /**
     * Mapping structure of admin permissions.
     *
     * @var array
     */
    private $permissions = [
        'Users' => [
            'List' => [
                'group' => 'Users',
                'label' => 'List',
                'guard' => 'admin',
                'name' => 'users-list',
            ],
            'Add' => [
                'group' => 'Users',
                'label' => 'Add',
                'guard' => 'admin',
                'name' => 'users-add',
            ],
            'Edit' => [
                'group' => 'Users',
                'label' => 'Edit',
                'guard' => 'admin',
                'name' => 'users-edit',
            ],
            'Delete' => [
                'group' => 'Users',
                'label' => 'Delete',
                'guard' => 'admin',
                'name' => 'users-delete',
            ],
            'Impersonate' => [
                'group' => 'Users',
                'label' => 'Impersonate',
                'guard' => 'admin',
                'name' => 'users-impersonate',
            ],
        ],
        'Admins' => [
            'List' => [
                'group' => 'Admins',
                'label' => 'List',
                'guard' => 'admin',
                'name' => 'admins-list',
            ],
            'Add' => [
                'group' => 'Admins',
                'label' => 'Add',
                'guard' => 'admin',
                'name' => 'admins-add',
            ],
            'Edit' => [
                'group' => 'Admins',
                'label' => 'Edit',
                'guard' => 'admin',
                'name' => 'admins-edit',
            ],
            'Delete' => [
                'group' => 'Admins',
                'label' => 'Delete',
                'guard' => 'admin',
                'name' => 'admins-delete',
            ],
        ],
        'Roles' => [
            'List' => [
                'group' => 'Roles',
                'label' => 'List',
                'guard' => 'admin',
                'name' => 'roles-list',
            ],
            'Add' => [
                'group' => 'Roles',
                'label' => 'Add',
                'guard' => 'admin',
                'name' => 'roles-add',
            ],
            'Edit' => [
                'group' => 'Roles',
                'label' => 'Edit',
                'guard' => 'admin',
                'name' => 'roles-edit',
            ],
            'Delete' => [
                'group' => 'Roles',
                'label' => 'Delete',
                'guard' => 'admin',
                'name' => 'roles-delete',
            ],
        ],
        'Permissions' => [
            'List' => [
                'group' => 'Permissions',
                'label' => 'List',
                'guard' => 'admin',
                'name' => 'permissions-list',
            ],
            'Add' => [
                'group' => 'Permissions',
                'label' => 'Add',
                'guard' => 'admin',
                'name' => 'permissions-add',
            ],
            'Edit' => [
                'group' => 'Permissions',
                'label' => 'Edit',
                'guard' => 'admin',
                'name' => 'permissions-edit',
            ],
            'Delete' => [
                'group' => 'Permissions',
                'label' => 'Delete',
                'guard' => 'admin',
                'name' => 'permissions-delete',
            ],
        ],
        'Uploads' => [
            'List' => [
                'group' => 'Uploads',
                'label' => 'List',
                'guard' => 'admin',
                'name' => 'uploads-list',
            ],
            'Select' => [
                'group' => 'Uploads',
                'label' => 'Select',
                'guard' => 'admin',
                'name' => 'uploads-select',
            ],
            'Upload' => [
                'group' => 'Uploads',
                'label' => 'Upload',
                'guard' => 'admin',
                'name' => 'uploads-upload',
            ],
            'Download' => [
                'group' => 'Uploads',
                'label' => 'Download',
                'guard' => 'admin',
                'name' => 'uploads-download',
            ],
            'Crop' => [
                'group' => 'Uploads',
                'label' => 'Crop',
                'guard' => 'admin',
                'name' => 'uploads-crop',
            ],
            'Delete' => [
                'group' => 'Uploads',
                'label' => 'Delete',
                'guard' => 'admin',
                'name' => 'uploads-delete',
            ],
        ],
        'Drafts' => [
            'List' => [
                'group' => 'Drafts',
                'label' => 'List',
                'guard' => 'admin',
                'name' => 'drafts-list',
            ],
            'view' => [
                'group' => 'Drafts',
                'label' => 'View',
                'guard' => 'admin',
                'name' => 'drafts-view',
            ],
            'Save' => [
                'group' => 'Drafts',
                'label' => 'Save',
                'guard' => 'admin',
                'name' => 'drafts-save',
            ],
            'Approval' => [
                'group' => 'Drafts',
                'label' => 'Approval',
                'guard' => 'admin',
                'name' => 'drafts-approval',
            ],
            'Publish' => [
                'group' => 'Drafts',
                'label' => 'Publish',
                'guard' => 'admin',
                'name' => 'drafts-publish',
            ],
            'Delete' => [
                'group' => 'Drafts',
                'label' => 'Delete',
                'guard' => 'admin',
                'name' => 'drafts-delete',
            ],
        ],
        'Revisions' => [
            'List' => [
                'group' => 'Revisions',
                'label' => 'List',
                'guard' => 'admin',
                'name' => 'revisions-list',
            ],
            'view' => [
                'group' => 'Revisions',
                'label' => 'View',
                'guard' => 'admin',
                'name' => 'revisions-view',
            ],
            'Rollback' => [
                'group' => 'Revisions',
                'label' => 'Rollback',
                'guard' => 'admin',
                'name' => 'revisions-rollback',
            ],
            'Delete' => [
                'group' => 'Revisions',
                'label' => 'Delete',
                'guard' => 'admin',
                'name' => 'revisions-delete',
            ],
        ],
    ];

    /**
     * Run the database seeds.
     *
     * @param PermissionModelContract $permission
     * @return void
     */
    public function run(PermissionModelContract $permission)
    {
        foreach ($this->permissions as $permissions) {
            foreach ($permissions as $data) {
                if ($permission->where('name', $data['name'])->count() == 0) {
                    $permission->create($data);
                }
            }
        }
    }
}
