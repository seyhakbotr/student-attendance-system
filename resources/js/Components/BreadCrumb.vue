<template>
  <Breadcrumb>
    <BreadcrumbList>
      <BreadcrumbItem v-for="(crumb, index) in crumbs" :key="index">
        <BreadcrumbLink v-if="index !== crumbs.length - 1" as-child>
          <Link :href="crumb.url">
            {{ crumb.label }}
          </Link>
        </BreadcrumbLink>
        <BreadcrumbPage class="text-green-500 font-extrabold" v-else>{{ crumb.label }}</BreadcrumbPage>
        <BreadcrumbSeparator v-if="index !== crumbs.length - 1" />
      </BreadcrumbItem>
    </BreadcrumbList>
  </Breadcrumb>
</template>

<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import {
  Breadcrumb,
  BreadcrumbItem,
  BreadcrumbLink,
  BreadcrumbList,
  BreadcrumbPage,
  BreadcrumbSeparator,
} from "@/Components/ui/breadcrumb";
import { Breadcrumb as BreadcrumbType } from "@/types/Breadcrumb";

defineProps({
  crumbs: {
    type: Array as () => BreadcrumbType[],
    required: true,
  },
});
</script>

