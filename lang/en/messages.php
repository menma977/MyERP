<?php

/**
 * @noinspection GrazieInspection
 * @noinspection DuplicatedCode
 * @noinspection SpellCheckingInspection
 */

return [
    'unauthorized_access_user' => 'You are not authorized to access another users data.',
    'successfully' => ':target Successfully',
    'success' => [
        'store' => 'The :target created successfully.',
        'update' => 'The :target updated successfully.',
        'delete' => 'The :target deleted successfully.',
    ],
    'fail' => [
        'action' => [
            'cost' => 'Failed to :action because :attribute does not have :target.',
        ],
        'exist' => [
            'cost' => 'Failed to :action because :attribute alredy have :target.',
        ],
        'delete' => [
            'cost' => 'Failed to delete :attribute because it has :target.',
        ],
    ],
    'job' => [
        'apply' => [
            'already' => 'You have already applied for this job.',
            'more_then' => 'You can only apply for :target jobs at a time.',
        ],
    ],
    'validation' => [
        'unique' => [
            'recruiter' => 'The hiring manager, primary recruiter, secondary recruiter, and tertiary recruiter must be unique.',
        ],
    ],
    'max' => [
        'limit' => ':attribute cannot be greater than :target.',
    ],
];
