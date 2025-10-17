<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstablishmentPic extends Model
{
    use HasFactory;

    protected $fillable = [
        'establishment_id',
        'image_path',
        'caption',
    ];

    public function establishment()
    {
        return $this->belongsTo(Establishment::class);
    }
}
