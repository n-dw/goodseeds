<template>
    <div class="mini-cart__wrapper">
        <a class="cart-button app-menu__link" @click="showMiniCart = !showMiniCart && lineItems.length > 0">
            <i class="icon-basket cart-count-wrapper app-menu__icon">
                <span class="cart-count count" v-show="lineItems.length">{{lineItems.length}}</span>
            </i>
            <span v-show="mobile">Cart</span>
        </a>
        <article class="mini-cart has-text-left box" :class="{'mini-cart--mobile': mobile}" v-show="showMiniCart">
            <header class="mini-cart__header">
                <a class="close-cart has-text-right u-display--block" @click="showMiniCart = false">
                    <i class="icon-cancel"></i>
                </a>
            </header>
            <div class="mini-cart__content">
                    <ul class="line-items c-list--clean">
                        <li v-for="(lineItem, index) in lineItems" v-if="index < 4">
                            <div class="line-item">
                                <a class="mini-cart__link mini-cart__link--underline" :href="lineItem.uri">
                                    <span class="product-title"><b>{{lineItem.name}}</b></span>,
                                    <span class="product-qty">Qty: {{lineItem.qty}}</span>,
                                    <span class="product-sub-total">{{lineItem.total}}</span>
                                </a>
                            </div>
                        </li>
                    </ul>
                <div class="more-item" v-if="lineItems.length > 3">
                    <a class="mini-cart__link mini-cart__link--underline" href="/shop/cart"> + {{lineItems.length - 3}} more</a>
                </div>
            </div>
            <footer class="mini-cart__footer">
                <div class="subtotal"><b>Subtotal:</b> <span class="price">{{subTotal}}</span></div>
                <div class="mini-cart__links">
                    <a class="c-button c-button--cta-black c-button--small c-button--cart u-display--inline-block" href="/shop/cart"><i class="icon-basket"></i> Cart</a>
                    <a href="/shop/checkout" class="c-button c-button--cta-blue c-button--small u-display--inline-block"><i class="icon-lock"></i>Checkout</a>
                </div>
            </footer>
        </article>
    </div>
</template>

<script>
    import bus from '../index'

    export default {
        name: 'minicart',
        props:{
            cart:{
                type: String,
                default: ''
            },
            mobile:{
                type: Boolean,
                default: false
            },
            subTotalInitial:{
                type: String,
                default: '$0'
            },
            showMiniCartInitial:{
                type: Boolean,
                default: false
            },
        },
        data() {
            return {
                lineItems: '',
                subTotal: '$0',
                showMiniCart: false,
                cartJson: {}
            };
        },
        mounted(){
            this.cartJson = JSON.parse(this.cart);
            this.lineItems = this.cartJson.lineitems;
            this.subTotal = this.cartJson.itemSubtotal;
            this.showMiniCart = this.showMiniCartInitial;
            bus.$on('cartUpdate', (ajaxCart) => {
                console.log(ajaxCart);
                this.updateCartVal(ajaxCart);
            });
        },
        methods:{
            updateCartVal(ajaxCart){
                this.subTotal= '$' + ajaxCart.subTotal;
                this.showMiniCart = true;
                this.lineItems = ajaxCart.lineItems;
            }
        }
    }
</script>