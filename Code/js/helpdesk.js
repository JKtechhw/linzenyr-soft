'use strict';

class helpdesk {
    messagesHash;
    messagesElement;

    constructor(messagesElement, messagesFormElement) {
        this.messagesElement = messagesElement;
        if(typeof messagesFormElement != "undefined" && messagesFormElement != null) {
            this.setFormEvents(messagesFormElement);
        }
        this.startHelpdesk();
        this.setCloseTicket(messagesFormElement);
    }
    
    async startHelpdesk() {
        let messages;

        try {
            messages = await this.fetchMessages();
        }

        catch(e) {
            //TODO Error message
            return;
        }
        
        this.messagesHash = messages.hash;
        this.buildMessages(messages.messages);
        setInterval(this.checkInterval.bind(this), 10000);
    }

    setFormEvents(formElement) {
        const submitButton = formElement.querySelector("button[type=\"submit\"]");

        formElement.addEventListener("submit", async (e) => {
            e.preventDefault();
            e.stopPropagation();
            submitButton.disabled = true;

            const FD = new FormData(formElement);

            const submitFetch = await fetch(window.location.pathname, {
                method: "POST",
                body: FD
            });

            if(submitFetch.ok) {
                const responseText = await submitFetch.text();
                let responseJson;
                try {
                    responseJson = JSON.parse(responseText);
                }

                catch(e) {
                    console.log(responseText);
                    //TODO response is not in JSON format
                    console.error("Response is not in JSON format");
                }

                if(typeof responseJson["message-data"] != "undefined") {
                    this.buildMessage(responseJson["message-data"]);
                    this.scrollDown();

                    const inputTextarea = formElement.querySelector("[name=\"message-text\"]");
                    inputTextarea.value = "";
                    inputTextarea.style.height = "";
                    submitButton.disabled = false;
                    console.log("New hash: " + responseJson["new-hash"]);
                    this.messagesHash = responseJson["new-hash"];
                }

                else {
                    //TODO No message data response
                    console.error("No message data response");
                }
            }

            else {
                //TODO Unable to send message
                console.error("Unable to send a message");
            }
        });

        const inputTextarea = formElement.querySelector("[name=\"message-text\"]");
        inputTextarea.addEventListener("input", () => {
            inputTextarea.style.height = "";
            inputTextarea.style.height = inputTextarea.scrollHeight + "px";
        });

        inputTextarea.addEventListener("keydown", (e) => {
            if(e.key == "Enter") {
                if(e.shiftKey) {
                    return;
                }
                
                e.preventDefault();
                //formElement.dispatchEvent(new Event("submit"));
                submitButton.click();

            }
        });
    }

    async checkInterval() {
        let messages;
        try {
            messages = await this.fetchMessages();
        }

        catch(e) {
            return;
        }

        if(messages.hash == this.messagesHash) {
            console.log("Messages doesnt change");
        }

        else {
            this.messagesHash = messages.hash;
            console.warn("Need to rebuild messages");
            console.log("New hash: " + messages.hash)
            this.buildMessages(messages.messages);
        }
    }

    buildMessages(messagesData) {
        this.messagesElement.textContent = "";
        let oldMessageDate = null;
        
        for(const message of messagesData) {
            const splitedDate = message["message-date"].split(/[- :]/);
            const messageDate = new Date(Date.UTC(splitedDate[0], splitedDate[1]-1, splitedDate[2], splitedDate[3], splitedDate[4], splitedDate[5]));
            messageDate.toLocaleDateString("en-US", {timeZone: "Europe/Prague"});

            if(oldMessageDate == null) {
                const separator = document.createElement("div");
                separator.classList.add("date-separator");
                separator.textContent = `${messageDate.toLocaleString([], { month: 'short' })} ${messageDate.getDate()}`;
                this.messagesElement.appendChild(separator);
            }

            else {
                if(
                    messageDate.getFullYear() != oldMessageDate.getFullYear() ||
                    messageDate.getMonth() != oldMessageDate.getMonth() ||
                    messageDate.getDate() != oldMessageDate.getDate()
                ) {
                    const separator = document.createElement("div");
                    separator.classList.add("date-separator");
                    separator.textContent = `${messageDate.toLocaleString([], { month: 'short' })} ${messageDate.getDate()}`;
                    this.messagesElement.appendChild(separator);
                }
            }

            this.buildMessage(message);

            oldMessageDate = messageDate;
        }

        this.scrollDown();
    }

