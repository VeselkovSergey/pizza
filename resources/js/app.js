allProducts = {};

if (localStorage.getItem('refactoring') === null) {
    localStorage.setItem('basket', JSON.stringify({}));
    localStorage.setItem('refactoring', Date.now().toString());
}

if (localStorage.getItem('basket') === null) {
    localStorage.setItem('basket', JSON.stringify({}));
}

UpdateBasketCounter();

function AddItemInBasket(key, data) {
    let basket = JSON.parse(localStorage.getItem('basket'));
    let amount = 1;
    if (basket[key] === undefined) {
        basket[key] = {
            amount: 1,
            data: data,
        };
    } else {
        amount = basket[key].amount = parseInt(basket[key].amount) + 1;
    }
    localStorage.setItem('basket', JSON.stringify(basket));
    UpdateBasketCounter();
    return amount;
}

function DeleteItemInBasket(key) {
    let basket = JSON.parse(localStorage.getItem('basket'));
    let amount = 0;
    if (basket[key].amount > 1) {
        amount = basket[key].amount = parseInt(basket[key].amount) - 1;
    } else {
        delete basket[key];
    }
    localStorage.setItem('basket', JSON.stringify(basket));
    UpdateBasketCounter();
    return amount;
}

function CountProductsInBasket() {
    let count = 0;
    let basket = JSON.parse(localStorage.getItem('basket'));
    Object.keys(basket).forEach((key) => {
        count += basket[key].amount
    });
    return count;
}

function DeleteAllProductsInBasket() {
    localStorage.setItem('basket', JSON.stringify({}));
    UpdateBasketCounter();
}

function PriceSumProductsInBasket() {

    let sum = 0;
    let sumPizza = 0;
    let sumIdentModifications = {};
    let basket = JSON.parse(localStorage.getItem('basket'));
    let discountAmount = 0;

    let reiterationsCounts = 0;

    let promoCode = localStorage.getItem('promoCode') !== null ? JSON.parse(localStorage.getItem('promoCode')) : null;

    if (promoCode) {
        reiterationsCounts = promoCode.every.reiterationsCounts;
    }

    Object.keys(basket).forEach((key) => {
        const item = basket[key];
        const amount = item.amount;
        const price =  item.data.price;
        sum += amount * price;

        const product = allProducts[item.data.productId];
        const modifications = product.modifications;
        const modification = modifications.find(modification => modification.id === item.data.modificationId);
        const modificationId = modification.id;
        const modificationTypeId = modification.modificationTypeId;
        const modificationTypeDiscountPrice = modification.modificationTypeDiscountPrice;

        if (promoCode) {        // если есть промокод
            if (promoCode.general.discountPercent === null && promoCode.general.discountSum === null) {       // если НЕ установлена скидка на весь заказ в процентах или сумме
                if (promoCode.every.productModifications.indexOf(modificationId) !== -1) {
                    if (reiterationsCounts > 0) {
                        let tempReiterationsCounts = reiterationsCounts > amount ? amount : reiterationsCounts;
                        reiterationsCounts -= tempReiterationsCounts;
                        if (promoCode.every.discountPercent !== null) {     // скидка на каждую позицию в процентах
                            discountAmount += (price / 100 * promoCode.every.discountPercent) * tempReiterationsCounts;
                        } else if (promoCode.every.discountSum !== null) {      // скидка на каждую позицию в деньгах
                            discountAmount += promoCode.every.discountSum * tempReiterationsCounts;
                        } else if (promoCode.every.salePrice !== null) {        // фиксированная стоимость продукта
                            discountAmount += (price - promoCode.every.salePrice) * tempReiterationsCounts;
                        }
                    }
                }
            }
        } else {
            if (modificationTypeDiscountPrice !== false) {
                if (!!!sumIdentModifications[modificationTypeId]) {
                    sumIdentModifications[modificationTypeId] = {
                        count: 0,
                        maxPrice: price,
                        minPrice: price,
                        discountPrice: modificationTypeDiscountPrice,
                    };
                }

                let oldCount = sumIdentModifications[modificationTypeId]['count'];
                sumIdentModifications[modificationTypeId]['count'] = oldCount + amount;

                let maxPrice = sumIdentModifications[modificationTypeId]['maxPrice'] > price
                    ? sumIdentModifications[modificationTypeId]['maxPrice']
                    : price;

                let minPrice = sumIdentModifications[modificationTypeId]['minPrice'] < price
                    ? sumIdentModifications[modificationTypeId]['minPrice']
                    : price;

                sumIdentModifications[modificationTypeId]['maxPrice'] = maxPrice;
                sumIdentModifications[modificationTypeId]['minPrice'] = minPrice;
                sumPizza += (price * amount);
            }
        }
    });


    let discountAmountPizza = 0;

    if (promoCode) {
        if (promoCode.general.discountPercent !== null) {
            discountAmount = (((sum / 100).toFixed(2)) * promoCode.general.discountPercent);
        } else if(promoCode.general.discountSum !== null) {
            discountAmount = promoCode.general.discountSum;
        }
    } else {
        Object.keys(sumIdentModifications).forEach((key) => {
            let count = sumIdentModifications[key]['count'];
            let discountPrice = sumIdentModifications[key]['discountPrice'];
            let maxPrice = sumIdentModifications[key]['maxPrice'];

            if (count === 1) {
                discountAmountPizza += parseInt(maxPrice);
            } else if (count % 2 === 0) {
                discountAmountPizza += (count / 2) * parseInt(discountPrice);
            } else if ((count - 1) % 2 === 0) {
                discountAmountPizza += ((count - 1) / 2) * parseInt(discountPrice);
                discountAmountPizza += parseInt(maxPrice);
            }
        });

        discountAmount = sumPizza - discountAmountPizza;

    }

    discountAmount = Math.ceil(discountAmount);

    return {
        sum: sum,
        discount: discountAmount,
        total: sum - discountAmount,
    };
}

