<template>
    <div class="p-6">
        <h1>{{ message }}</h1>
    </div>
</template>

<script setup>
defineProps({
    message: String,
});
</script>
