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
