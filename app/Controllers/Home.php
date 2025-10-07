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
            "SELECT a.*, u.username FROM announcements a 
             JOIN users u ON a.user_id = u.id 
             ORDER BY a.created_at DESC"
        )->getResult();

        foreach ($data['announcements'] as $announcement) {
            $announcement->image_url = $this->getAnnouncementImage($announcement->id);
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
        
        if ($user && in_array($user->role, ['admin', 'student', 'student'])) {
            session()->set([
                'user_id' => $user->id,
                'username' => $user->username,
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
            
            $image = $this->request->getFile('image');
            if ($image && $image->isValid() && !$image->hasMoved()) {
                $assetsPath = FCPATH . 'assets/';
                if (!is_dir($assetsPath)) {
                    mkdir($assetsPath, 0755, true);
                }
                
                $extension = $image->getExtension();
                $newName = $announcementId . '.' . $extension;
                
                $image->move($assetsPath, $newName, true);
            }
        }
        return redirect()->to('/');
    }

    public function createUser()
    {
        if (session()->get('role') === 'admin') {
            $role = $this->request->getPost('role');
            if (!in_array($role, ['admin', 'student', 'student'])) {
                $role = 'student';
            }
            
            $this->db->table('users')->insert([
                'username' => $this->request->getPost('username'),
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
            "SELECT a.*, u.username FROM announcements a 
             JOIN users u ON a.user_id = u.id 
             ORDER BY a.created_at DESC"
        )->getResult();

        foreach ($data['announcements'] as $announcement) {
            $announcement->image_url = $this->getAnnouncementImage($announcement->id);
        }

        $data['announcement'] = $this->db->query(
            "SELECT a.*, u.username FROM announcements a 
             JOIN users u ON a.user_id = u.id 
             WHERE a.id = ?", [$id]
        )->getRow();

        if ($data['announcement']) {
            $data['announcement']->image_url = $this->getAnnouncementImage($data['announcement']->id);
        }

        $data['comments'] = $this->db->query(
            "SELECT c.*, u.username FROM comments c 
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

    private function getAnnouncementImage($announcementId)
    {
        $assetsPath = FCPATH . 'assets/';
        $extensions = ['jpg', 'jpeg', 'png'];
        
        foreach ($extensions as $ext) {
            $filePath = $assetsPath . $announcementId . '.' . $ext;
            if (file_exists($filePath)) {
                return base_url('assets/' . $announcementId . '.' . $ext);
            }
        }
        
        return null;
    }
}