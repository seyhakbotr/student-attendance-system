<template>
    <AuthenticatedLayout :breadcrumbs="breadcrumbs">
        <div>
            <h1>Courses for Classroom: {{ classroom.name }}</h1>
            <Table>
                <TableCaption>List of Courses</TableCaption>
                <TableHeader>
                    <TableRow>
                        <TableHead>Course ID</TableHead>
                        <TableHead>Course Name</TableHead>
                        <TableHead>Major Name</TableHead>
                        <TableHead>Action</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="course in courses" :key="course.id">
                        <TableCell>{{ course.id }}</TableCell>
                        <TableCell>{{ course.name }}</TableCell>
                        <TableCell>{{ course.major_name}}</TableCell>
                        <div>
                            <TableCell>
                                <Button
                                    variant="destructive"
                                    @click.prevent="deleteCourse(course.id)"
                                    >Delete</Button
                                >
                            </TableCell>
                        </div>
                    </TableRow>
                </TableBody>
            </Table>
            <div v-if="$page.props.errors.error" class="error-message">
                <p>{{ $page.props.errors.error }}</p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import { Classrooms } from "./Index.vue";
import { Course } from "./Show.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Button from "@/Components/ui/button/Button.vue";
import {
    Table,
    TableBody,
    TableCaption,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table";
import { router } from "@inertiajs/vue3";
import { ref } from "vue";
import { toast } from "vue-sonner";
import { Breadcrumb as BreadcrumbType } from "@/types/Breadcrumb";
import { Major } from "@/Components/major/columns";

const props = defineProps<{
    courses: Course[];
    classroom: Classrooms;
    breadcrumbs: BreadcrumbType[];
}>();


const deleteCourse = (courseId: number) => {
    router.delete(`/course/${courseId}/classroom/${props.classroom.id}`, {
        onSuccess: () => {
            toast.success("Course deleted", {
                description: "Course has been deleted successfully",
            });
            console.log("Course deleted successfully");
        },
        onError: (errors) => {
            toast.error("Error!", {
                description: "Error deleting course",
            });

            console.error("Failed to delete course", errors);
        },
    });
};

//const openEditDialog = (course: Course) => {
//    // Clone the course object to prevent direct mutations
//    editedCourse.value = { ...course };
//};

//const saveChanges = (courseId: number) => {
//    // Perform save operation, e.g., send editedCourse.value to server
//    router.patch(`/course/${courseId}`, editedCourse.value, {
//        onSuccess: () => {
//            toast.success("Course Updated", {
//                description: "Course has been updated successfully",
//            });
//        },
//        onError: () => {
//            toast.error("Error", {
//                description: "Course updated unsuccesfully",
//            });
//        },
//    });
//};
</script>

<style scoped>
.error-message {
    color: red;
    margin-top: 20px;
}
</style>
