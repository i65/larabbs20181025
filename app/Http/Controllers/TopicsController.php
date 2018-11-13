<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\TopicRequest;

use App\Models\Category;
use Auth;

use App\Handlers\ImageUploadHandler;

class TopicsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

	public function index(Request $request, Topic $topic)
	{
		//with是预加载
		// $topics = Topic::with('user', 'category')->paginate(30);
		$topics = $topic->withOrder($request->order)->paginate(20);

		// $topics = Topic::paginate(30);
		return view('topics.index', compact('topics'));
	}

    public function show(Request $request, Topic $topic)
    {
		//url矫正
		if(!empty($topic->slug) && $topic->slug != $request->slug) {
			return redirect($topic->link(), 301);
		}
        return view('topics.show', compact('topic'));
    }

	public function create(Topic $topic)
	{
		$categories = Category::all();
		return view('topics.create_and_edit', compact('topic', 'categories'));
	}

	public function store(TopicRequest $request, Topic $topic)
	{

		$topic->fill($request->all());
		$topic->user_id = Auth::id();
		$topic->save();

		// return redirect()->route('topics.show', $topic->id)->with('message', '成功创建话题.');
		return redirect()->to($topic->link())->with('success', '成功创建话题！');
	}

	public function edit(Topic $topic)
	{
		$this->authorize('update', $topic);
		
		$categories = Category::all();
		return view('topics.create_and_edit', compact('topic', 'categories'));
	}

	public function update(TopicRequest $request, Topic $topic)
	{
		$this->authorize('update', $topic);
		$topic->update($request->all());

		// return redirect()->route('topics.show', $topic->id)->with('message', '更新成功.');
		return redirect()->to($topics->link())->with('success', '更新成功.');
	}

	public function destroy(Topic $topic)
	{
		$this->authorize('destroy', $topic);
		$topic->delete();

		return redirect()->route('topics.index')->with('message', '成功删除.');
	}


	//上传图片
	public function uploadImage(Request $request, ImageUploadHandler $uploader)
	{
		//初始化返回数据，默认是失败的
		$data = [
			'success' => false,
			'msg' => '上传失败',
			'file_path' => ''
		];
		//判断是否有文件上传，并赋值给$file
		if ($file = $request->upload_file){
			//保存图片到本地
			$result = $uploader->save($request->upload_file, 'topics', \Auth::id(), 1024);
			//图片保存成功
			if($result){
				$data['file_path']  = $result['path'];
				$data['msg'] 		= '上传成功';
				$data['success'] 	= true;
			}
		}
		return $data;
	}
}