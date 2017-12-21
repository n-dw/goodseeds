<template>
    <div class="bnc">
        <div class="modal" :class="{'is-active': cannabisConversionChartShow}">
            <div class="modal-background"></div>
            <div class="modal-content">
                <table class="table is-striped">
                    <thead>
                    <tr>
                        <th>Our Weights</th>
                        <th>Metric</th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <th colspan="2">Cannabis Conversion Chart</th>
                    </tr>
                    </tfoot>
                    <tbody>
                        <tr>
                            <td>1g</td>
                            <td>1 gram </td>
                        </tr>
                        <tr>
                            <td>1/8 oz</td>
                            <td>3.5 grams</td>
                        </tr>
                        <tr>
                            <td>1/4 oz</td>
                            <td>7 grams</td>
                        </tr>
                        <tr>
                            <td>1/2 oz</td>
                            <td>14 grams</td>
                        </tr>
                        <tr>
                            <td>1 oz</td>
                            <td>28 grams</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <button class="modal-close is-large" @click="toggleChart" aria-label="close"></button>
        </div>
    <div class="product-info-wrapper">
        <div class="media">
            <div class="media-content">
                <div class="product-price-wrapper has-text-centered">
                    <span v-if=" productData.sale" class="sale-price">was  <strike>{{price}}</strike></span>
                    <span class="price"><b>{{ salePrice }}</b></span>
                </div>
            </div>
        </div>
    </div>
    <div class="product-buy-now">
        <div class="buy-now">
            <form method="POST" class="buy-now__add-to-cart-form"  @submit.prevent="submitForm">

                <input v-for="input in formData.inputs" :type="input.type" :name="input.name" :value="input.value">

                <div v-if=" productData.stock > 0" class="in-stock-controls">
                    <a class="conversion-chart-link" @click="toggleChart">Conversion Chart</a>
                        <div class="product-variants">
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

                    <quantity @changequantity="changeQuantity" :disabled="productData.stock < 1" qty="1"></quantity>
                </div>

                <button v-if="productData.stock > 0" type="submit" class="c-button c-button--cta-buy-now add-to-cart"  :class="{'is-loading' : loading}">Buy Now</button>

                <button v-else-if="email != '' && productData.stock < 1" type="submit" :class="{'is-loading' : loading}" class="c-button c-button--cta-black add-to-cart">Notify Me Upon Restock</button>
                <div v-else class="notify-stock-component">
                    <input type="email" v-model="email"  v-show="notifyEmailShow" class="c-input has-text-centered" :class="{'error' : emailError}"  name="customerEmail" required aria-label="Email" placeholder="Email">
                    <button type="submit" :class="{'is-loading' : loading}" @click="notifyEmailShowSubmit" class="c-button c-button--cta-black add-to-cart">Notify Me Upon Restock</button>
                </div>
            </form>
        </div>
    </div>
    </div>
</template>

<script>

    import bus from '../index';
    import quantityComp from './quantity.vue';
    import qs from 'qs';

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
            productrating:{
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
                cannabisConversionChartShow: false,
                productData: {},
                formData: {},
                picked: false,
                price: '',
                salePrice: '',
                email: '',
                qty: '1',
                loading: false,
                emailError: false,
                rating: false
            };
        },
        computed:{
            capitalizeStrainType(){
                return this.product.strainType.charAt(0).toUpperCase() + this.product.strainType.slice(1);
            }
        },
        mounted(){
            this.formData = this.form;
            this.notifyEmailShow = false;
            this.productData = this.product;
            this.price = this.product.price;
            this.salePrice = this.product.salePrice;
            this.rating = this.productrating;
            if(this.$parent.$data.currentUserEmail){
                this.email = this.$parent.$data.currentUserEmail;
            }
            else{
                this.email = this.currentuseremail;
            }

        },
        methods: {
            notifyEmailShowSubmit: function(e) {
                if(this.notifyEmailShow){
                   return true;
                }
                this.notifyEmailShow = !this.notifyEmailShow;
            },
            submitForm: function(){
                this.loading = true;
                //if no vairant picked and its a buy click not a notify
                if(! this.picked && this.productData.stock > 0){
                   let msgData = {
                        type: 'error',
                        msg: 'Please choose a product weight, for ' + this.productData.title
                    }

                    bus.$emit('Message',msgData);
                    this.loading = false;
                    return false;
                }

                var config = {
                    responseType: 'json',
                    headers: {  'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' }
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

                axios.post('/', params, config).then(
                    ({ data }) =>{
                        this.setState({data});
                        this.loading = false;
                    }).catch(
                    err =>
                    {
                        this.setError({err});
                        this.loading = false;
                    });
            },
            toggleChart(){
                this.cannabisConversionChartShow = !this.cannabisConversionChartShow;
            },
            setError(err){
                let msgData = {
                    type: 'error',
                    msg: 'Something went wrong with the request.'
                }
                bus.$emit('Message',msgData);
            },
            setState(dataXHR){
                dataXHR = dataXHR.data;

                let msgData = {};
                if('error' in dataXHR){
                    if('cart' in dataXHR){
                        if('errors' in dataXHR.cart){
                            msgData = {
                                type: 'error',
                                msg: dataXHR.cart.errors.lineItems[0]
                            }
                        }
                    }
                   else{
                        msgData = {
                            type: 'error',
                            msg: dataXHR.error
                        }
                        this.emailError = true;
                        //if invalid reset field
                        this.email = '';
                    }
                    bus.$emit('Message',msgData);
                }
                else if('success' in dataXHR){
                    if('cart' in dataXHR){
                        let ajaxCart = {}
                        let lItems = [];

                        for (let key in dataXHR.cart.lineItems) {

                            let totalCurrency = dataXHR.cart.lineItems[key].total;
                           lItems.push({
                                qty: dataXHR.cart.lineItems[key].qty,
                                total: '$' + totalCurrency,
                                name: dataXHR.cart.lineItems[key].snapshot.product.title,
                                uri: dataXHR.cart.lineItems[key].snapshot.product.uri,
                            });

                        }
                        ajaxCart.lineItems = lItems;
                        ajaxCart.subTotal = dataXHR.cart.itemSubtotal;

                        bus.$emit('cartUpdate', ajaxCart);

                        let msgData = {
                            type: 'success',
                            msg: this.productData.title + ' has been successfully added to your cart.'
                        }
                        bus.$emit('Message', msgData);
                    }
                    else {
                        this.notifyEmailShow = false;
                        this.emailError = false;
                        let msgData = {
                            type: 'success',
                            msg: dataXHR.msg
                        }
                        bus.$emit('Message', msgData);
                    }
                }
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