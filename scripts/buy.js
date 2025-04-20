document.addEventListener("DOMContentLoaded", function () {
    console.log("hello world");

    const applyFilterBtn = document.getElementById("applyFilter");
    const propertyPrice = document.getElementById("propertyPrice");
    const propertyTypes = document.getElementById("propertyTypes");
    const propertyLocation = document.getElementById("propertyLocation");
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

        fetch("./filterProperties", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                type: selectedType,
                price: price,
                location: location
            })
        })
        .then(res => res.text())
        .then(html => {
            resultContainer.innerHTML = html;

            // ✅ Reinitialize swiper after injecting new DOM content
            if (typeof window.reinitializeAllSwipers === 'function') {
                window.reinitializeAllSwipers();
            } else {
                console.warn("Swiper reinit function not found!");
            }
        })
        .catch(err => console.error("Fetch Error:", err));
    });
});
