<script setup lang="ts">
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
    DialogClose,
} from "@/components/ui/dialog";
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";

import Button from "@/Components/ui/button/Button.vue";
import Label from "@/Components/ui/label/Label.vue";
import Input from "@/Components/ui/input/Input.vue";
import type { Course } from "@/Components/courses/columns";
import ComboBox from "@/Components/ComboBox.vue";
interface CourseData {
    course_id: number | null;
    name: string;
}

// Define props for the component
const props = defineProps<{
    courseData: CourseData;
    courses: Course[];
    createCourse: () => void;
}>();

function handleCourseSelect(id: number) {
    console.log("new id", id);
    props.courseData.course_id = id;
}
console.log("All courses", props.courses);
</script>
<template>
    <Dialog>
        <div>
            <DialogTrigger as-child>
                <Button variant="secondary" class="sm:p-2 sm:text-sm"
                    >Add Course</Button
                >
            </DialogTrigger>
        </div>
        <DialogContent class="sm:max-w-[425px]">
            <form @submit.prevent="createCourse">
                <DialogHeader>
                    <DialogTitle>Add Course</DialogTitle>
                    <DialogDescription>
                        Create a new course. Click save when you're done.
                    </DialogDescription>
                </DialogHeader>
                <div class="grid gap-4 py-4">
                    <div class="grid gap-4 py-4">
                        <div class="grid grid-cols-4 items-center gap-4">
                            <Label for="name" class="text-right"
                                >Course Name</Label
                            >
                            <ComboBox
                                :items="props.courses"
                                label="Course"
                                class="col-span-3"
                                v-model="props.courseData.course_id"
                                @update:selectedItemId="handleCourseSelect"
                            />
                        </div>
                    </div>
                </div>

                <DialogFooter class="flex items-end">
                    <DialogClose>
                        <Button type="submit">Save changes</Button>
                    </DialogClose>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
