<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from "@/Components/ui/card";
import { Progress } from "@/components/ui/progress";

import { Input } from "@/Components/ui/input";
import { Button } from "@/Components/ui/button";
import { Link, router, usePage } from "@inertiajs/vue3";
import { Breadcrumb as BreadcrumbType } from "@/types/Breadcrumb";
import { ref, defineProps, reactive, computed } from "vue";
import { useIntersectionObserver } from "@vueuse/core";
import axios from "axios";
import DeleteConfirm from "@/Components/DeleteConfirm.vue";
import { toast } from "vue-sonner";
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";
import type { Faculty } from "@/Components/faculty/columns";
import AddClassroom from "./partials/AddClassroom.vue";
import EditClassroom from "./partials/EditClassroom.vue";
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
    major: Major;
    faculty: Faculty;
    year_id: number | null;
    semester_id: number | null;
}

const last = ref(null);
const isLoading = ref(false);
const searchInput = ref("");
const selectedMajor = ref<Major | null>(null);
const selectedFaculty = ref<Faculty | null>(null);
const classroomForm = reactive({
    faculty_id: selectedFaculty.value?.id,
    major_id: selectedMajor.value?.id,
    room_number: "",
    year_id: 1,
    semester_id:1
});
const filteredMajors = computed(() => {
    // Check if a faculty is selected
    if (!selectedFaculty.value) {
        // No faculty selected, return all majors
        return props.majors;
    } else {
        return props.majors.filter(
            (major) =>
                major.faculty_id ===
                (selectedFaculty.value ? selectedFaculty.value.id : false),
        );
    }
});

const updateSelectedFaculty = (faculty: Faculty | null) => {
    selectedFaculty.value = faculty;
    // Reset the selected major when a new faculty is selected
    selectedMajor.value = null;
};
const searchFilter = (searchInput: string, selectedFaculty: Faculty | null) => {
    return props.classrooms.data.filter((classroom) => {
        const nameMatch = classroom.major.name
            .toLowerCase()
            .includes(searchInput.toLowerCase());

        const roomNumberMatch = String(classroom.room_number)
            .toLowerCase()
            .includes(searchInput.toLowerCase());

        // Check if the selected faculty matches the classroom's faculty
        const facultyMatch =
            !selectedFaculty ||
            classroom.major.faculty.id === selectedFaculty.id;

        return (nameMatch || roomNumberMatch) && facultyMatch;
    });
};
const filterClassrooms = computed(() =>
    searchFilter(searchInput.value, selectedFaculty.value),
);
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
    semester_id: "1",
});

const populateEditedClassroom = (classroom: Classrooms) => {
    form.major_id = classroom.major.id;
    form.room_number = classroom.room_number.toString();
    form.faculty_id = classroom.major.faculty.id.toString();
    form.year_id = classroom.year_id
    form.semester_id = classroom.semester_id

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

useIntersectionObserver(last, ([entry]) => {
    if (entry.isIntersecting && !isLoading.value) {
        loadMoreData();
    }
});
</script>

<template>
   <AuthenticatedLayout :breadcrumbs="props.breadcrumbs" >
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
                class="w-full mb-4 md:mb-0 p-2 md:w-5/12 sm:p-4"
            />
            <Select v-model="selectedFaculty" @change="updateSelectedFaculty">
                <SelectTrigger class="w-[180px]">
                    <SelectValue placeholder="Select a faculty" />
                </SelectTrigger>
                <SelectContent>
                    <SelectGroup>
                        <SelectLabel>Faculty</SelectLabel>
                        <SelectItem
                            v-for="faculty in props.faculties"
                            :key="faculty.id"
                            :value="faculty"
                        >
                            {{ faculty.name }}
                        </SelectItem>
                    </SelectGroup>
                </SelectContent>
            </Select>


            <Select v-model="selectedMajor">
                <SelectTrigger class="w-[180px]">
                    <SelectValue placeholder="Select a major" />
                </SelectTrigger>
                <SelectContent>
                    <SelectGroup>
                        <SelectLabel>Major</SelectLabel>
                        <SelectItem
                            v-for="major in filteredMajors"
                            :key="major.id"
                            :value="major"
                        >
                            {{ major.name }}
                        </SelectItem>
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
                        <CardContent></CardContent>
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
