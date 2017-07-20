Vue.component('quantity', {
    template: `
    <div class="qty-cpt">
    <button @click="decrease" type="button" class="c-button c-button--small c-button--cta-black quantity-control quantity-control--decrement">-</button>
    <input :disabled="disabled == 'true' ? true : false" type="number" name="qty" min="1" maxlength="3" v-model="quantity">
    <button @click="increase"  type="button" class="c-button c-button--small c-button--cta-black quantity-control quantity-control--decrement">+</button>
    </div>
`,
    props:{
        qty:{
            type: String,
            default: 0
        },
        disabled:{
            type: String,
            default: 'false'
        }
    },
    data() {
        return {
            quantity: this.qty,
            dis: this.disabled
        };
    },
    methods: {
        increase: function() {
            if(this.dis == 'false')
                this.quantity++;
        },
        decrease: function() {
            if(this.dis == 'false' && this.quantity > 1)
                this.quantity--;
        },
    }

})
