import { fetchJSON } from "./api.js";

export async function loadStudents() {

    try {

        const students =
            await fetchJSON("./data/students.json");

        renderStudents(students);

    } catch (error) {

        console.error(
            "Student loading error:",
            error
        );
    }
}

function renderStudents(students) {

    const container =
        document.getElementById("studentContainer");

    container.innerHTML = "";

    students.forEach(student => {

        const card =
            document.createElement("div");

        card.className = "card";

        card.innerHTML = `
            <h3>${student.name}</h3>

            <p>
                <strong>Course:</strong>
                ${student.course}
            </p>

            <p>
                <strong>Year:</strong>
                ${student.year}
            </p>

            <p>
                <strong>City:</strong>
                ${student.city}
            </p>

            <p>
                <strong>CGPA:</strong>
                ${student.cgpa}
            </p>
        `;

        container.appendChild(card);
    });
}