function Ajax(url, method, formDataRAW) {
    return new Promise(function (resolve, reject) {
        let formData = new FormData();
        if (typeof (method) === "undefined" || method === null) {
            method = 'get';
        }

        if (typeof (formDataRAW) === "undefined" || formDataRAW === null) {
            formDataRAW = {};
        } else {

        }

        Object.keys(formDataRAW).forEach((key) => {
            formData.append(key, formDataRAW[key]);
        });

        console.log(formData)

        let xhr = new XMLHttpRequest();
        xhr.open(method, url, true);

        let csrf_token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);

        xhr.onload = function () {
            if (this.status === 200) {
                try {
                    resolve(JSON.parse(this.response));
                } catch (e) {
                    resolve(this.response);
                }

            } else {
                let error = new Error(this.statusText);
                error.code = this.status;
                reject(error);
            }
        };

        xhr.onerror = function () {
            reject(new Error("Network Error"));
        };

        xhr.send(formData);
    });
}

function CheckingFieldForEmptiness(container, ShowFlashMessage = false) {
    let check = true;
    document.body.querySelectorAll('.' + container + ' .need-validate').forEach((element) => {
        let strValue = element.value;
        if (strValue === '' || strValue === null || strValue === undefined) {
            check = false;
            element.classList.add('invalid-value');
            element.addEventListener('input', () => {
                FieldCorrection(element);
            }, {once: true});
        }
    });
    if (!check) {
        FlashMessage('Заполните все поля!');
    }
    return check;
}

function FieldCorrection(element) {
    let strValue = element.value;
    if (strValue !== '' && strValue !== null && strValue !== undefined) {
        element.classList.remove('invalid-value');
        element.removeEventListener('input', null);
    }
}

function ShowElement(element) {
    element.classList.remove('hide')
}

function HideElement(element) {
    element.classList.add('hide');
}

function FlashMessage(message, autoClose = true, timeout = 3000) {
    let flashMessageContainer = document.body.querySelector('.flash-message-container');
    let newMessage = document.createElement('div');
    newMessage.classList.add('flash-message-text');
    newMessage.innerHTML = message;
    flashMessageContainer.append(newMessage);
    if (autoClose) {
        setTimeout(() => {
            newMessage.remove();
        }, timeout);
    }
}