    buildMessage(messageData) {
        const messageBox = document.createElement("div");
        messageBox.classList.add("message-box");
        messageBox.classList.add(messageData["message-author"] + "-message");

        const avatarBox = document.createElement("div");
        avatarBox.classList.add("avatar-box")
        messageBox.appendChild(avatarBox);
        
        const avatarImage = document.createElement("img");
        avatarImage.title = messageData["author-name"];
        avatarImage.src = messageData["author-avatar"];
        avatarBox.appendChild(avatarImage);
        
        const messageElement = document.createElement("div");
        messageElement.classList.add("message");
        messageBox.appendChild(messageElement);

        const messageTextElement = document.createElement("p");
        messageTextElement.classList.add("message-text");
        messageTextElement.innerHTML = messageData["message-text"].trim().replaceAll("\n", "<br />");
        messageElement.appendChild(messageTextElement);

        const messageDateElement = document.createElement("p");
        messageDateElement.classList.add("message-date");
        const date = new Date(messageData["message-date"] + "Z");

        let hours = new Intl.DateTimeFormat('en-US', {
            timeZone: 'Europe/Prague',
            hour12: false,
            hour: 'numeric',
            minute: 'numeric'
        }).format(date);

        let fullDate = new Intl.DateTimeFormat('en-US', {
            timeZone: 'Europe/Prague',
            hour12: false,
            year: 'numeric',
            month: 'numeric',
            day: 'numeric',
            hour: 'numeric',
            minute: 'numeric',
            second: 'numeric'
        }).format(date);

        messageDateElement.textContent = hours;
        messageDateElement.title = fullDate;
        messageElement.appendChild(messageDateElement);

        this.messagesElement.appendChild(messageBox);
    }

    async fetchMessages() {
        const fetchMessages = await fetch(window.location.href + "&get-messages&action-page=helpdesk");

        if(fetchMessages.ok) {
            const responseText = await fetchMessages.text();
            let responseJson;

            try {
                responseJson = JSON.parse(responseText);
            }

            catch(e) {
                console.error(responseText);
                throw new Error("Server returned unknown format");
            }

            return responseJson;
        }

        else {

            //TODO Unable to send message
            //console.error("Unable to get a messages");
            throw new Error("Unable to get messages");
        }
    }

    scrollDown() {
        this.messagesElement.parentElement.scrollTo(0, this.messagesElement.parentElement.scrollHeight);
    }

    setCloseTicket(formElement) {
        if(formElement == null) {
            return;
        }

        const closeButton = formElement.querySelector("button[data-role=\"close-ticket\"]");
        closeButton?.addEventListener("click", async () => {
            const confirmStatus = confirm("Opravdu chcete uzavřít ticket? Po zavření již nebude možné ho znovu otevřít!");
            if(confirmStatus) {
                const url = new URL(window.location.href).searchParams;
                const id = url.get("id");
                const FD = new FormData();
                FD.append("action-page", "helpdesk");
                FD.append("close", id);

                const closeFetch = await fetch(window.location.pathname, {
                    method: "POST",
                    body: FD
                });

                const responseText = await closeFetch.text();
                let responseJson;
                try {
                    responseJson = JSON.parse(responseText);
                }

                catch(e) {
                    console.error("Invalid response from server");
                    console.log(responseText);
                    return;
                }

                if(closeFetch?.ok) {
                    window.location.reload();
                }

                else {
                    console.log(responseJson);
                }
            }
        });
    }
}