<?php

class User
{
    private $db, $data, $session_name, $isLoggedIn, $cookieName;

    public function __construct($user = null)
    {
        $this->db = Database::getInstance();
        $this->session_name = Config::get('session.user_session');
        $this->cookieName = Config::get('cookie.cookie_name');

        if (!$user) {
            if (Session::exists($this->session_name)) {
               $user = Session::get($this->session_name);

               if ($this->find($user)) {
                   $this->isLoggedIn = true;
               } 
            }
        } else {
            $this->find($user);
        }
    }

    public function create($fields = []){
        $this->db->insert('test', $fields);
    }

    public function login($email = null, $password = null, $remember = false){
        if (!$email and !$password and $this->exists()) {
            Session::put($this->session_name, $this->data()->id);
        } else {
            $user = $this->find($email);
            if ($user) {
                if (password_verify($password, $this->data()->password)){
                    Session::put($this->session_name, $this->data()->id);

                    if ($remember) {
                        $hash = hash('sha256', uniqid());

                        $hashCheck = $this->db->get('user_sessions', ['user_id', '=', $this->data()->id]);

                        if (!$hashCheck->count()) {
                            $this->db->insert('user_sessions', [
                                'user_id' => $this->data()->id,
                                'hash' => $hash
                            ]);
                        } else {
                            $hash = $hashCheck->first()->hash;
                        }

                        Cookie::put($this->cookieName, $hash, Config::get('cookie.cookie_expiry'));
                    }

                    return true;
                }
            } 

            return false;
        }
    }

    public function find($value = null){
        if (is_numeric($value)) {
            $this->data = $this->db->get('test', ['id', '=', $value])->first();
        } else {
            $this->data = $this->db->get('test', ['email', '=', $value])->first();
        }
        
        if ($this->data) {
            return true;
        }
        return false;
    }

    public function data(){
        return $this->data;
    }

    public function isLoggedIn(){
        return $this->isLoggedIn;
    }

    public function logout(){
        $this->db->delete('user_sessions', ['user_id', '=', $this->data()->id]);
        Session::delete($this->session_name);
        Cookie::delete($this->cookieName);
    }

    public function exists(){
        return (!empty($this->data())) ? true : false;
    }

    public function update($fields = [], $id = null){
        
        if (!$id and $this->isLoggedIn()) {
            $id = $this->data()->id;
        }

        $this->db->update('test', $id, $fields);
    }

    public function hasPermissions($key = null){

        if ($key) {
            $group = $this->db->get('groups', ['id', '=', $this->data()->group_id]);

            if ($group->count()) {
                $permissions = $group->first()->permissions;
                $permissions = json_decode($permissions, true);
    
                if (!empty($permissions[$key])) {
                    return true;
                }
            }
        }

        return false;
    }
}
