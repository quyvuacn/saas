@if ($errors->any())
    <div class="error-message-block alert alert-danger border-left-danger alert-dismissible fade show" role="alert">
        <ul class="pl-4 my-2">
            @foreach (array_slice($errors->all(),0,50) as $error)
                <li>{{ str_replace(config('merchant.excel_replace.source'),config('merchant.excel_replace.des'),$error)}}</li>
            @endforeach
            @if($errors->count() > 50)
                <li>{{ __('Vui lòng kiểm tra các thông báo lỗi...')}}</li>
            @endif
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
