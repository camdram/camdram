import "./base.js";

window.addEventListener("DOMContentLoaded", () => {

    // show/form.html.twig
    const update_date_fields = function() {
        const val = document.querySelector("#show_multi_venue input:checked")?.value;
        // Display neither field if no radio buttons are clicked to avoid using the wrong one.
        for (const el of document.querySelectorAll(".performances .venue-row")) {
            el.style.display = (val == "multi") ? null : "none";
        }
        for (const el of document.querySelectorAll(".main-venue-row")) {
            el.style.display = (val == "single") ? null : "none";
        }
    };

    const perf_row = document.querySelector("#show_form_performances");
    if (perf_row) {
        perf_row.addEventListener("change", event => {
            const target = event.target;
            if (!target.matches || !target.matches("input[type=date]")) return;

            const performance = target.closest && target.closest(".performance");
            if (!performance) return;

            for (const el of performance.querySelectorAll("small.error, small.warning")) {
                el.remove();
            }
            for (const el of performance.querySelectorAll("input.error")) {
                el.classList.remove("error");
            }

            if (!target.valueAsDate) return;

            for (const input of performance.querySelectorAll("input[type=date]")) {
                // Avoid copying 0002, 0020, 0201 year while typing.
                if (!input.value && target.valueAsDate.getFullYear() > 1950) {
                    input.value  = target.value;
                }
            }

            const startAt = performance.querySelector("input[id*=\"start_at_date\"]");
            const endAt   = performance.querySelector("input[id*=\"repeat_until\"]");

            if (startAt.valueAsDate && endAt.valueAsDate) {
                let message = "";
                let isError = false;
                const now = new Date();
                const softMaxDate = new Date(now.getTime() + 86400000*182);
                // hardMaxDate === PHP's new Date("+18 months")
                const hardMaxDate = new Date(now.getFullYear() + 1 + (now.getMonth() > 5),
                    (now.getMonth() + 6) % 12, now.getDate());
                if (startAt.valueAsDate > endAt.valueAsDate) {
                    message += "The run can’t finish before it’s begun!<br>";
                    isError = true;
                }
                if (startAt.valueAsDate > hardMaxDate || endAt.valueAsDate > hardMaxDate) {
                    message += "Shows may only be listed on Camdram up to 18 months in advance.<br>";
                    isError = true;
                } else if (startAt.valueAsDate > softMaxDate || endAt.valueAsDate > softMaxDate) {
                    message += "Note: one or both dates are more than six months in advance.<br>";
                }
                if (startAt.valueAsDate < now || endAt.valueAsDate < now) {
                    message += "Note: one or both dates are in the past.<br>";
                }
                if (!message) return;

                message += isError ? "Check your dates!" : "Do check for typos, but you're free to post this if it's correct.";
                performance.insertAdjacentHTML("beforeend",
                    (isError ? "<small class=\"error\">" : "<small class=\"warning\">") + message + "</span>");
            }
        });

        for (const el of document.querySelectorAll("#show_multi_venue input")) {
            el.addEventListener("change", update_date_fields);
        }

        update_date_fields();
        document.querySelector(".performances").addEventListener("entitycollection:newrow", update_date_fields);
    }

    // advert.html.twig
    const advert_inputs = document.querySelectorAll("#advert_type input");
    if (advert_inputs.length) {
        const update_view = () => {
            document.querySelector("#auditions-row").style.display =
                (document.querySelector("#advert_type input:checked").value == "actors") ? null : "none";
        };
        for (const input of advert_inputs) input.addEventListener("change", update_view);
        update_view();
    }
});
