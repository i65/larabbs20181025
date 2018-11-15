<?php

namespace App\Observers;

use App\Models\Topic;
use App\Handlers\SlugTranslateHandler;
use App\Jobs\TranslateSlug;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class TopicObserver
{
    public function creating(Topic $topic)
    {
        //
    }

    public function updating(Topic $topic)
    {
        //
    }

    public function saving(Topic $topic)
    {
        //过滤，防止xss攻击
        $topic->body = clean($topic->body, 'user_topic_body');

        //生成话题摘录
        $topic->excerpt = make_excerpt($topic->body);        
    }

    public function saved(Topic $topic)
    {
        //如slug字段无内容，即使用翻译器对title 进行翻译
        if( !$topic->slug){
            
            // $topic->slug = app(SlugTranslateHandler::class)->translate($topic->title);
            //推送任务到队列
            dispatch(new TranslateSlug($topic));
        }
    }

    //话题连带删除，当话题被删除时，所有回复也会被清空
    //需要注意的是，在模型监听器中，数据库操作需要避免再次 Eloquent 事件，
    //所以这里我们使用了 DB 类进行操作。
    public function deleted(Topic $topic)
    {
        \DB::table('replies')->where('topic_id', $topic->id)->delete();
    }
}