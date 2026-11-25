<template>
    <main class="min-h-screen bg-gray-100 p-6 text-gray-900 sm:p-8">
        <div class="mx-auto max-w-6xl">
            <header class="mb-8 flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wide text-blue-700">
                        Filipino Club
                    </p>
                    <h1 class="mt-2 text-3xl font-bold">Dues Tracker</h1>
                    <p class="mt-2 text-gray-600">
                        Manage members, dues, and payment records.
                    </p>
                </div>

                <button
                    v-if="user"
                    class="rounded border border-gray-300 bg-white px-4 py-2 text-sm font-medium"
                    type="button"
                    @click="logout"
                >
                    Log out
                </button>
            </header>

            <section
                v-if="!user"
                class="max-w-lg rounded-lg border border-gray-200 bg-white p-6 shadow-sm"
            >
                <h2 class="text-xl font-semibold">
                    {{ isRegistering ? 'Create a member account' : 'Log in' }}
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    New accounts are registered with the member role.
                </p>

                <form class="mt-6 space-y-4" @submit.prevent="submitAuth">
                    <div v-if="isRegistering">
                        <label class="mb-1 block text-sm font-medium" for="name">Name</label>
                        <input
                            id="name"
                            v-model.trim="form.name"
                            autocomplete="name"
                            required
                            class="w-full rounded border border-gray-300 px-3 py-2"
                        >
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium" for="email">Email</label>
                        <input
                            id="email"
                            v-model.trim="form.email"
                            type="email"
                            autocomplete="email"
                            required
                            class="w-full rounded border border-gray-300 px-3 py-2"
                        >
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium" for="password">
                            Password
                        </label>
                        <input
                            id="password"
                            v-model="form.password"
                            type="password"
                            :autocomplete="isRegistering ? 'new-password' : 'current-password'"
                            required
                            minlength="8"
                            class="w-full rounded border border-gray-300 px-3 py-2"
                        >
                    </div>

                    <div v-if="isRegistering">
                        <label class="mb-1 block text-sm font-medium" for="password-confirmation">
                            Confirm password
                        </label>
                        <input
                            id="password-confirmation"
                            v-model="form.password_confirmation"
                            type="password"
                            autocomplete="new-password"
                            required
                            minlength="8"
                            class="w-full rounded border border-gray-300 px-3 py-2"
                        >
                    </div>

                    <p v-if="errorMessage" role="alert" class="text-sm text-red-700">
                        {{ errorMessage }}
                    </p>

                    <button
                        type="submit"
                        :disabled="loading"
                        class="w-full rounded bg-blue-700 px-4 py-2 font-semibold text-white disabled:opacity-60"
                    >
                        {{ loading ? 'Please wait…' : (isRegistering ? 'Register' : 'Log in') }}
                    </button>
                </form>

                <button
                    class="mt-4 text-sm font-medium text-blue-700 underline"
                    type="button"
                    @click="toggleAuthMode"
                >
                    {{ isRegistering ? 'Already have an account? Log in' : 'Create a member account' }}
                </button>
            </section>

            <template v-else>
                <section class="mb-8 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                    <h2 class="text-xl font-semibold">Welcome, {{ user.name }}</h2>
                    <p class="mt-2 text-gray-600">{{ user.email }}</p>
                    <p class="mt-1 text-sm text-gray-500">Role: {{ user.role }}</p>
                </section>

                <section aria-label="Dashboard summary" class="grid gap-4 sm:grid-cols-3">
                    <article
                        v-for="item in summaryItems"
                        :key="item.label"
                        class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm"
                    >
                        <h2 class="text-sm font-medium text-gray-600">{{ item.label }}</h2>
                        <p class="mt-3 text-2xl font-bold">{{ item.value }}</p>
                        <p class="mt-1 text-sm text-gray-500">{{ item.note }}</p>
                    </article>
                </section>

                <section class="mt-8 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                    <h2 class="text-xl font-semibold">Recent payments</h2>
                    <p class="mt-3 text-gray-600">
                        Payment history will appear here once the dashboard data is connected.
                    </p>
                </section>
            </template>
        </div>
    </main>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import api from './api';

const user = ref(null);
const isRegistering = ref(false);
const loading = ref(false);
const errorMessage = ref('');

const form = reactive({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const summaryItems = [
    { label: 'Members', value: '—', note: 'Dashboard data not connected yet' },
    { label: 'Dues collected', value: '—', note: 'Dashboard data not connected yet' },
    { label: 'Outstanding dues', value: '—', note: 'Dashboard data not connected yet' },
];

function clearForm() {
    form.name = '';
    form.email = '';
    form.password = '';
    form.password_confirmation = '';
    errorMessage.value = '';
}

function toggleAuthMode() {
    isRegistering.value = !isRegistering.value;
    clearForm();
}

async function submitAuth() {
    loading.value = true;
    errorMessage.value = '';

    try {
        const endpoint = isRegistering.value ? '/register' : '/login';
        const payload = isRegistering.value
            ? { ...form }
            : { email: form.email, password: form.password };

        const response = await api.post(endpoint, payload);
        sessionStorage.setItem('auth_token', response.data.token);
        user.value = response.data.user;
        clearForm();
    } catch (error) {
        errorMessage.value =
            error.response?.data?.message
            ?? Object.values(error.response?.data?.errors ?? {}).flat()[0]
            ?? 'Unable to sign in. Please try again.';
    } finally {
        loading.value = false;
    }
}

async function logout() {
    try {
        await api.post('/logout');
    } catch {
        // Clear the local session even if the API request fails.
    }

    sessionStorage.removeItem('auth_token');
    user.value = null;
    isRegistering.value = false;
    clearForm();
}

onMounted(async () => {
    if (!sessionStorage.getItem('auth_token')) return;

    try {
        const response = await api.get('/user');
        user.value = response.data.data ?? response.data;
    } catch {
        sessionStorage.removeItem('auth_token');
    }
});
</script>
