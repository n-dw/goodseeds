<template>
    <div v-show="isActive" :aria-hidden="! isActive" :id="id"><slot></slot></div>
</template>

<script>
    export default {
        name: 'tab',
        props: {
            id: { default: null },
            name: { required: true },
            selected: { default: false }
        },
        data() {
            return {
                isActive: false
            };
        },
        mounted(){
            this.isActive = this.selected;
        },
        computed: {
            href() {
                return '#' + this.name.toLowerCase().replace(/ /g, '-');
            }
        }
    }
</script>
