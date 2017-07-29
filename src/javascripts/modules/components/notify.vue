<template>
    <div class="notify-stock-component">
        <input type="email"  v-if="notifyEmailShow" class="c-input u-width-auto" name="customerEmail" required aria-label="Email" placeholder="Email">
        <button type="submit" @click.stop.prevent="notifyEmailShowSubmit" class="c-button c-button--cta-black add-to-cart">Notify Me Upon Restock</button>
    </div>
</template>

<script>
    export default {
        name: 'notifyRestock',
        data() {
            return {
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
            }
        }
    }
</script>