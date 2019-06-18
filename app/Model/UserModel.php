<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserModel extends Model
{
    protected $table = 'p_user';  //表名
    protected $primaryKey  = 'u_id';  //主键
    public $timestamps = false;  //开启自动写入时间戳

}
