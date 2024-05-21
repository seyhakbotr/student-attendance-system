<script setup lang="ts">
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps<{
    href: string;
    active?: boolean;
}>();

const classes = computed(() =>
    props.active
        ? "flex items-center gap-3 rounded-lg bg-muted px-3 py-2 text-green-500 transition-all hover:text-green-800"
        : "flex items-center gap-3 rounded-lg px-3 py-2 transition-all hover:text-primary",
);
</script>

<template>
    <Link :href="href" :class="classes">
    <slot />
    </Link>
</template>
