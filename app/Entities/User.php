<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class User extends Entity
{
    protected $casts = [
        'id'         => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $datamap = [];
    protected $dates   = ['created_at', 'updated_at'];
    protected $casts2  = [];
}