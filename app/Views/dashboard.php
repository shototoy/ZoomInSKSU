<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Announcement System</title>
    <link rel="stylesheet" href="/css/shared.css">
</head>
<body>
    <header>
        <h1>Announcement System</h1>
        <div class="user-info">
            <span><?= esc(session()->get('username')) ?> (<?= esc(session()->get('role')) ?>)</span>
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
                <h3><?= esc(session()->get('username')) ?></h3>
                <p class="user-role"><?= esc(session()->get('role')) ?></p>
                <small>ID: <?= session()->get('user_id') ?></small>
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
                                            <img src="<?= $userImageUrl ?>" alt="<?= esc($a->username) ?>">
                                        <?php else: ?>
                                            <div class="avatar-placeholder"><?= strtoupper(substr($a->username, 0, 1)) ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="user-info-card">
                                        <strong><?= esc($a->username) ?></strong>
                                        <small><?= $a->created_at ?></small>
                                    </div>
                                </div>
                                <h3><?= esc($a->title) ?></h3>
                                <?php if (isset($a->image_url) && $a->image_url): ?>
                                    <img src="<?= $a->image_url ?>" alt="<?= esc($a->title) ?>" class="announcement-image">
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
                                    <strong><?= esc($u->username) ?></strong>
                                    <small>Created: <?= $u->created_at ?></small>
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
                                <input type="file" name="image" accept="image/png,image/jpeg,image/jpg" required>
                                <button type="submit">Create Announcement</button>
                            </form>
                        </div>
                    </div>

                    <div id="create-user" class="tab-content">
                        <div class="admin-section">
                            <h3>Create New User</h3>
                            <form method="post" action="/user/create">
                                <input type="text" name="username" placeholder="Username" required>
                                <input type="password" name="password" placeholder="Password" required>
                                <select name="role">
                                    <option value="RESIDENT">Resident</option>
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
                                        <img src="<?= $userImageUrl ?>" alt="<?= esc($a->username) ?>">
                                    <?php else: ?>
                                        <div class="avatar-placeholder"><?= strtoupper(substr($a->username, 0, 1)) ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="user-info-card">
                                    <strong><?= esc($a->username) ?></strong>
                                    <small><?= $a->created_at ?></small>
                                </div>
                            </div>
                            <h3><?= esc($a->title) ?></h3>
                            <?php if (isset($a->image_url) && $a->image_url): ?>
                                <img src="<?= $a->image_url ?>" alt="<?= esc($a->title) ?>" class="announcement-image">
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
                                    <img src="<?= $userImageUrl ?>" alt="<?= esc($announcement->username) ?>">
                                <?php else: ?>
                                    <div class="avatar-placeholder"><?= strtoupper(substr($announcement->username, 0, 1)) ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="user-info-card">
                                <strong><?= esc($announcement->username) ?></strong>
                                <small><?= $announcement->created_at ?></small>
                            </div>
                        </div>
                        <h2><?= esc($announcement->title) ?></h2>
                        <?php if (isset($announcement->image_url) && $announcement->image_url): ?>
                            <img src="<?= $announcement->image_url ?>" alt="<?= esc($announcement->title) ?>" class="modal-image">
                        <?php endif; ?>
                        <p style="margin: 15px 0; line-height: 1.6;"><?= esc($announcement->content) ?></p>
                    </div>
                    <div class="modal-right">
                        <h3>Comments</h3>
                        <form method="post" action="/comment/add" style="margin: 20px 0;">
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
                                <strong><?= esc($item['comment']->username) ?></strong>
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
                                    <strong><?= esc($reply->username) ?></strong>
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