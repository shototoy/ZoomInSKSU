<?php

namespace App\Models;

use CodeIgniter\Model;

class AnnouncementModel extends Model
{
    protected $table = 'announcements';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'title', 'content'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = false;

    public function getWithUser($id = null)
    {
        $builder = $this->db->table('announcements a')
                            ->select('a.*, u.username')
                            ->join('users u', 'a.user_id = u.id')
                            ->orderBy('a.created_at', 'DESC');
        
        if ($id) {
            return $builder->where('a.id', $id)->get()->getRow();
        }
        
        return $builder->get()->getResult();
    }
}
