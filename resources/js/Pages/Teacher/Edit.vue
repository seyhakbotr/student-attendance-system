<script setup lang="ts">
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
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";
import { Teacher } from "@/Components/teachers/columns";
import { Classrooms } from "../Classroom/Index.vue";

const props = defineProps<{
    teacher: Teacher;
    classrooms: Classrooms[];
    breadcrumbs: BreadcrumbType[];
}>();

const selectedClassroom = ref<Teacher | null>(props.teacher.classroom);
const teacherName = reactive({
    name: props.teacher.name,
    classroom_id: selectedClassroom.value?.id,
});

const updateTeacher = (id: number) => {
    router.patch(`/teacher/${id}`, teacherName, {
        onSuccess: () => {
            toast.success(`Teacher ${teacherName.name} updated`, {
                description: `teacher has been successfully updated.`,
            });
        },
        onError: () => {
            const errorCheck = usePage().props.errors.name;
            console.log("errorCheck", errorCheck);
            toast.error("Failed to Update teacher", {
                description:
                    errorCheck || `There was an error updating the teacher`,
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
                    <CardTitle>Edit Teacher</CardTitle>
                    <CardDescription
                        >Please edit the teacher here.</CardDescription
                    >
                </CardHeader>
                <CardContent>
                    <div class="grid items-center w-full gap-4">
                        <div class="flex flex-col space-y-1.5">
                            <label for="name">teacher Name</label>
                            <Input
                                id="name"
                                v-model="teacherName.name"
                                placeholder="Name of the teacher"
                            />
                        </div>
                        <div class="grid items-center w-full gap-4">
                            <Select v-model="selectedClassroom">
                                <SelectTrigger class="w-[180px]">
                                    <SelectValue>
                                        <span v-if="!selectedClassroom"
                                            >Select a classroom</span
                                        >
                                        <span v-else>{{
                                            selectedClassroom.room_number
                                        }}</span>
                                    </SelectValue>
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectLabel>Classroom</SelectLabel>
                                        <SelectItem
                                            v-for="classroom in props.classrooms"
                                            :key="classroom.id.toString()"
                                            :value="classroom"
                                        >
                                            {{ classroom.room_number }}
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                        </div>
                    </div>
                </CardContent>
                <CardFooter class="flex justify-between px-6 pb-6">
                    <Link href="/teacher">
                        <Button variant="outline">Cancel</Button>
                    </Link>
                    <Button
                        type="submit"
                        @click="updateTeacher(props.teacher.id)"
                        class="w-1/5"
                    >
                        Edit
                    </Button>
                </CardFooter>
            </Card>
        </div>
    </AuthenticatedLayout>
</template>
