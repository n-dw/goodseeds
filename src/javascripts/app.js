import Form from './modules/forms/Form';
import Autocomplete from '../../node_modules/vue2-autocomplete-js';
import faq from './modules/components/faq';

window.Form = Form;

Vue.component('quantity', {
    template: `
    <div class="qty-cpt">
    <span @click="quantity--" class="c-button c-button--small c-button--cta-black quantity-control quantity-control--decrement">-</span>
    <input type="number" name="qty" min="1" maxlength="3" v-model="quantity">
    <span @click="quantity++" class="c-button c-button--small c-button--cta-black quantity-control quantity-control--decrement">+</span>
    </div>
`,
    props:{
        qty:{
            type: String,
            default: 0
        },
    },
    data() {
        return {
            quantity: this.qty
        };
    },
    
})



new Vue({
    el: '#header_wrapper',
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
            var data = json.data
           /* data.forEach(function(dataItem, index){
                if(dataItem.title.toLowerCase().indexOf('you') !== -1){
                    json.data[index].title = dataItem.title.replace(new RegExp(this.type, 'g'), '<span class="search-string-match">' + this.type + '</span>');
                }
            });*/

            return json.data;
        }
    },
});

