const SvgCloseButton = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16"> <path d="M1.293 1.293a1 1 0 0 1 1.414 0L8 6.586l5.293-5.293a1 1 0 1 1 1.414 1.414L9.414 8l5.293 5.293a1 1 0 0 1-1.414 1.414L8 9.414l-5.293 5.293a1 1 0 0 1-1.414-1.414L6.586 8 1.293 2.707a1 1 0 0 1 0-1.414z"/> </svg>';
const SvgPlusButton = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16"> <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/> </svg>';
const SvgMinusButton = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16"> <path d="M4 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 4 8z"/> </svg>';
const SvgTrashButton = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16"> <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/> <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/> </svg>';

function LoaderShow() {
    let loader = document.createElement("div");
    loader.className = 'loader';
    loader.innerHTML = '<div class="lds-ring"><div></div><div></div><div></div><div></div></div>';
    document.body.prepend(loader);
}

function LoaderHide() {
    document.body.querySelector('.loader').remove();
}

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

        let xhr = new XMLHttpRequest();
        xhr.open(method, url, true);

        let csrf_token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);

        xhr.onload = function () {
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
    if (!check && ShowFlashMessage) {
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

function ModalWindow(content, closingCallback, flash) {
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
                !flash ? documentBody.classList.remove('scroll-off') : '';
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
                !flash ? documentBody.classList.remove('scroll-off') : '';
            }
        }
    }, modalWindowContainer);

    let modalWindowContent = CreateElement('div', {
        attr: {
            class: 'modal-window-content',
        }
    }, modalWindowContainer);

    if (typeof content === 'string') {
        modalWindowContent.innerHTML = content
    } else {
        modalWindowContent.append(content)
    }

    document.body.append(modalWindowComponentContainer);

    // if (typeof content !== 'string') {
    //     CloseByScroll(modalWindowComponentContainer, modalWindowContainer, content, () => {
    //         closingCallback ? closingCallback() : '';
    //         modalWindowComponentContainer.slowRemove();
    //         !flash ? documentBody.classList.remove('scroll-off') : '';
    //     });
    // }

    return modalWindowComponentContainer;
}

