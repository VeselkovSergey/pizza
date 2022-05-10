const SvgCloseButton = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16"> <path d="M1.293 1.293a1 1 0 0 1 1.414 0L8 6.586l5.293-5.293a1 1 0 1 1 1.414 1.414L9.414 8l5.293 5.293a1 1 0 0 1-1.414 1.414L8 9.414l-5.293 5.293a1 1 0 0 1-1.414-1.414L6.586 8 1.293 2.707a1 1 0 0 1 0-1.414z"/> </svg>';
const SvgPlusButton = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16"> <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/> </svg>';
const SvgMinusButton = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16"> <path d="M4 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 4 8z"/> </svg>';
const SvgTrashButton = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16"> <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/> <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/> </svg>';
const SvgCheckedButton = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2" viewBox="0 0 16 16"> <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/> </svg>';

function LoaderShow() {
    let loader = document.createElement("div");
    loader.className = 'loader';
    loader.innerHTML = '<div class="lds-ring"><div></div><div></div><div></div><div></div></div>';
    document.body.prepend(loader);
}

function LoaderHide() {
    const loader = document.body.querySelector('.loader');
    if (loader) {
        loader.remove();
    }
}

function Ajax(url, method, formDataRAW, loader = false) {
    if (loader) {
        LoaderShow();
    }
    return new Promise(function (resolve, reject) {
        let formData = new FormData();
        if (typeof (method) === "undefined" || method === null) {
            method = 'get';
        }

        if (typeof (formDataRAW) === "undefined" || formDataRAW === null) {
            formDataRAW = {};
        } else {
            Object.keys(formDataRAW).forEach((key) => {

                if (Array.isArray(formDataRAW[key])) {

                    formDataRAW[key].forEach((value) => {
                        formData.append(key, value);
                    });

                } else {
                    formData.append(key, formDataRAW[key]);
                }
            });
        }

        let xhr = new XMLHttpRequest();
        xhr.open(method, url, true);

        let csrf_token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);

        xhr.onload = function () {
            if (loader) {
                LoaderHide();
            }
            if (this.status === 200) {
                try {
                    resolve(JSON.parse(this.response));
                } catch {
                    resolve(this.response);
                }
            } else {
                var error = new Error(this.statusText);
                error.code = this.status;
                reject(error);
            }
        };

        xhr.onerror = function () {
            if (loader) {
                LoaderHide();
            }
            reject(new Error("Network Error"));
        };

        xhr.send(formData);
    });
}

function GetDataFormContainer(container, startElement = document.body) {
    let data = [];
    startElement.querySelectorAll('.' + container + ' input, .' + container + ' select, .' + container + ' textarea').forEach((el) => {
        if (el.type === 'file') {
            for (let i = 0; i < el.files.length; i++) {
                data[el.id + '-' + i] = el.files[i];
            }
        } else {
            if (el.name === '') {
                data[el.id] = el.value;
            } else {
                if (data[el.name] === undefined) {
                    data[el.name] = [];
                }
                let value = el.value;
                if (el.type === 'checkbox' || el.type === 'radio') {
                    value = el.checked
                }
                data[el.name].push(value);
            }
        }
    });
    return data;
}

function CheckingFieldForEmptiness(container, ShowFlashMessage = false) {
    let check = true;
    document.body.querySelectorAll('.' + container + ' .need-validate').forEach((element) => {
        let strValue = element.value;
        if (strValue === '' || strValue === null || strValue === 'null' || strValue === undefined) {
            check = false;
            element.classList.add('invalid-value');
            element.addEventListener('input', () => {
                let strValue = element.value;
                if (strValue !== '' && strValue !== null && strValue !== 'null' && strValue !== undefined) {
                    element.classList.remove('invalid-value');
                    element.removeEventListener('input', null);
                }
            }, {once: true});
        }
    });
    if (!check && ShowFlashMessage) {
        FlashMessage('Заполните все поля!');
    }
    return check;
}

