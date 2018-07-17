@if (Session::has('success'))
    <div class="alert alert-success alert-with-icon">
        <i class="material-icons" data-notify="icon" >check</i>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">x</button>
        <span data-notify="message"> <b>Sukses :</b> {{Session::get('success')}} </span>
    </div>

@elseif (Session::has('delete'))
    <div class="alert alert-info alert-with-icon">
        <i class="material-icons" data-notify="icon" >info_outline</i>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">x</button>
        <span data-notify="message"> <b>Info :</b> {{Session::get('delete')}}</span>
    </div>

@elseif( count($errors) > 0)
    <div class="alert alert-danger alert-with-icon">
        <i class="material-icons" data-notify="icon" >error_outline</i>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">x</button>
        <span data-notify="message">
            <ul>
                @foreach($errors->all() as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </ul>
        </span>
    </div>
@endif