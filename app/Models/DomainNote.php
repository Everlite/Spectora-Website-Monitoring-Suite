<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DomainNote extends Model
{
    protected $fillable = ['domain_id', 'content', 'user_id'];

    protected $appends = ['author_name'];

    public function domain()
    {
        return $this->belongsTo(Domain::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getAuthorNameAttribute(): ?string
    {
        if (! $this->user) {
            return null;
        }

        return trim($this->user->first_name.' '.$this->user->last_name) ?: $this->user->email;
    }
}
