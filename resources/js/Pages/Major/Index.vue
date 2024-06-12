<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import type { Major } from "@/Components/major/columns";
import DataTable from "@/Components/major/DataTable.vue";
import { onMounted } from "vue";
import { columns } from "@/Components/major/columns";
import { ref } from "vue";
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
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";
import { Faculty } from "@/Components/faculty/columns";
const props = defineProps<{
    majors: Major[];
    faculties: Faculty[];
    breadcrumbs: BreadcrumbType[];
}>();

const selectedFaculty = ref<Faculty | null>(null);
const data = ref<Major[]>([]);
const majorForm = reactive({
    name: "",
    faculty_id: selectedFaculty.value?.id,
});
const createMajor = () => {
    if (selectedFaculty.value) {
        majorForm.faculty_id = selectedFaculty.value.id;
    }

    router.post("/major", majorForm, {
        onSuccess: () => {
            toast.success("Major added", {
                description: "The Major has been added succesfully",
            });
        },
        onError: () => {
            const error = usePage().props.errors.name;
            toast.error("Error adding Major", {
                description:
                    error || "The Major has not been added succesfully",
            });
        },
    });
};
onMounted(() => {
    data.value = props.majors;
});
</script>
<template>
    <AuthenticatedLayout :breadcrumbs="breadcrumbs">
        <h1 class="text-6xl font-extrabold text-primary-accent">Major List</h1>
        <Dialog>
            <div>
                <DialogTrigger as-child>
                    <Button variant="secondary" class="sm:p-2 sm:text-sm">
                        Add Major
                    </Button>
                </DialogTrigger>
            </div>
            <!-- Rest of the dialog content remains unchanged -->
            <DialogContent class="sm:max-w-[425px]">
                <form @submit.prevent="createMajor">
                    <DialogHeader>
                        <DialogTitle>Add Major</DialogTitle>
                        <DialogDescription>
                            Create a new Major. Click save when you're done
                        </DialogDescription>
                    </DialogHeader>
                    <div class="grid gap-4 py-4">
                        <div class="grid grid-cols-4 items-center gap-4">
                            <Label for="name" class="text-right">
                                Major Name
                            </Label>
                            <Input
                                id="name"
                                placeholder="John Doe..."
                                class="col-span-3"
                                v-model="majorForm.name"
                            />
                        </div>
                    </div>
                    <Select v-model="selectedFaculty">
                        <SelectTrigger class="w-[180px]">
                            <!-- <span v-if="!selectedCourse">Select a course</span> -->
                            <SelectValue>
                                <span v-if="!selectedFaculty"
                                    >Select a faculty</span
                                >
                                <span v-else>{{ selectedFaculty.name }}</span>
                            </SelectValue>
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
