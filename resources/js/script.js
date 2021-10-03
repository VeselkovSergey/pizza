const SvgCloseButton = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16"> <path d="M1.293 1.293a1 1 0 0 1 1.414 0L8 6.586l5.293-5.293a1 1 0 1 1 1.414 1.414L9.414 8l5.293 5.293a1 1 0 0 1-1.414 1.414L8 9.414l-5.293 5.293a1 1 0 0 1-1.414-1.414L6.586 8 1.293 2.707a1 1 0 0 1 0-1.414z"/> </svg>';
const SvgPlusButton = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16"> <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/> </svg>';
const SvgMinusButton = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16"> <path d="M4 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 4 8z"/> </svg>';
const SvgTrashButton = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16"> <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/> <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/> </svg>';

function Ajax(url, method, formDataRAW) {
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


        var xhr = new XMLHttpRequest();
        xhr.open(method, url, true);

        let csrf_token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);

        xhr.onload = function () {
            if (this.status == 200) {
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
    if (strValue !== '' && strValue !== null && strValue !== 'null' && strValue !== undefined) {
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
    if (flashMessageContainer === null) {
        flashMessageContainer = document.createElement('div');
        flashMessageContainer.className = 'flash-message-container pos-fix z-5 py-5';
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
}

if (localStorage.getItem('basket') === null) {
    localStorage.setItem('basket', JSON.stringify({}));
}

UpdateBasketCounter(CountProductsInBasket())

function AddProductInBasket(data) {
    let modificationId = data.modification.id
    let basket = JSON.parse(localStorage.getItem('basket'));
    let modificationInBasket = basket['modification-' + modificationId];
    if (modificationInBasket === undefined) {
        basket['modification-' + modificationId] = {
            amount: 1,
            data: data,
        };
    } else {
        basket['modification-' + modificationId].amount = modificationInBasket.amount + 1;
    }
    localStorage.setItem('basket', JSON.stringify(basket));
    UpdateBasketCounter(CountProductsInBasket())
}

function DeleteProductInBasket(data) {
    let modificationId = data.modification.id
    let basket = JSON.parse(localStorage.getItem('basket'));
    let modificationInBasket = basket['modification-' + modificationId];
    if (basket['modification-' + modificationId] !== undefined) { // есть в корзине
        if (basket['modification-' + modificationId].amount > 1) {
            basket['modification-' + modificationId].amount = modificationInBasket.amount - 1;
        } else {
            delete basket['modification-' + modificationId];
        }

        localStorage.setItem('basket', JSON.stringify(basket));
        UpdateBasketCounter(CountProductsInBasket())
    }
}

function ClearProductInBasket(data) {
    let modificationId = data.modification.id
    let basket = JSON.parse(localStorage.getItem('basket'));
    delete basket['modification-' + modificationId];

    localStorage.setItem('basket', JSON.stringify(basket));
    UpdateBasketCounter(CountProductsInBasket())
}

function CountProductsInBasket() {
    let count = 0;
    let basket = JSON.parse(localStorage.getItem('basket'));
    Object.keys(basket).forEach((key) => {
        count += basket[key].amount
    });
    return count;
}

function AmountProductInBasket(modificationId) {
    let basket = JSON.parse(localStorage.getItem('basket'));
    console.log(basket['modification-' + modificationId])
    if (basket['modification-' + modificationId] === undefined) {
        return 0;
    } else {
        return basket['modification-' + modificationId].amount
    }
}

function DeleteAllProductsInBasket() {
    localStorage.setItem('basket', JSON.stringify({}));
    UpdateBasketCounter(CountProductsInBasket())
}

function PriceSumProductsInBasket() {
    let priceSum = 0;
    let basket = JSON.parse(localStorage.getItem('basket'));
    Object.keys(basket).forEach((key) => {
        priceSum += (basket[key].data.modification.sellingPrice * basket[key].amount);
    });
    return priceSum;
}

function UpdateBasketCounter(value) {
    let basketCounter = document.body.querySelector('.amount-item-in-basket');
    basketCounter.innerHTML = value;
}

function GetAllProductsInBasket() {
    return JSON.parse(localStorage.getItem('basket'));
}

let basketButton = document.body.querySelector('.button-basket');
basketButton.addEventListener('click', () => {
    BasketWindow();
});

// triggerEvent( basketButton, 'click' );
function triggerEvent(elem, event) {
    elem.dispatchEvent(new Event(event));
}

function BasketWindow() {
    let basketWindow = document.createElement('div');
    basketWindow.className = 'basket-window w-100 h-100 pos-fix z-2';
    basketWindow.innerHTML =
        '<div class="basket-window-shadow w-100 h-100">' +
        '</div>' +
        '<div class="modal-window pos-abs bg-white border-radius-10 scroll-off">' +
            '<div class="button-close-basket-window pos-abs flex cp" style="right: 20px; top: 20px">' + SvgCloseButton + '</div>' +
            '<div class="scroll-auto" style="max-height: calc(100vh - 100px);">' +
                '<div class="p-25" style="height: calc(100% - 50px);">' +
                    '<div class="flex-wrap">' +
                        ProductsInBasketGenerationHTML() +
                    '</div>' +
                    '<div class="flex-wrap">' +
                        OrderInfoGenerationHTML() +
                    '</div>' +
                '</div>'+
            '</div>'+
        '</div>';

    let basketWindowShadow = basketWindow.querySelector('.basket-window-shadow');
    basketWindowShadow.addEventListener('click', () => {
        // basketWindow.remove();
        basketWindow.slowRemove();
    });

    let buttonCloseBasketWindowShadow = basketWindow.querySelector('.button-close-basket-window');
    buttonCloseBasketWindowShadow.addEventListener('click', () => {
        // basketWindow.remove();
        basketWindow.slowRemove();
    });

    document.body.prepend(basketWindow);

    let priceSumProductsInBasket = document.body.querySelector('.price-sum-products-in-basket');

    let deleteProductButtons = document.body.querySelectorAll('.delete-product-button');
    deleteProductButtons.forEach((el) => {
        el.addEventListener('click', () => {
            let modificationId = el.dataset.modificationId;
            let amountProduct = document.body.querySelector('.amount-product[data-modification-id="' + modificationId + '"]');
            let modification = {
                modification: {id: modificationId},
            }
            DeleteProductInBasket(modification);
            let amountProductInBasket = AmountProductInBasket(modificationId);
            amountProduct.innerHTML = amountProductInBasket;
            if (amountProductInBasket === 0) {
                let containerProductInBasket = el.closest('.container-product-in-basket');
                containerProductInBasket.remove();
            }
            let resultPriceSumProductsInBasket = PriceSumProductsInBasket();
            priceSumProductsInBasket.innerHTML = 'Итого: ' + resultPriceSumProductsInBasket + ' ₽';
            if (resultPriceSumProductsInBasket === 0) {
                // basketWindow.remove();
                basketWindow.slowRemove();
            }
        });
    });

    let clearProductButton = document.body.querySelectorAll('.clear-product-button');
    clearProductButton.forEach((el) => {
        el.addEventListener('click', () => {
            let modificationId = el.dataset.modificationId;
            let amountProduct = document.body.querySelector('.amount-product[data-modification-id="' + modificationId + '"]');
            let modification = {
                modification: {id: modificationId},
            }
            ClearProductInBasket(modification);
            let amountProductInBasket = AmountProductInBasket(modificationId);
            amountProduct.innerHTML = amountProductInBasket;
            if (amountProductInBasket === 0) {
                let containerProductInBasket = el.closest('.container-product-in-basket');
                containerProductInBasket.remove();
            }
            let resultPriceSumProductsInBasket = PriceSumProductsInBasket();
            priceSumProductsInBasket.innerHTML = 'Итого: ' + resultPriceSumProductsInBasket + ' ₽';
            if (resultPriceSumProductsInBasket === 0) {
                // basketWindow.remove();
                basketWindow.slowRemove();
            }
        });
    });

    let addProductButtons = document.body.querySelectorAll('.add-product-button');
    addProductButtons.forEach((el) => {
        el.addEventListener('click', () => {
            let modificationId = el.dataset.modificationId;
            let amountProduct = document.body.querySelector('.amount-product[data-modification-id="' + modificationId + '"]');
            let modification = {
                modification: {id: modificationId},
            }
            AddProductInBasket(modification);
            amountProduct.innerHTML = AmountProductInBasket(modificationId);
            priceSumProductsInBasket.innerHTML = 'Итого: ' + PriceSumProductsInBasket() + ' ₽';
        });
    });

    let orderCreateButton = document.body.querySelector('.order-create');
    if (orderCreateButton !== null) {
        orderCreateButton.addEventListener('click', () => {

            if (CheckingFieldForEmptiness('client-information') === false) {
                return;
            }

            let clientInformation = GetDataFormContainer('client-information', );
            let ObjectClientInformation = {};
            for (let key in clientInformation) {
                ObjectClientInformation[key] = clientInformation[key].length === 1 ? clientInformation[key][0] : clientInformation[key];
            }

            let data = {
                basket: JSON.stringify(GetAllProductsInBasket()),
                clientInformation: JSON.stringify(ObjectClientInformation),
            };

            Ajax(routeOrderCreate, 'POST', data).then((response) => {
                FlashMessage(response.message);
                if (response.status === true) {
                    // basketWindow.remove();
                    basketWindow.slowRemove();
                    DeleteAllProductsInBasket();
                }
            })
        });
    }

    let deliveryAddress = document.body.querySelector('.delivery-address');
    if (deliveryAddress !== null) {
        deliveryAddress.addEventListener('input', (event) => {
            let searchAddress = event.target.value;
            SuggestionsAddress(searchAddress, deliveryAddress);
        });
    }

    startTrackingNumberInput();

}

function ProductsInBasketGenerationHTML() {
    let productsInBasketGenerationHTML = '<div class="w-100">Корзина</div>';
    let countProductsInBasket = CountProductsInBasket();
    if (countProductsInBasket === 0) {
        productsInBasketGenerationHTML += '<div class="w-100">В корзине пусто</div>'
    } else {
        let basket = GetAllProductsInBasket();

        Object.keys(basket).forEach((key) => {
            let product = basket[key].data.product;
            let modification = basket[key].data.modification;
            let modificationId = modification.id
            let amount = basket[key].amount

            let modificationHTML =
                '<div class="container-product-in-basket w-100 flex-center-vertical">' +
                    '<div class="p-10 mr-a">' +
                        '<div>' + product.title + '</div>' +
                        '<div>' + modification.title + '</div>' +
                        '<div>' + modification.value + '</div>' +
                    '</div>' +
                    '<div class="border-radius-25 flex-center" style="background-color: rgb(243, 243, 247);">' +
                        '<button class="delete-product-button flex-center clear-button cp" data-modification-id="' + modificationId + '">' + SvgMinusButton + '</button>' +
                        '<div class="m-5 amount-product flex-center" style="min-width: 20px;" data-modification-id="' + modificationId + '">' + amount + '</div>' +
                        '<button class="add-product-button flex-center clear-button cp" data-modification-id="' + modificationId + '">' + SvgPlusButton + '</button>' +
                    '</div>' +
                    '<div class="p-10">' + modification.sellingPrice + ' ₽</div>' +
                    '<button class="clear-product-button flex-center clear-button cp" data-modification-id="' + modificationId + '">' + SvgTrashButton + '</button>' +
                '</div>';
            productsInBasketGenerationHTML += modificationHTML
        });
        productsInBasketGenerationHTML += '<div class="price-sum-products-in-basket w-100 text-right">Итого: ' + PriceSumProductsInBasket() + ' ₽</div>';
    }

    return productsInBasketGenerationHTML;
}

function OrderInfoGenerationHTML() {
    let countProductsInBasket = CountProductsInBasket();
    if (countProductsInBasket !== 0) {
        return  '<div class="client-information w-100">' +
                    '<div>Оформление заказа</div>' +
                    '<div class="w-100 flex-wrap mt-10">' +
                        '<label for="">Имя</label>' +
                        '<input name="clientName" class="need-validate w-100" type="text">' +
                    '</div>' +
                    '<div class="w-100 flex-wrap mt-10">' +
                        '<label for="">Номер телефона</label>' +
                        '<input name="clientPhone" class="need-validate phone-mask w-100" maxlength="17" type="text">' +
                    '</div>' +
                    '<div class="w-100 flex-wrap mt-10">' +
                        '<label for="">Адрес для доставки</label>' +
                        '<input name="clientAddressDelivery" autocomplete="new-password" autocorrect="off" autocapitalize="off" spellcheck="false"  class="need-validate delivery-address w-100" type="text">' +
                    '</div>' +
                    '<div class="w-100 flex-wrap mt-10">' +
                        '<label for="">Комментарий</label>' +
                        '<textarea name="clientComment" class="w-100"></textarea>' +
                    '</div>' +
                    '<div class="w-100 flex-wrap mt-10">' +
                        '<label for="">Промокод</label>' +
                        '<input name="clientPromokod" class="w-75" type="text">' +
                        '<button class="w-20 ml-a">Применить</button>' +
                    '</div>' +
                    '<div class="w-100 flex-wrap mt-10">' +
                        '<div class="w-100">Способ оплаты</div>' +
                        '<div class="flex w-100"">' +
                            '<div class="flex">' +
                                '<input checked name="typePayment" type="radio">' +
                                '<label for="">Карта</label>' +
                            '</div>' +
                            '<div class="flex ml-25">' +
                                '<input name="typePayment" type="radio">' +
                                '<label for="">Наличные</label>' +
                            '</div>' +
                        '</div>' +
                        '</div>' +
                    '<div class="order-create w-100 flex-center mt-25"><button class="w-75">Оформить заказ</button></div>' +
                '</div>';
    } else {
        return '';
    }
}

function startTrackingNumberInput() {
    document.body.querySelectorAll('.phone-mask').forEach((element) => {

        let phoneInput = element;

        if (phoneInput !== null) {
            phoneInput.addEventListener('keypress', (event) => {
                if (event.keyCode < 47 || event.keyCode > 57) {
                    event.preventDefault();
                }

                if (phoneInput.value.length === 2) {
                    phoneInput.value = phoneInput.value + "(";
                } else if (phoneInput.value.length === 6) {
                    phoneInput.value = phoneInput.value + ")";
                } else if (phoneInput.value.length === 11 || phoneInput.value.length === 14) {
                    phoneInput.value = phoneInput.value + "-";
                }
            });

            phoneInput.addEventListener('keyup', (event) => {
                let number = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
                if (number.indexOf(event.key) === -1) {
                    if ((event.key === 'Backspace' || event.key === 'Delete') && phoneInput.value.length <= 2) {
                        phoneInput.value = '+7';
                        phoneInput.selectionStart = phoneInput.value.length;
                    }
                    event.preventDefault();
                } else {
                    if (phoneInput.value.length < 3) {
                        phoneInput.value = '+7(' + event.key;
                    }
                }
            });

            phoneInput.addEventListener('focus', (event) => {
                if (phoneInput.value.length === 0) {
                    phoneInput.value = '+7';
                    phoneInput.selectionStart = phoneInput.value.length;
                }
            });

            phoneInput.addEventListener('click', (event) => {
                if (phoneInput.selectionStart < 2) {
                    phoneInput.selectionStart = phoneInput.value.length;
                }
                if (event.key === 'Backspace' && phoneInput.value.length <= 2) {
                    event.preventDefault();
                }
            });

            phoneInput.addEventListener('blur', () => {
                if (phoneInput.value === '+7') {
                    phoneInput.value = '';
                }
            });

            phoneInput.addEventListener('keydown', (event) => {
                if (event.key === 'Backspace' && phoneInput.value.length <= 2) {
                    phoneInput.value = '+7';
                    phoneInput.selectionStart = phoneInput.value.length;
                    event.preventDefault();
                }
            });
        }
    });
}

let timerSuggestionsAddress = null;
function SuggestionsAddress(query, inputSuggestions) {

    if (query.length < 4) {
        return
    }

    clearTimeout(timerSuggestionsAddress)

    timerSuggestionsAddress = setTimeout(() => {

        const url = "https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address";
        const token = "980b289f33c7bafda2d4007c51a2d45d6c980425";

        let options = {
            method: "POST",
            mode: "cors",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "Authorization": "Token " + token
            },
            body: JSON.stringify({query: query, count: 3})
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
    containerSuggestionsAbsolutePosition.className = 'container-suggestions-pos-abs pos-abs top-0 left-0 w-100 border-radius-5';
    if (result.length === 0) {
        let itemSuggestion = document.createElement('div');
        itemSuggestion.className = 'p-5';
        itemSuggestion.innerHTML = 'Нет результатов удовлетворяющих поиску';
        containerSuggestionsAbsolutePosition.append(itemSuggestion);
    } else {
        containerSuggestionsAbsolutePosition.innerHTML = '<div class="p-5 color-grey">Выберите подсказку:</div>';
        result.forEach((item) => {
            let itemSuggestion = document.createElement('div');
            itemSuggestion.className = 'p-5 suggestion-item';
            itemSuggestion.innerHTML = item.value;
            containerSuggestionsAbsolutePosition.append(itemSuggestion);
            itemSuggestion.addEventListener('click', () => {
                inputSuggestions.value = itemSuggestion.innerHTML;
                containerSuggestions.remove();
            });
        });
    }

    containerSuggestions.append(containerSuggestionsAbsolutePosition);

    inputSuggestions.insertAdjacentElement('afterEnd', containerSuggestions);
}


