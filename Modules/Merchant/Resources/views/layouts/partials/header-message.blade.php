<?php $alert = session('message') ? 'success' : 'danger' ?>
@if (session('message') || session('error'))
<div class="alert alert-{{$alert}} border-left-{{$alert}} alert-dismissible fade show" role="alert">
    {{ session('message') ?? session('error') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif
