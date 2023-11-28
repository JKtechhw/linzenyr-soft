const logoutElements = document.querySelectorAll(".logout");
logoutElements.forEach(element => {
    element.addEventListener("click", async (e) => {
        e.preventDefault();
        
        const FD = new FormData();
        FD.append("logout", "1");

        console.log(element.href)

        const logoutFetch = await fetch(element.href.endsWith("/") ? element.href : element.href +"/", {
            method: "POST",
            body: FD
        });

        if(logoutFetch?.ok) {
            const responseData = await logoutFetch.text();
            console.log(responseData);
            window.location.href = element.href;
        }

        else {
            const responseData = await logoutFetch.json();
            console.error(responseData);
        }
    });
});