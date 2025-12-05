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
        'restore' => 'The :target restored successfully.',
        'destroy' => 'The :target permanently deleted successfully.',
        'approve' => 'The :target approved successfully.',
        'reject' => 'The :target rejected successfully.',
        'cancel' => 'The :target cancelled successfully.',
        'rollback' => 'The :target rolled back successfully.',
        'force' => 'The :target force successfully.',
    ],
    'fail' => [
        'store' => 'Failed to create :target.',
        'update' => 'Failed to update :target.',
        'restore' => 'Failed to restore :target.',
        'approve' => 'Failed to approve :target.',
        'reject' => 'Failed to reject :target.',
        'cancel' => 'Failed to cancel :target.',
        'rollback' => 'Failed to rollback :target.',
        'force' => 'Failed to force :target.',
        'action' => [
            'cost' => 'Failed to :action because :attribute does not have :target.',
        ],
        'exist' => [
            'cost' => 'Failed to :action because :attribute alredy have :target.',
        ],
        'delete' => [
            'default' => 'Failed to delete :attribute.',
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
