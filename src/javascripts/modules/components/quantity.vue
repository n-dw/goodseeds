<template>
    <div class="qty-cpt">
        <button @click="decrease" type="button" class="c-button c-button--small c-button--cta-black quantity-control quantity-control--decrement">-</button>
        <input :disabled="disabled == 'true' ? true : false" type="number" name="qty" min="1" maxlength="3" v-model="quantity">
        <button @click="increase"  type="button" class="c-button c-button--small c-button--cta-black quantity-control quantity-control--decrement">+</button>
    </div>
</template>

<script>
    export default {
        name: 'quantity',
        props: {
            qty: {
                type: String,
                default: '0'
            },
            disabled: {
                type: Boolean,
                default: false
            }
        },
        data() {
            return {
                quantity: 0,
                dis: false
            };
        },
        mounted(){
            this.quantity = this.qty;
            this.dis = this.disabled;
        },
        methods: {
            increase() {
                if (this.dis == false)
                    this.quantity++;
            },
            decrease() {
                if (this.dis == false && this.quantity > 1)
                    this.quantity--;
            },
        }
    }
</script>