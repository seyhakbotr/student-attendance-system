<script setup lang="ts">
import type { Faculty } from "@/Components/faculty/columns";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {
    Card,
    CardTitle,
    CardHeader,
    CardDescription,
    CardContent,
    CardFooter,
} from "@/Components/ui/card";
import { Button } from "@/Components/ui/button";
import { Input } from "@/Components/ui/input";
import { reactive, computed, ref } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { toast } from "vue-sonner";
import { Link } from "@inertiajs/vue3";
import { Breadcrumb as BreadcrumbType } from "@/types/Breadcrumb";
import type { Student } from "@/Components/payments/columns";
import type { Major } from "@/Components/major/columns";
import type { Classroom } from "@/Pages/Classroom/Index.vue";
import ComboBox from "@/Components/ComboBox.vue";
import { RadioGroup, RadioGroupItem } from "@/components/ui/radio-group";
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
    student: Student;
    faculties: Faculty[];
    classrooms: Classroom[];
    majors: Major[];
    breadcrumbs: BreadcrumbType[];
}>();

const StudentName = reactive({
    name: props.student.name,
    faculty_id: props.student.faculty_id,
    major_id: props.student.major_id,
    year_id: props.student.year_id,
    semester_id: props.student.semester_id,
    classroom_id: props.student.classroom_id,
    gender: props.student.gender,
});

const updateStudent = (id: number) => {
    console.log("Student Form", StudentName);
    router.put(`/students/${id}`, StudentName, {
        onSuccess: () => {
            toast.success("Student Updated", {
                description: `Student has been successfully updated.`,
            });
        },
        onError: () => {
            const errorCheck = usePage().props.errors.name;
            toast.error("Failed to Update Student", {
                description:
                    errorCheck || `There was an error updating the Student`,
            });
        },
    });
};

function handleFacultySelect(id: number) {
    StudentName.faculty_id = id;
    StudentName.major_id = null
}
function handleMajorSelect(id: number) {
    StudentName.major_id = id;
    console.log("major id", StudentName.major_id);
}

const filteredMajors = computed(() => {
    if (StudentName.faculty_id === null) {
        return [];
    }
    return props.majors.filter(
        (major) => major.faculty_id === StudentName.faculty_id,
    );
});
const yearOptions = [
    { id: 1, label: "Year 1" },
    { id: 2, label: "Year 2" },
    { id: 3, label: "Year 3" },
    { id: 4, label: "Year 4" },
];

const genderOptions = [
    { id: "male", label: "Male" },
    { id: "female", label: "Female" },
];

const semesterOptions = [
    { id: 1, label: "Semester 1" },
    { id: 2, label: "Semester 2" },
];
</script>

<template>
    <AuthenticatedLayout :breadcrumbs="props.breadcrumbs">
        <div class="flex justify-center items-center h-full py-10">
            <div class="rounded-lg p-6 w-full max-w-2xl">
                <h2 class="text-2xl font-bold mb-6">Edit Student</h2>
                <div class="grid gap-6">
                    <div class="flex flex-col space-y-1.5">
                        <label for="name" class="font-medium"
                            >Student Name</label
                        >
                        <Input
                            id="name"
                            v-model="StudentName.name"
                            placeholder="Name of the Student"
                        />
                    </div>
                    <div class="flex flex-col space-y-1.5">
                        <label for="faculty" class="font-medium">Faculty</label>
                        <ComboBox
                            :items="props.faculties"
                            label="Faculty"
                            class="col-span-3"
                            :selectedId="StudentName.faculty_id"
                            @update:selectedItemId="handleFacultySelect"
                        />
                    </div>
                    <div class="flex flex-col space-y-1.5">
                        <label for="major" class="font-medium">Major</label>
                        <ComboBox
                            :items="filteredMajors"
                            label="Major"
                            class="col-span-3"
                            :selectedId="StudentName.major_id"
                            @update:selectedItemId="handleMajorSelect"
                        />
                    </div>
                    <div class="flex flex-col space-y-1.5">
                        <label for="year" class="font-medium">Year</label>
                        <div class="flex flex-wrap items-center space-x-4">
                            <RadioGroup
                                v-model="StudentName.year_id"
                                class="flex space-x-4"
                            >
                                <div
                                    v-for="option in yearOptions"
                                    :key="option.id"
                                    class="flex items-center space-x-2"
                                >
                                    <RadioGroupItem
                                        :id="'year' + option.id"
                                        :value="option.id"
                                    />
                                    <label
                                        :for="'year' + option.id"
                                        class="text-gray-600"
                                        >{{ option.label }}</label
                                    >
                                </div>
                            </RadioGroup>
                        </div>
                    </div>
                    <div class="flex flex-col space-y-1.5">
                        <label for="semester" class="font-medium"
                            >Semester</label
                        >
                        <div class="flex flex-wrap items-center space-x-4">
                            <RadioGroup
                                v-model="StudentName.semester_id"
                                class="flex space-x-4"
                            >
                                <div
                                    v-for="option in semesterOptions"
                                    :key="option.id"
                                    class="flex items-center space-x-2"
                                >
                                    <RadioGroupItem
                                        :id="'semester' + option.id"
                                        :value="option.id"
                                    />
                                    <label
                                        :for="'semester' + option.id"
                                        class="text-gray-600"
                                        >{{ option.label }}</label
                                    >
                                </div>
                            </RadioGroup>
                        </div>
                    </div>
                    <div class="flex flex-col space-y-1.5">
                        <label for="gender" class="font-medium">Gender</label>
                        <div class="flex flex-wrap items-center space-x-4">
                            <RadioGroup
                                v-model="StudentName.gender"
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
                    </div>
                </div>
                <div class="flex justify-end space-x-4 mt-6">
                    <Link href="/student">
                        <Button variant="outline">Cancel</Button>
                    </Link>
                    <Button
                        type="submit"
                        @click="updateStudent(props.student.id)"
                        class="w-32"
                        >Edit</Button
                    >
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
