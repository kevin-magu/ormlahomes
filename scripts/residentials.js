document.addEventListener("DOMContentLoaded", function () {
    const applyFilterBtn = document.getElementById("applyFilter");
    const propertyPrice = document.getElementById("propertyPrice");
    const propertyTypes = document.getElementById("propertyTypes");
    const propertyLocation = document.getElementById("propertyLocation");
    const listingType = document.getElementById("listingType");
    const resultContainer = document.getElementById("propertyResults");

    let selectedType = null;

    propertyTypes.addEventListener("click", function (e) {
        if (e.target.tagName === "P") {
            selectedType = e.target.getAttribute("data-type");
            [...propertyTypes.children].forEach(p => p.classList.remove("selected"));
            e.target.classList.add("selected");
        }
    });

    applyFilterBtn.addEventListener("click", function () {
        const price = propertyPrice.value;
        const location = propertyLocation.value;
        const listing = listingType.value;

        fetch("./filterResidentialProperties", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                type: selectedType,
                price: price,
                location: location,
                listingType: listing
            })
        })
        .then(res => res.text())
        .then(html => {
            resultContainer.innerHTML = html;

            if (typeof window.reinitializeAllSwipers === 'function') {
                window.reinitializeAllSwipers();
                window.reinitializeHeartIcons();
            } else {
                console.warn("Swiper reinit function not found!");
            }
        })
        .catch(err => console.error("Fetch Error:", err));
    });
});
