<?php

namespace App\Models\Complaintattachment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Complaintattachment extends Model
{
    use HasFactory;

    protected $table = 'complaintattachment';
    protected $primaryKey = 'id';
    protected $fillable = ['fk_complaint', 'name', 'file'];

    /**
     * Gera um código aleatório para nomear arquivos
     */
    public static function generateCustomRandomCode(int $length = 10): string
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code = '';

        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[random_int(0, strlen($characters) - 1)];
        }

        return $code;
    }
}
