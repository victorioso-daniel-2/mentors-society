import "../css/app.css"; // <-- Add this line at the very top
import { createApp } from "vue";
import App from "./components/App.vue";

// Create Vue application
const app = createApp({});

// Mount the app
createApp(App).mount("#app");
