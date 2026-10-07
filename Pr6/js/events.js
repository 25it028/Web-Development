import { fetchJSON } from "./api.js";

let events = [];
let currentPage = 1;

const recordsPerPage = 6;

export async function loadEvents() {

    try {

        showLoading(true);

        events = await fetchJSON("./data/events.json");

        showLoading(false);

        createCategoryFilter();

        renderEvents();

    } catch (error) {

        showLoading(false);

        document.getElementById("error").textContent =
            "Unable to load events. Please try again.";

        console.error(error);
    }
}

function renderEvents() {

    const searchText =
        document.getElementById("searchInput").value
        .toLowerCase();

    const category =
        document.getElementById("categoryFilter").value;

    const sortType =
        document.getElementById("sortSelect").value;

    let filteredEvents = events.filter(event => {

        const matchesSearch =
            event.title.toLowerCase().includes(searchText);

        const matchesCategory =
            category === "all" ||
            event.category === category;

        return matchesSearch && matchesCategory;
    });

    filteredEvents = sortEvents(
        filteredEvents,
        sortType
    );

    const totalPages =
        Math.ceil(
            filteredEvents.length / recordsPerPage
        );

    if (currentPage > totalPages) {
        currentPage = 1;
    }

    const start =
        (currentPage - 1) * recordsPerPage;

    const end =
        start + recordsPerPage;

    const pageEvents =
        filteredEvents.slice(start, end);

    renderEventCards(pageEvents);

    renderPagination(totalPages);
}

function renderEventCards(eventList) {

    const container =
        document.getElementById("eventContainer");

    container.innerHTML = "";

    if (eventList.length === 0) {

        container.innerHTML =
            "<p>No events found.</p>";

        return;
    }

    eventList.forEach(event => {

        const card = document.createElement("div");

        card.className = "card";

        card.innerHTML = `
            <h3>${event.title}</h3>

            <span class="badge">
                ${event.category}
            </span>

            <p><strong>Date:</strong> ${event.date}</p>

            <p><strong>Venue:</strong> ${event.venue}</p>

            <p><strong>Organizer:</strong>
                ${event.organizer}
            </p>
        `;

        container.appendChild(card);
    });
}

function createCategoryFilter() {

    const select =
        document.getElementById("categoryFilter");

    const categories =
        [...new Set(
            events.map(event => event.category)
        )];

    categories.forEach(category => {

        const option =
            document.createElement("option");

        option.value = category;
        option.textContent = category;

        select.appendChild(option);
    });
}

function sortEvents(eventList, sortType) {

    const sorted =
        [...eventList];

    if (sortType === "titleAsc") {

        sorted.sort((a, b) =>
            a.title.localeCompare(b.title)
        );

    } else if (sortType === "titleDesc") {

        sorted.sort((a, b) =>
            b.title.localeCompare(a.title)
        );

    } else if (sortType === "dateAsc") {

        sorted.sort((a, b) =>
            new Date(a.date) - new Date(b.date)
        );

    } else if (sortType === "dateDesc") {

        sorted.sort((a, b) =>
            new Date(b.date) - new Date(a.date)
        );
    }

    return sorted;
}

function renderPagination(totalPages) {

    const pagination =
        document.getElementById("pagination");

    pagination.innerHTML = "";

    if (totalPages <= 1) {
        return;
    }

    const previousButton =
        document.createElement("button");

    previousButton.textContent = "Previous";

    previousButton.disabled =
        currentPage === 1;

    previousButton.addEventListener("click", () => {

        currentPage--;

        renderEvents();
    });

    pagination.appendChild(previousButton);

    for (let i = 1; i <= totalPages; i++) {

        const button =
            document.createElement("button");

        button.textContent = i;

        button.disabled =
            i === currentPage;

        button.addEventListener("click", () => {

            currentPage = i;

            renderEvents();
        });

        pagination.appendChild(button);
    }

    const nextButton =
        document.createElement("button");

    nextButton.textContent = "Next";

    nextButton.disabled =
        currentPage === totalPages;

    nextButton.addEventListener("click", () => {

        currentPage++;

        renderEvents();
    });

    pagination.appendChild(nextButton);
}

function showLoading(show) {

    document.getElementById("loading").style.display =
        show ? "block" : "none";
}

export function setupEventControls() {

    document
        .getElementById("searchInput")
        .addEventListener("input", () => {

            currentPage = 1;
            renderEvents();
        });

    document
        .getElementById("categoryFilter")
        .addEventListener("change", () => {

            currentPage = 1;
            renderEvents();
        });

    document
        .getElementById("sortSelect")
        .addEventListener("change", () => {

            currentPage = 1;
            renderEvents();
        });
}