function UpdateBasketCounter() {
    const amount = CountProductsInBasket();
    let basketCounter = document.body.querySelector('.amount-item-in-basket');
    if (amount > 0) {
        basketCounter.show();
    } else {
        basketCounter.hide();
    }
    basketCounter.innerHTML = amount;
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

    if (Object.keys(allProducts).length === 0) {
        localStorage.setItem('execFunction', 'BasketWindow();');
        location.href = '/';
        return;
    }

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

    let promoCodeClearButton = document.body.querySelector('.promo-code-clear-button');
    if (promoCodeClearButton !== null) {
        promoCodeClearButton.addEventListener('click', () => {
            document.body.querySelector('input[name="clientPromoCode"]').value = '';
            localStorage.removeItem('promoCode');
            UpdateBasketSum();
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
            const productId = parseInt(productAdditionalSales.dataset.productId);
            ProductWindowGenerator(productId, () => {
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
                const item = basket[key];
                const amount = item.amount;
                const data = item.data;
                const modificationId = data.modificationId;
                const productId = data.productId;
                const title = data.title;
                const price = data.price;

                let modificationHTML =
                    '<div class="container-product-in-basket w-100 py-10" data-product-id="' + productId + '" data-modification-id="' + modificationId + '">' +
                        '<div class="p-10 mr-a">' +
                            '<div>' + title + '</div>' +
                        '</div>' +
                        '<div class="flex-space-between">' +
                            '<div class="flex-center">' +
                                '<div class="p-10">' + price + ' ₽</div>' +
                            '</div>' +
                            '<div class="buttons-edit-amount-product border-radius-25 flex-center">' +
                                '<button class="delete-product-button flex-center clear-button cp">' + SvgMinusButton + '</button>' +
                                '<div class="amount-product flex-center color-black">' + amount + '</div>' +
                                '<button class="add-product-button flex-center clear-button cp">' + SvgPlusButton + '</button>' +
                            '</div>' +
                        '</div>' +
                    '</div>';
                productsInBasketGenerationHTML += modificationHTML;
            });

            let resultPriceSumProductsInBasket = PriceSumProductsInBasket();
            productsInBasketGenerationHTML +=
                '<div class="price-sum-products-in-basket py-10 w-100 text-right">' +
                    '<div>Сумма: ' + resultPriceSumProductsInBasket.sum.toFixed(2) + ' ₽</div>' +
                    '<div>Скидка: ' + resultPriceSumProductsInBasket.discount.toFixed(2) + ' ₽</div>' +
                    '<div>Итого: ' + resultPriceSumProductsInBasket.total.toFixed(2) + ' ₽</div>' +
                '</div>';
        }

        productsInBasketGenerationElement.innerHTML = productsInBasketGenerationHTML;

        let deleteProductButtons = productsInBasketGenerationElement.querySelectorAll('.delete-product-button');
        deleteProductButtons.forEach((el) => {
            el.addEventListener('click', () => {

                const productContainer = el.closest('.container-product-in-basket');
                const productId = parseInt(productContainer.dataset.productId);
                const modificationId = parseInt(productContainer.dataset.modificationId);

                const amount = DeleteItemInBasket('product-' + productId + '-modification-' + modificationId);

                let amountProduct = productContainer.querySelector('.amount-product');
                amountProduct.innerHTML = amount;

                if (amount === 0) {
                    productContainer.remove();
                }

                if (UpdateBasketSum().total === 0) {
                    CloseModal(basketWindow);
                }
            });
        });

        let addProductButtons = productsInBasketGenerationElement.querySelectorAll('.add-product-button');
        addProductButtons.forEach((el) => {
            el.addEventListener('click', () => {
                const productContainer = el.closest('.container-product-in-basket');
                const productId = parseInt(productContainer.dataset.productId);
                const modificationId = parseInt(productContainer.dataset.modificationId);

                const product = allProducts[productId];
                const modifications = product.modifications;
                const modification = modifications.find(modification => modification.id === modificationId);
                const modificationTitle = modification.title;
                const modificationPrice = modification.price;

                const amount = AddItemInBasket('product-' + productId + '-modification-' + modificationId, {
                    productId: productId,
                    modificationId: modificationId,
                    title: modificationTitle,
                    price: modificationPrice,
                });

                let amountProduct = productContainer.querySelector('.amount-product');
                amountProduct.innerHTML = amount;

                UpdateBasketSum();
            });
        });

        return productsInBasketGenerationElement;
    }

    function AdditionalSales() {

        let content = '';

        if (CountProductsInBasket() !== 0) {

            content =   '<div style="max-width: 600px; scroll-snap-type: x mandatory;" class="flex scroll-x-auto additional-sales-scroll mb-10">';

            Object.keys(allProducts).forEach((key) => {
                let product = allProducts[key];

                if (product.isAdditionalSales === 1) {
                    let productId = product.id;
                    let productTitle = product.title;
                    let productSort = product.additionalSalesSort;
                    let productImg = product.imgUrl;

                    content +=
                        '<div class="mr-5 product-additional-sales-container cp" style="width: 100px;" data-product-id="'+productId+'" style="scroll-snap-align: start; order: '+productSort+'">' +
                            '<picture>' +
                                '<source srcset="'+productImg+'" type="image/webp">' +
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
                '<div class="pos-rel mr-10 flex promo-code-input-container">' +
                '<input name="clientPromoCode" autocomplete="off" class="w-100" type="text" />' +
                '<div class="pos-abs right-0 top-0 h-100 flex-center promo-code-clear-button-container"><button class="promo-code-clear-button cp flex-center">'+SvgCloseButton+'</button></div>' +
                '</div>' +
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
                '<div class="w-100 flex-center mt-25" style="padding-bottom: 50px;"><button class="cp order-create orange-button">' + (orderId ? 'Сохранить изменения' : (auth ? 'Оформить заказ' : 'Авторизоваться')) + '</button>' + (orderId ? '<button class="cp clean-basket orange-button ml-10">Очистить данные</button>'  : '') + '</div>' +
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

function ManagerArmCheckOrderStatusChange(data = null) {
    if (admin === false) {
        return;
    }

    let alarmContainer = document.body.querySelector('.alarm-container');
    if (data !== null) {
        if (data.newStatusId !== 8) {
            alarmContainer.classList.add('motion');
        }

        if (location.pathname === '/arm/management/orders') {
            MarkOrderNewStatus(data.orderId, data.oldStatusId, data.newStatusId)
        }

        let audio = new Audio(location.origin + '/audio/new-order.mp3'); // Создаём новый элемент Audio
        audio.play(); // Автоматически запускаем

    } else {
        alarmContainer.classList.remove('motion');
    }
}

let modificationSelected = null;
let startSellingPriceModification = 0;
let startWeightModification = 0;
function ProductWindowGenerator(productId, callback) {

    const product = allProducts[productId];
    const productTitle = product.title;
    const productImgUrl = product.imgUrl;

    let productContent = document.createElement('div');
    productContent.className = 'flex product-content h-100';
    productContent.innerHTML =
        '<div class="container-img-and-about-product">' +
            '<div class="w-100">' +
                '<div>' +
                    '<picture>'+
                        '<source class="w-100" srcset="' + productImgUrl + '" type="image/webp">'+
                        '<source class="w-100" srcset="' + productImgUrl + '" type="image/jpeg">'+
                        '<img class="w-100" src="' + productImgUrl + '" alt="">'+
                    '</picture>'+
                '</div>' +
            '</div>' +
        '</div>' +
        '<div class="container-modification-product flex" style="flex: 1;">' +
            '<div class="w-100 flex-column h-100">' +
                '<div class="text-center text-up">'+productTitle+' <span class="modification-weight"></span></div>' +
                '<div class="container-ingredients text-down">' +
                    IngredientsGenerator(productId) +
                '</div>'+
                ModificationsGenerate(productId) +
                '<div class="container-button-put-in-basket mt-a mx-a"><button class="button-put-in-basket orange-button mt-25">В корзину</button></div>' +
            '</div>' +
        '</div>';

    let buttonPutInBasket = productContent.querySelector('.button-put-in-basket');
    buttonPutInBasket.innerHTML = 'Добавить в корзину за ' + startSellingPriceModification + ' ₽';

    let modificationWeightContainer = productContent.querySelector('.modification-weight');
    if (startWeightModification === 0) {
        modificationWeightContainer.hide();
    }
    modificationWeightContainer.innerHTML = '(' + startWeightModification + ' гр.)';

    productContent.querySelectorAll('.modification-button').forEach((el) => {
        el.addEventListener('click', () => {
            const modifications = allProducts[productId].modifications;
            const modificationId = parseInt(el.dataset.modificationId);
            const modificationIndex = modifications.findIndex(modification => modification.id === modificationId);
            const modification = modifications[modificationIndex];
            const modificationPrice = modification.price;
            const modificationWeight = modification.weight;
            const modificationStopList = modification.stopList;

            productContent.querySelector('.container-ingredients').innerHTML = IngredientsGenerator(productId, modificationIndex);

            buttonPutInBasket.innerHTML = 'Добавить в корзину за ' + modificationPrice + ' ₽';
            if (modificationWeight === 0) {
                modificationWeightContainer.hide();
            }
            modificationWeightContainer.innerHTML = '(' + modificationWeight + ' гр.)';

            modificationSelected = {
                productId: productId,
                modificationId: modificationId,
            };

            if (modificationStopList) {
                ModalWindow('Позиция находится в стоп листе. Приносим свои извинения.');
            }
        });
    });

    buttonPutInBasket.addEventListener('click', () => {
        const product = allProducts[modificationSelected.productId];
        const productId = product.id;
        const modifications = product.modifications;
        const modification = modifications.find(modification => modification.id === modificationSelected.modificationId);
        const modificationId = modification.id;
        const modificationTitle = modification.title;
        const modificationPrice = modification.price;
        const modificationStopList = modification.stopList;

        if (modificationStopList) {
            ModalWindow('Позиция находится в стоп листе. Приносим свои извинения.');
            return;
        }

        FlashMessage('<div class="text-center">Добавлено: <br/>' + modificationTitle + '</div>');

        AddItemInBasket('product-' + productId + '-modification-' + modificationId, {
            productId: productId,
            modificationId: modificationId,
            title: modificationTitle,
            price: modificationPrice,
        });

        CloseModal(modalWindow);

        if (callback) {
            callback();
        }
    });

    let modalWindow = ModalWindow(productContent);
}

function ModificationsGenerate(productId) {

    const product = allProducts[productId];
    const productModifications = product.modifications;
    const modificationCount = product.modificationCount;

    let stopList = false;

    const buttonWidth = 'width:' + (100 / modificationCount) + '%;';

    let hideClass = '';
    if (modificationCount === 1) {
        hideClass = ' hide ';
    }

    let modificationsContainerElement = '<div class="modifications-container ' + hideClass + '">';

    let i = 0;
    Object.keys(productModifications).forEach((key) => {
        const modification = productModifications[key];
        const modificationId = productModifications[key].id;
        const modificationValue = modification.modificationValue;
        const modificationWeight = modification.weight;
        const modificationStopList = modification.stopList;
        const modificationPrice = modification.price;

        let checkedInput = '';
        if (i === 0) {
            modificationSelected = {
                productId: productId,
                modificationId: modificationId,
            };
            startWeightModification = modificationWeight;
            startSellingPriceModification = modificationPrice;
            checkedInput = ' checked ';
            stopList = modificationStopList;
        }

        modificationsContainerElement +=
            '<div class="text-center flex" style="' + buttonWidth + '">' +
                '<input name="modification" class="hide modification-input" id="' + modificationId + '" type="radio" ' + checkedInput + '/>' +
                '<label class="modification-button" for="' + modificationId + '" data-modification-id=" ' + modificationId + ' ">' + modificationValue + '</label>' +
            '</div>';
        i++;
    });

    if (stopList) {
        setTimeout(() => {
            ModalWindow('Позиция находится в стоп листе. Приносим свои извинения.');
        }, 200);
    }

    modificationsContainerElement += '</div>';

    return modificationsContainerElement;
}

function IngredientsGenerator(productId, modificationIndex = 0) {
    const product = allProducts[productId];

    let containerAllModifications = '<div class="flex-wrap-center">';

    const firstModificationIngredients = product.modifications[modificationIndex].ingredients;

    Object.keys(firstModificationIngredients).forEach((key) => {
        const ingredient = firstModificationIngredients[key];
        const ingredientTitle = ingredient.title;
        const ingredientVisible = ingredient.visible;
        if (ingredientVisible) {
            containerAllModifications += '<div class="pl-5 flex-center ingredient">' + ingredientTitle + '</div>';
        }
    });

    containerAllModifications += '</div>';
    return containerAllModifications;
}