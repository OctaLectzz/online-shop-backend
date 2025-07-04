<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed'
        ];
    }

    // Avatar
    public static function uploadAvatar(UploadedFile $avatar, string $name): string
    {
        $filename = time() . '-' . Str::slug($name) . '.' . $avatar->getClientOriginalExtension();
        $avatar->storeAs('avatars', $filename, 'public');
        return $filename;
    }
    public function deleteAvatar(): void
    {
        if ($this->avatar && Storage::disk('public')->exists('avatars/' . $this->avatar)) {
            Storage::disk('public')->delete('avatars/' . $this->avatar);
        }
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }
    public function products()
    {
        return $this->hasMany(Product::class, 'created_by');
    }
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
    public function promos()
    {
        return $this->hasMany(Promo::class, 'created_by');
    }
    public function carts()
    {
        return $this->hasMany(Cart::class);
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function pays()
    {
        return $this->hasMany(Pay::class, 'validated_by');
    }
    public function shipments()
    {
        return $this->hasMany(Pay::class, 'processed_by');
    }
}
