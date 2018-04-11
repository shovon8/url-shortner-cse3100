<?php
final class User {
    private $userName;
    private $userId;
    private $userEmail;
    private $dateJoined;
    private $password = null;
    private $mysqli = null;

    // constants for determining different states
    const CREATE_SUCCESS                    =   2;
    const CREATE_FAILED                     =   4;
    const CREATE_FAILED_DUPLICATE_EMAIL     =   8;
    const LOGIN_SUCCESS                     =   16;
    const LOGIN_FAILED                      =   32;
    const UPDATE_SUCCESS                    =   64;
    const UPDATE_FAILED                     =   128;


    // private constructor, user's wont be able to create User object without authenticating
    private function __construct($id, $name, $email, $dateJoined) {
        $this->userId      = $id;
        $this->userName    = $name;
        $this->userEmail   = $email;
        $this->dateJoined  = $dateJoined;
    }


    // getters
    public function getId() {
        return $this->userId;
    }

    public function getName() {
        return $this->userName;
    }

    public function getEmail() {
        return $this->userEmail;
    }

    public function getDateJoined() {
        return $this->dateJoined;
    }


    // private setter for database handler
    private function setDatabaseHandler($mysqli) {
        $this->mysqli = $mysqli;
    }


    // public setters
    public function setName($name) {
        $this->userName = $name;
    }

    public function changePassword($password) {
        $this->password = $password;
    }



    // save changes to the database
    public function save() {
        if($this->password) {
            $sql = 'UPDATE users SET user_name=?, password=MD5(?) WHERE user_id=?';
        } else {
            $sql = 'UPDATE users SET user_name=? WHERE user_id=?';
        }

        if($stmt = $this->mysqli->prepare($sql)) {
            if($this->password) {
                $stmt->bind_param('ssi', $this->userName, $this->password, $this->userId);
            } else {
                $stmt->bind_param('si', $this->userName, $this->userId);
            }

            $stmt->execute();

            if($stmt->affected_rows === 1) {
                $stmt->close();

                return User::UPDATE_SUCCESS;
            }

            $stmt->close();
        }

        return User::UPDATE_FAILED;
    }








    // static function for authenticating users
    public static function auth($email, $password, &$userObject) {
        global $mysqli;

        if($stmt = $mysqli->prepare('SELECT user_id, user_email, user_name, date_joined FROM users WHERE user_email=? AND password=MD5(?)')) {
            $stmt->bind_param('ss', $email, $password);
            $stmt->execute();
            $stmt->bind_result($userId, $userEmail, $userName, $dateJoined);
            $stmt->fetch();

            if(isset($userId) && $userId > 1) {
                $stmt->close();
                $userObject = new User($userId, $userName, $userEmail, $dateJoined);
                $userObject->setDatabaseHandler($mysqli);
                return User::LOGIN_SUCCESS;
            }
        }

        return User::LOGIN_FAILED;
    }



    // static function for creating new users
    public static function create($email, $password, $name) {
        global $mysqli;

        if($stmt = $mysqli->prepare('INSERT INTO users(user_email, user_name, password) VALUES(?, ?, MD5(?))')) {
            $stmt->bind_param('sss', $email, $name, $password);
            $stmt->execute();

            if($stmt->affected_rows === 1) {
                $stmt->close();

                return User::CREATE_SUCCESS;
            }

            $stmt->close();
            return User::CREATE_FAILED_DUPLICATE_EMAIL;
        }


        return User::CREATE_FAILED;
    }
}


