<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampusStock extends Model
{
    use HasFactory;

    protected $fillable = ['campus_id', 'stock_id', 'quantity'];
    protected $connection = 'mysql';

    protected $table = 'campus_stock';

    public function campus()
    {
        # code...
        return $this->belongsTo(Campus::class);
    }

    public function stock()
    {
        # code...
        return $this->belongsTo(Stock::class);
    }

    public function restore($quantity)
    {
        # code...
        if($this->quantity < $quantity){
            throw new Exception("Too few items in stock");
        }
        $this->quantity -= $quantity;
        $this->save();
        $stock = $this->campus()->get()->first();
        $stock->quantity += $quantity;
        $stock->save();
    }
}