function ModalWindow(content, closingCallback, flash, noCloseByScroll = true) {
    let documentBody = document.body;
    !flash ? documentBody.classList.add('scroll-off') : '';
    let closeButtonSVG = '<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"> <path fill-rule="evenodd" clip-rule="evenodd" d="M12.6365 13.3996L13.4001 12.636L7.76373 6.99961L13.4001 1.36325L12.6365 0.599609L7.0001 6.23597L1.36373 0.599609L0.600098 1.36325L6.23646 6.99961L0.600098 12.636L1.36373 13.3996L7.0001 7.76325L12.6365 13.3996Z" fill="#000000"></path> </svg>';

    let modalWindowComponentContainer = CreateElement('div', {
        attr: {
            class: 'modal-window-component-container',
        }
    });

    let modalWindowComponent = CreateElement('div', {attr: {class: 'modal-window-component'}}, modalWindowComponentContainer);

    CreateElement('div', {
        attr: {class: 'modal-window-shadow'}, events: {
            click: () => {
                closingCallback ? closingCallback() : '';
                modalWindowComponentContainer.slowRemove();
                ScrollOff(flash);
            }
        }
    }, modalWindowComponent);

    let modalWindowContainer = CreateElement('div', {
        attr: {
            class: 'modal-window-content-container',
        }
    }, modalWindowComponent);

    CreateElement('div', {
        attr: {
            class: 'modal-window-close-button',
        },
        content: closeButtonSVG,
        events: {
            click: () => {
                closingCallback ? closingCallback() : '';
                modalWindowComponentContainer.slowRemove();
                ScrollOff(flash);
            }
        }
    }, modalWindowContainer);

    let modalWindowContent = CreateElement('div', {
        attr: {
            class: 'modal-window-content',
        }
    }, modalWindowContainer);

    if (typeof content === 'string') {
        content = CreateElement('div', {
            content: content
        });
    }

    modalWindowContent.append(content)

    document.body.append(modalWindowComponentContainer);

    if (noCloseByScroll) {
        CloseByScroll(modalWindowComponentContainer, modalWindowContainer, modalWindowContent, () => {
            closingCallback ? closingCallback() : '';
            modalWindowComponentContainer.slowRemove();
            ScrollOff(flash);
        });
    }

    return modalWindowComponentContainer;

    function ScrollOff(flash) {
        if (document.querySelectorAll('.modal-window-component-container').length === 1) {
            setTimeout(() => {
                !flash ? documentBody.classList.remove('scroll-off') : '';
            }, 200);
        }
    }
}

function CloseModal(modal) {
    modal.slowRemove();
    setTimeout(() => {
        if (document.querySelectorAll('.modal-window-component-container').length === 0) {
            document.body.classList.remove('scroll-off');
        }
    }, 450);
}

