<template>
    <div>
        <div class="product-details-tabs c-tabs">
            <ul class="c-list--clean row">
                <li v-for="tab in tabs" class="col-xs c-tab" :class="{ 'c-tab--active': tab.isActive }">
                    <a :href="tab.href" @click="selectTab(tab)">{{ tab.name }}</a>
                </li>
            </ul>
        </div>

        <div class="c-tabs__content">
            <slot></slot>
        </div>
    </div>
</template>
<script>
    //import Tab from './tab.vue';
    export default {
        name: 'tabs',
        //components: {tab: Tab},
        data() {
            return {
                tabs: []
            };
        },
        created() {
           this.tabs = this.$children;
           console.log(this);
        },
        methods: {
            selectTab(selectedTab) {
                this.tabs.forEach(tab => {
                    tab.isActive = (tab.href == selectedTab.href);
                });
            }
        }
    }
</script>