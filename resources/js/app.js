import './bootstrap';
import { mount } from 'svelte';
import App from './Pages/App.svelte';

const app = mount(App, {
    target: document.getElementById('app'),
});