function CloseByScroll(modalWindowComponentContainer, container, content, closingCallback) {
    let widthClientScreen = document.documentElement.clientWidth;
    if (widthClientScreen < 768) {

        let containerModalWindow = container;

        let startTouchY = 0;
        // let startTouchX = 0;
        let correctionCoefficientY = 0;
        // let correctionCoefficientX = 0;
        containerModalWindow.addEventListener('touchstart', (event) => {
            containerModalWindow.style.transition = 'transform 0ms ease-out';
            if (content.getBoundingClientRect().top >= 0) {
                startTouchY = event.changedTouches[0].clientY;
                correctionCoefficientY = content.getBoundingClientRect().top;
            }
            // if (content.getBoundingClientRect().right >= 0) {
            //     startTouchX = event.changedTouches[0].clientX;
            //     correctionCoefficientX = content.getBoundingClientRect().right;
            // }
        })

        let lengthSwipeY = 0;
        // let lengthSwipeX = 0;
        containerModalWindow.addEventListener('touchmove', (event) => {
            if (content.getBoundingClientRect().top === content.firstChild.getBoundingClientRect().top && content.getBoundingClientRect().top >= (-1 + correctionCoefficientY)) {
                lengthSwipeY = event.changedTouches[0].clientY - startTouchY;
                if (lengthSwipeY > 0) {
                    containerModalWindow.style.transform = 'translateY(' + lengthSwipeY + 'px)';
                }
            } else {
                startTouchY = event.changedTouches[0].clientY;
            }

            // if (content.getBoundingClientRect().right === content.firstChild.getBoundingClientRect().right && content.getBoundingClientRect().right >= (-1 + correctionCoefficientX)) {
            //     lengthSwipeX = event.changedTouches[0].clientX - startTouchX;
            //     if (lengthSwipeX < 0) {
            //         containerModalWindow.style.transform = 'translateX(' + lengthSwipeX + 'px)';
            //     }
            // } else {
            //     startTouchX = event.changedTouches[0].clientX;
            // }
        });

        let heightClientScreen = document.documentElement.clientHeight;
        // let widthClientScreen = document.documentElement.clientWidth;

        containerModalWindow.addEventListener('touchend', () => {
            containerModalWindow.style.transition = '';
            if (lengthSwipeY < (heightClientScreen / 3)) {
                containerModalWindow.style.transform = 'translateY(0px)';
            } else {
                containerModalWindow.style.transform = 'translateY(' + heightClientScreen + 'px)';
                closingCallback ? closingCallback() : '';
            }

            // if ((lengthSwipeX * -1) > (widthClientScreen / 2)) {
            //     containerModalWindow.style.transform = 'translateX(-' + widthClientScreen + 'px)';
            //     closingCallback ? closingCallback() : '';
            // } else {
            //     containerModalWindow.style.transform = 'translateX(0px)';
            // }
        });
    }
}

function ModalWindowFlash(content, timer = 2000) {
    let modalWindow = ModalWindow(content, undefined, true)
    setTimeout(() => {
        modalWindow.remove();
    }, timer);
}

function FlashMessage(message, autoClose = true, timeout = 3000) {
    let flashMessageContainer = document.body.querySelector('.flash-message-container');
    if (flashMessageContainer === null) {
        flashMessageContainer = document.createElement('div');
        flashMessageContainer.className = 'flash-message-container pos-fix z-5';
        document.body.prepend(flashMessageContainer);
    }

    let newMessage = document.createElement('div');
    newMessage.classList.add('flash-message-text');
    newMessage.innerHTML = message;
    flashMessageContainer.append(newMessage);
    if (autoClose) {
        setTimeout(() => {
            newMessage.remove();
            if (flashMessageContainer.childNodes.length === 0) {
                flashMessageContainer.remove();
            }
        }, timeout);
    }

    newMessage.addEventListener('click', () => {
        newMessage.remove();
    });
}

function startTrackingNumberInput() {
    document.body.querySelectorAll('.phone-mask').forEach((element) => {

        let phoneInput = element;

        if (phoneInput !== null) {

            let number = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'ArrowTop', 'ArrowDown'];

            phoneInput.addEventListener('keydown', (event) => {
                if (number.indexOf(event.key) === -1) {
                    event.preventDefault();
                }
            });

            let timer;
            phoneInput.addEventListener('keyup', (event) => {
                if ((event.key !== 'Backspace' && event.key !== 'Delete')) {
                    clearTimeout(timer);
                    timer = setTimeout(() => {
                        let rawPhone = phoneInput.value;
                        let onlyNumber = rawPhone.replace(/[^0-9]/g, '');
                        let formatPhone = '';
                        for (let i = 0; i < onlyNumber.length; i++) {

                            let char = onlyNumber.charAt(i);

                            if (i === 0) {
                                formatPhone += '+';
                                if (char !== '7') {
                                    formatPhone += '7';
                                }
                                if (char === '8') {
                                    char = '';
                                }
                            } else if (i === 1) {
                                formatPhone += '(';
                            } else if (i === 4) {
                                formatPhone += ')';
                            } else if (i === 7 || i === 9) {
                                formatPhone += '-';
                            }

                            if (i <= 10) {
                                formatPhone += char;
                            }

                        }
                        phoneInput.value = formatPhone;

                    }, 50);
                }
            });
        }
    });
}

