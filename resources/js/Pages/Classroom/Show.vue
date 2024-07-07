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
                    <AssignSchedule
                        :teachers="teachers"
                        :courses="courses"
                        :weekdays="weekdays"
                        :scheduleData="scheduleData"
                        :selectedTeacherName="selectedTeacherName"
                        :selectedCourseName="selectedCourseName"
                        :assignSchedule="assignSchedule"

                    />
                    <!-- Add Student !-->
                    <AddStudent
                        :form="form"
                        :create-student="createStudent"
                    />
                    <AddTeacher
                        :teacher-data="teacherData"
                        :create-teacher="createTeacher"
                        :teachers="props.allTeachers"
                    />
                    <AddCourse
                        :course-data="courseData"
                        :courses="props.allCourses"
                        :create-course="createCourse"
                    />

                    <!-- End of Add Student button !--->
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
                    <Button variant="link">
                        <Link :href="`/classroom/${props.classroom.id}/importStudent`"
                            >Import Students</Link
                        >
                    </Button>
                </p>
                <p v-else class="text-sm sm:text-lg text-gray-400 mt-2 sm:mt-0">
                    No designated room number yet
                </p>
            </div>
            <ScheduleTable
                :schedules="schedules"
                :teachers="teachers"
                :courses="courses"
                :scheduleData="scheduleData"
                :selectedTeacherNameEdit="selectedTeacherNameEdit"
                :selectedCourseName="selectedCourseName"
                :weekdays="weekdays"
                :isVisible="isVisible"
                :toggleVisibility="toggleVisibility"
                :deleteSchedule="deleteSchedule"
                :populateScheduleData="populateScheduleData"
                :editSchedule="editSchedule"
            />
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
import AssignSchedule from "./partials/AssignSchedule.vue";
import AddStudent from "./partials/AddStudent.vue";
import AddTeacher from "./partials/AddTeacher.vue";
import ScheduleTable from "./partials/ScheduleTable.vue";
import { Link, router, usePage } from "@inertiajs/vue3";
import Button from "@/Components/ui/button/Button.vue";
import { Breadcrumb as BreadcrumbType } from "@/types/Breadcrumb";
import AddCourse from "./partials/AddCourse.vue";

import { reactive } from "vue";

import { toast } from "vue-sonner";
import type { Teacher } from "@/Components/teachers/columns";
const props = defineProps<{
    classroom: Classrooms;
    students: Student[];
    courses: Course[];
    allCourses: Course[];
    breadcrumbs: BreadcrumbType[];
    currentDay: String;
    teachers: Array[];
    allTeachers: Teacher[];
    schedules: Array[];
}>();
export interface Course {
    classroom_id: number;
    id: number;
    name: string;
    teacher_id: number | null;
}

const data = ref<Student[]>(props.students);
const form = reactive({
    name: "",
    gender: "male",
    classroom_id: props.classroom.id,
    major_id: props.classroom.major.id,
    faculty_id: props.classroom.major.faculty_id,
    year_id: props.classroom.year_id,
    semester_id: props.classroom.semester_id,
});
const courseData = reactive({
    course_id: null,
    classroom_id: props.classroom.id,
});

const teacherData = reactive({
    teacher_id: 0,
    classroom_id: props.classroom.id,
});
const scheduleData = reactive({
    teacher_id: null,
    course_id: null,
    day: null,
    time_in: "00:00",
    time_out: "00:00",
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

const fetchStudents= async () => {
    try {
        const response = await router.get("/student");
        data.value = response.data;
    } catch (error) {
        console.error("Error fetching faculties:", error);
    }
};
console.log("selectedTeacher", selectedTeacherName);
const createStudent = async () => {
    try {
        router.post("/student", form, {
            onSuccess: () => {
                toast.success("Addding student Successfully");
                fetchStudents();
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
                const alreadyExist = usePage().props.errors.alreadyExist;
                const teacherFull = usePage().props.errors.classroom_id;
                toast.error( alreadyExist ||teacherFull || error || "Error Adding teacher");
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
        scheduleData.time_in = schedule.time_in;
        scheduleData.time_out = schedule.time_out;
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
};
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
onMounted(() => {
    data.value = props.students;
});
</script>
