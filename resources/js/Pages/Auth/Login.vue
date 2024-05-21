<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from "@/Components/ui/button";
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/Components/ui/card'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import Checkbox from '@/components/ui/checkbox/Checkbox.vue';
defineProps<{
    canResetPassword?: boolean;
    status?: string;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => {
            form.reset('password');
        },
    });
};
</script>

<template>
    <GuestLayout>

        <Head title="Log in" />

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <Card class="w-full max-w-sm">
                <CardHeader>
                    <CardTitle class="text-2xl">
                        Login
                    </CardTitle>
                    <CardDescription>
                        Enter your email below to login to your account.
                    </CardDescription>
                </CardHeader>
                <CardContent class="grid gap-4">
                    <div class="grid gap-2">
                        <Label for="email">Email</Label>
                        <Input id="email" type="email" placeholder="m@example.com" v-model="form.email" required  autofocus autocomplete="username"/>
                        <InputError class="mt-2" :message="form.errors.email" />

                    </div>
                    <div class="grid gap-2">
                        <Label for="password">Password</Label>
                        <Input id="password" type="password" v-model="form.password" required autocomplete="new-password" />
                        <InputError class="mt-2" :message="form.errors.password" />

                    </div>
                    <div class="flex justify-between">
                        <label class="flex items-center">
                            <Checkbox name="remember" v-model:checked="form.remember" />
                            <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">Remember me</span>
                        </label>
                        <Link v-if="canResetPassword" :href="route('password.request')"
                            class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                        Forgot your password?
                        </Link>



                    </div>
                </CardContent>
                <CardFooter>
                    <Button class="w-full" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Sign in
                    </Button>
                </CardFooter>
            </Card>
        </form>
    </GuestLayout>
</template>