let timerSuggestionsAddress = null;

function SuggestionsAddress(query, inputSuggestions) {

    if (query.length < 4) {
        return;
    }

    clearTimeout(timerSuggestionsAddress)

    timerSuggestionsAddress = setTimeout(() => {

        const url = "https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address";
        const token = "980b289f33c7bafda2d4007c51a2d45d6c980425";

        let data = {
            query: query,
            locations: [{
                "region": "Московская",
                "city": "Дубна"
            }],
            restrict_value: true,
            count: 3,
        }

        let options = {
            method: "POST",
            mode: "cors",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "Authorization": "Token " + token
            },
            body: JSON.stringify(data)
        }

        fetch(url, options)
            .then(response => response.text())
            .then(result => ContainerSuggestionsGeneration(result, inputSuggestions))
            .catch(error => console.log("error", error));
    }, 500)
}

function ContainerSuggestionsGeneration(result, inputSuggestions) {
    result = JSON.parse(result).suggestions;

    let parentInputSuggestions = inputSuggestions.parentNode;
    let oldSuggestionsElement = parentInputSuggestions.querySelector('.container-suggestions');
    if (oldSuggestionsElement !== null) {
        oldSuggestionsElement.remove();
    }

    let containerSuggestions = document.createElement('div');
    containerSuggestions.className = 'container-suggestions w-100 pos-rel';

    let containerSuggestionsAbsolutePosition = document.createElement('div');
    containerSuggestionsAbsolutePosition.className = 'container-suggestions-pos-abs pos-abs left-0 border-radius-5';
    if (result.length === 0) {
        let itemSuggestion = document.createElement('div');
        itemSuggestion.className = 'p-5';
        itemSuggestion.innerHTML = 'Нет результатов удовлетворяющих запросу';
        containerSuggestionsAbsolutePosition.append(itemSuggestion);
    } else {
        containerSuggestionsAbsolutePosition.innerHTML = '<div class="p-5 color-grey">Выберите подсказку:</div>';
        result.forEach((item) => {
            let itemSuggestion = document.createElement('div');
            itemSuggestion.className = 'p-5 suggestion-item';
            itemSuggestion.innerHTML = item.value;
            containerSuggestionsAbsolutePosition.append(itemSuggestion);
            itemSuggestion.addEventListener('mousedown', () => {
                inputSuggestions.value = itemSuggestion.innerHTML;
                containerSuggestions.remove();
                setTimeout(() => {
                    inputSuggestions.focus();
                }, 100)

                /* #todo remake */
                let inputName = inputSuggestions.name;
                let inputValue = inputSuggestions.value;
                inputName = inputName[0].toUpperCase() + inputName.slice(1);
                localStorage.setItem('last' + inputName, inputValue);
            });
        });
    }

    inputSuggestions.addEventListener('blur', () => {
        containerSuggestions.remove();
    });

    containerSuggestions.append(containerSuggestionsAbsolutePosition);

    inputSuggestions.insertAdjacentElement('afterend', containerSuggestions);

    let heightClientScreen = document.documentElement.clientHeight;
    let h = containerSuggestionsAbsolutePosition.getBoundingClientRect().height;
    let b = containerSuggestionsAbsolutePosition.getBoundingClientRect().bottom;

    if (h + b > heightClientScreen) {
        containerSuggestionsAbsolutePosition.style.bottom = inputSuggestions.getBoundingClientRect().height + 'px';
    } else {
        containerSuggestionsAbsolutePosition.classList.add('top-0');
    }
}

