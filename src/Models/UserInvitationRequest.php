<?php

namespace Eightfold\RegisteredLaravel\Models;

use Illuminate\Database\Eloquent\Model;

use Validator;
use Illuminate\Database\Eloquent\Builder;

use Eightfold\RegisteredLaravel\Models\UserInvitation;

class UserInvitationRequest extends Model
{
    protected $fillable = ['email'];

    static public function unsentInvitationRequests()
    {
        return static::where('user_invitation_id', null)->get();
    }

    static public function withAddress($email)
    {
        return static::where('email', $email)->first();
    }

    static public function validatorPassed($email)
    {
        if (static::validator($email)->fails()) {
            return false;
        }
        return true;
    }

    static public function validator($email)
    {
        return Validator::make(['email' => $email], [
            'email' => static::validation()
        ]);
    }

    static public function validation()
    {
        return 'required|email|max:255|unique:user_invitation_requests';
    }

    /** Scopes */
    public function scopeWithEmail(Builder $query, string $email): Builder
    {
        return $query->where('email', $email);
    }
}
