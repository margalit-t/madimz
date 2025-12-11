//search

document.addEventListener("DOMContentLoaded", function () {
    const input = document.getElementById("live-search");
    const resultsBox = document.getElementById("search-results");

    input.addEventListener("keyup", function () {
        let s = this.value.trim();

        if (s.length < 2) {
            resultsBox.innerHTML = "";
            resultsBox.style.display = "none";
            return;
        }

        fetch(ajax_obj.ajaxurl, {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: new URLSearchParams({
                action: "custom_ajax_search",
                search: s
            })
        })
            .then(res => res.json())
            .then(data => {
                console.log("Results:", data);

                resultsBox.innerHTML = "";
                resultsBox.style.display = "block";

                if (data.length === 0) {
                    resultsBox.innerHTML = "<div class='nores'>לא נמצאו תוצאות</div>";
                    return;
                }

                data.forEach(item => {
                    const row = document.createElement("a");
                    row.href = item.link;
                    row.classList.add("search-item");

                    row.innerHTML = `
                        <img src="${item.image}" class="search-thumb">
                        <span>${item.title}</span>
                    `;

                    resultsBox.appendChild(row);
                });

                // כפתור לכל התוצאות
                const allResults = document.createElement("a");
                allResults.href = "/?s=" + encodeURIComponent(s) + "&post_type=product";
                allResults.classList.add("all-results");
                allResults.textContent = "לכל התוצאות…";

                resultsBox.appendChild(allResults);
            })
            .catch(err => {
                alert(ajax_obj.error_msg);
            });
    });
});

//cf7

document.addEventListener('wpcf7mailsent', function () {
    if (typeof ajax_obj === 'undefined') return;

    fetch(ajax_obj.ajaxurl + '?action=madimz_generate_ticket')
        .then(res => res.json())
        .then(data => {
            if (data.success && data.data.ticket) {
                const ticket = data.data.ticket;
                window.location.href =
                    ajax_obj.thank_you_url +
                    '?ticket=' + encodeURIComponent(ticket);
            } else {
                window.location.href = ajax_obj.thank_you_url;
            }
        })
        .catch(() => {
            window.location.href = ajax_obj.thank_you_url;
        });

}, false);
