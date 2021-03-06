<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;

class SessionsController extends Controller
{
      public function __construct()
      {
        $this->middleware('guest',[
          'only' => ['create']
        ]);
      }
      public function create()
      {
          return view('sessions.create');
      }

      public function store(Request $request)
    {
      $this->validate($request, [
          'email' => 'required|email|max:255',
          'password' => 'required'
      ]);

      $credentials = [
          'email'    => $request->email,
          'password' => $request->password,
      ];

      if (Auth::attempt($credentials,$request->has('remember'))) {
        if(Auth::user()->activated) {
              session()->flash('success', '欢迎回来！');
              // TODO 这里不明白，为什么可以这样伟参，route 定义是是 id 呀！
              // return redirect()->route('users.show', [Auth::user()]);
              // TODO 更友好的转向：返回到上一次尝试访问的页面
              return redirect()->intended(route('users.show', [Auth::user()]));
           } else {
               Auth::logout();
               session()->flash('warning', '你的账号未激活，请检查邮箱中的注册邮件进行激活。');
               return redirect('/');
           }
      } else {
          session()->flash('danger', '很抱歉，您的邮箱和密码不匹配');
          return redirect()->back();
      }
    }

    public function destroy()
{
    Auth::logout();
    session()->flash('success', '您已成功退出！');
    return redirect('login');
}
}
