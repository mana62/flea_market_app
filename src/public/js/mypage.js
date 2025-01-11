"use strict";

document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".tabs a").forEach((tab) => {
        tab.addEventListener("click", (event) => {
            event.preventDefault();
            const url = tab.getAttribute("href");
            window.location.href = url;
        });
    });
});
