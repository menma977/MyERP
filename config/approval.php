<?php

use App\Models\Approval\ApprovalGroup;
use App\Models\User;
use Spatie\Permission\Models\Role;

return [
    'group' => [
        'user' => User::class,
        'role' => Role::class,
        'group' => ApprovalGroup::class,
    ],
];
