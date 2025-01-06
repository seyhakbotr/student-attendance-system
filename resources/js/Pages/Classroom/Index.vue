<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { ref, defineProps, reactive, computed } from "vue";
import axios from "axios";
import { Link, router, usePage } from "@inertiajs/vue3";
import DeleteConfirm from "@/Components/DeleteConfirm.vue";
import { toast } from "vue-sonner";
import ComboBox from "@/Components/ComboBox.vue";
import type { Faculty } from "@/Components/faculty/columns";
import AddClassroom from "./partials/AddClassroom.vue";
import EditClassroom from "./partials/EditClassroom.vue";
import { Progress } from "../../Components/ui/progress";
import { Input } from "@/Components/ui/input";
import { Button } from "@/Components/ui/button";
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from "@/Components/ui/card";
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from "@/Components/ui/select";
import { useIntersectionObserver } from "@vueuse/core";

const props = defineProps<{
    classrooms: {
        current_page: number;
        data: Classrooms[];
        next_page_url: string | null;
    };
    majors: Major[];
    faculties: Faculty[];
    breadcrumbs: BreadcrumbType[];
    currentDay: String;
    schedules: Array;
}>();

interface Major {
    id: number;
    name: string;
    created_at: string;
    updated_at: string;
    faculty_id: number;
    faculty: Faculty;
}

export interface Classrooms {
    id: number;
    name: string;
    created_at: string;
    room_number: number;
    updated_at: string;
    shift: string | null;
    major: Major;
    faculty: Faculty;
    faculty_id: number | null;
    major_id: number | null;
    year_id: number | null;
    semester_id: number | null;
}

interface DropDownFiltering {
    faculty_id: number | null;
    major_id: number | null;
    room_number: string;
    year_id: number | null;
    semester_id: number | null;
    shift: string | null;
}
const classroomForm = reactive<Classrooms>({
    faculty_id: null,
    major_id: null,
    room_number: "",
    shift: "Morning",
    year_id: 1,
    semester_id: 1,
});
const searchInput = ref("");
const dropDownFiltering = ref<DropDownFiltering>({
    faculty_id: null,
    major_id: null,
    room_number: "",
    year_id: 1,
    semester_id: 1,
    shift: "",
});

const filteredMajors = computed(() => {
    if (dropDownFiltering.value.faculty_id === null) {
        return [];
    }
    return props.majors.filter(
        (major) => major.faculty_id === dropDownFiltering.value.faculty_id,
    );
});

const filterClassrooms = computed(() => {
    return searchFilter(
        searchInput.value,
        dropDownFiltering.value.faculty_id,
        dropDownFiltering.value.shift,
    );
});

function handleFacultySelect(id: number) {
    dropDownFiltering.value.faculty_id = id;
    dropDownFiltering.value.major_id = null;
}

function handleMajorSelect(id: number) {
    dropDownFiltering.value.major_id = id;
}

const searchFilter = (
    searchInput: string,
    facultyId: number | null,
    shift: string | null,
) => {
    return props.classrooms.data.filter((classroom) => {
        const nameMatch = classroom.major.name
            .toLowerCase()
            .includes(searchInput.toLowerCase());
        const roomNumberMatch = String(classroom.room_number)
            .toLowerCase()
            .includes(searchInput.toLowerCase());
        const facultyMatch =
            !facultyId || classroom.major.faculty.id === facultyId;
        const shiftMatch = !shift || classroom.shift === shift;
        return (nameMatch || roomNumberMatch) && facultyMatch && shiftMatch;
    });
};

const storeClassroom = () => {
    router.post("/classroom", classroomForm, {
        onSuccess: () => {
            toast.success("Classroom Added", {
                description: `Classroom has been successfully updated.`,
            });
        },
        onError: () => {
            const errorMes = usePage().props.errors.create;
            console.log("errorMessage", errorMes);
            toast.error("Failed to Add Classroom", {
                description:
                    errorMes || `There was an error adding the classroom.`,
            });
        },
    });
};

const deleteClassroom = (id: number) => {
    router.delete(`/classroom/${id}`);
};

const form = reactive({
    major_id: "",
    room_number: "",
    faculty_id: "",
    year_id: "1",
    shift: "",
    semester_id: "1",
});

const populateEditedClassroom = (classroom) => {
    form.major_id = classroom.major.id;
    form.room_number = classroom.room_number.toString();
    form.faculty_id = classroom.major.faculty.id;
    form.shift = classroom.shift;
    form.year_id = classroom.year_id;
    form.semester_id = classroom.semester_id;
};

