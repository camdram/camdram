import Routing from "router";

Camdram.autocomplete = class {
    #cache; #isMain; #timeout; #lastDisplayedSearch; // Data

    // Elements
    #container; #error; #field; #idField; #noresults; #results; #resultsUl;
    #spinner;

    constructor(container) {
        this.#cache   = {};   // Cache, for storing previous results
        this.#container = container;
        this.#error   = container.querySelector(".results .error");
        this.#field   = container.querySelector("input[type=text]");
        this.#idField = container.querySelector("input[type=hidden]");
        this.#isMain  = container.id == "search_form";
        this.#lastDisplayedSearch = null; // Avoid redrawing the control when no change has occurred
        this.#noresults = container.querySelector(".noresults");
        this.#results = container.querySelector(".results");
        this.#resultsUl = container.querySelector(".results ul");
        this.#spinner = container.querySelector(".fa-spinner");
        this.#timeout = null; // Timeout ID, for request debouncing
        this.searchRoute = container.dataset.entitysearchRoute;

        // Prevent repeat set-up of search.
        container.removeAttribute("data-entitysearch-route");

        container.addEventListener("focus", _e => {
            this.suggest();
            /* .active is to work around lack of :focus-within support and should eventually be removed */
            if (this.#isMain) document.querySelector("header .search").classList.add("active");
        }, true);
        container.addEventListener("blur", e => {
            if (e.relatedTarget && container.contains(e.relatedTarget)) {
                return;
            }
            window.setTimeout(() => {
                this.isVisible = false;
                if (this.#isMain) document.querySelector("header .search").classList.remove("active");
            }, 100);
        }, true);
        this.#field.addEventListener("keyup", e => {
            const contents = this.#field.value.toLowerCase();
            if (this.#isMain && (contents === "zz" || contents === "rr")) Camdram.spinTheWorld();
            if(e.keyCode == 38 || e.keyCode == 40)
                this.shiftOption(e.keyCode == 40);
            else if(e.keyCode == 13)
                this.chooseOption();
            else if(e.keyCode != 37 && e.keyCode != 39)
                this.suggest();
            e.preventDefault();
            return false;
        });
        this.#field.addEventListener("keydown", e => {
            if (e.keyCode == 13) {
                e.preventDefault();
                return false;
            }
            return true;
        });
        this.#field.addEventListener("paste", this.suggest.bind(this));

        const selectLiFromEvent = e => {
            let li = e.target;
            while (li && li.tagName.toLowerCase() != "li" && li != e.currentTarget) li = li.parentNode;
            if (li && li.tagName.toLowerCase() == "li" && !li.classList.contains("active")) {
                for (const el of this.#resultsUl.children) el.classList.remove("active");
                li.classList.add("active");
            }
        };

        this.#results.addEventListener("mousemove", selectLiFromEvent);
        this.#results.addEventListener("click", e => {
            selectLiFromEvent(e);
            this.chooseOption();
            e.preventDefault();
            e.stopPropagation();
        });
    }

    chooseOption() {
        // If we do have a selected item then jump to it, otherwise just search
        const active_item = this.#resultsUl.querySelector("li.active");
        if (this.#isMain) {
            if (active_item) {
                this.#field.value = active_item.querySelector("span").textContent;
                window.location.href = active_item.querySelector("a").href;
            } else {
                this.#container.submit();
            }
        } else {
            this.isVisible = false;
            let eventDetails = null;
            if (active_item) {
                this.#field.value = active_item.querySelector("span").textContent;
                this.#idField.value = active_item.dataset.entitysearchId;
                eventDetails = {
                    id: active_item.dataset.entitysearchId,
                    slug: active_item.dataset.entitysearchSlug,
                    name: this.#field.value
                };
            } else {
                this.#idField.value = "";
            }
            this.#container.dispatchEvent(new CustomEvent("entitysearch:changed",
                { bubbles: true, detail: eventDetails }));
        }
    }

    shiftOption(down) {
        const firstItem = this.#resultsUl.querySelector("li");
        if (firstItem == null) return;

        const current = this.#resultsUl.querySelector("li.active");

        if (current == null) {
            if (down) firstItem.classList.add("active");
        } else if (down && current.nextElementSibling) {
            current.classList.remove("active");
            current.nextElementSibling.classList.add("active");
        } else if (!down && current.previousElementSibling) {
            current.classList.remove("active");
            current.previousElementSibling.classList.add("active");
        }
    }

    get isVisible() {
        return this.#results.style.display != "none";
    }
    set isVisible(show) {
        if (show) {
            this.#results.style.display = null;
            window.setTimeout(() => {
                this.#results.classList.remove("hidden");
            }, 5);
        } else {
            this.#results.classList.add("hidden");
            window.setTimeout(() => {
                this.#results.style.display = "none";
            }, 400);
        }
    }

    suggest() {
        if (this.#timeout !== null) {
            window.clearTimeout(this.#timeout);
        }
        this.#timeout = window.setTimeout(this.suggestNow.bind(this), 100);
    }

    suggestNow() {
        const typed = this.#field.value;
        if (typed.length < 2) {
            this.isVisible = false;
            return;
        } else if (this.isVisible && typed === this.#lastDisplayedSearch) {
            return;
        }
        this.#lastDisplayedSearch = typed;

        if (typeof this.#cache[typed] !== "undefined") {
            // We've done this request before, so load the results from the cache
            this.displayResults(typed, this.#cache[typed]);
        } else {
            this.#spinner.classList.add("show");
            // Activate the field
            const url = Routing.generate(this.searchRoute,
                {_format: "json", mode: ".json", q: typed, limit: 10});
            Camdram.get(url, responseText => {
                const data = JSON.parse(responseText);
                this.displayResults(typed, data, false);
                this.#cache[typed] = data;
                this.#spinner.classList.remove("show");
            }, () => { // error
                this.displayResults(typed, [], true);
                this.#spinner.classList.remove("show");
            });
        }
    }

    displayResults(query, items, error) {
        const prevResultCount = this.#resultsUl.children.length;

        this.#resultsUl.innerHTML = "";
        this.#noresults.style.display = "none";
        this.#error.style.display = "none";

        let first_item = true;

        // Draw out the elements
        if (items.length > 0) {
            let i = 0;
            for (const result of items) {
                const item = document.createElement("li");
                item.dataset.entitysearchId = result.id;
                item.dataset.entitysearchSlug = result.slug;
                item.innerHTML = "<a class=\"resultText\"><i></i><span></span></a>";

                // Autoselect the first item
                if (first_item) {
                    first_item = false;
                    item.classList.add("active");
                }

                // Add in the text
                const link = item.children[0];
                link.href = Routing.generate("get_"+result.entity_type, {identifier: result.slug});

                // Add in the icon
                if (this.#isMain) {
                    let icon_class = "fa fa-user";
                    switch (result.entity_type) {
                    case "event":   icon_class = "fa fa-user-circle"; break;
                    case "show":    icon_class = "fa fa-ticket"; break;
                    case "venue":   icon_class = "fa fa-building"; break;
                    case "society": icon_class = "fa fa-briefcase"; break;
                    }
                    link.children[0].className = icon_class;
                }
                link.children[1].textContent = result.name;

                if (result.entity_type == "person" && result.first_active != null && result.last_active != null) {
                    const from = Camdram.parseISODate(result.first_active);
                    const till = Camdram.parseISODate(result.last_active);
                    const now_ms = Date.now();
                    const fromString = Camdram.formatMMMYYYY(from);
                    const tillString = Camdram.formatMMMYYYY(till);
                    const SIX_MONTHS = 180*86400*1000;
                    const em = document.createElement("em");

                    if (now_ms - from.valueOf() < SIX_MONTHS && now_ms - till.valueOf() < SIX_MONTHS) {
                        // All activity within past six months
                        em.textContent = "\xA0(active currently)";
                    } else if (fromString === tillString) {
                        // All activity in same month
                        em.textContent = `\xA0(active ${fromString})`;
                    } else if (now_ms - till.valueOf() < SIX_MONTHS) {
                        // Active both within and before past six months
                        em.textContent = `\xA0(active since ${fromString})`;
                    } else {
                        // Active only prior to past six months
                        em.textContent = `\xA0(active ${fromString}–${tillString})`;
                    }

                    link.appendChild(em);
                }

                if (result.entity_type == "show" && result.start_at != "") {
                    const date = Camdram.parseISODate(result.start_at);
                    const em = document.createElement("em");
                    if (date) {
                        em.textContent = "\xA0(" + Camdram.formatMMMYYYY(date) + ")";
                        link.appendChild(em);
                    }
                }

                if (i >= prevResultCount) {
                    item.classList.add("hidden");
                }
                // Add item into the page
                this.#resultsUl.appendChild(item);
                i++;
            }
            if (this.#isMain) {
                const allResultsItem = document.createElement("li");
                if (i >= prevResultCount) allResultsItem.classList.add("hidden");
                allResultsItem.innerHTML = "<a class=\"resultText\"><i></i><span>See all results...</span></a>";
                allResultsItem.children[0].href = Routing.generate(this.searchRoute, {q: query});
                this.#resultsUl.appendChild(allResultsItem);
            }
            this.isVisible = true;
            window.setTimeout(() => {
                for (const el of this.#resultsUl.querySelectorAll(".hidden")) {
                    el.classList.remove("hidden");
                }
            }, 10);
        } else if (error || items.error) {
            this.#error.textContent = items.error ?
                "An error occured. " + items.error :
                "Search is not available at the moment";
            this.#error.style.display = null;
            this.isVisible = true;
        } else {
            this.#noresults.style.display = null;
            this.#noresults.querySelector(".query").textContent = query;
            this.isVisible = true;
        }
    }
};
