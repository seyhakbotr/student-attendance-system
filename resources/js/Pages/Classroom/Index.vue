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
import { Label } from "@/Components/ui/label";
import { Button } from "@/Components/ui/button";
import { Link, router } from "@inertiajs/vue3";
import { Breadcrumb as BreadcrumbType } from "@/types/Breadcrumb";
import { ref, defineProps, reactive } from "vue";
import { useIntersectionObserver } from "@vueuse/core";
import { Pencil } from "lucide-vue-next";
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
    DialogClose,
} from "@/components/ui/dialog";
import axios from "axios";
import { computed } from "vue";
import DeleteConfirm from "@/Components/DeleteConfirm.vue";
import { toast } from "vue-sonner";
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from "@/components/ui/tooltip";
const props = defineProps<{
    classrooms: {
        current_page: number;
        data: Classrooms[];
        next_page_url: string | null;
    };
    breadcrumbs: BreadcrumbType[];
}>();

export interface Classrooms {
    id: number;
    name: string;
    created_at: string;
    room_number: number;
    updated_at: string;
}

type ClassroomData = Pick<Classrooms, "name" | "room_number">;
const form = reactive<ClassroomData>({
    name: "",
    room_number: 0,
});

const last = ref(null);
const isLoading = ref(false);
const searchInput = ref("");

const searchFilter = (searchInput: string) => {
    return props.classrooms.data.filter((classroom) => {
        const nameMatch = classroom.name
            .toLowerCase()
            .includes(searchInput.toLowerCase());

        const roomNumberMatch = String(classroom.room_number)
            .toLowerCase()
            .includes(searchInput.toLowerCase());

        return nameMatch || roomNumberMatch;
    });
};

const filterClassrooms = computed(() => searchFilter(searchInput.value));
const storeClassroom = () => {
    router.post("/classroom", form);
};

const deleteClassroom = (id: number) => {
    router.delete(`/classroom/${id}`);
};

const populateEditedClassroom = (classroom: ClassroomData) => {
    form.name = classroom.name;
    form.room_number = classroom.room_number;
};

const editClassroom = (classroom: ClassroomData) => {
    populateEditedClassroom(classroom);
};

const updateClassroom = (classroomId: number) => {
    router.put(`/classroom/${classroomId}`, form, {
        onSuccess: () => {
            toast.success('Classroom Updated', {
                description: `Classroom has been successfully updated.`,

            });
        },
        onError: () => {
            toast.error('Failed to Update Classroom', {
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

    <Head title="Classroom" />
    <AuthenticatedLayout :breadcrumbs="props.breadcrumbs">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Classroom
            </h2>
        </template>
        <template class="flex flex-col md:flex-row justify-between items-start md:items-center">
            <Input v-model="searchInput" placeholder="Search a classroom by name or room number.."
                class="w-full mb-4 md:mb-0 p-2 md:w-5/12 sm:p-4" />
            <Dialog>
                <DialogTrigger as-child>
                    <Button variant="outline" class="mt-4 md:mt-0">
                        Add Classroom
                    </Button>
                </DialogTrigger>
                <DialogContent class="sm:max-w-[425px]">
                    <form @submit.prevent="storeClassroom">
                        <DialogHeader>
                            <DialogTitle>Add Classroom</DialogTitle>
                            <DialogDescription>
                                Create a new classroom. Click save when you're
                                done
                            </DialogDescription>
                        </DialogHeader>
                        <div class="grid gap-4 py-4">
                            <div class="grid grid-cols-4 items-center gap-4">
                                <Label for="name" class="text-right">
                                    Name
                                </Label>
                                <Input id="name" placeholder="Room A.." class="col-span-3" v-model="form.name" />
                            </div>
                            <div class="grid grid-cols-4 items-center gap-4">
                                <Label for="Room Number" class="text-right">
                                    Room Number
                                </Label>
                                <Input id="roomNum" placeholder="123.." class="col-span-3" v-model="form.room_number" />
                            </div>
                        </div>

                        <DialogFooter class="flex items-end">
                            <DialogClose class="">
                                <Button type="submit"> Save changes </Button>
                            </DialogClose>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
        </template>
        <div v-if="props.classrooms.data.length > 0">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="(classroom, index) in filterClassrooms" :key="classroom.id">
                    <Card :class="{
                        'last-item':
                            index === props.classrooms.data.length - 1,
                    }">
                        <CardHeader>
                            <div class="flex items-center justify-between">
                                <CardTitle>{{ classroom.name }}</CardTitle>
                                <Dialog>
                                    <TooltipProvider>
                                        <Tooltip>
                                            <TooltipTrigger as-child>
                                                <DialogTrigger @click="
                                                    editClassroom(classroom)
                                                    ">
                                                    <Pencil />
                                                </DialogTrigger>
                                            </TooltipTrigger>
                                            <TooltipContent>
                                                <p>Edit Classroom</p>
                                            </TooltipContent>
                                        </Tooltip>
                                    </TooltipProvider>
                                    <DialogContent>
                                        <DialogHeader>
                                            <DialogTitle>Edit Classroom</DialogTitle>
                                            <DialogDescription>
                                                Make changes to your Classroom
                                                here. Click save when you're
                                                done.
                                            </DialogDescription>
                                        </DialogHeader>
                                        <form @submit.prevent="
                                            updateClassroom(classroom.id)
                                            ">
                                            <div class="grid gap-4 py-4">
                                                <div class="grid grid-cols-4 items-center gap-4">
                                                    <Label for="editName" class="text-right">Name</Label>
                                                    <Input id="editName" class="col-span-3" v-model="form.name" />
                                                </div>
                                                <div class="grid grid-cols-4 items-center gap-4">
                                                    <Label for="editRoom" class="text-right">Room Number</Label>
                                                    <Input id="editRoom" class="col-span-3" v-model="form.room_number
                                                        " />
                                                </div>
                                            </div>
                                            <DialogFooter>
                                                <Button type="submit" variant="outline">Save changes</Button>
                                            </DialogFooter>
                                        </form>
                                    </DialogContent>
                                </Dialog>
                            </div>
                            <CardDescription v-if="classroom.room_number">
                                Room: {{ classroom.room_number }}
                            </CardDescription>
                            <CardDescription v-else>
                                No designated room numbers
                            </CardDescription>
                        </CardHeader>
                        <CardContent></CardContent>
                        <CardFooter class="flex justify-between px-6 pb-6">
                            <DeleteConfirm :classroom-id="classroom.id" :delete-classroom="deleteClassroom" />
                            <Link :href="`/classroom/${classroom.id}`" :data="{ title: classroom.name }">
                            <Button>View Detail</Button>
                            </Link>
                        </CardFooter>
                    </Card>
                </div>
                <!-- Hitting this below will load more data -->
                <div ref="last">
                    <span v-if="isLoading" class="flex justify-center items-center">
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
