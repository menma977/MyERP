<?php

/**
 * @noinspection GrazieInspection
 * @noinspection DuplicatedCode
 * @noinspection SpellCheckingInspection
 */

return [
    'unauthorized_access_user' => 'Anda tidak memiliki akses ke data pengguna lain.',
    'successfully' => ':target dengan sukses',
    'success' => [
        'store' => ':target berhasil ditambahkan.',
        'update' => ':target berhasil diubah.',
        'delete' => ':target berhasil dihapus.',
    ],
    'fail' => [
        'action' => [
            'cost' => 'Gagal melakukan :action karena :attribute tidak memiliki :target.',
        ],
        'exist' => [
            'cost' => 'Gagal melakukan :action karena :attribute sudah memiliki :target.',
        ],
        'delete' => [
            'cost' => 'Gagal menghapus :attribute karena masih memiliki :target.',
        ],
    ],
    'job' => [
        'apply' => [
            'already' => 'Anda sudah mengajukan pekerjaan ini.',
            'more_then_tree' => 'Pekerjaan yang dipilih tidak boleh lebih dari :target.',
        ],
    ],
    'validation' => [
        'unique' => [
            'recruiter' => 'Hiring manager, primary recruiter, secondary recruiter, dan tertiary recruiter harus unik.',
        ],
    ],
    'max' => [
        'limit' => ':attribute tidak boleh lebih dari :target.',
    ],
];