function CloseByScroll(modalWindowComponentContainer, container, content, closingCallback) {
    let widthClientScreen = document.documentElement.clientWidth;
    if (widthClientScreen < 768) {

        let containerModalWindow = container;

        let startTouch = 0;
        containerModalWindow.addEventListener('touchstart', (event) => {
            //console.log('start', event)
            containerModalWindow.style.transition = 'transform 0ms ease-out';
            if (content.getBoundingClientRect().top >= 0) {
                startTouch = event.changedTouches[0].clientY;
            }
        })

        let lengthSwipe = 0;
        containerModalWindow.addEventListener('touchmove', (event) => {
            let correctionCoefficient = 50;     // padding +
            if (content.getBoundingClientRect().top >= (-1 + correctionCoefficient)) {
                lengthSwipe = event.changedTouches[0].clientY - startTouch;
                if (lengthSwipe > 0) {
                    containerModalWindow.style.transform = 'translateY(' + lengthSwipe + 'px)';
                }
            } else {
                startTouch = event.changedTouches[0].clientY;
            }
        });

        let heightClientScreen = document.documentElement.clientHeight;

        containerModalWindow.addEventListener('touchend', () => {
            containerModalWindow.style.transition = '';
            if (lengthSwipe < (heightClientScreen / 3)) {
                containerModalWindow.style.transform = 'translateY(0px)';
            } else {
                containerModalWindow.style.transform = 'translateY(' + heightClientScreen + 'px)';
                closingCallback ? closingCallback() : '';
            }
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
    } else {
        flashMessageContainer.addEventListener('click', () => {
            flashMessageContainer.remove();
        });
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
    let sumIdentModifications = {};
    let basket = JSON.parse(localStorage.getItem('basket'));
    let sumAllDiscountProduct = 0;
    Object.keys(basket).forEach((key) => {
        let modificationTypeId = basket[key].data.modification.modificationTypeId;

        if (basket[key].data.modification.modificationTypeDiscountPrice !== false) {
            if (!!!sumIdentModifications[modificationTypeId]) {
                sumIdentModifications[modificationTypeId] = {
                    count: 0,
                    maxPrice: basket[key].data.modification.sellingPrice,
                    minPrice: basket[key].data.modification.sellingPrice,
                    discountPrice: basket[key].data.modification.modificationTypeDiscountPrice,
                };
            }

            let oldCount = sumIdentModifications[modificationTypeId]['count'];
            sumIdentModifications[modificationTypeId]['count'] = oldCount + basket[key].amount;

            let maxPrice = sumIdentModifications[modificationTypeId]['maxPrice'] > basket[key].data.modification.sellingPrice
                ? sumIdentModifications[modificationTypeId]['maxPrice']
                : basket[key].data.modification.sellingPrice;

            let minPrice = sumIdentModifications[modificationTypeId]['minPrice'] < basket[key].data.modification.sellingPrice
                ? sumIdentModifications[modificationTypeId]['minPrice']
                : basket[key].data.modification.sellingPrice;

            sumIdentModifications[modificationTypeId]['maxPrice'] = maxPrice;
            sumIdentModifications[modificationTypeId]['minPrice'] = minPrice;
            sumAllDiscountProduct += (basket[key].data.modification.sellingPrice * basket[key].amount);
        }

        priceSum += (basket[key].data.modification.sellingPrice * basket[key].amount);
    });

    let sumDiscount = 0;
    Object.keys(sumIdentModifications).forEach((key) => {
        let count = sumIdentModifications[key]['count'];
        let discountPrice = sumIdentModifications[key]['discountPrice'];
        let maxPrice = sumIdentModifications[key]['maxPrice'];

        if (count === 1) {
            sumDiscount += parseInt(maxPrice);
        } else if (count % 2 === 0) {
            sumDiscount += (count / 2) * parseInt(discountPrice);
        } else if ((count - 1) % 2 === 0) {
            sumDiscount += ((count - 1) / 2) * parseInt(discountPrice);
            sumDiscount += parseInt(maxPrice);
        }

    });

    priceSum = priceSum - sumAllDiscountProduct + sumDiscount;

    return priceSum;
}

function UpdateBasketCounter(value) {
    let basketCounter = document.body.querySelector('.amount-item-in-basket');
    if (value > 0) {
        basketCounter.show();
    } else {
        basketCounter.hide();
    }
    basketCounter.innerHTML = value;
}

function GetAllProductsInBasket() {
    return JSON.parse(localStorage.getItem('basket'));
}

let basketButton = document.body.querySelector('.button-basket');
basketButton.addEventListener('click', () => {
    BasketWindow();
});

let basketWindow;

function BasketWindow() {
    let orderId = localStorage.getItem('orderId');
    let basketContent = document.createElement('div');
    basketContent.innerHTML =
        ProductsInBasketGenerationHTML() +
        OrderInfoGenerationHTML(orderId);

    basketWindow = ModalWindow(basketContent);

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
            priceSumProductsInBasket.innerHTML = 'Итого: ' + parseFloat(resultPriceSumProductsInBasket).toFixed(2) + ' ₽';
            if (resultPriceSumProductsInBasket === 0) {
                basketWindow.slowRemove();
                document.body.classList.remove('scroll-off');
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
            priceSumProductsInBasket.innerHTML = 'Итого: ' + parseFloat(resultPriceSumProductsInBasket).toFixed(2) + ' ₽';
            if (resultPriceSumProductsInBasket === 0) {
                basketWindow.slowRemove();
                document.body.classList.remove('scroll-off');
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
            let resultPriceSumProductsInBasket = PriceSumProductsInBasket();
            priceSumProductsInBasket.innerHTML = 'Итого: ' + parseFloat(resultPriceSumProductsInBasket).toFixed(2) + ' ₽';
        });
    });

    let orderCreateButton = document.body.querySelector('.order-create');
    if (orderCreateButton !== null) {
        orderCreateButton.addEventListener('click', () => {
            if (!auth) {
                LoginWindow('BasketWindow()');
                basketWindow.slowRemove();
            } else {
                CreateOrder(orderId);
            }
        });
    }

    let cleanBasketButton = document.body.querySelector('.clean-basket');
    if (cleanBasketButton !== null) {
        cleanBasketButton.addEventListener('click', () => {
            DeleteAllProductsInBasket();
            localStorage.removeItem('lastClientName');
            localStorage.removeItem('lastClientPhone');
            localStorage.removeItem('lastClientAddressDelivery');
            localStorage.removeItem('lastClientComment');
            localStorage.removeItem('lastTypePayment');
            localStorage.removeItem('orderId');
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

    let clientInformation = basketContent.querySelector('.client-information');
    if (clientInformation !== null) {
        clientInformation.querySelectorAll('.last-data').forEach((el) => {
            el.addEventListener('input', (event) => {
                let inputName = event.target.name;
                let inputValue = event.target.value;
                inputName = inputName[0].toUpperCase() + inputName.slice(1);
                localStorage.setItem('last' + inputName, inputValue);
            });
        });
    }

}

function CreateOrder(orderId) {
    if (CheckingFieldForEmptiness('client-information') === false) {
        return;
    }

    let clientInformation = GetDataFormContainer('client-information',);
    let ObjectClientInformation = {};
    for (let key in clientInformation) {
        ObjectClientInformation[key] = clientInformation[key].length === 1 ? clientInformation[key][0] : clientInformation[key];
    }

    const screenWidth = window.screen.width;
    const screenHeight = window.screen.height;
    const userAgent = navigator.userAgent;

    let data = {
        basket: JSON.stringify(GetAllProductsInBasket()),
        clientInformation: JSON.stringify(ObjectClientInformation),
        orderAmount: PriceSumProductsInBasket(),
        screenWidth: screenWidth,
        screenHeight: screenHeight,
        userAgent: userAgent,
        orderId: orderId,
    };

    LoaderShow();
    Ajax(routeOrderCreate, 'POST', data).then((response) => {
        LoaderHide();

        // if (ObjectClientInformation.typePayment[0] && false) {
        //
        //     if (response.status === true) {
        //         window.open(
        //             response.result.paymentLink,
        //             '_blank'
        //         );
        //     }
        //
        // } else {
            FlashMessage(response.message);
            if (response.status === true) {
                basketWindow.slowRemove();
                document.body.classList.remove('scroll-off');
                DeleteAllProductsInBasket();
                if (orderId) {
                    localStorage.removeItem('lastClientName');
                    localStorage.removeItem('lastClientPhone');
                    localStorage.removeItem('lastClientAddressDelivery');
                    localStorage.removeItem('lastClientComment');
                    localStorage.removeItem('lastTypePayment');
                    localStorage.removeItem('orderId');
                }
            }
        // }
    });
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
                        '<div>' + product.categoryTitle + ': ' + product.title + '</div>' +
                        '<div>' + (modification.title !== 'Соло-продукт' ? modification.title + ': ' : '') + (modification.value !== 'Отсутствует' ? modification.value : '') + '</div>' +
                    '</div>' +
                    '<div class="flex-column-center">' +
                        '<div class="buttons-edit-amount-product border-radius-25 flex-center" style="background-color: rgb(243, 243, 247);">' +
                            '<button class="delete-product-button flex-center clear-button cp" data-modification-id="' + modificationId + '">' + SvgMinusButton + '</button>' +
                            '<div class="m-5 amount-product flex-center color-black" style="min-width: 20px;" data-modification-id="' + modificationId + '">' + amount + '</div>' +
                            '<button class="add-product-button flex-center clear-button cp" data-modification-id="' + modificationId + '">' + SvgPlusButton + '</button>' +
                        '</div>' +
                        '<div class="flex-center">' +
                            '<div class="p-10">' + modification.sellingPrice + ' ₽</div>' +
                            '<button class="clear-product-button flex-center clear-button cp" data-modification-id="' + modificationId + '">' + SvgTrashButton + '</button>' +
                        '</div>' +
                    '</div>' +
                '</div>';
            productsInBasketGenerationHTML += modificationHTML
        });
        let resultPriceSumProductsInBasket = PriceSumProductsInBasket();
        productsInBasketGenerationHTML += '<div class="price-sum-products-in-basket w-100 text-right">Итого: ' + parseFloat(resultPriceSumProductsInBasket).toFixed(2) + ' ₽</div>';
    }

    return productsInBasketGenerationHTML;
}

function OrderInfoGenerationHTML(orderId) {

    let lastClientName = localStorage.getItem('lastClientName') !== null ? localStorage.getItem('lastClientName') : '';
    let lastClientPhone = localStorage.getItem('lastClientPhone') !== null ? localStorage.getItem('lastClientPhone') : '';
    let lastClientAddressDelivery = localStorage.getItem('lastClientAddressDelivery') !== null ? localStorage.getItem('lastClientAddressDelivery') : '';
    let lastClientComment = localStorage.getItem('lastClientComment') !== null ? localStorage.getItem('lastClientComment') : '';
    let lastTypePayment = localStorage.getItem('lastTypePayment') !== null ? localStorage.getItem('lastTypePayment') : '';

    let countProductsInBasket = CountProductsInBasket();
    let phoneInput = admin ?
        '<div class="w-100 flex-wrap mt-10">' +
            '<label for="">Номер телефона</label>' +
            '<input name="clientPhone" class="need-validate phone-mask last-data w-100" maxlength="16" type="text" value="' + lastClientPhone + '">' +
        '</div>' : '';

    if (countProductsInBasket !== 0) {
        return  '<div class="client-information w-100">' +
                    '<div>Оформление заказа</div>' +
                    '<div class="w-100 flex-wrap mt-10">' +
                        '<label for="">Имя</label>' +
                        '<input name="clientName" class="need-validate last-data w-100" type="text" value="' + lastClientName + '">' +
                    '</div>' +
                        phoneInput +
                    '<div class="w-100 flex-wrap mt-10">' +
                        '<label for="">Адрес для доставки (улица, дом, кв.)</label>' +
                        '<input name="clientAddressDelivery" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"  class="need-validate delivery-address last-data w-100" type="text"  value="' + lastClientAddressDelivery + '">' +
                    '</div>' +
                    '<div class="w-100 flex-wrap mt-10">' +
                        '<label for="">Комментарий</label>' +
                        '<textarea name="clientComment" class="w-100 last-data" placeholder="Особые пожелания, сдача, подъезд, код-домофона">' + lastClientComment + '</textarea>' +
                    '</div>' +
                    // '<div class="w-100 flex-wrap mt-10">' +
                    //     '<label for="">Промокод</label>' +
                    //     '<input name="clientPromoCode" class="w-75 mr-a mb-10" type="text">' +
                    //     '<button class="promo-code-apply-button clear-button bg-grey color-white py-5 px-10 border-radius-5 mb-10">Применить</button>' +
                    // '</div>' +
                    '<div class="w-100 flex-wrap mt-10">' +
                        '<div class="w-100">Способ оплаты</div>' +
                        '<div class="flex w-100"">' +
                            '<div class="flex">' +
                                '<input ' + ((lastTypePayment === 'card' || lastTypePayment === '') ? 'checked' : '') + ' name="typePayment" value="card" type="radio" class="last-data">' +
                                '<label for="">Карта</label>' +
                            '</div>' +
                            '<div class="flex ml-25">' +
                                '<input ' + (lastTypePayment === 'cash' ? 'checked' : '') + '  name="typePayment" type="radio" value="cash" class="last-data">' +
                                '<label for="">Наличные</label>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                    '<div class="text-center mt-10">Бесплатная доставка от 500 рублей, иначе 50 рублей по городу</div>' +
                    '<div class="w-100 flex-center mt-25"><button class="cp order-create btn first mt-25">' + (orderId ? 'Сохранить изменения' : (auth ? 'Оформить заказ' : 'Авторизоваться')) + '</button>' + (orderId ? '<button class="cp clean-basket btn first mt-25 ml-10">Очистить данные</button>'  : '') + '</div>' +
                '</div>';
    } else {
        return '';
    }
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
                                class: 'btn first',
                                events: {
                                    click: () => {
                                        if (PhoneValidation(phoneField.value) !== false) {
                                            phoneContainer.remove();
                                            confirmationContainer.show();
                                            confirmationCodeInput.focus();
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
                        content: 'Для подтверждения номера Вам поступит звонок, введите последние 4 цифры входящего номера',
                        class: 'mb-5 text-center',
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
                            }
                        }
                    }),
                    CreateElement('div', {
                        childs: [
                            approveButton = CreateElement('button', {
                                content: 'Подтвердить',
                                class: 'btn first',
                                events: {
                                    click: () => {
                                        let confirmationCodeInputValue = confirmationCodeInput.value.replace(/[^\d;]/g, '');
                                        if (confirmationCodeInputValue.length !== 4) {
                                            ModalWindowFlash('Нужно 4 цифры');
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
                                                    ModalWindowFlash(response.message);
                                                }
                                            });
                                        }
                                    }
                                }
                            }),
                        ],
                        class: 'flex-center mt-10',
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
    let profileContent = CreateElement('div', {
        childs: [
            CreateElement('div', {
                content: 'Личный кабинет находится в разработке <br /> Ваш номер телефона: +' + userPhone,
                class: 'mb-10 text-center'
            }),
            CreateElement('div', {
                childs: [
                    CreateElement('button', {
                        content: 'Выйти из профиля',
                        class: 'btn first',
                        events: {
                            click: () => {
                                Logout();
                            }
                        }
                    }),
                ],
                class: 'flex-center'
            }),
        ]
    });

    ModalWindow(profileContent);
}

let leftMenuButtons = document.body.querySelectorAll('.button-menu, .shadow-menu, .close-menu-button');
leftMenuButtons.forEach((button) => {
    button.addEventListener('click', () => {
        document.body.querySelector('.left-menu').showToggle();
    });
});

function ManagerArmCheckOrderStatusChange(data = null) {
    if (admin === false) {
        return;
    }

    let alarmContainer = document.body.querySelector('.alarm-container');
    if (data !== null) {
        alarmContainer.classList.add('motion');

        if (location.pathname === '/arm/management/orders') {
            MarkOrderNewStatus(data.orderId, data.oldStatusId, data.newStatusId)
        }

    } else {
        alarmContainer.classList.remove('motion');
    }
}
