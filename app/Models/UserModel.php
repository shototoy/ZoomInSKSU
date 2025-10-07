<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['username', 'password', 'role'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = false;
    protected $returnType = 'array';

    public function authenticate($username, $password)
    {
        return $this->where('username', $username)
                    ->where('password', $password)
                    ->first();
    }
}