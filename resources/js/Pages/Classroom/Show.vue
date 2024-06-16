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
                                <Button variant="secondary"
                                    >Add Schedule</Button
                                >
                            </DialogTrigger>
                        </div>
                        <DialogContent class="sm:max-w-[425px]">
                            <form @submit.prevent="assignSchedule">
                                <DialogHeader>
                                    <DialogTitle>Add Schedule</DialogTitle>
                                    <DialogDescription>
                                        Add a new schedule for a teacher on a
                                        specific day.
                                    </DialogDescription>
                                </DialogHeader>
                                <div class="grid gap-4 py-4">
                                    <div
                                        class="grid grid-cols-4 items-center gap-4"
                                    >
                                        <Label for="teacher" class="text-right"
                                            >Teacher</Label
                                        >
                                        <Select
                                            v-model="scheduleData.teacher_id"
                                        >
                                            <SelectTrigger class="w-[180px]">
                                                <SelectValue>
                                                    <span
                                                        v-if="
                                                            !scheduleData.teacher_id
                                                        "
                                                        >Select a teacher</span
                                                    >
                                                    <span v-else>{{
                                                        selectedTeacherName
                                                    }}</span>
                                                </SelectValue>
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectGroup>
                                                    <template
                                                        v-for="teacher in teachers"
                                                        :key="teacher.id"
                                                    >
                                                        <SelectItem
                                                            :value="teacher.id"
                                                            >{{
                                                                teacher.name
                                                            }}</SelectItem
                                                        >
                                                    </template>
                                                </SelectGroup>
                                            </SelectContent>
                                        </Select>
                                    </div>
                                    <div
                                        class="grid grid-cols-4 items-center gap-4"
                                    >
                                        <Label for="course" class="text-right"
                                            >Course</Label
                                        >
                                        <Select
                                            v-model="scheduleData.course_id"
                                        >
                                            <SelectTrigger class="w-[180px]">
                                                <SelectValue>
                                                    <span
                                                        v-if="
                                                            !scheduleData.course_id
                                                        "
                                                        >Select a course</span
                                                    >
                                                    <span v-else>{{
                                                        selectedCourseName
                                                    }}</span>
                                                </SelectValue>
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectGroup>
                                                    <template
                                                        v-for="course in courses"
                                                        :key="course.id"
                                                    >
                                                        <SelectItem
                                                            :value="course.id"
                                                            >{{
                                                                course.name
                                                            }}</SelectItem
                                                        >
                                                    </template>
                                                </SelectGroup>
                                            </SelectContent>
                                        </Select>
                                    </div>
                                    <div
                                        class="grid grid-cols-4 items-center gap-4"
                                    >
                                        <Label for="day" class="text-right"
                                            >Day</Label
                                        >
                                        <Select v-model="scheduleData.day">
                                            <SelectTrigger class="w-[180px]">
                                                <SelectValue>
                                                    <span
                                                        v-if="!scheduleData.day"
                                                        >Select a day</span
                                                    >
                                                    <span v-else>{{
                                                        scheduleData.day
                                                    }}</span>
                                                </SelectValue>
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectGroup>
                                                    <SelectItem
                                                        v-for="day in weekdays"
                                                        :key="day"
                                                        :value="day"
                                                        >{{ day }}</SelectItem
                                                    >
                                                </SelectGroup>
                                            </SelectContent>
                                        </Select>
                                    </div>
                                </div>
                                <DialogFooter class="flex items-end">
                                    <DialogClose>
                                        <Button type="submit"
                                            >Save Schedule</Button
                                        >
                                    </DialogClose>
                                </DialogFooter>
                            </form>
                        </DialogContent>
                    </Dialog>

                    <!-- Add Student !-->
                    <Dialog>
                        <div>
                            <DialogTrigger as-child>
                                <Button
                                    variant="outline"
                                    class="sm:p-2 sm:text-sm"
                                    :disabled="canAddStudent"
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
                    <!-- End of Add Student button !--->
                    <Dialog>
                        <div>
                            <DialogTrigger as-child>
                                <Button
                                    variant="secondary"
                                    class="sm:p-2 sm:text-sm"
                                    >Add Teacher</Button
                                >
                            </DialogTrigger>
                        </div>
                        <DialogContent class="sm:max-w-[425px]">
                            <form @submit.prevent="createTeacher">
                                <DialogHeader>
                                    <DialogTitle>Add Teacher</DialogTitle>
                                    <DialogDescription>
                                        Create a new Teacher. Click save when
                                        you're done.
                                    </DialogDescription>
                                </DialogHeader>
                                <div class="grid gap-4 py-4">
                                    <div
                                        class="grid grid-cols-4 items-center gap-4"
                                    >
                                        <Label for="name" class="text-right"
                                            >Teacher Name</Label
                                        >
                                        <Input
                                            id="name"
                                            placeholder="jane doe"
                                            class="col-span-3"
                                            v-model="teacherData.name"
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
                                        <Label for="teacher" class="text-right"
                                            >Teacher</Label
                                        >

                                        <Select v-model="courseData.teacher_id">
                                            <SelectTrigger class="w-[180px]">
                                                <SelectValue>
                                                    <span
                                                        v-if="
                                                            !courseData.teacher_id
                                                        "
                                                        >Select a teacher</span
                                                    >
                                                    <span v-else>{{
                                                        selectedTeacherName
                                                    }}</span>
                                                </SelectValue>
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectGroup>
                                                    <template
                                                        v-for="teacher in teachers"
                                                        :key="teacher.id"
                                                    >
                                                        <SelectItem
                                                            :value="teacher.id"
                                                        >
                                                            {{ teacher.name }}
                                                        </SelectItem>
                                                    </template>
                                                </SelectGroup>
                                            </SelectContent>
                                        </Select>
                                    </div>
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
                <div class="text-3xl font-extrabold">
                    Current Day: {{ currentDay }}
                </div>

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

                    <Button variant="link">
                        <Link :href="`/classroom/${props.classroom.id}/teacher`"
                            >View All teacher</Link
                        >
                    </Button>
                </p>
                <p v-else class="text-sm sm:text-lg text-gray-400 mt-2 sm:mt-0">
                    No designated room number yet
                </p>
            </div>
            <Button @click="toggleVisibility">{{
                isVisible ? "Hide Schedule" : "Show Schedule"
            }}</Button>
            <div v-if="isVisible" class="flex justify-center w-full">
                <Table>
                    <TableCaption>Schedule</TableCaption>
                    <TableHeader>
                        <TableRow>
                            <TableHead>ID</TableHead>
                            <TableHead>Teacher</TableHead>
                            <TableHead>Course</TableHead>
                            <TableHead>Day</TableHead>
                            <TableHead>Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow
                            v-for="schedule in schedules"
                            :key="schedule.id"
                        >
                            <TableCell>{{ schedule.id }}</TableCell>

                            <TableCell v-if="schedule.teacher">{{
                                schedule.teacher.name
                            }}</TableCell>
                            <TableCell v-else>N/A</TableCell>

                            <TableCell v-if="schedule.course">{{
                                schedule.course.name
                            }}</TableCell>
                            <TableCell v-else>N/A</TableCell>

                            <TableCell>{{ schedule.day }}</TableCell>
                            <TableCell class="flex gap-2">
                                <Button variant="destructive" @click="deleteSchedule(schedule.id)">Delete</Button>
                                <Dialog>
                                    <div>
                                        <DialogTrigger as-child>
                                            <Button
                                                variant="secondary"
                                                @click="
                                                    populateScheduleData(
                                                        schedule.id,
                                                    )
                                                "
                                            >
                                                Edit Schedule
                                            </Button>
                                        </DialogTrigger>
                                    </div>
                                    <DialogContent class="sm:max-w-[425px]">
                                        <form
                                            @submit.prevent="
                                                editSchedule(schedule.id)
                                            "
                                        >
                                            <DialogHeader>
                                                <DialogTitle
                                                    >Edit Schedule</DialogTitle
                                                >
                                                <DialogDescription>
                                                    Edit an existing schedule
                                                    for a teacher on a specific
                                                    day.
                                                </DialogDescription>
                                            </DialogHeader>
                                            <div class="grid gap-4 py-4">
                                                <div
                                                    class="grid grid-cols-4 items-center gap-4"
                                                >
                                                    <Label
                                                        for="teacher"
                                                        class="text-right"
                                                        >Teacher</Label
                                                    >
                                                    <Select
                                                        v-model="
                                                            scheduleData.teacher_id
                                                        "
                                                    >
                                                        <SelectTrigger
                                                            class="w-[180px]"
                                                        >
                                                            <SelectValue>
                                                                <span
                                                                    v-if="
                                                                        !scheduleData.teacher_id
                                                                    "
                                                                >
                                                                    Select a
                                                                    teacher
                                                                </span>
                                                                <span v-else>{{
                                                                    selectedTeacherNameEdit
                                                                }}</span>
                                                            </SelectValue>
                                                        </SelectTrigger>
                                                        <SelectContent>
                                                            <SelectGroup>
                                                                <template
                                                                    v-for="teacher in teachers"
                                                                    :key="
                                                                        teacher.id
                                                                    "
                                                                >
                                                                    <SelectItem
                                                                        :value="
                                                                            teacher.id
                                                                        "
                                                                    >
                                                                        {{
                                                                            teacher.name
                                                                        }}
                                                                    </SelectItem>
                                                                </template>
                                                            </SelectGroup>
                                                        </SelectContent>
                                                    </Select>
                                                </div>
                                                <div
                                                    class="grid grid-cols-4 items-center gap-4"
                                                >
                                                    <Label
                                                        for="course"
                                                        class="text-right"
                                                        >Course</Label
                                                    >
                                                    <Select
                                                        v-model="
                                                            scheduleData.course_id
                                                        "
                                                    >
                                                        <SelectTrigger
                                                            class="w-[180px]"
                                                        >
                                                            <SelectValue>
                                                                <span
                                                                    v-if="
                                                                        !scheduleData.course_id
                                                                    "
                                                                >
                                                                    Select a
                                                                    course
                                                                </span>
                                                                <span v-else>{{
                                                                    selectedCourseName
                                                                }}</span>
                                                            </SelectValue>
                                                        </SelectTrigger>
                                                        <SelectContent>
                                                            <SelectGroup>
                                                                <template
                                                                    v-for="course in courses"
                                                                    :key="
                                                                        course.id
                                                                    "
                                                                >
                                                                    <SelectItem
                                                                        :value="
                                                                            course.id
                                                                        "
                                                                    >
                                                                        {{
                                                                            course.name
                                                                        }}
                                                                    </SelectItem>
                                                                </template>
                                                            </SelectGroup>
                                                        </SelectContent>
                                                    </Select>
                                                </div>
                                                <div
                                                    class="grid grid-cols-4 items-center gap-4"
                                                >
                                                    <Label
                                                        for="day"
                                                        class="text-right"
                                                        >Day</Label
                                                    >
                                                    <Select
                                                        v-model="
                                                            scheduleData.day
                                                        "
                                                    >
                                                        <SelectTrigger
                                                            class="w-[180px]"
                                                        >
                                                            <SelectValue>
                                                                <span
                                                                    v-if="
                                                                        !scheduleData.day
                                                                    "
                                                                >
                                                                    Select a day
                                                                </span>
                                                                <span v-else>{{
                                                                    scheduleData.day
                                                                }}</span>
                                                            </SelectValue>
                                                        </SelectTrigger>
                                                        <SelectContent>
                                                            <SelectGroup>
                                                                <SelectItem
                                                                    v-for="day in weekdays"
                                                                    :key="day"
                                                                    :value="day"
                                                                >
                                                                    {{ day }}
                                                                </SelectItem>
                                                            </SelectGroup>
                                                        </SelectContent>
                                                    </Select>
                                                </div>
                                            </div>
                                            <DialogFooter
                                                class="flex items-end"
                                            >
                                                <DialogClose>
                                                    <Button type="submit"
                                                        >Save Schedule</Button
                                                    >
                                                </DialogClose>
                                            </DialogFooter>
                                        </form>
                                    </DialogContent>
                                </Dialog>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>

        <DataTable
            :columns="columns"
            :data="data"
            :classroom-id="props.classroom.id"
        />
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
    schedules: Array[];
}>();
export interface Course {
    classroom_id: number;
    id: number;
    name: string;
    teacher_id: number | null;
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
    teacher_id: null,
    classroom_id: props.classroom.id,
});

