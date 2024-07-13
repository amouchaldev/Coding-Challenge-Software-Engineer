<template>
    <div>
        <div class="row">
            <div class="col-md-8 col-lg-6">
                <div class="d-flex">
                    <div class="w-100 me-2">
                        <label for="sort">Sort by price:</label>
                        <select v-model="sortOrder" class="form-control form-control-sm">
                            <option value="asc">Ascending</option>
                            <option value="desc">Descending</option>
                        </select>
                    </div>
                    <div class="w-100">
                        <label for="category">Filter by category:</label>
                        <select v-model="selectedCategory" class="form-control form-control-sm">
                            <option value="">All</option>
                            <option v-for="category in categories" :key="category.id" :value="category.id">
                                {{ category.name }}
                            </option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <hr />
        <table class="table">
            <thead>
                <tr>
                    <th>image</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Category</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="product in products" :key="product.id">
                    <td class="align-middle">
                        <img :src="product.thumbnail" :alt="product.name" class="img-thumbnail" />
                    </td>
                    <td class="align-middle">
                        {{ product.name }}
                        <br />
                        <small class="opacity-75">{{ product.description }}</small>
                    </td>
                    <td class="align-middle">${{ product.price }}</td>
                    <td class="align-middle">
                        <span v-for="category in product.categories" :key="category.id" class="badge bg-primary me-1">{{
                            category.name }}</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
import axios from "axios";
import { ref, onMounted, watch, inject } from "vue";

export default {
    setup() {
        const categories = inject("categories"); // Inject the categories from the app setup
        const products = ref([]);
        const sortOrder = ref("");
        const selectedCategory = ref("");

        const fetchProducts = async () => {
            const response = await axios.get("/api/products", {
                params: {
                    category: selectedCategory.value,
                    sort: sortOrder.value,
                },
            });
            products.value = response.data;
        };

        watch([selectedCategory, sortOrder], () => {
            fetchProducts();
        });

        onMounted(fetchProducts);

        return {
            products,
            categories,
            sortOrder,
            selectedCategory,
            fetchProducts,
        };
    },
};
</script>

<style scoped>
img {
    width: 70px;
    height: 70px;
}
</style>
