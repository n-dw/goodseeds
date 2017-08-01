<template>
    <div class="buy-now">
        <form method="POST" id="addToCartForm">
            <input v-if="product.stock > 0" type="hidden" name="action" value="commerce/cart/updateCart">
            <template v-else>
                <input type="hidden" name="action" value="inStockNotifier/notification/requestRestockNotification">
                <input type="hidden" name="productId" :value="product.id">
                <input type="hidden" name="productName" :value="product.title">

                <input v-if="currentUserEmail != ''" type="hidden" name="customerEmail" :value="currentUserEmail">
            </template>
            <div v-if=" product.stock > 0" class="in-stock-controls">
                <div class="row">
                    <div class="product-variants col-xs">
                        <template v-for="variant in product.variants">
                            <div class="radio-wrapper variant">
                                <input :checked="variant.checked" :id="variant.id" name="purchasableId" type="radio"
                                   :value="variant.purchasableId"  :disabled="variant.disabled">
                                <label @click="toggleVariant" class="c-radio c-button--cta-black c-button--small variant-label" :disabled="variant.disabled" for="variant.id">
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
                        <quantity :disabled="product.stock < 1" qty="1"></quantity>
                    </div>
                </div>
            </div>

            <button v-if="product.stock > 0" type="submit" class="c-button c-button--cta-black add-to-cart">Buy Now</button>

            <button v-else-if="currentUserEmail != '' && product.stock < 1" type="submit" class="c-button c-button--cta-black add-to-cart">Notify Me Upon Restock</button>
            <div v-else class="notify-stock-component">
                <input type="email"  v-show="notifyEmailShow" class="c-input u-width-auto" name="customerEmail" required aria-label="Email" placeholder="Email">
                <button type="submit" @click.stop.prevent="notifyEmailShowSubmit" class="c-button c-button--cta-black add-to-cart">Notify Me Upon Restock</button>
            </div>
        </form>
    </div>
</template>

<script>

    import bus from '../index';
    import quantityComp from './quantity.vue';

    export default {
        name: 'buynow',
        components: {quantity: quantityComp},
        props: {
            currentUserEmail:{
                type: String,
                default: ""
            },
            product: {
                type: Object,
                default: {
                    variants:{},
                    id: 0,
                    stock: 0,
                    title: '',


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
            id: {
                type: String,
                default: 'password'
            }
        },
        data() {
            return {
                pwdVal: '',
                toggleLabel: 'Show',
                show: false,
                notifyEmailShow: false
            };
        },
        mounted(){
            this.notifyEmailShow = false;
        },
        methods: {
            notifyEmailShowSubmit: function(e) {
                if(this.notifyEmailShow){
                    e.target.click();
                }
                this.notifyEmailShow = !this.notifyEmailShow;
            },
            togglePassword(){
                bus.$emit('cartUpdate', cart);
            }
        },
    }
</script>