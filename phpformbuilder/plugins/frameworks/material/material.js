/* sizing Material selects with icons | addons */

var siblings = function (el) {
    if (el.parentNode === null) return [];
    return Array.prototype.filter.call(el.parentNode.children, function (child) {
        return child !== el;
    });
};

document.addEventListener('DOMContentLoaded', function (event) {
    var items = document.querySelectorAll('.addon-before, .addon-after, .icon-after');
    items.forEach(function (item, i) {
        var addonsWidth = 0,
            inputLeft = 0;

        const sb = siblings(item);
        sb.push(item);
        sb.forEach(element => {
            if (element.classList.contains('addon-before') || element.classList.contains('icon-after') || element.classList.contains('addon-after')) {
                const elSiblings = siblings(element);
                if (!element.classList.contains('icon-before') || !elSiblings.classList.contains('.select-wrapper')) {
                    addonsWidth += element.offsetWidth;
                    if (element.classList.contains('addon-before') || element.classList.contains('addon-after')) {
                        addonsWidth += 10;
                    }
                    if (element.classList.contains('addon-before') || element.classList.contains('icon-before')) {
                        inputLeft += element.offsetWidth;
                        if (element.classList.contains('addon-before')) {
                            inputLeft += 10;
                        }
                    }
                }
            }
        });

        if (item.parentNode.querySelector('.select-wrapper') !== null) {
            item.parentNode.querySelector('input.select-dropdown').style.width = 'calc(100% - ' + addonsWidth + 'px)';
            item.parentNode.querySelector('input.select-dropdown').style.marginLeft = inputLeft + 'px';
            if (item.classList.contains('icon-after')) {
                item.parentNode.querySelector('.select-wrapper .caret').style.right = '3rem';
            }
        } else {
            const sbls = item.parentNode.querySelectorAll('.has-addon-before, .has-addon-after, .has-icon-after');
            console.log(sbls);
            sbls.forEach(s => {
                s.style.width = 'calc(100% - ' + addonsWidth + 'px)';
                s.style.marginLeft = inputLeft + 'px';
                if (s.classList.contains('has-addon-before') && s.parentNode.querySelector('label') !== null) {
                    s.parentNode.querySelector('label').style.marginLeft = inputLeft + 'px';
                }
            });
        }
    });
});
