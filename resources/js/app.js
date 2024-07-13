require('./bootstrap');

import { createApp, onMounted, provide, ref } from 'vue';
import ProductListing from './components/ProductListing';
import AddProduct from './components/AddProduct.vue';

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
app.component('add-product', AddProduct)

app.mount('#app');