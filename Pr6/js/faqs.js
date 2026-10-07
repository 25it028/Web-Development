import { fetchJSON } from "./api.js";

export async function loadFAQs() {

    try {

        const faqs =
            await fetchJSON("./data/faqs.json");

        renderFAQs(faqs);

    } catch (error) {

        console.error(
            "FAQ loading error:",
            error
        );
    }
}

function renderFAQs(faqs) {

    const container =
        document.getElementById("faqContainer");

    container.innerHTML = "";

    faqs.forEach(faq => {

        const faqElement =
            document.createElement("div");

        faqElement.className = "faq";

        faqElement.innerHTML = `
            <div class="faq-question">
                ${faq.question}
            </div>

            <div class="faq-answer">
                ${faq.answer}
            </div>
        `;

        const question =
            faqElement.querySelector(".faq-question");

        const answer =
            faqElement.querySelector(".faq-answer");

        question.addEventListener("click", () => {

            answer.classList.toggle("show");

        });

        container.appendChild(faqElement);
    });
}