<script setup lang="ts">
import type { Major } from "@/Components/major/columns";
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
import { ref } from "vue";
import { Faculty } from "@/Components/faculty/columns";
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";

const props = defineProps<{
    major: Major;
    faculties: Faculty[]; // Change to the correct type if needed
    breadcrumbs: BreadcrumbType[];
}>();

// Initialize selectedFaculty with the initially selected faculty
const selectedFaculty = ref<Major | null>(props.major.faculty);

// Initialize majorName with the initially selected major name
const majorName = reactive({
    name: props.major.name,
    faculty_id: selectedFaculty.value?.id,
});

const updatemajor = (id: number) => {
    router.patch(`/major/${id}`, majorName, {
        onSuccess: () => {
            toast.success("major Updated", {
                description: `major has been successfully updated.`,
            });
        },
        onError: () => {
            const errorCheck = usePage().props.errors.name;
            console.log("errorCheck", errorCheck);
            toast.error("Failed to Update major", {
                description:
                    errorCheck || `There was an error updating the major`,
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
                    <CardTitle>Edit major</CardTitle>
                    <CardDescription>Please edit the major here.</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid items-center w-full gap-4">
                        <div class="flex flex-col space-y-1.5">
                            <label for="name">major Name</label>
                            <Input
                                id="name"
                                v-model="majorName.name"
                                placeholder="Name of the major"
                            />
                        </div>
                        <div class="grid items-center w-full gap-4">
                            <Select v-model="selectedFaculty">
                                <SelectTrigger class="w-[180px]">
                                    <SelectValue>
                                        <span v-if="!selectedFaculty">Select a faculty</span>
                                        <span v-else>{{ selectedFaculty.name }}</span>
                                    </SelectValue>
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectLabel>Faculty</SelectLabel>
                                        <SelectItem
                                            v-for="faculty in props.faculties"
                                            :key="faculty.id"
                                            :value="faculty"
                                        >
                                            {{ faculty.name }}
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                        </div>
                    </div>
                </CardContent>
                <CardFooter class="flex justify-between px-6 pb-6">
                    <Link href="/major">
                        <Button variant="outline">Cancel</Button>
                    </Link>
                    <Button
                        type="submit"
                        @click="updatemajor(props.major.id)"
                        class="w-1/5"
                    >
                        Edit
                    </Button>
                </CardFooter>
            </Card>
        </div>
    </AuthenticatedLayout>
</template>

