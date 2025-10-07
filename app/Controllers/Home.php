<?php

namespace App\Controllers;

class Home extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        if (!session()->get('user_id')) {
            return view('login');
        }

        $data = [];
        
        $data['announcements'] = $this->db->query(
            "SELECT a.*, u.username, u.profile_name FROM announcements a 
             JOIN users u ON a.user_id = u.id 
             ORDER BY a.created_at DESC"
        )->getResult();

        foreach ($data['announcements'] as $announcement) {
            $announcement->images = $this->getAnnouncementImages($announcement->id);
            $announcement->reaction_count = $this->getReactionCount($announcement->id);
            $announcement->user_reacted = $this->hasUserReacted($announcement->id, session()->get('user_id'));
        }

        if (session()->get('role') === 'admin') {
            $data['users'] = $this->db->table('users')->orderBy('created_at', 'DESC')->get()->getResult();
        }

        $data['view_mode'] = null;
        $data['announcement'] = null;
        $data['comments'] = [];

        return view('dashboard', $data);
    }

    public function login()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        
        $user = $this->db->table('users')
                         ->where('username', $username)
                         ->where('password', $password)
                         ->get()
                         ->getRow();
        
        if ($user && in_array($user->role, ['admin', 'student'])) {
            session()->set([
                'user_id' => $user->id,
                'username' => $user->username,
                'profile_name' => $user->profile_name,
                'role' => $user->role
            ]);
            
            return redirect()->to('/');
        }
        
        return redirect()->to('/')->with('error', 'Invalid credentials');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }

    public function createAnnouncement()
    {
        if (session()->get('role') === 'admin') {
            $data = [
                'user_id' => session()->get('user_id'),
                'title' => $this->request->getPost('title'),
                'content' => $this->request->getPost('content')
            ];
            
            $this->db->table('announcements')->insert($data);
            $announcementId = $this->db->insertID();
            
            $images = $this->request->getFileMultiple('images');
            if ($images) {
                $order = 0;
                foreach ($images as $image) {
                    if ($image->isValid() && !$image->hasMoved()) {
                        $assetsPath = FCPATH . 'assets/announcements/';
                        if (!is_dir($assetsPath)) {
                            mkdir($assetsPath, 0755, true);
                        }
                        
                        $extension = $image->getExtension();
                        $newName = $announcementId . '_' . uniqid() . '.' . $extension;
                        
                        $image->move($assetsPath, $newName, true);
                        
                        $this->db->table('announcement_images')->insert([
                            'announcement_id' => $announcementId,
                            'image_path' => $newName,
                            'display_order' => $order++
                        ]);
                    }
                }
            }
        }
        return redirect()->to('/');
    }

    public function createUser()
    {
        if (session()->get('role') === 'admin') {
            $role = $this->request->getPost('role');
            if (!in_array($role, ['admin', 'student'])) {
                $role = 'student';
            }
            
            $this->db->table('users')->insert([
                'username' => $this->request->getPost('username'),
                'profile_name' => $this->request->getPost('profile_name'),
                'password' => $this->request->getPost('password'),
                'role' => $role
            ]);
        }
        return redirect()->to('/');
    }

    public function viewAnnouncement($id)
    {
        if (!session()->get('user_id')) {
            return redirect()->to('/');
        }

        $data['view_mode'] = 'modal';
        
        $data['announcements'] = $this->db->query(
            "SELECT a.*, u.username, u.profile_name FROM announcements a 
             JOIN users u ON a.user_id = u.id 
             ORDER BY a.created_at DESC"
        )->getResult();

        foreach ($data['announcements'] as $announcement) {
            $announcement->images = $this->getAnnouncementImages($announcement->id);
            $announcement->reaction_count = $this->getReactionCount($announcement->id);
            $announcement->user_reacted = $this->hasUserReacted($announcement->id, session()->get('user_id'));
        }

        $data['announcement'] = $this->db->query(
            "SELECT a.*, u.username, u.profile_name FROM announcements a 
             JOIN users u ON a.user_id = u.id 
             WHERE a.id = ?", [$id]
        )->getRow();

        if ($data['announcement']) {
            $data['announcement']->images = $this->getAnnouncementImages($data['announcement']->id);
            $data['announcement']->reaction_count = $this->getReactionCount($data['announcement']->id);
            $data['announcement']->user_reacted = $this->hasUserReacted($data['announcement']->id, session()->get('user_id'));
        }

        $data['comments'] = $this->db->query(
            "SELECT c.*, u.username, u.profile_name FROM comments c 
             JOIN users u ON c.user_id = u.id 
             WHERE c.announcement_id = ? 
             ORDER BY c.created_at ASC", [$id]
        )->getResult();

        if (session()->get('role') === 'admin') {
            $data['users'] = $this->db->table('users')->orderBy('created_at', 'DESC')->get()->getResult();
        }

        return view('dashboard', $data);
    }

    public function addComment()
    {
        if (session()->get('user_id')) {
            $announcementId = $this->request->getPost('announcement_id');
            $parentId = $this->request->getPost('parent_id') ?: null;

            $this->db->table('comments')->insert([
                'announcement_id' => $announcementId,
                'user_id' => session()->get('user_id'),
                'parent_id' => $parentId,
                'comment' => $this->request->getPost('comment')
            ]);

            return redirect()->to('/view/' . $announcementId);
        }
        return redirect()->to('/');
    }

    public function toggleReaction()
    {
        if (session()->get('user_id')) {
            $announcementId = $this->request->getPost('announcement_id');
            $userId = session()->get('user_id');

            $existing = $this->db->table('reactions')
                ->where('announcement_id', $announcementId)
                ->where('user_id', $userId)
                ->get()
                ->getRow();

            if ($existing) {
                $this->db->table('reactions')
                    ->where('announcement_id', $announcementId)
                    ->where('user_id', $userId)
                    ->delete();
            } else {
                $this->db->table('reactions')->insert([
                    'announcement_id' => $announcementId,
                    'user_id' => $userId,
                    'reaction_type' => 'like'
                ]);
            }

            return redirect()->to('/view/' . $announcementId);
        }
        return redirect()->to('/');
    }

    public function uploadProfile()
    {
        if (session()->get('user_id')) {
            $userId = session()->get('user_id');
            $image = $this->request->getFile('profile_image');
            
            if ($image && $image->isValid() && !$image->hasMoved()) {
                $profilePath = FCPATH . 'assets/profiles/';
                if (!is_dir($profilePath)) {
                    mkdir($profilePath, 0755, true);
                }
                
                $extensions = ['jpg', 'jpeg', 'png'];
                foreach ($extensions as $ext) {
                    $oldFile = $profilePath . $userId . '.' . $ext;
                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }
                
                $extension = $image->getExtension();
                $newName = $userId . '.' . $extension;
                
                $image->move($profilePath, $newName, true);
            }
        }
        return redirect()->to('/');
    }

    private function getAnnouncementImages($announcementId)
    {
        $images = $this->db->table('announcement_images')
            ->where('announcement_id', $announcementId)
            ->orderBy('display_order', 'ASC')
            ->get()
            ->getResult();
        
        $imageUrls = [];
        foreach ($images as $img) {
            $imageUrls[] = base_url('assets/announcements/' . $img->image_path);
        }
        
        return $imageUrls;
    }

    private function getReactionCount($announcementId)
    {
        return $this->db->table('reactions')
            ->where('announcement_id', $announcementId)
            ->countAllResults();
    }

    private function hasUserReacted($announcementId, $userId)
    {
        $result = $this->db->table('reactions')
            ->where('announcement_id', $announcementId)
            ->where('user_id', $userId)
            ->get()
            ->getRow();
        
        return $result !== null;
    }
}