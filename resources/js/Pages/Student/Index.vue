<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import type { Student } from "@/Components/payments/columns";
import { onMounted, ref, reactive } from "vue";
import { columns } from "@/Components/students/columns";
import DataTable from "@/Components/students/DataTable.vue";
import { Button } from "@/components/ui/button";
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
    DialogClose,
} from "@/components/ui/dialog";
import { Input } from "@/components/ui/input";
import Label from "@/Components/ui/label/Label.vue";
import { RadioGroup, RadioGroupItem } from "@/components/ui/radio-group";
import { router, usePage } from "@inertiajs/vue3";
import { toast } from "vue-sonner";
import { Breadcrumb as BreadcrumbType } from "@/types/Breadcrumb";
import ComboBox from "@/Components/ComboBox.vue";
import type { Faculty } from "@/Components/faculty/columns";
import type { Major } from "@/Components/major/columns";
import type { Classrooms } from "@/Pages/Classroom/Index.vue";
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";
import { computed } from "vue";
const props = defineProps<{
    students: Student[];
    classrooms: Classrooms[];
    breadcrumbs: BreadcrumbType[];
    faculties: Faculty[];
    majors: Major[];
}>();

const studentForm = reactive({
    name: "",
    faculty_id: 0,
    major_id: 0,
    gender: "male",
    year_id: "1", // Default value for year
    semester_id: "1",
    classroom_id: null,
});

const data = ref<Student[]>([]);
const createStudent = () => {
    console.log("student form ", studentForm);
    router.post("/student", studentForm, {
        onSuccess: () => {
            toast.success("Student Added Succesfully");
            fetchStudent();
        },
        onError: () => {
            toast.error("Failed to added student");
        },
    });
};
const fetchStudent = async () => {
    try {
        const response = await router.get("/student");
        data.value = response.data;
    } catch (error) {
        console.error("Error fetching faculties:", error);
    }
};
onMounted(() => {
    data.value = props.students;
});

function handleFacultySelect(id: number) {
    studentForm.faculty_id = id;
    studentForm.major_id = null;
}

function handleMajorSelect(id: number) {
    studentForm.major_id = id;
}
const filteredMajors = computed(() => {
    if (studentForm.faculty_id === null) {
        return [];
    }
    return props.majors.filter(
        (major) => major.faculty_id === studentForm.faculty_id,
    );
});
</script>

<template>
    <AuthenticatedLayout :breadcrumbs="props.breadcrumbs">
        <h1 class="text-6xl font-extrabold text-primary-accent">
            Student List
        </h1>
        <Dialog>
            <div>
                <DialogTrigger as-child>
                    <Button variant="secondary" class="sm:p-2 sm:text-sm">
                        Add Student
                    </Button>
                </DialogTrigger>
            </div>
            <DialogContent class="sm:max-w-[425px]">
                <form @submit.prevent="createStudent">
                    <DialogHeader>
                        <DialogTitle>Add Student</DialogTitle>
                        <DialogDescription>
                            Create a new Student. Click save when you're done
                        </DialogDescription>
                    </DialogHeader>
                    <div class="grid gap-4 py-4">
                        <div class="grid grid-cols-4 items-center gap-4">
                            <Label for="name" class="text-right">
                                Student Name
                            </Label>
                            <Input
                                id="name"
                                placeholder="John Doe..."
                                class="col-span-3"
                                v-model="studentForm.name"
                            />
                        </div>
                        <div class="grid grid-cols-4 items-center gap-4">
                            <Label for="faculty" class="text-right">
                                Faculty
                            </Label>
                            <ComboBox
                                :items="props.faculties"
                                label="Faculty"
                                class="col-span-3"
                                v-model="studentForm.faculty_id"
                                @update:selectedItemId="handleFacultySelect"
                            />
                        </div>
                        <div class="grid grid-cols-4 items-center gap-4">
                            <Label for="major" class="text-right">
                                Major
                            </Label>
                            <ComboBox
                                :items="filteredMajors"
                                label="Major"
                                class="col-span-3"
                                v-model="studentForm.major_id"
                                @update:selectedItemId="handleMajorSelect"
                            />
                        </div>
                        <div class="grid grid-cols-4 items-center gap-4">
                            <Label for="year" class="text-right"> Year </Label>
                            <div class="flex items-center space-x-4">
                                <RadioGroup
                                    class="flex flex-row"
                                    v-model="studentForm.year_id"
                                    default-value="Year 1"
                                >
                                    <div class="flex items-center space-x-4">
                                        <RadioGroupItem id="year1" value="1" />
                                        <Label for="year1">Year 1</Label>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <RadioGroupItem id="year2" value="2" />
                                        <Label for="year2">Year 2</Label>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <RadioGroupItem
                                            id="year3"
                                            value="3"
                                        />
                                        <Label for="year3">Year 3</Label>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <RadioGroupItem id="year4" value="4" />
                        <Label for="year4">Year 4</Label>
                                    </div>
                                </RadioGroup>
                            </div>
                        </div>
                        <div class="grid grid-cols-4 items-center gap-4">
                            <Label for="semester" class="text-right">
                                Semester
                            </Label>
                            <div class="flex items-center space-x-4">
                                <RadioGroup
                                    class="flex flex-row"
                                    v-model="studentForm.semester_id"
                                    default-value="1"
                                >
                                    <div class="flex items-center space-x-4">
                                        <RadioGroupItem
                                            id="semester1"
                                            value="1"
                                        />
                                        <Label for="year1">Semester 1</Label>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <RadioGroupItem
                                            id="semester2"
                                            value="2"
                                        />
                                        <Label for="year2">Semester 2</Label>
                                    </div>
                                </RadioGroup>
                            </div>
                        </div>

                        <div class="grid grid-cols-4 items-center gap-4">
                            <Label for="Gender" class="text-right">
                                Gender
                            </Label>
                            <div class="flex items-center space-x-4">
                                <RadioGroup
                                    class="flex flex-row"
                                    v-model="studentForm.gender"
                                    default-value="male"
                                >
                                    <div class="flex items-center space-x-2">
                                        <RadioGroupItem
                                            id="male"
                                            value="male"
                                        />
                                        <Label for="male">Male</Label>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <RadioGroupItem
                                            id="female"
                                            value="female"
                                        />
                                        <Label for="female">Female</Label>
                                    </div>
                                </RadioGroup>
                            </div>
                        </div>
                    </div>
                    <DialogFooter class="flex items-end">
                        <DialogClose>
                            <Button type="submit"> Save changes </Button>
                        </DialogClose>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <div class="">
            <DataTable :columns="columns" :data="data" />
        </div>
    </AuthenticatedLayout>
</template>
