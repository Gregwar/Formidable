<?php

/**
 * Demo entity
 */
class Person 
{
    protected $firstname;

    protected $age;

    protected $gender;

    protected $birthday;

    protected $email;

    protected $animals;
    
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    public function setAge($age)
    {
        $this->age = $age;
    }

    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
    }

    public function setAnimals($animals)
    {
        $this->animals = $animals;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }
}
