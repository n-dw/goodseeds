/*
  Automatically instantiates modules based on data-attributes
  specifying module file-names.
*/
const moduleElements = document.querySelectorAll('[data-module]')

for (var i = 0; i < moduleElements.length; i++) {
  const el = moduleElements[i]
  const name = el.getAttribute('data-module')
  const Module = require(`./${name}`).default
  new Module(el)
}

import VueSticky from 'vue-sticky'; // Es6 module
import faqComp from './components/faq.vue';
import quantityComp from './components/quantity.vue';
import Autocomplete from './components/autocomplete.vue';
import Password from './components/password.vue';
import Notify from './components/notify.vue';
import Minicart from './components/miniCart.vue';
import BuyNow from './components/buynow.vue';
import Message from './components/message.vue';
import Openclose from './components/openClose.vue';
import productTabs from './components/productTabs.vue';


var bus = new Vue({});
export default bus;

var data = {menuOpen: false,  navMenuStatus: "mobile-nav--closed", menuFixed: false,  showSearch: false, searchIconButtonClass: 'icon-search' };
var components = {ptabs: productTabs, openclose: Openclose, message: Message, autocomplete: Autocomplete, faq: faqComp, quantity: quantityComp, password: Password, notify: Notify, minicart: Minicart, buynow: BuyNow};
var methods = {
    windowScroll(delay, headerPos){
        let last = undefined;
        let timer = undefined;

        return  () => {
            var now = +new Date();

            if (last && now < last + delay) {
                clearTimeout(timer);

                timer = setTimeout(() => {
                    last = now;
                    this.onScroll(headerPos);
                }, delay);
            } else {
                last = now;
                this.onScroll(headerPos);
            }
        };
    },
    onScroll(headerPos) {
        if (window.pageYOffset && window.pageYOffset > headerPos) {
            this.menuFixed = true;
        } else {
            this.menuFixed = false;
        }
    },
    toggle: function() {
        this.menuOpen = !this.menuOpen;
        this.menuOpen ? this.navMenuStatus = "mobile-nav--open" : this.navMenuStatus = "mobile-nav--closed";
    },
    itemSelected: function(data) {
        // ga('send', 'pageview', data.searchUrl);
        if(data.url) {
            window.location.href = data.url;
        }
    },
    processJsonData: function(json) {
        return json.data;
    },
    toggleSearchBar: function(json) {
        this.showSearch = !this.showSearch;
        this.searchIconButtonClass = (this.showSearch) ? 'icon-cancel' : 'icon-search';
        bus.$emit('searchBarToggle', this.showSearch);
    },
}

thcpost.vueParams.data = Object.assign(thcpost.vueParams.data, data);
thcpost.vueParams.components = Object.assign(thcpost.vueParams.components, components);
thcpost.vueParams.methods = Object.assign(thcpost.vueParams.methods, methods);


new Vue({
    el: '.vue-app',
    components: thcpost.vueParams.components,
    directives: {'sticky': VueSticky},
    delimiters: ['{|', '|}'],
    created(){
        let header = document.getElementById('header');
        let rect = header.getBoundingClientRect();
        let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        let headerPos  = rect.top + scrollTop;
        window.addEventListener('scroll', this.windowScroll(25, headerPos))
    },
    mounted(){

    },
    data: thcpost.vueParams.data,
    methods: thcpost.vueParams.methods,
});
