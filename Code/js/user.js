const logoutElements = document.querySelectorAll(".logout");
logoutElements.forEach(element => {
    element.addEventListener("click", async (e) => {
        e.preventDefault();
        
        const FD = new FormData();
        FD.append("logout", "1");

        console.log(element.href.endsWith("/") ? element.href : element.href +"/")

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

const forms = document.querySelectorAll("form");
for(const form of forms) {
    if(form?.dataset?.formEvents) {
        if(form.dataset.formEvents == "none") {
            continue;
        } 
    }

    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        const submitButton = form.querySelector("button[type=\"submit\"]");
        submitButton.disabled = true;

        const errorFields = form.querySelectorAll(".error-field");
        errorFields.forEach(element => {
            element.classList.remove("error-field");
        });

        const errorMessages = form.querySelectorAll(".error-message");
        errorMessages.forEach(element => {
            element.remove();
        });

        if(typeof tinymce != "undefined") {
            tinymce.triggerSave();
        }

        else {
            console.log("Tinymce is not defined");
        }
        const FD = new FormData(form);
        
        const submitFetch = await fetch(form.action, {
            method: form.method,
            body: FD
        });

        const responseText = await submitFetch.text();

        console.log(responseText);

        let responseJson;

        try {
            responseJson = JSON.parse(responseText)
        }

        catch(e) {
            console.error(responseText);
        }

        console.log(responseJson);

        if(submitFetch?.ok) {
            if(form?.dataset?.reloadOnsuccess && form.dataset.reloadOnsuccess == "true") {
                window.location.reload();
                return;
            }

            if(typeof responseJson["redirect-page"] != "undefined") {
                window.location.href = responseJson["redirect-page"];
                return;
            }

            const successMessageElement = document.createElement("p");
            successMessageElement.classList.add("success-message");
            successMessageElement.textContent = responseJson.message;

            if(form.querySelector(".buttons-row") == null) {
                form.querySelector("button[type=\"submit\"]").insertAdjacentElement("beforebegin", successMessageElement);
            }

            else {
                form.querySelector(".buttons-row").insertAdjacentElement("beforebegin", successMessageElement);
            }
        }

        else {
            if(typeof responseJson["error-field"] != "undefined") {
                let errorFieldElement = form.querySelector(`[name="${responseJson["error-field"]}"]`);

                if(errorFieldElement == null) {
                    errorFieldElement = form.querySelector(`[data-name="${responseJson["error-field"]}"]`);
                }

                errorFieldElement?.classList?.add("error-field");
            }

            let errorMessage;

            if(typeof responseJson["message"] != "undefined") {
                errorMessage = responseJson["message"];
            }

            else {
                errorMessage = "Došlo k neznámé chybě, zkuste to znovu později, pokud chyba přetrvá, využijte helpdesk";
            }

            const errorMessageElement = document.createElement("p");
            errorMessageElement.classList.add("error-message");
            errorMessageElement.textContent = errorMessage;

            submitButton.disabled = false;

            if(form.querySelector(".buttons-row") == null) {
                form.querySelector("button[type=\"submit\"]").insertAdjacentElement("beforebegin", errorMessageElement);
            }

            else {
                form.querySelector(".buttons-row").insertAdjacentElement("beforebegin", errorMessageElement);
            }
        }
    });

    if(form?.dataset?.waitOnChange && form.dataset.waitOnChange == "true") {
        const submitButton = form.querySelector("button[type=\"submit\"]");
        submitButton != null ? submitButton.disabled = true : null;
        form.addEventListener("input", () => {
            submitButton.disabled = false;
        }, {once: true});
    }
}

const selectMultipleElements = document.querySelectorAll(".select-multiple");
for(const selectBox of selectMultipleElements) {
    const triggerBox = selectBox.querySelector(".select-multiple-trigger");
    const optionsBox = selectBox.querySelector(".select-multiple-options");
    const options = optionsBox.querySelectorAll(".select-multiple-option");
    const inputsBox = selectBox.querySelector(".select-multiple-inputs");
    const selectedBox = selectBox.querySelector(".select-multiple-selected");

    if(options.length == 0) {
        continue;
    }

    function updateInputs() {
        while (inputsBox.firstChild) {
            inputsBox.removeChild(inputsBox.firstChild);
        }

        const selectedElements = selectBox.querySelectorAll(".selected-element");
        for (const selected of selectedElements) {
            const input = document.createElement("input");
            input.name = selectBox.dataset.name;
            input.type = "hidden";
            input.value = selected.dataset.value;
            inputsBox.appendChild(input);
        }
    }

    function hideOptions(e) {
        if(typeof e == "undefined") {
            selectBox.classList.remove("active");
            optionsBox.classList.remove("active");
            return;
        }
        
        if(selectBox.contains(e.target) == false) {
            selectBox.classList.remove("active");
            optionsBox.classList.remove("active");
            document.body.removeEventListener("click", hideOptions);
        }
    }

    triggerBox.addEventListener("click", (e) => {
        if(e.target == e.currentTarget) {
            if(optionsBox.classList.contains("active")) {
                selectBox.classList.remove("active");
                optionsBox.classList.remove("active");
                document.body.removeEventListener("click", hideOptions);
            }

            else {
                selectBox.classList.add("active");
                optionsBox.classList.add("active");
                document.body.addEventListener("click", hideOptions);
            }
        }
    });

    for(const [index, optionElement] of options.entries()) {
        optionElement.addEventListener("click", () => {
            if(optionElement.classList.contains("selected-option")) {
                const selectedToRemove = selectedBox.querySelector(`[data-index="${index}"]`);
                selectedToRemove?.remove();
                optionElement.classList.remove("selected-option");
                updateInputs();
                return;
            }

            optionElement.classList.add("selected-option");

            const selectedElement = document.createElement("div");
            selectedElement.dataset.index = index;
            selectedElement.classList.add("selected-element");
            selectedElement.textContent = optionElement.textContent;
            selectedElement.dataset.value = optionElement.dataset.value;

            const removeButton = document.createElement("button");
            removeButton.classList.add("selected-element-remove");
            removeButton.type = "button";

            removeButton.addEventListener("mousedown", function(e) {
                hideOptions();
                e.preventDefault();
                selectedElement.remove();
                optionElement.classList.remove("selected-option");
                updateInputs();
            });

            selectedElement.appendChild(removeButton);
            selectedBox.appendChild(selectedElement);

            updateInputs();
        });

        if(typeof optionElement?.dataset?.selected != "undefined") {
            if(optionElement?.dataset?.selected == "true") {
                optionElement.dispatchEvent(new Event("click"));
            }
        }
    }
}

const checkboxRoles = document.querySelectorAll("input[type=\"checkbox\"][data-role]");
checkboxRoles.forEach(element => {
    if(element.dataset.role == "select-all") {
        const targetElement = document.querySelector(element.dataset.target);
        const targetCheckboxes = targetElement.querySelectorAll("input[type=\"checkbox\"]");

        element.addEventListener("change",() => {
            const check = element.checked;    
            targetCheckboxes.forEach(input => {
                input.checked = check;
                input.dispatchEvent(new Event("change"));
            });
        });
    }
});

const releaseTable = document.querySelector("#release-table");
if(releaseTable != null) {
    const rowCheckboxes = releaseTable.querySelectorAll("input[type=\"checkbox\"]");
    const submitButton = document.querySelector("#submit-release");
    rowCheckboxes.forEach(element => {
        element.addEventListener("change", () => {
            const checkedElements = releaseTable.querySelectorAll("input[type=\"checkbox\"]:checked");
            if(rowCheckboxes.length == checkedElements.length) {
                checkboxRoles[0].indeterminate = false;
                checkboxRoles[0].checked = true;
                submitButton.disabled = false;
            }

            else if (checkedElements.length == 0) {
                checkboxRoles[0].indeterminate = false;
                checkboxRoles[0].checked = false;
                submitButton.disabled = true;
            }

            else {
                checkboxRoles[0].indeterminate = true;
                submitButton.disabled = false;
            }
        })
    });
}

const submitReleaseButton = document.querySelector("#submit-release[data-source-data]");
if(submitReleaseButton != null) {
    const sourceDataElement = document.querySelector(submitReleaseButton.dataset.sourceData);

    submitReleaseButton.addEventListener("click", async () => {
        const valueElements = sourceDataElement.querySelectorAll("[data-value]");
        const releaseNameInput = document.querySelector("input[name=\"release-title\"]");
        
        const FD = new FormData();
        FD.append("action-page", "submit-release");
        FD.append("release-title", releaseNameInput.value);

        valueElements.forEach((element) => {
            FD.append("article[]", element.dataset.value);
        });        

        const submitFetch = await fetch(window.location.pathname, {
            method: "POST",
            body: FD
        });

        const responseText = await submitFetch.text();
        let responseJson;
        try {
            responseJson = JSON.parse(responseText);
        }

        catch(e) {
            console.error(responseText);
            return;
        }

        if(submitFetch?.ok) {
            window.location.reload();
        }

        else {
            //TODO
        }

        console.log(responseJson);
    });
}