document.addEventListener("DOMContentLoaded", function () {
    // Find all elements with the class "read-more"
    const readMoreButtons = document.querySelectorAll(".read-more");

    // Add a click event listener to each "Read More" button
    readMoreButtons.forEach((button) => {
        button.addEventListener("click", function (event) {
            event.preventDefault();

            const article = this.closest(".article");
            const fullArticleText = article.querySelector(".full-article-text");
            const shortArticleText = article.querySelector(".short-article-text");

            // Nacita full text
            if (fullArticleText.style.display === "none" || fullArticleText.style.display === "") {
                fullArticleText.style.display = "block";
                shortArticleText.style.display = "none";
                this.textContent = "Collapse";
            } else {
                fullArticleText.style.display = "none";
                shortArticleText.style.display = "block"
                this.textContent = "Read More";
            }
        });
    });
});
