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
const props = defineProps<{
    teacherData: { name: string, classroom_id: number };
    createTeacher: () => void;
    canAddTeacher: boolean;
}>();
</script>
<template>
    <Dialog>
        <div>
            <DialogTrigger as-child>
                <Button variant="secondary" class="sm:p-2 sm:text-sm">Add Teacher</Button>
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
                        <Label for="name" class="text-right">Teacher Name</Label>
                        <Input
                            id="name"
                            placeholder="Jane Doe"
                            class="col-span-3"
                            v-model="props.teacherData.name"
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
