<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { BarChart } from "../Components/ui/chart-bar";
import { DonutChart } from "../Components/ui/chart-donut";

const props = defineProps<{
    majorCount: number;
    facultyCount: number;
    courseCount: number;
    studentCount: number;
    classroomCount: number;
    teacherCount: number;
    studentCreationData: Record<string, number>;
}>();

const data = [
    {
        name: "Major",
        total: props.majorCount,
    },
    {
        name: "Faculty",
        total: props.facultyCount,
    },
    {
        name: "Course",
        total: props.courseCount,
    },
    {
        name: "Classroom",
        total: props.classroomCount,
    },
    {
        name: "Student",
        total: props.studentCount,
    },
    {
        name: "Teacher",
        total: props.teacherCount
    }
];

const studentDataChart = Object.keys(props.studentCreationData).map(month => ({
    name: new Date(0, parseInt(month) - 1).toLocaleString('default', { month: 'short' }),
    total: props.studentCreationData[month],
}));
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="flex flex-wrap justify-between items-start w-full">
            <div class="w-full md:w-1/2 lg:w-1/3 h-64 mb-8 p-4">
                <h1 class="text-5xl font-extrabold text-primary-accent mb-4">
                    Total Count Of All Table
                </h1>
                <DonutChart index="name" :category="'total'" :data="data" />
            </div>
            <div class="w-full md:w-1/2 lg:w-2/3 h-64 mb-8 p-4">
                <h2 class="text-3xl font-extrabold text-primary-accent mb-4">
                    Students Added Per Month
                </h2>
                <BarChart
                    :data="studentDataChart"
                    index="name"
                    :categories="['total']"
                    :y-formatter="(tick, i) => {
                        return typeof tick === 'number'
                            ? tick.toString()
                            : ''
                    }"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>

