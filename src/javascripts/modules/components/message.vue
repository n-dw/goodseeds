<template>
    <div class="message">
        <div v-if="show" class="notification has-text-centered" :class="{ 'is-danger' :  error,  'is-success': !error}">
            <button class="delete"  @click="show = !show"></button>
            {{ message }}
        </div>
    </div>
</template>

<script>

    import bus from '../index';

    export default {
        name: 'quantity',
        props: {
            msg: {
                type: String,
                default: ''
            },
            error: {
                type: Boolean,
                default: false
            }
        },
        data() {
            return {
                show: false,
                message: ''
            };
        },
        mounted(){
            this.message = this.msg;
            bus.$on('Message', (message) => {
                console.log(message);
                this.updateMessage(message);
            });
        },
        methods: {
            updateMessage(message){
                 this.error = message.type == 'error' ? true : false;
                 this.show = true;
                 this.message = message.msg;
            }
        }
    }
</script>