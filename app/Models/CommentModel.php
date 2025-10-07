<?php

namespace App\Models;

use CodeIgniter\Model;

class CommentModel extends Model
{
    protected $table = 'comments';
    protected $primaryKey = 'id';
    protected $allowedFields = ['announcement_id', 'user_id', 'parent_id', 'comment'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = false;

    public function getByAnnouncement($announcementId)
    {
        return $this->db->table('comments c')
                        ->select('c.*, u.username')
                        ->join('users u', 'c.user_id = u.id')
                        ->where('c.announcement_id', $announcementId)
                        ->orderBy('c.created_at', 'ASC')
                        ->get()
                        ->getResult();
    }
}