const form = document.querySelector("form");
const submitButton = form.querySelector("button[type=\"submit\"]");

form.addEventListener("submit", async (e) => {
    e.preventDefault();
    e.stopPropagation();

    const errorFields = form.querySelectorAll(".invalid");
    errorFields.forEach(element => element.classList.remove("invalid"));

    const errorMessages = form.querySelectorAll(".error-message");
    errorMessages.forEach(element => element.remove());

    const FD = new FormData(form);

    const submitFetch = await fetch(window.location.pathname, {
        method: "POST",
        body: FD
    });

    const responseJson = await submitFetch.json();

    if(submitFetch?.ok) {
        window.location.reload();
    }

    else {
        //Check error field
        if(typeof responseJson["error-field"] != "undefined") {
            const errorField = form.querySelector(`[name="${responseJson["error-field"]}"]`);
            errorField.classList.add("invalid");
        }

        if(typeof responseJson["message"] != "undefined") {
            const errorMessage = document.createElement("p");
            errorMessage.classList.add("error-message");
            errorMessage.textContent = responseJson["message"];
            submitButton.insertAdjacentElement("beforebegin", errorMessage);
        }

    }
});

form.addEventListener("keyup", () => {
    const inputs = form.querySelectorAll("input");
    for(const input of inputs) {
        if(input.value == "") {
            submitButton.disabled = true;
            return;
        }
    }

    submitButton.disabled = false;
});