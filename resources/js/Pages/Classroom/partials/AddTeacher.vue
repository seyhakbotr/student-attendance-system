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
import Button from "@/Components/ui/button/Button.vue";
import Label from "@/Components/ui/label/Label.vue";
import Input from "@/Components/ui/input/Input.vue";
import ComboBox from "@/Components/ComboBox.vue";
import type { Teacher } from "@/Components/teachers/columns";
const props = defineProps<{
    teacherData: { teacher_id: number; name: string; classroom_id: number };
    teachers: Teacher[];
    createTeacher: () => void;
    canAddTeacher: boolean;
}>();
function handleTeacherSelect(id: number) {
    console.log("new id", id);
    props.teacherData.teacher_id = id;
}
console.log("Teachers at add", props.teachers);
</script>
<template>
    <Dialog>
        <div>
            <DialogTrigger as-child>
                <Button variant="secondary" class="sm:p-2 sm:text-sm"
                    >Add Teacher</Button
                >
            </DialogTrigger>
        </div>
        <DialogContent class="sm:max-w-[425px]">
            <form @submit.prevent="props.createTeacher">
                <DialogHeader>
                    <DialogTitle>Add Teacher</DialogTitle>
                    <DialogDescription>
                        Create a new Teacher. Click save when you're done.
                    </DialogDescription>
                </DialogHeader>
                <div class="grid gap-4 py-4">
                    <div class="grid grid-cols-4 items-center gap-4">
                        <Label for="name" class="text-right"
                            >Teacher Name</Label
                        >
                        <ComboBox
                            :items="props.teachers"
                            label="Teacher"
                            class="col-span-3"
                            v-model="props.teacherData.teacher_id"
                            @update:selectedItemId="handleTeacherSelect"
                        />
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
