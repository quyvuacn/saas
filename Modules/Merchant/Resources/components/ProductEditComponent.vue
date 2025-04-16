<template>
    <div class="product-image-upload">
        <form @submit.prevent="editProduct" @keydown="product_form.onKeydown($event)" enctype="multipart/form-data">
            <div class="img-block">
                <div class="input-info">
                    <div class="form-group">
                        <label for="name">Tên sản phẩm</label>
                        <input type="text" name="name" v-model="product_form.name" id="name" class="form-control" :class="{ 'is-invalid': product_form.errors.has('name') }"
                            placeholder="Nhập tên sản phẩm của bạn" required>
                        <has-error :form="product_form" field="name"></has-error>
                    </div>
                    <div class="form-group">
                        <label for="price_default">Giá bán mặc định</label>
                        <input type="number" name="price_default" id="price_default" class="form-control" :class="{ 'is-invalid': product_form.errors.has('price_default') }" placeholder="Nhập giá bán tham khảo của sản phẩm ..." v-model="product_form.price_default"
                            min="1000" max="100000" required
                        >
                        <has-error :form="product_form" field="price_default"></has-error>
                    </div>
                    <div class="form-group">
                        <label for="image">Hình ảnh sản phẩm</label>
                        <input type="file" class="form-control" :class="{ 'is-invalid': product_form.errors.has('file') }" name="file" accept="image/jpg,image/png,image/jpeg"
                            id="image"
                            @change="getImage">
                        <has-error :form="product_form" field="file"></has-error>
                    </div>
                </div>
                <figure>
                    <img :src="productImage ? productImage : product.image" alt="Product Image" class="mb-3">
                </figure>
            </div>
            <div class="form-group">
                <label for="brief">Miêu tả ngắn về sản phẩm</label>
                <textarea name="brief" id="brief" class="form-control" rows="3" v-model="product_form.brief" :class="{ 'is-invalid': product_form.errors.has('brief') }" required></textarea>
                <has-error :form="product_form" field="brief"></has-error>
            </div>
            <button type="submit" class="btn btn-facebook btn-block">
                <i class="fas fa-check"></i> Sửa sản phẩm
            </button>
        </form>
    </div>
</template>
<script>
    import {Form} from 'vform'

    export default {
        props: ['product'],
        mounted() {
            this.product_form.fill(this.product);
            this.productImage = this.product.image;
            $("figure").click(function () {
                $("#image").click();
            });
        },
        data() {
            return {
                productEdit: null,
                fileImage: null,
                productImage: null,
                product_form: new Form({
                    name: null,
                    brief: null,
                    price_default: null,
                    file: null,
                }),
            }
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
                            this.productImage = e.target.result;
                        };
                        // Image data
                        this.product_form.file = this.fileImage;
                        this.fileImage = image;
                        this.product_form.errors.clear('file');
                    } else {
                        let image_error = '';
                        if (image.size > 2000000) {
                            image_error = 'Dung lượng vượt quá 2M!';
                        }
                        if (image.type !== 'image/png' || image.type !== 'image/jpeg' || image.type !== 'image/jpg') {
                            image_error += ' File ảnh phải có định dạng .png, jpg, jpeg';
                        }
                        this.product_form.errors.set('file', image_error);
                        this.product_form.file = null;
                        this.productImage = null;
                    }
                } else {
                    this.product_form.errors.clear('file');
                    this.productImage = null;
                    this.product_form.file = null;
                }
            },
            editProduct() {
                this.product_form.post('/product/update/' + this.product.id, {
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
                            window.location.href = '/product';
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
            resetAll() {
                // this.product_form.reset();
                // this.product_form.clear();
                // this.productImage = null;
                // this.fileImage =  null;
            },
        },
    };
</script>
<style lang="scss" scoped>
    .product-image-upload {
        .img-block {
            display: flex;

            figure {
                width: 256px;
                height: 256px;
                flex-basis: 256px;
                object-fit: cover;
                object-position: center;
                background: #eee;
                border: 1px dashed #ccc;
                margin-left: 30px;
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
