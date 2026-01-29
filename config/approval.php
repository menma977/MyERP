<?php

use App\Models\Approval\ApprovalGroup;
use App\Models\Role;
use App\Models\User;

return [
    'group' => [
        'user' => User::class,
        'role' => Role::class,
        'group' => ApprovalGroup::class,
    ],
];
