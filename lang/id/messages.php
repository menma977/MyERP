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
        'restore' => ':target berhasil dikembalikan.',
        'destroy' => ':target berhasil dihapus permanen.',
        'approve' => ':target berhasil disetujui.',
        'reject' => ':target berhasil ditolak.',
        'cancel' => ':target berhasil dibatalkan.',
        'rollback' => ':target berhasil dikembalikan.',
        'force' => ':target berhasil di perosess.',
    ],
    'fail' => [
        'store' => ':target gagal ditambahkan.',
        'update' => ':target gagal diubah.',
        'restore' => ':target gagal dikembalikan.',
        'approve' => ':target gagal disetujui.',
        'reject' => ':target gagal ditolak.',
        'cancel' => ':target gagal dibatalkan.',
        'rollback' => ':target gagal dikembalikan.',
        'force' => ':target gagal di perosess.',
        'action' => [
            'cost' => 'Gagal melakukan :action karena :attribute tidak memiliki :target.',
        ],
        'exist' => [
            'cost' => 'Gagal melakukan :action karena :attribute sudah memiliki :target.',
        ],
        'delete' => [
            'default' => 'Gagal menghapus :attribute.',
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
