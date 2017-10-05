<template>
    <div>
        <div class="tabs gs-tabs is-centered">
            <ul>
                <li :class="{'is-active' : details}"><a @click="selectTab('details')">Details</a></li>
                <li :class="{'is-active' : effects}"><a  @click="selectTab('effects')">Effects</a></li>
                <li :class="{'is-active' : reviews}"><a @click="selectTab('reviews')">Reviews</a></li>
            </ul>
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