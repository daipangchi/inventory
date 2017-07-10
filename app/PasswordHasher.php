<?php

namespace App;

trait PasswordHasher
{
    public function setPasswordAttribute($password)
    {
        return $this->attributes['password'] = bcrypt($password);
    }
}
