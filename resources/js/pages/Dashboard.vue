<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { dashboardImages } from '@/constants/images.js';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col flex-1 h-full gap-4 p-4 overflow-x-auto rounded-xl">

            <!-- 3 cartes -->
            <div class="grid gap-4 md:grid-cols-3 auto-rows-min">

                <div class="relative overflow-hidden border aspect-video rounded-xl border-sidebar-border/70 dark:border-sidebar-border">
                    <img
                        :src="dashboardImages.card1"
                        class="object-cover w-full h-full"
                        alt="Card 1"
                    />
                </div>

                <div class="relative overflow-hidden border aspect-video rounded-xl border-sidebar-border/70 dark:border-sidebar-border">
                    <img
                        :src="dashboardImages.card2"
                        class="object-cover w-full h-full"
                        alt="Card 2"
                    />
                </div>

                <div class="relative overflow-hidden border aspect-video rounded-xl border-sidebar-border/70 dark:border-sidebar-border">
                    <img
                        :src="dashboardImages.card3"
                        class="object-cover w-full h-full"
                        alt="Card 3"
                    />
                </div>

            </div>

            <!-- Grande section -->
            <div class="rounded-xl border-sidebar-border/70 md:min-h-min dark:border-sidebar-border relative min-h-[100vh] flex-1 border">
                <img
                    :src="dashboardImages.main"
                    class="object-cover w-full h-full"
                    alt="Main Dashboard"
                />
            </div>

        </div>
    </AppLayout>
</template>
