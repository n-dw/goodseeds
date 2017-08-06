<template>
   <!-- <div class="form-filter-wrapper">
        <h5 class="form-filter-toggle is-black" @click="showForm"><i :class="formToggleIcon"></i>Filters</h5>
        <form id="product_filters_form" v-show="showForm" action="/shop" method="POST">
            {% set form = {
            'checkboxes' : {
            'products': [{name: 'Flower', value: '1'},{name: 'Oil', value: '1', label: 'Concentrates'}],
            'strains': [{name: 'sativa', value: '1'},{name: 'indica', value: '1'},{name: 'hybrid', value: '1'}],
            'other': [{name: 'Organic', value: '1'},{name: 'sale',value: '1'},{name: 'stock', value: '1', label: 'In Stock'}],
            },
            'select' : {
            name:'sortBy',
            options: [{name: 'Latest Products', value: 'new'},{name: 'Price: Low to High', value: 'lth'},{name: 'Price: High to Low', value: 'htl'}, {name: 'Best Reviews', value: 'reviewHtl'}]
            }
            } %}

            <div class="row">
                {% for key, checkboxes in form.checkboxes %}
                <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">
                    <div class="box filter-group">
                        <div class="has-text-left"><strong>{{ key | capitalize}}</strong></div>
                        {% for cbk in checkboxes %}
                        <div class="checkbox-wrapper">
                            <input  v-model="formShop.{{ cbk.name }}"
                                    {% if params.productParam == cbk.name or (params[cbk.name] is defined and params[cbk.name] == '1') %}checked{% endif %}
                            type="checkbox"
                            id="{{ cbk.name }}" name="{{ cbk.name }}"
                            value="{{ cbk.value }}">
                            <label for="{{ cbk.name }}">{% if cbk.label is defined and cbk.label != '' %}{{ cbk.label | title }}{% else %}{{ cbk.name | capitalize }}{% endif %}</label>
                        </div>
                        {% endfor %}
                    </div>
                </div>
                {% endfor %}

                <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">
                    <div class="box filter-group">
                        <div class="has-text-left"><strong>Sort By </strong></div>
                        <select v-model="formShop.sortBy" class="c-input-option" aria-label="Sort Product By" name="sortBy" id="sortBy">
                            {% for option in form.select.options %}
                            <option {% if loop.index == 1 and params[form.select.name] is not defined %}selected{% endif %} value="{{ option.value }}"
                                    {% if params[form.select.name] is defined and params[form.select.name] == option.value %}selected{% endif %}>{{ option.name | title }}</option>
                            {% endfor %}
                        </select>
                    </div>
                </div>
            </div>
            <div class="row center-xs">
                <div class="input-wrapper col-xs-12">
                    <div class="form-controls__product-filters">
                        <button :class="{'is-loading' : loading}" class="c-button c-button&#45;&#45;cta-black" type="submit">Submit</button>
                        <button class="c-button c-button&#45;&#45;cta-black" type="reset">Reset</button>
                    </div>
                </div>
            </div>
        </form>
    </div>-->
</template>

<script>

    import bus from '../index';

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
        },
        data() {
            return {
                notifyEmailShow: false,
                productData: {},
                formData: {},
                picked: '',
                price: '',
                salePrice: '',
                email: '',
                qty: '1',
                loading: false,
                showForm: false,
            };
        },
        mounted(){
            this.formData = this.form;
            this.email = this.currentuseremail;
        },
        methods: {
            showForm() {
                this.showForm = !this.showForm;
            },
            submitForm: function(){
                this.loading = true;

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