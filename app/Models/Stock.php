<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'quantity', 'type'];
    protected $connection = 'mysql';

    protected $table = 'stock';

    public function recieve($quantity)
    {
        # code...
        $this->quantity += $quantity;
        $this->save();
    }

    public function send($quantity, $campus)
    {
        # code...
        if ($this->quantity < $quantity) {
            # code...
            throw new Exception("Too few items in stock");
        }
        $this->quantity -= $quantity;
        $this->save();
        // increase campus_tock item quantity and save;
        $campusStock = CampusStock::where(['stock_id'=>$this->id])->where(['campus_id'=>$campus])->first();
        if (!$campusStock == null) {
            # code...
            $campusStock->quantity += $quantity;
            $campusStock->save();
        }else{
            $campusStock = new CampusStock(['stock_id'=>$this->id, 'campus_id'=>$campus, 'quantity'=>$quantity]);
            $campusStock->save();
        }
        // StockTransfers::create(['quantity'=>])
    }

    public function campusStock($campus_id)
    {
        # code...
        // dd( $this->hasMany(CampusStock::class, 'stock_id')->get()->where(['campus_stock.campus_id' => $campus_id])->first());
        return $this->hasMany(CampusStock::class, 'stock_id')->where(['campus_id' => $campus_id])->first();

    }

    public function transfers()
    {
        # code...
        return $this->hasMany(StockTransfers::class, 'stock_id')->where(['user_id'=>auth()->id()]);
    }

    public function latestTransfer()
    {
        # code...
        return $this->transfers()->first();
    }

    public function studentStock($campus_id = null)
    {
        # code...
        return $this->hasMany(StudentStock::class, 'stock_id')->where(function($q)use($campus_id){
            !$campus_id == null ? $q->where(['campus_id'=>$campus_id]) : null;
        })->orderBy('id', 'DESC');
    }
}
