<script setup lang="ts">
import type { Faculty } from "@/Components/faculty/columns";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {
    Card,
    CardTitle,
    CardHeader,
    CardDescription,
    CardContent,
} from "@/Components/ui/card";
import { Button } from "@/Components/ui/button";
import { Input } from "@/Components/ui/input";
import { reactive } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { toast } from "vue-sonner";
import { Link } from "@inertiajs/vue3";
import { Breadcrumb as BreadcrumbType } from "@/types/Breadcrumb";
const props = defineProps<{
    faculty: Faculty;
    breadcrumbs: BreadcrumbType[];
}>();

const facultyName = reactive({
    name: props.faculty.name,
});

const updateFaculty = (id: number) => {
    router.patch(`/faculty/${id}`, facultyName, {
        onSuccess: () => {
            toast.success("Faculty Updated", {
                description: `Faculty has been successfully updated.`,
            });
        },
        onError: () => {
            const errorCheck = usePage().props.errors.name;
    console.log("errorCheck", errorCheck);
            toast.error("Failed to Update Faculty", {
                description:
                    errorCheck || `There was an error updating the faculty`,
            });
        },
    });
};
</script>

<template>
    <AuthenticatedLayout :breadcrumbs="breadcrumbs">
        <div class="flex justify-center items-center h-full">
            <Card class="w-full md:w-[350px]">
                <CardHeader>
                    <CardTitle>Edit Faculty</CardTitle>
                    <CardDescription
                        >Please edit the faculty here.</CardDescription
                    >
                </CardHeader>
                <CardContent>
                    <div class="grid items-center w-full gap-4">
                        <div class="flex flex-col space-y-1.5">
                            <label for="name">Faculty Name</label>
                            <Input
                                id="name"
                                v-model="facultyName.name"
                                placeholder="Name of the faculty"
                            />
                        </div>
                    </div>
                </CardContent>
                <CardFooter class="flex justify-between px-6 pb-6">
                    <Link href="/faculty">
                        <Button variant="outline">Cancel</Button>
                    </Link>
                    <Button
                        type="submit"
                        @click="updateFaculty(props.faculty.id)"
                        class="w-1/5"
                        >Edit</Button
                    >
                </CardFooter>
            </Card>
        </div>
    </AuthenticatedLayout>
</template>
