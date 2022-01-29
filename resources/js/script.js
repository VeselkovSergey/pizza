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

    CloseByScroll(modalWindowComponentContainer, modalWindowContainer, modalWindowContent, () => {
        closingCallback ? closingCallback() : '';
        modalWindowComponentContainer.slowRemove();
        ScrollOff(flash);
    });

    return modalWindowComponentContainer;

    function ScrollOff(flash) {
        if (document.querySelectorAll('.modal-window-component-container').length === 1) {
            setTimeout(() => {
                !flash ? documentBody.classList.remove('scroll-off') : '';
            }, 200);
        }
    }
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
    let sum = 0;
    let sumIdentModifications = {};
    let basket = JSON.parse(localStorage.getItem('basket'));
    let sumAllDiscountProduct = 0;

    let reiterationsCounts = 0;

    let promoCode = localStorage.getItem('promoCode') !== null ? JSON.parse(localStorage.getItem('promoCode')) : null;

    if (promoCode) {
        reiterationsCounts = promoCode.every.reiterationsCounts;
    }

    //  сортируем массив по возрастанию цены
    let arrTemp = [];
    Object.keys(basket).forEach((key) => {
        arrTemp.push(basket[key]);
    });

    arrTemp.sort((prev, next) => prev.data.modification.sellingPrice - next.data.modification.sellingPrice);
    basket = arrTemp;

    Object.keys(basket).forEach((key) => {
        let modificationTypeId = basket[key].data.modification.modificationTypeId;
        let productModification = basket[key].data.modification;
        let productModificationAmount = parseInt(basket[key].amount);

        if (promoCode) {        // если есть промокод
            if (promoCode.general.discountPercent === null && promoCode.general.discountSum === null) {       // если НЕ установлена скидка на весь заказа в процентах или сумме
                if (promoCode.every.productModifications.indexOf(productModification.id) !== -1) {
                    if (reiterationsCounts > 0) {
                        let tempReiterationsCounts = reiterationsCounts > productModificationAmount ? productModificationAmount : reiterationsCounts;
                        reiterationsCounts -= tempReiterationsCounts;
                        if (promoCode.every.discountPercent !== null) {
                            sumAllDiscountProduct += (basket[key].data.modification.sellingPrice / 100 * promoCode.every.discountPercent) * tempReiterationsCounts;
                        } else if (promoCode.every.discountSum !== null) {
                            sumAllDiscountProduct += promoCode.every.discountSum * tempReiterationsCounts;
                        } else if (promoCode.every.salePrice !== null) {
                            sumAllDiscountProduct += (basket[key].data.modification.sellingPrice - promoCode.every.salePrice) * tempReiterationsCounts;
                        }
                    }
                }
            }
        } else {
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
        }

        sum += (basket[key].data.modification.sellingPrice * basket[key].amount);
    });

    if (promoCode) {
        if (promoCode.general.discountPercent !== null) {
            sumAllDiscountProduct = (((sum / 100).toFixed(2)) * promoCode.general.discountPercent);
        } else if(promoCode.general.discountSum !== null) {
            sumAllDiscountProduct = promoCode.general.discountSum;
        }
    }

    sumAllDiscountProduct = Math.ceil(sumAllDiscountProduct);

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


    let total = sum - sumAllDiscountProduct + sumDiscount;

    return {
        sum: sum,
        discount: sumDiscount === 0 ? sumAllDiscountProduct : sum - total,
        total: total
    };
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

