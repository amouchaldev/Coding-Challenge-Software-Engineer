<template>
    <h2>Add Product</h2>
    <hr />
    <!-- Success Alert -->
    <div v-if="successMessage" class="alert alert-success" role="alert">
        {{ successMessage }}
    </div>

    <!-- Error Alerts -->
    <div v-if="errorMessages.length > 0" class="alert alert-danger" role="alert">
        <ul class="mb-0">
            <li v-for="error in errorMessages" :key="error">{{ error }}</li>
        </ul>
    </div>
    <form @submit.prevent="submitForm">
        <div class="mb-3">
            <label for="name" class="form-label">Product Name:</label>
            <input type="text" class="form-control" id="name" v-model="product.name" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description:</label>
            <textarea id="description" class="form-control" v-model="product.description"></textarea>
        </div>

        <div class="mb-3">
            <label for="price" class="form-label">Price:</label>
            <input type="number" class="form-control" id="price" v-model.number="product.price" required>
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Image:</label>
            <input type="file" class="form-control" id="image" @change="handleFileUpload" accept="image/*" required>
        </div>

        <div class="mb-3">
            <label for="categories" class="form-label">Categories:</label>
            <select id="categories" class="form-select" v-model="product.categories" multiple required>
                <option v-for="category in categories" :key="category.id" :value="category.id">
                    {{ category.name }}
                </option>
            </select>
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary">Add Product</button>
        </div>
    </form>
</template>

<script>
import { ref, inject } from 'vue';
import axios from 'axios';

export default {
    setup() {
        const categories = inject('categories');
        const product = ref({
            name: '',
            description: '',
            price: null,
            image: null,
            categories: []
        });
        // State for success and error messages
        const successMessage = ref('');
        const errorMessages = ref([]);

        const submitForm = async () => {
            try {
                // Clear previous messages
                successMessage.value = '';
                errorMessages.value = [];

                // FormData to handle file upload
                const formData = new FormData();
                formData.append('name', product.value.name);
                formData.append('description', product.value.description);
                formData.append('price', product.value.price);
                formData.append('image', product.value.image);
                product.value.categories.forEach(categoryId => {
                formData.append('categories[]', categoryId);
                });
                // API call to create the product
                const response = await axios.post('/api/products', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    }
                });

                if(response.status === 201) {
                    successMessage.value = 'Product created successfully.';
                    resetForm();
                }
            } catch (error) {
                console.log(error)
                if (error.response && error.response.status === 422) {
                    // Validation errors from Laravel validation
                    errorMessages.value = Object.values(error.response.data.errors).flat();
                    console.log(Object.values(error.response.data.errors))
                } else {
                    // General error handling
                    errorMessages.value = ['Failed to create product. Please try again later.'];
                }
            }
        };
        // Function to reset the form
        const resetForm = () => {
            product.value.name = '';
            product.value.description = '';
            product.value.price = null;
            product.value.image = ''
            product.value.categories = []
        };

        const handleFileUpload = (event) => {
            product.value.image = event.target.files[0];
        };

        return {
            product,
            categories,
            successMessage,
            errorMessages,
            submitForm,
            resetForm,
            handleFileUpload
        };
    }
};
</script>

<style scoped>
/* Add scoped styles here */
</style>