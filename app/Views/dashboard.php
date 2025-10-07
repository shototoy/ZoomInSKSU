<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Announcement System</title>
    <link rel="stylesheet" href="/css/shared.css">
    <style>
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
        .carousel-images img {
            width: 100%;
            flex-shrink: 0;
            object-fit: contain;
            max-height: 500px;
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
        .modal-carousel-container {
            position: relative;
            width: 100%;
            background: #000;
            border-radius: 8px;
        }
        .modal-carousel-container img {
            width: 100%;
            max-height: 600px;
            object-fit: contain;
        }
        .reaction-section {
            display: flex;
            align-items: center;
            gap: 15px;
            margin: 15px 0;
            padding: 10px 0;
            border-top: 1px solid #e0e0e0;
            border-bottom: 1px solid #e0e0e0;
        }
        .reaction-btn {
            display: flex;
            align-items: center;
            gap: 5px;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 14px;
            color: #666;
            padding: 5px 10px;
            border-radius: 4px;
            transition: all 0.2s;
        }
        .reaction-btn:hover { background: #f0f0f0; }
        .reaction-btn.reacted { color: #e74c3c; }
        .reaction-btn svg {
            width: 20px;
            height: 20px;
            fill: currentColor;
        }
        .comment { margin: 8px 0; padding: 10px; background: #f9f9f9; border-radius: 8px; }
        .comment strong { font-size: 14px; display: block; margin-bottom: 4px; }
        .comment p { margin: 4px 0; font-size: 13px; line-height: 1.4; }
        .comment small { color: #999; font-size: 11px; }
        .comment form { margin-top: 8px; }
        .comment textarea { padding: 6px; font-size: 12px; min-height: 50px; }
        .comment button { padding: 6px 12px; font-size: 12px; }
        .reply { margin: 8px 0 0 20px; padding: 8px; background: #fff; border-left: 2px solid #ddd; border-radius: 4px; }
        .reply strong { font-size: 13px; display: block; margin-bottom: 3px; }
        .reply p { margin: 3px 0; font-size: 12px; }
        .reply small { color: #999; font-size: 11px; }
        .modal-right h3 { margin-bottom: 15px; font-size: 18px; }
        .modal-right form { margin-bottom: 20px; }
        .modal-right textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px; font-size: 13px; }
        .modal-right button { margin-top: 8px; }
    </style>
</head>
<body>
    <header>
        <h1>Announcement System</h1>
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
                <?php if (session()->get('role') === 'admin'): ?>
                    <div class="tabs">
                        <button class="tab active" onclick="showTab('announcements')">View Announcements</button>
                        <button class="tab" onclick="showTab('users')">View Users</button>
                        <button class="tab" onclick="showTab('create-announcement')">Create Announcement</button>
                        <button class="tab" onclick="showTab('create-user')">Create User</button>
                    </div>

                    <div id="announcements" class="tab-content active">
                        <h2>All Announcements</h2>
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
                                        <strong><?= esc($a->profile_name) ?></strong>
                                        <small><?= $a->created_at ?></small>
                                    </div>
                                </div>
                                <h3><?= esc($a->title) ?></h3>
                                <?php if (isset($a->images) && count($a->images) > 0): ?>
                                    <div class="carousel-container" onclick="event.stopPropagation()">
                                        <div class="carousel-images" data-carousel="<?= $a->id ?>">
                                            <?php foreach ($a->images as $img): ?>
                                                <img src="<?= $img ?>" alt="<?= esc($a->title) ?>">
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
                            <p>No announcements yet.</p>
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

                    <div id="create-announcement" class="tab-content">
                        <div class="admin-section">
                            <h3>Create New Announcement</h3>
                            <form method="post" action="/announcement/create" enctype="multipart/form-data">
                                <input type="text" name="title" placeholder="Title" required>
                                <textarea name="content" placeholder="Content" rows="5" required></textarea>
                                <input type="file" name="images[]" accept="image/png,image/jpeg,image/jpg" multiple required>
                                <small style="color: #666; display: block; margin-top: 5px;">You can select multiple images</small>
                                <button type="submit">Create Announcement</button>
                            </form>
                        </div>
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
                    <h2>Announcements</h2>
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
                                    <strong><?= esc($a->profile_name) ?></strong>
                                    <small><?= $a->created_at ?></small>
                                </div>
                            </div>
                            <h3><?= esc($a->title) ?></h3>
                            <?php if (isset($a->images) && count($a->images) > 0): ?>
                                <div class="carousel-container" onclick="event.stopPropagation()">
                                    <div class="carousel-images" data-carousel="<?= $a->id ?>">
                                        <?php foreach ($a->images as $img): ?>
                                            <img src="<?= $img ?>" alt="<?= esc($a->title) ?>">
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
                        <p>No announcements available.</p>
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
                                <strong><?= esc($announcement->profile_name) ?></strong>
                                <small><?= $announcement->created_at ?></small>
                            </div>
                        </div>
                        <h2><?= esc($announcement->title) ?></h2>
                        <?php if (isset($announcement->images) && count($announcement->images) > 0): ?>
                            <div class="modal-carousel-container">
                                <div class="carousel-images" data-carousel="modal-<?= $announcement->id ?>">
                                    <?php foreach ($announcement->images as $img): ?>
                                        <img src="<?= $img ?>" alt="<?= esc($announcement->title) ?>">
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
                        
                        <div class="reaction-section">
                            <form method="post" action="/reaction/toggle" style="margin: 0;">
                                <input type="hidden" name="announcement_id" value="<?= $announcement->id ?>">
                                <button type="submit" class="reaction-btn <?= $announcement->user_reacted ? 'reacted' : '' ?>">
                                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                    </svg>
                                    <span><?= $announcement->reaction_count ?></span>
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="modal-right">
                        <h3>Comments</h3>
                        <form method="post" action="/comment/add">
                            <input type="hidden" name="announcement_id" value="<?= $announcement->id ?>">
                            <textarea name="comment" placeholder="Add a comment..." rows="3" required></textarea>
                            <button type="submit">Post Comment</button>
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
                            <div class="comment">
                                <strong><?= esc($item['comment']->profile_name) ?></strong>
                                <p><?= esc($item['comment']->comment) ?></p>
                                <small><?= $item['comment']->created_at ?></small>
                                <form method="post" action="/comment/add">
                                    <input type="hidden" name="announcement_id" value="<?= $announcement->id ?>">
                                    <input type="hidden" name="parent_id" value="<?= $item['comment']->id ?>">
                                    <textarea name="comment" placeholder="Reply..." rows="2" required></textarea>
                                    <button type="submit">Reply</button>
                                </form>
                                
                                <?php foreach ($item['replies'] as $reply): ?>
                                <div class="reply">
                                    <strong><?= esc($reply->profile_name) ?></strong>
                                    <p><?= esc($reply->comment) ?></p>
                                    <small><?= $reply->created_at ?></small>
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
        <p>&copy; 2025 Announcement System</p>
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
            const parent = container.closest('.carousel-container, .modal-carousel-container');
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
    </script>
</body>
</html>