const teacherData = reactive({
    name: "",
    classroom_id: props.classroom.id,
});
const scheduleData = reactive({
    teacher_id: null,
    course_id: null,
    day: null,
    classroom_id: props.classroom.id,
});
const selectedTeacherNameEdit = computed(() => {
    const selectedTeacher = props.teachers.find(
        (teacher) => teacher.id === scheduleData.teacher_id,
    );
    return selectedTeacher ? selectedTeacher.name : "";
});
const selectedTeacherName = computed(() => {
    const selectedTeacher = props.teachers.find(
        (teacher) => teacher.id === courseData.teacher_id,
    );
    return selectedTeacher ? selectedTeacher.name : "";
});

const selectedCourseName = computed(() => {
    const selectedCourse = props.courses.find(
        (course) => course.id === scheduleData.course_id,
    );
    return selectedCourse ? selectedCourse.name : "";
});
console.log("selectedTeacher", selectedTeacherName);
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
                const errors = usePage().props.errors;
                let errorMessage = "";

                switch (true) {
                    case errors.hasOwnProperty("name"):
                        errorMessage = errors.name;
                        break;
                    case errors.hasOwnProperty("courseLimit"):
                        errorMessage = errors.courseLimit;
                        break;
                    default:
                        errorMessage = "Error Adding Course";
                }

                toast.error(errorMessage);
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

