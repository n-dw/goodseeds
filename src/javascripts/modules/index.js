
import Form from './forms/Form';
import faq from './components/faq';
import quantity from './components/quantity';
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

import Autocomplete from './components/autocomplete.vue';
window.Form = Form;

new Vue({
    el: '#header_wrapper',
    components: {
        autocomplete: Autocomplete,
        faq: faq,
        quantity: quantity
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

/*
  Usage:
  ======

  html
  ----
  <button data-module="disappear">disappear!</button>

  js
  --
  // modules/disappear.js
  export default class Disappear {
    constructor(el) {
      el.style.display = none
    }
  }
*/
