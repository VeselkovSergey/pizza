/* JS add prototypes */
Element.prototype.hide = function () {
    this.classList.add('hide');
}

Element.prototype.show = function () {
    this.classList.remove('hide');
}

Element.prototype.showToggle = function () {
    if (this.classList.contains('hide')) {
        this.classList.remove('hide');
    } else {
        this.classList.add('hide');
    }
}

Element.prototype.slowRemove = function () {
    this.classList.add('slow-remove');
    setTimeout(() => {
        this.remove()
    }, 400);
}

function CreateElement(tag, params, parent) {
    const element = document.createElement(tag);
    if (params.attr) {
        Object.keys(params.attr).forEach((a) => {
            element.setAttribute(a, params.attr[a]);
        });
    }
    if (params.class) {
        element.className = params.class;
    }
    if (params.events) {
        Object.keys(params.events).forEach((e) => {
            element.addEventListener(e, params.events[e]);
        });
    }
    if (params.content) {
        element.innerHTML = params.content;
    }
    if (parent) {
        parent.appendChild(element);
    }
    if (params.childs) {
        params.childs.forEach((child) => {
            element.appendChild(child);
        })
    }
    return element;

    // let buttonAnswerDelete = CreateElement('button', {
    //     class: 'px-15 py-5 ml-10 cp',
    //     content: 'Удалить ответ',
    //     events: {
    //         click: (e) => {
    //             containerFieldsAdditionalAnswer.remove();
    //         }
    //     }
    // }, containerAdditionalAnswer);
}

function ToggleShow() {
    let allToggleButtons = document.body.querySelectorAll('.toggle-button');
    allToggleButtons.forEach((toggleButton) => {
        let toggleContainerSelector = toggleButton.dataset.toogle;
        let toggleContainer = document.body.querySelector('.' + toggleContainerSelector);
        toggleContainer.hide();
        toggleButton.addEventListener('click', () => {
            toggleContainer.showToggle();
        });
    });
}

const getSort = ({ target }) => {
    const order = (target.dataset.order = -(target.dataset.order || -1));
    const index = [...target.parentNode.cells].indexOf(target);
    const collator = new Intl.Collator(['en', 'ru'], { numeric: true });
    const comparator = (index, order) => (a, b) => order * collator.compare(
        a.children[index].innerHTML,
        b.children[index].innerHTML
    );

    for(const tBody of target.closest('table').tBodies)
        tBody.append(...[...tBody.rows].sort(comparator(index, order)));

    for(const cell of target.parentNode.cells)
        cell.classList.toggle('sorted', cell === target);
};