import "./bootstrap";

import Alpine from "alpinejs";

window.Alpine = Alpine;

window.refreshCsrfToken = async function () {
    try {
        const response = await fetch("/csrf-token", {
            method: "GET",
            credentials: "same-origin",
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                Accept: "application/json",
            },
        });

        if (!response.ok) {
            return null;
        }

        const payload = await response.json();
        return payload.token || null;
    } catch (error) {
        return null;
    }
};

window.refreshCsrfAndSubmit = async function (event, form) {
    event.preventDefault();

    const token = await window.refreshCsrfToken();
    if (token) {
        const tokenInput = form.querySelector('input[name="_token"]');
        if (tokenInput) {
            tokenInput.value = token;
        }
    }

    form.submit();
    return false;
};

Alpine.start();