function UpdateBasketSum() {
    let resultPriceSumProductsInBasket = PriceSumProductsInBasket();

    let basketSumField = document.body.querySelector('.price-sum-products-in-basket');
    if (basketSumField) {
        basketSumField.innerHTML =  '<div>Сумма: ' + resultPriceSumProductsInBasket.sum.toFixed(2) + ' ₽</div>' +
                                    '<div>Скидка: ' + resultPriceSumProductsInBasket.discount.toFixed(2) + ' ₽</div>' +
                                    '<div>Итого: ' + resultPriceSumProductsInBasket.total.toFixed(2) + ' ₽</div>';
    }

    return resultPriceSumProductsInBasket;
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

    const ProductsInBasketGenerationElement = CreateElement('div', {});
    const AdditionalSalesElement = CreateElement('div', {});
    const OrderInfoGenerationHTMLElement = CreateElement('div', {});

    ProductsInBasketGenerationElement.append(ProductsInBasketGenerationHTML());
    AdditionalSalesElement.append(AdditionalSales());
    OrderInfoGenerationHTMLElement.append(OrderInfoGenerationHTML(orderId));

    basketContent.append(ProductsInBasketGenerationElement);
    basketContent.append(AdditionalSalesElement);
    basketContent.append(OrderInfoGenerationHTMLElement);

    basketWindow = ModalWindow(basketContent);

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
            localStorage.removeItem('promoCode');
            basketWindow.slowRemove();
        });
    }

    let promoCodeApplyButton = document.body.querySelector('.promo-code-apply-button');
    if (promoCodeApplyButton !== null) {

        promoCodeApplyButton.addEventListener('click', () => {

            localStorage.removeItem('promoCode');

            let promoCodeContainer = document.body.querySelector('.promo-code-container');
            let promoCodeField = promoCodeContainer.querySelector('input[name="clientPromoCode"]');
            let promoCodeValue = promoCodeField.value;

            if (promoCodeValue === '') {
                UpdateBasketSum();
            } else {
                Ajax(routeCheckPromoCodeRequest, 'POST', {promoCode: promoCodeValue})
                    .then((response) => {
                        if (response.status) {
                            let result = response.result;
                            localStorage.setItem('promoCode', JSON.stringify(result.conditions));
                            ModalWindow(result.description);
                        } else {
                            ModalWindow('Промокод не действителен');
                        }
                        UpdateBasketSum();
                    });
            }
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

    document.body.querySelectorAll('.product-additional-sales-container').forEach((productAdditionalSales) => {
        productAdditionalSales.addEventListener('click', () => {
            const productId = productAdditionalSales.dataset.productId;
            let productImg = '/img/jpg500/' + productId + '.img';
            let productImgWebP = '/img/png' + productId + '.png';
            ProductWindowGenerator(productId, productImg, productImgWebP, () => {
                ProductsInBasketGenerationElement.innerHTML = '';
                ProductsInBasketGenerationElement.append(ProductsInBasketGenerationHTML());
            });
        });
    });

    function ProductsInBasketGenerationHTML() {
        let productsInBasketGenerationElement = CreateElement('div', {class: 'w-100'});
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
                    '<div class="container-product-in-basket w-100 py-10">' +
                        '<div class="p-10 mr-a">' +
                            '<div>' + product.categoryTitle + ': ' + product.title + '</div>' +
                            '<div>' + (modification.title !== 'Соло-продукт' ? modification.title + ': ' : '') + (modification.value !== 'Отсутствует' ? modification.value : '') + '</div>' +
                        '</div>' +
                        '<div class="flex-space-between">' +
                            '<div class="flex-center">' +
                                '<div class="p-10">' + modification.sellingPrice + ' ₽</div>' +
                                '<button class="clear-product-button flex-center clear-button cp" data-modification-id="' + modificationId + '">' + SvgTrashButton + '</button>' +
                            '</div>' +
                            '<div class="buttons-edit-amount-product border-radius-25 flex-center">' +
                                '<button class="delete-product-button flex-center clear-button cp" data-modification-id="' + modificationId + '">' + SvgMinusButton + '</button>' +
                                '<div class="amount-product flex-center color-black" data-modification-id="' + modificationId + '">' + amount + '</div>' +
                                '<button class="add-product-button flex-center clear-button cp" data-modification-id="' + modificationId + '">' + SvgPlusButton + '</button>' +
                            '</div>' +
                        '</div>' +
                    '</div>';
                productsInBasketGenerationHTML += modificationHTML
            });
            let resultPriceSumProductsInBasket = PriceSumProductsInBasket();
            productsInBasketGenerationHTML +=   '<div class="price-sum-products-in-basket py-10 w-100 text-right">' +
                '<div>Сумма: ' + resultPriceSumProductsInBasket.sum.toFixed(2) + ' ₽</div>' +
                '<div>Скидка: ' + resultPriceSumProductsInBasket.discount.toFixed(2) + ' ₽</div>' +
                '<div>Итого: ' + resultPriceSumProductsInBasket.total.toFixed(2) + ' ₽</div>' +
                '</div>';
        }

        productsInBasketGenerationElement.innerHTML = productsInBasketGenerationHTML;


        let deleteProductButtons = productsInBasketGenerationElement.querySelectorAll('.delete-product-button');
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
                let resultPriceSumProductsInBasket = UpdateBasketSum();
                if (resultPriceSumProductsInBasket.sum === 0) {
                    basketWindow.slowRemove();
                    document.body.classList.remove('scroll-off');
                }
            });
        });

        let clearProductButton = productsInBasketGenerationElement.querySelectorAll('.clear-product-button');
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
                let resultPriceSumProductsInBasket = UpdateBasketSum();
                if (resultPriceSumProductsInBasket.sum === 0) {
                    basketWindow.slowRemove();
                    document.body.classList.remove('scroll-off');
                }
            });
        });

        let addProductButtons = productsInBasketGenerationElement.querySelectorAll('.add-product-button');
        addProductButtons.forEach((el) => {
            el.addEventListener('click', () => {
                let modificationId = el.dataset.modificationId;
                let amountProduct = document.body.querySelector('.amount-product[data-modification-id="' + modificationId + '"]');
                let modification = {
                    modification: {id: modificationId},
                }
                AddProductInBasket(modification);
                amountProduct.innerHTML = AmountProductInBasket(modificationId);
                UpdateBasketSum();
            });
        });

        return productsInBasketGenerationElement;
    }

    function AdditionalSales() {

        let content = '';
        if (typeof allProducts !== 'undefined' && CountProductsInBasket() !== 0) {

            content =   '<div style="max-width: 600px; scroll-snap-type: x mandatory;" class="flex scroll-x-auto additional-sales-scroll mb-10">';

            Object.keys(allProducts).forEach((key) => {
                let product = allProducts[key];

                if (product.is_additional_sales === 1) {
                let productId = product.id;
                let productTitle = product.title;
                let productSort = product.additional_sales_sort;
                let productImg = '/img/jpg500/' + productId + '.img';
                let productImgWebP = '/img/png' + productId + '.png';

                content +=
                        '<div class="mr-5 product-additional-sales-container cp" style="width: 100px;" data-product-id="'+productId+'" style="scroll-snap-align: start; order: '+productSort+'">' +
                            '<picture>' +
                                '<source srcset="'+productImgWebP+'" type="image/webp">' +
                                '<source class="w-100" srcset="'+productImg+'" type="image/jpeg">' +
                                '<img width="100" height="100" src="'+productImg+'" alt="">' +
                            '</picture>' +
                            '<div class="text-center">'+productTitle+'</div>' +
                        '</div>';
                }
            });

            content +=  '</div>';

        }

        return CreateElement('div', {content: content});
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
                '<input name="clientPhone" class="need-validate phone-mask last-data w-100" maxlength="16" type="text" value="' + lastClientPhone + '" />' +
            '</div>' : '';

        let content = '';

        if (countProductsInBasket !== 0) {
            content =
                '<div class="client-information w-100">' +
                    '<div class="promo-code-container w-100 flex-wrap-center mb-10">' +
                        '<label for="">Промокод (скидки и акции не суммируются)</label>' +
                        '<input name="clientPromoCode" autocomplete="off" class="w-75 mr-a" type="text" />' +
                        '<button class="promo-code-apply-button orange-button">Применить</button>' +
                    '</div>' +
                    '<div>Оформление заказа</div>' +
                    '<div class="w-100 flex-wrap mt-10">' +
                        '<label for="">Имя</label>' +
                        '<input name="clientName" placeholder="имя" class="need-validate last-data w-100" type="text" value="' + lastClientName + '" />' +
                    '</div>' +
                    phoneInput +
                    '<div class="w-100 flex-wrap mt-10">' +
                        '<label for="">Адрес для доставки</label>' +
                        '<input name="clientAddressDelivery" placeholder="улица, дом, кв." autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"  class="need-validate delivery-address last-data w-100" type="text"  value="' + lastClientAddressDelivery + '" />' +
                    '</div>' +
                    '<div class="w-100 flex-wrap mt-10">' +
                        '<label for="">Комментарий</label>' +
                        '<textarea  rows="3" name="clientComment" class="w-100 last-data" placeholder="Особые пожелания, сдача, подъезд, код-домофона">' + lastClientComment + '</textarea>' +
                    '</div>' +
                    '<div class="w-100 flex-wrap mt-10">' +
                        '<div class="w-100">Способ оплаты</div>' +
                        '<div class="flex w-100 px-5">' +
                            '<div class="flex w-50">' +
                                '<label for="bank-payment">' +
                                    '<input ' + ((lastTypePayment === 'card' || lastTypePayment === '') ? 'checked' : '') + ' name="typePayment" value="card" type="radio" id="bank-payment" class="last-data hide" />' +
                                    '<span class="cp py-10 block text-center w-100">Карта</span>' +
                                '</label>' +
                            '</div>' +
                            '<div class="flex w-50">' +
                                '<label for="cash-payment">' +
                                    '<input ' + (lastTypePayment === 'cash' ? 'checked' : '') + ' name="typePayment" type="radio" value="cash" id="cash-payment" class="last-data hide" />' +
                                    '<span class="cp py-10 block text-center w-100">Наличные</span>' +
                                '</label>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                    '<div class="text-center mt-10">Бесплатная доставка от 500 рублей, иначе 150 рублей по городу</div>' +
                    '<div class="w-100 flex-center mt-25" style="padding-bottom: 50px;"><button class="cp order-create btn first">' + (orderId ? 'Сохранить изменения' : (auth ? 'Оформить заказ' : 'Авторизоваться')) + '</button>' + (orderId ? '<button class="cp clean-basket btn first ml-10">Очистить данные</button>'  : '') + '</div>' +
                '</div>';
        }

        return CreateElement('div', {content: content});
    }

}

