<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { columns } from "@/Components/students/columns";
import DataTable from "@/Components/students/DataTable.vue";
import type { Student } from "@/Components/payments/columns";
import { onMounted, ref } from "vue";
import type { Classrooms } from "@/Pages/Classroom/Index.vue";
import { usePage } from "@inertiajs/vue3";

import { Breadcrumb as BreadcrumbType } from "@/types/Breadcrumb";
const props = defineProps<{
    students: Student[];
    classroom: Classrooms;
    breadcrumbs: BreadcrumbType[];
}>();

const data = ref<Student[]>([]);
const { url } = usePage();
console.log("url", url);
const segments = url.split("/");
const lastParameter = segments[segments.length - 2];

const classroomId = parseInt(lastParameter);
console.log("lastParameterAsNumber", classroomId);
onMounted(() => {
    const classroomFacultyId = props.classroom.faculty_id;
    const classroomMajorId = props.classroom.major_id;

    const filteredStudents = props.students.filter(student =>
        student.faculty_id === classroomFacultyId && student.major_id === classroomMajorId
    );

    data.value = filteredStudents;
});
</script>

<template>
    <AuthenticatedLayout :breadcrumbs="breadcrumbs">
        <h1>import students</h1>
        <div>
            <DataTable :columns="columns" :data="data" :classroomId="classroomId" />
        </div>
    </AuthenticatedLayout>
</template>
