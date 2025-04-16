<template>
    <div class="ads-image-upload">
        <form @submit.prevent="editAds" @keydown="ads_form.onKeydown($event)" enctype="multipart/form-data">
            <input type="hidden" v-model="ads_form.has_machine">
            <div class="img-block">
                <div class="row">
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label>Chọn máy bán hàng</label>
                            <div class="ads-machine-list">
                                <div v-for="machine in machines" :key="machine.id">
                                    <input type="checkbox" :id="machine.id" :value="machine.id" v-model="ads_form.machines_list">
                                    <label :for="machine.id">{{ machine.name }}</label>&nbsp;&nbsp;&nbsp;&nbsp;
                                </div>
                            </div>
                            <span :class="{ 'is-invalid': ads_form.errors.has('has_machine') }"></span>
                            <has-error :form="ads_form" field="has_machine"></has-error>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="input-info">
                            <div class="form-group">
                                <label for="image">Hình ảnh quảng cáo</label>
                                <input type="file" class="form-control" :class="{ 'is-invalid': ads_form.errors.has('file') }" name="file"
                                    accept="image/jpg,image/png,image/jpeg"
                                    id="image"
                                    @change="getImage">
                                <has-error :form="ads_form" field="file"></has-error>
                            </div>
                            <div class="form-group">
                                <label for="start_date">Ngày bắt đầu</label>
                                <flat-pickr
                                    v-model="ads_form.start_date"
                                    :config="config"
                                    id="start_date"
                                    class="form-control"
                                    :class="{ 'is-invalid': ads_form.errors.has('start_date') }"
                                    placeholder="DD/MM/YYYY">
                                </flat-pickr>
                                <has-error :form="ads_form" field="start_date"></has-error>
                            </div>
                            <div class="form-group">
                                <label for="end_date">Ngày kết thúc</label>
                                <flat-pickr
                                    v-model="ads_form.end_date"
                                    :config="config"
                                    class="form-control"
                                    :class="{ 'is-invalid': ads_form.errors.has('end_date') }"
                                    id="end_date"
                                    placeholder="DD/MM/YYYY">
                                </flat-pickr>
                                <has-error :form="ads_form" field="end_date"></has-error>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <figure>
                            <img :src="adsImage ? adsImage : ads.image" alt="Product Image" class="mb-3">
                        </figure>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-facebook btn-block">
                <i class="fas fa-check"></i> Sửa quảng cáo
            </button>
        </form>
    </div>
</template>
<script>
import {Form} from 'vform'
import flatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
import 'flatpickr/dist/themes/dark.css';

export default {
    props: ['ads', 'machines', 'ads_machines'],
    mounted() {

        $("figure").click(function () {
            $("#image").click();
        });
    },
    components: {
        flatPickr
    },
    data() {
        return {
            adsEdit: null,
            fileImage: null,
            adsImage: this.ads.image,
            ads_form: new Form({
                file: null,
                start_date: null,
                end_date: null,
                has_machine: '',
                machines_list: null,
            }),
            config: {
                wrap: true,
                dateFormat: 'd/m/Y',
                locale: Vietnamese,
                minDate: "today",
            },
        }
    },
    created() {
        this.ads_form.fill(this.ads);
        this.adsImage = this.ads.image;
        this.ads_form.machines_list = this.ads_machines;
        this.ads_form.start_date = moment(this.ads_form.start_date).format('DD/MM/YYYY');
        this.ads_form.end_date = moment(this.ads_form.end_date).format('DD/MM/YYYY');
    },
    watch: {
        'ads_form.machines_list': function (newVal, oldVal) {
            if (newVal.length) {
                this.ads_form.has_machine = true;
            } else {
                this.ads_form.has_machine = '';
            }
        },
    },
    methods: {
        getImage(e) {
            var image = e.target.files[0];
            this.fileImage = e.target.files[0];
            if (image) {
                if (image.size <= 2000000 && (image.type === 'image/png' || image.type === 'image/jpeg' || image.type === 'image/jpg')) {
                    // Show Image
                    var reader = new FileReader();
                    reader.readAsDataURL(image);
                    reader.onload = e => {
                        this.adsImage = e.target.result;
                    };
                    // Image data
                    this.ads_form.file = this.fileImage;
                    this.fileImage = image;
                    this.ads_form.errors.clear('file');
                } else {
                    let image_error = '';
                    if (image.size > 2000000) {
                        image_error = 'Dung lượng vượt quá 2M!';
                    }
                    if (image.type !== 'image/png' || image.type !== 'image/jpeg' || image.type !== 'image/jpg') {
                        image_error += ' File ảnh phải có định dạng .png, jpg, jpeg';
                    }
                    this.ads_form.errors.set('file', image_error);
                    this.ads_form.file = null;
                    this.adsImage = null;
                }
            } else {
                this.ads_form.errors.clear('file');
                this.adsImage = null;
                this.ads_form.file = null;
            }
        },
        editAds() {
            this.ads_form.post('/ads/' + this.ads.id + '/update', {
                // Transform form data to FormData
                transformRequest: [function (data, headers) {
                    return objectToFormData(data)
                }],
                onUploadProgress: e => {
                    // Do whatever you want with the progress event
                    // console.log(e)
                }
            }).then(response => {
                if (response.status == 200 && response.data.status) {
                    Swal.fire(
                        'Successfully!',
                        response.data.message,
                        'success'
                    );
                    this.resetAll();
                    setTimeout(function () {
                        window.location.href = '/ads';
                    }, 1500)
                } else {
                    Swal.fire(
                        'Oops!',
                        response.data.message,
                        'warning'
                    );
                }
            })
        },
        inThisMachine(machine_id) {
            return true;
        },
        resetAll() {
            // this.ads_form.reset();
            // this.ads_form.clear();
            // this.adsImage = null;
            // this.fileImage =  null;
        },
    },
};
</script>
<style lang="scss" scoped>
.ads-image-upload {
    .ads-machine-list {
        max-height: 215px;
        overflow-y: scroll;

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

    .img-block {
        //display: flex;

        figure {
            width: 100%;
            height: 256px;
            object-fit: cover;
            object-position: center;
            background: #eee;
            border: 1px dashed #ccc;
            position: relative;
            box-shadow: 3px 3px 6px #ccc;

            img {
                position: absolute;
                left: 50%;
                top: 50%;
                transform: translate(-50%, -50%);
                width: 100%;
                height: 100%;
                object-fit: cover;
                object-position: center;

            }
        }

        .input-info {
            flex: 1;
        }
    }
}
</style>
