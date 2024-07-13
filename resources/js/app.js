require('./bootstrap');

import { createApp, onMounted, provide, ref } from 'vue';
import ProductListing from './components/ProductListing';

const app = createApp({
    setup() {
        const categories = ref([]);

        const fetchCategories = async () => {
            const response = await axios.get('/api/categories');
            categories.value = response.data;
        };

        onMounted(fetchCategories);

        provide('categories', categories);

        return {
            categories
        };
    },
});

app.component('product-listing', ProductListing)

app.mount('#app');