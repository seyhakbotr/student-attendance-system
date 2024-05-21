<script setup lang="ts">
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

import { Button } from "@/Components/ui/button";
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from "@/Components/ui/card";
import { Input } from "@/Components/ui/input";
import { Label } from "@/Components/ui/label";
const form = useForm({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
});

const submit = () => {
    form.post(route("register"), {
        onFinish: () => {
            form.reset("password", "password_confirmation");
        },
    });
};
</script>

<template>
    <GuestLayout>

        <Head title="Register" />

        <form @submit.prevent="submit">
            <Card class="w-full max-w-sm">
                <CardHeader>
                    <CardTitle class="text-2xl">Register</CardTitle>
                    <CardDescription>Enter your account details</CardDescription>
                </CardHeader>
                <CardContent class="grid gap-4">
                    <div>
                        <Label for="name">Name</Label>
                        <Input id="name" type="text" placeholder="John Doe" v-model="form.name" required autofocus
                            autocomplete="name" />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div>
                        <Label for="email">Email</Label>
                        <Input id="email" type="email" v-model="form.email" required autofocus
                            autocomplete="username" />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div class="flex gap-4">
                        <div class="w-1/2">
                            <Label for="password">Password</Label>
                            <Input id="password" type="password" v-model="form.password" required
                                autocomplete="new-password" />
                            <InputError class="mt-2" :message="form.errors.password" />
                        </div>

                        <div class="w-1/2">
                            <Label for="password">Confirm Password</Label>
                            <Input id="password" type="password" v-model="form.password_confirmation" required
                                autocomplete="new-password" />
                            <InputError class="mt-2" :message="form.errors.password_confirmation" />
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <Link :href="route('login')"
                            class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                        Already registered?
                        </Link>
                    </div>
                </CardContent>

                <CardFooter>
                    <Button class="w-full" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Register
                    </Button>
                </CardFooter>
            </Card>
        </form>
    </GuestLayout>
</template>