function CreateOrder(orderId) {

    if (closeMessage) {
        return ModalWindow(closeMessage);
    }

    if (CheckingFieldForEmptiness('client-information') === false) {
        return;
    }

    let clientPhone = document.body.querySelector('.client-information [name="clientPhone"]');
    if (clientPhone) {
        clientPhone = (clientPhone.value).replace(/[^\d;]/g, '');
        if (clientPhone.length !== 11) {
            ModalWindow('Не верный формат номера');
            return;
        }
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
        orderAmount: PriceSumProductsInBasket().total,
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
                localStorage.removeItem('promoCode');
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
                                class: 'btn first',
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

let modificationSelected = null;
let startSellingPriceModification = 0;
function ProductWindowGenerator(productId, productImg, productImgWebP, callback) {

    let productTitle = allProducts['product-'+productId].title;

    let imgUrl = productImg;
    let webpUrl = productImgWebP;

    let productContent = document.createElement('div');
    productContent.className = 'flex product-content h-100';
    productContent.innerHTML =
        '<div class="container-img-and-about-product">' +
        '<div class="w-100">' +
        '<div>' +
        '<picture>'+
        '<source class="w-100" srcset="' + webpUrl + '" type="image/webp">'+
        '<source class="w-100" srcset="' + imgUrl + '" type="image/jpeg">'+
        '<img class="w-100" src="' + imgUrl + '" alt="">'+
        '</picture>'+
        '</div>' +
        // '<p>Традиционное итальянское блюдо в виде тонкой круглой лепёшки (пирога) из дрожжевого теста, выпекаемой с уложенной сверху начинкой из томатного соуса, кусочков сыра, мяса, овощей, грибов и других продуктов.</p>' +
        '</div>' +
        '</div>' +
        '<div class="container-modification-product flex" style="flex: 1;">' +
        '<div class="w-100 flex-column h-100">' +
        '<div class="text-center text-up">'+productTitle+'</div>' +
        '<div class="container-ingredients text-down">' +
        IngredientsGenerator(productId) +
        '</div>'+
        ModificationsGenerate(productId) +
        '<div class="container-button-put-in-basket mt-a mx-a" style="padding-bottom: 50px;"><button class="button-put-in-basket btn first mt-25">В корзину</button></div>' +
        '</div>' +
        '</div>';

    let buttonPutInBasket = productContent.querySelector('.button-put-in-basket');
    buttonPutInBasket.innerHTML = 'Добавить в корзину за ' + startSellingPriceModification + ' ₽';

    productContent.querySelectorAll('.modification-button').forEach((el) => {
        el.addEventListener('click', () => {
            let productId = el.dataset.productId;
            let modificationType = el.dataset.modificationType;
            let modificationId = el.dataset.modificationId;
            let stopList = parseInt(el.dataset.stopList);

            let modification = allProducts[productId]['modifications'][modificationType][modificationId];
            let sellingPriceModification = modification.sellingPrice;
            let ingredients = IngredientsGenerator(null, modification);
            let containerIngredients = productContent.querySelector('.container-ingredients');
            containerIngredients.innerHTML = ingredients;
            buttonPutInBasket.innerHTML = 'Добавить в корзину за ' + sellingPriceModification + ' ₽';
            modificationSelected = {
                product: allProducts[productId],
                modification: allProducts[productId]['modifications'][modificationType][modificationId],
                stopList: stopList,
            }

            if (stopList === 1) {
                ModalWindow('Позиция находится в стоп листе. Приносим свои извинения.');
            }
        });
    });

    buttonPutInBasket.addEventListener('click', () => {
        if (modificationSelected.stopList === 1) {
            ModalWindow('Позиция находится в стоп листе. Приносим свои извинения.');
            return;
        }
        FlashMessage('Добавлено: <br/>' + modificationSelected.product.title + (modificationSelected.modification.value !== 'Отсутствует' ? (', ' + modificationSelected.modification.title + ' ' + modificationSelected.modification.value) : ''));
        AddProductInBasket(modificationSelected);
        modalWindow.slowRemove();
        document.body.classList.remove('scroll-off');

        if (callback) {
            callback();
        }

    });

    let modalWindow = ModalWindow(productContent);
}

function ModificationsGenerate(productId) {
    let containerAllModificationsTemp = '';
    let disableModificationContainer = false;
    let stopList = false;
    Object.keys(allProducts['product-'+productId]['modifications']).forEach(function (modificationTypeId) {
        let modificationType = allProducts['product-'+productId]['modifications'][modificationTypeId];
        let modificationTypeHTML = '<div class="container-modification">';
        let i = 0;
        Object.keys(modificationType).forEach(function (modificationId) {
            let modification = modificationType[modificationId];
            let checkedInput = i === 0 ? 'checked' : '';
            if(i === 0) {
                startSellingPriceModification = modification.sellingPrice;
                modificationSelected = {
                    product: allProducts['product-'+productId],
                    modification: modificationType[modificationId],
                    stopList: modification.stop_list,
                }
            }

            if (modification.value === 'Отсутствует') {
                disableModificationContainer = true;
            }

            if (modification.stop_list === 1 && i === 0) {
                stopList = true;
            }

            let buttonWidth = 'width:' + (100 / modification.modificationTypeCount) + '%;';
            let modificationIdHTML =
                '<div class="text-center flex" style="' + buttonWidth + '">' +
                '<input name="' + modificationTypeId + '" class="hide modification-input" id="' + modificationId + '" type="radio" ' + checkedInput + '/>' +
                // '<label class="modification-button"data-product-id="product-' + productId + '" data-modification-type="' + modificationTypeId + '" data-modification-id="' + modificationId + '" for="' + modificationId + '">' + modification.title + ' - ' + modification.value + '</label>' +
                '<label class="modification-button" data-stop-list="' + modification.stop_list + '" data-product-id="product-' + productId + '" data-modification-type="' + modificationTypeId + '" data-modification-id="' + modificationId + '" for="' + modificationId + '">' + modification.value + '</label>' +
                '</div>';
            modificationTypeHTML += modificationIdHTML;
            i++;
        });
        modificationTypeHTML += '</div>';
        containerAllModificationsTemp += modificationTypeHTML;
    });
    let containerAllModifications;
    if (disableModificationContainer) {
        containerAllModifications = '<div class="hide">'+ containerAllModificationsTemp +'</div>';
    } else {
        containerAllModifications = '<div>'+ containerAllModificationsTemp +'</div>';
    }

    if (stopList) {
        setTimeout(() => {
            ModalWindow('Позиция находится в стоп листе. Приносим свои извинения.');
        }, 200);
    }

    return containerAllModifications;
}

function IngredientsGenerator(productId, modification) {
    let containerAllModifications = '<div class="flex-wrap-center">';
    if (modification === undefined) {
        Object.keys(allProducts['product-'+productId]['modifications']).forEach(function (modificationTypeId) {
            let modificationType = allProducts['product-'+productId]['modifications'][modificationTypeId];
            let i = 0;
            Object.keys(modificationType).forEach(function (modificationId) {
                let modification = modificationType[modificationId];
                let ingredients = modification.ingredients;
                Object.keys(ingredients).forEach(function (ingredientId) {
                    let ingredient = ingredients[ingredientId];
                    if (ingredient.visible !== 0) {
                        if (i === 0) {
                            containerAllModifications += '<div class="pl-5 flex-center ingredient"><input checked class="hide" type="checkbox" id="' + ingredientId + '"><label class="ingredient-title" for="' + ingredientId + '">' + ingredient.title + '</label></div>';
                        }
                    }
                });
                i++;
            });
        });
    } else {
        let ingredients = modification.ingredients;
        Object.keys(ingredients).forEach(function (ingredientId) {
            let ingredient = ingredients[ingredientId];
            if (ingredient.visible !== 0) {
                containerAllModifications += '<div class="pl-5 flex-center ingredient"><input checked class="hide" type="checkbox" id="' + ingredientId + '"><label class="ingredient-title" for="' + ingredientId + '">' + ingredient.title + '</label></div>';
            }
        });
    }
    containerAllModifications += '</div>';
    return containerAllModifications;
}
