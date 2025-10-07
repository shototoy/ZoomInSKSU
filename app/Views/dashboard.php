<!DOCTYPE html>
<html>
<head>
    <title>News Feed - Announcement System</title>
    <link rel="stylesheet" href="/css/shared.css">
    <style>
        .status-update-box {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            margin-top: 5vh;
        }
        .status-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 15px;
        }
        .status-header img, .status-header .avatar-placeholder {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        .avatar-placeholder {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 18px;
        }
        .status-input {
            width: 100%;
            padding: 12px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            min-height: 60px;
            resize: vertical;
            font-family: inherit;
        }
        .status-input:focus {
            outline: none;
            border-color: #667eea;
        }
        .post-options {
            display: flex;
            gap: 10px;
            margin: 15px 0;
            align-items: center;
        }
        .post-type-toggle {
            display: flex;
            gap: 5px;
            background: #f5f5f5;
            padding: 4px;
            border-radius: 8px;
        }
        .post-type-btn {
            padding: 8px 16px;
            border: none;
            background: transparent;
            cursor: pointer;
            border-radius: 6px;
            font-size: 13px;
            transition: all 0.2s;
            color: black;
        }
        .post-type-btn.active {
            background: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .image-upload-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: #f5f5f5;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
            transition: all 0.2s;
        }
        .image-upload-btn:hover {
            background: #e8e8e8;
        }
        .image-preview-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 10px;
            margin: 15px 0;
        }
        .image-preview {
            position: relative;
            aspect-ratio: 1;
            border-radius: 8px;
            overflow: hidden;
            background: #f5f5f5;
        }
        .image-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .remove-image {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(0,0,0,0.6);
            color: white;
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .post-button {
            padding: 10px 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.2s;
        }
        .post-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        .post-button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }
        .carousel-container {
    position: relative;
    width: 100%;
    overflow: hidden;
    background: #000;
    border-radius: 8px;
    margin: 15px 0;
}
.carousel-images {
    display: flex;
    transition: transform 0.3s ease;
}

.card .carousel-images img {
    width: 100%;
    height: 400px;
    flex-shrink: 0;
    object-fit: cover;
}

.modal .carousel-container {
    max-height: 70vh;
    background: #000;
}

.modal .carousel-images img {
    width: 100%;
    height: 70vh;
    max-height: 800px;
    flex-shrink: 0;
    object-fit: cover;
}

