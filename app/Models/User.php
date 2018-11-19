<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Auth;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Traits\LastActivedAtHelper;//用户最后活跃时间
    use Traits\ActiveUserHelper;//活跃用户
    use HasRoles;//获取扩展包提供的所有权限和角色的操作方法。
    use Notifiable {
        notify as protected laravelNotify;
    }
    public function notify($instance)
    {
        //如果要通知的人是当前用户，就不必通知了！
        if($this->id == Auth::id()) {
            return;
        }

        $this->increment('notification_count');
        $this->laravelNotify($instance);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'introduction', 'avatar'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function topics()
    {
        return $this->hasMany(Topic::class);
    }

    public function isAuthorOf($model)
    {
        return $this->id == $model->user_id;
    }
    //一个用户拥有多条评论
    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    public function markAsRead()
    {
        $this->notification_count = 0;
        $this->save();
        $this->unreadNotifications->markAsRead();
    }

    //后台修改用户密码时，加密后保存
    public function setPasswordAttribute($value)
    {
        //如果值的长度等于60，即认为是已经做过加密的情况
        if(strlen($value) != 60){
            //不等于60， 做密码加密处理
            $value = bcrypt($value);
        }

        $this->attributes['password'] = $value;
    }

    //后台修改用户头像
    public function setAvatarAttribute($path)
    {
        //如果不是 http 子串开头，那就是从后台上传的，需要补全url
        if(!starts_with($path, 'http')){
            //拼接完整的url
            $path = config('app.url') . "/uploads/images/avatars/$path";
        }
        $this->attributes['avatar'] = $path;
    }

}