function LoginWindow(callback) {
    let phoneContainer;
    let phoneField;
    let authButton;
    let approveButton;
    let confirmationContainer;
    let confirmationCodeInput;
    let callRepeatButton;
    let countRepeat = 0;
    let loginWindowContent = CreateElement('div', {
        childs: [
            CreateElement('div', {
                content: 'Авторизация',
                class: 'mb-10 text-center'
            }),
            phoneContainer = CreateElement('div', {
                childs: [
                    CreateElement('label', {
                        content: 'Номер телефона',
                        class: 'mb-5 text-center',
                    }),
                    phoneField = CreateElement('input', {
                        attr: {
                            placeholder: '+7(999)000-11-22',
                            class: 'clear-input p-5 border-radius-5 border w-a text-center phone-mask',
                            maxlength: '16',
                            type: 'tel',
                        },
                        events: {
                            keyup: (event) => {
                                if (event.key === 'Enter') {
                                    triggerEvent( authButton, 'click' );
                                }
                            }
                        }
                    }),
                    CreateElement('div', {
                        childs: [
                            authButton = CreateElement('button', {
                                content: 'Авторизоваться',
                                class: 'orange-button',
                                events: {
                                    click: () => {
                                        if (PhoneValidation(phoneField.value) !== false) {
                                            phoneContainer.hide();
                                            confirmationContainer.show();
                                            confirmationCodeInput.focus();
                                            setInterval(() => {
                                                const num = callRepeatButton.querySelector('span').innerHTML;
                                                if (num - 1 >= 0) {
                                                    callRepeatButton.querySelector('span').innerHTML = num - 1;
                                                }
                                            }, 1000);
                                        }
                                    }
                                }
                            }),
                        ],
                        class: 'flex-center mt-10',
                    }),
                ],
                class: 'mb-10 flex-column',
            }),
            confirmationContainer = CreateElement('div', {
                childs: [
                    CreateElement('label', {
                        content: 'Для подтверждения номера Вам поступит звонок, введите последние 4 цифры входящего номера (если звонок не поступил, возможно вы запретили неизвестные входящие номера)',
                        class: 'mb-5 text-center confirmation-code-title',
                    }),
                    confirmationCodeInput = CreateElement('input', {
                        attr: {
                            placeholder: '1234',
                            class: 'clear-input p-5 border-radius-5 border w-a text-center',
                            maxlength: 4,
                            type: 'tel',
                        },
                        events: {
                            keyup: (event) => {
                                if (event.key === 'Enter') {
                                    triggerEvent( approveButton, 'click' );
                                }
                            },
                            input: (event) => {
                                if (event.target.value.length === 4) {
                                    triggerEvent( approveButton, 'click' );
                                }
                            }
                        }
                    }),
                    CreateElement('div', {
                        childs: [
                            approveButton = CreateElement('button', {
                                content: 'Подтвердить',
                                class: 'orange-button',
                                events: {
                                    click: () => {
                                        let confirmationCodeInputValue = confirmationCodeInput.value.replace(/[^\d;]/g, '');
                                        if (confirmationCodeInputValue.length !== 4) {
                                            ModalWindow('Нужно 4 цифры');
                                        } else {
                                            let execFunction = '';
                                            if (callback) {
                                                execFunction = callback;
                                            }
                                            Ajax(routeCheckConfirmationCode, 'post', {
                                                confirmationCode: confirmationCodeInputValue,
                                                execFunction: execFunction
                                            }).then((response) => {
                                                if (response.status) {
                                                    loginWindow.slowRemove();
                                                    FlashMessage(response.message);
                                                    setTimeout(() => {
                                                        location.reload();
                                                    }, 1500);
                                                } else {
                                                    ModalWindow(response.message);
                                                }
                                            });
                                        }
                                    }
                                }
                            }),
                            callRepeatButton = CreateElement('button', {
                                content: 'Повторить (<span>30</span>)',
                                class: 'orange-button',
                                events: {
                                    click: () => {
                                        const num = callRepeatButton.querySelector('span').innerHTML;
                                        if (parseInt(num) === 0 && countRepeat < 2) {
                                            countRepeat++;
                                            callRepeatButton.querySelector('span').innerHTML = '30';
                                            PhoneValidation(phoneField.value);
                                        }

                                        if (countRepeat === 2) {
                                            callRepeatButton.hide();
                                        }
                                    }
                                }
                            }),
                        ],
                        class: 'mt-10 flex-column-center-adaptive',
                    }),
                ],
                class: 'mb-10 flex-column hide',
            }),
        ],
    });

    let loginWindow = ModalWindow(loginWindowContent);
    phoneField.focus();

    phoneField.selectionStart = phoneField.value.length

    startTrackingNumberInput();

    function PhoneValidation(phoneValue) {
        phoneValue = phoneValue.replace(/[^\d;]/g, '');
        if (phoneValue === '') {
            ModalWindowFlash('Укажите номер телефона');
            return false;
        }

        if (phoneValue.length !== 11) {
            ModalWindowFlash('Не верный формат номера');
            return false;
        }

        Ajax(routePhoneValidation, 'post', {phone: phoneValue}).then((response) => {
            if (response.status) {
                return true;
            } else {
                ModalWindow('Непредвиденная ошибка. Мы уже работаем над этим. Попробуйте позже.');
                return false;
            }
        });
    }
}

