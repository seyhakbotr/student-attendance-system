<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import type { Faculty } from "@/Components/faculty/columns";
import { onMounted } from "vue";
import { columns } from "@/Components/faculty/columns";
import { ref } from "vue";
import DataTable from "@/Components/faculty/DataTable.vue";
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
const props = defineProps<{
    faculties: Faculty[];
    breadcrumbs: BreadcrumbType[];
}>();

const facultyForm = reactive({
    name: "",
});
console.log("Faculty", props.faculties);
const data = ref<Faculty[]>([]);
const createFaculty = () => {
    router.post("/faculty", facultyForm, {
        onSuccess: () => {
            toast.success("Faculty added", {
                description: "The faculty has been added succesfully",
            });
            fetchFaculties();
        },
        onError: () => {
            const error = usePage().props.errors.name;
            toast.error("Error adding faculty", {
                description:
                    error || "The faculty has not been added succesfully",
            });
        },
    });
};

const fetchFaculties = async () => {
    try {
        const response = await router.get("/faculty");
        data.value = response.data;
    } catch (error) {
        console.error("Error fetching faculties:", error);
    }
};
onMounted(() => {
    data.value = props.faculties;
});
</script>

<template>
    <AuthenticatedLayout :breadcrumbs="breadcrumbs">
        <h1 class="text-6xl font-extrabold text-primary-accent">
            Faculty List
        </h1>
        <Dialog>
            <div>
                <DialogTrigger as-child>
                    <Button variant="secondary" class="sm:p-2 sm:text-sm">
                        Add Faculty
                    </Button>
                </DialogTrigger>
            </div>
            <!-- Rest of the dialog content remains unchanged -->
            <DialogContent class="sm:max-w-[425px]">
                <form @submit.prevent="createFaculty">
                    <DialogHeader>
                        <DialogTitle>Add Faculty</DialogTitle>
                        <DialogDescription>
                            Create a new Faculty. Click save when you're done
                        </DialogDescription>
                    </DialogHeader>
                    <div class="grid gap-4 py-4">
                        <div class="grid grid-cols-4 items-center gap-4">
                            <Label for="name" class="text-right">
                                Faculty Name
                            </Label>
                            <Input
                                id="name"
                                placeholder="John Doe..."
                                class="col-span-3"
                                v-model="facultyForm.name"
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
