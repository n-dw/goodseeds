/**
 * Created by nathan on 8/11/17.
 */
export default class productTabs {
    constructor(el) {
       el.onclick = (e) => {
           let button = e.target;
           let tabsToHide  = document.getElementsByClassName('product-tab');

           for (let i = 0; i < tabsToHide.length; i++ ) {
               tabsToHide[i].style.display = "none";
           }

           let elementToShow = document.getElementById(button.dataset.show);
           elementToShow.style.display = "block";

           let buttons = document.getElementsByClassName('product-tab-button');

           for (let i = 0; i < buttons.length; i++ ) {
               buttons[i].classList.remove("c-button--cta-black--active");
           }

           button.classList.add("c-button--cta-black--active");
       };
    }
}