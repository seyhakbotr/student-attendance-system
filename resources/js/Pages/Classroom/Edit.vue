<script setup lang="ts">
import { reactive } from "vue";
import Label from "@/Components/ui/label/Label.vue";
import { Input } from "@/Components/ui/input";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { RadioGroup, RadioGroupItem } from "@/Components/ui/radio-group";
import { router } from "@inertiajs/vue3";
import { Button } from "@/Components/ui/button";
import type { Student } from "@/Components/payments/columns";
import { Breadcrumb as BreadcrumbType } from "@/types/Breadcrumb";
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/Components/ui/select";
import { usePage } from "@inertiajs/vue3";

const props = defineProps<{
    student: Student;
    breadcrumbs: BreadcrumbType[];
}>();

const { url } = usePage();
const segments = url.split("/");
const lastParameter = segments[segments.length - 1];

const lastParameterAsNumber = parseInt(lastParameter);

if (!isNaN(lastParameterAsNumber)) {
    console.log("Last Parameter:", lastParameterAsNumber); // Log the last parameter as a number
} else {
    console.error("Last Parameter is not a number:", lastParameter); // Log an error if the last parameter is not a number
}
const genderOptions = [
    { id: "male", label: "Male" },
    { id: "female", label: "Female" },
];
const studentForm = reactive({
    name: props.student.name,
    gender: props.student.gender,
    attendances: props.student.attendances.map((attendance) => ({
        course_id: attendance.course_id,
        course_name: attendance.course_name,
        attended: attendance.attendance_status,
    })),
});

const updateStudent = async () => {
    try {
        await router.put(`/student/${props.student.id}`, studentForm);
    } catch (error) {
        console.error("Failed to update student:", error);
    }
};
</script>

<template>
    <AuthenticatedLayout :breadcrumbs="breadcrumbs">
        <div>
            <h1 class="text-5xl text-primary-accent font-extrabold">
                Edit Student
            </h1>

            <form @submit.prevent="updateStudent">
                <Label for="name">Name:</Label>
                <Input
                    type="text"
                    id="name"
                    v-model="studentForm.name"
                    required
                />

                <Label for="gender" class="font-medium">Gender</Label>
                <div class="flex flex-wrap items-center space-x-4">
                    <RadioGroup
                        v-model="studentForm.gender"
                        class="flex space-x-4"
                    >
                        <div
                            v-for="option in genderOptions"
                            :key="option.id"
                            class="flex items-center space-x-2"
                        >
                            <RadioGroupItem
                                :id="'gender' + option.id"
                                :value="option.id"
                            />
                            <label
                                :for="'gender' + option.id"
                                class="text-gray-600"
                                >{{ option.label }}</label
                            >
                        </div>
                    </RadioGroup>
                </div>

                <!-- Attendance records in a responsive grid layout -->
                <div
                    class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 mt-4"
                >
                    <div
                        v-for="attendance in studentForm.attendances"
                        :key="attendance.course_id"
                        class="flex flex-col space-y-2"
                    >
                        <h2 class="text-2xl">{{ attendance.course_name }}</h2>
                        <Label :for="'attendance_' + attendance.course_id"
                            >Attendance Status:</Label
                        >
                        <Select
                            v-model="attendance.attended"
                            :id="'attendance_' + attendance.course_id"
                        >
                            <!-- Changed from attendance_status to attended -->
                            <SelectTrigger class="w-[180px]">
                                <SelectValue
                                    placeholder="Select attendance status"
                                />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectGroup>
                                    <SelectItem value="present"
                                        >Present</SelectItem
                                    >
                                    <SelectItem value="absent"
                                        >Absent</SelectItem
                                    >
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                    </div>
                </div>

                <Button type="submit" class="mt-6">Update</Button>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
