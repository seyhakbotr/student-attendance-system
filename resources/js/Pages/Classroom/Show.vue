<template>
    <AuthenticatedLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col items-start w-full">
            <div class="flex w-full justify-between items-center mb-4">
                <div
                    class="text-xl sm:text-2xl md:text-4xl lg:text-5xl xl:text-6xl text-green-500 font-extrabold"
                >
                    {{ classroom.major.name }}
                </div>
                <div class="flex space-x-2">
                    <Dialog>
                        <div>
                            <DialogTrigger as-child>
                                <Button
                                    variant="outline"
                                    class="sm:p-2 sm:text-sm"
                                >
                                    Add Student
                                </Button>
                            </DialogTrigger>
                        </div>
                        <!-- Rest of the dialog content remains unchanged -->
                        <DialogContent class="sm:max-w-[425px]">
                            <form @submit.prevent="createStudent">
                                <DialogHeader>
                                    <DialogTitle>Add Student</DialogTitle>
                                    <DialogDescription>
                                        Create a new student. Click save when
                                        you're done.
                                    </DialogDescription>
                                </DialogHeader>
                                <div class="grid gap-4 py-4">
                                    <div
                                        class="grid grid-cols-4 items-center gap-4"
                                    >
                                        <Label for="name" class="text-right"
                                            >Student Name</Label
                                        >
                                        <Input
                                            id="name"
                                            placeholder="John Doe..."
                                            class="col-span-3"
                                            v-model="form.name"
                                        />
                                    </div>
                                    <div
                                        class="grid grid-cols-4 items-center gap-4"
                                    >
                                        <Label for="gender" class="text-right"
                                            >Student Gender</Label
                                        >
                                        <Select v-model="form.gender">
                                            <SelectTrigger class="w-[180px]">
                                                <SelectValue
                                                    placeholder="Select student gender"
                                                />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectGroup>
                                                    <SelectItem value="male"
                                                        >Male</SelectItem
                                                    >
                                                    <SelectItem value="female"
                                                        >Female</SelectItem
                                                    >
                                                </SelectGroup>
                                            </SelectContent>
                                        </Select>
                                    </div>
                                </div>
                                <DialogFooter class="flex items-end">
                                    <DialogClose>
                                        <Button type="submit"
                                            >Save changes</Button
                                        >
                                    </DialogClose>
                                </DialogFooter>
                            </form>
                        </DialogContent>
                    </Dialog>
                    <Dialog>
                        <div>
                            <DialogTrigger as-child>
                                <Button
                                    variant="secondary"
                                    class="sm:p-2 sm:text-sm"
                                    >Add Course</Button
                                >
                            </DialogTrigger>
                        </div>
                        <DialogContent class="sm:max-w-[425px]">
                            <form @submit.prevent="createCourse">
                                <DialogHeader>
                                    <DialogTitle>Add Course</DialogTitle>
                                    <DialogDescription>
                                        Create a new course. Click save when
                                        you're done.
                                    </DialogDescription>
                                </DialogHeader>
                                <div class="grid gap-4 py-4">
                                    <div
                                        class="grid grid-cols-4 items-center gap-4"
                                    >
                                        <Label for="name" class="text-right"
                                            >Course Name</Label
                                        >
                                        <Input
                                            id="name"
                                            placeholder="Course Name..."
                                            class="col-span-3"
                                            v-model="courseData.name"
                                        />
                                    </div>
                                </div>
                                <DialogFooter class="flex items-end">
                                    <DialogClose>
                                        <Button type="submit"
                                            >Save changes</Button
                                        >
                                    </DialogClose>
                                </DialogFooter>
                            </form>
                        </DialogContent>
                    </Dialog>
                </div>
            </div>
            <div class="flex flex-col">
                <p
                    v-if="classroom.room_number"
                    class="text-sm sm:text-lg text-gray-400 mt-2 sm:mt-0"
                >
                    Room Number: {{ classroom.room_number }}
                    <Button variant="link">
                        <Link
                            :href="`/classroom/${props.classroom.id}/attendance`"
                            >View Attendance</Link
                        >
                    </Button>
                    <Button variant="link">
                        <Link :href="`/classroom/${props.classroom.id}/course`"
                            >View All Course</Link
                        >
                    </Button>
                </p>
                <p v-else class="text-sm sm:text-lg text-gray-400 mt-2 sm:mt-0">
                    No designated room number yet
                </p>
            </div>
            <Button @click="toggleVisibility">{{ isVisible ? 'Hide Schedule' : 'Show Schedule' }}</Button>
              <div v-if="isVisible">
            <Table>
                <TableCaption>Schedule</TableCaption>
                <TableHeader>
                    <TableRow>
                        <TableHead>ID</TableHead>
                        <TableHead>Teacher</TableHead>
                        <TableHead>Course</TableHead>
                        <TableHead>Day</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="schedule in schedules" :key="schedule.id">
                        <TableCell>{{ schedule.id }}</TableCell>

                        <TableCell>{{ schedule.teacher.name }}</TableCell>
                        <TableCell>{{ schedule.course.name }}</TableCell>

                        <TableCell>{{ schedule.day }}</TableCell>
                    </TableRow>
                </TableBody>
            </Table>
            <div>Current Day {{ schedules[0].day }}</div>
              </div>
        </div>

        <div class="">
            <DataTable
                :columns="columns"
                :data="data"
                :classroom-id="props.classroom.id"
            />
        </div>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { ref, onMounted, computed } from "vue";
