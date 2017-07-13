
<template>
    <div :class="(className ? className + '-wrapper ' : '') + 'autocomplete-wrapper'">
        <input  type="text"
                :id="id"
                :class="(className ? className + '-input ' : '') + 'autocomplete-input'"
                :placeholder="placeholder"
                v-model="type"
                @input="input(type)"
                @dblclick="showAll"
                @blur="hideAll"
                @keydown="keydown"
                @focus="focus"
                autocomplete="off" />

        <div :class="(className ? className + '-list ' : '') + 'autocomplete transition autocomplete-list'" v-show="showList">
            <ul>
                <li v-for="(data, i) in json"
                    transition="showAll"
                    :class="activeClass(i)">

                    <a  href="#"
                        @click.prevent="selectList(data)"
                        @mousemove="mousemove(i)">
                        <b>{{ data[anchor] }}</b>
                        <span>{{ data[label] }}</span>
                    </a>

                </li>
            </ul>
        </div>
    </div>
</template>


<script>

    /*! Copyright (c) 2016 Naufal Rabbani (http://github.com/BosNaufal)
     * Licensed Under MIT (http://opensource.org/licenses/MIT)
     *
     * Vue 2 Autocomplete @ Version 0.0.1
     *
     */

    /*!
     *  javascript-debounce 1.0.0
     *
     *  A lightweight, dependency-free JavaScript module for debouncing functions based on David Walsh's debounce function.
     *
     *  Source code available at: https://github.com/jgarber623/javascript-debounce
     *
     *  (c) 2015-present Jason Garber (http://sixtwothree.org)
     *
     *  javascript-debounce may be freely distributed under the MIT license.
     */

    export default {

        props: {
            id: String,

            // query param
            quantity: {
                type: Number,
                default: 'q'
            },

        },

        data() {
            return {
                showList: false,
                type: "",
                json: [],
                focusList: ""
            };
        },


        methods: {

    }
</script>
