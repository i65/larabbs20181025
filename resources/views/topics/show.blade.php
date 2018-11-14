@extends('layouts.app')

@section('title', $topic->title)
@section('description', $topic->excerpt)

@section('content')

<div class="row">
    <div class="col-lg-3 col-md-3 hidden-sm hidden-xs author-info">
        <div class="panel panel-default">            

            <div class="panel-body">
                <div class="text-center">
                    作者：{{ $topic->user->name }}
                </div>
                <hr>
               <div class="media">
                   <div align="center">
                       <a href="{{ route('users.show', $topic->user->id) }}">
                           <img src="{{ $topic->user->avatar }}" alt="" class="thumbnail img-responsive" width="300px" height="300px">
                       </a>
                   </div>
               </div>
            </div>
        </div>
    </div>
    <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 topic-content">
        <div class="panel panel-default">
            <h1 class="text-center">
                {{ $topic->title }}
            </h1>

            <div class="article-meta text-center">
                {{ $topic->created_at->diffForHumans() }}
                .
                <span class="glyphicon glyphicon-comment" aria-hidden></span>
                {{ $topic->reply_count }}
            </div>

            <div class="topic-body">
                {!! $topic->body !!}
            </div>

            @can('update', $topic)
                <div class="operate">
                    <hr>
                    <a href="{{ route('topics.edit', $topic->id) }}" class="btn btn-default btn-xs pull-left" role="button">
                        <i class="glyphicon glyphicon-edit"></i>编辑
                    </a>
                    <form action="{{ route('topics.destroy', $topic->id) }}" method="post">
                        {{ csrf_field() }}
                        {{ method_field('DELETE')}}
                        <button class="btn btn-default btn-xs pull-left" type="submit" style="margin-left:6px;">
                            <i class="glyphicon glyphicon-trash"></i>删除
                        </button>
                    </form>                    
                </div>
            @endcan
        </div>

        {{-- 用户回复列表 --}}
        <div class="panel panel-default topic-reply">
            <div class="panel-body">
                {{-- Laravel Blade 模板提供了一个『视条件加载子模板』的语法：@includeWhen($boolean, 'view.name', ['some' => 'data']) --}}
                @includeWhen(Auth::check(), 'topics._reply_box', ['topic' => $topic])
                @include('topics._reply_list', ['replies' => $topic->replies()->with('user')->get()])
            </div>
        </div>
    </div>
</div>

@endsection