.carousel-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(0,0,0,0.5);
    color: white;
    border: none;
    padding: 15px 10px;
    cursor: pointer;
    font-size: 18px;
    z-index: 10;
}
.carousel-btn:hover { background: rgba(0,0,0,0.8); }
.carousel-btn.prev { left: 10px; }
.carousel-btn.next { right: 10px; }
        .carousel-indicators {
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 8px;
            z-index: 10;
        }
        .carousel-indicator {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: rgba(255,255,255,0.5);
            cursor: pointer;
        }
        .carousel-indicator.active { background: white; }
        .post-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .post-badge.announcement {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .post-badge.status {
            background: #f0f0f0;
            color: #666;
        }
        .post-badge.profile-update {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }
        .interaction-bar {
            display: flex;
            gap: 20px;
            padding: 10px 0;
            border-top: 1px solid #e0e0e0;
            margin-top: 10px;
        }
        .interaction-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 14px;
            color: #666;
            padding: 8px 12px;
            border-radius: 6px;
            transition: all 0.2s;
        }
        .interaction-btn:hover {
            background: #f5f5f5;
        }
        .interaction-btn svg {
            width: 18px;
            height: 18px;
            fill: currentColor;
        }
        .alert {
            padding: 12px 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-weight: 500;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <header>
        <h1>News Feed</h1>
        <div class="user-info">
            <span><?= esc(session()->get('profile_name')) ?> (<?= esc(session()->get('role')) ?>)</span>
            <a href="/logout">Logout</a>
        </div>
    </header>
    
    <div class="body-container">
        <aside class="sidebar">
            <div class="profile-card">
                <?php
                $userId = session()->get('user_id');
                $profileImagePath = FCPATH . 'assets/profiles/' . $userId;
                $profileImageUrl = null;
                $extensions = ['jpg', 'jpeg', 'png'];
                foreach ($extensions as $ext) {
                    if (file_exists($profileImagePath . '.' . $ext)) {
                        $profileImageUrl = base_url('assets/profiles/' . $userId . '.' . $ext);
                        break;
                    }
                }
                ?>
                <form method="post" action="/profile/upload" enctype="multipart/form-data" id="profileForm">
                    <label for="profileInput" style="cursor: pointer;">
                        <?php if ($profileImageUrl): ?>
                            <img src="<?= $profileImageUrl ?>" alt="Profile" class="profile-image">
                        <?php else: ?>
                            <div class="profile-placeholder">
                                <span>+</span>
                            </div>
                        <?php endif; ?>
                    </label>
                    <input type="file" name="profile_image" id="profileInput" accept="image/png,image/jpeg,image/jpg" style="display: none;" onchange="this.form.submit()">
                </form>
                <h3><?= esc(session()->get('profile_name')) ?></h3>
                <p class="user-role"><?= esc(session()->get('role')) ?></p>
                <small>@<?= esc(session()->get('username')) ?></small>
            </div>
        </aside>
        
        <main>
            <div class="container">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success">
                        <?= session()->getFlashdata('success') ?>
                    </div>
                <?php endif; ?>
                
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-error">
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>

                <?php if (session()->get('role') === 'admin'): ?>
                    <div class="tabs">
                        <button class="tab active" onclick="showTab('feed')">News Feed</button>
                        <button class="tab" onclick="showTab('users')">View Users</button>
                        <button class="tab" onclick="showTab('create-user')">Create User</button>
                    </div>

                    <div id="feed" class="tab-content active">
                        <!-- Post Creation Form -->
                        <div class="status-update-box">
                            <form method="post" action="/post/create" enctype="multipart/form-data" id="postForm">
                                <div class="status-header">
                                    <?php if ($profileImageUrl): ?>
                                        <img src="<?= $profileImageUrl ?>" alt="<?= esc(session()->get('profile_name')) ?>">
                                    <?php else: ?>
                                        <div class="avatar-placeholder"><?= strtoupper(substr(session()->get('profile_name'), 0, 1)) ?></div>
                                    <?php endif; ?>
                                    <div>
                                        <strong><?= esc(session()->get('profile_name')) ?></strong>
                                    </div>
                                </div>
                                
                                <textarea name="content" class="status-input" placeholder="What's on your mind?" required></textarea>
                                
                                <div class="post-options">
                                    <div class="post-type-toggle">
                                        <button type="button" class="post-type-btn active" data-type="status">Status</button>
                                        <button type="button" class="post-type-btn" data-type="announcement">Announcement</button>
                                    </div>
                                    <label for="imageUpload" class="image-upload-btn">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                                        </svg>
                                        Photo
                                    </label>
                                    <input type="file" id="imageUpload" name="images[]" accept="image/png,image/jpeg,image/jpg" multiple style="display: none;">
                                </div>
                                
                                <div id="imagePreviewContainer" class="image-preview-container"></div>
                                
                                <input type="hidden" name="post_type" id="postTypeInput" value="status">
                                <input type="hidden" name="title" id="titleInput" value="">
                                
                                <button type="submit" class="post-button">Post</button>
                            </form>
                        </div>

                        <h2>Recent Posts</h2>
                        <?php if (isset($announcements) && count($announcements) > 0): ?>
                            <?php foreach ($announcements as $a): ?>
                            <div class="card" onclick="window.location.href='/view/<?= $a->id ?>'" style="cursor: pointer;">
                                <div class="card-header">
                                    <div class="user-avatar">
                                        <?php
                                        $userImagePath = FCPATH . 'assets/profiles/' . $a->user_id;
                                        $userImageUrl = null;
                                        foreach ($extensions as $ext) {
                                            if (file_exists($userImagePath . '.' . $ext)) {
                                                $userImageUrl = base_url('assets/profiles/' . $a->user_id . '.' . $ext);
                                                break;
                                            }
                                        }
                                        ?>
                                        <?php if ($userImageUrl): ?>
                                            <img src="<?= $userImageUrl ?>" alt="<?= esc($a->profile_name) ?>">
                                        <?php else: ?>
                                            <div class="avatar-placeholder"><?= strtoupper(substr($a->profile_name, 0, 1)) ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="user-info-card">
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <strong><?= esc($a->profile_name) ?></strong>
                                            <span class="post-badge <?= $a->post_type ?>"><?= $a->post_type ?></span>
                                        </div>
                                        <small><?= $a->created_at ?></small>
                                    </div>
                                </div>
                                
                                <?php if ($a->title): ?>
                                    <h3><?= esc($a->title) ?></h3>
                                <?php endif; ?>
                                
                                <?php if (isset($a->images) && count($a->images) > 0): ?>
                                    <div class="carousel-container" onclick="event.stopPropagation()">
                                        <div class="carousel-images" data-carousel="<?= $a->id ?>">
                                            <?php foreach ($a->images as $img): ?>
                                                <img src="<?= $img ?>" alt="Post image">
                                            <?php endforeach; ?>
                                        </div>
                                        <?php if (count($a->images) > 1): ?>
                                            <button class="carousel-btn prev" onclick="moveCarousel(<?= $a->id ?>, -1)">❮</button>
                                            <button class="carousel-btn next" onclick="moveCarousel(<?= $a->id ?>, 1)">❯</button>
                                            <div class="carousel-indicators">
                                                <?php for ($i = 0; $i < count($a->images); $i++): ?>
                                                    <span class="carousel-indicator <?= $i === 0 ? 'active' : '' ?>" onclick="goToSlide(<?= $a->id ?>, <?= $i ?>)"></span>
                                                <?php endfor; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <p><?= esc($a->content) ?></p>
                                

                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No posts yet. Be the first to share something!</p>
                        <?php endif; ?>
                    </div>

                    <div id="users" class="tab-content">
                        <h2>All Users</h2>
                        <?php if (isset($users) && count($users) > 0): ?>
                            <?php foreach ($users as $u): ?>
                            <div class="user-card">
                                <div class="info">
                                    <strong><?= esc($u->profile_name) ?></strong>
                                    <small>@<?= esc($u->username) ?> • Created: <?= $u->created_at ?></small>
                                </div>
                                <span class="badge <?= $u->role === 'admin' ? 'admin' : '' ?>"><?= esc($u->role) ?></span>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No users found.</p>
                        <?php endif; ?>
                    </div>

                    <div id="create-user" class="tab-content">
                        <div class="admin-section">
                            <h3>Create New User</h3>
                            <form method="post" action="/user/create">
                                <input type="text" name="username" placeholder="Username (for login)" required>
                                <input type="text" name="profile_name" placeholder="Profile Name (display name)" required>
                                <input type="password" name="password" placeholder="Password" required>
                                <select name="role">
                                    <option value="student">Student</option>
                                    <option value="admin">Admin</option>
                                </select>
                                <button type="submit">Create User</button>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Student View -->
                    <div class="status-update-box">
                        <form method="post" action="/post/create" enctype="multipart/form-data" id="postFormStudent">
                            <div class="status-header">
                                <?php if ($profileImageUrl): ?>
                                    <img src="<?= $profileImageUrl ?>" alt="<?= esc(session()->get('profile_name')) ?>">
                                <?php else: ?>
                                    <div class="avatar-placeholder"><?= strtoupper(substr(session()->get('profile_name'), 0, 1)) ?></div>
                                <?php endif; ?>
                                <div>
                                    <strong><?= esc(session()->get('profile_name')) ?></strong>
                                </div>
                            </div>
                            
                            <textarea name="content" class="status-input" placeholder="What's on your mind?" required></textarea>
                            
                            <div class="post-options">
                                <label for="imageUploadStudent" class="image-upload-btn">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                                    </svg>
                                    Photo
                                </label>
                                <input type="file" id="imageUploadStudent" name="images[]" accept="image/png,image/jpeg,image/jpg" multiple style="display: none;">
                            </div>
                            
                            <div id="imagePreviewContainerStudent" class="image-preview-container"></div>
                            
                            <input type="hidden" name="post_type" value="status">
                            <input type="hidden" name="title" value="">
                            
                            <button type="submit" class="post-button">Post</button>
                        </form>
                    </div>

                    <h2>News Feed</h2>
                    <?php if (isset($announcements) && count($announcements) > 0): ?>
                        <?php foreach ($announcements as $a): ?>
                        <div class="card" onclick="window.location.href='/view/<?= $a->id ?>'" style="cursor: pointer;">
                            <div class="card-header">
                                <div class="user-avatar">
                                    <?php
                                    $userImagePath = FCPATH . 'assets/profiles/' . $a->user_id;
                                    $userImageUrl = null;
                                    foreach ($extensions as $ext) {
                                        if (file_exists($userImagePath . '.' . $ext)) {
                                            $userImageUrl = base_url('assets/profiles/' . $a->user_id . '.' . $ext);
                                            break;
                                        }
                                    }
                                    ?>
                                    <?php if ($userImageUrl): ?>
                                        <img src="<?= $userImageUrl ?>" alt="<?= esc($a->profile_name) ?>">
                                    <?php else: ?>
                                        <div class="avatar-placeholder"><?= strtoupper(substr($a->profile_name, 0, 1)) ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="user-info-card">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <strong><?= esc($a->profile_name) ?></strong>
                                        <span class="post-badge <?= $a->post_type ?>"><?= $a->post_type ?></span>
                                    </div>
                                    <small><?= $a->created_at ?></small>
                                </div>
                            </div>
                            
                            <?php if ($a->title): ?>
                                <h3><?= esc($a->title) ?></h3>
                            <?php endif; ?>
                            
                            <?php if (isset($a->images) && count($a->images) > 0): ?>
                                <div class="carousel-container" onclick="event.stopPropagation()">
                                    <div class="carousel-images" data-carousel="<?= $a->id ?>">
                                        <?php foreach ($a->images as $img): ?>
                                            <img src="<?= $img ?>" alt="Post image">
                                        <?php endforeach; ?>
                                    </div>
                                    <?php if (count($a->images) > 1): ?>
                                        <button class="carousel-btn prev" onclick="moveCarousel(<?= $a->id ?>, -1)">❮</button>
                                        <button class="carousel-btn next" onclick="moveCarousel(<?= $a->id ?>, 1)">❯</button>
                                        <div class="carousel-indicators">
                                            <?php for ($i = 0; $i < count($a->images); $i++): ?>
                                                <span class="carousel-indicator <?= $i === 0 ? 'active' : '' ?>" onclick="goToSlide(<?= $a->id ?>, <?= $i ?>)"></span>
                                            <?php endfor; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            
                            <p><?= esc($a->content) ?></p>
                            
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No posts available.</p>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <?php if (isset($view_mode) && $view_mode === 'modal' && isset($announcement)): ?>
            <div class="modal">
                <div class="modal-content">
                    <div class="modal-left">
                        <a href="/" class="close-btn">← Back</a>
                        <div class="card-header">
                            <div class="user-avatar">
                                <?php
                                $userImagePath = FCPATH . 'assets/profiles/' . $announcement->user_id;
                                $userImageUrl = null;
                                foreach ($extensions as $ext) {
                                    if (file_exists($userImagePath . '.' . $ext)) {
                                        $userImageUrl = base_url('assets/profiles/' . $announcement->user_id . '.' . $ext);
                                        break;
                                    }
                                }
                                ?>
                                <?php if ($userImageUrl): ?>
                                    <img src="<?= $userImageUrl ?>" alt="<?= esc($announcement->profile_name) ?>">
                                <?php else: ?>
                                    <div class="avatar-placeholder"><?= strtoupper(substr($announcement->profile_name, 0, 1)) ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="user-info-card">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <strong><?= esc($announcement->profile_name) ?></strong>
                                    <span class="post-badge <?= $announcement->post_type ?>"><?= $announcement->post_type ?></span>
                                </div>
                                <small><?= $announcement->created_at ?></small>
                            </div>
                        </div>
                        
                        <?php if ($announcement->title): ?>
                            <h2><?= esc($announcement->title) ?></h2>
                        <?php endif; ?>
                        
                        <?php if (isset($announcement->images) && count($announcement->images) > 0): ?>
                            <div class="carousel-container">
                                <div class="carousel-images" data-carousel="modal-<?= $announcement->id ?>">
                                    <?php foreach ($announcement->images as $img): ?>
                                        <img src="<?= $img ?>" alt="Post image">
                                    <?php endforeach; ?>
                                </div>
                                <?php if (count($announcement->images) > 1): ?>
                                    <button class="carousel-btn prev" onclick="moveCarousel('modal-<?= $announcement->id ?>', -1)">❮</button>
                                    <button class="carousel-btn next" onclick="moveCarousel('modal-<?= $announcement->id ?>', 1)">❯</button>
                                    <div class="carousel-indicators">
                                        <?php for ($i = 0; $i < count($announcement->images); $i++): ?>
                                            <span class="carousel-indicator <?= $i === 0 ? 'active' : '' ?>" onclick="goToSlide('modal-<?= $announcement->id ?>', <?= $i ?>)"></span>
                                        <?php endfor; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <p style="margin: 15px 0; line-height: 1.6;"><?= esc($announcement->content) ?></p>
                        
                        <div class="reaction-section" style="border-top: 1px solid #e0e0e0; padding-top: 15px; margin-top: 15px;">
                            <form method="post" action="/reaction/toggle" style="margin: 0;">
                                <input type="hidden" name="announcement_id" value="<?= $announcement->id ?>">
                                <button type="submit" class="interaction-btn <?= $announcement->user_reacted ? 'reacted' : '' ?>" style="<?= $announcement->user_reacted ? 'color: #e74c3c;' : '' ?>">
                                    <svg viewBox="0 0 24 24">
                                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                    </svg>
                                    <span><?= $announcement->reaction_count ?></span>
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="modal-right">
                        <h3>Comments</h3>
                        <form method="post" action="/comment/add" style="margin-bottom: 20px;">
                            <input type="hidden" name="announcement_id" value="<?= $announcement->id ?>">
                            <textarea name="comment" placeholder="Add a comment..." rows="3" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px; font-size: 13px;"></textarea>
                            <button type="submit" style="margin-top: 8px;">Post Comment</button>
                        </form>
                        
                        <?php
                        $commentTree = [];
                        if (isset($comments)) {
                            foreach ($comments as $c) {
                                if ($c->parent_id === null) {
                                    $commentTree[$c->id] = ['comment' => $c, 'replies' => []];
                                }
                            }
                            foreach ($comments as $c) {
                                if ($c->parent_id !== null && isset($commentTree[$c->parent_id])) {
                                    $commentTree[$c->parent_id]['replies'][] = $c;
                                }
                            }
                        }
                        ?>
                        
                        <?php if (count($commentTree) > 0): ?>
                            <?php foreach ($commentTree as $item): ?>
                            <div class="comment" style="margin: 8px 0; padding: 10px; background: #f9f9f9; border-radius: 8px;">
                                <strong style="font-size: 14px; display: block; margin-bottom: 4px;"><?= esc($item['comment']->profile_name) ?></strong>
                                <p style="margin: 4px 0; font-size: 13px; line-height: 1.4;"><?= esc($item['comment']->comment) ?></p>
                                <small style="color: #999; font-size: 11px;"><?= $item['comment']->created_at ?></small>
                                
                                <form method="post" action="/comment/add" style="margin-top: 8px;">
                                    <input type="hidden" name="announcement_id" value="<?= $announcement->id ?>">
                                    <input type="hidden" name="parent_id" value="<?= $item['comment']->id ?>">
                                    <textarea name="comment" placeholder="Reply..." rows="2" required style="padding: 6px; font-size: 12px; min-height: 50px; width: 100%; border: 1px solid #ddd; border-radius: 4px;"></textarea>
                                    <button type="submit" style="padding: 6px 12px; font-size: 12px;">Reply</button>
                                </form>
                                
                                <?php foreach ($item['replies'] as $reply): ?>
                                <div class="reply" style="margin: 8px 0 0 20px; padding: 8px; background: #fff; border-left: 2px solid #ddd; border-radius: 4px;">
                                    <strong style="font-size: 13px; display: block; margin-bottom: 3px;"><?= esc($reply->profile_name) ?></strong>
                                    <p style="margin: 3px 0; font-size: 12px;"><?= esc($reply->comment) ?></p>
                                    <small style="color: #999; font-size: 11px;"><?= $reply->created_at ?></small>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p style="color: #999; text-align: center; margin-top: 30px;">No comments yet. Be the first to comment!</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </main>
    </div>
    
    <footer>
        <p>&copy; 2025 News Feed System</p>
    </footer>

    <script>
        const carouselPositions = {};
        function initCarousel(id) {
            if (!carouselPositions[id]) {
                carouselPositions[id] = 0;
            }
        }

        function moveCarousel(id, direction) {
            event.stopPropagation();
            initCarousel(id);
            
            const container = document.querySelector(`[data-carousel="${id}"]`);
            const images = container.querySelectorAll('img');
            const totalImages = images.length;
            
            carouselPositions[id] += direction;
            
            if (carouselPositions[id] < 0) {
                carouselPositions[id] = totalImages - 1;
            } else if (carouselPositions[id] >= totalImages) {
                carouselPositions[id] = 0;
            }
            
            container.style.transform = `translateX(-${carouselPositions[id] * 100}%)`;
            updateIndicators(id);
        }

        function goToSlide(id, index) {
            event.stopPropagation();
            initCarousel(id);
            
            const container = document.querySelector(`[data-carousel="${id}"]`);
            carouselPositions[id] = index;
            container.style.transform = `translateX(-${index * 100}%)`;
            updateIndicators(id);
        }

        function updateIndicators(id) {
            const container = document.querySelector(`[data-carousel="${id}"]`);
            const parent = container.closest('.carousel-container');
            const indicators = parent.querySelectorAll('.carousel-indicator');
            
            indicators.forEach((indicator, index) => {
                if (index === carouselPositions[id]) {
                    indicator.classList.add('active');
                } else {
                    indicator.classList.remove('active');
                }
            });
        }
        function showTab(tabName) {
            const tabs = document.querySelectorAll('.tab');
            const contents = document.querySelectorAll('.tab-content');
            
            tabs.forEach(t => t.classList.remove('active'));
            contents.forEach(c => c.classList.remove('active'));
            
            event.target.classList.add('active');
            document.getElementById(tabName).classList.add('active');
        }

        document.querySelectorAll('.post-type-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.post-type-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                const postType = this.getAttribute('data-type');
                document.getElementById('postTypeInput').value = postType;
                
                if (postType === 'announcement') {
                    const title = prompt('Enter announcement title:');
                    if (title) {
                        document.getElementById('titleInput').value = title;
                    } else {
                        document.querySelector('.post-type-btn[data-type="status"]').classList.add('active');
                        this.classList.remove('active');
                        document.getElementById('postTypeInput').value = 'status';
                    }
                } else {
                    document.getElementById('titleInput').value = '';
                }
            });
        });
        document.getElementById('imageUpload')?.addEventListener('change', function(e) {
            previewImages(e, 'imagePreviewContainer');
        });
        document.getElementById('imageUploadStudent')?.addEventListener('change', function(e) {
            previewImages(e, 'imagePreviewContainerStudent');
        });

        function previewImages(event, containerId) {
            const files = Array.from(event.target.files);
            const container = document.getElementById(containerId);
            container.innerHTML = '';
            
            files.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.createElement('div');
                    preview.className = 'image-preview';
                    preview.innerHTML = `
                        <img src="${e.target.result}" alt="Preview">
                        <button type="button" class="remove-image" onclick="removeImage(this, '${containerId}')">×</button>
                    `;
                    container.appendChild(preview);
                };
                reader.readAsDataURL(file);
            });
        }

        function removeImage(button, containerId) {
            button.closest('.image-preview').remove();
        }
    </script>
</body>
</html>