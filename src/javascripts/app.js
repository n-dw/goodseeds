
Vue.component('faq', {
    template: `<div :class="{'faq--active' : isActive}" class="faq" :id="faqId">
            <h3 @click="activate" class="faq__question">
                <i :class="icon" ></i>
                <slot name="question"></slot>
            </h3>
            <p class="faq__answer"><slot name="answer"></slot></p>
        </div>`,
    props:{
        open:{
            type: Boolean,
            default: false
        },
        fid: {default: "faq_un"},
        openIcon: {default:'icon-plus-1'},
        closeIcon: {default: 'icon-minus'}
    },
    data() {
        return {
            isActive: false,
            icon: 'icon-plus-1',
            faqId : 'null'
        };
    },
    mounted(){
        this.isActive = this.open;
        this.faqId = this.fid;
        if(this.isActive){
            this.icon = 'icon-minus';
        }
    },
    methods: {
        activate: function() {
            this.isActive = !this.isActive;
            (this.isActive) ? this.icon = 'icon-minus' : this.icon = 'icon-plus-1';
        },
    },

})


new Vue({
    el: '#header',
    delimiters: ['{|', '|}'],
    data: {
        menuOpen: false,
        navMenuStatus: "mobile-nav--closed"
    },
    methods: {
        toggle: function() {
            this.menuOpen = !this.menuOpen;
            this.menuOpen ? this.navMenuStatus = "mobile-nav--open" : this.navMenuStatus = "mobile-nav--closed";
        },
    },
});

new Vue({
    el: '#faqs',
    delimiters: ['${', '}'],
});