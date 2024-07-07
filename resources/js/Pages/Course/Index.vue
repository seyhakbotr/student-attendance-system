<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { onMounted } from "vue";
import { columns } from "@/Components/courses/columns";
import { ref } from "vue";
import DataTable from "@/Components/courses/DataTable.vue";
import { Button } from "@/components/ui/button";
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from "@/components/ui/dialog";
import { Input } from "@/components/ui/input";
import Label from "@/Components/ui/label/Label.vue";
import { reactive } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { toast } from "vue-sonner";
import { Breadcrumb as BreadcrumbType } from "@/types/Breadcrumb";
import type { Course } from "@/Components/courses/columns";
import ComboBox from "@/Components/ComboBox.vue";
import { Major } from "@/Components/major/columns";
const props = defineProps<{
    courses: Course[];
    majors: Major[];
    breadcrumbs: BreadcrumbType[];
}>();

const courseForm = reactive({
    name: "",
    major_id: 0,
});
console.log("course", props.courses);
const data = ref<Course[]>([]);
const createcourse = () => {
    router.post("/courses", courseForm, {
        onSuccess: () => {
            toast.success("course added", {
                description: "The course has been added succesfully",
            });
            fetchcourses();
        },
        onError: () => {
            const error = usePage().props.errors.name;
            toast.error("Error adding course", {
                description:
                    error || "The course has not been added succesfully",
            });
        },
    });
};

const fetchcourses = async () => {
    try {
        const response = await router.get("/course");
        data.value = response.data;
    } catch (error) {
        console.error("Error fetching courses:", error);
    }
};
function handleMajorSelect(id: number) {
    courseForm.major_id = id;
}
onMounted(() => {
    data.value = props.courses;
});
</script>

<template>
    <AuthenticatedLayout :breadcrumbs="breadcrumbs">
        <h1 class="text-6xl font-extrabold text-primary-accent">course List</h1>
        <Dialog>
            <div>
                <DialogTrigger as-child>
                    <Button variant="secondary" class="sm:p-2 sm:text-sm">
                        Add course
                    </Button>
                </DialogTrigger>
            </div>
            <!-- Rest of the dialog content remains unchanged -->
            <DialogContent class="sm:max-w-[425px]">
                <form @submit.prevent="createcourse">
                    <DialogHeader>
                        <DialogTitle>Add course</DialogTitle>
                        <DialogDescription>
                            Create a new course. Click save when you're done
                        </DialogDescription>
                    </DialogHeader>
                    <div class="grid gap-4 py-4">
                        <div class="grid grid-cols-4 items-center gap-4">
                            <Label for="name" class="text-right">
                                course Name
                            </Label>
                            <Input
                                id="name"
                                placeholder="John Doe..."
                                class="col-span-3"
                                v-model="courseForm.name"
                            />
                        </div>
                        <div class="grid grid-cols-4 items-center gap-4">
                            <Label for="major" class="text-right">
                                Major
                            </Label>
                            <ComboBox
                                :items="props.majors"
                                label="Major"
                                class="col-span-3"
                                v-model="courseForm.major_id"
                                @update:selectedItemId="handleMajorSelect"
                            />
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

        <div class="">
            <DataTable :columns="columns" :data="data" />
        </div>
    </AuthenticatedLayout>
</template>
