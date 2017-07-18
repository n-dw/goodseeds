<template>
    <div :class="{'faq--active' : isActive}" class="faq" :id="faqId">
        <h3 @click="activate" class="faq__question">
            <i :class="icon" ></i>
            <slot name="question"></slot>
        </h3>
        <p class="faq__answer"><slot name="answer"></slot></p>
    </div>
</template>


<script>
    export default {
        name: 'faq',
        props:{
            open:{
                type: Boolean,
                default: false
            },
            faqid: {default: "faq_un"},
            openIcon: {default:'icon-plus-1'},
            closeIcon: {default: 'icon-minus'}
        },
        data() {
            return {
                isActive: false,
                icon: 'icon-plus-1',
                faqId : 'faq_#'
            };
        },
        mounted(){
            this.isActive = this.open;
            this.faqId = this.faqid;
            if(this.isActive){
                this.icon = 'icon-minus';
            }
        },
        methods: {
            activate() {
                this.isActive = !this.isActive;
                (this.isActive) ? this.icon = 'icon-minus' : this.icon = 'icon-plus-1';
            },
        },
    }
</script>