function Logout() {
    Ajax(routeLogout).then(() => {
        location.reload();
    });
}

function Profile() {
    return location.href = routeProfile;
}

let leftMenuButtons = document.body.querySelectorAll('.button-menu, .shadow-menu, .close-menu-button');
leftMenuButtons.forEach((button) => {
    button.addEventListener('click', () => {
        let leftMenu = document.body.querySelector('.left-menu');
        if (leftMenu.classList.contains('hide')) {
            leftMenu.classList.remove('hide');
            setTimeout(() => {
                leftMenu.querySelector('.left-menu-content-container').style.transform = "translateX(0%)"
            }, 50)
        } else {
            leftMenu.querySelector('.left-menu-content-container').style.transform = "translateX(-120%)"
            setTimeout(() => {
                leftMenu.classList.add('hide');
            }, 300)
        }
    });
});

function OpeningHours(startHour, startMints, endHour, endMints) {
    const openingHours = document.body.querySelector('.opening-hours');
    if (openingHours) {
        openingHours.innerHTML = '<div>'+startHour+':'+((startMints < 10 ? '0' : '') + startMints)+'</div><div>'+endHour+':'+((endMints < 10 ? '0' : '') + endMints)+'</div>';
    }
    let moskowUtc = 3;
    let time = new Date();
    let hour = time.getUTCHours() + moskowUtc;
    let mints = time.getMinutes() + moskowUtc;

    if ((hour === startHour && mints >= startMints) || (startHour < hour && hour < endHour) || (hour === endHour && mints <= endMints)) {
        //
    } else {
        if (!admin) {
            ModalWindow('<div class="text-center">Часы работы с ' + startHour + ':'+ ((startMints < 10 ? '0' : '') + startMints) +' до ' + endHour + ':'+ ((endMints < 10 ? '0' : '') + endMints) +'</div></div>');
        }
    }
}

/* сообщение о куках */
if (localStorage.getItem('cookiesAccepted') === null) {
    const cookiesInfo = '<div class="pos-fix bottom-0 bg-black w-100 shadow-white"><div class="flex-space-between flex-wrap p-25"><div class="text-center py-10"> Мы тоже используем куки, потому что без них вообще ничего не работает</div><button class="cookies-accept-button orange-button">Ничего, я привык</button></div></div>';
    let cookiesInfoElement = CreateElement('div', {content: cookiesInfo}, document.body);
    let cookiesAcceptButton = document.body.querySelector('.cookies-accept-button');
    if (cookiesAcceptButton) {
        cookiesAcceptButton.addEventListener('click', () => {
            localStorage.setItem('cookiesAccepted', Date.now().toString());
            cookiesInfoElement.remove();
        });
    }
}

