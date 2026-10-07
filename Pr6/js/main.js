import {
    loadEvents,
    setupEventControls
} from "./events.js";

import {
    loadStudents
} from "./students.js";

import {
    loadFAQs
} from "./faqs.js";


document.addEventListener(
    "DOMContentLoaded",
    () => {

        loadEvents();

        setupEventControls();

        loadStudents();

        loadFAQs();

    }
);