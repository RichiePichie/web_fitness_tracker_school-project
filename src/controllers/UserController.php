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
            
            // Validace uživatelského jména
            if (empty($username)) {
                $errors['username'] = 'Uživatelské jméno je povinné';
            } elseif (strlen($username) > 50) {
                $errors['username'] = 'Uživatelské jméno může mít maximálně 50 znaků';
            } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
                $errors['username'] = 'Uživatelské jméno může obsahovat pouze písmena, čísla a podtržítko';
            } elseif ($this->userModel->usernameExists($username)) {
                $errors['username'] = 'Toto uživatelské jméno je již zabrané';
            }

            // Validace emailu
            if (empty($email)) {
                $errors['email'] = 'Email je povinný';
            } elseif (strlen($email) > 100) {
                $errors['email'] = 'Email může mít maximálně 100 znaků';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Neplatný formát emailu';
            } elseif ($this->userModel->emailExists($email)) {
                $errors['email'] = 'Účet s tímto emailem již existuje';
            }
            
            // Validace hesla
            if (empty($password)) {
                $errors['password'] = 'Heslo je povinné';
            } elseif (strlen($password) < 6) {
                $errors['password'] = 'Heslo musí mít alespoň 6 znaků';
            } elseif (strlen($password) > 255) {
                $errors['password'] = 'Heslo je příliš dlouhé';
            }
            
            // Validace potvrzení hesla
            if ($password !== $passwordConfirm) {
                $errors['password_confirm'] = 'Hesla se neshodují';
            }
            
            // Validace pohlaví
            if (empty($gender)) {
                $errors['gender'] = 'Pohlaví je povinné';
            } elseif (!in_array($gender, ['male', 'female', 'other'])) {
                $errors['gender'] = 'Neplatná hodnota pohlaví';
            }
            
            // Validace souhlasu s podmínkami
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
            
            $errors = [];
            
            // Validace emailu
            if (empty($email)) {
                $errors['email'] = 'Email je povinný';
            } elseif (strlen($email) > 100) {
                $errors['email'] = 'Email může mít maximálně 100 znaků';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Neplatný formát emailu';
            }
            
            // Validace hesla
            if (empty($password)) {
                $errors['password'] = 'Heslo je povinné';
            } elseif (strlen($password) > 255) {
                $errors['password'] = 'Heslo je příliš dlouhé';
            }
            
            if (empty($errors)) {
                $user = $this->userModel->login($email, $password);
                
                if ($user) {
                    // Vyčištění session před nastavením nových dat
                    unset($_SESSION['login_errors']);
                    unset($_SESSION['login_form_data']);
                    unset($_SESSION['login_email']);
                    
                    // Nastavení session dat
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['user_type'] = $user['user_type'];
                    
                    header('Location: index.php?page=dashboard');
                    exit;
                } else {
                    $errors['general'] = 'Neplatný email nebo heslo';
                    error_log("Login failed for email: $email");
                    
                    // Uložení chyb a dat do session
                    $_SESSION['login_errors'] = $errors;
                    $_SESSION['login_form_data'] = [
                        'email' => $email,
                        'remember' => $remember
                    ];
                    
                    header('Location: index.php?page=login');
                    exit;
                }
            } else {
                // Uložení chyb a dat do session
                $_SESSION['login_errors'] = $errors;
                $_SESSION['login_form_data'] = $email;
                
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
            
            $user = $this->userModel->getById($userId);

            $errors = [];

            // Validace uživatelského jména
            if ($username != $_SESSION["username"]) {
                if (empty($username)) {
                    $errors['username'] = 'Uživatelské jméno je povinné';
                } elseif (strlen($username) > 50) {
                    $errors['username'] = 'Uživatelské jméno může mít maximálně 50 znaků';
                } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
                    $errors['username'] = 'Uživatelské jméno může obsahovat pouze písmena, čísla a podtržítko';
                } elseif ($this->userModel->usernameExists($username)) {
                    $errors['username'] = 'Toto jméno už někdo používá';
                } else {
                    $data['username'] = $username;
                }
            }

            // Validace emailu
            if ($email != $user['email']) {
                if (empty($email)) {
                    $errors['email'] = 'Email je povinný';
                } elseif (strlen($email) > 100) {
                    $errors['email'] = 'Email může mít maximálně 100 znaků';
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors['email'] = 'Neplatný formát emailu';
                } elseif ($this->userModel->emailExists($email)) {
                    $errors['email'] = 'Tento email už někdo používá';
                } else {
                    $data['email'] = $email;
                }
            }

            if(!empty($height)) {
                if(!is_numeric($height)) {
                    $errors['height'] = 'Výška musí být číslo';
                } elseif($height < 50 || $height > 250) {
                    $errors['height'] = 'Výška musí být mezi 50 a 250 cm';
                } elseif($user['height'] != $height){
                    $height = round($height, 2); // DECIMAL(5,2) v databázi
                    $data['height'] = $height;
                }
            }

            if(!empty($weight)) {
                if(!is_numeric($weight)) {
                    $errors['weight'] = 'Váha musí být číslo';
                } elseif($weight < 20 || $weight > 500) {
                    $errors['weight'] = 'Váha musí být mezi 20 a 500 kg';
                } elseif($user['weight'] != $weight){
                    $weight = round($weight, 2); // DECIMAL(5,2) v databázi
                    $data['weight'] = $weight;
                }
            }

            if(!empty($dateOfBirth)) {
                $date = DateTime::createFromFormat('Y-m-d', $dateOfBirth);
                
                if(!$date) {
                    $errors['date_of_birth'] = 'Datum narození musí být ve formátu RRRR-MM-DD';
                } else {
                    $dnes = new DateTime();
                    $dnes->setTime(0, 0, 0);
                    $date->setTime(0, 0, 0);
                    $vek = $dnes->diff($date)->y;

                    if($date > $dnes) {
                        $errors['date_of_birth'] = 'Datum narození nesmí být v budoucnosti';
                    } elseif($vek < 13) {
                        $errors['date_of_birth'] = 'Musíte být starší 13 let';
                    } elseif($vek > 120) {
                        $errors['date_of_birth'] = 'Zadejte prosím platné datum narození';
                    } elseif($user['date_of_birth'] != $dateOfBirth){
                        $data['date_of_birth'] = $dateOfBirth;
                    }
                }
            }

            if (!empty($passwordNew) || !empty($password) || !empty($passwordConfirm)) {
                if (empty($password)) {
                    $errors['current_password'] = 'Pro změnu hesla musíte zadat současné heslo';
                } elseif (empty($passwordNew)) {
                    $errors['new_password'] = 'Zadejte nové heslo';
                } elseif (empty($passwordConfirm)) {
                    $errors['confirm_password'] = 'Potvrďte nové heslo';
                } else {
                    // Ověření původního hesla pomocí login metody
                    $userCheck = $this->userModel->login($user['email'], $password);
                    
                    if (!$userCheck) {
                        $errors['current_password'] = 'Nesprávné současné heslo';
                    } else {
                        if (strlen($passwordNew) < 6) {
                            $errors['new_password'] = 'Heslo musí mít alespoň 6 znaků';
                        } elseif (strlen($passwordNew) > 255) {
                            $errors['new_password'] = 'Heslo je příliš dlouhé';
                        } elseif ($passwordNew === $password) {
                            $errors['new_password'] = 'Nové heslo musí být jiné než současné heslo';
                        } elseif ($passwordNew !== $passwordConfirm) {
                            $errors['confirm_password'] = 'Hesla se neshodují';
                        } else {
                            $data['password'] = $passwordNew;
                        }
                    }
                }
            }
            
            if (empty($errors)) {
                if(!empty($data)) {
                    $result = $this->userModel->updateProfile($userId, $data);
                    unset($_SESSION['profile_errors']);

                    if ($result) {
                        $_SESSION['profile_updated'] = true;
                        $_SESSION['username'] = $username;
                        header('Location: index.php?page=profile');
                        exit;
                    } else {
                        $errors['general'] = 'Nastala chyba při aktualizaci profilu';
                    }
                }
            }else{
                $_SESSION['profile_errors'] = $errors;
            }
                       
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