/**
 * Created by nathan on 8/11/17.
 */
export default class productTabs {
    constructor(el) {
        this.el = el;
        el.addEventListener("click", evt => this.toggleTab(evt));
        console.log(this.el);
    }
    toggleTab(evt){
        console.log(evt);
        let tabsToHide  = document.getElementsByClassName('product-tab');
        let elementToShow = document.getElementById(this.el.dataset.show);

        elementToShow.style.display = "block";
        this.el.classList.add("c-button--cta-black--active");
    }

}