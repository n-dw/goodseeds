import Form from './modules/forms/Form';
import Autocomplete from '../../node_modules/vue2-autocomplete-js';

window.Form = Form;

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
        activate: function() {
            this.isActive = !this.isActive;
            (this.isActive) ? this.icon = 'icon-minus' : this.icon = 'icon-plus-1';
        },
    },

})

new Vue({
    el: '#header',
    components: {
        autocomplete: Autocomplete
    },
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
        prerenderLink: function(e) {
            var head = document.getElementsByTagName("head")[0];
            var refs = head.childNodes;
            ref = refs[ refs.length - 1];

            var elements = head.getElementsByTagName("link");
            Array.prototype.forEach.call(elements, function(el, i) {
                if (("rel" in el) && (el.rel === "prerender"))
                    el.parentNode.removeChild(el);
            });

            var prerenderTag = document.createElement("link");
            prerenderTag.rel = "prerender";
            prerenderTag.href = e.currentTarget.href;
            ref.parentNode.insertBefore(prerenderTag,  ref);
        },
        itemSelected: function(data) {
           // ga('send', 'pageview', data.searchUrl);
            window.location.href = data.url;
        },
        processJsonData: function(json) {
            return json.data;
        }
    },
});

