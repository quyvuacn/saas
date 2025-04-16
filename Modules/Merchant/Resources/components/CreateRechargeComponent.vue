<template>
    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary float-left">Thông tin người dùng </h6>
                </div>
                <div class="card-body">
                    <div class="mb-5">
                        <form @submit.prevent="searchUser" @keydown="form.onKeydown($event)">
                            <div class="input-group">
                                <Select2 v-model="form.email" :options="users" class="form-control fix" :class="{ 'is-invalid': form.errors.has('email') }" :settings="{selectionCssClass:'selectionCssClass'}" :placeholder="'Nhập email người dùng...'" />
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-search fa-sm"></i>
                                    </button>
                                </div>
                                <has-error :form="form" field="email"></has-error>
                            </div>
                        </form>
                    </div>
                    <ul class="list-group" v-if="user">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Email: <strong>{{ user.email }}<br>
                        </strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">Ngày
                            tạo tài khoản:
                            <strong>{{ user.created_at | moment }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Hạn mức đã cấp:
                            <strong>{{ formatPrice(user.credit_quota) || 0 }}
                                <small> coin</small></strong></li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Dự nợ/số dư hiện tại:
                            <strong class="text-danger">{{ formatPrice(user.coin) || 0 }}
                                <small> coin</small></strong></li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Ngày cấp hạn mức:
                            <strong>{{ user.credit_updated_at | moment }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Người cấp hạn mức:
                            <strong>{{ user.merchant_update_by ? user.merchant_update_by.name : '---' }}</strong>
                        </li>
                    </ul>
                    <div class="alert alert-danger border-left-danger alert-dismissible fade show" role="alert" v-if="!user && search">
                        <ul class="pl-4 my-2">
                            <li>Không tồn tại User có thông tin: <strong>{{ key }}</strong></li>
                        </ul>
                        <button type="button" class="close" aria-label="Close" @click="search=false">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thêm mới số coin</h6>
                </div>
                <div class="card-body">
                    <form @submit.prevent="createCoinRequest" @keydown="form_create.onKeydown($event)">
                        <div class="form-group">
                            <div class="alert alert-danger border-left-danger alert-dismissible fade show" role="alert" v-if="create && form_create.errors.any()">
                                <ul class="pl-4 my-2">
                                    <li v-if="form_create.errors.has('user_id')">{{ form_create.errors.get('user_id') }}</li>
                                    <li v-if="form_create.errors.has('user_coin')">{{ form_create.errors.get('user_coin') }}</li>
                                </ul>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close" @click="create=false">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        </div>
                        <h5>Chọn nhanh số coin cần nạp:</h5>
                        <div class="coin-list" v-if="coin_recharge">
                            <span class="btn btn-info btn-sm mr-2 mb-2" v-for="(coin, index) in coin_recharge" :key="index" @click="chooseCoin(coin)">{{ formatPrice(coin) }} Coin</span>
                        </div>
                        <div class="form-group">
                            <label for="user_coin">Tùy chỉnh số coin</label>
                            <input type="number" class="form-control" name="user_coin" id="user_coin" placeholder="Nhập số coin người dùng đã mua" :min="min_credit_quote" :max="max_credit_quote"
                                v-model="form_create.user_coin" :class="{ 'is-invalid': form_create.errors.has('user_coin') }">
                            <has-error :form="form_create" field="user_coin"></has-error>
                        </div>
                        <div class="form-group">
                            <label for="user_message">Nội dung yêu cầu</label>
                            <textarea name="user_message" id="user_message" v-model="form_create.user_message" rows="3" class="form-control"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-user btn-block">
                            Tạo mới yêu cầu
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import {Form} from 'vform'
import Select2 from 'v-select2-component';

export default {
    props: ['coins', 'min_credit_quote', 'max_credit_quote', 'users'],
    mounted() {
        this.coin_recharge = JSON.parse(this.coins);
        // this.select2Settings.data = this.users;
    },
    data() {
        return {
            user: null,
            search: false,
            create: false,
            key: '',
            coin_recharge: null,
            form: new Form({
                email: ''
            }),
            form_create: new Form({
                user_coin: '',
                user_id: null,
                user_message: '',
            }),
            select2Settings: {
                selectionCssClass: 'selectionCssClass',
                // dropdownCssClass: 'dropdownCssClass',
            }
        }
    },
    components: {Select2},
    methods: {
        number_format(number, decimals, dec_point, thousands_sep) {
            // *     example: number_format(1234.56, 2, ',', ' ');
            // *     return: '1 234,56'
            number = (number + '').replace(',', '').replace(' ', '');
            var n = !isFinite(+number) ? 0 : +number,
                prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
                sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
                dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
                s = '',
                toFixedFix = function (n, prec) {
                    var k = Math.pow(10, prec);
                    return '' + Math.round(n * k) / k;
                };
            // Fix for IE parseFloat(0.55).toFixed(0) = 0;
            s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
            if (s[0].length > 3) {
                s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
            }
            if ((s[1] || '').length < prec) {
                s[1] = s[1] || '';
                s[1] += new Array(prec - s[1].length + 1).join('0');
            }
            return s.join(dec);
        },
        quickApprove(request_id) {
            let email = this.form.email;
            let coin = this.number_format(this.form_create.user_coin);
            Swal.fire({
                title: '<h3>Bạn có xác nhận nạp ' + coin + ' <sup>Vnđ</sup><br> cho tài khoản ' + email + ' hay không?</h3>',
                icon: 'success',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Xác nhận',
                cancelButtonText: 'Hủy',
            }).then((result) => {
                if (result.value) {
                    axios.post('/user/recharge-quick-approve/' + request_id).then(response => {
                        if (response.status == 200 && response.data.status) {
                            Swal.fire(
                                'Successfull!',
                                response.data.message,
                                'success'
                            );
                        } else {
                            Swal.fire(
                                'Ops!',
                                response.data.message,
                                'error'
                            );
                        }
                        $('#dataTable-vti').DataTable().ajax.reload();
                    });
                }
            })
        },
        searchUser() {
            this.user = null;
            this.search = false;
            this.create = false;
            this.form_create.clear();
            this.form_create.user_id = null;
            this.form.post('/user/search').then(response => {
                if (response.status == 200 && response.data.status) {
                    this.user = response.data.data;
                    this.search = true;
                    this.key = this.form.email;
                    if (this.user) {
                        this.form_create.user_id = this.user.id;
                    }
                } else {
                    Swal.fire(
                        'Kết quả tìm kiếm!',
                        response.data.message,
                        'warning'
                    );
                }
            });
        },
        chooseCoin(coin) {
            this.form_create.user_coin = coin;
        },
        resetAll() {
            this.form_create.reset();
            this.form_create.clear();
            this.form.reset();
            this.form.clear();
            this.create = false;
            this.search = false;
            this.user = null;
        },
        createCoinRequest: function () {
            this.create = true;
            this.search = false;
            this.form.clear();
            this.form_create.post('/user/recharge-store').then(response => {
                if (response.status == 200 && response.data.status) {
                    this.quickApprove(response.data.request_id);
                    this.resetAll();
                } else {
                    Swal.fire(
                        'Oops!',
                        response.data.message,
                        'warning'
                    );
                }
            })
        },
        formatPrice(value) {
            let val = (value / 1).toFixed(0).replace('.', ',')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        }
    },
    filters: {
        toCurrency(value) {
            if (typeof value !== "number") {
                return value;
            }
            var formatter = new Intl.NumberFormat('vi-VN', {
                style: 'currency',
                currency: 'VND',
                minimumFractionDigits: 0
            });
            return formatter.format(value);
        },
        moment: function (date) {
            if (date == null || date == '') {
                return '---';
            }
            return moment(date).lang('en').format('DD/MM/YYYY, h:mm:ss A');
        }
    }
}
</script>
<style lang="scss">
.form-control.fix {
    padding: 0;
    border: 0;
    .select2{
        .select2-selection{
            height: 40px!important;
            line-height: 40px;
            .select2-selection__rendered, .select2-selection__arrow{
                height: 40px;
                line-height: 40px;
            }
        }
    }
    .select2-container--focus .select2-selection--single:focus{
        outline: transparent;
        border-color: #4e73df;
    }
}
.form-control.fix.is-invalid {
    .select2-selection--single{
        border-color: #e74a3b;
    }
}
.select2-results__options{
    &::-webkit-scrollbar {
        width: 6px;
    }

    /* Track */
    &::-webkit-scrollbar-track {
        background: #fff;
        border-radius: 10px;
    }

    /* Handle */
    &::-webkit-scrollbar-thumb {
        background: #718096;
        border-radius: 10px;
    }

    /* Handle on hover */
    &::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

}

</style>
