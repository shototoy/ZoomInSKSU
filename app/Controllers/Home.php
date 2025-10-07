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
            "SELECT a.*, u.username, u.profile_name 
             FROM announcements a 
             JOIN users u ON a.user_id = u.id 
             ORDER BY a.created_at DESC"
        )->getResult();
        foreach ($data['announcements'] as $announcement) {
            $announcement->images = $this->getAnnouncementImages($announcement->id);
            $announcement->reaction_count = $this->getReactionCount($announcement->id);
            $announcement->user_reacted = $this->hasUserReacted($announcement->id, session()->get('user_id'));
            $announcement->comment_count = $this->getCommentCount($announcement->id);
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
        
        if ($user) {
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

public function createPost()
{
    if (!session()->get('user_id')) {
        return redirect()->to('/');
    }

    $content = $this->request->getPost('content');
    $postType = $this->request->getPost('post_type') ?: 'status';
    $title = $this->request->getPost('title');
    
    if (empty($content)) {
        return redirect()->to('/')->with('error', 'Content cannot be empty');
    }

    try {
        $data = [
            'user_id' => session()->get('user_id'),
            'title' => $title,
            'content' => $content,
            'post_type' => $postType
        ];
        
        $this->db->table('announcements')->insert($data);
        $postId = $this->db->insertID();
        $files = $this->request->getFiles();  
        if (isset($files['images'])) {
            $assetsPath = FCPATH . 'assets/announcements/';
            if (!is_dir($assetsPath)) {
                mkdir($assetsPath, 0755, true);
            }
            
            $order = 0;
            foreach ($files['images'] as $image) {
                if ($image->isValid() && !$image->hasMoved()) {
                    $extension = $image->getExtension();
                    $newName = $postId . '_' . time() . '_' . $order . '.' . $extension;
                    $image->move($assetsPath, $newName);
                    $this->db->table('announcement_images')->insert([
                        'announcement_id' => $postId,
                        'image_path' => $newName,
                        'display_order' => $order
                    ]);
                    
                    $order++;
                }
            }
        }
        
        return redirect()->to('/')->with('success', 'Post created successfully!');
        
    } catch (\Exception $e) {
        log_message('error', 'Error creating post: ' . $e->getMessage());
        return redirect()->to('/')->with('error', 'Failed to create post');
    }
}

public function uploadProfile()
{
    if (!session()->get('user_id')) {
        return redirect()->to('/');
    }

    $userId = session()->get('user_id');
    $image = $this->request->getFile('profile_image');
    
    if (!$image || !$image->isValid()) {
        return redirect()->to('/')->with('error', 'Invalid image file');
    }

    try {
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
        $announcementData = [
            'user_id' => $userId,
            'content' => 'updated their profile picture',
            'post_type' => 'profile_update'
        ];
        
        $this->db->table('announcements')->insert($announcementData);
        $postId = $this->db->insertID();
        
        if (!$postId) {
            log_message('error', 'Failed to create profile update announcement');
            return redirect()->to('/')->with('error', 'Profile picture uploaded but announcement failed');
        }
        $assetsPath = FCPATH . 'assets/announcements/';
        if (!is_dir($assetsPath)) {
            mkdir($assetsPath, 0755, true);
        }
        
        $announcementImageName = $postId . '_profile.' . $extension;
        
        if (!copy($profilePath . $newName, $assetsPath . $announcementImageName)) {
            log_message('error', 'Failed to copy profile image to announcements folder');
        }
        $this->db->table('announcement_images')->insert([
            'announcement_id' => $postId,
            'image_path' => $announcementImageName,
            'display_order' => 0
        ]);
        
        return redirect()->to('/')->with('success', 'Profile picture updated!');
        
    } catch (\Exception $e) {
        log_message('error', 'Error uploading profile: ' . $e->getMessage());
        log_message('error', 'Stack trace: ' . $e->getTraceAsString());
        return redirect()->to('/')->with('error', 'Failed to update profile picture: ' . $e->getMessage());
    }
}
    public function createUser()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/');
        }

        $role = $this->request->getPost('role');
        if (!in_array($role, ['admin', 'student'])) {
            $role = 'student';
        }
        
        try {
            $this->db->table('users')->insert([
                'username' => $this->request->getPost('username'),
                'profile_name' => $this->request->getPost('profile_name'),
                'password' => $this->request->getPost('password'),
                'role' => $role
            ]);
            
            return redirect()->to('/')->with('success', 'User created successfully!');
        } catch (\Exception $e) {
            return redirect()->to('/')->with('error', 'Failed to create user');
        }
    }

    public function viewAnnouncement($id)
    {
        if (!session()->get('user_id')) {
            return redirect()->to('/');
        }
        $data['view_mode'] = 'modal';
        $data['announcements'] = $this->db->query(
            "SELECT a.*, u.username, u.profile_name 
             FROM announcements a 
             JOIN users u ON a.user_id = u.id 
             ORDER BY a.created_at DESC"
        )->getResult();

        foreach ($data['announcements'] as $announcement) {
            $announcement->images = $this->getAnnouncementImages($announcement->id);
            $announcement->reaction_count = $this->getReactionCount($announcement->id);
            $announcement->user_reacted = $this->hasUserReacted($announcement->id, session()->get('user_id'));
            $announcement->comment_count = $this->getCommentCount($announcement->id);
        }

        $data['announcement'] = $this->db->query(
            "SELECT a.*, u.username, u.profile_name 
             FROM announcements a 
             JOIN users u ON a.user_id = u.id 
             WHERE a.id = ?", [$id]
        )->getRow();

        if ($data['announcement']) {
            $data['announcement']->images = $this->getAnnouncementImages($data['announcement']->id);
            $data['announcement']->reaction_count = $this->getReactionCount($data['announcement']->id);
            $data['announcement']->user_reacted = $this->hasUserReacted($data['announcement']->id, session()->get('user_id'));
            $data['announcement']->comment_count = $this->getCommentCount($data['announcement']->id);
        }
        $data['comments'] = $this->db->query(
            "SELECT c.*, u.username, u.profile_name 
             FROM comments c 
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
        if (!session()->get('user_id')) {
            return redirect()->to('/');
        }

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

    public function toggleReaction()
    {
        if (!session()->get('user_id')) {
            return redirect()->to('/');
        }

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

    private function getCommentCount($announcementId)
    {
        return $this->db->table('comments')
            ->where('announcement_id', $announcementId)
            ->countAllResults();
    }
}