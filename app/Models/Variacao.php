<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Variacao extends Model
{
    protected $fillable = ['produto_id', 'nome', 'preco'];

    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }
}

