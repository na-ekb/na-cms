<template>
    <span class="ap-dropdown-menu active ap-with-places" role="listbox" id="algolia-places-listbox-0">
                <div class="ap-dataset-places">
                    <span class="ap-suggestions">
                        <suggestion
                            v-for="item in suggestions[locale]"
                            :suggestion="item"
                            :key="item.value"
                            v-on:select="onSelect"
                        ></suggestion>
                    </span>
               </div>
            </span>
</template>
<script>
    import Suggestion from "./Suggestion";
    import axios from "axios";

    export default {
        components: {
            Suggestion
        },
        props: {
            value: String
        },
        data: function(){
            return {
                cancel: null,
                locale: 'en',
                suggestions: []
            }
        },
        watch: {
            value: function(){
                this.sendRequest();
            }
        },
        mounted: function() {
            this.locale = document.getElementsByTagName('html')[0].getAttribute('lang');
        },
        methods: {
            sendRequest() {
                if (this.cancel !== null) {
                    this.cancel();
                    this.cancel = null;
                }

                this.suggestions = [];
                Nova.request(
                    {
                        url: '/nova-dadata-suggestion/query',
                        method: 'POST',
                        data: {
                            q: this.value
                        },
                        cancelToken: new axios.CancelToken((c) => {
                            this.cancel = c;
                        }),
                    })
                    .then((response) => {
                        this.suggestions = response.data
                    })
                    .catch(() => {

                    });
            },
            onSelect(suggestion){
                this.$emit('select', suggestion, this.suggestions);
            }
        }
    }
</script>
