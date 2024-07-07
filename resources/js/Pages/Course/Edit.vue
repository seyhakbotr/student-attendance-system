<script setup lang="ts">
import type { Course } from "@/Components/courses/columns";
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
import ComboBox from "@/Components/ComboBox.vue";
import Label from "@/Components/ui/label/Label.vue";
import { Major } from "@/Components/major/columns";
const props = defineProps<{
    course: Course;
    majors: Major[];
    breadcrumbs: BreadcrumbType[];
}>();

const courseName = reactive({
    name: props.course.name,
    major_id: props.course.major_id,
});

function handleMajorSelect(id: number) {
    courseName.major_id = id;
}
const updatecourse = (id: number) => {
    router.patch(`/course/${id}`, courseName, {
        onSuccess: () => {
            toast.success("course Updated", {
                description: `course has been successfully updated.`,
            });
        },
        onError: () => {
            const errorCheck = usePage().props.errors.name;
            console.log("errorCheck", errorCheck);
            toast.error("Failed to Update course", {
                description:
                    errorCheck || `There was an error updating the course`,
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
                    <CardTitle>Edit course</CardTitle>
                    <CardDescription
                        >Please edit the course here.</CardDescription
                    >
                </CardHeader>
                <CardContent>
                    <div class="grid items-center w-full gap-4">
                        <div class="flex flex-col space-y-1.5">
                            <label for="name">Course Name</label>
                            <Input
                                id="name"
                                v-model="courseName.name"
                                placeholder="Name of the course"
                            />
                        </div>

                        <div class="flex flex-col space-y-1.5">
                            <Label for="major" class="text-left py-2">
                                Major
                            </Label>
                            <ComboBox
                                :items="props.majors"
                                label="Major"
                                class="col-span-3"
                                :selectedId="courseName.major_id"
                                @update:selectedItemId="handleMajorSelect"
                            />
                        </div>
                    </div>
                </CardContent>
                <CardFooter class="flex justify-between px-6 pb-6">
                    <Link href="/course">
                        <Button variant="outline">Cancel</Button>
                    </Link>
                    <Button
                        type="submit"
                        @click="updatecourse(props.course.id)"
                        class="w-1/5"
                        >Edit</Button
                    >
                </CardFooter>
            </Card>
        </div>
    </AuthenticatedLayout>
</template>
