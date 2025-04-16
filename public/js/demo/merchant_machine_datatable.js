$(function () {
    var table = $('#dataTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('merchant.machine.list') }}",
        columns: [
            {data: 'id', name: 'id'},
            {data: 'email', name: 'email'},
            // {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
    });
});
