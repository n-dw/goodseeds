<template>
    <div class="message">
        <div v-if="show" class="notification has-text-centered" :class="{ 'is-danger' :  isErr,  'is-success': !isErr}">
            <button class="icon-cancel"  @click="show = !show"></button>
            <h4 class="notification-header">{{ message }}</h4>
        </div>
    </div>
</template>

<script>

    import bus from '../index';

    export default {
        name: 'message',
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
                message: '',
                isErr: false,
                timer: ''
            };
        },
        created(){

        },
        mounted(){
            this.message = this.msg;
            this.isErr = this.error;
            bus.$on('Message', (message) => {
                this.updateMessage(message);
            });
        },
        methods: {
            hideMessage(){
                this.show = false;
            },
            updateMessage(message){
                 this.isErr = message.type == 'error' ? true : false;
                 this.show = true;
                 this.message = message.msg;
                 this.timer = setTimeout(this.hideMessage, 7000)
            }
        }
    }
</script>
