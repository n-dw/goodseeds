<template>
    <div class="bnc">
    <div class="product-info-wrapper">
        <div class="media">
            <div class="media-left">
                <div class="product-details-wrapper">
                    <h4 class="product-strain-type" :class="productData.strainType">
                        {{ productData.strainTypeFrontVal }}
                    </h4>
                    <h4 v-if="productData.organic" class="product-strain-type organic">O</h4>
                </div>
            </div>
            <div class="media-content">
                <div class="product-price-wrapper has-text-right">
                    <span v-if=" productData.sale" class="sale-price">was  <strike>{{price}}</strike></span>
                    <span class="price"><b>{{ salePrice }}</b></span>
                </div>
            </div>
        </div>
    </div>
    <div class="product-buy-now">
        <div class="buy-now">
            <form method="POST" id="addToCartForm"  @submit.prevent="submitForm">

                <input v-for="input in form.inputs" :type="input.type" :name="input.name" :value="input.value">

                <div v-if=" productData.stock > 0" class="in-stock-controls">
                    <div class="row">
                        <div class="product-variants col-xs">
                            <!--<input v-model="picked" :id="purchasableId" name="purchasableId" type="hidden"
                                   :value="variant.purchasableId"  :disabled="variant.disabled">-->
                            <template v-for="(variant, index) in productData.variants">
                                <div class="radio-wrapper variant">
                                    <input v-model="picked" :id="variant.place" name="purchasableId" type="radio"
                                       :value="variant.purchasableId" :checked="variant.checked" :disabled="variant.disabled">
                                    <label  class="c-radio c-button c-button--cta-black c-button--small variant-label"  @click="variantClick(index)" :disabled="variant.disabled" :for="variant.place">
                                        <span class="variant-weight">
                                        <strike v-if="variant.disabled">
                                                {{ variant.weight }}
                                        </strike>
                                        <template v-else>
                                            {{ variant.weight }}
                                        </template>
                                        </span>
                                    </label>
                                </div>
                            </template>
                        </div>
                    </div>
                    <div class="row center-xs">
                        <div class="col-xs">
                            <quantity @changequantity="changeQuantity" :disabled="productData.stock < 1" qty="1"></quantity>
                        </div>
                    </div>
                </div>

                <button v-if="productData.stock > 0" type="submit" class="c-button c-button--cta-black add-to-cart"  :class="loading">Buy Now</button>

                <button v-else-if="currentuseremail != '' && productData.stock < 1" type="submit" :class="loading" class="c-button c-button--cta-black add-to-cart">Notify Me Upon Restock</button>
                <div v-else class="notify-stock-component">
                    <input type="email" v-model="email"  v-show="notifyEmailShow" class="c-input has-text-centered"  name="customerEmail" required aria-label="Email" placeholder="Email">
                    <button type="submit" :class="loading" @click="notifyEmailShowSubmit" class="c-button c-button--cta-black add-to-cart">Notify Me Upon Restock</button>
                </div>
            </form>
        </div>
    </div>
    </div>
</template>

