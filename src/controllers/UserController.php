<?php
class UserController {
    private $userModel;
    
    public function __construct($userModel) {
        $this->userModel = $userModel;
    }
    
    // Zpracování registrace uživatele
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $passwordConfirm = $_POST['password_confirm'] ?? '';
            $gender = $_POST['gender'] ?? '';
            $terms = isset($_POST['terms']) ? true : false;
            
            // Debug - log the POST data
            error_log("Register POST data: " . print_r($_POST, true));
            
            $errors = [];
            
            // Validace vstupu
            if (empty($email)) {
                $errors['email'] = 'Email je povinný';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Neplatný email';
            } elseif ($this->userModel->emailExists($email)) {
                $errors['email'] = 'Účet s tímto emailem již existuje';
            }

            if (empty($username)) {
                $errors['username'] = 'Uživatelské jméno je povinné';
            } elseif ($this->userModel->usernameExists($email)) {
                $errors['username'] = 'Toto uživatelské jméno je již zabrané';
            }
            
            if (empty($password)) {
                $errors['password'] = 'Heslo je povinné';
            } elseif (strlen($password) < 6) {
                $errors['password'] = 'Heslo musí mít alespoň 6 znaků';
            }
            
            if ($password !== $passwordConfirm) {
                $errors['password_confirm'] = 'Hesla se neshodují';
            }
            
            if (empty($gender)) {
                $errors['gender'] = 'Pohlaví je povinné';
            }
            
            if (!$terms) {
                $errors['terms'] = 'Musíte souhlasit s podmínkami';
            }
            