function SelectWithSearch(selector) {

    selector.hide();
    let oldValue = null;

    const defaultOption = selector.querySelector('option[selected]');
    let defaultOptionText = ''
    if (defaultOption) {
        defaultOptionText = defaultOption.innerHTML;
        oldValue = defaultOption.value;
    }

    const options = selector.querySelectorAll('option');

    let container = selector.parentNode;

    let searchFieldContainer = container.querySelector('.search-field-container');
    if (!searchFieldContainer) {
        searchFieldContainer = CreateElement('div', {
            attr: {type: 'text'},
            class: 'search-field-container'
        }, container);
    }

    let searchField = searchFieldContainer.querySelector('.search-field');
    if (!searchField) {
        searchField = CreateElement('input', {
            attr: {type: 'text'},
            class: 'search-field'
        }, searchFieldContainer);
    }


    if (defaultOption) {
        if (defaultOption.getAttribute('disabled') !== null) {
            searchField.setAttribute('placeholder', defaultOptionText)
        } else {
            searchField.value = defaultOptionText;
        }
    }

    let customOptionsContainer = searchFieldContainer.querySelector('.custom-options-container');
    if (!customOptionsContainer) {
        customOptionsContainer = CreateElement('div', {class: 'custom-options-container hide'}, searchFieldContainer);
    }
    customOptionsContainer.innerHTML = '';

    let optionsCustom = [];
    options.forEach((option) => {
        const text = option.innerHTML;
        const value = option.value;
        if (option.getAttribute('disabled') === null) {
            const customOption = CreateElement('div', {
                attr: {'data-value': value},
                class: 'custom-option',
                content: text
            }, customOptionsContainer);
            optionsCustom.push(customOption);
            customOption.addEventListener('mousedown', (event) => {
                searchField.value = event.target.innerHTML;
                const value = event.target.dataset.value;
                selector.value = value;
                oldValue = value;
                triggerEvent(selector, 'change');
            });
        }
    });

    searchField.addEventListener('focus', () => {
        customOptionsContainer.show();
    });

    searchField.addEventListener('blur', () => {
        customOptionsContainer.hide();
        if (!oldValue) {
            searchField.value = '';
            for (let i = 0; i < optionsCustom.length; i++) {
                optionsCustom[i].show();
            }
        }
        selector.value = oldValue;
    });

    searchField.addEventListener('keyup', (event) => {
        oldValue = null;
        let target = event.target;

        let regExp = new RegExp(target.value, 'ig');
        for (let i = 0; i < optionsCustom.length; i++) {
            let option = optionsCustom[i];

            if (option.innerHTML.match(regExp)) {
                option.show();
            } else {
                option.hide();
            }
        }
    });
}

function fnExcelReport(tableId) {
    var tab_text = "<table border='2px'><tr bgcolor='#FFFFFF'>";
    var j = 0;
    tab = document.getElementById(tableId);

    for (j = 1; j < tab.rows.length; j++) {
        tab_text = tab_text + tab.rows[j].innerHTML + "</tr>";
    }

    tab_text = tab_text + "</table>";
    tab_text = tab_text.replace(/<A[^>]*>|<\/A>/g, "");
    tab_text = tab_text.replace(/<img[^>]*>/gi, "");
    tab_text = tab_text.replace(/<input[^>]*>|<\/input>/gi, "");

    var uri = 'data:application/vnd.ms-excel,';
    var a = document.createElement('a');
    a.setAttribute("href", uri + encodeURIComponent(tab_text))
    a.setAttribute('download', new Date() + '.xls');
    document.body.appendChild(a);
    a.click()
}