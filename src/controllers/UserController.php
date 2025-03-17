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
            $firstName = trim($_POST['first_name'] ?? '');
            $lastName = trim($_POST['last_name'] ?? '');
            
            $errors = [];
            
            // Validace vstupu
            if (empty($username)) {
                $errors['username'] = 'Uživatelské jméno je povinné';
            } elseif ($this->userModel->usernameExists($username)) {
                $errors['username'] = 'Uživatelské jméno již existuje';
            }
            
            if (empty($email)) {
                $errors['email'] = 'Email je povinný';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Neplatný email';
            } elseif ($this->userModel->emailExists($email)) {
                $errors['email'] = 'Email již existuje';
            }
            
            if (empty($password)) {
                $errors['password'] = 'Heslo je povinné';
            } elseif (strlen($password) < 6) {
                $errors['password'] = 'Heslo musí mít alespoň 6 znaků';
            }
            
            if ($password !== $passwordConfirm) {
                $errors['password_confirm'] = 'Hesla se neshodují';
            }
            
            if (empty($errors)) {
                $userId = $this->userModel->register($username, $email, $password, $firstName, $lastName);
                
                if ($userId) {
                    $_SESSION['user_id'] = $userId;
                    $_SESSION['username'] = $username;
                    
                    header('Location: index.php?page=dashboard');
                    exit;
                } else {
                    $errors['general'] = 'Nastala chyba při registraci, zkuste to znovu';
                }
            }
            
            // Pokud došlo k chybě, uložíme chyby a data do session
            $_SESSION['register_errors'] = $errors;
            $_SESSION['register_data'] = [
                'username' => $username,
                'email' => $email,
                'first_name' => $firstName,
                'last_name' => $lastName
            ];
            
            header('Location: index.php?page=register');
            exit;
        }
    }
    
    // Zpracování přihlášení uživatele
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            
            $errors = [];
            
            if (empty($email)) {
                $errors['email'] = 'Email je povinný';
            }
            
            if (empty($password)) {
                $errors['password'] = 'Heslo je povinné';
            }
            
            if (empty($errors)) {
                $user = $this->userModel->login($email, $password);
                
                if ($user) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    
                    header('Location: index.php?page=dashboard');
                    exit;
                } else {
                    $errors['general'] = 'Neplatný email nebo heslo';
                }
            }
            
            // Pokud došlo k chybě, uložíme chyby a email do session
            $_SESSION['login_errors'] = $errors;
            $_SESSION['login_email'] = $email;
            
            header('Location: index.php?page=login');
            exit;
        }
    }
    
    // Odhlášení uživatele
    public function logout() {
        session_start();
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
            $firstName = trim($_POST['first_name'] ?? '');
            $lastName = trim($_POST['last_name'] ?? '');
            $height = !empty($_POST['height']) ? (float)$_POST['height'] : null;
            $weight = !empty($_POST['weight']) ? (float)$_POST['weight'] : null;
            $dateOfBirth = !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null;
            $password = $_POST['password'] ?? '';
            $passwordConfirm = $_POST['password_confirm'] ?? '';
            
            $data = [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'height' => $height,
                'weight' => $weight,
                'date_of_birth' => $dateOfBirth
            ];
            
            $errors = [];
            
            // Validace nového hesla, pokud bylo zadáno
            if (!empty($password)) {
                if (strlen($password) < 6) {
                    $errors['password'] = 'Heslo musí mít alespoň 6 znaků';
                } elseif ($password !== $passwordConfirm) {
                    $errors['password_confirm'] = 'Hesla se neshodují';
                } else {
                    $data['password'] = $password;
                }
            }
            
            // Zpracování nahraného obrázku
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                $targetDir = 'public/images/profiles/';
                
                // Vytvoření složky, pokud neexistuje
                if (!file_exists($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }
                
                $fileName = basename($_FILES['profile_image']['name']);
                $targetFile = $targetDir . $userId . '_' . $fileName;
                $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
                
                // Kontrola, zda je soubor obrázek
                $check = getimagesize($_FILES['profile_image']['tmp_name']);
                if ($check === false) {
                    $errors['profile_image'] = 'Soubor není obrázek';
                }
                
                // Kontrola velikosti (max 2MB)
                elseif ($_FILES['profile_image']['size'] > 2000000) {
                    $errors['profile_image'] = 'Soubor je příliš velký (max 2MB)';
                }
                
                // Povolené typy souborů
                elseif (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                    $errors['profile_image'] = 'Jsou povoleny pouze JPG, JPEG, PNG a GIF soubory';
                }
                
                // Nahrání souboru
                elseif (empty($errors)) {
                    if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetFile)) {
                        $this->userModel->uploadProfileImage($userId, $targetFile);
                    } else {
                        $errors['profile_image'] = 'Nastala chyba při nahrávání souboru';
                    }
                }
            }
            
            if (empty($errors)) {
                $result = $this->userModel->updateProfile($userId, $data);
                
                if ($result) {
                    $_SESSION['profile_updated'] = true;
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
        
        include 'src/views/profile.php';
    }
}
?> 