import { mount } from '@vue/test-utils';
import { beforeEach, describe, expect, it, vi } from 'vitest';
import App from './App.vue';
import api from './api';

vi.mock('./api', () => ({
    default: {
        get: vi.fn(),
        post: vi.fn(),
    },
}));

describe('Authentication', () => {
    beforeEach(() => {
        sessionStorage.clear();
        vi.clearAllMocks();
    });

    it('shows the login form and allows switching to registration', async () => {
        const wrapper = mount(App);

        expect(wrapper.get('h2').text()).toBe('Log in');
        expect(wrapper.find('input#email').exists()).toBe(true);
        expect(wrapper.find('input#password').exists()).toBe(true);

        await wrapper.get('button.underline').trigger('click');

        expect(wrapper.get('h2').text()).toBe('Create a member account');
        expect(wrapper.find('input#name').exists()).toBe(true);
        expect(wrapper.find('input#password-confirmation').exists()).toBe(true);
    });

    it('stores the token and displays the user after successful login', async () => {
        api.post.mockResolvedValue({
            data: {
                token: 'test-token',
                user: {
                    id: 1,
                    name: 'Test Member',
                    email: 'member@example.com',
                    role: 'member',
                },
            },
        });

        const wrapper = mount(App);

        await wrapper.get('input#email').setValue('member@example.com');
        await wrapper.get('input#password').setValue('password123');
        await wrapper.get('form').trigger('submit');

        expect(api.post).toHaveBeenCalledWith('/login', {
            email: 'member@example.com',
            password: 'password123',
        });
        expect(sessionStorage.getItem('auth_token')).toBe('test-token');
        expect(wrapper.text()).toContain('Welcome, Test Member');
        expect(wrapper.text()).toContain('Role: member');
    });
});