const createTeacher = async () => {
    try {
        // await router.post("/course",courseData);
        router.post("/teacher", teacherData, {
            onSuccess: () => {
                toast.success("Success adding teacher");
            },
            onError: () => {
                const error = usePage().props.errors.name;
                toast.error(error || "Error Adding teacher");
            },
            onProgress: () => {
                toast.loading("loading");
            },
        });
    } catch (error) {
        console.error("Error submitting teacher: ", error);
    }
};
const populateScheduleData = (scheduleId: number) => {
    const schedule = props.schedules.find((s) => s.id === scheduleId);
    if (schedule) {
        scheduleData.teacher_id = schedule.teacher_id;
        scheduleData.course_id = schedule.course_id;
        scheduleData.day = schedule.day;
    }
};
const editSchedule = async (scheduleId: number) => {
    try {
        // await router.post("/course",courseData);
        router.patch(`/schedule/${scheduleId}`, scheduleData, {
            onSuccess: () => {
                toast.success("Successfully update schedule");
            },
            onError: () => {
                const error = usePage().props.errors.scheduleExist;
                toast.error(error || "Error Adding schedule");
            },
            onProgress: () => {
                toast.loading("loading");
            },
        });
    } catch (error) {
        console.error("Error submitting schedule: ", error);
    }
};
const assignSchedule = async () => {
    try {
        // await router.post("/course",courseData);
        router.post("/schedule", scheduleData, {
            onSuccess: () => {
                toast.success("Successfully assigning schedule");
            },
            onError: () => {
                const error = usePage().props.errors.scheduleExist;
                toast.error(error || "Error Adding schedule");
            },
            onProgress: () => {
                toast.loading("loading");
            },
        });
    } catch (error) {
        console.error("Error submitting schedule: ", error);
    }
};
const deleteSchedule = (scheduleId: number) => {
    router.delete(`/schedule/${scheduleId}`, {
            onSuccess: () => {
                toast.success("Successfully deleting schedule");
            },
            onError: () => {
                const error = usePage().props.errors.scheduleExist;
                toast.error(error || "Error deleting schedule");
            },
            onProgress: () => {
                toast.loading("loading");
            },
        });
}
const weekdays = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"];

const isScheduleFull = computed(() => {
    const weekdays = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"];
    const scheduledDays = props.schedules.map((schedule) => schedule.day);
    const uniqueScheduledDays = new Set(scheduledDays);
    return uniqueScheduledDays.size >= weekdays.length;
});
const isVisible = ref(false);
const toggleVisibility = () => {
    isVisible.value = !isVisible.value;
};
const canAddStudent = computed(() => props.courses.length <= 4);
onMounted(() => {
    data.value = props.students;
});
</script>
