<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
use App\Models\User;

use App\Handlers\ImageUploadHandler;

class UsersController extends Controller
{
    //使用auth 中间件来验证用户
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['show']]);
    }
    //
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);//编辑权限控制，用户只能编辑自己的个人资料
        return view('users.edit', compact('user'));
    }

    public function update(UserRequest $request, ImageUploadHandler $uploader, User $user)
    {
        $this->authorize('update', $user);//同上
        $data = $request->all();

        if($request->avatar){
            $result = $uploader->save($request->avatar, 'avatars', $user->id, 362);//图片最大为362px
            if($result){
                $data['avatar'] = $result['path'];
                // dd($result['path']);return;
            }
        }
        $user->update($data);
        return redirect()->route('users.show', $user->id)->with('success', '个人资料更新成功');
    }
}
