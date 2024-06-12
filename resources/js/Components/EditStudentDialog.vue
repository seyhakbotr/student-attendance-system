<template>
    <Dialog open>
        <DialogOverlay />
        <DialogContent>
            <DialogHeader>
                <DialogTitle>Edit Student</DialogTitle>
            </DialogHeader>
            <DialogBody>
                <form @submit.prevent="saveChanges">
                    <div class="grid gap-4 py-4">
                        <div class="grid grid-cols-4 items-center gap-4">
                            <Label for="name" class="text-right">Student Name</Label>
                            <Input id="name" v-model="form.name" class="col-span-3" />
                        </div>
                        <div class="grid grid-cols-4 items-center gap-4">
                            <Label for="gender" class="text-right">Student Gender</Label>
                            <Select v-model="form.gender" class="col-span-3">
                                <SelectTrigger>
                                    <SelectValue placeholder="Select gender" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectItem value="male">Male</SelectItem>
                                        <SelectItem value="female">Female</SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                        </div>
                    </div>
                    <DialogFooter>
                        <Button type="submit">Save changes</Button>
                        <Button type="button" variant="outline" @click="$emit('close')">Cancel</Button>
                    </DialogFooter>
                </form>
            </DialogBody>
        </DialogContent>
    </Dialog>
</template>

<script setup lang="ts">
import { ref, watch } from "vue";
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
    DialogClose,
} from "@/components/ui/dialog";

const props = defineProps({
    student: Object,
});

const form = ref({ ...props.student });

watch(
    () => props.student,
    (newStudent) => {
        form.value = { ...newStudent };
    },
);

const saveChanges = () => {
    emit("save", form.value);
};
</script>