            if (empty($errors)) {
                
                // Debug - log the data being passed to the model
                error_log("Attempting to register user: $username, $email, [password], $gender");
                
                $userId = $this->userModel->register($username, $email, $password, $gender);
                
                if ($userId) {
                    $_SESSION['user_id'] = $userId;
                    $_SESSION['username'] = $username;
                    
                    header('Location: index.php?page=dashboard');
                    exit;
                } else {
                    $errors['general'] = 'Nastala chyba při registraci, zkuste to znovu';
                    error_log("Registration failed. Last error: " . implode(', ', $this->userModel->getLastError()));

                    // Uložíme chyby a data do session
                    $_SESSION['register_errors'] = $errors;
                    $_SESSION['register_form_data'] = [
                        'username' =>$username,
                        'email' => $email,
                        'gender' => $gender,
                        'terms' => $terms
                    ];
                    
                    header('Location: index.php?page=register');
                    exit;
                }
            }else{
                // Uložíme chyby a data do session
                $_SESSION['register_errors'] = $errors;
                $_SESSION['register_form_data'] = [
                    'username' => $username,
                    'email' => $email,
                    'gender' => $gender,
                    'terms' => $terms
                ];
        
                header('Location: index.php?page=register');
                exit;
            }
        }
    }
    
    // Zpracování přihlášení uživatele
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $remember = isset($_POST['remember']) ? true : false;
            
            // Debug - log the POST data
            error_log("Login POST data: " . print_r($_POST, true));
            
            $errors = [];
            
            // Validace vstupu
            if (empty($email)) {
                $errors['email'] = 'Email je povinný';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Neplatný email';
            }
            
            if (empty($password)) {
                $errors['password'] = 'Heslo je povinné';
            }
            
            if (empty($errors)) {
                // Debug - log the data being passed to the model
                error_log("Attempting to login user: $email");
                
                $user = $this->userModel->login($email, $password);
                
                if ($user) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['user_type'] = $user['user_type'];
                    
                    /* Pokud uživatel zaškrtl "Zapamatovat si mě" (Kdyžtak dodělat)
                    if ($remember) {
                        // Vytvoření bezpečného tokenu
                        $token = bin2hex(random_bytes(32));
                        $expires = time() + (30 * 24 * 60 * 60); // 30 dní
                        
                        // Uložení tokenu do databáze (předpokládá existenci tabulky remember_tokens)
                        $sql = "INSERT INTO remember_tokens (user_id, token, expires_at) 
                                VALUES (:user_id, :token, :expires)";
                        $stmt = $this->pdo->prepare($sql);
                        $stmt->execute([
                            ':user_id' => $user['id'],
                            ':token' => $token,
                            ':expires' => date('Y-m-d H:i:s', $expires)
                        ]);
                        
                        // Nastavení cookie
                        setcookie('remember_token', $token, $expires, '/', '', true, true);
                    }*/
                    
                    header('Location: index.php?page=dashboard');
                    exit;
                } else {
                    $errors['general'] = 'Neplatný email nebo heslo';
                    error_log("Login failed for email: $email");
                    
                    // Uložíme chyby a email do session
                    $_SESSION['login_errors'] = $errors;
                    $_SESSION['login_form_data'] = [
                        'email' => $email,
                        'remember' => $remember
                    ];
                    
                    header('Location: index.php?page=login');
                    exit;
                }
            } else {
                // Uložíme chyby a data do session
                $_SESSION['login_errors'] = $errors;
                $_SESSION['login_email'] = $email;
                
                header('Location: index.php?page=login');
                exit;
            }
        }
    }
    
    // Odhlášení uživatele
    public function logout() {
        session_unset();
        session_destroy();
        
        header('Location: index.php');
        exit;
    }
    
    // Zpracování aktualizace profilu
    public function updateProfile() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user_id'];
            $height = !empty($_POST['height']) ? (float)$_POST['height'] : null;
            $weight = !empty($_POST['weight']) ? (float)$_POST['weight'] : null;
            $dateOfBirth = !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null;
            $email = !empty($_POST['email']) ? $_POST['email'] : null;
            $username = !empty($_POST['username']) ? $_POST['username'] : $_SESSION['username'];
            $password = $_POST['current_password'] ?? '';
            $passwordNew = $_POST['new_password'] ?? '';
            $passwordConfirm = $_POST['confirm_password'] ?? '';
            
            $data = [
                'height' => $height,
                'weight' => $weight,
                'date_of_birth' => $dateOfBirth
            ];
            
            $errors = [];
            
            // Validace nového hesla, pokud bylo zadáno
            if (!empty($passwordNew)) {
                if (strlen($passwordNew) < 6) {
                    $errors['password'] = 'Heslo musí mít alespoň 6 znaků';
                } elseif ($password !== $passwordConfirm) {
                    $errors['password_confirm'] = 'Hesla se neshodují';
                } else {
                    $data['passwordNew'] = $password;
                }
            }

            if ($username != $_SESSION["username"]) {
                if (empty($username)) {
                    $errors['username'] = 'Uživatelské jméno je povinné';
                } elseif ($this->userModel->usernameExists($username)) {
                    $errors['username'] = 'Toto jmeno už někdo používá';
                } else {
                    $data['username'] = $username;
                }
            }

            if ($email != $this->userModel->getById($userId)['email']) {
                if (empty($email)) {
                    $errors['email'] = 'Email je povinný';
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors['email'] = 'Nový email je neplatný';
                } elseif ($this->userModel->emailExists($email)) {
                    $errors['email'] = 'Tento email už někdo používá';
                } else {
                    $data['email'] = $email;
                }
            }
            
            if (empty($errors)) {
                $result = $this->userModel->updateProfile($userId, $data);
                
                if ($result) {
                    $_SESSION['profile_updated'] = true;
                    $_SESSION['username'] = $username;
                    header('Location: index.php?page=profile');
                    exit;
                } else {
                    $errors['general'] = 'Nastala chyba při aktualizaci profilu';
                }
            }
            
            // Pokud došlo k chybě, uložíme chyby do session
            $_SESSION['profile_errors'] = $errors;
            header('Location: index.php?page=profile');
            exit;
        }
    }
    
    // Zobrazení profilu
    public function showProfile() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        $user = $this->userModel->getById($userId);
        
        include __DIR__ . '/../views/profile.php';
    }
}
?> 