<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import type { Major } from "@/Components/major/columns";
import DataTable from "@/Components/major/DataTable.vue";
import { onMounted } from "vue";
import { columns } from "@/Components/major/columns";
import { ref } from "vue";
import { Button } from "@/Components/ui/button";
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from "@/Components/ui/dialog";
import { Input } from "@/Components/ui/input";
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
} from "@/Components/ui/select";
import { Faculty } from "@/Components/faculty/columns";
import ComboBox from "@/Components/ComboBox.vue";
const props = defineProps<{
    majors: Major[];
    faculties: Faculty[];
    breadcrumbs: BreadcrumbType[];
}>();

interface AddMajor {
    name: string | null;
    faculty_id: number | null;
}

const data = ref<Major[]>([]);
const majorForm = reactive<AddMajor>({
    name: "",
    faculty_id: 0,
});
function handleFacultySelect(id: number) {
    majorForm.faculty_id = id;
}
const createMajor = () => {
    router.post("/major", majorForm, {
        onSuccess: () => {
            toast.success("Major added", {
                description: "The Major has been added succesfully",
            });
            fetchMajors();
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

const fetchMajors = async () => {
    try {
        const response = await router.get("/major");
        data.value = response.data;
    } catch (error) {
        console.error("Error fetching major:", error);
    }
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
                    <div class="grid gap-4 py-4">
                        <div class="grid grid-cols-4 items-center gap-4">
                            <Label for="faculty" class="text-right">
                                Faculty
                            </Label>
                            <ComboBox
                                :items="props.faculties"
                                label="Faculty"
                                class="col-span-3"
                                v-model="majorForm.faculty_id"
                                @update:selectedItemId="handleFacultySelect"
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
