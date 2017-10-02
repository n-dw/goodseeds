<template>
    <div>
        <div class="tabs">
            <ul>
                <li><a  :class="{'c-button--cta-black--active' : details}" @click="selectTab('details')">Details</a></li>
                <li><a  :class="{'c-button--cta-black--active' : effects}" @click="selectTab('effects')">Effects</a></li>
                <li><a :class="{'c-button--cta-black--active' : reviews}" @click="selectTab('reviews')">Reviews</a></li>
            </ul>
           <!-- <div class="col-xs-4 col-psm-4 col-md-3 col-lg-3">
                <button
                        class="product-tab-button product-cat-card product-cat-card&#45;&#45;link c-button c-button&#45;&#45;cta-black c-button&#45;&#45;medium"
                        :class="{'c-button&#45;&#45;cta-black&#45;&#45;active' : details}"
                        type="button"
                        @click="selectTab('details')"
                        >
                    Details
                </button>
            </div>
            <div class="col-xs-4 col-psm-4 col-md-3 col-lg-3">
                <button data-show="product_effects" data-module="productTabs"
                        class="product-tab-button product-cat-card product-cat-card&#45;&#45;link c-button c-button&#45;&#45;cta-black c-button&#45;&#45;medium"
                        :class="{'c-button&#45;&#45;cta-black&#45;&#45;active' : effects}"
                        type="button"
                        @click="selectTab('effects')"
                        >
                    Effects
                </button>
            </div>
            <div class="col-xs-4 col-psm-4 col-md-3 col-lg-3">
                <button data-show="product_reviews" data-module="productTabs"
                        class="product-tab-button product-cat-card product-cat-card&#45;&#45;link c-button&#45;&#45;tab c-button c-button&#45;&#45;cta-black c-button&#45;&#45;medium"
                        :class="{'c-button&#45;&#45;cta-black&#45;&#45;active' : reviews}"
                        type="button"
                        @click="selectTab('reviews')"
                        >
                    Reviews
                </button>
            </div>-->
        </div>
        <footer class="product-footer">
            <div class="product-details-tabs c-tabs">
                <div class="card-content">
                    <div class="product-description product-tab" id="product_description" v-show="details">
                        <slot name="details"></slot>
                    </div>
                    <div class="product-effects product-tab" id="product_effects" v-show="effects">
                        <slot name="effects"></slot>
                    </div>
                    <div class="product-reviews product-tab" id="product_reviews" v-show="reviews">
                        <slot name="reviews"></slot>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>
<script>
    export default {
        name: 'productTabs',
        data() {
            return {
                currentTab: 'details',
                details: true,
                effects: false,
                reviews: false,
            };
        },
        methods: {
            selectTab(selectedTab) {
                this.details = false;
                this.effects = false;
                this.reviews = false;
                this.currentTab = selectedTab;

                if(selectedTab === 'details')
                    this.details = true;
                else if(selectedTab === 'effects')
                    this.effects = true;
                else if(selectedTab === 'reviews')
                    this.reviews = true;
            }
        }
    }
</script>