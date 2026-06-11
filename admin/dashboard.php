<?php
/**
 * Administrator Dashboard View & Controller
 * Complete CRUD panel managing contact inquiries, navbar parent categories, and dynamic service capabilities.
 */

session_start();
require_once dirname(__DIR__) . '/api/db_connect.php';
require_once dirname(__DIR__) . '/api/upload_helper.php';

// Verify session
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$message = '';
$messageType = 'success';

try {
    $db = getDatabaseConnection();
    
    // -------------------------------------------------------------
    // CONTROLLER: Handle CRUD Actions
    // -------------------------------------------------------------
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
        $action = $_POST['action'];
        
        // Lead Action: Update Status
        if ($action === 'update_status' && isset($_POST['id']) && isset($_POST['status'])) {
            $id = (int)$_POST['id'];
            $status = filter_var($_POST['status'], FILTER_DEFAULT);
            
            $query = "UPDATE submissions SET status = :status WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->execute([':status' => $status, ':id' => $id]);
            $message = "Lead inquiry status updated successfully.";
            $messageType = "success";
        }
        
        // Lead Action: Delete Inquiry
        elseif ($action === 'delete' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            
            $query = "DELETE FROM submissions WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->execute([':id' => $id]);
            $message = "Inquiry record deleted successfully.";
            $messageType = "warning";
        }
        
        // Navbar Action: Add Menu Category
        elseif ($action === 'add_menu' && isset($_POST['name'])) {
            $name = trim(filter_var($_POST['name'], FILTER_DEFAULT));
            $sort_order = (int)$_POST['sort_order'];
            
            if (!empty($name)) {
                $query = "INSERT INTO menus (name, sort_order) VALUES (:name, :sort_order)";
                $stmt = $db->prepare($query);
                $stmt->execute([':name' => $name, ':sort_order' => $sort_order]);
                $message = "Navbar category '{$name}' created successfully.";
                $messageType = "success";
            } else {
                $message = "Category name cannot be empty.";
                $messageType = "danger";
            }
        }
        
        // Navbar Action: Edit Menu Category
        elseif ($action === 'edit_menu' && isset($_POST['id']) && isset($_POST['name'])) {
            $id = (int)$_POST['id'];
            $name = trim(filter_var($_POST['name'], FILTER_DEFAULT));
            $sort_order = (int)$_POST['sort_order'];
            
            if (!empty($name) && $id > 0) {
                $query = "UPDATE menus SET name = :name, sort_order = :sort_order WHERE id = :id";
                $stmt = $db->prepare($query);
                $stmt->execute([':name' => $name, ':sort_order' => $sort_order, ':id' => $id]);
                $message = "Navbar category '{$name}' updated successfully.";
                $messageType = "success";
            } else {
                $message = "Invalid parameters for editing category.";
                $messageType = "danger";
            }
        }
        
        // Navbar Action: Delete Menu Category
        elseif ($action === 'delete_menu' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            
            if ($id > 0) {
                // Delete associated submenus first due to SQLite foreign key constraints
                $stmt = $db->prepare("DELETE FROM submenus WHERE menu_id = :menu_id");
                $stmt->execute([':menu_id' => $id]);
                
                $query = "DELETE FROM menus WHERE id = :id";
                $stmt = $db->prepare($query);
                $stmt->execute([':id' => $id]);
                
                $message = "Navbar category and all its nested submenus were deleted successfully.";
                $messageType = "warning";
            }
        }
        
        // Service Action: Add Submenu/Service
        elseif ($action === 'add_submenu') {
            $menu_id = (int)$_POST['menu_id'];
            $name = trim(filter_var($_POST['name'], FILTER_DEFAULT));
            $service_key = trim(filter_var($_POST['service_key'], FILTER_DEFAULT));
            $tagline = trim(filter_var($_POST['tagline'], FILTER_DEFAULT));
            $icon = trim(filter_var($_POST['icon'], FILTER_DEFAULT));
            $desc1 = trim(filter_var($_POST['desc1'], FILTER_DEFAULT));
            $desc2 = trim(filter_var($_POST['desc2'], FILTER_DEFAULT));
            $features = trim(filter_var($_POST['features'], FILTER_DEFAULT));
            $banner_grad = trim(filter_var($_POST['banner_grad'], FILTER_DEFAULT));
            $sort_order = (int)$_POST['sort_order'];
            
            if (empty($service_key)) {
                $service_key = strtolower(preg_replace('/[^a-zA-Z0-9\-]/', '', str_replace(' ', '-', $name)));
            }
            
            if (!empty($name) && $menu_id > 0) {
                // Handle image upload
                $image_url = null;
                if (isset($_FILES['service_image']) && $_FILES['service_image']['error'] === UPLOAD_ERR_OK) {
                    $image_url = upload_service_image($_FILES['service_image']);
                }

                $query = "INSERT INTO submenus (menu_id, name, service_key, tagline, icon, desc1, desc2, features, banner_grad, sort_order, image_url) 
                          VALUES (:menu_id, :name, :service_key, :tagline, :icon, :desc1, :desc2, :features, :banner_grad, :sort_order, :image_url)";
                $stmt = $db->prepare($query);
                $stmt->execute([
                    ':menu_id' => $menu_id,
                    ':name' => $name,
                    ':service_key' => $service_key,
                    ':tagline' => $tagline,
                    ':icon' => $icon,
                    ':desc1' => $desc1,
                    ':desc2' => $desc2,
                    ':features' => $features,
                    ':banner_grad' => $banner_grad,
                    ':sort_order' => $sort_order,
                    ':image_url' => $image_url
                ]);
                $message = "Dynamic service / submenu '{$name}' added successfully.";
                $messageType = "success";
            } else {
                $message = "Service name and parent navbar category are required.";
                $messageType = "danger";
            }
        }
        
        // Service Action: Edit Submenu/Service
        elseif ($action === 'edit_submenu' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $menu_id = (int)$_POST['menu_id'];
            $name = trim(filter_var($_POST['name'], FILTER_DEFAULT));
            $service_key = trim(filter_var($_POST['service_key'], FILTER_DEFAULT));
            $tagline = trim(filter_var($_POST['tagline'], FILTER_DEFAULT));
            $icon = trim(filter_var($_POST['icon'], FILTER_DEFAULT));
            $desc1 = trim(filter_var($_POST['desc1'], FILTER_DEFAULT));
            $desc2 = trim(filter_var($_POST['desc2'], FILTER_DEFAULT));
            $features = trim(filter_var($_POST['features'], FILTER_DEFAULT));
            $banner_grad = trim(filter_var($_POST['banner_grad'], FILTER_DEFAULT));
            $sort_order = (int)$_POST['sort_order'];
            
            if (empty($service_key)) {
                $service_key = strtolower(preg_replace('/[^a-zA-Z0-9\-]/', '', str_replace(' ', '-', $name)));
            }
            
            if ($id > 0 && !empty($name) && $menu_id > 0) {
                // Handle image upload
                $image_url = null;
                $has_new_image = false;
                if (isset($_FILES['service_image']) && $_FILES['service_image']['error'] === UPLOAD_ERR_OK) {
                    $image_url = upload_service_image($_FILES['service_image']);
                    $has_new_image = true;
                }

                if ($has_new_image) {
                    $query = "UPDATE submenus SET 
                                menu_id = :menu_id, 
                                name = :name, 
                                service_key = :service_key, 
                                tagline = :tagline, 
                                icon = :icon, 
                                desc1 = :desc1, 
                                desc2 = :desc2, 
                                features = :features, 
                                banner_grad = :banner_grad, 
                                sort_order = :sort_order,
                                image_url = :image_url
                              WHERE id = :id";
                    $params = [
                        ':menu_id' => $menu_id,
                        ':name' => $name,
                        ':service_key' => $service_key,
                        ':tagline' => $tagline,
                        ':icon' => $icon,
                        ':desc1' => $desc1,
                        ':desc2' => $desc2,
                        ':features' => $features,
                        ':banner_grad' => $banner_grad,
                        ':sort_order' => $sort_order,
                        ':image_url' => $image_url,
                        ':id' => $id
                    ];
                } else {
                    $query = "UPDATE submenus SET 
                                menu_id = :menu_id, 
                                name = :name, 
                                service_key = :service_key, 
                                tagline = :tagline, 
                                icon = :icon, 
                                desc1 = :desc1, 
                                desc2 = :desc2, 
                                features = :features, 
                                banner_grad = :banner_grad, 
                                sort_order = :sort_order 
                              WHERE id = :id";
                    $params = [
                        ':menu_id' => $menu_id,
                        ':name' => $name,
                        ':service_key' => $service_key,
                        ':tagline' => $tagline,
                        ':icon' => $icon,
                        ':desc1' => $desc1,
                        ':desc2' => $desc2,
                        ':features' => $features,
                        ':banner_grad' => $banner_grad,
                        ':sort_order' => $sort_order,
                        ':id' => $id
                    ];
                }
                $stmt = $db->prepare($query);
                $stmt->execute($params);
                $message = "Dynamic service '{$name}' modified successfully.";
                $messageType = "success";
            } else {
                $message = "Invalid fields or service not found.";
                $messageType = "danger";
            }
        }
        
        // Service Action: Delete Submenu/Service
        elseif ($action === 'delete_submenu' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            
            if ($id > 0) {
                $query = "DELETE FROM submenus WHERE id = :id";
                $stmt = $db->prepare($query);
                $stmt->execute([':id' => $id]);
                $message = "Service template capability removed successfully.";
                $messageType = "warning";
            }
        }
        
        // Settings Action: Save Contact details and Popup configs
        elseif ($action === 'save_settings') {
            $contact_phone = trim(filter_var($_POST['contact_phone'] ?? '', FILTER_DEFAULT));
            $contact_email = trim(filter_var($_POST['contact_email'] ?? '', FILTER_DEFAULT));
            
            if (!empty($contact_phone) && !empty($contact_email)) {
                $stmt = $db->prepare("UPDATE settings SET value = :value WHERE key = :key");
                
                $stmt->execute([':value' => $contact_phone, ':key' => 'contact_phone']);
                $stmt->execute([':value' => $contact_email, ':key' => 'contact_email']);
                
                // Popup keys
                $popup_status = $_POST['popup_status'] ?? 'hide';
                $popup_type = $_POST['popup_type'] ?? 'both';
                $popup_title = trim(filter_var($_POST['popup_title'] ?? '', FILTER_DEFAULT));
                $popup_text = trim(filter_var($_POST['popup_text'] ?? '', FILTER_DEFAULT));
                
                $stmt->execute([':value' => $popup_status, ':key' => 'popup_status']);
                $stmt->execute([':value' => $popup_type, ':key' => 'popup_type']);
                $stmt->execute([':value' => $popup_title, ':key' => 'popup_title']);
                $stmt->execute([':value' => $popup_text, ':key' => 'popup_text']);
                
                // Handle image upload if a new file is uploaded
                if (isset($_FILES['popup_image_file']) && $_FILES['popup_image_file']['error'] === UPLOAD_ERR_OK) {
                    $uploaded_url = upload_service_image($_FILES['popup_image_file']);
                    if ($uploaded_url) {
                        $stmt->execute([':value' => $uploaded_url, ':key' => 'popup_image']);
                    }
                } elseif (isset($_POST['popup_image_clear']) && $_POST['popup_image_clear'] == '1') {
                    // Option to clear the image
                    $stmt->execute([':value' => '', ':key' => 'popup_image']);
                }
                
                // Hero Background and Opacity keys
                $hero_bg_opacity = $_POST['hero_bg_opacity'] ?? '0.20';
                $stmt->execute([':value' => $hero_bg_opacity, ':key' => 'hero_bg_opacity']);
                
                if (isset($_FILES['hero_bg_image_file']) && $_FILES['hero_bg_image_file']['error'] === UPLOAD_ERR_OK) {
                    $uploaded_hero_url = upload_service_image($_FILES['hero_bg_image_file']);
                    if ($uploaded_hero_url) {
                        $stmt->execute([':value' => $uploaded_hero_url, ':key' => 'hero_bg_image']);
                    }
                } elseif (isset($_POST['hero_bg_image_clear']) && $_POST['hero_bg_image_clear'] == '1') {
                    $stmt->execute([':value' => '', ':key' => 'hero_bg_image']);
                }

                // About Section Content keys
                $about_title = trim(filter_var($_POST['about_title'] ?? '', FILTER_DEFAULT));
                $about_subtitle = trim(filter_var($_POST['about_subtitle'] ?? '', FILTER_DEFAULT));
                $about_desc_1 = trim(filter_var($_POST['about_desc_1'] ?? '', FILTER_DEFAULT));
                $about_desc_2 = trim(filter_var($_POST['about_desc_2'] ?? '', FILTER_DEFAULT));
                
                $stmt->execute([':value' => $about_title, ':key' => 'about_title']);
                $stmt->execute([':value' => $about_subtitle, ':key' => 'about_subtitle']);
                $stmt->execute([':value' => $about_desc_1, ':key' => 'about_desc_1']);
                $stmt->execute([':value' => $about_desc_2, ':key' => 'about_desc_2']);
                
                if (isset($_FILES['about_image_file']) && $_FILES['about_image_file']['error'] === UPLOAD_ERR_OK) {
                    $uploaded_about_url = upload_service_image($_FILES['about_image_file']);
                    if ($uploaded_about_url) {
                        $stmt->execute([':value' => $uploaded_about_url, ':key' => 'about_image']);
                    }
                }
                
                $message = "Global website settings updated successfully.";
                $messageType = "success";
            } else {
                $message = "Phone and email cannot be empty.";
                $messageType = "danger";
            }
        }
        
        // Settings Action: Change Admin Credentials (Username and Password)
        elseif ($action === 'change_credentials') {
            $new_username = trim(filter_var($_POST['new_username'] ?? '', FILTER_DEFAULT));
            $current_password = $_POST['current_password'] ?? '';
            $new_password = $_POST['new_password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            
            if (!empty($new_username) && !empty($current_password)) {
                $current_username = $_SESSION['admin_username'] ?? 'admin';
                
                // Fetch current hashed password
                $stmt = $db->prepare("SELECT password FROM admins WHERE username = :username");
                $stmt->execute([':username' => $current_username]);
                $hashed = $stmt->fetchColumn();
                
                if ($hashed && password_verify($current_password, $hashed)) {
                    $username_taken = false;
                    // Check if new username is already taken by a different account
                    if (strtolower($new_username) !== strtolower($current_username)) {
                        $checkStmt = $db->prepare("SELECT COUNT(*) FROM admins WHERE LOWER(username) = LOWER(:username)");
                        $checkStmt->execute([':username' => $new_username]);
                        if ($checkStmt->fetchColumn() > 0) {
                            $username_taken = true;
                        }
                    }
                    
                    if ($username_taken) {
                        $message = "The username '{$new_username}' is already taken.";
                        $messageType = "danger";
                    } else {
                        if (!empty($new_password)) {
                            if ($new_password === $confirm_password) {
                                $new_hashed = password_hash($new_password, PASSWORD_BCRYPT);
                                $updateStmt = $db->prepare("UPDATE admins SET username = :new_username, password = :password WHERE username = :current_username");
                                $updateStmt->execute([
                                    ':new_username' => $new_username,
                                    ':password' => $new_hashed,
                                    ':current_username' => $current_username
                                ]);
                                $message = "Admin credentials (username and password) updated successfully.";
                                $messageType = "success";
                                $_SESSION['admin_username'] = $new_username;
                            } else {
                                $message = "New password and confirmation do not match.";
                                $messageType = "danger";
                            }
                        } else {
                            // Only update username
                            $updateStmt = $db->prepare("UPDATE admins SET username = :new_username WHERE username = :current_username");
                            $updateStmt->execute([
                                ':new_username' => $new_username,
                                ':current_username' => $current_username
                            ]);
                            $message = "Admin username updated successfully.";
                            $messageType = "success";
                            $_SESSION['admin_username'] = $new_username;
                        }
                    }
                } else {
                    $message = "Current password is incorrect.";
                    $messageType = "danger";
                }
            } else {
                $message = "New username and current password are required.";
                $messageType = "danger";
            }
        }
        
        // Blog Action: Add Blog Post
        elseif ($action === 'add_blog') {
            $title = trim(filter_var($_POST['title'], FILTER_DEFAULT));
            $summary = trim(filter_var($_POST['summary'], FILTER_DEFAULT));
            $content = $_POST['content']; // HTML content
            $seo_title = trim(filter_var($_POST['seo_title'], FILTER_DEFAULT));
            $meta_description = trim(filter_var($_POST['meta_description'], FILTER_DEFAULT));
            $author = trim(filter_var($_POST['author'] ?? '', FILTER_DEFAULT));
            if (empty($author)) {
                $author = 'LGS Editorial Team';
            }
            
            if (!empty($title) && !empty($content)) {
                $slug = strtolower(preg_replace('/[^a-zA-Z0-9\-]/', '', str_replace(' ', '-', $title)));
                $stmt = $db->prepare("SELECT COUNT(*) FROM blogs WHERE slug = :slug");
                $stmt->execute([':slug' => $slug]);
                if ($stmt->fetchColumn() > 0) {
                    $slug .= '-' . time();
                }
                
                // Handle image upload
                $image_url = null;
                if (isset($_FILES['blog_image']) && $_FILES['blog_image']['error'] === UPLOAD_ERR_OK) {
                    $image_url = upload_service_image($_FILES['blog_image']);
                }
                
                $query = "INSERT INTO blogs (title, slug, summary, content, seo_title, meta_description, image_url, author) 
                          VALUES (:title, :slug, :summary, :content, :seo_title, :meta_description, :image_url, :author)";
                $stmt = $db->prepare($query);
                $stmt->execute([
                    ':title' => $title,
                    ':slug' => $slug,
                    ':summary' => $summary,
                    ':content' => $content,
                    ':seo_title' => $seo_title,
                    ':meta_description' => $meta_description,
                    ':image_url' => $image_url,
                    ':author' => $author
                ]);
                $message = "Blog post '{$title}' created successfully.";
                $messageType = "success";
            } else {
                $message = "Blog title and article content are required.";
                $messageType = "danger";
            }
        }
        
        // Blog Action: Edit Blog Post
        elseif ($action === 'edit_blog' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $title = trim(filter_var($_POST['title'], FILTER_DEFAULT));
            $summary = trim(filter_var($_POST['summary'], FILTER_DEFAULT));
            $content = $_POST['content']; // HTML content
            $seo_title = trim(filter_var($_POST['seo_title'], FILTER_DEFAULT));
            $meta_description = trim(filter_var($_POST['meta_description'], FILTER_DEFAULT));
            $author = trim(filter_var($_POST['author'] ?? '', FILTER_DEFAULT));
            if (empty($author)) {
                $author = 'LGS Editorial Team';
            }
            
            if ($id > 0 && !empty($title) && !empty($content)) {
                // Handle image upload
                $image_url = null;
                $has_new_image = false;
                if (isset($_FILES['blog_image']) && $_FILES['blog_image']['error'] === UPLOAD_ERR_OK) {
                    $image_url = upload_service_image($_FILES['blog_image']);
                    $has_new_image = true;
                }
                
                if ($has_new_image) {
                    $query = "UPDATE blogs SET 
                                title = :title, 
                                summary = :summary, 
                                content = :content, 
                                seo_title = :seo_title, 
                                meta_description = :meta_description,
                                image_url = :image_url,
                                author = :author
                              WHERE id = :id";
                    $params = [
                        ':title' => $title,
                        ':summary' => $summary,
                        ':content' => $content,
                        ':seo_title' => $seo_title,
                        ':meta_description' => $meta_description,
                        ':image_url' => $image_url,
                        ':author' => $author,
                        ':id' => $id
                    ];
                } else {
                    $query = "UPDATE blogs SET 
                                title = :title, 
                                summary = :summary, 
                                content = :content, 
                                seo_title = :seo_title, 
                                meta_description = :meta_description,
                                author = :author
                              WHERE id = :id";
                    $params = [
                        ':title' => $title,
                        ':summary' => $summary,
                        ':content' => $content,
                        ':seo_title' => $seo_title,
                        ':meta_description' => $meta_description,
                        ':author' => $author,
                        ':id' => $id
                    ];
                }
                $stmt = $db->prepare($query);
                $stmt->execute($params);
                $message = "Blog post '{$title}' updated successfully.";
                $messageType = "success";
            } else {
                $message = "Blog title and article content are required.";
                $messageType = "danger";
            }
        }
        
        // Blog Action: Delete Blog Post
        elseif ($action === 'delete_blog' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            if ($id > 0) {
                $query = "DELETE FROM blogs WHERE id = :id";
                $stmt = $db->prepare($query);
                $stmt->execute([':id' => $id]);
                $message = "Blog post deleted successfully.";
                $messageType = "warning";
            }
        }

        // Testimonials Action: Add Testimonial
        elseif ($action === 'add_testimonial') {
            $client_name = trim(filter_var($_POST['client_name'] ?? '', FILTER_DEFAULT));
            $service_name = trim(filter_var($_POST['service_name'] ?? '', FILTER_DEFAULT));
            $testimonial_text = trim(filter_var($_POST['testimonial_text'] ?? '', FILTER_DEFAULT));
            $sort_order = (int)($_POST['sort_order'] ?? 0);
            
            if (!empty($client_name) && !empty($testimonial_text)) {
                // Handle image upload
                $image_url = null;
                if (isset($_FILES['testimonial_image']) && $_FILES['testimonial_image']['error'] === UPLOAD_ERR_OK) {
                    $image_url = upload_service_image($_FILES['testimonial_image']);
                }
                
                $query = "INSERT INTO testimonials (client_name, service_name, testimonial_text, image_url, sort_order) 
                          VALUES (:client_name, :service_name, :testimonial_text, :image_url, :sort_order)";
                $stmt = $db->prepare($query);
                $stmt->execute([
                    ':client_name' => $client_name,
                    ':service_name' => $service_name,
                    ':testimonial_text' => $testimonial_text,
                    ':image_url' => $image_url,
                    ':sort_order' => $sort_order
                ]);
                $message = "Testimonial from '{$client_name}' added successfully.";
                $messageType = "success";
            } else {
                $message = "Client Name and Testimonial text are required.";
                $messageType = "danger";
            }
        }
        
        // Testimonials Action: Edit Testimonial
        elseif ($action === 'edit_testimonial' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $client_name = trim(filter_var($_POST['client_name'] ?? '', FILTER_DEFAULT));
            $service_name = trim(filter_var($_POST['service_name'] ?? '', FILTER_DEFAULT));
            $testimonial_text = trim(filter_var($_POST['testimonial_text'] ?? '', FILTER_DEFAULT));
            $sort_order = (int)($_POST['sort_order'] ?? 0);
            
            if ($id > 0 && !empty($client_name) && !empty($testimonial_text)) {
                // Handle image upload
                $image_url = null;
                $has_new_image = false;
                if (isset($_FILES['testimonial_image']) && $_FILES['testimonial_image']['error'] === UPLOAD_ERR_OK) {
                    $image_url = upload_service_image($_FILES['testimonial_image']);
                    $has_new_image = true;
                }
                
                if ($has_new_image) {
                    $query = "UPDATE testimonials SET 
                                client_name = :client_name, 
                                service_name = :service_name, 
                                testimonial_text = :testimonial_text, 
                                image_url = :image_url, 
                                sort_order = :sort_order 
                              WHERE id = :id";
                    $params = [
                        ':client_name' => $client_name,
                        ':service_name' => $service_name,
                        ':testimonial_text' => $testimonial_text,
                        ':image_url' => $image_url,
                        ':sort_order' => $sort_order,
                        ':id' => $id
                    ];
                } else {
                    $query = "UPDATE testimonials SET 
                                client_name = :client_name, 
                                service_name = :service_name, 
                                testimonial_text = :testimonial_text, 
                                sort_order = :sort_order 
                              WHERE id = :id";
                    $params = [
                        ':client_name' => $client_name,
                        ':service_name' => $service_name,
                        ':testimonial_text' => $testimonial_text,
                        ':sort_order' => $sort_order,
                        ':id' => $id
                    ];
                }
                $stmt = $db->prepare($query);
                $stmt->execute($params);
                $message = "Testimonial from '{$client_name}' updated successfully.";
                $messageType = "success";
            } else {
                $message = "Client Name and Testimonial text are required.";
                $messageType = "danger";
            }
        }
        
        // Testimonials Action: Delete Testimonial
        elseif ($action === 'delete_testimonial' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            if ($id > 0) {
                $query = "DELETE FROM testimonials WHERE id = :id";
                $stmt = $db->prepare($query);
                $stmt->execute([':id' => $id]);
                $message = "Testimonial deleted successfully.";
                $messageType = "warning";
            }
        }
        
        // Industries Action: Add Industry
        elseif ($action === 'add_industry') {
            $name = trim(filter_var($_POST['name'] ?? '', FILTER_DEFAULT));
            $title = trim(filter_var($_POST['title'] ?? '', FILTER_DEFAULT));
            $description = trim(filter_var($_POST['description'] ?? '', FILTER_DEFAULT));
            $features = trim(filter_var($_POST['features'] ?? '', FILTER_DEFAULT));
            $icon = trim(filter_var($_POST['icon'] ?? 'fa-industry', FILTER_DEFAULT));
            $sort_order = (int)($_POST['sort_order'] ?? 0);
            
            if (!empty($name) && !empty($title) && !empty($description)) {
                $image_url = null;
                if (isset($_FILES['industry_image']) && $_FILES['industry_image']['error'] === UPLOAD_ERR_OK) {
                    $image_url = upload_service_image($_FILES['industry_image']);
                }

                $query = "INSERT INTO industries (name, title, description, features, icon, sort_order, image_url) 
                          VALUES (:name, :title, :description, :features, :icon, :sort_order, :image_url)";
                $stmt = $db->prepare($query);
                $stmt->execute([
                    ':name' => $name,
                    ':title' => $title,
                    ':description' => $description,
                    ':features' => $features,
                    ':icon' => $icon,
                    ':sort_order' => $sort_order,
                    ':image_url' => $image_url
                ]);
                $message = "Industry sector '{$name}' created successfully.";
                $messageType = "success";
            } else {
                $message = "Sector Name, Title, and Description are required.";
                $messageType = "danger";
            }
        }
        
        // Industries Action: Edit Industry
        elseif ($action === 'edit_industry' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $name = trim(filter_var($_POST['name'] ?? '', FILTER_DEFAULT));
            $title = trim(filter_var($_POST['title'] ?? '', FILTER_DEFAULT));
            $description = trim(filter_var($_POST['description'] ?? '', FILTER_DEFAULT));
            $features = trim(filter_var($_POST['features'] ?? '', FILTER_DEFAULT));
            $icon = trim(filter_var($_POST['icon'] ?? 'fa-industry', FILTER_DEFAULT));
            $sort_order = (int)($_POST['sort_order'] ?? 0);
            
            if ($id > 0 && !empty($name) && !empty($title) && !empty($description)) {
                $image_url = null;
                $has_new_image = false;
                if (isset($_FILES['industry_image']) && $_FILES['industry_image']['error'] === UPLOAD_ERR_OK) {
                    $image_url = upload_service_image($_FILES['industry_image']);
                    $has_new_image = true;
                }
                
                if ($has_new_image) {
                    $query = "UPDATE industries SET 
                                name = :name, 
                                title = :title, 
                                description = :description, 
                                features = :features, 
                                icon = :icon, 
                                sort_order = :sort_order,
                                image_url = :image_url
                              WHERE id = :id";
                    $params = [
                        ':name' => $name,
                        ':title' => $title,
                        ':description' => $description,
                        ':features' => $features,
                        ':icon' => $icon,
                        ':sort_order' => $sort_order,
                        ':image_url' => $image_url,
                        ':id' => $id
                    ];
                } else {
                    if (isset($_POST['industry_image_clear']) && $_POST['industry_image_clear'] == '1') {
                        $query = "UPDATE industries SET 
                                    name = :name, 
                                    title = :title, 
                                    description = :description, 
                                    features = :features, 
                                    icon = :icon, 
                                    sort_order = :sort_order,
                                    image_url = NULL
                                  WHERE id = :id";
                    } else {
                        $query = "UPDATE industries SET 
                                    name = :name, 
                                    title = :title, 
                                    description = :description, 
                                    features = :features, 
                                    icon = :icon, 
                                    sort_order = :sort_order 
                                  WHERE id = :id";
                    }
                    $params = [
                        ':name' => $name,
                        ':title' => $title,
                        ':description' => $description,
                        ':features' => $features,
                        ':icon' => $icon,
                        ':sort_order' => $sort_order,
                        ':id' => $id
                    ];
                }
                $stmt = $db->prepare($query);
                $stmt->execute($params);
                $message = "Industry sector '{$name}' updated successfully.";
                $messageType = "success";
            } else {
                $message = "Sector Name, Title, and Description are required.";
                $messageType = "danger";
            }
        }
        
        // Industries Action: Delete Industry
        elseif ($action === 'delete_industry' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            if ($id > 0) {
                $query = "DELETE FROM industries WHERE id = :id";
                $stmt = $db->prepare($query);
                $stmt->execute([':id' => $id]);
                $message = "Industry sector deleted successfully.";
                $messageType = "warning";
            }
        }
    }
    
    // -------------------------------------------------------------
    // VIEWS: Fetch Database Information
    // -------------------------------------------------------------
    
    // Fetch stats for header cards
    $totalCount = $db->query("SELECT COUNT(*) FROM submissions")->fetchColumn();
    $pendingCount = $db->query("SELECT COUNT(*) FROM submissions WHERE status = 'Pending'")->fetchColumn();
    $menuCount = $db->query("SELECT COUNT(*) FROM menus")->fetchColumn();
    $submenuCount = $db->query("SELECT COUNT(*) FROM submenus")->fetchColumn();
    $blogsCount = $db->query("SELECT COUNT(*) FROM blogs")->fetchColumn();
    
    // Fetch settings key-value pair
    $settings = $db->query("SELECT key, value FROM settings")->fetchAll(PDO::FETCH_KEY_PAIR);
    if (!isset($settings['contact_phone'])) $settings['contact_phone'] = '+91-9718117270';
    if (!isset($settings['contact_email'])) $settings['contact_email'] = 'sales@localglobal.com';
    
    // Fetch all blogs
    $blogs = $db->query("SELECT * FROM blogs ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
    
    // 1. Fetch categories (Menus)
    $menus = $db->query("SELECT * FROM menus ORDER BY sort_order ASC, id ASC")->fetchAll(PDO::FETCH_ASSOC);
    
    // 2. Fetch nested submenus / services
    $submenus = $db->query("SELECT s.*, m.name as parent_name FROM submenus s LEFT JOIN menus m ON s.menu_id = m.id ORDER BY s.menu_id ASC, s.sort_order ASC, s.id ASC")->fetchAll(PDO::FETCH_ASSOC);
    
    // Fetch testimonials
    $testimonials = $db->query("SELECT * FROM testimonials ORDER BY sort_order ASC, id DESC")->fetchAll(PDO::FETCH_ASSOC);

    // Fetch industries
    $industriesList = $db->query("SELECT * FROM industries ORDER BY sort_order ASC, id ASC")->fetchAll(PDO::FETCH_ASSOC);
    
    // 3. Fetch submissions (Leads) with filters
    $filterStatus = isset($_GET['status']) ? filter_input(INPUT_GET, 'status', FILTER_DEFAULT) : '';
    $filterService = isset($_GET['service']) ? filter_input(INPUT_GET, 'service', FILTER_DEFAULT) : '';
    $searchQuery = isset($_GET['search']) ? filter_input(INPUT_GET, 'search', FILTER_DEFAULT) : '';
    
    $sql = "SELECT * FROM submissions WHERE 1=1";
    $params = [];
    
    if (!empty($filterStatus)) {
        $sql .= " AND status = :status";
        $params[':status'] = $filterStatus;
    }
    if (!empty($filterService)) {
        $sql .= " AND service = :service";
        $params[':service'] = $filterService;
    }
    if (!empty($searchQuery)) {
        $sql .= " AND (name LIKE :search OR email LIKE :search OR phone LIKE :search OR message LIKE :search)";
        $params[':search'] = '%' . $searchQuery . '%';
    }
    
    $sql .= " ORDER BY id DESC";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Unique list of services from submissions for filter dropdown
    $servicesDropdown = $db->query("SELECT DISTINCT service FROM submissions ORDER BY service ASC")->fetchAll(PDO::FETCH_COLUMN);
    
} catch (PDOException $e) {
    die("Database Connection Error: " . $e->getMessage() . " - Please verify that you ran the <a href='../api/init.php'>initializer</a>.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enterprise Admin Dashboard - Local Global Services</title>
    <!-- Local Bootstrap 5 CSS -->
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <!-- Summernote CSS & jQuery + JS CDNs -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');
        
        :root {
            --primary-navy: #0B356D;
            --accent-red: #E31B23;
            --dark-blue: #031D44;
            --glass-bg: rgba(255, 255, 255, 0.85);
            --bg-canvas: #F3F4F6;
            --card-radius: 16px;
            --transition-smooth: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --navy-glow: 0 4px 20px rgba(11, 53, 109, 0.15);
        }
        
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-canvas);
            color: #1F2937;
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        h1, h2, h3, h4, h5, h6, .navbar-brand {
            font-family: 'Outfit', sans-serif;
        }
        
        /* Modern Header panel */
        .admin-header {
            background-color: var(--glass-bg);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(11, 53, 109, 0.08);
            padding: 16px 40px;
            position: sticky;
            top: 0;
            z-index: 1020;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
        }
        
        .brand-logo {
            font-weight: 800;
            font-size: 1.6rem;
            color: var(--dark-blue);
            text-decoration: none;
            display: flex;
            align-items: center;
            letter-spacing: -0.5px;
        }
        
        .brand-logo span {
            color: var(--accent-red);
        }
        
        .brand-logo-icon {
            font-size: 1.4rem;
            margin-right: 8px;
            color: var(--primary-navy);
            transform: rotate(-10deg);
        }
        
        /* Layout Sidebar Setup */
        .dashboard-container {
            display: flex;
            min-height: calc(100vh - 78px);
        }
        
        .sidebar-panel {
            width: 280px;
            background-color: var(--dark-blue);
            padding: 30px 15px;
            flex-shrink: 0;
            display: flex;
            flex-column: justify;
            flex-direction: column;
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.05);
        }
        
        .main-content-panel {
            flex-grow: 1;
            padding: 40px;
            overflow-y: auto;
            max-width: calc(100% - 280px);
        }
        
        /* Sidebar Links navigation items styling */
        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .sidebar-menu li a {
            display: flex;
            align-items: center;
            padding: 14px 20px;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            font-weight: 500;
            border-radius: 12px;
            transition: var(--transition-smooth);
            cursor: pointer;
            border-left: 3px solid transparent;
        }
        
        .sidebar-menu li a i {
            font-size: 1.15rem;
            margin-right: 14px;
            width: 24px;
            text-align: center;
            transition: var(--transition-smooth);
        }
        
        .sidebar-menu li a:hover {
            color: #FFFFFF;
            background: rgba(255, 255, 255, 0.06);
        }
        
        .sidebar-menu li a.active {
            color: #FFFFFF;
            background: linear-gradient(135deg, rgba(11, 53, 109, 0.8) 0%, rgba(227, 27, 35, 0.15) 100%);
            border-left-color: var(--accent-red);
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        }
        
        .sidebar-menu li a.active i {
            color: var(--accent-red);
            transform: scale(1.1);
        }
        
        /* Stats Widgets */
        .stat-card-gradient {
            border-radius: var(--card-radius);
            border: none;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            transition: var(--transition-smooth);
            overflow: hidden;
            position: relative;
            background: #FFFFFF;
        }
        
        .stat-card-gradient:hover {
            transform: translateY(-5px);
            box-shadow: var(--navy-glow);
        }
        
        .stat-card-gradient::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            height: 4px;
            width: 100%;
            background: var(--primary-navy);
        }
        
        .stat-card-gradient.pending::after { background: #F59E0B; }
        .stat-card-gradient.categories::after { background: var(--accent-red); }
        .stat-card-gradient.services::after { background: #10B981; }
        .stat-card-gradient.blogs::after { background: #6366F1; }
        
        .stat-icon-wrapper {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
        }
        
        /* Elegant Cards & Tables */
        .glass-card {
            background-color: #FFFFFF;
            border-radius: var(--card-radius);
            border: 1px solid rgba(11, 53, 109, 0.05);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
            padding: 30px;
            margin-bottom: 30px;
            transition: var(--transition-smooth);
        }
        
        .glass-card:hover {
            box-shadow: 0 15px 40px rgba(0,0,0,0.04);
        }
        
        .table th {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.8px;
            color: #6B7280;
            background-color: #F9FAFB;
            border-bottom: 2px solid #E5E7EB;
            padding: 16px 20px;
        }
        
        .table td {
            padding: 16px 20px;
            vertical-align: middle;
            border-bottom: 1px solid #F3F4F6;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(11, 53, 109, 0.02);
        }
        
        /* Badges status */
        .status-badge {
            font-size: 0.75rem;
            font-weight: 700;
            padding: 6px 14px;
            border-radius: 50px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            letter-spacing: 0.3px;
        }
        
        .badge-pending {
            background-color: rgba(245, 158, 11, 0.1);
            color: #D97706;
        }
        
        .badge-processed {
            background-color: rgba(11, 53, 109, 0.1);
            color: var(--primary-navy);
        }
        
        .badge-replied {
            background-color: rgba(16, 185, 129, 0.1);
            color: #059669;
        }
        
        .badge-converted {
            background-color: rgba(16, 185, 129, 0.1) !important;
            color: #059669 !important;
        }
        .badge-rejected {
            background-color: rgba(239, 68, 68, 0.1) !important;
            color: #DC2626 !important;
        }
        .badge-ongoing {
            background-color: rgba(59, 130, 246, 0.1) !important;
            color: #2563EB !important;
        }
        .badge-process {
            background-color: rgba(139, 92, 246, 0.1) !important;
            color: #7C3AED !important;
        }
        
        /* Form inputs customization */
        .form-control, .form-select {
            border-radius: 10px;
            padding: 12px 16px;
            border: 1.5px solid #E5E7EB;
            font-size: 0.95rem;
            transition: var(--transition-smooth);
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-navy);
            box-shadow: 0 0 0 4px rgba(11, 53, 109, 0.1);
            outline: none;
        }
        
        .btn-brand-primary {
            background-color: var(--primary-navy);
            color: white;
            border: none;
            padding: 11px 24px;
            border-radius: 10px;
            font-weight: 600;
            transition: var(--transition-smooth);
        }
        
        .btn-brand-primary:hover {
            background-color: var(--dark-blue);
            transform: translateY(-2px);
            box-shadow: var(--navy-glow);
            color: white;
        }
        
        .btn-brand-accent {
            background-color: var(--accent-red);
            color: white;
            border: none;
            padding: 11px 24px;
            border-radius: 10px;
            font-weight: 600;
            transition: var(--transition-smooth);
        }
        
        .btn-brand-accent:hover {
            background-color: #C1131A;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(227, 27, 35, 0.3);
            color: white;
        }
        
        /* Tab panel content transition animation effects */
        .tab-panel-custom {
            display: none;
            animation: fadeIn 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .tab-panel-custom.active {
            display: block;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Service Cards list */
        .service-list-card {
            border: 1px solid rgba(11, 53, 109, 0.08);
            border-radius: 12px;
            padding: 20px;
            background: #FFFFFF;
            transition: var(--transition-smooth);
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .service-list-card:hover {
            border-color: var(--primary-navy);
            box-shadow: var(--navy-glow);
        }
        
        .service-card-icon {
            width: 48px;
            height: 48px;
            background-color: rgba(11, 53, 109, 0.05);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            color: var(--primary-navy);
            margin-bottom: 15px;
        }
        
        .service-gradient-indicator {
            height: 6px;
            border-radius: 50px;
            width: 60px;
            margin-bottom: 12px;
        }
        
        /* Custom alert notification popups */
        .alert-custom {
            border-radius: 12px;
            padding: 16px 24px;
            border: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
            display: flex;
            align-items: center;
            gap: 12px;
        }
    </style>
</head>
<body>

    <!-- Header bar -->
    <header class="admin-header d-flex justify-content-between align-items-center">
        <a href="../index.html" class="brand-logo" target="_blank">
            <i class="fa-solid fa-network-wired brand-logo-icon"></i>
            Local<span>Global</span> <span style="font-size: 0.9rem; font-weight: 500; color: #6B7280; margin-left: 10px; text-transform: uppercase;">CMS Admin</span>
        </a>
        
        <div class="d-flex align-items-center gap-4">
            <a href="../index.html" class="btn btn-sm btn-outline-secondary px-3 py-2" target="_blank" style="border-radius: 8px;">
                <i class="fa-solid fa-arrow-up-right-from-screen me-2"></i> View Site
            </a>
            
            <div class="vr text-black-50" style="height: 25px;"></div>
            
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted small">
                    <i class="fa-solid fa-shield-halved me-1 text-primary"></i> 
                    Security Administrator: <strong><?php echo htmlspecialchars($_SESSION['admin_username']); ?></strong>
                </span>
                <a href="logout.php" class="btn btn-outline-danger btn-sm px-3 py-2" style="border-radius: 8px; font-weight: 600;">
                    <i class="fa-solid fa-arrow-right-from-bracket me-1"></i> Sign Out
                </a>
            </div>
        </div>
    </header>

    <div class="dashboard-container">
        
        <!-- Sidebar Panel -->
        <aside class="sidebar-panel">
            <ul class="sidebar-menu" id="dashboardSidebar">
                <li>
                    <a class="tab-trigger active" data-target="panel-overview">
                        <i class="fa-solid fa-chart-line"></i> Dashboard Overview
                    </a>
                </li>
                <li>
                    <a class="tab-trigger" data-target="panel-leads">
                        <i class="fa-solid fa-envelope-open-text"></i> Client Leads
                        <?php if ($pendingCount > 0): ?>
                            <span class="badge bg-warning text-dark ms-auto font-weight-bold" style="font-size: 0.75rem; border-radius: 50px; padding: 4px 8px;"><?php echo $pendingCount; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li>
                    <a class="tab-trigger" data-target="panel-navbar">
                        <i class="fa-solid fa-bars"></i> Navbar Categories
                    </a>
                </li>
                <li>
                    <a class="tab-trigger" data-target="panel-services">
                        <i class="fa-solid fa-layer-group"></i> Services & Content
                    </a>
                </li>
                <li>
                    <a class="tab-trigger" data-target="panel-blogs">
                        <i class="fa-solid fa-newspaper"></i> Manage Blogs
                    </a>
                </li>
                <li>
                    <a class="tab-trigger" data-target="panel-testimonials">
                        <i class="fa-regular fa-comment-dots"></i> Testimonials
                    </a>
                </li>
                <li>
                    <a class="tab-trigger" data-target="panel-industries">
                        <i class="fa-solid fa-industry"></i> Industries Sectors
                    </a>
                </li>
                <li>
                    <a class="tab-trigger" data-target="panel-settings">
                        <i class="fa-solid fa-gears"></i> Site Settings
                    </a>
                </li>
            </ul>
            
            <div class="mt-auto p-3 text-center text-white-50 small border-top border-secondary border-opacity-25 pt-4">
                <span>Enterprise CMS Platform<br>v1.5 &copy; 2026 LGS IT</span>
            </div>
        </aside>
        
        <!-- Main Content Area -->
        <main class="main-content-panel">
            
            <!-- Global action notification alerts -->
            <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $messageType; ?> alert-custom alert-dismissible fade show mb-4" role="alert">
                    <div>
                        <?php if ($messageType === 'success'): ?>
                            <i class="fa-solid fa-circle-check fs-5 text-success"></i>
                        <?php elseif ($messageType === 'warning'): ?>
                            <i class="fa-solid fa-triangle-exclamation fs-5 text-warning"></i>
                        <?php else: ?>
                            <i class="fa-solid fa-circle-exclamation fs-5 text-danger"></i>
                        <?php endif; ?>
                    </div>
                    <div>
                        <strong>System Notice:</strong> <?php echo $message; ?>
                    </div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- ------------------------------------------------------------- -->
            <!-- TAB 1: OVERVIEW PANEL -->
            <!-- ------------------------------------------------------------- -->
            <section class="tab-panel-custom active" id="panel-overview">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="fw-bold mb-1">Corporate System Status</h2>
                        <p class="text-muted mb-0">Overview of client interactions and website architecture configurations.</p>
                    </div>
                    <span class="badge bg-light text-dark border py-2 px-3 small" style="border-radius: 8px;">
                        <i class="fa-regular fa-calendar me-2 text-primary"></i> Server Time: <?php echo date('Y-m-d H:i'); ?>
                    </span>
                </div>
                
                <!-- Metrics counters cards row -->
                <div class="row g-4 mb-5">
                    <div class="col-md col-sm-6">
                        <div class="card stat-card-gradient p-4 h-100">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-muted font-weight-bold mb-1 small text-uppercase tracking-wider">Total Inquiries</h6>
                                    <h2 class="mb-0 fw-extrabold text-navy"><?php echo $totalCount; ?></h2>
                                </div>
                                <div class="stat-icon-wrapper bg-primary bg-opacity-10 text-primary">
                                    <i class="fa-solid fa-envelope-open-text"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md col-sm-6">
                        <div class="card stat-card-gradient pending p-4 h-100">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-muted font-weight-bold mb-1 small text-uppercase tracking-wider">Pending Leads</h6>
                                    <h2 class="mb-0 fw-extrabold text-warning"><?php echo $pendingCount; ?></h2>
                                </div>
                                <div class="stat-icon-wrapper text-warning" style="background-color: rgba(245, 158, 11, 0.1);">
                                    <i class="fa-solid fa-clock-rotate-left"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md col-sm-6">
                        <div class="card stat-card-gradient categories p-4 h-100">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-muted font-weight-bold mb-1 small text-uppercase tracking-wider">Navbar Categories</h6>
                                    <h2 class="mb-0 fw-extrabold text-danger"><?php echo $menuCount; ?></h2>
                                </div>
                                <div class="stat-icon-wrapper bg-danger bg-opacity-10 text-danger">
                                    <i class="fa-solid fa-bars"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md col-sm-6">
                        <div class="card stat-card-gradient services p-4 h-100">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-muted font-weight-bold mb-1 small text-uppercase tracking-wider">Active Services</h6>
                                    <h2 class="mb-0 fw-extrabold text-success"><?php echo $submenuCount; ?></h2>
                                </div>
                                <div class="stat-icon-wrapper bg-success bg-opacity-10 text-success">
                                    <i class="fa-solid fa-layer-group"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md col-sm-6">
                        <div class="card stat-card-gradient blogs p-4 h-100">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-muted font-weight-bold mb-1 small text-uppercase tracking-wider">Total Blogs</h6>
                                    <h2 class="mb-0 fw-extrabold" style="color: #6366F1;"><?php echo $blogsCount; ?></h2>
                                </div>
                                <div class="stat-icon-wrapper text-indigo" style="background-color: rgba(99, 102, 241, 0.1); color: #6366F1;">
                                    <i class="fa-solid fa-newspaper"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row g-4">
                    <!-- Quick Actions -->
                    <div class="col-lg-6">
                        <div class="glass-card h-100">
                            <h4 class="fw-bold mb-4"><i class="fa-solid fa-bolt me-2 text-danger"></i> Quick Platform Operations</h4>
                            <p class="text-muted small">Perform typical platform configuration updates directly through these shortcut modals.</p>
                            
                            <div class="d-flex flex-column gap-3 mt-4">
                                <button class="btn btn-outline-primary text-start p-3 d-flex align-items-center justify-content-between" style="border-radius: 12px;" data-bs-toggle="modal" data-bs-target="#modalAddMenu">
                                    <span><i class="fa-solid fa-plus-circle me-3 text-primary fs-5"></i> Add New Navbar Parent Category</span>
                                    <i class="fa-solid fa-arrow-right"></i>
                                </button>
                                
                                <button class="btn btn-outline-primary text-start p-3 d-flex align-items-center justify-content-between" style="border-radius: 12px;" data-bs-toggle="modal" data-bs-target="#modalAddSubmenu">
                                    <span><i class="fa-solid fa-layer-group me-3 text-primary fs-5"></i> Create Dynamic Service Page / Submenu</span>
                                    <i class="fa-solid fa-arrow-right"></i>
                                </button>
                                
                                <a href="../api/init.php" onclick="return confirm('WARNING: Re-initializing will reset all content back to Excel defaults. Any manual CRUD changes will be lost. Are you sure you want to proceed?')" class="btn btn-outline-danger text-start p-3 d-flex align-items-center justify-content-between" style="border-radius: 12px;">
                                    <span><i class="fa-solid fa-arrows-rotate me-3 text-danger fs-5"></i> Factory Reset Database to Excel Defaults</span>
                                    <i class="fa-solid fa-exclamation-triangle"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Admin Guidelines -->
                    <div class="col-lg-6">
                        <div class="glass-card h-100" style="background-color: var(--primary-navy); color: white;">
                            <h4 class="fw-bold mb-4 text-white"><i class="fa-solid fa-circle-question me-2 text-danger"></i> Dynamic Content Instructions</h4>
                            
                            <div class="small d-flex flex-column gap-3 text-white-50">
                                <div class="d-flex gap-3">
                                    <i class="fa-solid fa-chevron-right text-danger mt-1"></i>
                                    <p class="mb-0"><strong>Single HTML Architecture:</strong> The client services menu runs dynamically from the database. When you modify or add service details in the <em>Services</em> tab, it updates the live corporate navbar and is immediately accessible via `service.html?id=service_key` with zero coding required.</p>
                                </div>
                                <div class="d-flex gap-3">
                                    <i class="fa-solid fa-chevron-right text-danger mt-1"></i>
                                    <p class="mb-0"><strong>Image & Cloudinary Upload:</strong> You can upload illustration images for service pages. If Cloudinary credentials are set in `api/cloudinary_config.php`, images will be hosted in Cloudinary; otherwise, the system will seamlessly save files locally in `resources/uploads/`.</p>
                                </div>
                                <div class="d-flex gap-3">
                                    <i class="fa-solid fa-chevron-right text-danger mt-1"></i>
                                    <p class="mb-0"><strong>Navbar Integration:</strong> Parent categories added here appear in the top-bar immediately. If a category contains nested services/submenus, it dynamically displays as an organized drop-down or full mega-menu column. Otherwise, it functions as a single landing anchor.</p>
                                </div>
                                <div class="d-flex gap-3">
                                    <i class="fa-solid fa-chevron-right text-danger mt-1"></i>
                                    <p class="mb-0"><strong>Features formatting:</strong> To set highlights in a service page, input them one-per-line in the Features textarea. The system processes them natively as high-quality bullet checklist rows.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ------------------------------------------------------------- -->
            <!-- TAB 2: CLIENT LEADS PANEL -->
            <!-- ------------------------------------------------------------- -->
            <section class="tab-panel-custom" id="panel-leads">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="fw-bold mb-1">Client Inquiries Manager</h2>
                        <p class="text-muted mb-0">Inspect, filter, update processing status, or delete submission logs.</p>
                    </div>
                </div>
                
                <!-- Search & Filters -->
                <div class="glass-card mb-4">
                    <h5 class="fw-bold mb-3"><i class="fa-solid fa-filter me-2 text-primary"></i> Query & Filters</h5>
                    <form method="GET" action="" class="row g-3">
                        <input type="hidden" name="active_tab" value="panel-leads">
                        
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="search" placeholder="Search by name, email, content..." value="<?php echo htmlspecialchars($searchQuery); ?>">
                        </div>
                        
                        <div class="col-md-3">
                            <select class="form-select" name="status">
                                <option value="">-- All Statuses --</option>
                                <option value="Pending" <?php echo $filterStatus === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="Processed" <?php echo $filterStatus === 'Processed' ? 'selected' : ''; ?>>Processed</option>
                                <option value="Replied" <?php echo $filterStatus === 'Replied' ? 'selected' : ''; ?>>Replied</option>
                                <option value="Converted" <?php echo $filterStatus === 'Converted' ? 'selected' : ''; ?>>Converted</option>
                                <option value="Rejected" <?php echo $filterStatus === 'Rejected' ? 'selected' : ''; ?>>Rejected</option>
                                <option value="Ongoing" <?php echo $filterStatus === 'Ongoing' ? 'selected' : ''; ?>>Ongoing</option>
                                <option value="Process" <?php echo $filterStatus === 'Process' ? 'selected' : ''; ?>>Process</option>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <select class="form-select" name="service">
                                <option value="">-- Filter by Service --</option>
                                <?php foreach ($servicesDropdown as $srvName): ?>
                                    <option value="<?php echo htmlspecialchars($srvName); ?>" <?php echo $filterService === $srvName ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($srvName); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-2 d-flex gap-2">
                            <button type="submit" class="btn btn-brand-primary w-100 px-2">Filter</button>
                            <a href="dashboard.php?active_tab=panel-leads" class="btn btn-outline-secondary w-100">Reset</a>
                        </div>
                    </form>
                </div>
                
                <!-- Table -->
                <div class="glass-card">
                    <h5 class="fw-bold mb-4"><i class="fa-regular fa-folder-open me-2 text-primary"></i> Client Inquiries Log</h5>
                    
                    <?php if (count($submissions) === 0): ?>
                        <div class="text-center py-5">
                            <i class="fa-regular fa-envelope text-muted mb-3" style="font-size: 3.5rem;"></i>
                            <p class="text-muted">No inquiries found matching your filter parameters.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Name & Contact</th>
                                        <th>Service Capability</th>
                                        <th>Message Details</th>
                                        <th>Received</th>
                                        <th>Status</th>
                                        <th class="text-end">Operations</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($submissions as $sub): ?>
                                        <tr>
                                            <td>
                                                <div class="fw-bold text-navy"><?php echo htmlspecialchars($sub['name']); ?></div>
                                                <a href="mailto:<?php echo htmlspecialchars($sub['email']); ?>" class="text-muted small text-decoration-none d-block">
                                                    <i class="fa-regular fa-envelope me-1"></i><?php echo htmlspecialchars($sub['email']); ?>
                                                </a>
                                                <?php if (!empty($sub['phone'])): ?>
                                                    <a href="tel:<?php echo htmlspecialchars($sub['phone']); ?>" class="text-muted small text-decoration-none d-block mt-1">
                                                        <i class="fa-solid fa-phone me-1" style="font-size: 0.75rem;"></i><?php echo htmlspecialchars($sub['phone']); ?>
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                            
                                            <td>
                                                <span class="badge bg-light text-dark border p-2" style="font-size: 0.8rem; border-radius: 6px; font-weight: 500;">
                                                    <?php echo htmlspecialchars($sub['service']); ?>
                                                </span>
                                            </td>
                                            
                                            <td>
                                                <div class="text-wrap small text-muted" style="max-width: 320px; max-height: 80px; overflow-y: auto;">
                                                    <?php echo nl2br(htmlspecialchars($sub['message'])); ?>
                                                </div>
                                            </td>
                                            
                                            <td class="small text-muted" style="font-size: 0.85rem;">
                                                <?php echo date('Y-m-d H:i', strtotime($sub['created_at'])); ?>
                                            </td>
                                            
                                            <td>
                                                <?php if ($sub['status'] === 'Pending'): ?>
                                                    <span class="status-badge badge-pending"><i class="fa-solid fa-clock"></i> Pending</span>
                                                <?php elseif ($sub['status'] === 'Processed'): ?>
                                                    <span class="status-badge badge-processed"><i class="fa-solid fa-arrows-spin fa-spin"></i> Processed</span>
                                                <?php elseif ($sub['status'] === 'Replied'): ?>
                                                    <span class="status-badge badge-replied"><i class="fa-solid fa-circle-check"></i> Replied</span>
                                                <?php elseif ($sub['status'] === 'Converted'): ?>
                                                    <span class="status-badge badge-converted"><i class="fa-solid fa-circle-check"></i> Converted</span>
                                                <?php elseif ($sub['status'] === 'Rejected'): ?>
                                                    <span class="status-badge badge-rejected"><i class="fa-solid fa-circle-xmark"></i> Rejected</span>
                                                <?php elseif ($sub['status'] === 'Ongoing'): ?>
                                                    <span class="status-badge badge-ongoing"><i class="fa-solid fa-spinner fa-spin-slow"></i> Ongoing</span>
                                                <?php elseif ($sub['status'] === 'Process'): ?>
                                                    <span class="status-badge badge-process"><i class="fa-solid fa-gear fa-spin"></i> Process</span>
                                                <?php else: ?>
                                                    <span class="status-badge badge bg-secondary text-white"><?php echo htmlspecialchars($sub['status']); ?></span>
                                                <?php endif; ?>
                                            </td>
                                            
                                            <td class="text-end">
                                                <div class="d-flex justify-content-end align-items-center gap-2">
                                                    <!-- Update Status Inline -->
                                                    <form method="POST" action="" class="d-inline-flex m-0">
                                                        <input type="hidden" name="action" value="update_status">
                                                        <input type="hidden" name="id" value="<?php echo $sub['id']; ?>">
                                                        <select name="status" class="form-select form-select-sm" style="padding: 4px 8px; font-size: 0.8rem; border-radius: 6px;" onchange="this.form.submit()">
                                                            <option value="Pending" <?php echo $sub['status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                                            <option value="Processed" <?php echo $sub['status'] === 'Processed' ? 'selected' : ''; ?>>Processed</option>
                                                            <option value="Replied" <?php echo $sub['status'] === 'Replied' ? 'selected' : ''; ?>>Replied</option>
                                                            <option value="Converted" <?php echo $sub['status'] === 'Converted' ? 'selected' : ''; ?>>Converted</option>
                                                            <option value="Rejected" <?php echo $sub['status'] === 'Rejected' ? 'selected' : ''; ?>>Rejected</option>
                                                            <option value="Ongoing" <?php echo $sub['status'] === 'Ongoing' ? 'selected' : ''; ?>>Ongoing</option>
                                                            <option value="Process" <?php echo $sub['status'] === 'Process' ? 'selected' : ''; ?>>Process</option>
                                                        </select>
                                                    </form>
                                                    
                                                    <!-- Delete submission -->
                                                    <form method="POST" action="" class="d-inline-flex m-0" onsubmit="return confirm('Are you sure you want to delete this submission log forever?')">
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="id" value="<?php echo $sub['id']; ?>">
                                                        <button type="submit" class="btn btn-outline-danger btn-sm" style="border-radius: 6px; padding: 5px 8px;" title="Delete Record">
                                                            <i class="fa-regular fa-trash-can"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <!-- ------------------------------------------------------------- -->
            <!-- TAB 3: NAVBAR CATEGORIES PANEL -->
            <!-- ------------------------------------------------------------- -->
            <section class="tab-panel-custom" id="panel-navbar">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="fw-bold mb-1">Navbar Categories CRUD</h2>
                        <p class="text-muted mb-0">Manage the top-level parent menu categories. Changing order affects the live header bar.</p>
                    </div>
                    <button class="btn btn-brand-primary" data-bs-toggle="modal" data-bs-target="#modalAddMenu">
                        <i class="fa-solid fa-plus me-2"></i> Add Parent Category
                    </button>
                </div>
                
                <div class="glass-card">
                    <h5 class="fw-bold mb-4"><i class="fa-solid fa-bars me-2 text-primary"></i> Primary Menu Rows</h5>
                    
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th style="width: 80px;">ID</th>
                                    <th>Category Title</th>
                                    <th>Link Format (Calculated)</th>
                                    <th>Sorting Weight</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($menus as $m): ?>
                                    <tr>
                                        <td class="fw-bold text-muted"><?php echo $m['id']; ?></td>
                                        <td>
                                            <div class="fw-bold text-navy" style="font-size: 1.05rem;"><?php echo htmlspecialchars($m['name']); ?></div>
                                        </td>
                                        <td>
                                            <code class="small" style="color: var(--accent-red);">
                                                <?php 
                                                // Check if it has submenus
                                                $stmt = $db->prepare("SELECT COUNT(*) FROM submenus WHERE menu_id = :menu_id");
                                                $stmt->execute([':menu_id' => $m['id']]);
                                                $countSub = $stmt->fetchColumn();
                                                
                                                if ($countSub > 0) {
                                                    echo "Dynamic Mega/Dropdown (Multi-link)";
                                                } else {
                                                    echo "index.html#" . strtolower(preg_replace('/[^a-z0-9]/', '', $m['name']));
                                                }
                                                ?>
                                            </code>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary rounded-pill py-1 px-3"><?php echo $m['sort_order']; ?></span>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end align-items-center gap-2">
                                                <button class="btn btn-outline-primary btn-sm btn-edit-menu" 
                                                        data-id="<?php echo $m['id']; ?>"
                                                        data-name="<?php echo htmlspecialchars($m['name']); ?>"
                                                        data-sort="<?php echo $m['sort_order']; ?>"
                                                        data-bs-toggle="modal" data-bs-target="#modalEditMenu" style="border-radius: 6px;">
                                                    <i class="fa-regular fa-pen-to-square me-1"></i> Edit
                                                </button>
                                                
                                                <form method="POST" action="" class="d-inline-flex m-0" onsubmit="return confirm('DANGER: Deleting this menu category will delete all nested service items and capabilities linked to it! Are you absolutely sure?')">
                                                    <input type="hidden" name="action" value="delete_menu">
                                                    <input type="hidden" name="id" value="<?php echo $m['id']; ?>">
                                                    <button type="submit" class="btn btn-outline-danger btn-sm" style="border-radius: 6px;" title="Delete Category">
                                                        <i class="fa-regular fa-trash-can"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- ------------------------------------------------------------- -->
            <!-- TAB 4: SERVICES MANAGER PANEL -->
            <!-- ------------------------------------------------------------- -->
            <section class="tab-panel-custom" id="panel-services">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="fw-bold mb-1">Dynamic Services Content CRUD</h2>
                        <p class="text-muted mb-0">Create new service capability templates or modify text, checklists, and gradients of existing ones.</p>
                    </div>
                    <button class="btn btn-brand-primary" data-bs-toggle="modal" data-bs-target="#modalAddSubmenu">
                        <i class="fa-solid fa-plus me-2"></i> Create New Service Page
                    </button>
                </div>
                
                <div class="row g-4">
                    <?php if (count($submenus) === 0): ?>
                        <div class="col-12 text-center py-5 bg-white rounded shadow-sm border">
                            <i class="fa-solid fa-circle-info text-muted mb-3" style="font-size: 3rem;"></i>
                            <p class="text-muted">No nested services/submenus found. Add your first service template above!</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($submenus as $s): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="service-list-card">
                                    <div>
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1 font-weight-bold" style="font-size: 0.75rem; border-radius: 50px;">
                                                Parent: <?php echo htmlspecialchars($s['parent_name']); ?>
                                            </span>
                                            <span class="small text-muted font-weight-bold">Order: <?php echo $s['sort_order']; ?></span>
                                        </div>
                                        
                                        <h5 class="fw-bold text-navy mt-2 mb-1"><?php echo htmlspecialchars($s['name']); ?></h5>
                                        <div class="service-gradient-indicator" style="background: <?php echo $s['banner_grad']; ?>;"></div>
                                        
                                        <div class="small text-muted mb-3" style="line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 38px;">
                                            <?php echo htmlspecialchars($s['tagline']); ?>
                                        </div>
                                        
                                        <div class="d-flex gap-2 align-items-center mb-3">
                                            <?php if (!empty($s['image_url'])): ?>
                                                <img src="<?php echo htmlspecialchars($s['image_url']); ?>" alt="" class="rounded border" style="width: 32px; height: 32px; object-fit: contain;">
                                            <?php else: ?>
                                                <div class="service-card-icon m-0" style="width: 32px; height: 32px; font-size: 1rem; border-radius: 6px;">
                                                    <i class="fa-solid <?php echo htmlspecialchars($s['icon']); ?>"></i>
                                                </div>
                                            <?php endif; ?>
                                            <code class="small text-dark font-weight-bold bg-light p-1 border rounded">key: <?php echo htmlspecialchars($s['service_key']); ?></code>
                                        </div>
                                    </div>
                                    
                                    <div class="border-top pt-3 d-flex justify-content-between align-items-center mt-3">
                                        <!-- Open Live Link -->
                                        <a href="../service.html?id=<?php echo htmlspecialchars($s['service_key']); ?>" target="_blank" class="btn btn-link text-decoration-none p-0 text-primary small" style="font-size: 0.85rem; font-weight: 600;">
                                            <i class="fa-solid fa-arrow-up-right-from-screen me-1"></i> Preview Live
                                        </a>
                                        
                                        <div class="d-flex gap-1">
                                            <!-- Edit Trigger -->
                                            <button class="btn btn-outline-primary btn-sm btn-edit-submenu" 
                                                    data-id="<?php echo $s['id']; ?>"
                                                    data-menu_id="<?php echo $s['menu_id']; ?>"
                                                    data-name="<?php echo htmlspecialchars($s['name']); ?>"
                                                    data-key="<?php echo htmlspecialchars($s['service_key']); ?>"
                                                    data-tagline="<?php echo htmlspecialchars($s['tagline']); ?>"
                                                    data-icon="<?php echo htmlspecialchars($s['icon']); ?>"
                                                    data-desc1="<?php echo htmlspecialchars($s['desc1']); ?>"
                                                    data-desc2="<?php echo htmlspecialchars($s['desc2']); ?>"
                                                    data-features="<?php echo htmlspecialchars($s['features']); ?>"
                                                    data-banner="<?php echo htmlspecialchars($s['banner_grad']); ?>"
                                                    data-sort="<?php echo $s['sort_order']; ?>"
                                                    data-image="<?php echo htmlspecialchars($s['image_url'] ?? ''); ?>"
                                                    data-bs-toggle="modal" data-bs-target="#modalEditSubmenu" style="border-radius: 6px;">
                                                <i class="fa-regular fa-pen-to-square"></i>
                                            </button>
                                            
                                            <!-- Delete Trigger -->
                                            <form method="POST" action="" class="d-inline-flex m-0" onsubmit="return confirm('Are you sure you want to delete this service template capability page?')">
                                                <input type="hidden" name="action" value="delete_submenu">
                                                <input type="hidden" name="id" value="<?php echo $s['id']; ?>">
                                                <button type="submit" class="btn btn-outline-danger btn-sm" style="border-radius: 6px;" title="Delete Service">
                                                    <i class="fa-regular fa-trash-can"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>

            <!-- TAB 5: MANAGE BLOGS PANEL -->
            <section class="tab-panel-custom" id="panel-blogs">
                <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom">
                    <div>
                        <h2 class="fw-bold mb-1">Manage Blog Articles</h2>
                        <p class="text-muted mb-0">Write, edit, and publish articles, news updates, and search engine resources.</p>
                    </div>
                    <button class="btn btn-danger btn-custom" data-bs-toggle="modal" data-bs-target="#modalAddBlog">
                        <i class="fa-solid fa-pen-nib me-2"></i> Write New Article
                    </button>
                </div>
                
                <div class="glass-card p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 80px;">Cover</th>
                                    <th>Title</th>
                                    <th>Slug</th>
                                    <th>Created At</th>
                                    <th class="text-end" style="width: 150px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($blogs)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="fa-regular fa-newspaper display-1 mb-3 d-block text-secondary opacity-50"></i>
                                            No blog articles published yet. Click "Write New Article" to create one.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($blogs as $blog): ?>
                                        <tr>
                                            <td>
                                                <?php if (!empty($blog['image_url'])): ?>
                                                    <img src="<?php echo htmlspecialchars($blog['image_url']); ?>" alt="" style="width: 60px; height: 40px; object-fit: cover; border-radius: 6px;">
                                                <?php else: ?>
                                                    <div class="bg-secondary bg-opacity-25 rounded d-flex align-items-center justify-content-center text-muted" style="width: 60px; height: 40px; font-size: 0.8rem;">
                                                        <i class="fa-regular fa-image"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-navy"><?php echo htmlspecialchars($blog['title']); ?></div>
                                                <span class="badge bg-light text-dark border mt-1 font-weight-normal"><?php echo htmlspecialchars($blog['seo_title']); ?></span>
                                            </td>
                                            <td><code>/blog.html?id=<?php echo htmlspecialchars($blog['slug']); ?></code></td>
                                            <td class="small text-muted"><?php echo date('M d, Y H:i', strtotime($blog['created_at'])); ?></td>
                                            <td class="text-end">
                                                <div class="btn-group">
                                                    <button type="button" 
                                                            class="btn btn-outline-primary btn-sm btn-edit-blog" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#modalEditBlog"
                                                            data-id="<?php echo $blog['id']; ?>"
                                                            data-title="<?php echo htmlspecialchars($blog['title'], ENT_QUOTES); ?>"
                                                            data-summary="<?php echo htmlspecialchars($blog['summary'], ENT_QUOTES); ?>"
                                                            data-content="<?php echo htmlspecialchars($blog['content'], ENT_QUOTES); ?>"
                                                            data-seo_title="<?php echo htmlspecialchars($blog['seo_title'], ENT_QUOTES); ?>"
                                                            data-meta_desc="<?php echo htmlspecialchars($blog['meta_description'], ENT_QUOTES); ?>"
                                                            data-image="<?php echo htmlspecialchars($blog['image_url'] ?? '', ENT_QUOTES); ?>"
                                                            data-author="<?php echo htmlspecialchars($blog['author'] ?? '', ENT_QUOTES); ?>"
                                                            style="border-radius: 6px 0 0 6px;" 
                                                            title="Edit Article">
                                                        <i class="fa-regular fa-pen-to-square"></i>
                                                    </button>
                                                    <form action="dashboard.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this blog post? This action cannot be undone.');" style="display:inline;">
                                                        <input type="hidden" name="action" value="delete_blog">
                                                        <input type="hidden" name="id" value="<?php echo $blog['id']; ?>">
                                                        <button type="submit" class="btn btn-outline-danger btn-sm" style="border-radius: 0 6px 6px 0;" title="Delete Article">
                                                            <i class="fa-regular fa-trash-can"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- ========================================================================== -->
            <!-- TAB: MANAGE TESTIMONIALS PANEL -->
            <!-- ========================================================================== -->
            <section class="tab-panel-custom" id="panel-testimonials">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                    <div>
                        <h2 class="fw-bold mb-1">Manage Testimonials</h2>
                        <p class="text-muted mb-0">Add, edit, or delete customer reviews displayed on the home page testimonial track.</p>
                    </div>
                    <button class="btn btn-danger btn-custom" data-bs-toggle="modal" data-bs-target="#modalAddTestimonial">
                        <i class="fa-solid fa-plus me-2"></i> Add Testimonial
                    </button>
                </div>
                
                <div class="glass-card">
                    <h5 class="fw-bold mb-4"><i class="fa-regular fa-comment-dots me-2 text-primary"></i> Testimonies Listing</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th style="width: 80px;">Avatar</th>
                                    <th>Client Details</th>
                                    <th>Service Name</th>
                                    <th>Testimonial Text</th>
                                    <th>Order</th>
                                    <th class="text-end">Operations</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($testimonials) === 0): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            No testimonials found in database. Add one to show on homepage!
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($testimonials as $t): ?>
                                        <tr>
                                            <td>
                                                <?php if (!empty($t['image_url'])): ?>
                                                    <img src="<?php echo htmlspecialchars('../' . str_replace('../', '', $t['image_url'])); ?>" alt="" style="width: 45px; height: 45px; object-fit: cover; border-radius: 50%; border: 1px solid #ddd;">
                                                <?php else: ?>
                                                    <div class="bg-secondary bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center text-muted text-uppercase" style="width: 45px; height: 45px; font-weight: bold; font-size: 0.85rem;">
                                                        <?php echo htmlspecialchars(substr($t['client_name'], 0, 2)); ?>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td><div class="fw-bold text-navy"><?php echo htmlspecialchars($t['client_name']); ?></div></td>
                                            <td><span class="badge bg-light text-dark border font-weight-normal"><?php echo htmlspecialchars($t['service_name']); ?></span></td>
                                            <td>
                                                <div class="text-wrap small text-muted" style="max-width: 400px; max-height: 80px; overflow-y: auto;">
                                                    <?php echo htmlspecialchars($t['testimonial_text']); ?>
                                                </div>
                                            </td>
                                            <td><?php echo $t['sort_order']; ?></td>
                                            <td class="text-end">
                                                <div class="btn-group">
                                                    <button type="button" 
                                                            class="btn btn-outline-primary btn-sm btn-edit-testimonial" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#modalEditTestimonial"
                                                            data-id="<?php echo $t['id']; ?>"
                                                            data-client="<?php echo htmlspecialchars($t['client_name'], ENT_QUOTES); ?>"
                                                            data-service="<?php echo htmlspecialchars($t['service_name'], ENT_QUOTES); ?>"
                                                            data-text="<?php echo htmlspecialchars($t['testimonial_text'], ENT_QUOTES); ?>"
                                                            data-sort="<?php echo $t['sort_order']; ?>"
                                                            data-image="<?php echo htmlspecialchars($t['image_url'] ?? '', ENT_QUOTES); ?>"
                                                            style="border-radius: 6px 0 0 6px;" 
                                                            title="Edit Testimonial">
                                                        <i class="fa-regular fa-pen-to-square"></i>
                                                    </button>
                                                    <form action="dashboard.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this testimonial?');" style="display:inline;">
                                                        <input type="hidden" name="action" value="delete_testimonial">
                                                        <input type="hidden" name="id" value="<?php echo $t['id']; ?>">
                                                        <button type="submit" class="btn btn-outline-danger btn-sm" style="border-radius: 0 6px 6px 0;" title="Delete Testimonial">
                                                            <i class="fa-regular fa-trash-can"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- ========================================================================== -->
            <!-- TAB: MANAGE INDUSTRIES PANEL -->
            <!-- ========================================================================== -->
            <section class="tab-panel-custom" id="panel-industries">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                    <div>
                        <h2 class="fw-bold mb-1">Manage Industries We Empower</h2>
                        <p class="text-muted mb-0">Control the dynamic sectors system shown on the index home page.</p>
                    </div>
                    <button class="btn btn-danger btn-custom" data-bs-toggle="modal" data-bs-target="#modalAddIndustry">
                        <i class="fa-solid fa-plus me-2"></i> Add Industry Sector
                    </button>
                </div>
                
                <div class="glass-card">
                    <h5 class="fw-bold mb-4"><i class="fa-solid fa-industry me-2 text-primary"></i> Industry Sectors Listing</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">Icon</th>
                                    <th>Sector Name</th>
                                    <th>Display Title</th>
                                    <th>Description Snippet</th>
                                    <th>Order</th>
                                    <th class="text-end">Operations</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($industriesList) === 0): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            No industry sectors found in database. Add one to show on homepage!
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($industriesList as $ind): ?>
                                        <tr>
                                            <td>
                                                <div class="bg-light text-navy rounded border d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-size: 1.1rem; overflow: hidden;">
                                                    <?php if (!empty($ind['image_url'])): ?>
                                                        <img src="<?php echo htmlspecialchars('../' . str_replace('../', '', $ind['image_url'])); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                                    <?php else: ?>
                                                        <i class="fa-solid <?php echo htmlspecialchars($ind['icon']); ?>"></i>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td><div class="fw-bold text-navy"><?php echo htmlspecialchars($ind['name']); ?></div></td>
                                            <td><div class="fw-bold text-muted small"><?php echo htmlspecialchars($ind['title']); ?></div></td>
                                            <td>
                                                <div class="text-wrap small text-muted" style="max-width: 400px; max-height: 80px; overflow-y: auto;">
                                                    <?php echo htmlspecialchars($ind['description']); ?>
                                                </div>
                                            </td>
                                            <td><?php echo $ind['sort_order']; ?></td>
                                            <td class="text-end">
                                                <div class="btn-group">
                                                    <button type="button" 
                                                            class="btn btn-outline-primary btn-sm btn-edit-industry" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#modalEditIndustry"
                                                            data-id="<?php echo $ind['id']; ?>"
                                                            data-name="<?php echo htmlspecialchars($ind['name'], ENT_QUOTES); ?>"
                                                            data-title="<?php echo htmlspecialchars($ind['title'], ENT_QUOTES); ?>"
                                                            data-desc="<?php echo htmlspecialchars($ind['description'], ENT_QUOTES); ?>"
                                                            data-features="<?php echo htmlspecialchars($ind['features'] ?? '', ENT_QUOTES); ?>"
                                                            data-icon="<?php echo htmlspecialchars($ind['icon'], ENT_QUOTES); ?>"
                                                            data-sort="<?php echo $ind['sort_order']; ?>"
                                                            data-image="<?php echo htmlspecialchars($ind['image_url'] ?? '', ENT_QUOTES); ?>"
                                                            style="border-radius: 6px 0 0 6px;" 
                                                            title="Edit Sector">
                                                        <i class="fa-regular fa-pen-to-square"></i>
                                                    </button>
                                                    <form action="dashboard.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this industry sector?');" style="display:inline;">
                                                        <input type="hidden" name="action" value="delete_industry">
                                                        <input type="hidden" name="id" value="<?php echo $ind['id']; ?>">
                                                        <button type="submit" class="btn btn-outline-danger btn-sm" style="border-radius: 0 6px 6px 0;" title="Delete Sector">
                                                            <i class="fa-regular fa-trash-can"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- TAB 6: SITE SETTINGS PANEL -->
            <section class="tab-panel-custom" id="panel-settings">
                <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom">
                    <div>
                        <h2 class="fw-bold mb-1">Global Site Settings</h2>
                        <p class="text-muted mb-0">Update central system variables, contact numbers, emails, and global content overlays.</p>
                    </div>
                </div>
                
                <form action="dashboard.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="save_settings">
                    
                    <div class="row g-4">
                        <!-- Left Column: Contact Details -->
                        <div class="col-lg-6">
                            <div class="glass-card p-4 h-100 d-flex flex-column">
                                <h5 class="fw-bold text-navy mb-4"><i class="fa-solid fa-address-book text-danger me-2"></i> Contact Details</h5>
                                
                                <div class="mb-3">
                                    <label class="form-label font-weight-bold small text-muted text-uppercase">Contact Mobile Number</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-phone"></i></span>
                                        <input type="text" class="form-control" name="contact_phone" value="<?php echo htmlspecialchars($settings['contact_phone'] ?? ''); ?>" required placeholder="e.g. +91-9718117270">
                                    </div>
                                    <div class="form-text">This phone number will update dynamically in headers, footers, and contact links across the site.</div>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label font-weight-bold small text-muted text-uppercase">Contact Email Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-envelope"></i></span>
                                        <input type="email" class="form-control" name="contact_email" value="<?php echo htmlspecialchars($settings['contact_email'] ?? ''); ?>" required placeholder="e.g. sales@localglobal.com">
                                    </div>
                                    <div class="form-text">This email address will update dynamically in footers and contact links across the site.</div>
                                </div>
                                
                            </div>
                        </div>
                        
                        <!-- Right Column: Home Popup Config -->
                        <div class="col-lg-6">
                            <div class="glass-card p-4 h-100">
                                <h5 class="fw-bold text-navy mb-4"><i class="fa-solid fa-bullhorn text-danger me-2"></i> Homepage Popup Settings</h5>
                                
                                <div class="mb-3">
                                    <label class="form-label font-weight-bold small text-muted text-uppercase">Popup Display Status</label>
                                    <select class="form-select" name="popup_status">
                                        <option value="show" <?php echo ($settings['popup_status'] ?? 'hide') === 'show' ? 'selected' : ''; ?>>Active (Show Popup to visitors)</option>
                                        <option value="hide" <?php echo ($settings['popup_status'] ?? 'hide') === 'hide' ? 'selected' : ''; ?>>Inactive (Hide/Stop Popup)</option>
                                    </select>
                                    <div class="form-text">Controls if a centered popup appears on the user's initial page visit.</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label font-weight-bold small text-muted text-uppercase">Popup Type</label>
                                    <select class="form-select" name="popup_type">
                                        <option value="both" <?php echo ($settings['popup_type'] ?? 'both') === 'both' ? 'selected' : ''; ?>>Image and Text</option>
                                        <option value="text" <?php echo ($settings['popup_type'] ?? 'both') === 'text' ? 'selected' : ''; ?>>Text Only</option>
                                        <option value="image" <?php echo ($settings['popup_type'] ?? 'both') === 'image' ? 'selected' : ''; ?>>Image Only</option>
                                    </select>
                                    <div class="form-text">Determines whether to show image, text description, or both.</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label font-weight-bold small text-muted text-uppercase">Popup Title (Text Type)</label>
                                    <input type="text" class="form-control" name="popup_title" value="<?php echo htmlspecialchars($settings['popup_title'] ?? ''); ?>" placeholder="e.g. Exclusive Enterprise Announcement">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label font-weight-bold small text-muted text-uppercase">Popup Body Message (Text Type)</label>
                                    <textarea class="form-control" name="popup_text" rows="3" placeholder="Provide announcement text..."><?php echo htmlspecialchars($settings['popup_text'] ?? ''); ?></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label font-weight-bold small text-muted text-uppercase">Popup Banner Image (Image Type)</label>
                                    <input type="file" class="form-control" name="popup_image_file" accept="image/*">
                                    <div class="form-text">Upload popup banner image file.</div>
                                    
                                    <?php if (!empty($settings['popup_image'])): ?>
                                        <div class="mt-2 p-2 border rounded bg-light d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center gap-2">
                                                <img src="<?php echo htmlspecialchars('../' . str_replace('../', '', $settings['popup_image'])); ?>" style="height: 40px; width: 60px; object-fit: cover; border-radius: 4px;">
                                                <span class="small text-muted text-truncate" style="max-width: 150px;">Active Image</span>
                                            </div>
                                            <div class="form-check form-switch mb-0">
                                                <input class="form-check-input" type="checkbox" id="clearPopupImage" name="popup_image_clear" value="1">
                                                <label class="form-check-label small text-danger" for="clearPopupImage">Remove image</label>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Row 2: Hero Section Settings -->
                        <div class="col-lg-6 mt-4">
                            <div class="glass-card p-4 h-100">
                                <h5 class="fw-bold text-navy mb-4"><i class="fa-solid fa-image text-danger me-2"></i> Hero Background Customization</h5>
                                
                                <div class="mb-3">
                                    <label class="form-label font-weight-bold small text-muted text-uppercase">Hero Background Image File</label>
                                    <input type="file" class="form-control" name="hero_bg_image_file" accept="image/*">
                                    <div class="form-text">Select a custom background image for the main Hero section.</div>
                                    
                                    <?php if (!empty($settings['hero_bg_image'])): ?>
                                        <div class="mt-2 p-2 border rounded bg-light d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center gap-2">
                                                <img src="<?php echo htmlspecialchars('../' . str_replace('../', '', $settings['hero_bg_image'])); ?>" style="height: 40px; width: 60px; object-fit: cover; border-radius: 4px;">
                                                <span class="small text-muted text-truncate" style="max-width: 150px;">Active Background</span>
                                            </div>
                                            <div class="form-check form-switch mb-0">
                                                <input class="form-check-input" type="checkbox" id="clearHeroBgImage" name="hero_bg_image_clear" value="1">
                                                <label class="form-check-label small text-danger" for="clearHeroBgImage">Reset to Default</label>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label font-weight-bold small text-muted text-uppercase d-flex justify-content-between">
                                        <span>Hero BG Image Opacity / Intensity</span>
                                        <span id="hero-bg-opacity-val" class="text-danger fw-bold"><?php echo htmlspecialchars($settings['hero_bg_opacity'] ?? '0.20'); ?></span>
                                    </label>
                                    <input type="range" class="form-range" name="hero_bg_opacity" min="0" max="1" step="0.05" value="<?php echo htmlspecialchars($settings['hero_bg_opacity'] ?? '0.20'); ?>" id="hero-bg-opacity-slider" oninput="document.getElementById('hero-bg-opacity-val').textContent = this.value;">
                                    <div class="form-text text-muted small">Adjust background opacity. 0 = invisible, 1 = fully clear background image.</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Row 2 Right Column: About Section Content -->
                        <div class="col-lg-6 mt-4">
                            <div class="glass-card p-4 h-100">
                                <h5 class="fw-bold text-navy mb-4"><i class="fa-solid fa-circle-info text-danger me-2"></i> About Us (Who We Are) CMS</h5>
                                
                                <div class="mb-3">
                                    <label class="form-label font-weight-bold small text-muted text-uppercase">Section Tagline Title</label>
                                    <input type="text" class="form-control" name="about_title" value="<?php echo htmlspecialchars($settings['about_title'] ?? 'Who We Are'); ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label font-weight-bold small text-muted text-uppercase">Section Display Subtitle</label>
                                    <input type="text" class="form-control" name="about_subtitle" value="<?php echo htmlspecialchars($settings['about_subtitle'] ?? ''); ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label font-weight-bold small text-muted text-uppercase">Description Paragraph 1 (Lead)</label>
                                    <textarea class="form-control" name="about_desc_1" rows="3" required><?php echo htmlspecialchars($settings['about_desc_1'] ?? ''); ?></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label font-weight-bold small text-muted text-uppercase">Description Paragraph 2</label>
                                    <textarea class="form-control" name="about_desc_2" rows="3" required><?php echo htmlspecialchars($settings['about_desc_2'] ?? ''); ?></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label font-weight-bold small text-muted text-uppercase">Section Showcase Image</label>
                                    <input type="file" class="form-control" name="about_image_file" accept="image/*">
                                    <div class="form-text text-muted small">Upload an image for the "Who We Are" section.</div>
                                    
                                    <?php if (!empty($settings['about_image'])): ?>
                                        <div class="mt-2 p-2 border rounded bg-light d-flex align-items-center gap-2">
                                            <img src="<?php echo htmlspecialchars('../' . str_replace('../', '', $settings['about_image'])); ?>" style="height: 40px; width: 60px; object-fit: cover; border-radius: 4px;">
                                            <span class="small text-muted text-truncate">Active Image</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Unified Settings Action Buttons -->
                    <div class="mt-4 border-top pt-4 text-end">
                        <button type="submit" class="btn btn-danger btn-custom px-5 py-2" style="border-radius: 8px; font-weight: 600;">
                            <i class="fa-regular fa-floppy-disk me-2"></i> Save All Settings
                        </button>
                    </div>
                </form>

                <!-- Change Admin Credentials Form Section -->
                <div class="mt-5 pt-4 border-top">
                    <h3 class="fw-bold mb-1">Account & Security Settings</h3>
                    <p class="text-muted mb-4">Update your dashboard administrator login credentials here.</p>
                    
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="glass-card p-4">
                                <h5 class="fw-bold text-navy mb-4"><i class="fa-solid fa-lock text-danger me-2"></i> Update Admin Credentials</h5>
                                
                                <form action="dashboard.php" method="POST">
                                    <input type="hidden" name="action" value="change_credentials">
                                    
                                    <div class="mb-3">
                                        <label class="form-label font-weight-bold small text-muted text-uppercase">Admin Username</label>
                                        <input type="text" class="form-control" name="new_username" required value="<?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'admin'); ?>" placeholder="Enter new username">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label font-weight-bold small text-muted text-uppercase">Current Password</label>
                                        <input type="password" class="form-control" name="current_password" required placeholder="Enter current password to verify identity">
                                    </div>
                                    
                                    <hr class="my-4">
                                    <p class="small text-muted mb-3">Leave the fields below blank if you only want to change your username.</p>
                                    
                                    <div class="mb-3">
                                        <label class="form-label font-weight-bold small text-muted text-uppercase">New Password (Optional)</label>
                                        <input type="password" class="form-control" name="new_password" placeholder="Enter new password">
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label class="form-label font-weight-bold small text-muted text-uppercase">Confirm New Password</label>
                                        <input type="password" class="form-control" name="confirm_password" placeholder="Confirm new password">
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary btn-custom px-4 py-2" style="border-radius: 8px; font-weight: 600;">
                                        <i class="fa-solid fa-key me-2"></i> Update Credentials
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </main>
    </div>

    <!-- ========================================================================== -->
    <!-- MODALS PANEL: CRUD FORMS -->
    <!-- ========================================================================== -->

    <!-- Modal: Add Blog Article -->
    <div class="modal fade" id="modalAddBlog" tabindex="-1" aria-labelledby="modalAddBlogLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
                <div class="modal-header border-bottom-0 pb-0" style="padding: 24px 24px 0 24px;">
                    <h5 class="modal-title fw-bold text-navy" id="modalAddBlogLabel"><i class="fa-solid fa-pen-nib text-danger me-2"></i> Write New Blog Article</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="dashboard.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add_blog">
                    <div class="modal-body" style="padding: 24px;">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label font-weight-bold small text-muted text-uppercase">Article Title</label>
                                <input type="text" class="form-control" name="title" required placeholder="e.g. Decoupling Legacy Systems with SAP S/4HANA">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label font-weight-bold small text-muted text-uppercase">Author Name</label>
                                <input type="text" class="form-control" name="author" value="LGS Editorial Team" placeholder="e.g. LGS Editorial Team">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label font-weight-bold small text-muted text-uppercase">Cover Image</label>
                                <input type="file" class="form-control" name="blog_image" accept="image/*">
                            </div>
                            <div class="col-12">
                                <label class="form-label font-weight-bold small text-muted text-uppercase">Short Summary (Used in cards)</label>
                                <textarea class="form-control" name="summary" rows="2" required placeholder="Provide a brief paragraph (1-2 sentences) summarizing the blog content..."></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label font-weight-bold small text-muted text-uppercase">Article Content</label>
                                <textarea class="form-control" name="content" id="blog_content" rows="10"></textarea>
                            </div>
                            
                            <!-- SEO Parameters -->
                            <div class="col-12 border-top pt-3 mt-4">
                                <h6 class="fw-bold text-navy mb-3"><i class="fa-solid fa-chart-line text-danger me-2"></i> Search Engine Optimization (SEO)</h6>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold small text-muted text-uppercase">SEO Meta Title</label>
                                <input type="text" class="form-control" name="seo_title" required placeholder="Target page title tag">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold small text-muted text-uppercase">SEO Meta Description</label>
                                <input type="text" class="form-control" name="meta_description" required placeholder="Target search snippet description (max 160 characters)">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0" style="padding: 0 24px 24px 24px;">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn btn-navy" style="border-radius: 8px;">Publish Article</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal: Edit Blog Article -->
    <div class="modal fade" id="modalEditBlog" tabindex="-1" aria-labelledby="modalEditBlogLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
                <div class="modal-header border-bottom-0 pb-0" style="padding: 24px 24px 0 24px;">
                    <h5 class="modal-title fw-bold text-navy" id="modalEditBlogLabel"><i class="fa-solid fa-pen-nib text-danger me-2"></i> Edit Blog Article</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="dashboard.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="edit_blog">
                    <input type="hidden" name="id" id="edit_blog_id">
                    <div class="modal-body" style="padding: 24px;">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label font-weight-bold small text-muted text-uppercase">Article Title</label>
                                <input type="text" class="form-control" name="title" id="edit_blog_title" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label font-weight-bold small text-muted text-uppercase">Author Name</label>
                                <input type="text" class="form-control" name="author" id="edit_blog_author" placeholder="e.g. LGS Editorial Team">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label font-weight-bold small text-muted text-uppercase">Cover Image</label>
                                <input type="file" class="form-control" name="blog_image" accept="image/*">
                                <div id="edit_blog_image_preview_container" class="mt-2 d-none">
                                    <span class="small text-muted d-block mb-1">Current Image Preview:</span>
                                    <img src="" id="edit_blog_image_preview" alt="" class="img-fluid rounded border" style="max-height: 80px; object-fit: cover;">
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label font-weight-bold small text-muted text-uppercase">Short Summary (Used in cards)</label>
                                <textarea class="form-control" name="summary" id="edit_blog_summary" rows="2" required></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label font-weight-bold small text-muted text-uppercase">Article Content</label>
                                <textarea class="form-control" name="content" id="edit_blog_content" rows="10"></textarea>
                            </div>
                            
                            <!-- SEO Parameters -->
                            <div class="col-12 border-top pt-3 mt-4">
                                <h6 class="fw-bold text-navy mb-3"><i class="fa-solid fa-chart-line text-danger me-2"></i> Search Engine Optimization (SEO)</h6>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold small text-muted text-uppercase">SEO Meta Title</label>
                                <input type="text" class="form-control" name="seo_title" id="edit_blog_seo_title" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold small text-muted text-uppercase">SEO Meta Description</label>
                                <input type="text" class="form-control" name="meta_description" id="edit_blog_meta_desc" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0" style="padding: 0 24px 24px 24px;">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn btn-navy" style="border-radius: 8px;">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal: Add Navbar Category -->
    <div class="modal fade" id="modalAddMenu" tabindex="-1" aria-labelledby="modalAddMenuLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
                <div class="modal-header border-bottom-0 pb-0" style="padding: 24px 24px 0 24px;">
                    <h5 class="modal-title fw-bold text-navy" id="modalAddMenuLabel"><i class="fa-solid fa-plus-circle me-2 text-danger"></i> Add Navbar Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="">
                    <input type="hidden" name="action" value="add_menu">
                    <div class="modal-body" style="padding: 24px;">
                        <div class="mb-3">
                            <label class="form-label font-weight-bold small text-muted">Category Title</label>
                            <input type="text" class="form-control" name="name" required placeholder="e.g. Services, About Us, Industries">
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold small text-muted">Sorting Weight</label>
                            <input type="number" class="form-control" name="sort_order" value="10" placeholder="e.g. 10, 20 (Lower weights float left)">
                            <div class="form-text text-muted small">Lower weight value items are sorted to display first in the navbar menus.</div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0" style="padding: 0 24px 24px 24px;">
                        <button type="button" class="btn btn-outline-secondary px-4 py-2" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn btn-brand-primary">Create Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal: Edit Navbar Category -->
    <div class="modal fade" id="modalEditMenu" tabindex="-1" aria-labelledby="modalEditMenuLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
                <div class="modal-header border-bottom-0 pb-0" style="padding: 24px 24px 0 24px;">
                    <h5 class="modal-title fw-bold text-navy" id="modalEditMenuLabel"><i class="fa-regular fa-pen-to-square me-2 text-danger"></i> Rename Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="">
                    <input type="hidden" name="action" value="edit_menu">
                    <input type="hidden" name="id" id="edit_menu_id">
                    <div class="modal-body" style="padding: 24px;">
                        <div class="mb-3">
                            <label class="form-label font-weight-bold small text-muted">Category Title</label>
                            <input type="text" class="form-control" name="name" id="edit_menu_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold small text-muted">Sorting Weight</label>
                            <input type="number" class="form-control" name="sort_order" id="edit_menu_sort" required>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0" style="padding: 0 24px 24px 24px;">
                        <button type="button" class="btn btn-outline-secondary px-4 py-2" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn btn-brand-primary">Apply Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    </div>

    <!-- Modal: Add Testimonial -->
    <div class="modal fade" id="modalAddTestimonial" tabindex="-1" aria-labelledby="modalAddTestimonialLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
                <div class="modal-header border-bottom-0 pb-0" style="padding: 24px 24px 0 24px;">
                    <h5 class="modal-title fw-bold text-navy" id="modalAddTestimonialLabel"><i class="fa-solid fa-plus-circle me-2 text-danger"></i> Add Client Testimonial</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add_testimonial">
                    <div class="modal-body" style="padding: 24px;">
                        <div class="mb-3">
                            <label class="form-label font-weight-bold small text-muted">Client Name</label>
                            <input type="text" class="form-control" name="client_name" required placeholder="e.g. John Doe">
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold small text-muted">Service Name / Designation</label>
                            <input type="text" class="form-control" name="service_name" required placeholder="e.g. Chief Technology Officer, SAP Core ERP">
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold small text-muted">Avatar Image</label>
                            <input type="file" class="form-control" name="testimonial_image" accept="image/*">
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold small text-muted">Testimonial Quote</label>
                            <textarea class="form-control" name="testimonial_text" rows="4" required placeholder="Paste client review quote..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold small text-muted">Sorting Order</label>
                            <input type="number" class="form-control" name="sort_order" value="10" placeholder="e.g. 10, 20">
                        </div>
                    </div>
                    <div class="modal-footer border-top-0" style="padding: 0 24px 24px 24px;">
                        <button type="button" class="btn btn-outline-secondary px-4 py-2" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn btn-brand-primary">Create Testimonial</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal: Edit Testimonial -->
    <div class="modal fade" id="modalEditTestimonial" tabindex="-1" aria-labelledby="modalEditTestimonialLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
                <div class="modal-header border-bottom-0 pb-0" style="padding: 24px 24px 0 24px;">
                    <h5 class="modal-title fw-bold text-navy" id="modalEditTestimonialLabel"><i class="fa-regular fa-pen-to-square me-2 text-danger"></i> Edit Testimonial</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="edit_testimonial">
                    <input type="hidden" name="id" id="edit_testimonial_id">
                    <div class="modal-body" style="padding: 24px;">
                        <div class="mb-3">
                            <label class="form-label font-weight-bold small text-muted">Client Name</label>
                            <input type="text" class="form-control" name="client_name" id="edit_testimonial_client" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold small text-muted">Service Name / Designation</label>
                            <input type="text" class="form-control" name="service_name" id="edit_testimonial_service" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold small text-muted">Avatar Image</label>
                            <input type="file" class="form-control" name="testimonial_image" accept="image/*">
                            <div id="edit_testimonial_image_preview_container" class="mt-2 d-none">
                                <span class="small text-muted d-block mb-1">Current Avatar Preview:</span>
                                <img src="" id="edit_testimonial_image_preview" alt="" class="img-fluid rounded border" style="width: 50px; height: 50px; object-fit: cover;">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold small text-muted">Testimonial Quote</label>
                            <textarea class="form-control" name="testimonial_text" id="edit_testimonial_text" rows="4" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold small text-muted">Sorting Order</label>
                            <input type="number" class="form-control" name="sort_order" id="edit_testimonial_sort" required>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0" style="padding: 0 24px 24px 24px;">
                        <button type="button" class="btn btn-outline-secondary px-4 py-2" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn btn-brand-primary">Apply Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal: Add Industry Sector -->
    <div class="modal fade" id="modalAddIndustry" tabindex="-1" aria-labelledby="modalAddIndustryLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
                <div class="modal-header border-bottom-0 pb-0" style="padding: 24px 24px 0 24px;">
                    <h5 class="modal-title fw-bold text-navy" id="modalAddIndustryLabel"><i class="fa-solid fa-plus-circle me-2 text-danger"></i> Add Industry Sector</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add_industry">
                    <div class="modal-body" style="padding: 24px;">
                        <div class="mb-3">
                            <label class="form-label font-weight-bold small text-muted">Sector Name (Used in pill buttons)</label>
                            <input type="text" class="form-control" name="name" required placeholder="e.g. Manufacturing, Energy">
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold small text-muted">Display Title (Tab Pane Heading)</label>
                            <input type="text" class="form-control" name="title" required placeholder="e.g. Manufacturing & Production">
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold small text-muted">Icon Class (FontAwesome)</label>
                            <input type="text" class="form-control" name="icon" value="fa-industry" required placeholder="e.g. fa-industry, fa-bolt, fa-truck">
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold small text-muted">Sector Illustration Image</label>
                            <input type="file" class="form-control" name="industry_image" accept="image/*">
                            <div class="form-text text-muted small">Upload an image to show on the homepage tab pane instead of the giant vector icon.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold small text-muted">Sector Description</label>
                            <textarea class="form-control" name="description" rows="4" required placeholder="Explain how LGS empowers this sector..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold small text-muted">Sector Pointers / Features (One per line, shown with red checkmarks)</label>
                            <textarea class="form-control" name="features" rows="4" placeholder="e.g. Optimize shop floor automation&#10;Implement OEE dashboards"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold small text-muted">Sorting Order</label>
                            <input type="number" class="form-control" name="sort_order" value="10" placeholder="e.g. 10, 20">
                        </div>
                    </div>
                    <div class="modal-footer border-top-0" style="padding: 0 24px 24px 24px;">
                        <button type="button" class="btn btn-outline-secondary px-4 py-2" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn btn-brand-primary">Create Sector</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal: Edit Industry Sector -->
    <div class="modal fade" id="modalEditIndustry" tabindex="-1" aria-labelledby="modalEditIndustryLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
                <div class="modal-header border-bottom-0 pb-0" style="padding: 24px 24px 0 24px;">
                    <h5 class="modal-title fw-bold text-navy" id="modalEditIndustryLabel"><i class="fa-regular fa-pen-to-square me-2 text-danger"></i> Edit Industry Sector</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="edit_industry">
                    <input type="hidden" name="id" id="edit_industry_id">
                    <div class="modal-body" style="padding: 24px;">
                        <div class="mb-3">
                            <label class="form-label font-weight-bold small text-muted">Sector Name (Used in pill buttons)</label>
                            <input type="text" class="form-control" name="name" id="edit_industry_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold small text-muted">Display Title (Tab Pane Heading)</label>
                            <input type="text" class="form-control" name="title" id="edit_industry_title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold small text-muted">Icon Class (FontAwesome)</label>
                            <input type="text" class="form-control" name="icon" id="edit_industry_icon" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold small text-muted">Sector Illustration Image</label>
                            <input type="file" class="form-control" name="industry_image" accept="image/*">
                            <div class="form-text text-muted small mb-2">Upload a new image to show on the homepage tab pane instead of the giant vector icon.</div>
                            <div id="edit_industry_image_preview_container" class="mt-2 d-none">
                                <span class="small text-muted d-block mb-1">Current Sector Image:</span>
                                <div class="d-flex align-items-center justify-content-between p-2 border rounded bg-light">
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="" id="edit_industry_image_preview" style="height: 40px; width: 60px; object-fit: cover; border-radius: 4px;">
                                        <span class="small text-muted">Active Image</span>
                                    </div>
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" id="clearIndustryImage" name="industry_image_clear" value="1">
                                        <label class="form-check-label small text-danger" for="clearIndustryImage">Remove image</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold small text-muted">Sector Description</label>
                            <textarea class="form-control" name="description" id="edit_industry_desc" rows="4" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold small text-muted">Sector Pointers / Features (One per line, shown with red checkmarks)</label>
                            <textarea class="form-control" name="features" id="edit_industry_features" rows="4"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold small text-muted">Sorting Order</label>
                            <input type="number" class="form-control" name="sort_order" id="edit_industry_sort" required>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0" style="padding: 0 24px 24px 24px;">
                        <button type="button" class="btn btn-outline-secondary px-4 py-2" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn btn-brand-primary">Apply Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal: Add Submenu / Service page -->
    <div class="modal fade" id="modalAddSubmenu" tabindex="-1" aria-labelledby="modalAddSubmenuLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
                <div class="modal-header border-bottom-0 pb-0" style="padding: 24px 24px 0 24px;">
                    <h5 class="modal-title fw-bold text-navy" id="modalAddSubmenuLabel"><i class="fa-solid fa-plus-circle me-2 text-danger"></i> Create New Dynamic Service Page</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add_submenu">
                    <div class="modal-body" style="padding: 24px; max-height: 70vh; overflow-y: auto;">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold small text-muted">Parent Navbar Category</label>
                                <select class="form-select" name="menu_id" required>
                                    <option value="">-- Choose Category --</option>
                                    <?php foreach ($menus as $m): ?>
                                        <option value="<?php echo $m['id']; ?>"><?php echo htmlspecialchars($m['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold small text-muted">Service Page Title</label>
                                <input type="text" class="form-control" name="name" required placeholder="e.g. SAP Analytics Cloud">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold small text-muted">URL service key (Unique Slug)</label>
                                <input type="text" class="form-control" name="service_key" placeholder="e.g. sap-analytics (Autogenerated if blank)">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold small text-muted">FontAwesome Icon Class</label>
                                <input type="text" class="form-control" name="icon" value="fa-cube" placeholder="e.g. fa-server, fa-chart-line, fa-globe">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold small text-muted">Service Page Illustration Image</label>
                                <input type="file" class="form-control" name="service_image" accept="image/*">
                                <div class="form-text text-muted small">Upload an image file. It will be stored in Cloudinary (or local fallback).</div>
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label font-weight-bold small text-muted">Banner Tagline / Hook Text</label>
                                <input type="text" class="form-control" name="tagline" required placeholder="A short high-impact description displayed in the page hero banner.">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold small text-muted">Banner Gradient Style</label>
                                <select class="form-select" name="banner_grad">
                                    <option value="linear-gradient(135deg, #0B356D 0%, #031D44 100%)">LGS Navy Default</option>
                                    <option value="linear-gradient(135deg, #0B356D 0%, #E31B23 100%)">Navy & Crimson glow</option>
                                    <option value="linear-gradient(135deg, #0B356D 0%, #1e3a8a 100%)">Deep Royal Blue</option>
                                    <option value="linear-gradient(135deg, #0B356D 0%, #10b981 100%)">Emerald Green Accent</option>
                                    <option value="linear-gradient(135deg, #0B356D 0%, #0284c7 100%)">Vibrant Sky Blue</option>
                                    <option value="linear-gradient(135deg, #0B356D 0%, #7c3aed 100%)">Neon Purple Accent</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold small text-muted">Sorting Weight in dropdown</label>
                                <input type="number" class="form-control" name="sort_order" value="10" placeholder="10">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold small text-muted">Paragraph Copy Description 1</label>
                                <textarea class="form-control" name="desc1" rows="4" required placeholder="First paragraph explaining service concept, business challenge, and background..."></textarea>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold small text-muted">Paragraph Copy Description 2</label>
                                <textarea class="form-control" name="desc2" rows="4" required placeholder="Second paragraph detailing LGS technical execution, resources capacity, and benefits..."></textarea>
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label font-weight-bold small text-muted">Key highlights checklist (One-per-line)</label>
                                <textarea class="form-control" name="features" rows="4" required placeholder="Feature Highlight Item 1&#10;Feature Highlight Item 2&#10;Feature Highlight Item 3"></textarea>
                                <div class="form-text text-muted small">Input each feature or capability highlight on a separate new line. It will be parsed into beautiful dynamic bullet checkmark rows on the live page.</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0" style="padding: 0 24px 24px 24px;">
                        <button type="button" class="btn btn-outline-secondary px-4 py-2" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn btn-brand-primary">Create Service Page</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal: Edit Submenu / Service page -->
    <div class="modal fade" id="modalEditSubmenu" tabindex="-1" aria-labelledby="modalEditSubmenuLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
                <div class="modal-header border-bottom-0 pb-0" style="padding: 24px 24px 0 24px;">
                    <h5 class="modal-title fw-bold text-navy" id="modalEditSubmenuLabel"><i class="fa-regular fa-pen-to-square me-2 text-danger"></i> Update Service Content</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="edit_submenu">
                    <input type="hidden" name="id" id="edit_sub_id">
                    <div class="modal-body" style="padding: 24px; max-height: 70vh; overflow-y: auto;">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold small text-muted">Parent Navbar Category</label>
                                <select class="form-select" name="menu_id" id="edit_sub_menu_id" required>
                                    <?php foreach ($menus as $m): ?>
                                        <option value="<?php echo $m['id']; ?>"><?php echo htmlspecialchars($m['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold small text-muted">Service Page Title</label>
                                <input type="text" class="form-control" name="name" id="edit_sub_name" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold small text-muted">URL service key (Unique Slug)</label>
                                <input type="text" class="form-control" name="service_key" id="edit_sub_key" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold small text-muted">FontAwesome Icon Class</label>
                                <input type="text" class="form-control" name="icon" id="edit_sub_icon" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold small text-muted">Service Page Illustration Image</label>
                                <input type="file" class="form-control" name="service_image" accept="image/*">
                                <div class="form-text text-muted small">Upload a new image to replace the current one. Leave blank to keep existing.</div>
                                <div id="edit_sub_image_preview_container" class="mt-2 d-none">
                                    <span class="small text-muted d-block mb-1">Current Image:</span>
                                    <img id="edit_sub_image_preview" src="" alt="" style="max-height: 50px; object-fit: contain;" class="rounded border">
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label font-weight-bold small text-muted">Banner Tagline / Hook Text</label>
                                <input type="text" class="form-control" name="tagline" id="edit_sub_tagline" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold small text-muted">Banner Gradient Style</label>
                                <input type="text" class="form-control" name="banner_grad" id="edit_sub_banner" placeholder="linear-gradient(...)">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold small text-muted">Sorting Weight in dropdown</label>
                                <input type="number" class="form-control" name="sort_order" id="edit_sub_sort" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold small text-muted">Paragraph Copy Description 1</label>
                                <textarea class="form-control" name="desc1" id="edit_sub_desc1" rows="5" required></textarea>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold small text-muted">Paragraph Copy Description 2</label>
                                <textarea class="form-control" name="desc2" id="edit_sub_desc2" rows="5" required></textarea>
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label font-weight-bold small text-muted">Key highlights checklist (One-per-line)</label>
                                <textarea class="form-control" name="features" id="edit_sub_features" rows="5" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0" style="padding: 0 24px 24px 24px;">
                        <button type="button" class="btn btn-outline-secondary px-4 py-2" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn btn-brand-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="../js/bootstrap.bundle.min.js"></script>
    
    <!-- Script Controller for Tabs switching & dynamic modal data prefilling -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            
            // 1. Sidebar Tab Switching System
            const triggers = document.querySelectorAll('.tab-trigger');
            const panels = document.querySelectorAll('.tab-panel-custom');
            
            // Read active tab parameter from URL query string if present (useful to keep tab active after form POST reloads)
            const urlParams = new URLSearchParams(window.location.search);
            let activeTabId = urlParams.get('active_tab') || localStorage.getItem('admin_active_tab') || 'panel-overview';
            
            // Verify active tab ID exists, otherwise fallback to overview
            if (!document.getElementById(activeTabId)) {
                activeTabId = 'panel-overview';
            }
            
            switchTab(activeTabId);
            
            triggers.forEach(trig => {
                trig.addEventListener('click', (e) => {
                    e.preventDefault();
                    const targetId = trig.getAttribute('data-target');
                    switchTab(targetId);
                });
            });
            
            function switchTab(targetId) {
                // Update Sidebar highlight
                triggers.forEach(t => {
                    if (t.getAttribute('data-target') === targetId) {
                        t.classList.add('active');
                    } else {
                        t.classList.remove('active');
                    }
                });
                
                // Update visible panel view
                panels.forEach(p => {
                    if (p.getAttribute('id') === targetId) {
                        p.classList.add('active');
                    } else {
                        p.classList.remove('active');
                    }
                });
                
                // Cache active tab locally
                localStorage.setItem('admin_active_tab', targetId);
            }
            
            // 2. Prefill Navbar Category edit modal
            const btnEditMenus = document.querySelectorAll('.btn-edit-menu');
            btnEditMenus.forEach(btn => {
                btn.addEventListener('click', () => {
                    document.getElementById('edit_menu_id').value = btn.getAttribute('data-id');
                    document.getElementById('edit_menu_name').value = btn.getAttribute('data-name');
                    document.getElementById('edit_menu_sort').value = btn.getAttribute('data-sort');
                });
            });
            
            // 3. Prefill Submenu/Service edit modal
            const btnEditSubmenus = document.querySelectorAll('.btn-edit-submenu');
            btnEditSubmenus.forEach(btn => {
                btn.addEventListener('click', () => {
                    document.getElementById('edit_sub_id').value = btn.getAttribute('data-id');
                    document.getElementById('edit_sub_menu_id').value = btn.getAttribute('data-menu_id');
                    document.getElementById('edit_sub_name').value = btn.getAttribute('data-name');
                    document.getElementById('edit_sub_key').value = btn.getAttribute('data-key');
                    document.getElementById('edit_sub_tagline').value = btn.getAttribute('data-tagline');
                    document.getElementById('edit_sub_icon').value = btn.getAttribute('data-icon');
                    document.getElementById('edit_sub_desc1').value = btn.getAttribute('data-desc1');
                    document.getElementById('edit_sub_desc2').value = btn.getAttribute('data-desc2');
                    document.getElementById('edit_sub_features').value = btn.getAttribute('data-features');
                    document.getElementById('edit_sub_banner').value = btn.getAttribute('data-banner');
                    document.getElementById('edit_sub_sort').value = btn.getAttribute('data-sort');
                    
                    const currentImg = btn.getAttribute('data-image');
                    const imgPreviewContainer = document.getElementById('edit_sub_image_preview_container');
                    const imgPreview = document.getElementById('edit_sub_image_preview');
                    if (currentImg && imgPreviewContainer && imgPreview) {
                        imgPreview.src = currentImg;
                        imgPreviewContainer.classList.remove('d-none');
                    } else if (imgPreviewContainer) {
                        imgPreviewContainer.classList.add('d-none');
                    }
                });
            });

            // 4. Prefill Blog edit modal & init Summernote
            const btnEditBlogs = document.querySelectorAll('.btn-edit-blog');
            btnEditBlogs.forEach(btn => {
                btn.addEventListener('click', () => {
                    document.getElementById('edit_blog_id').value = btn.getAttribute('data-id');
                    document.getElementById('edit_blog_title').value = btn.getAttribute('data-title');
                    document.getElementById('edit_blog_summary').value = btn.getAttribute('data-summary');
                    
                    const editorContent = btn.getAttribute('data-content');
                    if (typeof $('#edit_blog_content').summernote !== 'undefined') {
                        $('#edit_blog_content').summernote('code', editorContent);
                    } else {
                        document.getElementById('edit_blog_content').value = editorContent;
                    }
                    
                    document.getElementById('edit_blog_seo_title').value = btn.getAttribute('data-seo_title');
                    document.getElementById('edit_blog_meta_desc').value = btn.getAttribute('data-meta_desc');
                    document.getElementById('edit_blog_author').value = btn.getAttribute('data-author') || '';
                    
                    const currentImg = btn.getAttribute('data-image');
                    const imgPreviewContainer = document.getElementById('edit_blog_image_preview_container');
                    const imgPreview = document.getElementById('edit_blog_image_preview');
                    if (currentImg && imgPreviewContainer && imgPreview) {
                        imgPreview.src = currentImg;
                        imgPreviewContainer.classList.remove('d-none');
                    } else if (imgPreviewContainer) {
                        imgPreviewContainer.classList.add('d-none');
                    }
                });
            });

            // 5. Prefill Testimonial edit modal
            const btnEditTestimonials = document.querySelectorAll('.btn-edit-testimonial');
            btnEditTestimonials.forEach(btn => {
                btn.addEventListener('click', () => {
                    document.getElementById('edit_testimonial_id').value = btn.getAttribute('data-id');
                    document.getElementById('edit_testimonial_client').value = btn.getAttribute('data-client');
                    document.getElementById('edit_testimonial_service').value = btn.getAttribute('data-service');
                    document.getElementById('edit_testimonial_text').value = btn.getAttribute('data-text');
                    document.getElementById('edit_testimonial_sort').value = btn.getAttribute('data-sort');
                    
                    const currentImg = btn.getAttribute('data-image');
                    const imgPreviewContainer = document.getElementById('edit_testimonial_image_preview_container');
                    const imgPreview = document.getElementById('edit_testimonial_image_preview');
                    if (currentImg && imgPreviewContainer && imgPreview) {
                        imgPreview.src = currentImg.startsWith('http') ? currentImg : '../' + currentImg.replace(/^\.\.\//, '');
                        imgPreviewContainer.classList.remove('d-none');
                    } else if (imgPreviewContainer) {
                        imgPreviewContainer.classList.add('d-none');
                    }
                });
            });

            // 6. Prefill Industry edit modal
            const btnEditIndustries = document.querySelectorAll('.btn-edit-industry');
            btnEditIndustries.forEach(btn => {
                btn.addEventListener('click', () => {
                    document.getElementById('edit_industry_id').value = btn.getAttribute('data-id');
                    document.getElementById('edit_industry_name').value = btn.getAttribute('data-name');
                    document.getElementById('edit_industry_title').value = btn.getAttribute('data-title');
                    document.getElementById('edit_industry_desc').value = btn.getAttribute('data-desc');
                    document.getElementById('edit_industry_features').value = btn.getAttribute('data-features') || '';
                    document.getElementById('edit_industry_icon').value = btn.getAttribute('data-icon');
                    document.getElementById('edit_industry_sort').value = btn.getAttribute('data-sort');
                    
                    const currentImg = btn.getAttribute('data-image');
                    const imgPreviewContainer = document.getElementById('edit_industry_image_preview_container');
                    const imgPreview = document.getElementById('edit_industry_image_preview');
                    if (currentImg && imgPreviewContainer && imgPreview) {
                        imgPreview.src = currentImg.startsWith('http') ? currentImg : '../' + currentImg.replace(/^\.\.\//, '');
                        imgPreviewContainer.classList.remove('d-none');
                    } else if (imgPreviewContainer) {
                        imgPreviewContainer.classList.add('d-none');
                    }
                    const clearCheckbox = document.getElementById('clearIndustryImage');
                    if (clearCheckbox) clearCheckbox.checked = false;
                });
            });

            // Initialize Summernote Text Editor
            if (typeof $.fn.summernote !== 'undefined') {
                $('#blog_content').summernote({
                    placeholder: 'Write your article here...',
                    tabsize: 2,
                    height: 250,
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'underline', 'clear']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['table', ['table']],
                        ['insert', ['link', 'picture', 'video']],
                        ['view', ['fullscreen', 'codeview', 'help']]
                    ]
                });
                
                $('#edit_blog_content').summernote({
                    placeholder: 'Write your article here...',
                    tabsize: 2,
                    height: 250,
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'underline', 'clear']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['table', ['table']],
                        ['insert', ['link', 'picture', 'video']],
                        ['view', ['fullscreen', 'codeview', 'help']]
                    ]
                });
            }
        });
    </script>
</body>
</html>
