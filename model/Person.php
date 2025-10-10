<?php 
class Person {
	protected $userID;
	protected $firstname;
	protected $midint;
	protected $lastname;
	protected $email;
	protected $username;
	protected $password;
	protected $role;
	protected $status;
	protected $date_created;

	public function __construct($userID, $firstname, $midint, $lastname, $email, $username, $password, $role, $status, $date_created) {
		$this->userID = $userID;
		$this->firstname = $firstname;
		$this->midint = $midint;
		$this->lastname = $lastname;
		$this->email = $email;
		$this->username = $username;
		$this->password = $password;
		$this->role = $role;
		$this->status = $status;
		$this->date_created = $date_created;

	}

	// Getters
	public function getUserID() { return $this->userID; }
	public function getFirstname() { return $this->firstname; }
	public function getMidint() { return $this->midint; }
	public function getLastname() { return $this->lastname; }
	public function getEmail() { return $this->email; }
	public function getUsername() { return $this->username; }
	public function getPassword() { return $this->password; }
	public function getRole() { return $this->role; }
	public function getStatus() { return $this->status; }
	public function getDateCreated() { return $this->date_created; }

	// Setters
	public function setFirstname($firstname) { $this->firstname = $firstname;}
	public function setMidint($midint) { $this->midint = $midint;}
	public function setLastname($lastname) { $this->lastname = $lastname;}
	public function setEmail($email) { $this->email = $email;}
	public function setUsername($username) { $this->username = $username;}
	public function setPassword($password) { $this->password = $password;}
	public function setRole($role) { $this->role = $role;}
	public function setStatus($status) { $this->status = $status;}
}

?>