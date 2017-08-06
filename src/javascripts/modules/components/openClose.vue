<template>
    <div class="open-close-component">
        <i :class="icon" class="occ-icon" @click="toggleOpenClose" ></i>
        <slot name="title"></slot>
        <div v-show="open" class="open-close__content">
            <slot name="content"></slot>
        </div>
    </div>
</template>


<script>
    export default {
        name: 'openclose',
        props:{
            opened:{
                type: Boolean,
                default: true
            },
            openIcon: {default:'icon-plus-1'},
            closeIcon: {default: 'icon-minus'}
        },
        data() {
            return {
                open: true,
                icon: 'icon-plus-1',
            };
        },
        mounted(){
            this.open = this.opened;
            if(this.open){
                this.icon = this.closeIcon;
            }
        },
        methods: {
            toggleOpenClose() {
                this.open = !this.open;
                (this.open) ? this.icon = this.closeIcon : this.icon = this.openIcon;
            },
        },
    }
</script>