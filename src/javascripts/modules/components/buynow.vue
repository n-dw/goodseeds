<template>
    <div class="c-password-test-toggle">
        <input v-if="show" class="c-input" :class="{'error' : hasError}" :name="name" :id="id" required placeholder="********" type="text" v-model="pwdVal">
        <input v-if="!show" class="c-input" :class="{'error' : hasError}":name="name" :id="id" required placeholder="********" type="password" v-model="pwdVal">
        <button type="button" class="pwd-toggle" @click.self.prevent="togglePassword">{{toggleLabel}}</button>
    </div>
</template>

<script>
    export default {
        name: 'password',
        props:{
            hasError:{
                type: Boolean,
                default: false
            },
            name:{
                type: String,
                default: 'password'
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
                show: false
            };
        },

        methods: {
            togglePassword(){
                this.show = !this.show
                this.toggleLabel === 'Hide' ? this.toggleLabel = 'Show' :  this.toggleLabel = 'Hide';
            }
        },
    }
</script>