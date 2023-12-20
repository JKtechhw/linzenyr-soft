const searchBox = document.querySelector("#search-box input");
const searchTargetElement = document.querySelector("#search-box #search-output");

searchBox?.addEventListener("input", async () => {
    const searchFetch = await fetch(searchBox.dataset.action + "?q=" + searchBox.value);
    const responseText = await searchFetch.text();
    let responseJson;

    try {
        responseJson = JSON.parse(responseText);
    }

    catch(e) {
        console.error(responseText);
    }


    if(searchFetch.ok) {
        searchTargetElement.textContent = "";

        if(responseJson.length == 0) {
            return;
        }

        for(const article of responseJson) {
            targetLink = searchBox.dataset.action.startsWith("..") ? "../article-detail?article=" + article.articleID : "./article-detail?article=" + article.articleID;

            const articleLink = document.createElement("a");
            articleLink.href = targetLink;
            articleLink.classList.add("search-article");
            articleLink.textContent = article.title;

            searchTargetElement.appendChild(articleLink);
        }
    }

    else {
        console.error(responseJson);
    }
});