const editClassroom = (classroom: Classrooms) => {
    populateEditedClassroom(classroom);
};

const updateClassroom = (classroomId: number) => {
    router.put(`/classroom/${classroomId}`, form, {
        onSuccess: () => {
            toast.success("Classroom Updated", {
                description: `Classroom has been successfully updated.`,
            });
        },
        onError: () => {
            toast.error("Failed to Update Classroom", {
                description: `There was an error updating the classroom.`,
            });
        },
    });
};

const loadMoreData = async () => {
    if (props.classrooms.next_page_url) {
        isLoading.value = true;
        try {
            const response = await axios.get(props.classrooms.next_page_url);
            props.classrooms.data.push(...response.data.data);
            props.classrooms.current_page++;
            props.classrooms.next_page_url = response.data.next_page_url;
        } catch (error) {
            console.error("Failed to fetch classrooms:", error);
        } finally {
            isLoading.value = false;
        }
    }
};

const last = ref(null);
const isLoading = ref(false);

useIntersectionObserver(last, ([entry]) => {
    if (entry.isIntersecting && !isLoading.value) {
        loadMoreData();
    }
});
</script>

<template>
    <AuthenticatedLayout :breadcrumbs="props.breadcrumbs">
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Classroom
            </h2>
        </template>
        <div
            class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4"
        >
            <Input
                v-model="searchInput"
                placeholder="Search a classroom by major name or room number.."
                class="w-full mb-4 md:mb-0 p-2 md:w-3/12 sm:p-4"
            />
            <ComboBox
                :items="props.faculties"
                label="Faculty"
                class="col-span-3"
                v-model="dropDownFiltering.faculty_id"
                @update:selectedItemId="handleFacultySelect"
            />
            <ComboBox
                :items="filteredMajors"
                label="Major"
                class="col-span-3"
                v-model="dropDownFiltering.major_id"
                @update:selectedItemId="handleMajorSelect"
            />
            <Select v-model="dropDownFiltering.shift">
                <SelectTrigger class="w-[120px]">
                    <SelectValue placeholder="Select a shift" />
                </SelectTrigger>
                <SelectContent>
                    <SelectGroup>
                        <SelectLabel>Shifts</SelectLabel>
                        <SelectItem :value="null">All Shifts</SelectItem>
                        <SelectItem value="Morning">Morning</SelectItem>
                        <SelectItem value="Afternoon">Afternoon</SelectItem>
                        <SelectItem value="Evening">Evening</SelectItem>
                    </SelectGroup>
                </SelectContent>
            </Select>
            <AddClassroom
                :majors="props.majors"
                :faculties="props.faculties"
                :classroom-form="classroomForm"
                :store-classroom="storeClassroom"
            />
        </div>
        <div v-if="props.classrooms.data.length > 0">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div
                    v-for="(classroom, index) in filterClassrooms"
                    :key="classroom.id"
                >
                    <Card
                        :class="{
                            'last-item':
                                index === props.classrooms.data.length - 1,
                        }"
                    >
                        <CardHeader>
                            <div class="flex items-center justify-between">
                                <CardTitle
                                    >Faculty of
                                    {{
                                        classroom.major.faculty.name
                                    }}</CardTitle
                                >
                                <EditClassroom
                                    :classroom="classroom"
                                    :form="form"
                                    :majors="props.majors"
                                    :faculties="props.faculties"
                                    :editClassroom="editClassroom"
                                    :updateClassroom="updateClassroom"
                                />
                            </div>
                            <CardDescription v-if="classroom.room_number">
                                Major: {{ classroom.major.name }} Room:
                                {{ classroom.room_number }}
                            </CardDescription>
                            <CardDescription v-else>
                                No designated room numbers
                            </CardDescription>
                        </CardHeader>
                        <CardContent>Shift: {{ classroom.shift }}</CardContent>
                        <CardFooter class="flex justify-between px-6 pb-6">
                            <DeleteConfirm
                                :classroom-id="classroom.id"
                                :delete-classroom="deleteClassroom"
                            />
                            <Link
                                :href="`/classroom/${classroom.id}`"
                                :data="{ title: classroom.major.name }"
                            >
                                <Button>View Detail</Button>
                            </Link>
                        </CardFooter>
                    </Card>
                </div>
                <div ref="last">
                    <span
                        v-if="isLoading"
                        class="flex justify-center items-center"
                    >
                        <Progress :model-value="66" />
                    </span>
                </div>
            </div>
        </div>
        <div v-else>
            <h1>No classroom yet</h1>
        </div>
    </AuthenticatedLayout>
</template>
