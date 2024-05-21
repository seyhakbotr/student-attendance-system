<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from "@/Components/ui/card";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/Components/ui/select";

import { Label } from "@/Components/ui/label";
import { Button } from "@/Components/ui/button";
import { Bell, CircleUser, Home, LineChart, Menu, Package, Package2, Search, ShoppingCart, Users } from 'lucide-vue-next'

import { Breadcrumb as BreadcrumbType } from "@/types/Breadcrumb";



const props = defineProps<{
  breadcrumbs: BreadcrumbType[],
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
    </AuthenticatedLayout>
</template>
