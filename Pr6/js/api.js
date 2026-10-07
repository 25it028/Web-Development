export async function fetchJSON(url) {

    const response = await fetch(url);

    if (!response.ok) {
        throw new Error(
            `HTTP Error: ${response.status}`
        );
    }

    return await response.json();
}