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
            const productId = productAdditionalSales.dataset.productId;
            let productImg = '/img/jpg500/' + productId + '.img';
            let productImgWebP = '/img/png/' + productId + '.png';
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
                    let productImgWebP = '/img/png/' + productId + '.png';

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

        let audio = new Audio('./audio/new-order.mp3'); // Создаём новый элемент Audio
        audio.play(); // Автоматически запускаем

    } else {
        alarmContainer.classList.remove('motion');
    }
}

let modificationSelected = null;
let startSellingPriceModification = 0;
let startWeightModification = 0;
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
            let productId = el.dataset.productId;
            let modificationType = el.dataset.modificationType;
            let modificationId = el.dataset.modificationId;
            let stopList = parseInt(el.dataset.stopList);

            let modification = allProducts[productId]['modifications'][modificationType][modificationId];
            let sellingPriceModification = modification.sellingPrice;
            let weightModification = modification.weight;
            let ingredients = IngredientsGenerator(null, modification);
            let containerIngredients = productContent.querySelector('.container-ingredients');
            containerIngredients.innerHTML = ingredients;
            buttonPutInBasket.innerHTML = 'Добавить в корзину за ' + sellingPriceModification + ' ₽';
            if (modification.weight === 0) {
                modificationWeightContainer.hide();
            }
            modificationWeightContainer.innerHTML = '(' + weightModification + ' гр.)';

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
                startWeightModification = modification.weight;
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