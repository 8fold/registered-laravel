<?php

namespace Eightfold\Registered\Traits;

use Hash;
use Validator;

use Eightfold\Registered\Models\UserRegistration;

trait RegisteredUser
{
    public function getUserTypeSlugAttribute()
    {
        return $this->registration->type->slug;
    }

    public function registration()
    {
        return $this->hasOne(UserRegistration::class, 'user_id');
    }

    public function getIsSiteOwnerAttribute()
    {
        return $this->userTypeSlug == 'owners';
    }

    public function isSiteOwner($string = false)
    {
        // Only people strictly assigned as Owner will be.
        return $this->isSiteOwner;
    }

    public function isUser($strict = false)
    {
        $s = ($this->userTypeSlug == 'users');
        if ($strict) {
            return $s;
        }
        // All user types are users.
        return true;
    }

    public function getIsUserAttribute()
    {
        return $this->isUser();
    }

    public function setPasswordAttribute($pass)
    {
        $this->attributes['password'] = Hash::make($pass);
        $this->save();
    }

    public function setUsernameAttribute(string $username): bool
    {
        if (static::usernameValidatorPassed($username)) {
            $this->usernameValidator($username)->validate();
            $this->attributes['username'] = strtolower($username);
            return true;
        }
        return false;
    }

    /**
     *
     * @param  string $username The username of the person you are looking for.
     *
     * @return User
     */
    static public function withUsername(string $username)
    {
        return static::where('username', $username)->first();
    }

    static protected function usernameValidatorPassed(string $username): bool
    {
        if (static::usernameValidator($username)->fails()) {
            return false;
        }
        return true;
    }

    static protected function usernameValidator(string $username)
    {
        return Validator::make(['username' => $username], [
            'username' => static::usernameValidation()
        ]);
    }

    static public function usernameValidation(): string
    {
        return UserRegistration::usernameValidation();
    }
}
