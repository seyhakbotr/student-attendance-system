<script setup lang="ts">
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from "@/components/ui/alert-dialog";
import { Button } from "@/Components/ui/button";
import { toast } from "vue-sonner";
interface DeleteConfirmationProps {
    classroomId: number;
    deleteClassroom: (id: number) => void; // Function to delete the classroom
}

const props = defineProps<DeleteConfirmationProps>();
const deleteClassroom =  () => {
    try {
        // Pass the classroomId to the deleteClassroom function
        props.deleteClassroom(props.classroomId);
        // Trigger toast upon successful deletion
        toast.success('Classroom has been deleted successfully');
    } catch (error) {
        console.error('Error deleting classroom:', error);
        toast.error('An error occurred while deleting the classroom');
    }
};
</script>

<template>
    <AlertDialog>
        <AlertDialogTrigger>
            <Button variant="destructive">Delete</Button>
        </AlertDialogTrigger>
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>Are you absolutely sure?</AlertDialogTitle>
                <AlertDialogDescription>
                    This action cannot be undone. This will permanently delete
                    the classroom and remove the data from our servers.
                </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogCancel>Cancel</AlertDialogCancel>
                <!-- Pass the classroomId to deleteClassroom -->
                <AlertDialogAction  @click="deleteClassroom">Continue</AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>

