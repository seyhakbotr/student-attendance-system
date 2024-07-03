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
defineProps<{
  form: {
    name: string;
    gender: string;
  };
  canAddStudent: boolean;
  createStudent: () => void;
}>();
</script>
<template>
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
                        Create a new student. Click save when you're done.
                    </DialogDescription>
                </DialogHeader>
                <div class="grid gap-4 py-4">
                    <div class="grid grid-cols-4 items-center gap-4">
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
                    <div class="grid grid-cols-4 items-center gap-4">
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
                                    <SelectItem value="male">Male</SelectItem>
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
                        <Button type="submit">Save changes</Button>
                    </DialogClose>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
