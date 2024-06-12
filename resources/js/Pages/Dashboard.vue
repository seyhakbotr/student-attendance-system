<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { Breadcrumb as BreadcrumbType } from "@/types/Breadcrumb";
import { BarChart } from "@/components/ui/chart-bar";

const data = [
    {
        name: "Jan",
        total: Math.floor(Math.random() * 2000) + 500,
        predicted: Math.floor(Math.random() * 2000) + 500,
    },
    {
        name: "Feb",
        total: Math.floor(Math.random() * 2000) + 500,
        predicted: Math.floor(Math.random() * 2000) + 500,
    },
    {
        name: "Mar",
        total: Math.floor(Math.random() * 2000) + 500,
        predicted: Math.floor(Math.random() * 2000) + 500,
    },
    {
        name: "Apr",
        total: Math.floor(Math.random() * 2000) + 500,
        predicted: Math.floor(Math.random() * 2000) + 500,
    },
    {
        name: "May",
        total: Math.floor(Math.random() * 2000) + 500,
        predicted: Math.floor(Math.random() * 2000) + 500,
    },
    {
        name: "Jun",
        total: Math.floor(Math.random() * 2000) + 500,
        predicted: Math.floor(Math.random() * 2000) + 500,
    },
    {
        name: "Jul",
        total: Math.floor(Math.random() * 2000) + 500,
        predicted: Math.floor(Math.random() * 2000) + 500,
    },
];
const props = defineProps<{
    breadcrumbs: BreadcrumbType[];
}>();
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout :breadcrumbs="props.breadcrumbs">
        <!-- <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Dashboard
            </h2>

        </template> -->
        <!-- <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        You're logged in!
                    </div>
                </div>
            </div>
        </div> -->
        <h1>Hello</h1>
        <BarChart
            index="name"
            :data="data"
            :categories="['total', 'predicted']"
            :y-formatter="
                (tick, i) => {
                    return typeof tick === 'number'
                        ? `$ ${new Intl.NumberFormat('us').format(tick).toString()}`
                        : '';
                }
            "
            :type="'stacked'"
        />
    </AuthenticatedLayout>
</template>
