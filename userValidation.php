<?php

class UserValidator {
  private $data;
  private $errors = [];
  private $fields;
  private $type;

  public function __construct($post_data, $type) {
    $this->data = $post_data;
    $this->type = $type;
    if($type === 'login') 
      $this->fields = ['username', 'password'];
    elseif($type === 'register')
      $this->fields = ['fullname', 'username', 'email', 'password'];
    else
      trigger_error("invalid user type");
  }

  public function validateForm() {
    foreach($this->fields as $field) {
      if(!array_key_exists($field, $this->data)) {
        trigger_error("$field not present");
        return;
      }
    }

    $this->validateUsername();
    $this->validatePassword();
    if($this->type === 'register') {
      $this->validateFullname();   
      $this->validateEmail();
    }
    
    return $this->errors;
  }

  private function validateUsername() {
    $val = trim($this->data['username']);
    if(empty($val)) {
      $this->addError('username', 'User Name cannot be empty!');
    } else {
      if(!preg_match('/^[a-zA-Z0-9]{5,15}$/', $val)) {
        $this->addError('username', 'User Name must have 5-15 characters and have letters and numbers only!');
      }
    }
  }

  private function validatePassword() {
    $val = $this->data['password'];
    if(empty($val)) {
      $this->addError('password', 'Password cannot be empty!');
    } else {
      if(!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[@$!%*?&])[A-Za-z0-9@$!%*?&]{8,}$/', $val)) {
        $this->addError('password', 'Password must be minimum 8 characters long and have at least one uppercase letter, one lowercase letter, one number digit and one special character!');
      }
    }
  }

  private function validateFullname() {
    $val = trim($this->data['fullname']);
    if(empty($val)) {
      $this->addError('fullname', 'Full Name cannot be empty!');
    } else {
      if(!preg_match('/^[a-zA-Z\s]*$/', $val)) {
        $this->addError('fullname', 'Full Name must have only letters and spaces!');
      }
    }
  }

  private function validateEmail() {
    $val = trim($this->data['email']);
    if(empty($val)) {
      $this->addError('email', 'Email cannot be empty!');
    } else {
      if(!filter_var($val, FILTER_VALIDATE_EMAIL)) {
        $this->addError('email', 'Email must be a valid email!');
      }
    }
  }

  private function addError($key, $message) {
    $this->errors[$key] = $message;
  }

}

?>