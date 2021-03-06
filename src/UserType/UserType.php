<?php

namespace Eightfold\Registered\UserType;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Builder;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

use Eightfold\Registered\Registration\UserRegistration;

class UserType extends Model
{
    protected $casts = [
        'can_delete' => 'boolean'
    ];

    protected $fillable = [
        'slug', 'display'
    ];

    static public function userTypesForRoutes()
    {
        if (Schema::hasTable('user_types')) {
            $types = DB::table('user_types')->get();
            $typeReturn = [];
            foreach ($types as $type) {
                $typeReturn[] = [
                    'slug' => $type->slug,
                    'display' => $type->display
                ];
            }
            return $typeReturn;
        }
        return [];
    }

    public function registrations()
    {
        return $this->belongsToMany(UserRegistration::class);
    }

    public function setDisplayAttribute(string $display): bool
    {
        $this->attributes['display'] = $display;
        $this->attributes['slug'] = str_slug($display);
        return true;
    }

    /** Scopes */
    public function scopeWithSlug(Builder $query, string $slug): Builder
    {
        return $query->where('slug', $slug);
    }

    public function scopeWithSlugs(Builder $query, array $slugs): Builder
    {
        $count = 0;
        foreach ($slugs as $slug) {
            if ($count == 0) {
                $query->where('slug', $slug);
                $count++;

            } else {
                $query->orWhere('slug', $slug);
            }
        }
        return $query;
    }
}
