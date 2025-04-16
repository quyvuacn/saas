function deleteItem(url, params = [], callback) {
    Swal.fire({
        title: 'Bạn có chắc muốn xóa?',
        text: "Bạn sẽ không thể hoàn tác điều này!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Xóa!',
        cancelButtonText: 'Hủy'
    }).then((result) => {
        if (result.value) {
            axios.post(url, {data: params}).then(response => {
                callback(response);
            }).catch(function (error) {
                showMessageError();
            });
        }
    })
}
function showMessageSuccess(msg = 'Xoá thành công') {
    Swal.fire({
        position: 'center',
        icon: 'success',
        title: msg,
        showConfirmButton: false,
        timer: 1500
    });
}

function showMessageError(msg = '') {
    if(msg == ''){
        msg = 'Có lỗi xảy ra';
    }
    Swal.fire({
        position: 'center',
        icon: 'error',
        title: msg,
        showConfirmButton: false,
    });
}
function loading(){
    $('#modal-spinner').modal('show');
}
function hideLoading() {
    setTimeout(function () {
        $('#modal-spinner').modal('hide');
    }, 500)
}