<script>

    import bus from '../index';
    import quantityComp from './quantity.vue';

    export default {
        name: 'buynow',
        components: {quantity: quantityComp},
        props: {
            form:{
                type: Object,
                default: {
                    inputs : [
                        {
                            name: 'action',
                            value: 'commerce/cart/updateCart',
                            type: 'hidden'
                        }
                    ]
                }
            },
            currentuseremail:{
                type: String,
                default: ""
            },
            formaction:{
                type: String,
                default: "commerce/cart/updateCart"
            },
            product: {
                type: Object,
                default: {
                    variants:{},
                    id: 0,
                    stock: 0,
                    title: '',
                    price: '$ notSureATM',
                    salePrice: '$ notSureATM',
                    sale: false,
                    organic: false,
                    strainType: 'sativa',
                    strainTypeFrontVal: 'S'
                }
            },
            variantWeights: {
                type: Array,
                default: () => [
                    {weight: '1g', gramWeight: 1},
                    {weight: '1/8oz', gramWeight: 3.5},
                    {weight: '1/4oz', gramWeight: 7},
                    {weight: '1/2oz', gramWeight: 14},
                    {weight: '1oz', gramWeight: 28}
                ]
            },
        },
        data() {
            return {
                notifyEmailShow: false,
                productData: {},
                picked: '',
                price: '',
                salePrice: '',
                email: '',
                qty: '1',
                loading: '',
            };
        },
        mounted(){
            this.notifyEmailShow = false;
            this.productData = this.product;
            this.price = this.product.price;
            this.salePrice = this.product.salePrice;
            this.email = this.currentuseremail;
        },
        methods: {
            notifyEmailShowSubmit: function(e) {
                if(this.notifyEmailShow){
                   return true;
                }
                this.notifyEmailShow = !this.notifyEmailShow;
            },
            submitForm: function(e){
                this.loading = 'is-loading';
                var config = {
                    responseType: 'json'
                };

                let params = new URLSearchParams();

                var dataXHR = {};

                if(this.picked !== ''){
                    dataXHR['purchasableId'] = this.picked;
                    params.append('purchasableId', this.picked);
                }

                if(this.email !== ''){
                    dataXHR['customerEmail'] = this.email;
                    params.append('customerEmail', this.email);
                }

                if(this.qty !== ''){
                    dataXHR['qty'] = this.qty;
                    params.append('qty', this.qty);
                }

                for (let input of this.form.inputs){

                    if(input.name !== 'customerEmail') {
                        dataXHR[input.name] = input.value;
                        params.append(input.name, input.value);
                    }
                }
                params.append('action', this.productData.formAction);

                dataXHR[window.csrfTokenName] = window.csrfTokenValue; // Append CSRF Token
                params.append(window.csrfTokenName, window.csrfTokenValue);
               /* axios.post(this.productData.formAction, params)
                    .then(function(response) {
                        console.log('saved', response, data)
                    });*/
                Vue.http.post('/', dataXHR)
                    .then(function(response) {
                        console.log(response);
                        if('error' in response.body){
                            let msgData = {
                                type: 'error',
                                msg: response.body.error
                            }
                            bus.$emit('Message',msgData);
                        }
                        else if('success' in response.body){
                            if('cart' in response.body){
                                let ajaxCart = {}

                                let lItems = [];
                                for (let lineItem of response.body.cart.lineItems) {
                                    console.log(lineItem);

                                    lItems.push({
                                        qty: lineItem.qty,
                                        total: '$' + lineItem.qty,
                                        name: lineItem.snapshot.product.title,
                                        uri: lineItem.snapshot.product.uri,
                                    });

                                }
                                ajaxCart.lineItems = lItems;
                                ajaxCart.subTotal = response.body.cart.itemSubtotal;

                                bus.$emit('cartUpdate', ajaxCart);

                                let msgData = {
                                    type: 'success',
                                    msg: this.productData.title + 'has been successfully added to your cart.'
                                }
                                bus.$emit('Message', msgData);
                            }
                            else {
                                this.notifyEmailShow = false;
                                let msgData = {
                                    type: 'success',
                                    msg: response.body.msg
                                }
                                bus.$emit('Message', msgData);
                            }
                        }
                    }).catch(function(error){
                            let msgData = {
                                type: 'error',
                                msg: 'Something went wrong with the request.'
                            }
                            bus.$emit('Message',msgData);
                });

                this.loading = '';
            },
            changeQuantity(qty) {
                    this.qty = qty;
                },
            handleAjax(response){
                bus.$emit('cartUpdate', response);
            },
            variantClick(index){
                if( ! this.product.variants[index].disabled){
                    this.price = this.product.variants[index].price;
                    this.salePrice = this.product.variants[index].salePrice;

                    this.picked = this.product.variants[index].purchasableId;
                    this.productData.variants[index].checked = true;
                }
            },
        },
    }
</script>