import { columns } from "@/Components/payments/columns";
import type { Student } from "@/Components/payments/columns";
import DataTable from "@/Components/payments/data-table.vue";
import type { Classrooms } from "./Index.vue";

import { Link, router, usePage } from "@inertiajs/vue3";
import Button from "@/Components/ui/button/Button.vue";
import { Breadcrumb as BreadcrumbType } from "@/types/Breadcrumb";
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
    DialogClose,
} from "@/components/ui/dialog";
import { Input } from "@/Components/ui/input";
import { reactive } from "vue";
import {
    Table,
    TableBody,
    TableCaption,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table";
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from "@/components/ui/tooltip";
import { toast } from "vue-sonner";

const props = defineProps<{
    classroom: Classrooms;
    students: Student[];
    courses: Course[];
    breadcrumbs: BreadcrumbType[];
    currentDay: String;
    teachers: Array[];
    schedules: Array;
}>();
export interface Course {
    classroom_id: number;
    id: number;
    name: string;
}

type AddCourse = Omit<Course, "id">;
interface StudentData
    extends Omit<Student, "id" | "qr_code_image_path" | "attendance"> {
    classroom_id: number;
}

const data = ref<Student[]>(props.students);
const form = reactive<StudentData>({
    name: "",
    gender: "male",
    classroom_id: props.classroom.id,
    major_id: props.classroom.major.id,
});

const courseData = reactive<AddCourse>({
    name: "",
    classroom_id: props.classroom.id,
});

const createStudent = async () => {
    try {
        router.post("/student", form, {
            onSuccess: () => {
                return Promise.all([location.reload()]);
            },
            onError: () => {
                console.error("Error adding student");
            },
            onProgress: () => {
                console.log("loading");
            },
        });
    } catch (error) {
        console.error("Error submitting student:", error);
    }
};

const createCourse = async () => {
    try {
        // await router.post("/course",courseData);
        router.post("/course", courseData, {
            onSuccess: () => {
                toast.success("Success adding course");
            },
            onError: () => {
                const error = usePage().props.errors.name;
                toast.error(error || "Error Adding Course");
            },
            onProgress: () => {
                toast.loading("loading");
            },
        });
        console.log(courseData.name, courseData.classroom_id);
    } catch (error) {
        console.error("Error submitting course: ", error);
    }
};
const isVisible = ref(false);
const toggleVisibility = () => {
    isVisible.value = !isVisible.value
}
//const canAddStudent = computed(() => props.courses.length >= 5);
onMounted(() => {
    data.value = props.students;
});
</script>
