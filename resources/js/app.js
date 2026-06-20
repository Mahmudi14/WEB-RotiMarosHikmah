import "./bootstrap";

import Alpine from "alpinejs";
import Sortable from "sortablejs";

window.Alpine = Alpine;
window.Sortable = Sortable;

Alpine.start();

if ("serviceWorker" in navigator) {
    window.addEventListener("load", () => {
        navigator.serviceWorker.register("/sw.js").catch((error) => {
            console.error("Service Worker gagal didaftarkan:", error);
        });
    });
}
