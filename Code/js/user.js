const logoutElements = document.querySelectorAll(".logout");
logoutElements.forEach(element => {
    element.addEventListener("click", async (e) => {
        e.stopPropagation();
        e.preventDefault();
        
        const FD = new FormData();
        FD.append("logout", "1");

        const logoutFetch = await fetch(window.location.pathname, {
            method: "POST",
            body: FD
        });

        if(logoutFetch?.ok) {
            window.location.reload();
        }

        else {
            const responseData = await logoutFetch.json();
            console.error(responseData);
        }
    });
});