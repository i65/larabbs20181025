@if(Session::has('message'))
    <div class="alert alert-info">
        <button class="close" class="close" data-dismiss="alert" aria-hidden="true">x</button>
        {{ Session::get('message')}}
    </div>
@endif

@if(Session::has('success'))
    <div class="alert alert-success">
        <button class="close" class="close" data-dismiss="alert" aria-hidden="true">x</button>
        {{ Session::get('success')}}
    </div>
@endif

@if(Session::has('danger'))
    <div class="alert alert-danger">   
        <button class="close" class="close" data-dismiss="alert" aria-hidden="true">x</button>
        {{ Session::get('danger')}}
    </div>
@endif 