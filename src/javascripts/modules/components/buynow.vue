<template>
    <div class="bnc">
    <div class="product-info-wrapper">
        <div class="media">
            <div class="media-left">
                <div class="product-details-wrapper">
                    <h4 class="product-strain-type" :class="product.strainType">
                        {{ product.strainTypeFrontVal }}
                    </h4>
                    <h4 v-if="product.organic" class="product-strain-type organic">O</h4>
                </div>
            </div>
            <div class="media-content">
                <div class="product-price-wrapper has-text-right">
                    <span v-if=" product.sale" class="sale-price">was  <strike>{{price}}</strike></span>
                    <span class="price"><b>{{ salePrice }}</b></span>
                </div>
            </div>
        </div>
    </div>
    <div class="product-buy-now">
        <div class="buy-now">
            <form method="POST" id="addToCartForm"  @submit.prevent="submitForm">

                    <input v-for="input in form.inputs" type="input.type" name="input.name" value="input.value">
                    <input type="hidden" name="action" value="commerce/cart/updateCart">
                    <input type="hidden" name="productId" :value="productData.id">
                    <input type="hidden" name="productName" :value="productData.title">

                    <input v-if="currentuseremail != ''" type="hidden" name="customerEmail" :value="currentuseremail">

                <div v-if=" productData.stock > 0" class="in-stock-controls">
                    <div class="row">
                        <div class="product-variants col-xs">
                            <template v-for="(variant, index) in productData.variants">
                                <div class="radio-wrapper variant">
                                    <input v-model="picked" :id="variant.place" name="purchasableId" type="radio"
                                       :value="variant.purchasableId"  :disabled="variant.disabled">
                                    <label  class="c-radio c-button--cta-black c-button--small variant-label" :disabled="variant.disabled" :for="variant.place">
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
                            <quantity :disabled="productData.stock < 1" qty="1"></quantity>
                        </div>
                    </div>
                </div>

                <button v-if="productData.stock > 0" type="submit" class="c-button c-button--cta-black add-to-cart">Buy Now</button>

                <button v-else-if="currentuseremail != '' && productData.stock < 1" type="submit" class="c-button c-button--cta-black add-to-cart">Notify Me Upon Restock</button>
                <div v-else class="notify-stock-component">
                    <input type="email"  v-show="notifyEmailShow" class="c-input u-width-auto" name="customerEmail" required aria-label="Email" placeholder="Email">
                    <button type="submit" @click.stop.prevent="notifyEmailShowSubmit" class="c-button c-button--cta-black add-to-cart">Notify Me Upon Restock</button>
                </div>
            </form>
        </div>
    </div>
    </div>
</template>

<script>

    import bus from '../index';
    import quantityComp from './quantity.vue';
    import axios from 'axios';
    <input v-for="input in form.inputs" type="input.type" name="input.name" value="input.value">
        <input type="hidden" name="action" value="commerce/cart/updateCart">
        <input type="hidden" name="productId" :value="productData.id">
        <input type="hidden" name="productName" :value="productData.title">

        <input v-if="currentuseremail != ''" type="hidden" name="customerEmail" :value="currentuseremail">
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
            };
        },
        mounted(){
            this.notifyEmailShow = false;
            this.productData = this.product;
            this.price = this.product.price;
            this.salePrice = this.product.salePrice;
            //this.picked = this.product;
            console.log(this.productData);
            console.log(this.currentuseremail);
        },
        methods: {
            notifyEmailShowSubmit: function(e) {
                if(this.notifyEmailShow){
                   this.submitForm();
                }
                this.notifyEmailShow = !this.notifyEmailShow;
            },
            submitForm: () =>{
                var config = {
                    responseType: 'json'
                };

                var data = {
                    purchasableId: this.picked,
                };
                for (var input in this.form.inputs){
                    data[input.name] = input.value;
                }

                data[window.csrfTokenName] = window.csrfTokenValue; // Append CSRF Token

                axios.post('/', data, config)
                    .then(function(response) {
                        console.log('saved', response, data)
                    });
            },
            togglePassword(){
                bus.$emit('cartUpdate', cart);
            }
        },
    }
</script>